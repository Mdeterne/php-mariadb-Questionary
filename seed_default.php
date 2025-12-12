<?php
// seed_default.php

require_once __DIR__ . '/src/Models/Database.php';
require_once __DIR__ . '/src/Models/user.php';
require_once __DIR__ . '/src/Models/questionnaire.php';

try {
    echo "--- Début du seed ---\n";

    // 1. S'assurer que l'utilisateur ID 1 existe (utilisé par index.php)
    $userModel = new User();
    $targetId = 1;
    $user = $userModel->findById($targetId);

    if (!$user) {
        echo "⚠️  Utilisateur ID 1 non trouvé. Création forcée...\n";
        // Force insert user with ID 1
        $query = "INSERT INTO users (id, email, full_name) VALUES (1, 'user@gmail.com', 'Utilisateur Test')";
        // Need to access connection directly or use raw query method if available. 
        // User model doesn't expose connection publically but we can extend or just hack it via a new Database instance here or reflection.
        // Easiest: use Database class directly for this one-off.
        $db = new Database();
        $conn = $db->getConnection();
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $userId = 1;
    } else {
        $userId = $targetId;
        echo "✅ Utilisateur ID: $userId (Existe déjà)\n";
    }

    // 2. Préparer les données du questionnaire
    $titre = "Questionnaire de Démonstration";
    $description = "Ceci est un questionnaire généré automatiquement pour tester l'application.";
    $access_pin = "TEST01"; // PIN simple
    $qr_code_token = bin2hex(random_bytes(16));

    // 3. Préparer les questions
    $questions = [
        [
            'type' => 'Réponse courte',
            'title' => 'Quel est votre prénom ?',
            'required' => true,
            'options' => []
        ],
        [
            'type' => 'Cases à cocher',
            'title' => 'Quelles fonctionnalités préférez-vous ?',
            'required' => false,
            'options' => [
                ['label' => 'Interface intuitive'],
                ['label' => 'Rapidité'],
                ['label' => 'Analyses détaillées']
            ]
        ],
        [
            'type' => 'Choix multiples', // Radio
            'title' => 'Quelle note donneriez-vous à ce test ?',
            'required' => true,
            'options' => [
                ['label' => '5/5'],
                ['label' => '4/5'],
                ['label' => '3/5'],
                ['label' => 'Moins que ça']
            ]
        ],
        [
            'type' => 'Paragraphe',
            'title' => 'Laissez un commentaire libre :',
            'required' => false,
            'options' => []
        ]
    ];

    // 4. Utiliser le modèle pour sauvegarder (gère transactions et tables liées)
    $surveyModel = new questionnaire();
    
    // Clean up previous runs (force delete by PIN to avoid constraint errors)
    echo "🧹 Nettoyage de l'ancien questionnaire TEST01...\n";
    $db = new Database();
    $conn = $db->getConnection();
    $stmtDel = $conn->prepare("DELETE FROM surveys WHERE access_pin = :pin");
    $stmtDel->execute([':pin' => $access_pin]);

    // Create new
    $surveyId = $surveyModel->saveSurvey($userId, $titre, $description, $access_pin, $qr_code_token, $questions);
    
    // Force status to 'active' for this demo survey
    $stmtUpdate = $conn->prepare("UPDATE surveys SET status = 'active' WHERE id = :id");
    $stmtUpdate->execute([':id' => $surveyId]);

    echo "✅ Questionnaire créé avec succès ! ID: $surveyId\n";
    echo "➡  Titre : $titre\n";
    echo "➡  PIN   : $access_pin\n";
    echo "➡  Status: Active\n";

    echo "--- Seed terminé ---\n";

} catch (Exception $e) {
    echo "❌ Erreur : " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
