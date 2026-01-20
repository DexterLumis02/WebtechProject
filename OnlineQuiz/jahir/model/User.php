<?php
declare(strict_types=1);

class User
{
    public int $id;
    public int $role_id;
    public string $name;
    public string $email;
    public string $password_hash;
    public bool $is_active;
    public ?string $remember_token = null;
    public ?string $role_name = null;

    public static function create(string $name, string $email, string $password, string $roleName = 'user'): ?int
    {
        $db = Database::getInstance()->getConnection();

        $role = Role::getByName($roleName);
        if (!$role) {
            return null;
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $db->prepare('INSERT INTO users (role_id, name, email, password_hash, is_active, created_at) VALUES (?, ?, ?, ?, 1, NOW())');
        $roleId = $role->id;
        $stmt->bind_param('isss', $roleId, $name, $email, $passwordHash);
        $ok = $stmt->execute();
        if (!$ok) {
            $stmt->close();
            return null;
        }
        $id = $stmt->insert_id;
        $stmt->close();
        return (int)$id;
    }

    public static function findByEmail(string $email): ?self
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        if ($row) {
            return self::fromRow($row);
        }
        return null;
    }

    public static function findById(int $id): ?self
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        if ($row) {
            return self::fromRow($row);
        }
        return null;
    }

    public static function authenticate(string $email, string $password): ?array
    {
        $user = self::findByEmail($email);
        if (!$user || !$user->is_active) {
            return null;
        }
        if (!password_verify($password, $user->password_hash)) {
            return null;
        }

        $role = self::getRoleName($user->role_id);

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role_id' => $user->role_id,
            'role' => $role,
        ];
    }

    public static function getAll(): array
    {
        $db = Database::getInstance()->getConnection();
        $sql = 'SELECT u.*, r.name AS role_name FROM users u JOIN roles r ON r.id = u.role_id ORDER BY u.created_at DESC';
        $result = $db->query($sql);
        $users = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $user = self::fromRow($row);
                $users[] = $user;
            }
        }
        return $users;
    }

    public static function updateBasic(int $id, string $name, string $email, ?string $password = null, ?int $roleId = null, ?bool $isActive = null): bool
    {
        $db = Database::getInstance()->getConnection();

        $fields = ['name = ?', 'email = ?'];
        $types = 'ss';
        $values = [$name, $email];

        if ($password !== null && $password !== '') {
            $fields[] = 'password_hash = ?';
            $types .= 's';
            $values[] = password_hash($password, PASSWORD_DEFAULT);
        }

        if ($roleId !== null) {
            $fields[] = 'role_id = ?';
            $types .= 'i';
            $values[] = $roleId;
        }

        if ($isActive !== null) {
            $fields[] = 'is_active = ?';
            $types .= 'i';
            $values[] = $isActive ? 1 : 0;
        }

        $types .= 'i';
        $values[] = $id;

        $sql = 'UPDATE users SET ' . implode(', ', $fields) . ' WHERE id = ?';
        $stmt = $db->prepare($sql);
        $stmt->bind_param($types, ...$values);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public static function setActive(int $id, bool $active): bool
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare('UPDATE users SET is_active = ? WHERE id = ?');
        $flag = $active ? 1 : 0;
        $stmt->bind_param('ii', $flag, $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public static function setRememberToken(int $id, ?string $token): bool
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare('UPDATE users SET remember_token = ? WHERE id = ?');
        $stmt->bind_param('si', $token, $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public static function findByRememberToken(string $token): ?self
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare('SELECT * FROM users WHERE remember_token = ? LIMIT 1');
        $stmt->bind_param('s', $token);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        if ($row) {
            return self::fromRow($row);
        }
        return null;
    }

    private static function fromRow(array $row): self
    {
        $user = new self();
        $user->id = (int)$row['id'];
        $user->role_id = (int)$row['role_id'];
        $user->name = $row['name'];
        $user->email = $row['email'];
        $user->password_hash = $row['password_hash'];
        $user->is_active = (bool)$row['is_active'];
        $user->remember_token = $row['remember_token'] ?? null;
        $user->role_name = $row['role_name'] ?? null;
        return $user;
    }

    private static function getRoleName(int $roleId): string
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare('SELECT name FROM roles WHERE id = ? LIMIT 1');
        $stmt->bind_param('i', $roleId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        if ($row && isset($row['name'])) {
            return $row['name'];
        }
        return 'user';
    }
}
