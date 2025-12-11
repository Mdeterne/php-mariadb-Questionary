<?php
// http://localhost/Web-app-questionary/migrate.php --> Executer ce fichier pour faire les migrations

$possible_creds = [
    ['host' => '127.0.0.1', 'user' => 'root', 'pass' => 'root'],       // MAMP default
    ['host' => '127.0.0.1', 'user' => 'mariadb', 'pass' => 'mariadb'], // Docker default
    ['host' => '127.0.0.1', 'user' => 'root', 'pass' => ''],           // WAMP/XAMPP default
    ['host' => 'localhost', 'user' => 'root', 'pass' => 'root'],       // Localhost fallback
    ['host' => '127.0.0.1', 'user' => 'root', 'pass' => 'mariadb'],      // Docker root default
    ['host' => 'localhost', 'user' => 'root', 'pass' => 'root', 'socket' => '/tmp/mysql.sock'], // Socket default
    ['host' => 'localhost', 'user' => 'root', 'pass' => '', 'socket' => '/tmp/mysql.sock'],     // Socket no pass
    ['host' => 'localhost', 'user' => 'milan', 'pass' => '', 'socket' => '/tmp/mysql.sock'],    // User no pass
];

$db_name = "questionary";
$conn = null;
$connected_cred = null;
$errors = [];

echo "Tentative de connexion à la base de données...\n";

foreach ($possible_creds as $cred) {
    try {
        // Suppress warnings to avoid cluttering output on failure
        $socket = isset($cred['socket']) ? $cred['socket'] : null;
        $conn = new mysqli($cred['host'], $cred['user'], $cred['pass'], "", 3306, $socket);

        if ($conn->connect_error) {
            $errors[] = "echec {$cred['user']}@{$cred['host']}: " . $conn->connect_error;
            continue;
        }

        $connected_cred = $cred;
        echo "✅ Connexion réussie avec l'utilisateur '{$cred['user']}' sur '{$cred['host']}'\n";
        break;
    } catch (Exception $e) {
        $errors[] = "echec {$cred['user']}@{$cred['host']}: " . $e->getMessage();
        continue;
    }
}

if (!$conn) {
    echo "❌ Impossible de se connecter à la base de données. Vérifiez que votre serveur (MAMP/Docker) est lancé.\n";
    echo "Détails des erreurs :\n";
    foreach ($errors as $err) {
        echo " - $err\n";
    }
    exit(1);
}

// Create database if not exists
echo "Vérification de la base de données '$db_name'...\n";
if (!$conn->query("CREATE DATABASE IF NOT EXISTS `$db_name`")) {
    die("❌ Erreur lors de la création de la base de données : " . $conn->error . "\n");
}

// Select the database
if (!$conn->select_db($db_name)) {
    die("❌ Impossible de sélectionner la base de données '$db_name' : " . $conn->error . "\n");
}

// Create migrations table if not exists
$conn->query("CREATE TABLE IF NOT EXISTS migrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    migration VARCHAR(255) NOT NULL,
    executed_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");

$done = [];
$res = $conn->query("SELECT migration FROM migrations");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $done[] = $row['migration'];
    }
}

echo "Migrations déjà effectuées :\n";
if (empty($done)) {
    echo "Aucune.\n";
} else {
    print_r($done);
}

$migrationsDir = __DIR__ . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR . "database" . DIRECTORY_SEPARATOR . "migrations";

if (!is_dir($migrationsDir)) {
    die("❌ Dossier de migrations introuvable : $migrationsDir\n");
}

$files = scandir($migrationsDir);
sort($files);

foreach ($files as $file) {
    if (pathinfo($file, PATHINFO_EXTENSION) !== "sql") {
        continue;
    }

    if (in_array($file, $done)) {
        continue;
    }

    echo "Exécution de : $file...\n";

    $sql = file_get_contents($migrationsDir . "/" . $file);

    // Split SQL by semicolon slightly more robustly (basic implementation)
    // Multi_query is better but can be tricky with error handling per statement
    if ($conn->multi_query($sql)) {
        do {
            // consume all results
            if ($result = $conn->store_result()) {
                $result->free();
            }
        } while ($conn->more_results() && $conn->next_result());

        if ($conn->errno) {
            echo "❌ Erreur dans $file : " . $conn->error . "\n";
            break;
        }

        $stmt = $conn->prepare("INSERT INTO migrations (migration) VALUES (?)");
        $stmt->bind_param("s", $file);
        $stmt->execute();

        echo "✅ Migration appliquée : $file\n";
    } else {
        echo "❌ Erreur dans $file : " . $conn->error . "\n";
        break;
    }
}

echo "\n✨ Toutes les migrations sont à jour !\n";
if ($connected_cred['user'] !== 'root' || $connected_cred['pass'] !== 'root') {
    echo "\n⚠️  ATTENTION : Vos identifiants diffèrent de ceux par défaut.\n";
    echo "Pour que le site fonctionne, mettez à jour 'src/Models/Database.php' :\n";
    echo "   private \$username = \"{$connected_cred['user']}\";\n";
    echo "   private \$password = \"{$connected_cred['pass']}\";\n";
}
?>