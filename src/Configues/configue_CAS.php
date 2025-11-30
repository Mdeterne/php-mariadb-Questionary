<?php

require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

// Configuration du serveur CAS de l'Université de Limoges
define('CAS_HOST', 'cas.unilim.fr');
define('CAS_PORT', 443);
define('CAS_CONTEXT', '/cas');
define('CAS_PROTOCOL', '2.0');

// Variable globale pour tracer l'initialisation CAS
$CAS_INITIALIZED = false;

// Fonction pour initialiser CAS une seule fois (lazy initialization)
function initializeCAS() {
    global $CAS_INITIALIZED;
    
    if ($CAS_INITIALIZED) {
        return;
    }
    
    try {
        error_log("DEBUG: Tentative d'initialisation phpCAS");
        
        phpCAS::client(
            CAS_PROTOCOL,        // Version du protocole
            CAS_HOST,            // Serveur CAS
            CAS_PORT,            // Port HTTPS
            CAS_CONTEXT,         // Contexte
            true                 // Gérer la session nous-mêmes
        );
        
        // En développement : désactiver la validation SSL
        // À ACTIVER EN PRODUCTION avec un certificat valide
        phpCAS::setNoCasServerValidation();
        error_log("DEBUG: phpCAS initialisé avec succès");
        
        $CAS_INITIALIZED = true;
        
    } catch (Exception $e) {
        error_log("Erreur initialisation CAS : " . $e->getMessage());
    }
}


//Vérifie si l'utilisateur est authentifié via CAS
function isCASAuthenticated() {
    initializeCAS();
    try {
        return phpCAS::isAuthenticated();
    } catch (Exception $e) {
        error_log("Erreur vérification authentification CAS : " . $e->getMessage());
        return false;
    }
}


//Récupère l'identifiant de l'utilisateur CAS
function getCASUser() {
    if (isCASAuthenticated()) {
        try {
            return phpCAS::getUser();
        } catch (Exception $e) {
            error_log("Erreur récupération utilisateur CAS : " . $e->getMessage());
            return null;
        }
    }
    return null;
}


//Récupère tous les attributs LDAP/AD de l'utilisateur
function getCASAttributes() {
    if (isCASAuthenticated()) {
        try {
            return phpCAS::getAttributes();
        } catch (Exception $e) {
            error_log("Erreur récupération attributs CAS : " . $e->getMessage());
            return [];
        }
    }
    return [];
}


//Force l'authentification CAS
function requireCASLogin() {
    initializeCAS();
    if (!isCASAuthenticated()) {
        try {
            phpCAS::forceAuthentication();
        } catch (Exception $e) {
            error_log("Erreur lors de forceAuthentication : " . $e->getMessage());
        }
    }
}


//Déconnecte l'utilisateur du CAS
function logoutCAS($redirect_url = null) {
    initializeCAS();
    try {
        $logout_url = $redirect_url ?? 'index.php';
        phpCAS::logout(['url' => $logout_url]);
    } catch (Exception $e) {
        error_log("Erreur lors de la déconnexion CAS : " . $e->getMessage());
    }
}

?>
