<?php
declare(strict_types=1);

class Role
{
    public int $id;
    public string $name;

    public static function getAll(): array
    {
        $db = Database::getInstance()->getConnection();
        $sql = 'SELECT id, name FROM roles ORDER BY id ASC';
        $result = $db->query($sql);
        $roles = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $role = new self();
                $role->id = (int)$row['id'];
                $role->name = $row['name'];
                $roles[] = $role;
            }
        }
        return $roles;
    }

    public static function getByName(string $name): ?self
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare('SELECT id, name FROM roles WHERE name = ? LIMIT 1');
        $stmt->bind_param('s', $name);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        if ($row) {
            $role = new self();
            $role->id = (int)$row['id'];
            $role->name = $row['name'];
            return $role;
        }
        return null;
    }
}

