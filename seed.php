<?php
require_once 'src/Modeles/Database.php';

$db = new Database();
$conn = $db->getConnection();

if (!$conn) {
    die("Échec de la connexion à la base de données.");
}

// Nettoyage préalable (optionnel, mais utile pour éviter les doublons lors des tests)
// On supprimer les précédents tests si nécessaire par leur PIN
$pins = ['111111', '222222', '333333', '444444'];
$inQuery = implode(',', array_map(function($p) use ($conn) { return $conn->quote($p); }, $pins));
$conn->exec("DELETE FROM surveys WHERE access_pin IN ($inQuery)");

$userId = 2;

$surveysData = [
    [
        'pin' => '111111',
        'title' => 'Évaluation des Enseignements - R2.01',
        'desc' => 'Merci de répondre à ce questionnaire pour améliorer le cours de Développement Orienté Objet.',
        'status' => 'active',
        'tags' => ['BUT1', '2026']
    ],
    [
        'pin' => '222222',
        'title' => 'Retour sur le stage de 2ème année',
        'desc' => 'Faites-nous un retour sur votre expérience en entreprise.',
        'status' => 'active',
        'tags' => ['BUT2', '2026']
    ],
    [
        'pin' => '333333',
        'title' => 'Sondage : Qualité de la vie étudiante à l\'IUT',
        'desc' => 'Aidez-nous à améliorer les infrastructures (BDE, cafétéria, espaces de travail).',
        'status' => 'closed',
        'tags' => ['BUT1', 'BUT2', 'BUT3', '2025']
    ],
    [
        'pin' => '444444',
        'title' => 'Bilan des compétences SAÉ',
        'desc' => 'Auto-évaluation de vos compétences en fin de parcours.',
        'status' => 'active',
        'tags' => ['BUT3', '2026']
    ]
];

foreach ($surveysData as $data) {
    // Insérer le questionnaire
    $stmt = $conn->prepare("INSERT INTO surveys (user_id, access_pin, title, description, status, created_at) VALUES (:u, :pin, :t, :d, :s, NOW())");
    $stmt->execute([
        ':u' => $userId,
        ':pin' => $data['pin'],
        ':t' => $data['title'],
        ':d' => $data['desc'],
        ':s' => $data['status']
    ]);
    
    $surveyId = $conn->lastInsertId();

    // Insérer les tags
    $stmtTag = $conn->prepare("INSERT IGNORE INTO survey_tags (survey_id, tag) VALUES (:s, :t)");
    foreach ($data['tags'] as $tag) {
        $stmtTag->execute([':s' => $surveyId, ':t' => $tag]);
    }

    // Ajouter quelques questions factices pour faire "vrai"
    $stmtQ = $conn->prepare("INSERT INTO questions (survey_id, type, label, is_required) VALUES (:s, :ty, :txt, 1)");
    
    // Q1 : Question texte
    $stmtQ->execute([':s' => $surveyId, ':ty' => 'long_text', ':txt' => 'Que pensez-vous globalement de ce sujet ?']);
    
    // Q2 : Échelle
    if ($data['pin'] === '111111') {
        $stmtQ = $conn->prepare("INSERT INTO questions (survey_id, type, label, is_required) VALUES (:s, 'scale', 'Notez la clarté du cours sur 5', 1)");
        $stmtQ->execute([':s' => $surveyId]);
    }

    echo "Questionnaire '{$data['title']}' créé avec succès.\n";
}

echo "Génération des données de test terminée !";
