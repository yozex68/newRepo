<?php
namespace App\Helpers;

use App\Core\Session;

class Helper {
    /**
     * Escape HTML output to prevent XSS
     */
    public static function esc(?string $text): string {
        if ($text === null) {
            return '';
        }
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Formats URL to absolute root
     */
    public static function url(string $path = ''): string {
        return URL_ROOT . '/' . ltrim($path, '/');
    }

    /**
     * Generate HTML input for CSRF protection
     */
    public static function csrf_field(): string {
        $session = new Session();
        $token = $session->generateCsrfToken();
        return '<input type="hidden" name="csrf_token" value="' . self::esc($token) . '">';
    }

    /**
     * Fetch old form input for repopulation
     */
    public static function old(string $key, $default = '') {
        // We will fetch from request POST data directly or from session if cached
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }

    /**
     * Link to assets
     */
    public static function asset(string $path): string {
        return URL_ROOT . '/assets/' . ltrim($path, '/');
    }

    /**
     * Format file size
     */
    public static function formatBytes(int $bytes, int $precision = 2): string {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * Return user role badge color
     */
    public static function roleBadge(string $role): string {
        return match ($role) {
            'admin' => 'bg-danger text-white',
            'student' => 'bg-success text-white',
            default => 'bg-warning text-dark',
        };
    }
    
    /**
     * Check if a given route path is active
     */
    public static function isActive(string $routePath): string {
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        $scriptName = dirname($_SERVER['SCRIPT_NAME']);
        if (strpos($path, $scriptName) === 0) {
            $path = substr($path, strlen($scriptName));
        }
        if (strpos($path, '/public') === 0) {
            $path = substr($path, 7);
        }
        $position = strpos($path, '?');
        if ($position !== false) {
            $path = substr($path, 0, $position);
        }
        
        $pathClean = ($path === '/') ? '/' : rtrim($path, '/');
        $routeClean = ($routePath === '/') ? '/' : rtrim($routePath, '/');
        
        return ($pathClean === $routeClean) ? 'active' : '';
    }
}
