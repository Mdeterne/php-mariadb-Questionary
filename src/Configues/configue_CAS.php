<?php

require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

// Enable verbose error messages. Disable in production!
phpCAS::setVerbose(true);

// Initialize phpCAS
phpCAS::client(CAS_VERSION_2_0, "cas.unilim.fr", 443, 'cas', "http://164.81.120.75",true);

// pour la version définitive avec certificat SSL
//phpCAS::setCasServerCACert($cas_server_ca_cert_path);

// pour tester en local sans certificat SSL
phpCAS::setNoCasServerValidation();

phpCAS::forceAuthentication();

?>
