<?php

namespace src\models;
use PDO;
use PDOException;

class Model{

    protected $pdo;

    public function __construct()
    {
        $dbFile = __DIR__ . '/../database/database.db';

        try {
            $this->pdo = new PDO("sqlite:$dbFile");
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    public function getUsers() {
        $stmt = $this->pdo->query('SELECT * FROM users');
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $users;
    }

    public function getAllMessages() {
        $stmt = $this->pdo->query('SELECT * FROM Messages');
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $messages;
    }

    public function getGroups() {
        $stmt = $this->pdo->query('SELECT * FROM Groups');
        $groups = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $groups;
    }

    public function getUsersGroups() {
        $stmt = $this->pdo->query('SELECT * FROM user_groups');
        $ugt = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $ugt;
    }

}