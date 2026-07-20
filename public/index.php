<?php
/**
 * SmartHUB Front Controller & Routing Gateway
 */

// 1. Load Configurations
require_once __DIR__ . '/../app/config/config.php';

// 2. Custom PSR-4 Class Autoloader
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/../app/';
    $len = strlen($prefix);
    
    if (strncmp($prefix, $class, $len) !== 0) {
        return; // Move to next registered autoloader
    }
    
    $relativeClass = substr($class, $len);
    // Replace namespace separator with directory separator, add .php
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    
    if (file_exists($file)) {
        require_once $file;
    }
});

// 3. Initialize Session Security
$session = new App\Core\Session();

// 4. Instantiate Core Request, Response and Router
$request = new App\Core\Request();
$response = new App\Core\Response();
$router = new App\Core\Router($request, $response);

// 5. Define Middlewares
$guest = App\Middleware\GuestMiddleware::class;
$auth = App\Middleware\AuthMiddleware::class;
$admin = App\Middleware\AdminMiddleware::class;
$csrf = App\Middleware\CsrfMiddleware::class;

// ----------------------------------------------------
// ROUTE REGISTRATION
// ----------------------------------------------------

// Guest & General Routes
$router->get('/', [App\Controllers\HomeController::class, 'index']);
$router->get('/announcements', [App\Controllers\HomeController::class, 'announcements']);

$router->get('/login', [App\Controllers\AuthController::class, 'showLogin'], [$guest]);
$router->post('/login', [App\Controllers\AuthController::class, 'login'], [$guest, $csrf]);
$router->get('/register', [App\Controllers\AuthController::class, 'showRegister'], [$guest]);
$router->post('/register', [App\Controllers\AuthController::class, 'register'], [$guest, $csrf]);
$router->get('/logout', [App\Controllers\AuthController::class, 'logout']);

// Standard Student / Authenticated Routes
$router->get('/dashboard', [App\Controllers\HomeController::class, 'dashboard'], [$auth]);
$router->get('/profile', [App\Controllers\AuthController::class, 'profile'], [$auth]);
$router->post('/profile', [App\Controllers\AuthController::class, 'updateProfile'], [$auth, $csrf]);
$router->post('/profile/password', [App\Controllers\AuthController::class, 'changePassword'], [$auth, $csrf]);

$router->get('/courses', [App\Controllers\StudentController::class, 'courses'], [$auth]);
$router->get('/courses/{id}', [App\Controllers\StudentController::class, 'courseDetails'], [$auth]);

$router->get('/materials/download/{id}', [App\Controllers\MaterialController::class, 'download'], [$auth]);
$router->get('/materials/bookmark/{id}', [App\Controllers\MaterialController::class, 'bookmark'], [$auth]);

// Questions & Replies (Q&A Board)
$router->get('/questions', [App\Controllers\QuestionController::class, 'index'], [$auth]);
$router->post('/questions/create', [App\Controllers\QuestionController::class, 'create'], [$auth, $csrf]);
$router->get('/questions/{id}', [App\Controllers\QuestionController::class, 'view'], [$auth]);
$router->post('/questions/{id}/reply', [App\Controllers\QuestionController::class, 'reply'], [$auth, $csrf]);

// Subscriptions
$router->get('/subscribe', [App\Controllers\StudentController::class, 'subscribe'], [$auth]);
$router->post('/subscribe/checkout/{id}', [App\Controllers\StudentController::class, 'checkout'], [$auth, $csrf]);

// Error Routes
$router->get('/error-403', function($req, $res) {
    $c = new App\Controllers\HomeController();
    return $c->render('errors/403', ['title' => 'Forbidden Access']);
});

// ----------------------------------------------------
// ADMINISTRATOR ROUTES
// ----------------------------------------------------

// Faculties
$router->get('/admin/faculties', [App\Controllers\AdminController::class, 'faculties'], [$admin]);
$router->post('/admin/faculties/create', [App\Controllers\AdminController::class, 'createFaculty'], [$admin, $csrf]);
$router->post('/admin/faculties/edit/{id}', [App\Controllers\AdminController::class, 'editFaculty'], [$admin, $csrf]);
$router->get('/admin/faculties/delete/{id}', [App\Controllers\AdminController::class, 'deleteFaculty'], [$admin]);

// Programmes
$router->get('/admin/programmes', [App\Controllers\AdminController::class, 'programmes'], [$admin]);
$router->post('/admin/programmes/create', [App\Controllers\AdminController::class, 'createProgramme'], [$admin, $csrf]);
$router->post('/admin/programmes/edit/{id}', [App\Controllers\AdminController::class, 'editProgramme'], [$admin, $csrf]);
$router->get('/admin/programmes/delete/{id}', [App\Controllers\AdminController::class, 'deleteProgramme'], [$admin]);

// Courses
$router->get('/admin/courses', [App\Controllers\AdminController::class, 'courses'], [$admin]);
$router->post('/admin/courses/create', [App\Controllers\AdminController::class, 'createCourse'], [$admin, $csrf]);
$router->post('/admin/courses/edit/{id}', [App\Controllers\AdminController::class, 'editCourse'], [$admin, $csrf]);
$router->get('/admin/courses/delete/{id}', [App\Controllers\AdminController::class, 'deleteCourse'], [$admin]);

// Materials
$router->get('/admin/materials', [App\Controllers\AdminController::class, 'materials'], [$admin]);
$router->post('/admin/materials/upload', [App\Controllers\MaterialController::class, 'upload'], [$admin, $csrf]);
$router->get('/admin/materials/delete/{id}', [App\Controllers\MaterialController::class, 'delete'], [$admin]);

// Users & Permissions Management
$router->get('/admin/users', [App\Controllers\AdminController::class, 'users'], [$admin]);
$router->post('/admin/users/update-role/{id}', [App\Controllers\AdminController::class, 'updateUserRole'], [$admin, $csrf]);
$router->post('/admin/users/grant-permission', [App\Controllers\AdminController::class, 'grantPermission'], [$admin, $csrf]);
$router->get('/admin/users/revoke-permission/{id}', [App\Controllers\AdminController::class, 'revokePermission'], [$admin]);

// Subscriptions
$router->get('/admin/subscriptions', [App\Controllers\AdminController::class, 'subscriptions'], [$admin]);

// Reports & Financial Analytics
$router->get('/admin/reports', [App\Controllers\ReportController::class, 'index'], [$admin]);
$router->get('/admin/reports/export', [App\Controllers\ReportController::class, 'export'], [$admin]);

// Database Backups
$router->get('/admin/backup', [App\Controllers\AdminController::class, 'backupDatabase'], [$admin]);


// 6. Resolve Route
$router->resolve();
