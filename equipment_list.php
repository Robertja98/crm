<?php
require_once 'layout_start.php';
require_once 'equipment_mysql.php';

// --- DELETE HANDLER ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log('POST array: ' . print_r($_POST, true));
    if (isset($_POST['delete_id'])) {
        $deleteId = $_POST['delete_id'];
        error_log('Delete handler triggered for ID: ' . $deleteId);
        if (!empty($deleteId)) {
            $conn = get_mysql_connection();
            $stmt = $conn->prepare('DELETE FROM equipment WHERE equipment_id = ?');
            $stmt->bind_param('s', $deleteId);
            $success = $stmt->execute();
            error_log('Delete SQL executed: ' . ($success ? 'success' : 'fail') . ' Rows affected: ' . $stmt->affected_rows);
            $stmt->close();
            $conn->close();
            // Redirect to self to prevent resubmission and refresh the list
            header('Location: equipment_list.php');
            exit();
        }
    }
}

$pageTitle = 'Equipment Inventory';
$equipmentSchema = require __DIR__ . '/equipment_schema.php';

$equipment = fetch_table_mysql('equipment', $equipmentSchema);
$customers = fetch_table_mysql('customers', require __DIR__ . '/customer_schema.php');
$contracts = fetch_table_mysql('contracts', require __DIR__ . '/contract_schema.php');

// Calculate metrics
$totalEquipment = count($equipment);
$customerOwned = 0;
$evoquaLeased = 0;
$serviceDue = 0;
$today = strtotime(date('Y-m-d'));

foreach ($equipment as &$item) {
    // Count by ownership
    if ($item['ownership'] === 'Customer Owned') {
        $customerOwned++;
    } elseif (strpos($item['ownership'], 'Evoqua') !== false) {
        $evoquaLeased++;
    }
    // Check service due
    if (!empty($item['next_service_date'])) {
        $nextService = strtotime($item['next_service_date']);
        if ($nextService <= $today) {
            $serviceDue++;
            $item['service_status'] = 'overdue';
        } elseif ($nextService <= strtotime('+7 days')) {
            $serviceDue++;
            $item['service_status'] = 'due-soon';
        } else {
            $item['service_status'] = 'scheduled';
        }
    }
}
?>

<style>
.equipment-header {
    background: linear-gradient(135deg, #8B5CF6 0%, #7C3AED 100%);
    color: white;
    padding: 32px;
    border-radius: 12px;
    margin-bottom: 24px;
}

.equipment-header h1 {
    margin: 0 0 8px 0;
    font-size: 32px;
    font-weight: 700;
}

/* Reuse metrics and table styling from contracts */
.metrics-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 20px;
    margin: 24px 0;
}

.metric-card {
    background: white;
    padding: 24px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    border-left: 4px solid #8B5CF6;
}

.metric-label {
    font-size: 13px;
    color: #6B7280;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 8px;
}

.metric-value {
    font-size: 32px;
    font-weight: 700;
    color: #1F2937;
}

.metric-subtext {
    font-size: 12px;
    color: #9CA3AF;
    margin-top: 4px;
}

.action-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    gap: 16px;
    flex-wrap: wrap;
}

.filter-group {
    display: flex;
    gap: 12px;
    align-items: center;
}

.filter-group select,
.filter-group input {
    padding: 10px 16px;
    border: 2px solid #E5E7EB;
    border-radius: 8px;
    font-size: 14px;
}

.btn {
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    border: none;
    font-size: 14px;
}

.btn-primary {
    background: linear-gradient(135deg, #8B5CF6 0%, #7C3AED 100%);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
}

.equipment-table {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.equipment-table table {
    width: 100%;
    border-collapse: collapse;
}

.equipment-table th {
    background: #F9FAFB;
    padding: 16px;
    text-align: left;
    font-size: 12px;
    font-weight: 700;
    color: #374151;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 2px solid #E5E7EB;
}

.equipment-table td {
    padding: 16px;
    border-bottom: 1px solid #F3F4F6;
    font-size: 14px;
}

.equipment-table tr:hover {
    background: #F9FAFB;
}

.ownership-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    display: inline-block;
}

.ownership-customer {
    background: #DBEAFE;
    color: #1E40AF;
}

.ownership-evoqua {
    background: #FEF3C7;
    color: #92400E;
}

.status-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    display: inline-block;
}

.status-active {
    background: #D1FAE5;
    color: #065F46;
}

.status-inactive {
    background: #FEE2E2;
    color: #991B1B;
}

.service-badge {
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 700;
    display: inline-block;
}

.service-scheduled {
    background: #D1FAE5;
    color: #065F46;
}

.service-due-soon {
    background: #FEF3C7;
    color: #92400E;
}

.service-overdue {
    background: #FEE2E2;
    color: #991B1B;
}

.action-btns {
    display: flex;
    gap: 8px;
}

.action-btn {
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 12px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.2s;
}

.action-btn-view {
    background: #EFF6FF;
    color: #1E40AF;
}

.action-btn-view:hover {
    background: #DBEAFE;
}

.action-btn-service {
    background: #D1FAE5;
    color: #065F46;
}

.action-btn-service:hover {
    background: #A7F3D0;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #6B7280;
}

.empty-state-icon {
    font-size: 64px;
    margin-bottom: 16px;
}
</style>

<div class="equipment-header">
    <h1>🔧 Equipment Inventory</h1>
    <p>Track all customer equipment and Evoqua leased systems</p>
</div>

<!-- Metrics Dashboard -->
<div class="metrics-grid">
    <div class="metric-card">
        <div class="metric-label">Total Equipment</div>
        <div class="metric-value"><?= $totalEquipment ?></div>
        <div class="metric-subtext">All tracked equipment</div>
    </div>
    
    <div class="metric-card">
        <div class="metric-label">Customer Owned</div>
        <div class="metric-value"><?= $customerOwned ?></div>
        <div class="metric-subtext">Equipment sold to customers</div>
    </div>
    
    <div class="metric-card">
        <div class="metric-label">Evoqua Leased</div>
        <div class="metric-value"><?= $evoquaLeased ?></div>
        <div class="metric-subtext">SDI service equipment</div>
    </div>
    
    <div class="metric-card <?= $serviceDue > 0 ? 'warning' : '' ?>">
        <div class="metric-label">Service Due</div>
        <div class="metric-value"><?= $serviceDue ?></div>
        <div class="metric-subtext">Needs service soon/overdue</div>
    </div>
</div>

<!-- Action Bar -->
<div class="action-bar">
    <div class="filter-group">
        <select id="ownershipFilter" onchange="filterEquipment()">
            <option value="">All Ownership</option>
            <option value="Customer Owned">Customer Owned</option>
            <option value="Evoqua Lease">Evoqua Lease</option>
            <option value="Evoqua Rental">Evoqua Rental</option>
        </select>
        
        <select id="typeFilter" onchange="filterEquipment()">
            <option value="">All Types</option>
            <option value="Softener">Softener</option>
            <option value="RO System">RO System</option>
            <option value="Filtration">Filtration</option>
            <option value="DI Tank">DI Tank</option>
        </select>
        
        <select id="statusFilter" onchange="filterEquipment()">
            <option value="">All Status</option>
            <option value="Active">Active</option>
            <option value="Inactive">Inactive</option>
        </select>
        
        <input type="text" id="searchInput" placeholder="Search equipment..." onkeyup="filterEquipment()">
    </div>
    
    <a href="equipment_form.php" class="btn btn-primary">➕ Add Equipment</a>
</div>

<!-- Equipment Table -->
<div class="equipment-table">
    <?php if (empty($equipment)): ?>
        <div class="empty-state">
            <div class="empty-state-icon">🔧</div>
            <h3>No Equipment Tracked Yet</h3>
            <p>Start by adding your first piece of equipment</p>
            <a href="equipment_form.php" class="btn btn-primary" style="margin-top: 20px;">➕ Add First Equipment</a>
        </div>
    <?php else: ?>
        <table id="equipmentTable">
            <thead>
                <tr>
                    <th>Equipment ID</th>
                    <th>Type</th>
                    <th>Model/Serial</th>
                    <th>Customer</th>
                    <th>Ownership</th>
                    <th>Install Date</th>
                    <th>Service Status</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($equipment as $item): ?>
                    <?php
                    // Find customer name
                    $customerName = 'Unknown';
                    foreach ($customers as $c) {
                        if ($c['customer_id'] === $item['customer_id']) {
                            $customerName = $c['contact_name'];
                            break;
                        }
                    }
                    
                    $ownershipClass = 'ownership-' . (strpos($item['ownership'], 'Customer') !== false ? 'customer' : 'evoqua');
                    $statusClass = 'status-' . strtolower($item['status'] ?? 'active');
                    $serviceClass = 'service-' . ($item['service_status'] ?? 'scheduled');
                    ?>
                    <tr data-ownership="<?= htmlspecialchars($item['ownership']) ?>" 
                        data-type="<?= htmlspecialchars($item['equipment_type']) ?>"
                        data-status="<?= htmlspecialchars($item['status'] ?? 'Active') ?>">
                        <td><strong><?= htmlspecialchars($item['equipment_id']) ?></strong></td>
                        <td><?= htmlspecialchars($item['equipment_type']) ?></td>
                        <td>
                            <?php if (!empty($item['model_number'])): ?>
                                <?= htmlspecialchars($item['model_number']) ?><br>
                                <small style="color: #6B7280;"><?= htmlspecialchars($item['serial_number']) ?></small>
                            <?php else: ?>
                                <?= htmlspecialchars($item['serial_number']) ?>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($customerName) ?></td>
                        <td>
                            <span class="ownership-badge <?= $ownershipClass ?>">
                                <?= htmlspecialchars($item['ownership']) ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($item['install_date']) ?></td>
                        <td>
                            <?php if (!empty($item['next_service_date'])): ?>
                                <span class="service-badge <?= $serviceClass ?>">
                                    <?php if ($item['service_status'] === 'overdue'): ?>
                                        ⚠️ Overdue
                                    <?php elseif ($item['service_status'] === 'due-soon'): ?>
                                        ⏰ Due Soon
                                    <?php else: ?>
                                        ✓ <?= htmlspecialchars($item['next_service_date']) ?>
                                    <?php endif; ?>
                                </span>
                            <?php else: ?>
                                <span style="color: #9CA3AF;">Not scheduled</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="status-badge <?= $statusClass ?>">
                                <?= htmlspecialchars($item['status'] ?? 'Active') ?>
                            </span>
                        </td>
                        <td>
                            <div class="action-btns">
                                <a href="equipment_view.php?id=<?= urlencode($item['equipment_id']) ?>" 
                                   class="action-btn action-btn-view">View</a>
                                <?php if ($item['service_status'] === 'overdue' || $item['service_status'] === 'due-soon'): ?>
                                    <a href="service_log.php?equipment_id=<?= urlencode($item['equipment_id']) ?>" 
                                       class="action-btn action-btn-service">Service Now</a>
                                <?php endif; ?>
                                <form method="POST" action="" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this equipment? This action cannot be undone.');">
                                    <input type="hidden" name="delete_id" value="<?= htmlspecialchars($item['equipment_id']) ?>">
                                    <button type="submit" class="action-btn action-btn-delete" style="background:#dc2626;color:#fff;padding:4px 10px;border:none;border-radius:4px;cursor:pointer;">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<script>
function filterEquipment() {
    const ownershipFilter = document.getElementById('ownershipFilter').value.toLowerCase();
    const typeFilter = document.getElementById('typeFilter').value.toLowerCase();
    const statusFilter = document.getElementById('statusFilter').value.toLowerCase();
    const searchInput = document.getElementById('searchInput').value.toLowerCase();
    const table = document.getElementById('equipmentTable');
    const rows = table.getElementsByTagName('tr');
    
    for (let i = 1; i < rows.length; i++) {
        const row = rows[i];
        const ownership = row.getAttribute('data-ownership').toLowerCase();
        const type = row.getAttribute('data-type').toLowerCase();
        const status = row.getAttribute('data-status').toLowerCase();
        const text = row.textContent.toLowerCase();
        
        let showRow = true;
        
        if (ownershipFilter && ownership !== ownershipFilter) showRow = false;
        if (typeFilter && type !== typeFilter) showRow = false;
        if (statusFilter && status !== statusFilter) showRow = false;
        if (searchInput && !text.includes(searchInput)) showRow = false;
        
        row.style.display = showRow ? '' : 'none';
    }
}
</script>

<?php include_once(__DIR__ . '/layout_end.php'); ?>
