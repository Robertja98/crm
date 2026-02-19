<?php
require_once 'layout_start.php';
require_once 'db_mysql.php';

$pageTitle = 'Add Service Contract';
$contractSchema = require __DIR__ . '/contract_schema.php';

function fetch_mysql($table, $schema) {
    $conn = get_mysql_connection();
    $fields = implode(',', array_map(function($f) { return '`' . $f . '`'; }, $schema));
    $result = $conn->query("SELECT $fields FROM $table");
    $rows = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        $result->free();
    }
    $conn->close();
    return $rows;
}

$contacts = fetch_mysql('contacts', require __DIR__ . '/contact_schema.php');
$customers = fetch_mysql('customers', require __DIR__ . '/customer_schema.php');
$equipment = fetch_mysql('equipment', require __DIR__ . '/equipment_schema.php');

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Generate contract ID (find max contract_id in DB)
    $conn = get_mysql_connection();
    $result = $conn->query("SELECT COUNT(*) AS cnt FROM contracts");
    $row = $result ? $result->fetch_assoc() : null;
    $count = $row ? (int)$row['cnt'] : 0;
    $contractId = 'CNT-' . date('Ymd') . '-' . str_pad($count + 1, 4, '0', STR_PAD_LEFT);

    // Calculate values
    $monthlyFee = (float)$_POST['monthly_fee'];
    $contractTerm = (int)$_POST['contract_term'];
    $annualValue = $monthlyFee * 12;

    // Calculate end date
    $startDate = $_POST['start_date'];
    $endDate = date('Y-m-d', strtotime($startDate . ' + ' . $contractTerm . ' months'));

    // Calculate renewal date (end date - notice period)
    $noticePeriod = (int)$_POST['notice_period'];
    $renewalDate = date('Y-m-d', strtotime($endDate . ' - ' . $noticePeriod . ' days'));

    // Validate date fields
    function valid_date_or_null($val) {
        $val = trim($val ?? '');
        return ($val === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $val)) ? null : $val;
    }
    $fields = [
        'contract_id' => $contractId,
        'contact_id' => $_POST['contact_id'],
        'customer_id' => (isset($_POST['customer_id']) && is_numeric($_POST['customer_id']) && $_POST['customer_id'] !== '' ? (int)$_POST['customer_id'] : null),
        'contract_type' => $_POST['contract_type'],
        'contract_status' => 'Active',
        'equipment_type' => $_POST['equipment_type'],
        'monthly_fee' => $monthlyFee,
        'annual_value' => $annualValue,
        'payment_frequency' => $_POST['payment_frequency'],
        'contract_term' => $contractTerm,
        'start_date' => $startDate,
        'end_date' => $endDate,
        'renewal_date' => $renewalDate,
        'auto_renew' => $_POST['auto_renew'] ?? 'No',
        'notice_period' => $noticePeriod,
        'evoqua_account' => $_POST['evoqua_account'] ?? '',
        'evoqua_contract' => $_POST['evoqua_contract'] ?? '',
        'equipment_ids' => $_POST['equipment_ids'] ?? '',
        'service_frequency' => $_POST['service_frequency'],
        'last_service_date' => valid_date_or_null($_POST['last_service_date'] ?? ''),
        'next_service_date' => valid_date_or_null($_POST['next_service_date'] ?? ''),
        'notes' => $_POST['notes'] ?? '',
        'created_date' => date('Y-m-d H:i:s'),
        'created_by' => $_SESSION['user_id'] ?? 'system',
        'modified_date' => date('Y-m-d H:i:s'),
        'modified_by' => $_SESSION['user_id'] ?? 'system'
    ];

    $columns = implode(',', array_map(function($k) { return '`' . $k . '`'; }, array_keys($fields)));
    $placeholders = implode(',', array_fill(0, count($fields), '?'));
    $types = '';
    foreach ($fields as $k => $v) {
        if (is_int($v)) {
            $types .= 'i';
        } elseif (is_float($v)) {
            $types .= 'd';
        } else {
            $types .= 's';
        }
    }
    $stmt = $conn->prepare("INSERT INTO contracts ($columns) VALUES ($placeholders)");
    $stmt->bind_param($types, ...array_values($fields));
    $result = $stmt->execute();
    if ($result) {
        $stmt->close();
        $conn->close();
        // Clean output buffer before redirect
        if (ob_get_length()) {
            ob_end_clean();
        }
        header('Location: contracts_list.php?success=1');
        exit;
    } else {
        echo '<div style="color:red;"><b>Failed to add contract:</b> ' . htmlspecialchars($stmt->error) . '</div>';
    }
}
?>

<!-- Debug Section (visible only if DEBUG is true) -->

<style>
.page-header {
    background: linear-gradient(135deg, #10B981 0%, #059669 100%);
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

.form-container {
    background: white;
    padding: 32px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    max-width: 1200px;
}

.form-section {
    margin-bottom: 32px;
    padding-bottom: 32px;
    border-bottom: 2px solid #E5E7EB;
}

.form-section:last-child {
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
.form-group select,
.form-group textarea {
    padding: 12px 16px;
    border: 2px solid #E5E7EB;
    border-radius: 8px;
    font-size: 15px;
    font-family: inherit;
    transition: all 0.2s;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #10B981;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

.form-group textarea {
    resize: vertical;
    min-height: 100px;
}

.calculated-value {
    background: #F0FDF4;
    padding: 16px;
    border-radius: 8px;
    border: 2px solid #10B981;
    font-weight: 700;
    color: #065F46;
    font-size: 20px;
}

.form-actions {
    display: flex;
    gap: 12px;
    margin-top: 32px;
}

.btn {
    padding: 14px 32px;
    border-radius: 8px;
    font-weight: 700;
    font-size: 15px;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
    display: inline-block;
    border: none;
}

.btn-primary {
    background: linear-gradient(135deg, #10B981 0%, #059669 100%);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.btn-secondary {
    background: #F3F4F6;
    color: #374151;
    border: 2px solid #E5E7EB;
}

.btn-secondary:hover {
    background: #E5E7EB;
}

.form-help {
    font-size: 12px;
    color: #6B7280;
    margin-top: 6px;
}

@media (max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<a href="contracts_list.php" style="display: inline-flex; align-items: center; gap: 8px; color: #10B981; text-decoration: none; font-weight: 600; margin-bottom: 16px;">
    ← Back to Contracts
</a>

<div class="page-header">
    <h1>📋 Add Service Contract</h1>
    <p>Create a new SDI service agreement</p>
</div>

<?php if (isset($error)): ?>
    <div style="background: #FEE2E2; border: 2px solid #EF4444; color: #991B1B; padding: 16px; border-radius: 8px; margin-bottom: 24px;">
        ⚠️ <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<div class="form-container">
    <form method="POST" id="contractForm">
        <!-- Contact & Customer Information -->
        <div class="form-section">
            <div class="form-section-title">👤 Contact & Customer Information</div>
            <div class="form-grid">
                <div class="form-group">
                    <label for="contact_id">Contact *</label>
                    <select name="contact_id" id="contact_id" required>
                        <option value="">Select Contact</option>
                        <?php foreach ($contacts as $contact): ?>
                            <option value="<?= htmlspecialchars($contact['id']) ?>">
                                <?= htmlspecialchars(trim($contact['first_name'] . ' ' . $contact['last_name'])) ?> 
                                <?php if (!empty($contact['company'])): ?>
                                    - <?= htmlspecialchars($contact['company']) ?>
                                <?php endif; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="customer_id">Customer ID (Optional)</label>
                    <input type="text" name="customer_id" id="customer_id">
                    <div class="form-help">Leave blank if contact is the customer</div>
                </div>
            </div>
        </div>
        
        <!-- Contract Details -->
        <div class="form-section">
            <div class="form-section-title">📄 Contract Details</div>
            <div class="form-grid">
                <div class="form-group">
                    <label for="contract_type">Contract Type *</label>
                    <select name="contract_type" id="contract_type" required>
                        <option value="">Select Type</option>
                        <option value="New">New Contract</option>
                        <option value="Renewal">Renewal</option>
                        <option value="Upsell">Upsell</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="equipment_type">Equipment Type *</label>
                    <select name="equipment_type" id="equipment_type" required>
                        <option value="">Select Equipment</option>
                        <option value="Softener">Water Softener</option>
                        <option value="RO System">RO System</option>
                        <option value="Filtration">Filtration System</option>
                        <option value="DI System">DI System</option>
                        <option value="Mixed Systems">Mixed Systems</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="monthly_fee">Monthly Fee ($) *</label>
                    <input type="number" name="monthly_fee" id="monthly_fee" step="0.01" min="0" required 
                           onchange="calculateAnnualValue()">
                </div>
                
                <div class="form-group">
                    <label for="annual_value">Annual Contract Value ($)</label>
                    <div class="calculated-value" id="annual_value_display">$0.00</div>
                    <div class="form-help">Calculated automatically</div>
                </div>
                
                <div class="form-group">
                    <label for="payment_frequency">Payment Frequency *</label>
                    <select name="payment_frequency" id="payment_frequency" required>
                        <option value="Monthly">Monthly</option>
                        <option value="Quarterly">Quarterly</option>
                        <option value="Annual">Annual</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="contract_term">Contract Term (Months) *</label>
                    <select name="contract_term" id="contract_term" required onchange="calculateDates()">
                        <option value="12">12 Months</option>
                        <option value="24">24 Months</option>
                        <option value="36">36 Months</option>
                        <option value="48">48 Months</option>
                        <option value="60">60 Months</option>
                    </select>
                </div>
            </div>
        </div>
        
        <!-- Dates & Terms -->
        <div class="form-section">
            <div class="form-section-title">📅 Dates & Terms</div>
            <div class="form-grid">
                <div class="form-group">
                    <label for="start_date">Contract Start Date *</label>
                    <input type="date" name="start_date" id="start_date" required onchange="calculateDates()">
                </div>
                
                <div class="form-group">
                    <label for="end_date">Contract End Date</label>
                    <div class="calculated-value" id="end_date_display">Not calculated</div>
                    <div class="form-help">Calculated from start date + term</div>
                </div>
                
                <div class="form-group">
                    <label for="notice_period">Cancellation Notice Period (Days) *</label>
                    <select name="notice_period" id="notice_period" required onchange="calculateDates()">
                        <option value="30">30 Days</option>
                        <option value="60">60 Days</option>
                        <option value="90">90 Days</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="renewal_date">Renewal Notification Date</label>
                    <div class="calculated-value" id="renewal_date_display">Not calculated</div>
                    <div class="form-help">End date minus notice period</div>
                </div>
                
                <div class="form-group">
                    <label for="auto_renew">Auto-Renewal</label>
                    <select name="auto_renew" id="auto_renew">
                        <option value="Yes">Yes - Auto Renew</option>
                        <option value="No">No - Manual Renewal</option>
                    </select>
                </div>
            </div>
        </div>
        
        <!-- Evoqua Details -->
        <div class="form-section">
            <div class="form-section-title">🏢 Evoqua Details</div>
            <div class="form-grid">
                <div class="form-group">
                    <label for="evoqua_account">Evoqua Account Number</label>
                    <input type="text" name="evoqua_account" id="evoqua_account">
                </div>
                
                <div class="form-group">
                    <label for="evoqua_contract">Evoqua Contract Number</label>
                    <input type="text" name="evoqua_contract" id="evoqua_contract">
                </div>
            </div>
        </div>
        
        <!-- Service Schedule -->
        <div class="form-section">
            <div class="form-section-title">🔧 Service Schedule</div>
            <div class="form-grid">
                <div class="form-group">
                    <label for="service_frequency">Service Frequency *</label>
                    <select name="service_frequency" id="service_frequency" required>
                        <option value="Weekly">Weekly</option>
                        <option value="Bi-weekly">Bi-weekly</option>
                        <option value="Monthly">Monthly</option>
                        <option value="Quarterly">Quarterly</option>
                        <option value="Semi-Annual">Semi-Annual</option>
                        <option value="Annual">Annual</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="next_service_date">Next Service Date</label>
                    <input type="date" name="next_service_date" id="next_service_date">
                </div>
                
                <div class="form-group full-width">
                    <label for="notes">Contract Notes</label>
                    <textarea name="notes" id="notes" placeholder="Additional contract notes, special terms, etc."></textarea>
                </div>
            </div>
        </div>
        
        <!-- Actions -->
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">💾 Create Contract</button>
            <a href="contracts_list.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<script>
function calculateAnnualValue() {
    const monthlyFee = parseFloat(document.getElementById('monthly_fee').value) || 0;
    const annualValue = monthlyFee * 12;
    document.getElementById('annual_value_display').textContent = '$' + annualValue.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

function calculateDates() {
    const startDate = document.getElementById('start_date').value;
    const termMonths = parseInt(document.getElementById('contract_term').value);
    const noticeDays = parseInt(document.getElementById('notice_period').value);
    
    if (!startDate || !termMonths) return;
    
    // Calculate end date
    const start = new Date(startDate);
    const end = new Date(start);
    end.setMonth(end.getMonth() + termMonths);
    
    const endDateStr = end.toISOString().split('T')[0];
    document.getElementById('end_date_display').textContent = endDateStr;
    
    // Calculate renewal date
    const renewal = new Date(end);
    renewal.setDate(renewal.getDate() - noticeDays);
    const renewalDateStr = renewal.toISOString().split('T')[0];
    document.getElementById('renewal_date_display').textContent = renewalDateStr;
}

// Set default start date to today
document.getElementById('start_date').valueAsDate = new Date();
calculateDates();
</script>

<?php include_once(__DIR__ . '/layout_end.php'); ?>
