<?php
// Force correct session name before anything else (let Auth handle session_start)
if (session_status() === PHP_SESSION_NONE) {
    session_name('CRM_SESSION');
}
// Enforce authentication/session validation
require_once __DIR__ . '/simple_auth/middleware.php';
// Enforce authentication/session validation
require_once __DIR__ . '/simple_auth/middleware.php';
// export_table.php - User-friendly SQL table to CSV exporter with selection UI
require_once 'db_mysql.php';
require_once 'csv_handler.php';
require_once 'csrf_helper.php';

function exportCSVFiltered(
    string $filename,
    array $rows,
    array $filters = [],
    array $schema = []
) {
    $filtered = array_filter(
        $rows,
        function ($row) use ($filters) {
            foreach ($filters as $key => $value) {
                if (!isset($row[$key]) || $row[$key] !== $value) {
                    return false;
                }
            }
            return true;
        }
    );
    if (empty($schema) && !empty($filtered)) {
        $schema = array_keys(reset($filtered));
    }
    return writeCSV($filename, array_values($filtered), $schema);
}

$tableSchemas = [
    'contacts' => 'contact_schema.php',
    'customers' => 'customer_schema.php',
    'contracts' => 'contract_schema.php',
    'equipment' => 'equipment_schema.php',
    'opportunities' => 'opportunity_schema.php',
    'discussions' => 'discussion_schema.php',
    'inventory' => 'inventory_schema.php',
    'purchase_orders' => 'purchase_order_schema.php',
    'tags' => 'tag_schema.php',
];

$exported = [];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['tables'])) {
    // CSRF check
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid CSRF token. Please refresh and try again.';
    } else {
        foreach ($_POST['tables'] as $table) {
            if (!isset($tableSchemas[$table])) continue;
            $schemaFile = $tableSchemas[$table];
            $schema = require __DIR__ . '/' . $schemaFile;
            $filters = [];
            foreach ($schema as $field) {
                if (!empty($_POST[$field])) {
                    $filters[$field] = $_POST[$field];
                }
            }
            try {
                $conn = get_mysql_connection();
                $fields = implode(',', array_map(function($f) { return '`' . $f . '`'; }, $schema));
                $sql = "SELECT $fields FROM `$table`";
                $where = [];
                $params = [];
                foreach ($filters as $key => $value) {
                    $where[] = "$key = ?";
                    $params[] = $value;
                }
                if ($where) {
                    $sql .= " WHERE " . implode(' AND ', $where);
                }
                $stmt = $conn->prepare($sql);
                if ($params) {
                    $types = str_repeat('s', count($params));
                    $stmt->bind_param($types, ...$params);
                }
                $stmt->execute();
                $result = $stmt->get_result();
                $rows = [];
                while ($row = $result->fetch_assoc()) {
                    $rows[] = $row;
                }
                $stmt->close();
                $conn->close();
                $outputFile = __DIR__ . "/{$table}_export.csv";
                exportCSVFiltered($outputFile, $rows, $filters, $schema);
                $exported[] = [
                    'table' => $table,
                    'count' => count($rows),
                    'file' => "{$table}_export.csv"
                ];
            } catch (Exception $e) {
                $errors[] = "Export failed for $table: " . htmlspecialchars($e->getMessage());
            }
        }
    }
}

    // Layout and page title
    $pageTitle = 'Export Data Tables to CSV';
    include_once 'layout_start.php';
    ?>
    <div class="export-container">
    <h2 class="export-title">Export Data Tables to CSV</h2>

    <div class="export-table-select">
        <label class="export-label">Select tables to export:</label><br>
        <label class="export-checkbox-label">
            <input type="checkbox" id="selectAllTables" aria-label="Select all tables"> <b>Select All</b>
        </label>
        <?php foreach ($tableSchemas as $table => $schemaFile): ?>
            <label class="export-checkbox-label">
                <input type="checkbox" class="table-checkbox" name="tables[]" value="<?= htmlspecialchars($table) ?>" aria-label="<?= htmlspecialchars(ucfirst(str_replace('_', ' ', $table))) ?>">
                <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $table))) ?>
            </label>
        <?php endforeach; ?>
    </div>
    <script>
    // Select All functionality
    document.addEventListener('DOMContentLoaded', function() {
        var selectAll = document.getElementById('selectAllTables');
        var checkboxes = document.querySelectorAll('.table-checkbox');
        if (selectAll) {
            selectAll.addEventListener('change', function() {
                checkboxes.forEach(cb => cb.checked = selectAll.checked);
            });
        }
    });
    </script>
<?php if (!empty($exported)): ?>
    <div class="alert-success" role="status">
        <b>Export complete.</b><br>
        <?php foreach ($exported as $exp): ?>
            <?= htmlspecialchars($exp['table']) ?>: <?= $exp['count'] ?> rows exported to 
            <a href="<?= htmlspecialchars($exp['file']) ?>" download><?= htmlspecialchars($exp['file']) ?></a>.<br>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if (!empty($errors)): ?>
    <div class="alert-danger">
        <?php foreach ($errors as $err): ?>
            <?= $err ?><br>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form method="post" style="margin-bottom:24px;">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(getCSRFToken()) ?>">
    <div style="margin-bottom:16px;">
        <label style="font-weight:bold;">Select tables to export:</label><br>
        <?php foreach ($tableSchemas as $table => $schemaFile): ?>
            <label style="display:inline-block;margin-right:18px;margin-bottom:8px;">
                <input type="checkbox" name="tables[]" value="<?= htmlspecialchars($table) ?>">
                <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $table))) ?>
            </label>
        <?php endforeach; ?>
    </div>
    <button type="submit" class="btn btn-primary export-btn" aria-busy="false">Export Selected</button>
    <div id="exportSpinner" class="export-spinner" aria-hidden="true" style="display:none;">
        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Exporting...
    </div>
</form>
</div>
<a href="./" class="btn export-back">&larr; Back to Dashboard</a>
</div>
<style>
.export-container {
    max-width: 600px;
    margin: 40px auto;
    padding: 32px 24px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
}
.export-title {
    margin-top: 0;
}
.export-table-select {
    margin-bottom: 16px;
}
.export-label {
    font-weight: bold;
}
.export-checkbox-label {
    display: inline-block;
    margin-right: 18px;
    margin-bottom: 8px;
}
.export-btn {
    font-size: 16px;
    padding: 8px 20px;
}
.export-back {
    margin-top: 24px;
    display: inline-block;
}
.export-spinner {
    margin-top: 12px;
    color: #007bff;
    font-size: 16px;
}
</style>
<script>
// Select All functionality
document.addEventListener('DOMContentLoaded', function() {
    var selectAll = document.getElementById('selectAllTables');
    var checkboxes = document.querySelectorAll('.table-checkbox');
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
        });
    }
    // Loading spinner on submit
    var form = document.querySelector('form[method="post"]');
    var spinner = document.getElementById('exportSpinner');
    var btn = document.querySelector('.export-btn');
    if (form && spinner && btn) {
        form.addEventListener('submit', function() {
            spinner.style.display = 'inline-block';
            btn.setAttribute('aria-busy', 'true');
        });
    }
});
</script>
</div>
<?php include_once 'layout_end.php'; ?>
