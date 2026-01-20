<?php
declare(strict_types=1);

abstract class BaseController
{
    protected array $data = [];

    protected function render(string $view, array $params = []): void
    {
        $this->data = $params;
        $viewFile = BASE_PATH . '/view/' . $view . '.php';
        if (!file_exists($viewFile)) {
            http_response_code(500);
            echo 'View not found';
            exit;
        }

        $content = $this->renderViewFile($viewFile, $this->data);
        $layoutFile = BASE_PATH . '/view/layouts/main.php';
        if (file_exists($layoutFile)) {
            $title = $params['title'] ?? 'Online Exam';
            require $layoutFile;
        } else {
            echo $content;
        }
    }

    private function renderViewFile(string $_viewFile_, array $_params_): string
    {
        extract($_params_, EXTR_OVERWRITE);
        ob_start();
        require $_viewFile_;
        return ob_get_clean() ?: '';
    }

    protected function redirect(string $path): void
    {
        header('Location: ' . base_url($path));
        exit;
    }

    protected function setFlash(string $key, string $message): void
    {
        $_SESSION['flash'][$key] = $message;
    }

    protected function getFlash(string $key): ?string
    {
        if (!empty($_SESSION['flash'][$key])) {
            $message = $_SESSION['flash'][$key];
            unset($_SESSION['flash'][$key]);
            return $message;
        }
        return null;
    }

    protected function generateCsrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    protected function verifyCsrfToken(?string $token): bool
    {
        $sessionToken = $_SESSION['csrf_token'] ?? '';
        return is_string($token) && hash_equals($sessionToken, $token);
    }

    protected function requireLogin(?string $role = null): void
    {
        $this->ensureRememberedUser();

        if (empty($_SESSION['user'])) {
            $this->redirect('login');
        }

        if ($role !== null && ($_SESSION['user']['role'] ?? null) !== $role) {
            http_response_code(403);
            echo 'Forbidden';
            exit;
        }
    }

    protected function ensureRememberedUser(): void
    {
        if (!empty($_SESSION['user'])) {
            return;
        }
        if (empty($_COOKIE['remember_token'])) {
            return;
        }
        $token = $_COOKIE['remember_token'];
        if (!is_string($token) || $token === '') {
            return;
        }
        if (!class_exists('User')) {
            return;
        }
        $user = User::findByRememberToken($token);
        if (!$user || !$user->is_active) {
            return;
        }
        $role = self::resolveRoleName($user->role_id);
        $_SESSION['user'] = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role_id' => $user->role_id,
            'role' => $role,
        ];
    }

    protected static function resolveRoleName(int $roleId): string
    {
        if (class_exists('Role')) {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare('SELECT name FROM roles WHERE id = ? LIMIT 1');
            $stmt->bind_param('i', $roleId);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $stmt->close();
            if ($row && isset($row['name'])) {
                return $row['name'];
            }
        }
        return 'user';
    }
}
