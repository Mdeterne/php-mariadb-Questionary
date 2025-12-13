<?php
require_once __DIR__ . '/src/Models/Database.php';
$pdo = (new Database())->getConnection();
$stmt = $pdo->query("DESCRIBE responses");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
