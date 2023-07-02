<?php

namespace src\models;
use src\models\Model;
use PDO;

class Group extends Model
{

    public function getMessages($group_id) {
        $stmt = $this->pdo->prepare('SELECT * FROM messages WHERE group_id=?');
        $stmt->execute([$group_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function newGroup($name, $description) {
        $stmt = $this->pdo->prepare('INSERT INTO groups (name, description) VALUES (?, ?)');
        $stmt->execute([$name, $description]);
    }

    public function addUserToGroup($user_id, $group_id){
        $stmt = $this->pdo->prepare('INSERT INTO user_groups (user_id, group_id) VALUES (?, ?)');
        $stmt->execute([$user_id, $group_id]);
    }

    public function getLastGroupId() {
        $stmt = $this->pdo->prepare('SELECT * FROM groups ORDER BY timestamp DESC LIMIT 1');
        $stmt->execute();
        $group = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $group[0]['id'];
    }

    public function getAttendants($group_id) {
        $stmt = $this->pdo->prepare('SELECT * FROM user_groups WHERE group_id=?');
        $stmt->execute([$group_id]);
        $attendants = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $attendants;
    }

    public function getGroupById($group_id) {
        $stmt = $this->pdo->prepare('SELECT * FROM groups WHERE id=?');
        $stmt->execute([$group_id]);
        $groups = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $groups;
    }

}
