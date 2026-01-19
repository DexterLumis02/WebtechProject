<?php
declare(strict_types=1);

class ExamController extends BaseController
{
    public function start(): void
    {
        $this->requireLogin('user');

        $userId = (int)$_SESSION['user']['id'];
        $examId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($examId <= 0) {
            $this->setFlash('error', 'Invalid exam.');
            $this->redirect('user/dashboard');
        }

        $exam = Exam::findById($examId);
        if (!$exam || !$exam->is_active) {
            $this->setFlash('error', 'Exam not available.');
            $this->redirect('user/dashboard');
        }

        $completedAttempt = Attempt::findCompletedForUserExam($userId, $examId);
        if ($completedAttempt) {
            $this->setFlash('error', 'You have already attempted this exam.');
            $this->redirect('user/dashboard');
        }

        $attempt = Attempt::findActiveForUserExam($userId, $examId);
        if (!$attempt) {
            $attemptId = Attempt::create($userId, $examId);
            if (!$attemptId) {
                $this->setFlash('error', 'Unable to start exam.');
                $this->redirect('user/dashboard');
            }
            $attempt = Attempt::findById($attemptId);
        }

        if (!isset($_SESSION['exam_timers'])) {
            $_SESSION['exam_timers'] = [];
        }

        if (empty($_SESSION['exam_timers'][$attempt->id])) {
            $_SESSION['exam_timers'][$attempt->id] = time() + ($exam->duration_minutes * 60);
        }

        $endTime = (int)$_SESSION['exam_timers'][$attempt->id];
        $remainingSeconds = max(0, $endTime - time());

        $questions = Question::findByExam($examId);
        $questionsWithOptions = [];

        foreach ($questions as $question) {
            $options = Option::findByQuestion($question->id);
            $questionsWithOptions[] = [
                'question' => $question,
                'options' => $options,
            ];
        }

        $csrfToken = $this->generateCsrfToken();

        $this->render('user/exam', [
            'title' => $exam->title,
            'exam' => $exam,
            'attempt' => $attempt,
            'questionsWithOptions' => $questionsWithOptions,
            'remainingSeconds' => $remainingSeconds,
            'csrfToken' => $csrfToken,
        ]);
    }

    public function submit(): void
    {
        $this->requireLogin('user');

        if (!is_post()) {
            $this->redirect('user/dashboard');
        }

        $token = $_POST['csrf_token'] ?? null;
        if (!$this->verifyCsrfToken($token)) {
            $this->setFlash('error', 'Invalid request.');
            $this->redirect('user/dashboard');
        }

        $userId = (int)$_SESSION['user']['id'];
        $examId = isset($_POST['exam_id']) ? (int)$_POST['exam_id'] : 0;
        $attemptId = isset($_POST['attempt_id']) ? (int)$_POST['attempt_id'] : 0;

        if ($examId <= 0 || $attemptId <= 0) {
            $this->setFlash('error', 'Invalid submission.');
            $this->redirect('user/dashboard');
        }

        $attempt = Attempt::findById($attemptId);
        if (!$attempt || $attempt->user_id !== $userId || $attempt->exam_id !== $examId) {
            $this->setFlash('error', 'Invalid attempt.');
            $this->redirect('user/dashboard');
        }

        if ($attempt->status === 'completed') {
            $this->setFlash('error', 'This attempt is already completed.');
            $this->redirect('user/dashboard');
        }

        $exam = Exam::findById($examId);
        if (!$exam) {
            $this->setFlash('error', 'Exam not found.');
            $this->redirect('user/dashboard');
        }

        $endTime = $_SESSION['exam_timers'][$attemptId] ?? (time() + 1);
        if (time() > $endTime) {
            $this->setFlash('error', 'Time is over. Your answers have been submitted.');
        }

        $answers = $_POST['answers'] ?? [];
        $questions = Question::findByExam($examId);

        $score = 0;
        $correct = 0;
        $wrong = 0;

        foreach ($questions as $question) {
            $questionId = $question->id;
            $givenOptionId = isset($answers[$questionId]) ? (int)$answers[$questionId] : 0;
            $correctOptionId = Option::getCorrectOptionId($questionId);

            if ($givenOptionId && $correctOptionId && $givenOptionId === $correctOptionId) {
                $score += $question->marks;
                $correct++;
            } elseif ($givenOptionId) {
                $wrong++;
            }
        }

        Attempt::complete($attemptId);
        unset($_SESSION['exam_timers'][$attemptId]);

        $resultId = Result::create($attemptId, $userId, $examId, $score, $exam->total_marks, $correct, $wrong);

        if (!$resultId) {
            $this->setFlash('error', 'Failed to save result.');
            $this->redirect('user/dashboard');
        }

        $this->redirect('user/result/view?attempt_id=' . $attemptId);
    }
}

