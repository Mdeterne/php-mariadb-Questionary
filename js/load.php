<?php
// js/load.php - Script PHP pour servir les fichiers JS avec le bon header
// Cela contourne les problèmes de configuration Apache/MIME types sur certains serveurs.

$file = $_GET['f'] ?? '';

// Sécurité : on autorise que les fichiers alphanumériques + .js/.css
// On empêche de remonter dans les dossiers (..)
if (!preg_match('/^[a-zA-Z0-9_-]+\.(js|css)$/', $file)) {
    header("HTTP/1.0 400 Bad Request");
    echo "Fichier invalide ou interdit.";
    exit;
}

$path = __DIR__ . DIRECTORY_SEPARATOR . $file;

if (file_exists($path)) {
    // Définition du Content-Type correct
    if (str_ends_with($file, '.css')) {
        header('Content-Type: text/css');
    } else {
        header('Content-Type: application/javascript');
    }

    // Désactivation du cache pour le debug (optionnel, à retirer en prod si besoin)
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");

    readfile($path);
} else {
    header("HTTP/1.0 404 Not Found");
    echo "Fichier introuvable.";
}
