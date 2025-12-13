<?php
require_once __DIR__ . '/src/Models/Database.php';
$pdo = (new Database())->getConnection();
try {
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    $pdo->exec("TRUNCATE TABLE answer_choices");
    $pdo->exec("TRUNCATE TABLE answers");
    $pdo->exec("TRUNCATE TABLE responses");
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    echo "Tables Truncated Successfully.\n";
} catch (PDOException $e) {
    die("Error truncating: " . $e->getMessage() . "\n");
}
