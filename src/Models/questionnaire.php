<?php

require_once __DIR__ . DIRECTORY_SEPARATOR . 'Database.php';

class questionnaire
{
    private $conn;

    function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    private function generateUuidV4()
    {
        $data = random_bytes(16);
        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    function createNewSurvey($userId, $title)
    {
        if ($this->conn === null) return false;
        $accessPin = substr(strtoupper(bin2hex(random_bytes(3))), 0, 6);
        $surveyId = $this->generateUuidV4();
        $req = $this->conn->prepare("INSERT INTO surveys (id, user_id, title, access_pin, status) VALUES (:id, :user_id, :title, :access_pin, 'draft')");
        $req->execute([':id' => $surveyId, ':user_id' => (int)$userId, ':title' => $title, ':access_pin' => $accessPin]);
        return $req->rowCount() ? $surveyId : false;
    }

    public function saveSurvey($data)
    {
        if ($this->conn === null) return false;

        $id = isset($data['id']) && $data['id'] ? $data['id'] : null;
        $userId = isset($data['user_id']) ? (int)$data['user_id'] : null;
        $title = isset($data['title']) ? $data['title'] : '';
        $description = isset($data['description']) ? $data['description'] : null;
        $accessPin = isset($data['access_pin']) ? $data['access_pin'] : null;
        $qrToken = isset($data['qr_code_token']) ? $data['qr_code_token'] : null;

        if ($id) {
            $req = $this->conn->prepare(
                "UPDATE surveys SET user_id = :user_id, title = :title, description = :description, access_pin = :access_pin, qr_code_token = :qr_code_token WHERE id = :id"
            );
            $ok = $req->execute([
                ':user_id' => $userId,
                ':title' => $title,
                ':description' => $description,
                ':access_pin' => $accessPin,
                ':qr_code_token' => $qrToken,
                ':id' => $id
            ]);
            return $ok ? $id : false;
        } else {
            $newId = $this->generateUuidV4();
            $req = $this->conn->prepare(
                "INSERT INTO surveys (id, user_id, title, description, access_pin, qr_code_token) VALUES (:id, :user_id, :title, :description, :access_pin, :qr_code_token)"
            );
            $ok = $req->execute([
                ':id' => $newId,
                ':user_id' => $userId,
                ':title' => $title,
                ':description' => $description,
                ':access_pin' => $accessPin,
                ':qr_code_token' => $qrToken
            ]);
            return $ok ? $newId : false;
        }
    }
}