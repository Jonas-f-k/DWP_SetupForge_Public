<?php
class User
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function create($username, $email, $password)
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = 'INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)';
        $query = $this->db->prepare($sql);
        $query->execute([$username, $email, $hash]);
        return $this->db->lastInsertId();
    }

    public function login($email, $password)
    {
        $sql = 'SELECT * FROM users WHERE email = ?';
        $query = $this->db->prepare($sql);
        $query->execute([$email]);
        $user = $query->fetch();

        if (!$user) {
            return false;
        }
        if ($user['is_banned']) {
            return false;
        }
        if (password_verify($password, $user['password_hash'])) {
            return $user;
        }
        return false;
    }

    public function find($id)
    {
        $sql = 'SELECT * FROM users WHERE user_id = ?';
        $query = $this->db->prepare($sql);
        $query->execute([$id]);
        $result = $query->fetch();
        return $result;
    }

    public function all()
    {
        $sql = 'SELECT * FROM users ORDER BY user_id DESC';
        $query = $this->db->query($sql);
        $result = $query->fetchAll();
        return $result;
    }

    public function update($id, $username, $email, $bio, $password)
    {
        if ($password === '') {
            $sql = 'UPDATE users SET username = ?, email = ?, bio = ? WHERE user_id = ?';
            $query = $this->db->prepare($sql);
            $query->execute([$username, $email, $bio, $id]);
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $sql = 'UPDATE users SET username = ?, email = ?, bio = ?, password_hash = ? WHERE user_id = ?';
            $query = $this->db->prepare($sql);
            $query->execute([$username, $email, $bio, $hash, $id]);
        }
    }

    public function access($id, $role, $banned)
    {
        $sql = 'UPDATE users SET role = ?, is_banned = ? WHERE user_id = ?';
        $query = $this->db->prepare($sql);
        $query->execute([$role, $banned, $id]);
    }

    public function delete($id)
    {
        $this->db->beginTransaction();
        try {
            $sql = 'DELETE FROM comments WHERE user_id = ?';
            $query = $this->db->prepare($sql);
            $query->execute([$id]);
            $sql = 'DELETE FROM posts WHERE user_id = ?';
            $query = $this->db->prepare($sql);
            $query->execute([$id]);
            $sql = 'DELETE FROM users WHERE user_id = ?';
            $query = $this->db->prepare($sql);
            $query->execute([$id]);
            $this->db->commit();
        } catch (PDOException $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
