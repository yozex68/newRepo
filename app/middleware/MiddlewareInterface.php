<?php
namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;

interface MiddlewareInterface {
    /**
     * Intercepts and validates HTTP request before it reaches the controller action.
     *
     * @param Request $request
     * @param Response $response
     * @return void
     */
    public function execute(Request $request, Response $response): void;
}
