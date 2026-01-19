<?php
declare(strict_types=1);

class AuthController extends BaseController
{
    public function login(): void
    {
        $this->ensureRememberedUser();
        if (!empty($_SESSION['user'])) {
            $role = $_SESSION['user']['role'] ?? 'user'; //role line
            if ($role === 'admin') {
                $this->redirect('admin/dashboard');
            } else {
                $this->redirect('user/dashboard');
            }
        }

        $errors = [];
        $email = '';
        $role = 'user';

        if (is_post()) {
            $email = sanitize($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $remember = !empty($_POST['remember']);
            $role = $_POST['role'] ?? 'user';
            $token = $_POST['csrf_token'] ?? null;

            if ($role !== 'admin' && $role !== 'user') {
                $role = 'user';
            }

            if (!$this->verifyCsrfToken($token)) {
                $errors[] = 'Invalid request.';
            } elseif ($email === '' || $password === '') {
                $errors[] = 'Email and password are required.';
            } else {
                $userData = User::authenticate($email, $password);
                if (!$userData) {
                    $errors[] = 'Invalid credentials or inactive account.';
                } else {
                    if ($userData['role'] !== $role) {
                        $errors[] = 'You cannot log in as ' . $role . ' with this account.';
                    } else {
                        $_SESSION['user'] = $userData;

                        if ($remember) {
                            $rememberToken = bin2hex(random_bytes(32));
                            User::setRememberToken($userData['id'], $rememberToken);
                            setcookie(
                                'remember_token',
                                $rememberToken,
                                time() + 60 * 60 * 24 * 30,
                                '/',
                                '',
                                false,
                                true
                            );
                        } else {
                            User::setRememberToken($userData['id'], null);
                            setcookie(
                                'remember_token',
                                '',
                                time() - 3600,
                                '/',
                                '',
                                false,
                                true
                            );
                        }

                        if ($userData['role'] === 'admin') {
                            $this->redirect('admin/dashboard');
                        } else {
                            $this->redirect('user/dashboard');
                        }
                    }
                }
            }
        }

        $csrfToken = $this->generateCsrfToken();
        $this->render('auth/login', [
            'title' => 'Login',
            'errors' => $errors,
            'email' => $email,
            'role' => $role,
            'csrfToken' => $csrfToken,
        ]);
    }

    public function register(): void
    {
        if (!empty($_SESSION['user'])) {
            $this->redirect('home');
        }

        $errors = [];
        $name = '';
        $email = '';
        $role = 'user';

        if (is_post()) {
            $name = sanitize($_POST['name'] ?? '');
            $email = sanitize($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            $role = $_POST['role'] ?? 'user';
            $token = $_POST['csrf_token'] ?? null;

            if ($role !== 'admin' && $role !== 'user') {
                $role = 'user';
            }

            if (!$this->verifyCsrfToken($token)) {
                $errors[] = 'Invalid request.';
            } else {
                if ($name === '' || $email === '' || $password === '' || $confirmPassword === '') {
                    $errors[] = 'All fields are required.';
                }
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = 'Invalid email format.';
                }
                if ($password !== $confirmPassword) {
                    $errors[] = 'Passwords do not match.';
                }
                if (User::findByEmail($email)) {
                    $errors[] = 'Email already registered.';
                }

                if (!$errors) {
                    $userId = User::create($name, $email, $password, $role);
                    if ($userId) {
                        $this->setFlash('success', 'Registration successful. You can log in now.');
                        $this->redirect('login');
                    } else {
                        $errors[] = 'Registration failed.';
                    }
                }
            }
        }

        $csrfToken = $this->generateCsrfToken();
        $this->render('auth/register', [
            'title' => 'Register',
            'errors' => $errors,
            'name' => $name,
            'email' => $email,
            'role' => $role,
            'csrfToken' => $csrfToken,
        ]);
    }

    public function forgot(): void
    {
        $errors = [];
        $email = '';

        if (is_post()) {
            $email = sanitize($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            $token = $_POST['csrf_token'] ?? null;

            if (!$this->verifyCsrfToken($token)) {
                $errors[] = 'Invalid request.';
            } else {
                if ($email === '' || $password === '' || $confirmPassword === '') {
                    $errors[] = 'All fields are required.';
                }
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = 'Invalid email format.';
                }
                if ($password !== $confirmPassword) {
                    $errors[] = 'Passwords do not match.';
                }

                $user = User::findByEmail($email);
                if (!$user) {
                    $errors[] = 'No user found with this email.';
                }

                if (!$errors && $user) {
                    $ok = User::updateBasic($user->id, $user->name, $email, $password);
                    if ($ok) {
                        $this->setFlash('success', 'Password updated. Please log in.');
                        $this->redirect('login');
                    } else {
                        $errors[] = 'Failed to update password.';
                    }
                }
            }
        }

        $csrfToken = $this->generateCsrfToken();
        $this->render('auth/forgot', [
            'title' => 'Forgot Password',
            'errors' => $errors,
            'email' => $email,
            'csrfToken' => $csrfToken,
        ]);
    }

    public function logout(): void
    {
        if (!empty($_SESSION['user'])) {
            if (isset($_SESSION['user']['id'])) {
                User::setRememberToken((int)$_SESSION['user']['id'], null);
            }
        }

        setcookie('remember_token', '', time() - 3600, '/', '', false, true);
        $_SESSION = [];
        if (session_id() !== '') {
            session_destroy();
        }
        session_start();

        $this->setFlash('success', 'Logged out successfully.');
        $this->redirect('login');
    }
}