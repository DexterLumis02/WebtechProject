<?php
declare(strict_types=1);

class Option
{
    public int $id;
    public int $question_id;
    public string $option_text;
    public bool $is_correct;

    public static function create(int $questionId, string $text, bool $isCorrect): ?int
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare('INSERT INTO options (question_id, option_text, is_correct) VALUES (?, ?, ?)');
        $flag = $isCorrect ? 1 : 0;
        $stmt->bind_param('isi', $questionId, $text, $flag);
        $ok = $stmt->execute();
        if (!$ok) {
            $stmt->close();
            return null;
        }
        $id = $stmt->insert_id;
        $stmt->close();
        return (int)$id;
    }

    public static function findByQuestion(int $questionId): array
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare('SELECT * FROM options WHERE question_id = ? ORDER BY id ASC');
        $stmt->bind_param('i', $questionId);
        $stmt->execute();
        $result = $stmt->get_result();
        $items = [];
        while ($row = $result->fetch_assoc()) {
            $items[] = self::fromRow($row);
        }
        $stmt->close();
        return $items;
    }

    public static function deleteByQuestion(int $questionId): bool
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare('DELETE FROM options WHERE question_id = ?');
        $stmt->bind_param('i', $questionId);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public static function getCorrectOptionId(int $questionId): ?int
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare('SELECT id FROM options WHERE question_id = ? AND is_correct = 1 LIMIT 1');
        $stmt->bind_param('i', $questionId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        if ($row) {
            return (int)$row['id'];
        }
        return null;
    }

    private static function fromRow(array $row): self
    {
        $o = new self();
        $o->id = (int)$row['id'];
        $o->question_id = (int)$row['question_id'];
        $o->option_text = $row['option_text'];
        $o->is_correct = (bool)$row['is_correct'];
        return $o;
    }
}

