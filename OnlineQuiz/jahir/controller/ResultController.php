<?php
declare(strict_types=1);

class ResultController extends BaseController
{
    public function index(): void
    {
        $this->requireLogin('user');
        $userId = (int)$_SESSION['user']['id'];
        $results = Result::findByUser($userId);

        $this->render('user/result', [
            'title' => 'My Results',
            'results' => $results,
        ]);
    }

    public function view(): void
    {
        $this->requireLogin('user');

        $attemptId = isset($_GET['attempt_id']) ? (int)$_GET['attempt_id'] : 0;
        if ($attemptId <= 0) {
            $this->setFlash('error', 'Invalid result.');
            $this->redirect('user/results');
        }

        $attempt = Attempt::findById($attemptId);
        if (!$attempt || $attempt->user_id !== (int)$_SESSION['user']['id']) {
            $this->setFlash('error', 'Result not found.');
            $this->redirect('user/results');
        }

        $result = Result::findByAttempt($attemptId);
        if (!$result) {
            $this->setFlash('error', 'Result not found.');
            $this->redirect('user/results');
        }

        $exam = Exam::findById($attempt->exam_id);

        $this->render('user/result', [
            'title' => 'Result Details',
            'singleResult' => $result,
            'exam' => $exam,
            'attempt' => $attempt,
        ]);
    }
}

