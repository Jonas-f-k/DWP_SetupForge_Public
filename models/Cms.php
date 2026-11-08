<?php
class Cms
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function settings()
    {
        $sql = 'SELECT setting_key, setting_value FROM site_settings';
        $query = $this->db->prepare($sql);
        $query->execute();
        $settings = [];
        foreach ($query->fetchAll() as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        return $settings;
    }

    public function setting($key, $value, $adminId)
    {
        $sql = 'SELECT setting_key FROM site_settings WHERE setting_key = ?';
        $query = $this->db->prepare($sql);
        $query->execute([$key]);
        $existing = $query->fetch();

        if ($existing) {
            $sql = 'UPDATE site_settings SET setting_value = ?, updated_by = ? WHERE setting_key = ?';
            $query = $this->db->prepare($sql);
            $query->execute([$value, $adminId, $key]);
        } else {
            $sql = 'INSERT INTO site_settings (setting_key, setting_value, updated_by) VALUES (?, ?, ?)';
            $query = $this->db->prepare($sql);
            $query->execute([$key, $value, $adminId]);
        }
    }

    public function pages($published = false)
    {
        $sql = 'SELECT * FROM content_pages';
        if ($published) {
            $sql .= ' WHERE is_published = 1';
        }
        $sql .= ' ORDER BY page_id';
        $query = $this->db->prepare($sql);
        $query->execute();
        $result = $query->fetchAll();
        return $result;
    }

    public function page($slug)
    {
        $sql = 'SELECT * FROM content_pages WHERE slug = ?';
        $query = $this->db->prepare($sql);
        $query->execute([$slug]);
        $result = $query->fetch();
        return $result;
    }

    public function savePage($id, $slug, $title, $body, $published, $adminId)
    {
        if ($id) {
            $sql = 'UPDATE content_pages SET slug = ?, title = ?, body = ?, is_published = ?, updated_by = ? WHERE page_id = ?';
            $query = $this->db->prepare($sql);
            $query->execute([$slug, $title, $body, $published, $adminId, $id]);
        } else {
            $sql = 'INSERT INTO content_pages (slug, title, body, is_published, updated_by) VALUES (?, ?, ?, ?, ?)';
            $query = $this->db->prepare($sql);
            $query->execute([$slug, $title, $body, $published, $adminId]);
        }
    }

    public function deletePage($id)
    {
        $sql = 'DELETE FROM content_pages WHERE page_id = ?';
        $query = $this->db->prepare($sql);
        $query->execute([$id]);
    }
}
