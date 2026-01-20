<?php
declare(strict_types=1);

class Question
{
    public int $id;
    public int $exam_id;
    public string $question_text;
    public int $marks;

    public static function create(int $examId, string $text, int $marks, array $options): ?int
    {
        $db = Database::getInstance()->getConnection();
        $db->begin_transaction();
        try {
            $stmt = $db->prepare('INSERT INTO questions (exam_id, question_text, marks) VALUES (?, ?, ?)');
            $stmt->bind_param('isi', $examId, $text, $marks);
            $ok = $stmt->execute();
            if (!$ok) {
                $stmt->close();
                $db->rollback();
                return null;
            }
            $questionId = (int)$stmt->insert_id;
            $stmt->close();

            foreach ($options as $option) {
                $optionText = $option['text'];
                $isCorrect = !empty($option['is_correct']);
                Option::create($questionId, $optionText, $isCorrect);
            }

            $db->commit();
            return $questionId;
        } catch (Throwable $e) {
            $db->rollback();
            return null;
        }
    }

    public static function findByExam(int $examId): array
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare('SELECT * FROM questions WHERE exam_id = ? ORDER BY id ASC');
        $stmt->bind_param('i', $examId);
        $stmt->execute();
        $result = $stmt->get_result();
        $items = [];
        while ($row = $result->fetch_assoc()) {
            $items[] = self::fromRow($row);
        }
        $stmt->close();
        return $items;
    }

    public static function deleteByExam(int $examId): bool
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare('SELECT id FROM questions WHERE exam_id = ?');
        $stmt->bind_param('i', $examId);
        $stmt->execute();
        $result = $stmt->get_result();
        $ids = [];
        while ($row = $result->fetch_assoc()) {
            $ids[] = (int)$row['id'];
        }
        $stmt->close();

        foreach ($ids as $id) {
            Option::deleteByQuestion($id);
        }

        if (!$ids) {
            return true;
        }

        $in = implode(',', array_fill(0, count($ids), '?'));
        $types = str_repeat('i', count($ids));
        $stmt = $db->prepare('DELETE FROM questions WHERE id IN (' . $in . ')');
        $stmt->bind_param($types, ...$ids);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    private static function fromRow(array $row): self
    {
        $q = new self();
        $q->id = (int)$row['id'];
        $q->exam_id = (int)$row['exam_id'];
        $q->question_text = $row['question_text'];
        $q->marks = (int)$row['marks'];
        return $q;
    }
}

