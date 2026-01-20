<?php
declare(strict_types=1);

class Result
{
    public int $id;
    public int $attempt_id;
    public int $user_id;
    public int $exam_id;
    public int $score;
    public int $total_marks;
    public int $correct_answers;
    public int $wrong_answers;
    public string $created_at;

    public static function create(int $attemptId, int $userId, int $examId, int $score, int $totalMarks, int $correctAnswers, int $wrongAnswers): ?int
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare('INSERT INTO results (attempt_id, user_id, exam_id, score, total_marks, correct_answers, wrong_answers, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())');
        $stmt->bind_param('iiiiiii', $attemptId, $userId, $examId, $score, $totalMarks, $correctAnswers, $wrongAnswers);
        $ok = $stmt->execute();
        if (!$ok) {
            $stmt->close();
            return null;
        }
        $id = $stmt->insert_id;
        $stmt->close();
        return (int)$id;
    }

    public static function findByAttempt(int $attemptId): ?self
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare('SELECT * FROM results WHERE attempt_id = ? LIMIT 1');
        $stmt->bind_param('i', $attemptId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        if ($row) {
            return self::fromRow($row);
        }
        return null;
    }

    public static function findByUser(int $userId): array
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare('SELECT r.*, e.title AS exam_title FROM results r JOIN exams e ON e.id = r.exam_id WHERE r.user_id = ? ORDER BY r.created_at DESC');
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $items = [];
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
        $stmt->close();
        return $items;
    }

    public static function findAllWithFilters(?int $examId = null, ?int $userId = null): array
    {
        $db = Database::getInstance()->getConnection();
        $conditions = [];
        $types = '';
        $values = [];

        if ($examId !== null) {
            $conditions[] = 'r.exam_id = ?';
            $types .= 'i';
            $values[] = $examId;
        }
        if ($userId !== null) {
            $conditions[] = 'r.user_id = ?';
            $types .= 'i';
            $values[] = $userId;
        }

        $sql = 'SELECT r.*, u.name AS user_name, e.title AS exam_title 
                FROM results r 
                JOIN users u ON u.id = r.user_id 
                JOIN exams e ON e.id = r.exam_id';
        if ($conditions) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }
        $sql .= ' ORDER BY r.created_at DESC';

        if ($conditions) {
            $stmt = $db->prepare($sql);
            $stmt->bind_param($types, ...$values);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $result = $db->query($sql);
        }

        $items = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $items[] = $row;
            }
        }

        if (isset($stmt)) {
            $stmt->close();
        }

        return $items;
    }

    private static function fromRow(array $row): self
    {
        $r = new self();
        $r->id = (int)$row['id'];
        $r->attempt_id = (int)$row['attempt_id'];
        $r->user_id = (int)$row['user_id'];
        $r->exam_id = (int)$row['exam_id'];
        $r->score = (int)$row['score'];
        $r->total_marks = (int)$row['total_marks'];
        $r->correct_answers = (int)$row['correct_answers'];
        $r->wrong_answers = (int)$row['wrong_answers'];
        $r->created_at = $row['created_at'];
        return $r;
    }
}

