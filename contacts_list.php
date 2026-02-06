<?php
// Handle AJAX save for field visibility FIRST, before any output
if (isset($_POST['save_fields']) && isset($_POST['fields'])) {
    $csvPath = __DIR__ . '/field_visibility.csv';
    $schemaFile = __DIR__ . '/contact_schema.php';
    
    if (!file_exists($schemaFile)) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Schema file not found']);
        exit;
    }
    
    $schema = require $schemaFile;
    
    header('Content-Type: application/json');
    error_log('Saving fields: ' . $_POST['fields']);
    
    try {
        $handle = fopen($csvPath, 'w');
        if (!$handle) {
            echo json_encode(['status' => 'error', 'message' => 'Cannot open CSV file']);
            exit;
        }
        
        $fieldsToSave = json_decode($_POST['fields'], true) ?? [];
        error_log('Decoded fields: ' . json_encode($fieldsToSave));
        
        foreach ($schema as $field) {
            $visible = in_array($field, $fieldsToSave) ? 'true' : 'false';
            fputcsv($handle, [$field, $visible]);
        }
        fclose($handle);
        
        echo json_encode(['status' => 'success']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}

// Now include layout and navbar ONLY for non-AJAX requests
include_once(__DIR__ . '/layout_start.php');
$currentPage = basename(__FILE__);
include_once(__DIR__ . '/navbar.php');
require_once 'csv_handler.php';

// Load schema safely
$schemaFile = __DIR__ . '/contact_schema.php';
if (!file_exists($schemaFile)) {
    die('Error: contact_schema.php not found.');
}
$schema = require $schemaFile;
if (!is_array($schema)) {
    die('Error: contact_schema.php must return an array.');
}

// Load field visibility preferences from CSV
$csvPath = __DIR__ . '/field_visibility.csv';

// Save field visibility preferences to CSV only when Apply is clicked
if (isset($_GET['apply']) && isset($_GET['display'])) {
    $handle = fopen($csvPath, 'w');
    foreach ($schema as $field) {
        $visible = in_array($field, $_GET['display']) ? 'true' : 'false';
        fputcsv($handle, [$field, $visible]);
    }
    fclose($handle);
}

// Load field visibility from CSV
$displayFields = [];
if (file_exists($csvPath)) {
    $handle = fopen($csvPath, 'r');
    while (($row = fgetcsv($handle)) !== false) {
        if (count($row) === 2 && strtolower($row[1]) === 'true') {
            $displayFields[] = $row[0];
        }
    }
    fclose($handle);
}

// Fallback if displayFields is empty
if (empty($displayFields)) {
    $displayFields = ['first_name', 'last_name', 'company', 'email'];
}

// Handle query and sort
$query = strtolower(trim($_GET['query'] ?? ''));
$field = $_GET['field'] ?? '';
$sortFields = explode(',', $_GET['sort'] ?? '');
$sortDirection = $_GET['direction'] ?? 'asc';
$activeSort = array_flip($sortFields);
$showOnlyCustomers = isset($_GET['customers_only']) && $_GET['customers_only'] === '1';

// Load contacts safely
$contacts = readCSV('contacts.csv', $schema);
if (!is_array($contacts)) {
    $contacts = [];
}

// Detect duplicates
$emailCount = [];
foreach ($contacts as $c) {
    $email = strtolower(trim($c['email'] ?? ''));
    if (!empty($email)) {
        $emailCount[$email] = ($emailCount[$email] ?? 0) + 1;
    }
}

// Apply filter
if ($query !== '' && in_array($field, $schema)) {
    $contacts = array_filter($contacts, function($c) use ($field, $query) {
        return strpos(strtolower($c[$field] ?? ''), $query) !== false;
    });
}

// Apply "show only customers" filter
if ($showOnlyCustomers) {
    $contacts = array_filter($contacts, function($c) {
        $isCustomer = strtolower(trim($c['is_customer'] ?? ''));
        return $isCustomer === 'yes' || $isCustomer === '1' || $isCustomer === 'true';
    });
}

// Apply multi-field sort
$validSortFields = array_filter($sortFields, function($f) use ($displayFields) {
    return in_array($f, $displayFields);
});

if (!empty($validSortFields)) {
    usort($contacts, function($a, $b) use ($validSortFields, $sortDirection) {
        foreach ($validSortFields as $field) {
            $valA = $a[$field] ?? '';
            $valB = $b[$field] ?? '';
            $isNumeric = is_numeric($valA) && is_numeric($valB);
            $cmp = $isNumeric ? ($valA <=> $valB) : strnatcasecmp($valA, $valB);
            if ($cmp !== 0) {
                return $sortDirection === 'desc' ? -$cmp : $cmp;
            }
        }
        return 0;
    });
}

// Export logic
if (isset($_GET['export']) && $_GET['export'] === '1') {
    $filename = 'contacts_export_' . date('Ymd_His') . '.csv';
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    $output = fopen('php://output', 'w');
    if (!$output) {
        die('Error: Unable to open export stream.');
    }
    fputcsv($output, $displayFields);
    foreach ($contacts as $contact) {
        $row = [];
        foreach ($displayFields as $f) {
            $row[] = $contact[$f] ?? '';
        }
        fputcsv($output, $row);
    }
    fclose($output);
    exit;
}
?>
<style>
  body {
    font-size: var(--crm-font-size, 0.85rem);
  }
</style>

<div class="page-contacts">
  <div class="container">
    <h2>All Contacts</h2>

    <div style="margin-bottom:10px;">
      <button type="button" onclick="toggleFieldPanel()" style="font-size:0.85rem;">🛠 Show/Hide Field Selection</button>
      <div id="fieldPanel" style="display:none; margin-top:10px; border:1px solid #ccc; padding:10px; border-radius:6px; background:#f9f9f9;">
        <fieldset>
          <legend style="font-weight:bold;">Visible Fields:</legend>
          <div style="display:flex; flex-wrap:wrap; gap:10px;">
            <?php foreach ($schema as $f): ?>
              <label style="display:flex; align-items:center; gap:4px;">
                <input type="checkbox" class="field-visibility" name="field_<?= $f ?>" id="field_<?= $f ?>" value="<?= $f ?>" data-field="<?= $f ?>" <?= in_array($f, $displayFields) ? 'checked' : '' ?>>
                <?= ucfirst(str_replace('_', ' ', $f)) ?>
              </label>
            <?php endforeach; ?>
          </div>
          <button type="button" onclick="saveFieldVisibility()" style="margin-top:10px;">💾 Apply</button>
        </fieldset>
      </div>
    </div>

<form method="GET" class="contact-form">
      <div style="display:flex; flex-wrap:wrap; gap:20px; align-items:flex-end;">
        <div>
          <label for="field">Filter by:</label><br>
          <select name="field" id="field">
            <option value="">-- None --</option>
            <?php foreach ($schema as $f): ?>
              <option value="<?= $f ?>" <?= ($field === $f) ? 'selected' : '' ?>>
                <?= ucfirst(str_replace('_', ' ', $f)) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div>
          <label for="query">Search:</label><br>
          <input type="text" name="query" id="query" value="<?= htmlspecialchars($_GET['query'] ?? '') ?>">
        </div>

        <div>
          <label for="fontSize">Font size:</label><br>
          <select id="fontSize">
            <option value="0.75rem">Small</option>
            <option value="0.85rem" selected>Medium</option>
            <option value="1rem">Large</option>
          </select>
        </div>

        <div style="display:flex; flex-direction:column; gap:6px;">
          <button type="submit">Apply</button>
          <button type="submit" name="export" value="1">Export</button>
        </div>

        <div>
          <label for="sort">Sort by:</label><br>
          <select name="sort" id="sort">
            <option value="">-- None --</option>
            <?php foreach ($displayFields as $f): ?>
              <option value="<?= $f ?>" <?= in_array($f, $sortFields) ? 'selected' : '' ?>>
                <?= ucfirst(str_replace('_', ' ', $f)) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div>
          <label for="direction">Direction:</label><br>
          <select name="direction" id="direction">
            <option value="asc" <?= $sortDirection === 'asc' ? 'selected' : '' ?>>A–Z / 0–9</option>
            <option value="desc" <?= $sortDirection === 'desc' ? 'selected' : '' ?>>Z–A / 9–0</option>
          </select>
        </div>

        <div style="display:flex; align-items:flex-end; gap:8px;">
          <label style="display:flex; align-items:center; gap:6px; margin-bottom:0;">
            <input type="checkbox" name="customers_only" value="1" <?= $showOnlyCustomers ? 'checked' : '' ?>>
            Show only customers
          </label>
        </div>
      </div>
    </form>

    <table class="contact-table">
      <thead>
        <tr>
          <th>Actions</th>
          <?php foreach ($displayFields as $f): ?>
            <th>
              <?= ucfirst(str_replace('_', ' ', $f)) ?>
              <?php if (isset($activeSort[$f])): ?>
                <span style="<?= $activeSort[$f] === 0 ? 'font-weight:bold; color:#0099A8;' : '' ?>">
                  <?= $sortDirection === 'desc' ? '↓' : '↑' ?>
                </span>
              <?php endif; ?>
            </th>
          <?php endforeach; ?>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($contacts as $contact): ?>
          <?php
            $id = isset($contact['id']) ? $contact['id'] : '';
            $email = $contact['email'] ?? '';
            $isDuplicate = !empty($email) && $emailCount[strtolower(trim($email))] > 1;
			
          ?>
		  
          <tr onclick="this.nextElementSibling.classList.toggle('expanded')">
            <td>
              <a href="contact_view.php?id=<?= $id ?>">👁</a>
              <form method="POST" action="delete_contact.php" style="display:inline;" onsubmit="return confirm('Delete this contact?');">
                <input type="hidden" name="id" value="<?= $id ?>">
                <button type="submit" class="btn-outline">🗑</button>
              </form>
            </td>
            <?php foreach ($displayFields as $f): ?>
              <td>
                <?= htmlspecialchars($contact[$f] ?? '') ?>
                <?= ($f === 'email' && $isDuplicate) ? "<span style='color:red;'> [Duplicate]</span>" : '' ?>
              </td>
            <?php endforeach; ?>
          </tr>
          <tr class="details-row">
            <td colspan="<?= count($displayFields) + 1 ?>">
              <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:12px;">
                <?php foreach ($schema as $f): ?>
                  <div>
                    <strong><?= ucfirst(str_replace('_', ' ', $f)) ?>:</strong>
                    <?= htmlspecialchars($contact[$f] ?? '') ?>
                  </div>
                <?php endforeach; ?>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
  // Save current view state to localStorage
  function saveViewState() {
    const queryString = window.location.search;
    if (queryString) {
      localStorage.setItem('contactsListState', queryString);
      console.log('Saved view state:', queryString);
    }
  }

  // Restore view state from localStorage on page load
  function restoreViewState() {
    if (!window.location.search) {
      const savedState = localStorage.getItem('contactsListState');
      if (savedState) {
        console.log('Restoring view state:', savedState);
        window.location.href = window.location.pathname + savedState;
        return false;
      }
    }
    return true;
  }

  // Save state when page loads
  document.addEventListener('DOMContentLoaded', function() {
    saveViewState();
  });

  // Also restore on initial page load before DOM is ready
  if (document.readyState === 'loading') {
    restoreViewState();
  } else {
    saveViewState();
  }

  function toggleFieldPanel() {
    const panel = document.getElementById('fieldPanel');
    panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
  }

  function saveFieldVisibility() {
    const checkboxes = document.querySelectorAll('input.field-visibility:checked');
    const selectedFields = Array.from(checkboxes).map(cb => cb.value);
    
    console.log('Selected fields:', selectedFields);

    const formData = new FormData();
    formData.append('save_fields', '1');
    formData.append('fields', JSON.stringify(selectedFields));

    fetch(window.location.pathname, {
      method: 'POST',
      body: formData
    })
    .then(response => {
      console.log('Response status:', response.status);
      console.log('Content-Type:', response.headers.get('Content-Type'));
      return response.text().then(text => {
        console.log('Response text:', text);
        try {
          return JSON.parse(text);
        } catch (e) {
          throw new Error('Invalid JSON response: ' + text);
        }
      });
    })
    .then(data => {
      console.log('Save response:', data);
      if (data.status === 'success') {
        // Save current state and reload
        saveViewState();
        setTimeout(() => {
          window.location.href = window.location.href;
        }, 300);
      } else {
        console.error('Save failed:', data.message || 'Unknown error');
        alert('Error saving field visibility: ' + (data.message || 'Unknown error'));
      }
    })
    .catch(err => {
      console.error('Error saving field visibility:', err);
      alert('Error: ' + err.message);
    });
  }

  document.getElementById('fontSize').addEventListener('change', function () {
    document.documentElement.style.setProperty('--crm-font-size', this.value);
  });
</script>

<?php include_once(__DIR__ . '/layout_end.php'); ?>
