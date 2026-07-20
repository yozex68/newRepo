<?php
namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;

class CsrfMiddleware implements MiddlewareInterface {
    public function execute(Request $request, Response $response): void {
        if ($request->isPost()) {
            $session = new Session();
            $token = $request->input('csrf_token') ?: ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? null);
            
            if (!$session->verifyCsrfToken($token)) {
                $response->setStatusCode(403);
                $session->setFlash('error', 'Security Alert: CSRF token validation failed.');
                $response->redirect(URL_ROOT . '/error-403');
            }
        }
    }
}
