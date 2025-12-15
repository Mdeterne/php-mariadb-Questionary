<?php
session_start();
$_SESSION['user_id'] = 1;

require_once __DIR__ . '/src/Controleurs/tableauDeBordControlleur.php';

// Mock input
$input = json_encode([
    'id' => 1,
    'acceptResponses' => false,
    'dateStart' => '2025-01-01',
    'dateEnd' => '2025-12-31',
    'notifResponse' => true,
    'notifLimit' => false,
    'notifInvalid' => false
]);

// We can't easily mock php://input for a controller call without a real request or modifying the controller.
// So I will modify the controller temporarily to accept data as argument or read from a global if set, 
// OR I will just use curl to call the endpoint.

$url = "http://localhost:8080/index.php?c=tableauDeBord&a=saveSettings";
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $input);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIE, "PHPSESSID=" . session_id());

$response = curl_exec($ch);
curl_close($ch);

echo "Response: " . $response . "\n";

// Check DB
require_once __DIR__ . '/src/Models/Database.php';
$db = new Database();
$conn = $db->getConnection();
$stmt = $conn->query("SELECT status FROM surveys WHERE id = 1");
$row = $stmt->fetch(PDO::FETCH_ASSOC);
echo "Status in DB: " . $row['status'] . "\n";
