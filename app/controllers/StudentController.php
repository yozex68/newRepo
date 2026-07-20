<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Models\User;
use App\Models\Course;
use App\Models\Material;
use App\Models\SubscriptionPlan;
use App\Models\Subscription;
use App\Models\AuditLog;

class StudentController extends Controller {

    /**
     * Show courses belonging to the student's programme, organized by Year & Semester.
     */
    public function courses(Request $request, Response $response): void {
        $session = new Session();
        $userId = $session->get('user_id');

        $userModel = new User();
        $student = $userModel->findWithDetails($userId);
        
        if ($session->get('user_role') === 'guest') {
            $session->setFlash('warning', 'Guest accounts cannot access materials. Please buy a subscription plan.');
            $this->redirect('/subscribe');
            return;
        }

        if (empty($student['programme_id'])) {
            $session->setFlash('warning', 'You are not enrolled in any academic programme. Please contact administrator.');
            $this->redirect('/dashboard');
            return;
        }

        // Get student's enrolled programme courses
        $courseModel = new Course();
        
        $sql = "SELECT c.*, y.name AS year_name, y.code AS year_code, s.name AS semester_name, s.code AS semester_code 
                FROM courses c
                JOIN years y ON c.year_id = y.id
                JOIN semesters s ON c.semester_id = s.id
                WHERE c.programme_id = :prog_id
                ORDER BY y.code ASC, s.code ASC, c.code ASC";
        $courses = $userModel->query($sql, ['prog_id' => $student['programme_id']]);

        // Organize courses by Year and Semester
        $curriculum = [];
        foreach ($courses as $c) {
            $year = $c['year_name'];
            $semester = $c['semester_name'];
            $curriculum[$year][$semester][] = $c;
        }

        // Fetch external programs they have permission to access
        $sqlPerm = "SELECT p.*, pr.name AS programme_name, pr.code AS programme_code
                    FROM student_permissions p
                    JOIN programmes pr ON p.programme_id = pr.id
                    WHERE p.user_id = :user_id";
        $permissions = $userModel->query($sqlPerm, ['user_id' => $userId]);
        
        // Fetch courses for those external programs
        $externalCourses = [];
        foreach ($permissions as $p) {
            $extProgId = $p['programme_id'];
            $extCourses = $userModel->query(
                "SELECT c.*, pr.name AS programme_name FROM courses c JOIN programmes pr ON c.programme_id = pr.id WHERE c.programme_id = :pid", 
                ['pid' => $extProgId]
            );
            if (!empty($extCourses)) {
                $externalCourses[$p['programme_name']] = $extCourses;
            }
        }

        $this->render('student/courses', [
            'title' => 'My Academic Courses',
            'student' => $student,
            'curriculum' => $curriculum,
            'externalCourses' => $externalCourses
        ], 'main');
    }

    /**
     * Show Course details and listing of Learning Materials
     */
    public function courseDetails(Request $request, Response $response, array $params): void {
        $session = new Session();
        $userId = $session->get('user_id');
        $courseId = (int)($params['id'] ?? 0);

        $userModel = new User();
        $courseModel = new Course();
        $materialModel = new Material();

        $course = $courseModel->findWithDetails($courseId);
        if (!$course) {
            $session->setFlash('error', 'Course not found.');
            $this->redirect('/courses');
            return;
        }

        // ENFORCE student boundary checks: must be in same programme OR has explicit admin permission
        if (!$userModel->hasAccessToProgramme($userId, $course['programme_id'])) {
            $session->setFlash('error', 'Security Alert: You are not authorized to access courses in ' . $course['programme_code'] . '. Purchase a subscription plan to unlock.');
            $this->redirect('/subscribe');
            return;
        }

        // Fetch materials grouped by type
        $materials = $materialModel->query(
            "SELECT * FROM materials WHERE course_id = :cid ORDER BY created_at DESC", 
            ['cid' => $courseId]
        );

        $groupedMaterials = [];
        foreach ($materials as $m) {
            $groupedMaterials[$m['material_type']][] = $m;
        }

        // Fetch bookmarks to display saved statuses
        $bookmarkModel = new \App\Models\Bookmark();
        $bookmarks = $bookmarkModel->query("SELECT material_id FROM bookmarks WHERE user_id = :uid", ['uid' => $userId]);
        $bookmarkedIds = array_column($bookmarks, 'material_id');

        $this->render('student/course_details', [
            'title' => $course['code'] . ' | ' . $course['name'],
            'course' => $course,
            'groupedMaterials' => $groupedMaterials,
            'bookmarkedIds' => $bookmarkedIds
        ], 'main');
    }

    /**
     * Show Subscription Plans and current active billing
     */
    public function subscribe(Request $request, Response $response): void {
        $session = new Session();
        $userId = $session->get('user_id');

        $planModel = new SubscriptionPlan();
        $plans = $planModel->all();

        $subModel = new Subscription();
        $activeSub = $subModel->findActiveByUser($userId);

        $this->render('student/subscriptions', [
            'title' => 'Premium Subscription Services',
            'plans' => $plans,
            'activeSub' => $activeSub
        ], 'main');
    }

    /**
     * Mock Checkout Gateway
     */
    public function checkout(Request $request, Response $response, array $params): void {
        $session = new Session();
        $userId = $session->get('user_id');
        $planId = (int)($params['id'] ?? 0);

        $planModel = new SubscriptionPlan();
        $subModel = new Subscription();
        $logModel = new AuditLog();

        $plan = $planModel->find($planId);
        if (!$plan) {
            $session->setFlash('error', 'Selected subscription plan does not exist.');
            $this->redirect('/subscribe');
            return;
        }

        // Perform mock billing transaction registration
        $subId = $subModel->subscribe($userId, $planId);

        if ($subId > 0) {
            $logModel->log($userId, 'Plan Subscription', "Subscribed to plan: " . $plan['name']);
            $session->setFlash('success', 'Thank you! Subscription to ' . $plan['name'] . ' activated successfully.');
        } else {
            $session->setFlash('error', 'Transaction aborted. Please check payment details.');
        }

        $this->redirect('/subscribe');
    }
}
