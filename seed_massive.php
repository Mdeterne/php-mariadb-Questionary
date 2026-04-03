<?php
require_once 'src/Modeles/Database.php';

$db = new Database();
$conn = $db->getConnection();
if (!$conn) die("DB Connection failed");

$userId = 2; // Utilisateur Test (session active)

// Dictionnaires de mots pour générer des titres
$sujets = ['Développement Web', 'Base de données', 'Gestion de projet', 'Réseaux', 'Culture numérique', 'Mathématiques', 'Communication', 'Stage de fin d\'année', 'SAÉ 1.01', 'SAÉ 2.04', 'SAÉ 3.01', 'Vie Étudiante', 'Environnement de travail', 'BDE'];
$types = ['Évaluation :', 'Bilan de', 'Retour sur', 'Avis concernant', 'Sondage :', 'Enquête :'];
$years = ['2021', '2022', '2023', '2024', '2025', '2026'];
$buts = ['BUT1', 'BUT2', 'BUT3'];

$questionTypes = ['short_text', 'long_text', 'scale', 'single_choice', 'multiple_choice'];

$dummyTexts = [
    "Plutôt bien organisé.", 
    "Très intéressant, merci !", 
    "C'était difficile mais très enrichissant au final.", 
    "RAS, aucun problème majeur.", 
    "Parfait !", 
    "Je n'ai pas bien compris la dernière partie du module.", 
    "À améliorer sur certains points d'organisation.", 
    "La charge de travail était assez importante par rapport au temps imparti.",
    "J'aurais aimé plus de travaux pratiques.",
    "Bons supports de cours."
];

function generateRandomPin($conn) {
    do {
        $pin = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $stmt = $conn->prepare("SELECT id FROM surveys WHERE access_pin = :pin");
        $stmt->execute([':pin' => $pin]);
    } while ($stmt->fetch());
    return $pin;
}

echo "Début de la génération massive (50 questionnaires)...\n";
$conn->beginTransaction();

try {
    for ($i = 0; $i < 50; $i++) {
        $title = $types[array_rand($types)] . ' ' . $sujets[array_rand($sujets)];
        $y = $years[array_rand($years)];
        $b = $buts[array_rand($buts)];
        $pin = generateRandomPin($conn);

        // Date de création aléatoire selon l'année choisie
        $randomMonth = str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT);
        $randomDay = str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT);
        $createdAt = "$y-$randomMonth-$randomDay 10:00:00";

        // Insertion du survey
        $stmt = $conn->prepare("INSERT INTO surveys (user_id, access_pin, title, description, status, created_at) VALUES (:u, :p, :t, :d, 'closed', :c)");
        $stmt->execute([
            ':u' => $userId,
            ':p' => $pin,
            ':t' => $title,
            ':d' => "Questionnaire fictif généré automatiquement pour les tests d'interface et l'exportation des données de l'année $y.",
            ':c' => $createdAt
        ]);
        
        $surveyId = $conn->lastInsertId();

        // Insertion des tags
        $stmtTag = $conn->prepare("INSERT IGNORE INTO survey_tags (survey_id, tag) VALUES (:s, :t)");
        $stmtTag->execute([':s' => $surveyId, ':t' => $y]);
        $stmtTag->execute([':s' => $surveyId, ':t' => $b]);

        // Génération des questions (3 à 6 par sondage)
        $numQuestions = rand(3, 6);
        $questions = [];
        for ($q = 1; $q <= $numQuestions; $q++) {
            $qType = $questionTypes[array_rand($questionTypes)];
            $label = '';
            
            switch($qType) {
                case 'scale': $label = "Évaluez votre niveau de satisfaction générale (1 = Très faible, 5 = Très élevé)."; break;
                case 'short_text': $label = "En un mot, comment décririez-vous cette expérience ?"; break;
                case 'long_text': $label = "Avez-vous des commentaires supplémentaires ou suggestions ?"; break;
                case 'single_choice': $label = "Quelle a été votre principale difficulté ?"; break;
                case 'multiple_choice': $label = "Quels sont les points positifs à retenir ? (plusieurs choix possibles)"; break;
            }

            $stmtQ = $conn->prepare("INSERT INTO questions (survey_id, type, label, order_index, is_required) VALUES (:s, :ty, :l, :o, 1)");
            $stmtQ->execute([':s' => $surveyId, ':ty' => $qType, ':l' => $label, ':o' => $q]);
            $qId = $conn->lastInsertId();

            $options = [];
            // Si la question requiert des choix, on les ajoute dans question_options
            if ($qType === 'single_choice' || $qType === 'multiple_choice') {
                $optLabels = ['Le temps imparti', 'La complexité', 'Le manque d\'outils', 'L\'organisation', 'Autre'];
                shuffle($optLabels);
                $numOpt = rand(2, 4);
                for ($o = 0; $o < $numOpt; $o++) {
                    $stmtOpt = $conn->prepare("INSERT INTO question_options (question_id, label, order_index) VALUES (:q, :l, :oi)");
                    $stmtOpt->execute([':q' => $qId, ':l' => $optLabels[$o], ':oi' => $o]);
                    $options[] = $conn->lastInsertId();
                }
            }
            $questions[] = ['id' => $qId, 'type' => $qType, 'options' => $options];
        }

        // Ajout des réponses par les étudiants (entre 8 et 25 réponses par sondage)
        $numResp = rand(8, 25);
        for ($r = 0; $r < $numResp; $r++) {
            $stmtR = $conn->prepare("INSERT INTO responses (survey_id, user_id, started_at, submitted_at) VALUES (:s, NULL, :dat, :dat)");
            $stmtR->execute([':s' => $surveyId, ':dat' => $createdAt]); // On simplifie pour l'exemple
            $respId = $conn->lastInsertId();

            foreach ($questions as $qItem) {
                $val = null;
                if ($qItem['type'] === 'short_text' || $qItem['type'] === 'long_text') {
                    $val = $dummyTexts[array_rand($dummyTexts)];
                } elseif ($qItem['type'] === 'scale') {
                    $val = rand(2, 5); // Ex: Les notes sont globalement positives (2 à 5)
                }

                $stmtA = $conn->prepare("INSERT INTO answers (response_id, question_id, text_value) VALUES (:r, :q, :v)");
                $stmtA->execute([':r' => $respId, ':q' => $qItem['id'], ':v' => $val]);
                $ansId = $conn->lastInsertId();

                // Lier les choix multiples/uniques dans answer_choices
                if ($qItem['type'] === 'single_choice') {
                    $opt = $qItem['options'][array_rand($qItem['options'])];
                    $conn->prepare("INSERT INTO answer_choices (answer_id, option_id) VALUES (:a, :o)")->execute([':a' => $ansId, ':o' => $opt]);
                } elseif ($qItem['type'] === 'multiple_choice') {
                    // Sélectionner 1 à 2 options au hasard
                    $numSelected = rand(1, min(2, count($qItem['options'])));
                    $selectedOpts = (array) array_rand(array_flip($qItem['options']), $numSelected);
                    foreach ($selectedOpts as $opt) {
                        $conn->prepare("INSERT INTO answer_choices (answer_id, option_id) VALUES (:a, :o)")->execute([':a' => $ansId, ':o' => $opt]);
                    }
                }
            }
        }
    }
    
    $conn->commit();
    echo "Succès : 50 questionnaires pleins générés et peuplés de dizaines de réponses ! 🎉\n";
} catch (Exception $e) {
    $conn->rollBack();
    echo "Erreur lors de la génération : " . $e->getMessage() . "\n";
}
