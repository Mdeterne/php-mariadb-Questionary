<?php
require_once __DIR__ . '/src/Models/Database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    $sql = file_get_contents(__DIR__ . '/src/database/migrations/009_add_scale_labels.sql');
    
    if (!$sql) {
        die("Erreur de lecture du fichier migration.");
    }
    
    echo "Application de la migration 009...\n";
    $conn->exec($sql);
    echo "✅ Migration réussie.\n";
    
} catch (PDOException $e) {
    if (strpos($e->getMessage(), "Duplicate column name") !== false) {
        echo "⚠️ Migration déjà appliquée (Colonnes existent).\n";
    } else {
        echo "❌ Erreur : " . $e->getMessage() . "\n";
    }
}
