<?php
// api/contacts.php
header('Content-Type: application/json');
$sanitize_for_json = function($data) {
    if (is_array($data)) {
        $clean = [];
        foreach ($data as $k => $v) {
            $clean[$k] = $sanitize_for_json($v);
        }
        return $clean;
    } elseif (is_string($data)) {
        // Remove invalid UTF-8, normalize line endings, remove stray backslashes
        $data = mb_convert_encoding($data, 'UTF-8', 'UTF-8');
        $data = str_replace(["\r\n", "\r"], "\n", $data);
        $data = preg_replace('/\\(?!["\\\/bfnrtu])/', '', $data); // Remove stray backslashes not part of valid escapes
        return $data;
    } else {
        return $data;
    }
};
require_once __DIR__ . '/../csv_handler.php';
$schema = ['id','first_name','last_name','company','email','phone','address_1','address_2','city','province','postal_code','country','notes','tank_number','created_at','last_modified','tags','is_customer','customer_id'];

// Simple authentication check (placeholder, replace with real auth)
// if (!isset($_SERVER['HTTP_AUTHORIZATION'])) { http_response_code(401); echo json_encode(['error'=>'Unauthorized']); exit; }

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        $contacts = readCSV(__DIR__ . '/../contacts.csv', $schema);
        // Sanitize all data before encoding
        $contacts = $sanitize_for_json($contacts);
        echo json_encode($contacts);
        break;
    case 'POST':
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input || !isset($input['email'])) {
            http_response_code(400);
            echo json_encode(['error'=>'Invalid input']);
            exit;
        }
        $contacts = readCSV(__DIR__ . '/../contacts.csv', $schema);
        $input['id'] = uniqid('cnt_', true);
        $input['created_at'] = date('Y-m-d H:i:s');
        $input['last_modified'] = $input['created_at'];
        $contacts[] = $input;
        writeCSV(__DIR__ . '/../contacts.csv', $contacts, $schema);
        http_response_code(201);
        echo json_encode(['success'=>true,'id'=>$input['id']]);
        break;
    default:
        http_response_code(405);
        echo json_encode(['error'=>'Method not allowed']);
}
