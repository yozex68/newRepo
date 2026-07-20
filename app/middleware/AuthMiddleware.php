<?php
namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;

class AuthMiddleware implements MiddlewareInterface {
    public function execute(Request $request, Response $response): void {
        $session = new Session();
        if (!$session->get('user_id')) {
            $session->setFlash('error', 'Access denied. Please login to access this resource.');
            $response->redirect(URL_ROOT . '/login');
        }
    }
}
