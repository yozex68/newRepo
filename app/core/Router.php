<?php
namespace App\Core;

use Exception;

class Router {
    protected array $routes = [];
    protected Request $request;
    protected Response $response;

    public function __construct(Request $request, Response $response) {
        $this->request = $request;
        $this->response = $response;
    }

    public function get(string $path, $callback, array $middlewares = []): void {
        $this->routes['get'][$path] = [
            'callback' => $callback,
            'middleware' => $middlewares
        ];
    }

    public function post(string $path, $callback, array $middlewares = []): void {
        $this->routes['post'][$path] = [
            'callback' => $callback,
            'middleware' => $middlewares
        ];
    }

    public function resolve() {
        $path = $this->request->getPath();
        $method = $this->request->getMethod();
        
        $routes = $this->routes[$method] ?? [];
        
        foreach ($routes as $routePath => $routeData) {
            // Trim slashes for comparison except for root
            $routeClean = ($routePath === '/') ? '/' : rtrim($routePath, '/');
            $pathClean = ($path === '/') ? '/' : rtrim($path, '/');
            
            // Replace e.g., {id} with named capture group
            $pattern = '^' . preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $routeClean) . '$';
            $pattern = str_replace('/', '\/', $pattern);
            
            if (preg_match('/' . $pattern . '/', $pathClean, $matches)) {
                $params = [];
                foreach ($matches as $key => $value) {
                    if (is_string($key)) {
                        $params[$key] = urldecode($value);
                    }
                }
                
                // Execute Middlewares
                $middlewares = $routeData['middleware'];
                foreach ($middlewares as $middlewareClass) {
                    $middleware = new $middlewareClass();
                    $middleware->execute($this->request, $this->response);
                }
                
                $callback = $routeData['callback'];
                if (is_array($callback)) {
                    $controllerClass = $callback[0];
                    $action = $callback[1];
                    
                    if (class_exists($controllerClass)) {
                        $controller = new $controllerClass();
                        if (method_exists($controller, $action)) {
                            return call_user_func_array([$controller, $action], [$this->request, $this->response, $params]);
                        }
                        throw new Exception("Action '{$action}' not found in controller '{$controllerClass}'");
                    }
                    throw new Exception("Controller class '{$controllerClass}' not found");
                }
                
                if (is_callable($callback)) {
                    return call_user_func_array($callback, [$this->request, $this->response, $params]);
                }
            }
        }
        
        // 404 Route Not Found
        $this->response->setStatusCode(404);
        
        // Fallback to rendering custom 404 error page
        $controllerClass = '\\App\\Controllers\\HomeController';
        if (class_exists($controllerClass)) {
            $controller = new $controllerClass();
            return $controller->render('errors/404', ['title' => 'Page Not Found']);
        }
        
        echo "404 Not Found";
        exit;
    }
}
