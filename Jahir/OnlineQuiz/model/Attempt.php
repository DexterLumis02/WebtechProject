<?php
declare(strict_types=1);

class Attempt
{
    public int $id;
    public int $user_id;
    public int $exam_id;
    public string $status;
    public string $started_at;
    public ?string $completed_at;

    public static function create(int $userId, int $examId): ?int
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare('INSERT INTO attempts (user_id, exam_id, status, started_at) VALUES (?, ?, "in_progress", NOW())');
        $stmt->bind_param('ii', $userId, $examId);
        $ok = $stmt->execute();
        if (!$ok) {
            $stmt->close();
            return null;
        }
        $id = $stmt->insert_id;
        $stmt->close();
        return (int)$id;
    }

    public static function findActiveForUserExam(int $userId, int $examId): ?self
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare('SELECT * FROM attempts WHERE user_id = ? AND exam_id = ? AND status = "in_progress" LIMIT 1');
        $stmt->bind_param('ii', $userId, $examId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        if ($row) {
            return self::fromRow($row);
        }
        return null;
    }

    public static function findCompletedForUserExam(int $userId, int $examId): ?self
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare('SELECT * FROM attempts WHERE user_id = ? AND exam_id = ? AND status = "completed" LIMIT 1');
        $stmt->bind_param('ii', $userId, $examId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        if ($row) {
            return self::fromRow($row);
        }
        return null;
    }

    public static function complete(int $id): bool
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare('UPDATE attempts SET status = "completed", completed_at = NOW() WHERE id = ?');
        $stmt->bind_param('i', $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public static function findById(int $id): ?self
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare('SELECT * FROM attempts WHERE id = ? LIMIT 1');
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

    private static function fromRow(array $row): self
    {
        $a = new self();
        $a->id = (int)$row['id'];
        $a->user_id = (int)$row['user_id'];
        $a->exam_id = (int)$row['exam_id'];
        $a->status = $row['status'];
        $a->started_at = $row['started_at'];
        $a->completed_at = $row['completed_at'] ?? null;
        return $a;
    }
}

