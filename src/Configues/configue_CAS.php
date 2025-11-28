<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Désactiver l'autoloader dépréciée de phpCAS pour éviter les warnings
if (function_exists('spl_autoload_unregister')) {
    // Supprimer tous les autoloaders enregistrés par phpCAS avant l'include
    $autoloaders = spl_autoload_functions();
    if ($autoloaders) {
        foreach ($autoloaders as $autoloader) {
            spl_autoload_unregister($autoloader);
        }
    }
}

// Inclure phpCAS depuis les sources directement
require_once __DIR__ . '/../../lib/CAS/source/CAS.php';

// Réenregistrer les autoloaders si nécessaire
if (isset($autoloaders) && $autoloaders) {
    foreach ($autoloaders as $autoloader) {
        spl_autoload_register($autoloader);
    }
}

// CONFIGURATION DU SERVEUR CAS
$cas_host = 'cas.unilim.fr';
$cas_context = '/cas';
$cas_port = 443;

// Initialisation de phpCAS
phpCAS::client(CAS_VERSION_2_0, $cas_host, $cas_port, $cas_context, '');

// Désactiver la vérification du certificat (développement)
phpCAS::setNoCasServerValidation();

// Vérifier si l'utilisateur est authentifié
if (phpCAS::isAuthenticated()) {
    $_SESSION['cas_user'] = phpCAS::getUser();
} else {
    phpCAS::forceAuthentication(); 
}

?>