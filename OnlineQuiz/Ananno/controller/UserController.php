<?php
declare(strict_types=1);

class UserController extends BaseController
{
    public function dashboard(): void
    {
        $this->requireLogin('user');

        $userId = (int)$_SESSION['user']['id'];
        $exams = Exam::getActive();
        $examStatuses = [];

        foreach ($exams as $exam) {
            $completedAttempt = Attempt::findCompletedForUserExam($userId, $exam->id);
            $examStatuses[$exam->id] = [
                'completed' => $completedAttempt !== null,
            ];
        }

        $results = Result::findByUser($userId);

        $this->render('user/dashboard', [
            'title' => 'User Dashboard',
            'exams' => $exams,
            'examStatuses' => $examStatuses,
            'results' => $results,
        ]);
    }
}

