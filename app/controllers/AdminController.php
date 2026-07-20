<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Models\User;
use App\Models\Faculty;
use App\Models\Programme;
use App\Models\Course;
use App\Models\Material;
use App\Models\SubscriptionPlan;
use App\Models\Subscription;
use App\Models\AuditLog;
use App\Helpers\Backup;
use App\Helpers\Validation;

class AdminController extends Controller {

    // ==========================================
    // FACULTY CRUD
    // ==========================================

    public function faculties(Request $request, Response $response): void {
        $facModel = new Faculty();
        $faculties = $facModel->getWithProgrammesCount();
        
        $this->render('admin/faculties', [
            'title' => 'Manage Academic Faculties',
            'faculties' => $faculties
        ], 'main');
    }

    public function createFaculty(Request $request, Response $response): void {
        $session = new Session();
        $facModel = new Faculty();
        $logModel = new AuditLog();

        $name = $request->input('name');
        $desc = $request->input('description');

        if (empty($name)) {
            $session->setFlash('error', 'Faculty name is required.');
            $this->redirect('/admin/faculties');
            return;
        }

        if ($facModel->findBy('name', $name)) {
            $session->setFlash('error', 'A faculty with this name already exists.');
            $this->redirect('/admin/faculties');
            return;
        }

        $facId = $facModel->create([
            'name' => $name,
            'description' => $desc
        ]);

        if ($facId > 0) {
            $logModel->log($session->get('user_id'), 'Create Faculty', "Created faculty: {$name}");
            $session->setFlash('success', 'Faculty created successfully.');
        } else {
            $session->setFlash('error', 'Failed to create faculty.');
        }
        $this->redirect('/admin/faculties');
    }

    public function editFaculty(Request $request, Response $response, array $params): void {
        $session = new Session();
        $facModel = new Faculty();
        $logModel = new AuditLog();
        $id = (int)($params['id'] ?? 0);

        $name = $request->input('name');
        $desc = $request->input('description');

        if (empty($name)) {
            $session->setFlash('error', 'Faculty name is required.');
            $this->redirect('/admin/faculties');
            return;
        }

        $current = $facModel->find($id);
        if ($current && $current['name'] !== $name) {
            if ($facModel->findBy('name', $name)) {
                $session->setFlash('error', 'A faculty with this name already exists.');
                $this->redirect('/admin/faculties');
                return;
            }
        }

        if ($facModel->update($id, ['name' => $name, 'description' => $desc])) {
            $logModel->log($session->get('user_id'), 'Edit Faculty', "Edited faculty ID: {$id} to {$name}");
            $session->setFlash('success', 'Faculty updated successfully.');
        } else {
            $session->setFlash('error', 'Failed to update faculty.');
        }
        $this->redirect('/admin/faculties');
    }

    public function deleteFaculty(Request $request, Response $response, array $params): void {
        $session = new Session();
        $facModel = new Faculty();
        $logModel = new AuditLog();
        $id = (int)($params['id'] ?? 0);

        $fac = $facModel->find($id);
        if ($fac && $facModel->delete($id)) {
            $logModel->log($session->get('user_id'), 'Delete Faculty', "Deleted faculty: " . $fac['name']);
            $session->setFlash('success', 'Faculty deleted successfully.');
        } else {
            $session->setFlash('error', 'Failed to delete faculty.');
        }
        $this->redirect('/admin/faculties');
    }

    // ==========================================
    // PROGRAMME CRUD
    // ==========================================

    public function programmes(Request $request, Response $response): void {
        $facModel = new Faculty();
        $progModel = new Programme();
        
        $faculties = $facModel->all();
        $programmes = $progModel->getWithFacultyDetails();

        $this->render('admin/programmes', [
            'title' => 'Manage Academic Programmes',
            'faculties' => $faculties,
            'programmes' => $programmes
        ], 'main');
    }

    public function createProgramme(Request $request, Response $response): void {
        $session = new Session();
        $progModel = new Programme();
        $logModel = new AuditLog();

        $facId = (int)$request->input('faculty_id');
        $name = $request->input('name');
        $code = strtoupper(trim($request->input('code')));
        $desc = $request->input('description');

        if (empty($name) || empty($code) || $facId <= 0) {
            $session->setFlash('error', 'Name, code and faculty are required.');
            $this->redirect('/admin/programmes');
            return;
        }

        if ($progModel->findBy('code', $code)) {
            $session->setFlash('error', 'A programme with code ' . $code . ' already exists.');
            $this->redirect('/admin/programmes');
            return;
        }

        $progId = $progModel->create([
            'faculty_id' => $facId,
            'name' => $name,
            'code' => $code,
            'description' => $desc
        ]);

        if ($progId > 0) {
            $logModel->log($session->get('user_id'), 'Create Programme', "Created programme: {$code} - {$name}");
            $session->setFlash('success', 'Programme created successfully.');
        } else {
            $session->setFlash('error', 'Failed to create programme.');
        }
        $this->redirect('/admin/programmes');
    }
    public function editProgramme(Request $request, Response $response, array $params): void {
        $session = new Session();
        $progModel = new Programme();
        $logModel = new AuditLog();
        $id = (int)($params['id'] ?? 0);

        $facId = (int)$request->input('faculty_id');
        $name = $request->input('name');
        $code = strtoupper(trim($request->input('code')));
        $desc = $request->input('description');

        if (empty($name) || empty($code) || $facId <= 0) {
            $session->setFlash('error', 'Name, code and faculty are required.');
            $this->redirect('/admin/programmes');
            return;
        }

        $current = $progModel->find($id);
        if ($current) {
            if ($current['code'] !== $code && $progModel->findBy('code', $code)) {
                $session->setFlash('error', 'A programme with code ' . $code . ' already exists.');
                $this->redirect('/admin/programmes');
                return;
            }
            if ($current['name'] !== $name && $progModel->findBy('name', $name)) {
                $session->setFlash('error', 'A programme with name ' . $name . ' already exists.');
                $this->redirect('/admin/programmes');
                return;
            }
        }

        if ($progModel->update($id, [
            'faculty_id' => $facId,
            'name' => $name,
            'code' => $code,
            'description' => $desc
        ])) {
            $logModel->log($session->get('user_id'), 'Edit Programme', "Edited programme ID: {$id} to {$code} - {$name}");
            $session->setFlash('success', 'Programme updated successfully.');
        } else {
            $session->setFlash('error', 'Failed to update programme.');
        }
        $this->redirect('/admin/programmes');
    }

    public function deleteProgramme(Request $request, Response $response, array $params): void {
        $session = new Session();
        $progModel = new Programme();
        $logModel = new AuditLog();
        $id = (int)($params['id'] ?? 0);

        $prog = $progModel->find($id);
        if ($prog && $progModel->delete($id)) {
            $logModel->log($session->get('user_id'), 'Delete Programme', "Deleted programme: " . $prog['code']);
            $session->setFlash('success', 'Programme deleted successfully.');
        } else {
            $session->setFlash('error', 'Failed to delete programme.');
        }
        $this->redirect('/admin/programmes');
    }

    // ==========================================
    // COURSE CRUD
    // ==========================================

    public function courses(Request $request, Response $response): void {
        $courseModel = new Course();
        $progModel = new Programme();
        $userModel = new User();

        $courses = $courseModel->getWithDetails();
        $programmes = $progModel->all();
        
        $years = $userModel->query("SELECT * FROM years");
        $semesters = $userModel->query("SELECT * FROM semesters");

        $this->render('admin/courses', [
            'title' => 'Manage University Courses',
            'courses' => $courses,
            'programmes' => $programmes,
            'years' => $years,
            'semesters' => $semesters
        ], 'main');
    }

    public function createCourse(Request $request, Response $response): void {
        $session = new Session();
        $courseModel = new Course();
        $logModel = new AuditLog();

        $progId = (int)$request->input('programme_id');
        $yearId = (int)$request->input('year_id');
        $semId = (int)$request->input('semester_id');
        
        $name = $request->input('name');
        $code = strtoupper(trim($request->input('code')));
        $lecturer = $request->input('lecturer');
        $desc = $request->input('description');

        if (empty($name) || empty($code) || empty($lecturer) || $progId <= 0 || $yearId <= 0 || $semId <= 0) {
            $session->setFlash('error', 'All fields are required.');
            $this->redirect('/admin/courses');
            return;
        }

        if ($courseModel->findBy('code', $code)) {
            $session->setFlash('error', 'A course with code ' . $code . ' already exists.');
            $this->redirect('/admin/courses');
            return;
        }

        $courseId = $courseModel->create([
            'programme_id' => $progId,
            'year_id' => $yearId,
            'semester_id' => $semId,
            'name' => $name,
            'code' => $code,
            'lecturer' => $lecturer,
            'description' => $desc
        ]);

        if ($courseId > 0) {
            $logModel->log($session->get('user_id'), 'Create Course', "Created course: {$code} - {$name}");
            $session->setFlash('success', 'Course created successfully.');
        } else {
            $session->setFlash('error', 'Failed to create course.');
        }
        $this->redirect('/admin/courses');
    }
    public function editCourse(Request $request, Response $response, array $params): void {
        $session = new Session();
        $courseModel = new Course();
        $logModel = new AuditLog();
        $id = (int)($params['id'] ?? 0);

        $progId = (int)$request->input('programme_id');
        $yearId = (int)$request->input('year_id');
        $semId = (int)$request->input('semester_id');
        
        $name = $request->input('name');
        $code = strtoupper(trim($request->input('code')));
        $lecturer = $request->input('lecturer');
        $desc = $request->input('description');

        if (empty($name) || empty($code) || empty($lecturer) || $progId <= 0 || $yearId <= 0 || $semId <= 0) {
            $session->setFlash('error', 'All fields are required.');
            $this->redirect('/admin/courses');
            return;
        }

        $current = $courseModel->find($id);
        if ($current && $current['code'] !== $code) {
            if ($courseModel->findBy('code', $code)) {
                $session->setFlash('error', 'A course with code ' . $code . ' already exists.');
                $this->redirect('/admin/courses');
                return;
            }
        }

        if ($courseModel->update($id, [
            'programme_id' => $progId,
            'year_id' => $yearId,
            'semester_id' => $semId,
            'name' => $name,
            'code' => $code,
            'lecturer' => $lecturer,
            'description' => $desc
        ])) {
            $logModel->log($session->get('user_id'), 'Edit Course', "Edited course ID: {$id} to {$code} - {$name}");
            $session->setFlash('success', 'Course updated successfully.');
        } else {
            $session->setFlash('error', 'Failed to update course.');
        }
        $this->redirect('/admin/courses');
    }

    public function deleteCourse(Request $request, Response $response, array $params): void {
        $session = new Session();
        $courseModel = new Course();
        $logModel = new AuditLog();
        $id = (int)($params['id'] ?? 0);

        $course = $courseModel->find($id);
        if ($course && $courseModel->delete($id)) {
            $logModel->log($session->get('user_id'), 'Delete Course', "Deleted course: " . $course['code']);
            $session->setFlash('success', 'Course deleted successfully.');
        } else {
            $session->setFlash('error', 'Failed to delete course.');
        }
        $this->redirect('/admin/courses');
    }

    // ==========================================
    // MATERIALS
    // ==========================================

    public function materials(Request $request, Response $response): void {
        $courseModel = new Course();
        $materialModel = new Material();

        $courses = $courseModel->getWithDetails();
        
        // Fetch all materials
        $materials = $materialModel->query(
            "SELECT m.*, c.name AS course_name, c.code AS course_code, u.name AS uploader_name
             FROM materials m
             JOIN courses c ON m.course_id = c.id
             JOIN users u ON m.uploader_id = u.id
             ORDER BY m.created_at DESC"
        );

        $this->render('admin/materials', [
            'title' => 'Manage Academic Materials Catalog',
            'courses' => $courses,
            'materials' => $materials
        ], 'main');
    }

    // ==========================================
    // USERS & PERMISSIONS
    // ==========================================

    public function users(Request $request, Response $response): void {
        $userModel = new User();
        $progModel = new Programme();

        $users = $userModel->query(
            "SELECT u.*, p.code AS programme_code 
             FROM users u 
             LEFT JOIN programmes p ON u.programme_id = p.id
             ORDER BY u.role ASC, u.name ASC"
        );
        $programmes = $progModel->all();

        // Fetch student permissions
        $permissions = $userModel->query(
            "SELECT sp.*, u.name AS student_name, u.email AS student_email, p.name AS programme_name, p.code AS programme_code, admin.name AS admin_name
             FROM student_permissions sp
             JOIN users u ON sp.user_id = u.id
             JOIN programmes p ON sp.programme_id = p.id
             JOIN users admin ON sp.granted_by = admin.id
             ORDER BY sp.created_at DESC"
        );

        $this->render('admin/users', [
            'title' => 'User Directory & Permissions Control',
            'users' => $users,
            'programmes' => $programmes,
            'permissions' => $permissions
        ], 'main');
    }

    public function updateUserRole(Request $request, Response $response, array $params): void {
        $session = new Session();
        $userModel = new User();
        $logModel = new AuditLog();
        
        $id = (int)($params['id'] ?? 0);
        $role = $request->input('role');
        $status = $request->input('status');

        if (!in_array($role, ['admin', 'student', 'guest']) || !in_array($status, ['active', 'inactive'])) {
            $session->setFlash('error', 'Invalid role or status parameters.');
            $this->redirect('/admin/users');
            return;
        }

        $user = $userModel->find($id);
        if ($user) {
            // Prevent locking out last admin
            if ($user['role'] === 'admin' && $role !== 'admin') {
                $admins = $userModel->query("SELECT id FROM users WHERE role = 'admin' AND status = 'active'");
                if (count($admins) <= 1) {
                    $session->setFlash('error', 'Access Alert: Cannot downgrade the only active administrator.');
                    $this->redirect('/admin/users');
                    return;
                }
            }

            if ($userModel->update($id, ['role' => $role, 'status' => $status])) {
                $logModel->log($session->get('user_id'), 'Update User Status', "Modified user {$user['email']}: Role: {$role}, Status: {$status}");
                $session->setFlash('success', 'User updated successfully.');
            } else {
                $session->setFlash('error', 'Failed to update user.');
            }
        }
        $this->redirect('/admin/users');
    }

    public function grantPermission(Request $request, Response $response): void {
        $session = new Session();
        $userModel = new User();
        $logModel = new AuditLog();

        $userId = (int)$request->input('user_id');
        $progId = (int)$request->input('programme_id');

        if ($userId <= 0 || $progId <= 0) {
            $session->setFlash('error', 'Select both user and programme.');
            $this->redirect('/admin/users');
            return;
        }

        $user = $userModel->find($userId);
        if (!$user || $user['role'] !== 'student') {
            $session->setFlash('error', 'Permissions can only be granted to students.');
            $this->redirect('/admin/users');
            return;
        }

        // Avoid granting access to their own programme
        if ((int)$user['programme_id'] === $progId) {
            $session->setFlash('error', 'Student already has access to their enrolled programme.');
            $this->redirect('/admin/users');
            return;
        }

        try {
            $userModel->query(
                "INSERT INTO student_permissions (user_id, programme_id, granted_by) VALUES (:uid, :pid, :gid)",
                [
                    'uid' => $userId,
                    'pid' => $progId,
                    'gid' => $session->get('user_id')
                ]
            );
            $logModel->log($session->get('user_id'), 'Grant Permission', "Granted student ID: {$userId} access to programme ID: {$progId}");
            $session->setFlash('success', 'Permission granted successfully.');
        } catch (\Exception $e) {
            $session->setFlash('error', 'Permission already exists or grant failed.');
        }
        
        $this->redirect('/admin/users');
    }

    public function revokePermission(Request $request, Response $response, array $params): void {
        $session = new Session();
        $userModel = new User();
        $logModel = new AuditLog();
        $id = (int)($params['id'] ?? 0);

        try {
            $userModel->query("DELETE FROM student_permissions WHERE id = :id", ['id' => $id]);
            $logModel->log($session->get('user_id'), 'Revoke Permission', "Revoked permission ID: {$id}");
            $session->setFlash('success', 'Permission revoked successfully.');
        } catch (\Exception $e) {
            $session->setFlash('error', 'Failed to revoke permission.');
        }
        $this->redirect('/admin/users');
    }

    // ==========================================
    // SUBSCRIPTIONS
    // ==========================================

    public function subscriptions(Request $request, Response $response): void {
        $subModel = new Subscription();
        $subscriptions = $subModel->getActiveSubscriptions();

        $this->render('admin/subscriptions', [
            'title' => 'Financial Subscriptions History',
            'subscriptions' => $subscriptions
        ], 'main');
    }

    // ==========================================
    // DATABASE BACKUPS
    // ==========================================

    public function backupDatabase(Request $request, Response $response): void {
        $session = new Session();
        $logModel = new AuditLog();

        try {
            $sqlText = Backup::generateBackup();
            
            // Log action in audit logs
            $logModel->log($session->get('user_id'), 'Database Backup', 'Database SQL backup exported.');

            // Download instructions
            $filename = 'smarthub_backup_' . date('Y-m-d_H-i-s') . '.sql';
            
            header('Content-Type: application/octet-stream');
            header("Content-Transfer-Encoding: Binary");
            header("Content-disposition: attachment; filename=\"" . $filename . "\"");
            
            echo $sqlText;
            exit;
        } catch (\Exception $e) {
            $session->setFlash('error', 'Database backup failed: ' . $e->getMessage());
            $this->redirect('/dashboard');
        }
    }
}
