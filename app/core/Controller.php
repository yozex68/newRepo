<?php
namespace App\Core;

use Exception;

abstract class Controller {
    /**
     * Renders a view template inside a layout.
     *
     * @param string $view The view file name (relative to app/views)
     * @param array $data Data variables to extract into the view context
     * @param string $layout The layout shell file (main, auth, raw)
     */
    public function render(string $view, array $data = [], string $layout = 'main'): void {
        // Extract variables to local scope
        extract($data);
        
        // Provide session helper to views
        $session = new Session();
        
        $viewFile = APP_ROOT . "/views/{$view}.php";
        if (!file_exists($viewFile)) {
            throw new Exception("View file '{$view}' not found at: {$viewFile}");
        }

        if ($layout === 'raw') {
            include $viewFile;
            return;
        }

        // Buffer the view content
        ob_start();
        include $viewFile;
        $content = ob_get_clean();

        // Load the specified layout shell
        $layoutFile = APP_ROOT . "/views/layouts/{$layout}.php";
        if (file_exists($layoutFile)) {
            include $layoutFile;
        } else {
            // Default fallback
            include APP_ROOT . "/views/templates/header.php";
            echo $content;
            include APP_ROOT . "/views/templates/footer.php";
        }
    }

    /**
     * Helper to return standard JSON response
     */
    protected function json(array $data, int $status = 200): void {
        $response = new Response();
        $response->json($data, $status);
    }

    /**
     * Helper to redirect
     */
    protected function redirect(string $path): void {
        $response = new Response();
        $response->redirect(URL_ROOT . $path);
    }
}
