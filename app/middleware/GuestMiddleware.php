<?php
namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;

class GuestMiddleware implements MiddlewareInterface {
    public function execute(Request $request, Response $response): void {
        $session = new Session();
        if ($session->get('user_id')) {
            $response->redirect(URL_ROOT . '/dashboard');
        }
    }
}
