
<?php
include_once(__DIR__ . '/layout_start.php');
include_once(__DIR__ . '/navbar.php');
require_once 'csv_handler.php';

$schema = require __DIR__ . '/inventory_schema.php';
$inventoryFile = __DIR__ . '/inventory.csv';
$items = readCSV($inventoryFile, $schema);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_inventory'])) {
  $itemId = trim($_POST['item_id'] ?? '');
  $activeIndex = trim($_POST['active_index'] ?? '');
  if ($itemId !== '') {
    $items = readCSV($inventoryFile, $schema);
    foreach ($items as &$row) {
      if (($row['item_id'] ?? '') === $itemId) {
        foreach ($schema as $field) {
          if (array_key_exists($field, $_POST)) {
            $row[$field] = trim($_POST[$field] ?? '');
          }
        }
        $row['updated_at'] = date('Y-m-d H:i:s');
        break;
      }
    }
    unset($row);
    writeCSV($inventoryFile, $items, $schema);
  }
  $redirect = 'inventory_list.php';
  if ($activeIndex !== '' && ctype_digit($activeIndex)) {
    $redirect .= '?active_index=' . urlencode($activeIndex);
  } elseif ($itemId !== '') {
    $redirect .= '?active_item=' . urlencode($itemId);
  }
  header('Location: ' . $redirect);
  exit;
}

// Build filter array from GET
$filters = [];
foreach ($schema as $f) {
    $filters[$f] = isset($_GET[$f]) ? trim($_GET[$f]) : '';
}

// Filter items by all non-empty fields
$filtered = $items;
foreach ($filters as $field => $val) {
    if ($val !== '') {
        $filtered = array_filter($filtered, function($item) use ($field, $val) {
            return stripos($item[$field] ?? '', $val) !== false;
        });
    }
}

$statusOptions = ['Stock', 'Production'];
foreach ($items as $row) {
  $status = trim($row['status'] ?? '');
  if ($status !== '' && !in_array($status, $statusOptions, true)) {
    $statusOptions[] = $status;
  }
}
sort($statusOptions);
?>
<div class="container">
  <h2>Inventory List</h2>
  <div style="display:flex; justify-content:flex-end; margin-bottom:20px;">
    <a href="inventory_add.php" class="btn-outline">➕ Add Item</a>
  </div>
  <div style="display:flex; flex-wrap:wrap; gap:10px; align-items:center; margin-bottom:14px;">
    <input type="text" id="invSearch" placeholder="Search by item name or ID" style="padding:6px 8px; border-radius:4px; border:1px solid #bbb; min-width:240px;">
    <select id="invStatus" style="padding:6px 8px; border-radius:4px; border:1px solid #bbb;">
      <option value="">All Status</option>
      <?php foreach ($statusOptions as $status): ?>
        <option value="<?= htmlspecialchars($status) ?>"><?= htmlspecialchars($status) ?></option>
      <?php endforeach; ?>
    </select>
    <button type="button" id="invExpandAll">Expand All</button>
    <button type="button" id="invCollapseAll">Collapse All</button>
    <div id="invCount" style="margin-left:auto; color:#555;"></div>
  </div>
  <style>
    .inv-layout {
      display: grid;
      grid-template-columns: 280px 1fr;
      gap: 16px;
    }
    .inv-list {
      border: 1px solid #e2e2e2;
      border-radius: 8px;
      padding: 10px;
      background: #fff;
      max-height: 70vh;
      overflow: auto;
    }
    .inv-list-item {
      width: 100%;
      text-align: left;
      border: 1px solid #ddd;
      border-radius: 6px;
      padding: 8px 10px;
      background: #f9fafb;
      cursor: pointer;
      margin-bottom: 8px;
    }
    .inv-list-item.active {
      background: #e9eef6;
      border-color: #8aa4c8;
    }
    .inv-list-title { font-weight: 600; margin-bottom: 4px; }
    .inv-list-meta {
      display: flex;
      gap: 8px;
      flex-wrap: wrap;
      font-size: 0.9em;
      color: #555;
    }
    .inv-pill {
      display: inline-flex;
      align-items: center;
      padding: 2px 8px;
      border-radius: 999px;
      background: #f1f3f6;
      border: 1px solid #d9dee7;
    }
    .inv-pill.status { background: #eef4ff; border-color: #cddcff; color: #274690; }
    .inv-pill.qty { background: #e9f6ec; border-color: #b8e0c2; color: #1f6a35; }
    .inv-pill.stock-level { background: #fff6e5; border-color: #f5d19a; color: #8a5a00; }
    .inv-pill.low { background: #ffe8e8; border-color: #f2b6b6; color: #9b1c1c; }
    .inv-pill.ok { background: #e9f6ec; border-color: #b8e0c2; color: #1f6a35; }
    .inv-detail {
      border: 1px solid #e2e2e2;
      border-radius: 8px;
      padding: 14px;
      background: #fff;
      min-height: 200px;
    }
    .inv-detail-panel { display: none; }
    .inv-detail-panel.active { display: block; }
    .inv-detail-title { font-weight: 700; margin-bottom: 10px; }
    .inv-section {
      margin-bottom: 14px;
      border: 1px solid #e6e6e6;
      border-radius: 6px;
      padding: 10px 12px;
      background: #fafafa;
    }
    .inv-section-title {
      font-weight: 800;
      font-size: 1.05em;
      margin-bottom: 8px;
    }
    .inv-section-grid {
      display: grid;
      grid-template-columns: 160px 1fr 160px 1fr;
      gap: 8px 16px;
      align-items: center;
    }
    .inv-label { font-weight: 600; color: #222; }
    .inv-input {
      width: 100%;
      padding: 6px 8px;
      border-radius: 4px;
      border: 1px solid #e6e6e6;
      background: #fff;
      box-sizing: border-box;
      font-size: 0.95em;
    }
    .inv-input[readonly] { background: #f5f5f5; }
    .inv-textarea { height: 70px; resize: vertical; }
    @media (max-width: 900px) {
      .inv-layout { grid-template-columns: 1fr; }
      .inv-list { max-height: none; }
      .inv-section-grid { grid-template-columns: 160px 1fr; }
    }
  </style>
  <?php if (empty($filtered)): ?>
    <div style="text-align:center; color:#888;">No items found.</div>
  <?php else: ?>
    <?php
      $sections = [
        'General' => ['item_id','item_name','description','category','brand','model','serial_number','barcode','rfid_tag','status','notes'],
        'Supplier' => ['supplier_id','supplier_name','purchase_date'],
        'Pricing' => ['cost_price','margin','selling_price','currency'],
        'Stock' => ['quantity_in_stock','reorder_level','reorder_quantity','unit'],
        'Location' => ['warehouse','location'],
        'Audit' => ['created_at','updated_at','created_by','updated_by']
      ];
    ?>
    <div class="inv-layout">
      <div class="inv-list" id="invList">
        <?php foreach ($filtered as $index => $row): ?>
          <?php
            $title = trim(($row['item_name'] ?? '') ?: ($row['item_id'] ?? 'Inventory Item'));
            $qty = $row['quantity_in_stock'] ?? '';
            $reorder = $row['reorder_quantity'] ?? '';
            $qtyNum = is_numeric($qty) ? (float)$qty : null;
            $reorderNum = is_numeric($reorder) ? (float)$reorder : null;
            $isLowStock = $qtyNum !== null && $reorderNum !== null && $qtyNum < $reorderNum;
          ?>
          <button
            type="button"
            class="inv-list-item"
            data-target="inv-detail-<?= $index ?>"
            data-item-name="<?= htmlspecialchars(strtolower($row['item_name'] ?? '')) ?>"
            data-item-id="<?= htmlspecialchars(strtolower($row['item_id'] ?? '')) ?>"
            data-status="<?= htmlspecialchars(strtolower($row['status'] ?? '')) ?>"
          >
            <div class="inv-list-title"><?= htmlspecialchars($title) ?></div>
            <div class="inv-list-meta">
              <?php if (!empty($row['item_id'])): ?><span class="inv-pill" data-role="id">ID: <?= htmlspecialchars($row['item_id']) ?></span><?php endif; ?>
              <?php if (!empty($row['status'])): ?><span class="inv-pill status" data-role="status">Status: <?= htmlspecialchars($row['status']) ?></span><?php endif; ?>
              <?php if ($qty !== ''): ?>
                <span class="inv-pill qty <?= $isLowStock ? 'low' : 'ok' ?>" data-role="qty">Qty: <?= htmlspecialchars($qty) ?></span>
              <?php endif; ?>
              <?php if ($reorder !== ''): ?>
                <span class="inv-pill stock-level" data-role="stock-level">Stock Level: <?= htmlspecialchars($reorder) ?></span>
              <?php endif; ?>
            </div>
          </button>
        <?php endforeach; ?>
      </div>
      <div class="inv-detail" id="invDetail">
        <?php foreach ($filtered as $index => $row): ?>
          <?php
            $title = trim(($row['item_name'] ?? '') ?: ($row['item_id'] ?? 'Inventory Item'));
          ?>
          <div class="inv-detail-panel" id="inv-detail-<?= $index ?>">
            <div class="inv-detail-title"><?= htmlspecialchars($title) ?></div>
            <form method="post" class="inv-edit-form">
              <input type="hidden" name="save_inventory" value="1">
            <?php foreach ($sections as $sectionName => $fields): ?>
              <?php
                $hasValue = false;
                foreach ($fields as $field) {
                  if (!empty($row[$field])) { $hasValue = true; break; }
                }
              ?>
              <div class="inv-section">
                <div class="inv-section-title"><?= htmlspecialchars($sectionName) ?></div>
                <div class="inv-section-grid">
                  <?php foreach ($fields as $field): ?>
                    <?php
                      $label = ucwords(str_replace('_', ' ', $field));
                      if ($field === 'margin') {
                        $label = 'Margin (%)';
                      }
                    ?>
                    <div class="inv-label"><?= htmlspecialchars($label) ?></div>
                    <?php
                      $value = trim($row[$field] ?? '');
                      if ($field === 'margin' && $value === '') {
                        $value = '30';
                      }
                    ?>
                    <?php
                      $readonly = in_array($field, ['item_id', 'created_at', 'updated_at']);
                      $isTextarea = in_array($field, ['description', 'notes']);
                      $dataRole = '';
                      if ($field === 'cost_price') { $dataRole = 'cost'; }
                      if ($field === 'margin') { $dataRole = 'margin'; }
                      if ($field === 'selling_price') { $dataRole = 'selling'; }
                      $dataAttr = $dataRole !== '' ? ' data-role="' . $dataRole . '"' : '';
                    ?>
                    <?php if ($field === 'status'): ?>
                      <div>
                        <select class="inv-input status-select" name="status" data-current="<?= htmlspecialchars($value) ?>"></select>
                        <div style="margin-top:6px; display:flex; gap:6px; flex-wrap:wrap;">
                          <input type="text" class="inv-input status-input" placeholder="Add status" style="max-width:160px;">
                          <button type="button" class="status-add">Add</button>
                          <button type="button" class="status-remove">Remove</button>
                        </div>
                      </div>
                    <?php elseif ($isTextarea): ?>
                      <textarea class="inv-input inv-textarea" name="<?= $field ?>" <?= $readonly ? 'readonly' : '' ?><?= $dataAttr ?>><?= htmlspecialchars($value) ?></textarea>
                    <?php else: ?>
                      <input class="inv-input" type="text" name="<?= $field ?>" value="<?= htmlspecialchars($value) ?>" <?= $readonly ? 'readonly' : '' ?><?= $dataAttr ?>>
                    <?php endif; ?>
                  <?php endforeach; ?>
                </div>
              </div>
            <?php endforeach; ?>
              <div style="display:flex; gap:10px; margin-top:10px;">
                <button type="submit">Save Changes</button>
              </div>
            </form>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
    <div id="invNoMatch" style="display:none; text-align:center; color:#888; margin-top:12px;">No matching items.</div>
    <script>
      const listItems = Array.from(document.querySelectorAll('.inv-list-item'));
      const detailPanels = Array.from(document.querySelectorAll('.inv-detail-panel'));
      const searchInput = document.getElementById('invSearch');
      const statusSelect = document.getElementById('invStatus');
      const countEl = document.getElementById('invCount');
      const noMatchEl = document.getElementById('invNoMatch');
      const activeItemId = (new URLSearchParams(window.location.search).get('active_item') || '').toLowerCase();
      const activeIndexParam = new URLSearchParams(window.location.search).get('active_index');
      const activeIndex = Number.isInteger(parseInt(activeIndexParam, 10)) ? parseInt(activeIndexParam, 10) : -1;

      const defaultStatusOptions = <?= json_encode($statusOptions) ?>;
      const storedStatus = JSON.parse(localStorage.getItem('inventoryStatusOptions') || '[]');
      let statusOptions = Array.from(new Set([...defaultStatusOptions, ...storedStatus])).sort();

      function renderStatusSelects() {
        document.querySelectorAll('.status-select').forEach(select => {
          const current = select.getAttribute('data-current') || '';
          select.innerHTML = '';
          const emptyOption = document.createElement('option');
          emptyOption.value = '';
          emptyOption.textContent = 'Select status';
          select.appendChild(emptyOption);
          statusOptions.forEach(status => {
            const option = document.createElement('option');
            option.value = status;
            option.textContent = status;
            select.appendChild(option);
          });
          if (current && !statusOptions.includes(current)) {
            const customOption = document.createElement('option');
            customOption.value = current;
            customOption.textContent = current;
            select.appendChild(customOption);
          }
          select.value = current;
        });
      }

      function renderFilterStatusOptions() {
        if (!statusSelect) {
          return;
        }
        const current = statusSelect.value || '';
        statusSelect.innerHTML = '';
        const allOption = document.createElement('option');
        allOption.value = '';
        allOption.textContent = 'All Status';
        statusSelect.appendChild(allOption);
        statusOptions.forEach(status => {
          const option = document.createElement('option');
          option.value = status;
          option.textContent = status;
          statusSelect.appendChild(option);
        });
        if (current && !statusOptions.includes(current)) {
          const customOption = document.createElement('option');
          customOption.value = current;
          customOption.textContent = current;
          statusSelect.appendChild(customOption);
        }
        statusSelect.value = current;
      }

      function persistStatusOptions() {
        localStorage.setItem('inventoryStatusOptions', JSON.stringify(statusOptions));
      }

      function activateItem(item) {
        listItems.forEach(btn => btn.classList.remove('active'));
        detailPanels.forEach(panel => panel.classList.remove('active'));
        if (!item) {
          return;
        }
        item.classList.add('active');
        const targetId = item.getAttribute('data-target');
        const panel = document.getElementById(targetId);
        if (panel) {
          panel.classList.add('active');
        }
      }

      listItems.forEach(btn => {
        btn.addEventListener('click', () => activateItem(btn));
      });

      document.querySelectorAll('.inv-edit-form').forEach(form => {
        form.addEventListener('submit', event => {
          if (!confirm('Save changes to this inventory item?')) {
            event.preventDefault();
          }
        });

        form.querySelectorAll('.status-add').forEach(btn => {
          btn.addEventListener('click', () => {
            const input = form.querySelector('.status-input');
            const newStatus = (input.value || '').trim();
            if (newStatus && !statusOptions.includes(newStatus)) {
              statusOptions.push(newStatus);
              statusOptions.sort();
              persistStatusOptions();
              renderStatusSelects();
              renderFilterStatusOptions();
            }
            input.value = '';
          });
        });

        form.querySelectorAll('.status-remove').forEach(btn => {
          btn.addEventListener('click', () => {
            const select = form.querySelector('.status-select');
            const value = select.value;
            if (value === '') {
              return;
            }
            statusOptions = statusOptions.filter(s => s !== value);
            persistStatusOptions();
            renderStatusSelects();
            renderFilterStatusOptions();
          });
        });

        const statusSelectEl = form.querySelector('.status-select');
        if (statusSelectEl) {
          statusSelectEl.addEventListener('change', () => {
            const panel = statusSelectEl.closest('.inv-detail-panel');
            if (!panel) {
              return;
            }
            const targetId = panel.getAttribute('id');
            const listItem = document.querySelector(`.inv-list-item[data-target="${targetId}"]`);
            if (listItem) {
              const newStatus = statusSelectEl.value;
              listItem.setAttribute('data-status', (newStatus || '').toLowerCase());
              const meta = listItem.querySelector('.inv-list-meta');
              if (meta) {
                let statusPill = meta.querySelector('[data-role="status"]');
                if (newStatus) {
                  if (!statusPill) {
                    statusPill = document.createElement('span');
                    statusPill.className = 'inv-pill status';
                    statusPill.setAttribute('data-role', 'status');
                    meta.appendChild(statusPill);
                  }
                  statusPill.textContent = `Status: ${newStatus}`;
                  statusPill.style.display = '';
                } else if (statusPill) {
                  statusPill.style.display = 'none';
                }
              }
            }
            filterList();
          });
        }

        const costInput = form.querySelector('input[name="cost_price"]');
        const marginInput = form.querySelector('input[name="margin"]');
        const sellingInput = form.querySelector('input[name="selling_price"]');

        function updateSellingPrice() {
          if (!costInput || !marginInput || !sellingInput) {
            return;
          }
          const cost = parseFloat(costInput.value);
          const margin = parseFloat(marginInput.value);
          if (!Number.isFinite(cost) || !Number.isFinite(margin) || margin === 0) {
            return;
          }
          const selling = cost * (1 + (margin / 100));
          if (Number.isFinite(selling)) {
            sellingInput.value = selling.toFixed(2);
          }
        }

        if (costInput) {
          costInput.addEventListener('input', updateSellingPrice);
        }
        if (marginInput) {
          marginInput.addEventListener('input', updateSellingPrice);
        }

        updateSellingPrice();
      });

      function filterList() {
        const query = (searchInput.value || '').trim().toLowerCase();
        const status = (statusSelect.value || '').trim().toLowerCase();
        let visibleCount = 0;
        let firstVisible = null;

        listItems.forEach(btn => {
          const name = btn.getAttribute('data-item-name') || '';
          const id = btn.getAttribute('data-item-id') || '';
          const itemStatus = btn.getAttribute('data-status') || '';
          const matchesQuery = query === '' || name.includes(query) || id.includes(query);
          const matchesStatus = status === '' || itemStatus === status;
          const show = matchesQuery && matchesStatus;
          btn.style.display = show ? '' : 'none';
          if (show) {
            visibleCount += 1;
            if (!firstVisible) {
              firstVisible = btn;
            }
          }
        });

        if (countEl) {
          countEl.textContent = `${visibleCount} item(s)`;
        }
        if (noMatchEl) {
          noMatchEl.style.display = visibleCount === 0 ? 'block' : 'none';
        }

        const active = document.querySelector('.inv-list-item.active');
        if (!active || active.style.display === 'none') {
          activateItem(firstVisible);
        }
      }

      searchInput.addEventListener('input', filterList);
      statusSelect.addEventListener('change', filterList);

      renderStatusSelects();
      renderFilterStatusOptions();
      let initialItem = null;
      if (activeIndex >= 0 && activeIndex < listItems.length) {
        initialItem = listItems[activeIndex];
      } else if (activeItemId) {
        initialItem = listItems.find(item => (item.getAttribute('data-item-id') || '') === activeItemId);
      }
      if (!initialItem) {
        initialItem = listItems[0];
      }
      activateItem(initialItem);
      filterList();
    </script>
  <?php endif; ?>
</div>
<?php include_once(__DIR__ . '/layout_end.php'); ?>
