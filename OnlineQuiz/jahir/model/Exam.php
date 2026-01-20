<?php
declare(strict_types=1);

class Exam
{
    public int $id;
    public string $title;
    public string $description;
    public int $duration_minutes;
    public int $total_marks;
    public bool $is_active;

    public static function create(string $title, string $description, int $durationMinutes, int $totalMarks, bool $isActive = true): ?int
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare('INSERT INTO exams (title, description, duration_minutes, total_marks, is_active, created_at) VALUES (?, ?, ?, ?, ?, NOW())');
        $flag = $isActive ? 1 : 0;
        $stmt->bind_param('ssiii', $title, $description, $durationMinutes, $totalMarks, $flag);
        $ok = $stmt->execute();
        if (!$ok) {
            $stmt->close();
            return null;
        }
        $id = $stmt->insert_id;
        $stmt->close();
        return (int)$id;
    }

    public static function update(int $id, string $title, string $description, int $durationMinutes, int $totalMarks, bool $isActive): bool
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare('UPDATE exams SET title = ?, description = ?, duration_minutes = ?, total_marks = ?, is_active = ?, updated_at = NOW() WHERE id = ?');
        $flag = $isActive ? 1 : 0;
        $stmt->bind_param('ssiiii', $title, $description, $durationMinutes, $totalMarks, $flag, $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public static function delete(int $id): bool
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare('DELETE FROM exams WHERE id = ?');
        $stmt->bind_param('i', $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public static function findById(int $id): ?self
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare('SELECT * FROM exams WHERE id = ? LIMIT 1');
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

    public static function getAll(): array
    {
        $db = Database::getInstance()->getConnection();
        $sql = 'SELECT * FROM exams ORDER BY created_at DESC';
        $result = $db->query($sql);
        $items = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $items[] = self::fromRow($row);
            }
        }
        return $items;
    }

    public static function getActive(): array
    {
        $db = Database::getInstance()->getConnection();
        $sql = 'SELECT * FROM exams WHERE is_active = 1 ORDER BY created_at DESC';
        $result = $db->query($sql);
        $items = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $items[] = self::fromRow($row);
            }
        }
        return $items;
    }

    private static function fromRow(array $row): self
    {
        $exam = new self();
        $exam->id = (int)$row['id'];
        $exam->title = $row['title'];
        $exam->description = $row['description'];
        $exam->duration_minutes = (int)$row['duration_minutes'];
        $exam->total_marks = (int)$row['total_marks'];
        $exam->is_active = (bool)$row['is_active'];
        return $exam;
    }
}

