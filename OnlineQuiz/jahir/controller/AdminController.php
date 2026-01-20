<?php
declare(strict_types=1);

class AdminController extends BaseController
{
    public function dashboard(): void
    {
        $this->requireLogin('admin');

        $users = User::getAll();
        $exams = Exam::getAll();

        $db = Database::getInstance()->getConnection();
        $attemptCount = 0;
        $resultCount = 0;

        $res = $db->query('SELECT COUNT(*) AS c FROM attempts');
        if ($res) {
            $row = $res->fetch_assoc();
            $attemptCount = (int)$row['c'];
        }

        $res = $db->query('SELECT COUNT(*) AS c FROM results');
        if ($res) {
            $row = $res->fetch_assoc();
            $resultCount = (int)$row['c'];
        }

        $this->render('admin/dashboard', [
            'title' => 'Admin Dashboard',
            'usersCount' => count($users),
            'examsCount' => count($exams),
            'attemptsCount' => $attemptCount,
            'resultsCount' => $resultCount,
        ]);
    }

    public function users(): void
    {
        $this->requireLogin('admin');

        if (is_post()) {
            $token = $_POST['csrf_token'] ?? null;
            if ($this->verifyCsrfToken($token)) {
                $action = $_POST['action'] ?? '';
                $userId = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
                if ($userId > 0 && $action === 'toggle_status') {
                    $user = User::findById($userId);
                    if ($user) {
                        User::setActive($userId, !$user->is_active);
                        $this->setFlash('success', 'User status updated.');
                    }
                }
            } else {
                $this->setFlash('error', 'Invalid request.');
            }
            $this->redirect('admin/users');
        }

        $users = User::getAll();
        $csrfToken = $this->generateCsrfToken();

        $this->render('admin/users', [
            'title' => 'Manage Users',
            'users' => $users,
            'csrfToken' => $csrfToken,
        ]);
    }

    public function editUser(): void
    {
        $this->requireLogin('admin');

        $userId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($userId <= 0) {
            $this->setFlash('error', 'Invalid user.');
            $this->redirect('admin/users');
        }

        $user = User::findById($userId);
        if (!$user) {
            $this->setFlash('error', 'User not found.');
            $this->redirect('admin/users');
        }

        $errors = [];

        if (is_post()) {
            $token = $_POST['csrf_token'] ?? null;
            if (!$this->verifyCsrfToken($token)) {
                $errors[] = 'Invalid request.';
            } else {
                $name = sanitize($_POST['name'] ?? '');
                $email = sanitize($_POST['email'] ?? '');
                $password = $_POST['password'] ?? '';
                $roleId = isset($_POST['role_id']) ? (int)$_POST['role_id'] : $user->role_id;
                $isActive = !empty($_POST['is_active']);

                if ($name === '' || $email === '') {
                    $errors[] = 'Name and email are required.';
                }
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = 'Invalid email format.';
                }

                if (!$errors) {
                    $ok = User::updateBasic($userId, $name, $email, $password !== '' ? $password : null, $roleId, $isActive);
                    if ($ok) {
                        $this->setFlash('success', 'User updated.');
                        $this->redirect('admin/users');
                    } else {
                        $errors[] = 'Failed to update user.';
                    }
                }
            }
        }

        $roles = Role::getAll();
        $csrfToken = $this->generateCsrfToken();

        $this->render('admin/users', [
            'title' => 'Edit User',
            'editUser' => $user,
            'roles' => $roles,
            'errors' => $errors,
            'csrfToken' => $csrfToken,
        ]);
    }

    public function exams(): void
    {
        $this->requireLogin('admin');

        if (is_post()) {
            $token = $_POST['csrf_token'] ?? null;
            if ($this->verifyCsrfToken($token)) {
                $action = $_POST['action'] ?? '';
                $examId = isset($_POST['exam_id']) ? (int)$_POST['exam_id'] : 0;

                if ($examId > 0 && $action === 'delete') {
                    Question::deleteByExam($examId);
                    Exam::delete($examId);
                    $this->setFlash('success', 'Exam deleted.');
                }
            } else {
                $this->setFlash('error', 'Invalid request.');
            }
            $this->redirect('admin/exams');
        }

        $exams = Exam::getAll();
        $csrfToken = $this->generateCsrfToken();

        $this->render('admin/exams', [
            'title' => 'Manage Exams',
            'exams' => $exams,
            'csrfToken' => $csrfToken,
        ]);
    }

    public function createExam(): void
    {
        $this->requireLogin('admin');

        $errors = [];
        $title = '';
        $description = '';
        $duration = '';
        $totalMarks = '';

        if (is_post()) {
            $token = $_POST['csrf_token'] ?? null;
            if (!$this->verifyCsrfToken($token)) {
                $errors[] = 'Invalid request.';
            } else {
                $title = sanitize($_POST['title'] ?? '');
                $description = sanitize($_POST['description'] ?? '');
                $duration = (int)($_POST['duration'] ?? 0);
                $totalMarks = (int)($_POST['total_marks'] ?? 0);
                $isActive = !empty($_POST['is_active']);

                if ($title === '' || $description === '' || $duration <= 0 || $totalMarks <= 0) {
                    $errors[] = 'All fields are required and must be valid.';
                }

                if (!$errors) {
                    $id = Exam::create($title, $description, $duration, $totalMarks, $isActive);
                    if ($id) {
                        $this->setFlash('success', 'Exam created. Add questions next.');
                        $this->redirect('admin/exams');
                    } else {
                        $errors[] = 'Failed to create exam.';
                    }
                }
            }
        }

        $csrfToken = $this->generateCsrfToken();

        $this->render('admin/exams', [
            'title' => 'Create Exam',
            'createMode' => true,
            'errors' => $errors,
            'titleValue' => $title,
            'descriptionValue' => $description,
            'durationValue' => $duration,
            'totalMarksValue' => $totalMarks,
            'csrfToken' => $csrfToken,
        ]);
    }

    public function editExam(): void
    {
        $this->requireLogin('admin');

        $examId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($examId <= 0) {
            $this->setFlash('error', 'Invalid exam.');
            $this->redirect('admin/exams');
        }

        $exam = Exam::findById($examId);
        if (!$exam) {
            $this->setFlash('error', 'Exam not found.');
            $this->redirect('admin/exams');
        }

        $errors = [];

        if (is_post()) {
            $token = $_POST['csrf_token'] ?? null;
            if (!$this->verifyCsrfToken($token)) {
                $errors[] = 'Invalid request.';
            } else {
                $title = sanitize($_POST['title'] ?? '');
                $description = sanitize($_POST['description'] ?? '');
                $duration = (int)($_POST['duration'] ?? $exam->duration_minutes);
                $totalMarks = (int)($_POST['total_marks'] ?? $exam->total_marks);
                $isActive = !empty($_POST['is_active']);

                if ($title === '' || $description === '' || $duration <= 0 || $totalMarks <= 0) {
                    $errors[] = 'All fields are required and must be valid.';
                }

                if (!$errors) {
                    $ok = Exam::update($examId, $title, $description, $duration, $totalMarks, $isActive);
                    if ($ok) {
                        $this->setFlash('success', 'Exam updated.');
                        $this->redirect('admin/exams');
                    } else {
                        $errors[] = 'Failed to update exam.';
                    }
                }
            }
        }

        $csrfToken = $this->generateCsrfToken();

        $this->render('admin/exams', [
            'title' => 'Edit Exam',
            'editExam' => $exam,
            'errors' => $errors,
            'csrfToken' => $csrfToken,
        ]);
    }

    public function questions(): void
    {
        $this->requireLogin('admin');

        $examId = isset($_GET['exam_id']) ? (int)$_GET['exam_id'] : 0;
        if ($examId <= 0) {
            $this->setFlash('error', 'Invalid exam.');
            $this->redirect('admin/exams');
        }

        $exam = Exam::findById($examId);
        if (!$exam) {
            $this->setFlash('error', 'Exam not found.');
            $this->redirect('admin/exams');
        }

        $errors = [];

        if (is_post()) {
            $token = $_POST['csrf_token'] ?? null;
            if (!$this->verifyCsrfToken($token)) {
                $errors[] = 'Invalid request.';
            } else {
                $questionText = sanitize($_POST['question_text'] ?? '');
                $marks = (int)($_POST['marks'] ?? 0);
                $optionTexts = [
                    sanitize($_POST['option_1'] ?? ''),
                    sanitize($_POST['option_2'] ?? ''),
                    sanitize($_POST['option_3'] ?? ''),
                    sanitize($_POST['option_4'] ?? ''),
                ];
                $correctIndex = isset($_POST['correct_option']) ? (int)$_POST['correct_option'] : -1;

                if ($questionText === '' || $marks <= 0) {
                    $errors[] = 'Question text and marks are required.';
                }
                foreach ($optionTexts as $text) {
                    if ($text === '') {
                        $errors[] = 'All options are required.';
                        break;
                    }
                }
                if ($correctIndex < 0 || $correctIndex > 3) {
                    $errors[] = 'Select the correct option.';
                }

                if (!$errors) {
                    $options = [];
                    foreach ($optionTexts as $index => $text) {
                        $options[] = [
                            'text' => $text,
                            'is_correct' => $index === $correctIndex,
                        ];
                    }
                    $questionId = Question::create($examId, $questionText, $marks, $options);
                    if ($questionId) {
                        $this->setFlash('success', 'Question added.');
                        $this->redirect('admin/exams/questions?exam_id=' . $examId);
                    } else {
                        $errors[] = 'Failed to add question.';
                    }
                }
            }
        }

        $questions = Question::findByExam($examId);
        $questionsData = [];
        foreach ($questions as $question) {
            $options = Option::findByQuestion($question->id);
            $questionsData[] = [
                'question' => $question,
                'options' => $options,
            ];
        }

        $csrfToken = $this->generateCsrfToken();

        $this->render('admin/exams', [
            'title' => 'Manage Questions',
            'questionsExam' => $exam,
            'questionsData' => $questionsData,
            'errors' => $errors,
            'csrfToken' => $csrfToken,
        ]);
    }

    public function results(): void
    {
        $this->requireLogin('admin');

        $examId = isset($_GET['exam_id']) ? (int)$_GET['exam_id'] : null;
        $userId = isset($_GET['user_id']) ? (int)$_GET['user_id'] : null;

        if ($examId !== null && $examId <= 0) {
            $examId = null;
        }
        if ($userId !== null && $userId <= 0) {
            $userId = null;
        }

        $results = Result::findAllWithFilters($examId, $userId);
        $exams = Exam::getAll();
        $users = User::getAll();

        $this->render('admin/results', [
            'title' => 'Results',
            'results' => $results,
            'exams' => $exams,
            'users' => $users,
            'selectedExamId' => $examId,
            'selectedUserId' => $userId,
        ]);
    }
}

