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
    // Get customer_items
    $stmtItems = $conn->prepare('SELECT * FROM customer_items WHERE customer_id = ?');
    $stmtItems->bind_param('s', $customerId);
    $stmtItems->execute();
    $resultItems = $stmtItems->get_result();
    $items = [];
    while ($row = $resultItems->fetch_assoc()) {
        $items[] = $row;
    }
    $stmtItems->close();
    // Insert into archive table
    $stmtArchive = $conn->prepare('INSERT INTO customer_archive (customer_id, customer_data, items_data, deleted_at) VALUES (?, ?, ?, NOW())');
    $customerJson = json_encode($customer);
    $itemsJson = json_encode($items);
    $stmtArchive->bind_param('sss', $customerId, $customerJson, $itemsJson);
    $stmtArchive->execute();
    $stmtArchive->close();
    $conn->close();
    return true;
}
