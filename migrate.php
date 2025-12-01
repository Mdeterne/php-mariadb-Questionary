<?php
// http://localhost/Web-app-questionary/migrate.php --> les gars executer ce fichier pour faire les migrations
$host = "localhost";
$user = "root";
$pass = "";
$db   = "form";


$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Erreur de connexion MySQL: " . $conn->connect_error);
}


$done = [];
$res = $conn->query("SELECT migration FROM migrations");

while ($row = $res->fetch_assoc()) {
    $done[] = $row['migration'];
}

echo " Migrations déjà effectuées :\n";
print_r($done);


$migrationsDir = __DIR__ . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR . "database" . DIRECTORY_SEPARATOR . "migrations";



$files = scandir($migrationsDir);
sort($files);

foreach ($files as $file) {
    if (pathinfo($file, PATHINFO_EXTENSION) !== "sql") {
        continue;
    }

    if (in_array($file, $done)) {
        echo " Migration déjà appliquée : $file\n";
        continue;
    }

    echo "Exécution de : $file...\n";

    $sql = file_get_contents($migrationsDir . "/" . $file);

    if ($conn->multi_query($sql)) {

        
        while ($conn->more_results() && $conn->next_result()) {}

        
        $stmt = $conn->prepare("INSERT INTO migrations (migration) VALUES (?)");
        $stmt->bind_param("s", $file);
        $stmt->execute();

        echo " Migration appliquée : $file\n";
    } else {
        echo " Erreur dans $file : " . $conn->error . "\n";
        break;
    }
}

echo "\nToutes les migrations sont à jour !\n";
?>
