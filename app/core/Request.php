<?php
namespace App\Core;

class Request {
    public function getPath(): string {
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        
        // Strip public prefix from subfolder deployments (e.g. /SMARTHUB/public/admin -> /admin)
        $scriptName = dirname($_SERVER['SCRIPT_NAME']);
        if (strpos($path, $scriptName) === 0) {
            $path = substr($path, strlen($scriptName));
        }
        
        // Clean public keyword if present
        if (strpos($path, '/public') === 0) {
            $path = substr($path, 7);
        }

        $position = strpos($path, '?');
        if ($position !== false) {
            $path = substr($path, 0, $position);
        }
        
        return $path === '' ? '/' : $path;
    }

    public function getMethod(): string {
        return strtolower($_SERVER['REQUEST_METHOD'] ?? 'get');
    }

    public function isGet(): bool {
        return $this->getMethod() === 'get';
    }

    public function isPost(): bool {
        return $this->getMethod() === 'post';
    }

    public function getBody(): array {
        $body = [];
        if ($this->isGet()) {
            foreach ($_GET as $key => $value) {
                $body[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }
        if ($this->isPost()) {
            if ($this->isJson()) {
                $raw = file_get_contents('php://input');
                $decoded = json_decode($raw, true);
                if (is_array($decoded)) {
                    foreach ($decoded as $key => $value) {
                        $body[$key] = $this->sanitize($value);
                    }
                }
            } else {
                foreach ($_POST as $key => $value) {
                    $body[$key] = $this->sanitize($value);
                }
            }
        }
        return $body;
    }

    private function sanitize($value) {
        if (is_array($value)) {
            foreach ($value as $k => $v) {
                $value[$k] = $this->sanitize($v);
            }
            return $value;
        }
        return htmlspecialchars(trim((string)$value), ENT_QUOTES, 'UTF-8');
    }

    public function input(string $key, $default = null) {
        $body = $this->getBody();
        return $body[$key] ?? $default;
    }

    public function isJson(): bool {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        return strpos($contentType, 'application/json') !== false;
    }

    public function getFiles(): array {
        return $_FILES;
    }

    public function file(string $key): ?array {
        return $_FILES[$key] ?? null;
    }
}
