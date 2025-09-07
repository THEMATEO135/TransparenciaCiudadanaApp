<?php
namespace App\Models;


class UserModel extends BaseModel {
public function findById($id) {
$stmt = $this->pdo()->prepare('SELECT id, username, email FROM users WHERE id = :id');
$stmt->execute(['id' => $id]);
return $stmt->fetch();
}


public function all($limit = 100) {
$stmt = $this->pdo()->prepare('SELECT id, username, email FROM users ORDER BY id DESC LIMIT :limit');
$stmt->bindValue(':limit', (int)$limit, \PDO::PARAM_INT);
$stmt->execute();
return $stmt->fetchAll();
}
}