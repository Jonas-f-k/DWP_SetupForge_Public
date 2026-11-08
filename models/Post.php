<?php
class Post
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function all()
    {
        $sql = "SELECT * FROM post_overview WHERE visibility = 'visible' AND is_banned = 0 ORDER BY post_id DESC";
        $query = $this->db->query($sql);
        $result = $query->fetchAll();
        return $result;
    }

    public function hot()
    {
        $sql = "SELECT * FROM post_overview WHERE visibility = 'visible' AND is_banned = 0 AND is_hot = 1 ORDER BY post_id DESC LIMIT 6";
        $query = $this->db->query($sql);
        $result = $query->fetchAll();
        return $result;
    }

    public function sticky()
    {
        $sql = "SELECT * FROM post_overview WHERE visibility = 'visible' AND is_banned = 0 AND is_sticky = 1 ORDER BY post_id DESC LIMIT 6";
        $query = $this->db->query($sql);
        $result = $query->fetchAll();
        return $result;
    }

    public function forUser($id)
    {
        $sql = "SELECT * FROM post_overview WHERE user_id = ? AND visibility = 'visible' AND is_banned = 0 ORDER BY post_id DESC";
        $query = $this->db->prepare($sql);
        $query->execute([$id]);
        $result = $query->fetchAll();
        return $result;
    }

    public function adminList()
    {
        $sql = 'SELECT * FROM post_overview ORDER BY post_id DESC';
        $query = $this->db->query($sql);
        $result = $query->fetchAll();
        return $result;
    }

    public function find($id)
    {
        $sql = 'SELECT * FROM post_overview WHERE post_id = ?';
        $query = $this->db->prepare($sql);
        $query->execute([$id]);
        $result = $query->fetch();
        return $result;
    }

    public function categories()
    {
        $sql = 'SELECT * FROM categories ORDER BY name';
        $query = $this->db->query($sql);
        $result = $query->fetchAll();
        return $result;
    }

    public function create($userId, $categoryId, $title, $description, $image)
    {
        $sql = 'INSERT INTO posts (user_id, category_id, title, description, image_path) VALUES (?, ?, ?, ?, ?)';
        $query = $this->db->prepare($sql);
        $query->execute([$userId, $categoryId, $title, $description, $image]);
        return $this->db->lastInsertId();
    }

    public function update($id, $categoryId, $title, $description, $image)
    {
        $sql = 'UPDATE posts SET category_id = ?, title = ?, description = ?, image_path = ? WHERE post_id = ?';
        $query = $this->db->prepare($sql);
        $query->execute([$categoryId, $title, $description, $image, $id]);
    }

    public function moderate($id, $visibility)
    {
        $sql = 'UPDATE posts SET visibility = ? WHERE post_id = ?';
        $query = $this->db->prepare($sql);
        $query->execute([$visibility, $id]);
    }

    public function delete($id)
    {
        $sql = 'DELETE FROM posts WHERE post_id = ?';
        $query = $this->db->prepare($sql);
        $query->execute([$id]);
    }

    public function liked($userId, $postId)
    {
        $sql = 'SELECT * FROM likes WHERE user_id = ? AND post_id = ?';
        $query = $this->db->prepare($sql);
        $query->execute([$userId, $postId]);
        $result = $query->fetch();
        return $result;
    }

    public function like($userId, $postId)
    {
        if (!$this->liked($userId, $postId)) {
            $sql = 'INSERT INTO likes (user_id, post_id) VALUES (?, ?)';
            $query = $this->db->prepare($sql);
            $query->execute([$userId, $postId]);
        }
    }

    public function unlike($userId, $postId)
    {
        $sql = 'DELETE FROM likes WHERE user_id = ? AND post_id = ?';
        $query = $this->db->prepare($sql);
        $query->execute([$userId, $postId]);
    }

    public function comments($id)
    {
        $sql = 'SELECT c.*, u.username FROM comments c JOIN users u ON c.user_id = u.user_id WHERE c.post_id = ? AND u.is_banned = 0 ORDER BY c.comment_id';
        $query = $this->db->prepare($sql);
        $query->execute([$id]);
        $result = $query->fetchAll();
        return $result;
    }

    public function latestComments()
    {
        $sql = 'SELECT * FROM latest_comments ORDER BY created_at DESC, comment_id DESC LIMIT 10';
        $query = $this->db->query($sql);
        $result = $query->fetchAll();
        return $result;
    }

    public function addComment($userId, $postId, $body)
    {
        $sql = 'INSERT INTO comments (user_id, post_id, body) VALUES (?, ?, ?)';
        $query = $this->db->prepare($sql);
        $query->execute([$userId, $postId, $body]);
    }

    public function findComment($id)
    {
        $sql = 'SELECT * FROM comments WHERE comment_id = ?';
        $query = $this->db->prepare($sql);
        $query->execute([$id]);
        $result = $query->fetch();
        return $result;
    }

    public function deleteComment($id)
    {
        $sql = 'DELETE FROM comments WHERE comment_id = ?';
        $query = $this->db->prepare($sql);
        $query->execute([$id]);
    }

    public function imagesByUser($id)
    {
        $sql = 'SELECT image_path FROM posts WHERE user_id = ?';
        $query = $this->db->prepare($sql);
        $query->execute([$id]);
        $result = $query->fetchAll();
        return $result;
    }

    public function history()
    {
        $sql = 'SELECT h.*, p.title FROM post_history h JOIN posts p ON h.post_id = p.post_id ORDER BY history_id DESC LIMIT 50';
        $query = $this->db->query($sql);
        $result = $query->fetchAll();
        return $result;
    }
}
