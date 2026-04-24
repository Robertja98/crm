<?php
// Archive deleted customer info
function archive_customer($customerId) {
    require_once 'db_mysql.php';
    $conn = get_mysql_connection();
    // Get customer info
    $stmt = $conn->prepare('SELECT * FROM customers WHERE customer_id = ?');
    $stmt->bind_param('s', $customerId);
    $stmt->execute();
    $result = $stmt->get_result();
    $customer = $result ? $result->fetch_assoc() : null;
    $stmt->close();
    if (!$customer) {
        $conn->close();
        return false;
    }
    // Get equipment rows for this customer
    $stmtEq = $conn->prepare('SELECT * FROM equipment WHERE customer_id = ?');
    $stmtEq->bind_param('s', $customerId);
    $stmtEq->execute();
    $resultEq = $stmtEq->get_result();
    $items = [];
    while ($row = $resultEq->fetch_assoc()) {
        $items[] = $row;
    }
    $stmtEq->close();
    // Insert into audit_log as a soft archive (customer_archive table may not exist)
    $stmtArchive = $conn->prepare(
        'INSERT INTO audit_log (user_id, action, entity_type, entity_id, changes, summary, timestamp) VALUES (0, "DELETE", "customers", ?, ?, "Customer deleted", NOW())'
    );
    $archiveJson = json_encode(['customer' => $customer, 'equipment' => $items]);
    $stmtArchive->bind_param('ss', $customerId, $archiveJson);
    $stmtArchive->execute();
    $stmtArchive->close();
    $conn->close();
    return true;
}
