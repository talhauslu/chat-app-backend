<?php

namespace src\models;
use src\models\Model;
use PDO;

class Message extends Model
{

    public function getMessageById($message_id) {
        $stmt = $this->pdo->prepare('SELECT * FROM messages WHERE token=?');
        $stmt->execute([$message_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function sendMessage($to, $sender_id, $content) {
        $statement = $this->pdo->prepare('INSERT INTO messages (group_id, sender_id, content) VALUES (?, ?, ?)');
        $statement->execute([$to, $sender_id, $content]);
    }

    public function markAsSeen($user_id, $message_id) {
        $stmt = $this->pdo->prepare('SELECT * FROM messages WHERE id=?');
        $stmt->execute([$message_id]);
        $message = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];
        if ($message['seen_by'] == null || $message['seen_by'] == '') {
            $stmt = $this->pdo->prepare('UPDATE messages SET seen_by=? WHERE id=?');
            $stmt->execute([$user_id, $message_id]);
        } else {
            $new_seen_by = $message['seen_by'] . ','. strval($user_id);
            $stmt = $this->pdo->prepare('UPDATE messages SET seen_by=? WHERE id=?');
            $stmt->execute([$new_seen_by, $message_id]);
        }
    }

    public function getUnseenMessages($user_id, $group_id) {
        $stmt = $this->pdo->prepare('SELECT * FROM messages WHERE group_id = ? AND NOT sender_id = ?');
        $stmt->execute([$group_id, $user_id]);
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $unseen_message = [];
        foreach($messages as $message) {
            $seen_by = explode(',', $message['seen_by']);
            if (!in_array($user_id, $seen_by)) {
                array_push($unseen_message, $message);
            }
        }
        return $unseen_message;
    }

}
