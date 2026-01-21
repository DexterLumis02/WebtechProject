<?php
declare(strict_types=1);

class ApiController extends BaseController
{
    /**
     * Helper to send JSON response
     */
    private function jsonResponse(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * GET: Fetch details of a specific exam
     */
    public function getExamDetails(): void
    {
        // 1. Check Authentication (Optional but recommended)
        $this->ensureRememberedUser();
        if (empty($_SESSION['user'])) {
             $this->jsonResponse(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        // 2. Get ID from Query Params
        $examId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        // 3. Fetch Data
        $exam = Exam::findById($examId);

        // 4. Return JSON
        if ($exam) {
            $this->jsonResponse([
                'success' => true,
                'data' => [
                    'id' => $exam->id,
                    'title' => $exam->title,
                    'description' => $exam->description,
                    'duration' => $exam->duration_minutes,
                    'marks' => $exam->total_marks
                ]
            ]);
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'Exam not found'], 404);
        }
    }

    /**
     * POST: Create a new exam via AJAX
     */
    public function createExam(): void
    {
        // 1. Check Admin Permissions
        $this->ensureRememberedUser();
        if (($_SESSION['user']['role'] ?? '') !== 'admin') {
            $this->jsonResponse(['success' => false, 'message' => 'Forbidden'], 403);
        }

        // 2. Read JSON Input
        $input = json_decode(file_get_contents('php://input'), true);

        $title = $input['title'] ?? '';
        $description = $input['description'] ?? '';
        $duration = (int)($input['duration'] ?? 0);
        $totalMarks = (int)($input['total_marks'] ?? 0);

        // 3. Validate
        if (empty($title) || empty($description)) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid input'], 400);
        }

        // 4. Create in Database
        $id = Exam::create($title, $description, $duration, $totalMarks, true);

        // 5. Return Success
        if ($id) {
            $this->jsonResponse(['success' => true, 'message' => 'Exam created successfully', 'id' => $id]);
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'Database error'], 500);
        }
    }

    /**
     * DELETE: Delete an exam via AJAX
     */
    public function deleteExam(): void
    {
        // 1. Check Admin Permissions
        $this->ensureRememberedUser();
        if (($_SESSION['user']['role'] ?? '') !== 'admin') {
            $this->jsonResponse(['success' => false, 'message' => 'Forbidden'], 403);
        }

        // 2. Read JSON Input
        $input = json_decode(file_get_contents('php://input'), true);
        $examId = (int)($input['id'] ?? 0);

        if ($examId <= 0) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid ID'], 400);
        }

        // 3. Perform Delete
        // Note: You might want to check if exam exists first
        Question::deleteByExam($examId);
        $deleted = Exam::delete($examId);

        // 4. Return Result
        if ($deleted) {
            $this->jsonResponse(['success' => true, 'message' => 'Exam deleted']);
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'Failed to delete'], 500);
        }
    }
}
