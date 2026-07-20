<?php
namespace App\Core;

class Session {
    protected const FLASH_KEY = 'flash_messages';

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            // Set secure session cookie parameters
            session_set_cookie_params([
                'lifetime' => SESSION_LIFETIME,
                'path' => '/',
                'domain' => '',
                'secure' => isset($_SERVER['HTTPS']),
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
            session_start();
        }

        // Check for session timeout
        $this->checkTimeout();

        // Mark flash messages to be deleted
        $flashMessages = $_SESSION[self::FLASH_KEY] ?? [];
        foreach ($flashMessages as $key => &$flashMessage) {
            $flashMessage['remove'] = true;
        }
        $_SESSION[self::FLASH_KEY] = $flashMessages;
    }

    private function checkTimeout(): void {
        if ($this->get('user_id')) {
            $now = time();
            $lastActivity = $this->get('last_activity', $now);
            if ($now - $lastActivity > SESSION_LIFETIME) {
                $this->destroy();
                $this->setFlash('error', 'Your session has expired due to inactivity. Please login again.');
            } else {
                $this->set('last_activity', $now);
            }
        }
    }

    public function setFlash(string $key, string $message): void {
        $_SESSION[self::FLASH_KEY][$key] = [
            'remove' => false,
            'value' => $message
        ];
    }

    public function getFlash(string $key): ?string {
        return $_SESSION[self::FLASH_KEY][$key]['value'] ?? null;
    }

    public function hasFlash(string $key): bool {
        return isset($_SESSION[self::FLASH_KEY][$key]);
    }

    public function set(string $key, $value): void {
        $_SESSION[$key] = $value;
    }

    public function get(string $key, $default = null) {
        return $_SESSION[$key] ?? $default;
    }

    public function remove(string $key): void {
        unset($_SESSION[$key]);
    }

    public function regenerate(): void {
        session_regenerate_id(true);
        $this->set('last_activity', time());
    }

    public function destroy(): void {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        session_destroy();
    }

    // CSRF Token Management
    public function generateCsrfToken(): string {
        if (!$this->get('csrf_token')) {
            $token = bin2hex(random_bytes(32));
            $this->set('csrf_token', $token);
        }
        return $this->get('csrf_token');
    }

    public function verifyCsrfToken(?string $token): bool {
        $stored = $this->get('csrf_token');
        if (!$stored || !$token) {
            return false;
        }
        return hash_equals($stored, $token);
    }

    public function __destruct() {
        // Clean up flash messages that were marked for deletion
        $flashMessages = $_SESSION[self::FLASH_KEY] ?? [];
        foreach ($flashMessages as $key => $flashMessage) {
            if ($flashMessage['remove']) {
                unset($flashMessages[$key]);
            }
        }
        $_SESSION[self::FLASH_KEY] = $flashMessages;
    }
}
