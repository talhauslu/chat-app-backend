<?php

namespace src\models;
use src\models\Model;
use PDO;
use PDOException;

class User extends Model
{

    public function getIdByToken($token) {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE token=?');
        $stmt->execute([$token]);
        $user = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $user[0]['id'];
    }

    public function getUserById($user_id) {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE id=?');
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createUser($username, $token) {
        $stmt = $this->pdo->prepare('INSERT INTO users (username, token) VALUES (?, ?)');
        $stmt->execute([$username, $token]);
    }

    public function checkIfAttendant($user_id, $group_id) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM User_Groups WHERE user_id = ? AND group_id = ?");
            $stmt->execute([$user_id, $group_id]);
            if ($stmt->fetch(PDO::FETCH_ASSOC)) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function getAttendedGroups($user_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM User_Groups WHERE user_id = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}
