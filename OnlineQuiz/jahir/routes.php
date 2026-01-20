<?php
declare(strict_types=1);

return [
    '' => ['controller' => 'HomeController', 'action' => 'index'],
    'home' => ['controller' => 'HomeController', 'action' => 'index'],

    'login' => ['controller' => 'AuthController', 'action' => 'login'],
    'register' => ['controller' => 'AuthController', 'action' => 'register'],
    'forgot-password' => ['controller' => 'AuthController', 'action' => 'forgot'],
    'logout' => ['controller' => 'AuthController', 'action' => 'logout'],

    'admin/dashboard' => ['controller' => 'AdminController', 'action' => 'dashboard'],
    'admin/users' => ['controller' => 'AdminController', 'action' => 'users'],
    'admin/users/edit' => ['controller' => 'AdminController', 'action' => 'editUser'],
    'admin/exams' => ['controller' => 'AdminController', 'action' => 'exams'],
    'admin/exams/create' => ['controller' => 'AdminController', 'action' => 'createExam'],
    'admin/exams/edit' => ['controller' => 'AdminController', 'action' => 'editExam'],
    'admin/exams/questions' => ['controller' => 'AdminController', 'action' => 'questions'],
    'admin/results' => ['controller' => 'AdminController', 'action' => 'results'],

    'user/dashboard' => ['controller' => 'UserController', 'action' => 'dashboard'],
    'user/exam/start' => ['controller' => 'ExamController', 'action' => 'start'],
    'user/exam/submit' => ['controller' => 'ExamController', 'action' => 'submit'],
    'user/results' => ['controller' => 'ResultController', 'action' => 'index'],
    'user/result/view' => ['controller' => 'ResultController', 'action' => 'view'],

    // API Routes (JSON Operations)
    'api/exam/details' => ['controller' => 'ApiController', 'action' => 'getExamDetails'], // GET
    'api/exam/create' => ['controller' => 'ApiController', 'action' => 'createExam'],     // POST
    'api/exam/delete' => ['controller' => 'ApiController', 'action' => 'deleteExam'],     // DELETE
];

