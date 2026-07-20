<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Models\User;
use App\Models\Course;
use App\Models\Material;
use App\Models\Download;
use App\Models\Announcement;
use App\Models\Programme;
use App\Models\AuditLog;

class HomeController extends Controller {
    
    /**
     * Public Homepage
     */
    public function index(Request $request, Response $response): void {
        $annModel = new Announcement();
        $announcements = $annModel->getWithCreatorDetails(3);
        
        $this->render('home/index', [
            'title' => 'Welcome to SmartHUB Academic Library',
            'announcements' => $announcements
        ], 'guest');
    }

    /**
     * Public Announcements Board
     */
    public function announcements(Request $request, Response $response): void {
        $annModel = new Announcement();
        $announcements = $annModel->getWithCreatorDetails(15);
        
        $this->render('home/announcements', [
            'title' => 'Announcements Board',
            'announcements' => $announcements
        ], 'guest');
    }

    /**
     * Dashboard Router
     */
    public function dashboard(Request $request, Response $response): void {
        $session = new Session();
        $role = $session->get('user_role');

        if ($role === 'admin') {
            $this->adminDashboard($request, $response);
        } else {
            $this->studentDashboard($request, $response);
        }
    }

    /**
     * Admin Dashboard Statistics
     */
    private function adminDashboard(Request $request, Response $response): void {
        $userModel = new User();
        $courseModel = new Course();
        $materialModel = new Material();
        $downloadModel = new Download();
        $progModel = new Programme();
        $logModel = new AuditLog();

        // Total Counters
        $totalStudents = count($userModel->query("SELECT id FROM users WHERE role = 'student'"));
        $totalCourses = count($courseModel->all());
        $totalMaterials = count($materialModel->all());
        $totalDownloads = count($downloadModel->all());
        $totalProgrammes = count($progModel->all());
        
        // Materials Type Breakdown
        $totalBooks = count($materialModel->query("SELECT id FROM materials WHERE material_type = 'book'"));
        $totalNotes = count($materialModel->query("SELECT id FROM materials WHERE material_type = 'note'"));
        $totalPapers = count($materialModel->query("SELECT id FROM materials WHERE material_type = 'past_paper'"));

        // Activities and logs
        $recentUploads = $materialModel->getRecentUploads(5);
        $recentUsers = $userModel->query("SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC LIMIT 5");
        $auditLogs = $logModel->getWithUserDetails(8);

        // Chart Data aggregates
        $downloadTrends = $downloadModel->getDownloadTrends(7);
        $typeBreakdown = $materialModel->countByType();

        $this->render('home/admin_dashboard', [
            'title' => 'Admin Control Panel',
            'totalStudents' => $totalStudents,
            'totalCourses' => $totalCourses,
            'totalMaterials' => $totalMaterials,
            'totalDownloads' => $totalDownloads,
            'totalProgrammes' => $totalProgrammes,
            'totalBooks' => $totalBooks,
            'totalNotes' => $totalNotes,
            'totalPapers' => $totalPapers,
            'recentUploads' => $recentUploads,
            'recentUsers' => $recentUsers,
            'auditLogs' => $auditLogs,
            'downloadTrends' => $downloadTrends,
            'typeBreakdown' => $typeBreakdown
        ], 'main');
    }

    /**
     * Student Dashboard View
     */
    private function studentDashboard(Request $request, Response $response): void {
        $session = new Session();
        $userId = $session->get('user_id');

        $userModel = new User();
        $student = $userModel->findWithDetails($userId);
        
        $annModel = new Announcement();
        $announcements = $annModel->getWithCreatorDetails(5);

        // Get Bookmark list
        $bookmarkModel = new \App\Models\Bookmark();
        $bookmarks = $bookmarkModel->getUserBookmarks($userId);

        // Get recent materials uploaded in their programme
        $recentMaterials = [];
        if (!empty($student['programme_id'])) {
            $sql = "SELECT m.*, c.name AS course_name, c.code AS course_code 
                    FROM materials m
                    JOIN courses c ON m.course_id = c.id
                    WHERE c.programme_id = :prog_id
                    ORDER BY m.created_at DESC LIMIT 5";
            $recentMaterials = $userModel->query($sql, ['prog_id' => $student['programme_id']]);
        }

        // Get list of permissions granted to study external courses
        $sql = "SELECT p.*, pr.name AS programme_name, pr.code AS programme_code
                FROM student_permissions p
                JOIN programmes pr ON p.programme_id = pr.id
                WHERE p.user_id = :user_id";
        $permissions = $userModel->query($sql, ['user_id' => $userId]);

        $this->render('home/student_dashboard', [
            'title' => 'Student Workspace',
            'student' => $student,
            'announcements' => $announcements,
            'bookmarks' => $bookmarks,
            'recentMaterials' => $recentMaterials,
            'permissions' => $permissions
        ], 'main');
    }
}
