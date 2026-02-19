<?php
require_once 'layout_start.php';

require_once 'db_pgsql.php';
$schema = require __DIR__ . '/opportunity_schema.php';
$contactSchema = require __DIR__ . '/contact_schema.php';

function fetch_contacts_pgsql($schema) {
  $conn = get_pgsql_connection();
  $fields = implode(',', array_map(function($f) { return '"' . $f . '"'; }, $schema));
  $result = pg_query($conn, "SELECT $fields FROM contacts");
  if (!$result) return [];
  $rows = [];
  while ($row = pg_fetch_assoc($result)) {
    $rows[] = $row;
  }
  pg_free_result($result);
  return $rows;
}

function fetch_opportunity_pgsql($id, $schema) {
  $conn = get_pgsql_connection();
  $fields = implode(',', array_map(function($f) { return '"' . $f . '"'; }, $schema));
  $idEsc = pg_escape_string($id);
  $result = pg_query($conn, "SELECT $fields FROM opportunities WHERE id='$idEsc'");
  if (!$result) return null;
  $row = pg_fetch_assoc($result);
  pg_free_result($result);
  return $row;
}

function update_opportunity_pgsql($id, $fields) {
  $conn = get_pgsql_connection();
  $set = [];
  foreach ($fields as $k => $v) {
    $set[] = '"' . pg_escape_string($k) . '"=' . (is_null($v) ? 'NULL' : "'" . pg_escape_string($v) . "'");
  }
  $setStr = implode(',', $set);
  $idEsc = pg_escape_string($id);
  $sql = "UPDATE opportunities SET $setStr WHERE id='$idEsc'";
  return pg_query($conn, $sql);
}

$contacts = fetch_contacts_pgsql($contactSchema);

// Get opportunity ID
$opportunityId = $_GET['id'] ?? $_POST['id'] ?? null;

if (!$opportunityId) {
    header('Location: opportunities_list.php?error=' . urlencode('No opportunity ID provided'));
    exit;
}


$opportunity = fetch_opportunity_pgsql($opportunityId, $schema);
if (!$opportunity) {
  header('Location: opportunities_list.php?error=' . urlencode('Opportunity not found'));
  exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate inputs
    $errors = [];
    
    if (empty($_POST['contact_id'])) {
        $errors[] = 'Contact is required';
    }
    
    if (!isset($_POST['value']) || $_POST['value'] === '' || !is_numeric($_POST['value']) || $_POST['value'] < 0) {
        $errors[] = 'Valid opportunity value is required';
    }
    
    if (!isset($_POST['probability']) || $_POST['probability'] === '' || !is_numeric($_POST['probability']) || $_POST['probability'] < 0 || $_POST['probability'] > 100) {
        $errors[] = 'Probability must be between 0 and 100';
    }
    
    if (empty($_POST['stage'])) {
        $errors[] = 'Stage is required';
    }
    
    if (empty($_POST['expected_close'])) {
        $errors[] = 'Expected close date is required';
    }
    
    if (empty($errors)) {
        // Update opportunity
        $fields = [
          'contact_id' => $_POST['contact_id'],
          'value' => $_POST['value'],
          'stage' => $_POST['stage'],
          'probability' => $_POST['probability'],
          'expected_close' => $_POST['expected_close'],
        ];
        $result = update_opportunity_pgsql($opportunityId, $fields);
        if ($result) {
          header('Location: opportunities_list.php?success=2');
          exit;
        } else {
          $error = 'Failed to update opportunity.';
        }
    } else {
        $error = implode(', ', $errors);
    }
}
?>

<style>
  .page-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 32px;
    border-radius: 12px;
    margin-bottom: 24px;
  }
  
  .page-header h1 {
    margin: 0 0 8px 0;
    font-size: 32px;
    font-weight: 700;
  }
  
  .page-header p {
    margin: 0;
    opacity: 0.9;
    font-size: 14px;
  }
  
  .back-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: #667eea;
    text-decoration: none;
    font-weight: 600;
    margin-bottom: 16px;
    font-size: 14px;
  }
  
  .back-link:hover {
    color: #5558dd;
  }
  
  .form-container {
    background: white;
    padding: 32px;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    max-width: 900px;
    margin: 0 auto;
  }
  
  .form-section {
    margin-bottom: 32px;
    padding-bottom: 32px;
    border-bottom: 2px solid #E5E7EB;
  }
  
  .form-section:last-child {
    margin-bottom: 0;
    padding-bottom: 0;
    border-bottom: none;
  }
  
  .form-section-title {
    font-size: 18px;
    font-weight: 700;
    color: #1F2937;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 12px;
  }
  
  .form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 24px;
  }
  
  .form-group {
    display: flex;
    flex-direction: column;
  }
  
  .form-group.full-width {
    grid-column: 1 / -1;
  }
  
  .form-group label {
    font-size: 13px;
    font-weight: 700;
    color: #374151;
    margin-bottom: 8px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }
  
  .form-group input,
  .form-group select {
    padding: 14px 16px;
    border: 2px solid #E5E7EB;
    border-radius: 8px;
    font-size: 15px;
    font-family: inherit;
    transition: all 0.2s;
    background: white;
  }
  
  .form-group input:focus,
  .form-group select:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
  }
  
  .form-group select {
    cursor: pointer;
  }
  
  .form-help {
    font-size: 12px;
    color: #6B7280;
    margin-top: 6px;
  }
  
  .stage-visual {
    display: flex;
    gap: 8px;
    margin-top: 12px;
    flex-wrap: wrap;
  }
  
  .stage-badge {
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    color: white;
    cursor: pointer;
    transition: all 0.2s;
    border: 2px solid transparent;
  }
  
  .stage-badge:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
  }
  
  .stage-badge.selected {
    border-color: white;
    box-shadow: 0 0 0 3px rgba(255,255,255,0.3);
  }
  
  .form-actions {
    display: flex;
    gap: 12px;
    margin-top: 32px;
  }
  
  .btn-primary {
    padding: 14px 32px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: 700;
    font-size: 15px;
    cursor: pointer;
    transition: all 0.3s;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  }
  
  .btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
  }
  
  .btn-secondary {
    padding: 14px 32px;
    background: #F3F4F6;
    color: #374151;
    border: 2px solid #E5E7EB;
    border-radius: 8px;
    font-weight: 700;
    font-size: 15px;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
    display: inline-block;
  }
  
  .btn-secondary:hover {
    background: #E5E7EB;
  }
  
  .error-alert {
    background: linear-gradient(135deg, #FEE2E2 0%, #FECACA 100%);
    border: 2px solid #EF4444;
    color: #991B1B;
    padding: 16px 20px;
    border-radius: 8px;
    margin-bottom: 24px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 12px;
  }
  
  .probability-slider {
    display: flex;
    align-items: center;
    gap: 12px;
  }
  
  .probability-value {
    font-size: 20px;
    font-weight: 700;
    color: #667eea;
    min-width: 50px;
  }
  
  input[type="range"] {
    flex: 1;
    height: 8px;
    border-radius: 4px;
    background: #E5E7EB;
    outline: none;
    padding: 0;
  }
  
  input[type="range"]::-webkit-slider-thumb {
    appearance: none;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: #667eea;
    cursor: pointer;
  }
  
  input[type="range"]::-moz-range-thumb {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: #667eea;
    cursor: pointer;
    border: none;
  }
  
  @media (max-width: 768px) {
    .form-grid {
      grid-template-columns: 1fr;
    }
    
    .page-header {
      padding: 24px;
    }
    
    .form-container {
      padding: 24px;
    }
  }
</style>

<a href="opportunities_list.php" class="back-link">
  ← Back to Opportunities
</a>

<div class="page-header">
  <h1>✏️ Edit Opportunity #<?= htmlspecialchars($opportunityId) ?></h1>
  <p>Update opportunity details and sales progress</p>
</div>

<?php if (isset($error)): ?>
  <div class="error-alert">
    <span>⚠️</span>
    <span><?= htmlspecialchars($error) ?></span>
  </div>
<?php endif; ?>

<div class="form-container">
  <form method="POST" id="opportunityForm">
    <input type="hidden" name="id" value="<?= htmlspecialchars($opportunityId) ?>">
    
    <div class="form-section">
      <div class="form-section-title">
        <span>👤</span>
        <span>Contact Information</span>
      </div>
      
      <div class="form-grid">
        <div class="form-group full-width">
          <label for="contact_id">Contact *</label>
          <select name="contact_id" id="contact_id" required>
            <option value="">Select a contact...</option>
            <?php foreach ($contacts as $contact): ?>
              <?php
                $fullName = trim($contact['first_name'] . ' ' . $contact['last_name']);
                $company = $contact['company'] ?? '';
                $displayName = $fullName ?: 'Unnamed Contact';
                if ($company) {
                  $displayName .= ' (' . $company . ')';
                }
                $selected = ($contact['id'] == $opportunity['contact_id']) ? 'selected' : '';
              ?>
              <option value="<?= htmlspecialchars($contact['id']) ?>" <?= $selected ?>>
                <?= htmlspecialchars($displayName) ?>
              </option>
            <?php endforeach; ?>
          </select>
          <div class="form-help">Select the contact associated with this opportunity</div>
        </div>
      </div>
    </div>
    
    <div class="form-section">
      <div class="form-section-title">
        <span>💰</span>
        <span>Deal Details</span>
      </div>
      
      <div class="form-grid">
        <div class="form-group">
          <label for="value">Opportunity Value *</label>
          <input 
            type="number" 
            name="value" 
            id="value" 
            placeholder="0.00" 
            step="0.01" 
            min="0"
            value="<?= htmlspecialchars($opportunity['value']) ?>"
            required
          >
          <div class="form-help">Enter the total value in dollars</div>
        </div>
        
        <div class="form-group">
          <label for="expected_close">Expected Close Date *</label>
          <input 
            type="date" 
            name="expected_close" 
            id="expected_close"
            value="<?= htmlspecialchars($opportunity['expected_close']) ?>"
            required
          >
          <div class="form-help">When do you expect to close this deal?</div>
        </div>
      </div>
    </div>
    
    <div class="form-section">
      <div class="form-section-title">
        <span>📊</span>
        <span>Sales Stage & Probability</span>
      </div>
      
      <div class="form-grid">
        <div class="form-group full-width">
          <label for="stage">Sales Stage *</label>
          <select name="stage" id="stage" required>
            <option value="">Select stage...</option>
            <option value="Prospecting" data-probability="10" <?= $opportunity['stage'] === 'Prospecting' ? 'selected' : '' ?>>Prospecting (10% win rate)</option>
            <option value="Proposal" data-probability="25" <?= $opportunity['stage'] === 'Proposal' ? 'selected' : '' ?>>Proposal (25% win rate)</option>
            <option value="Negotiation" data-probability="50" <?= $opportunity['stage'] === 'Negotiation' ? 'selected' : '' ?>>Negotiation (50% win rate)</option>
            <option value="Closed Won" data-probability="100" <?= $opportunity['stage'] === 'Closed Won' ? 'selected' : '' ?>>Closed Won (100%)</option>
            <option value="Closed Lost" data-probability="0" <?= $opportunity['stage'] === 'Closed Lost' ? 'selected' : '' ?>>Closed Lost (0%)</option>
          </select>
          
          <div class="stage-visual" style="margin-top: 16px;">
            <div class="stage-badge <?= $opportunity['stage'] === 'Prospecting' ? 'selected' : '' ?>" style="background: #6366F1;" data-stage="Prospecting">Prospecting</div>
            <div class="stage-badge <?= $opportunity['stage'] === 'Proposal' ? 'selected' : '' ?>" style="background: #8B5CF6;" data-stage="Proposal">Proposal</div>
            <div class="stage-badge <?= $opportunity['stage'] === 'Negotiation' ? 'selected' : '' ?>" style="background: #F59E0B;" data-stage="Negotiation">Negotiation</div>
            <div class="stage-badge <?= $opportunity['stage'] === 'Closed Won' ? 'selected' : '' ?>" style="background: #10B981;" data-stage="Closed Won">Closed Won</div>
            <div class="stage-badge <?= $opportunity['stage'] === 'Closed Lost' ? 'selected' : '' ?>" style="background: #EF4444;" data-stage="Closed Lost">Closed Lost</div>
          </div>
        </div>
        
        <div class="form-group full-width">
          <label for="probability">Win Probability *</label>
          <div class="probability-slider">
            <input 
              type="range" 
              name="probability" 
              id="probability" 
              min="0" 
              max="100" 
              value="<?= htmlspecialchars($opportunity['probability']) ?>"
              step="5"
            >
            <span class="probability-value"><?= htmlspecialchars($opportunity['probability']) ?>%</span>
          </div>
          <div class="form-help">Estimated likelihood of closing this deal</div>
        </div>
      </div>
    </div>
    
    <div class="form-actions">
      <button type="submit" class="btn-primary">💾 Update Opportunity</button>
      <a href="opportunities_list.php" class="btn-secondary">Cancel</a>
    </div>
  </form>
</div>

<script>
  // Update probability slider value display
  const probabilitySlider = document.getElementById('probability');
  const probabilityValue = document.querySelector('.probability-value');
  
  probabilitySlider.addEventListener('input', function() {
    probabilityValue.textContent = this.value + '%';
  });
  
  // Auto-update probability based on stage selection
  const stageSelect = document.getElementById('stage');
  stageSelect.addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const suggestedProbability = selectedOption.getAttribute('data-probability');
    
    if (suggestedProbability) {
      probabilitySlider.value = suggestedProbability;
      probabilityValue.textContent = suggestedProbability + '%';
    }
    
    // Update visual badges
    document.querySelectorAll('.stage-badge').forEach(badge => {
      badge.classList.remove('selected');
      if (badge.getAttribute('data-stage') === this.value) {
        badge.classList.add('selected');
      }
    });
  });
  
  // Allow clicking stage badges to select stage
  document.querySelectorAll('.stage-badge').forEach(badge => {
    badge.addEventListener('click', function() {
      const stageName = this.getAttribute('data-stage');
      stageSelect.value = stageName;
      stageSelect.dispatchEvent(new Event('change'));
    });
  });
</script>

<?php include_once(__DIR__ . '/layout_end.php'); ?>
