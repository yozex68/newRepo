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
use App\Models\Subscription;
use App\Models\AuditLog;
use App\Helpers\Helper;

class ReportController extends Controller {

    /**
     * Reports Dashboard
     */
    public function index(Request $request, Response $response): void {
        $userModel = new User();
        $courseModel = new Course();
        $materialModel = new Material();
        $downloadModel = new Download();
        $subModel = new Subscription();

        // 1. Fetch Students lists
        $students = $userModel->query(
            "SELECT u.*, p.name AS programme_name, sp.name AS plan_name 
             FROM users u
             LEFT JOIN programmes p ON u.programme_id = p.id
             LEFT JOIN subscription_plans sp ON u.subscription_plan_id = sp.id
             WHERE u.role = 'student'
             ORDER BY u.created_at DESC"
        );

        // 2. Fetch Downloads Log
        $downloads = $downloadModel->getDetailedReport();

        // 3. Fetch Subscription logs
        $subscriptions = $subModel->getActiveSubscriptions();

        // 4. Fetch Most Downloaded Files
        $mostDownloaded = $downloadModel->getMostDownloaded(15);

        // 5. Fetch Material sizes and counts
        $materials = $materialModel->query(
            "SELECT m.*, c.code AS course_code, u.name AS uploader_name
             FROM materials m
             JOIN courses c ON m.course_id = c.id
             JOIN users u ON m.uploader_id = u.id
             ORDER BY m.downloads_count DESC"
        );

        $this->render('reports/index', [
            'title' => 'Reports & System Analytics',
            'students' => $students,
            'downloads' => $downloads,
            'subscriptions' => $subscriptions,
            'mostDownloaded' => $mostDownloaded,
            'materials' => $materials
        ], 'main');
    }

    /**
     * Export Reports to PDF or Excel
     */
    public function export(Request $request, Response $response): void {
        $session = new Session();
        $format = $request->input('format'); // excel | pdf
        $reportType = $request->input('report'); // downloads | students | courses | materials | subscriptions

        $userModel = new User();
        $courseModel = new Course();
        $materialModel = new Material();
        $downloadModel = new Download();
        $subModel = new Subscription();

        $data = [];
        $headers = [];
        $title = '';

        switch ($reportType) {
            case 'downloads':
                $title = 'Downloads Activity Log';
                $headers = ['Date/Time', 'Student Name', 'Email', 'File Name', 'Format', 'Course Code', 'IP Address'];
                $rows = $downloadModel->getDetailedReport();
                foreach ($rows as $r) {
                    $data[] = [
                        $r['downloaded_at'],
                        $r['user_name'],
                        $r['user_email'],
                        $r['file_title'],
                        strtoupper($r['material_type']),
                        $r['course_code'],
                        $r['ip_address']
                    ];
                }
                break;

            case 'students':
                $title = 'Enrolled Students Report';
                $headers = ['Student Name', 'Email', 'Role/Status', 'Programme Enrolled', 'Active Plan', 'Enrolled At'];
                $rows = $userModel->query(
                    "SELECT u.*, p.code AS programme_code, sp.name AS plan_name 
                     FROM users u
                     LEFT JOIN programmes p ON u.programme_id = p.id
                     LEFT JOIN subscription_plans sp ON u.subscription_plan_id = sp.id
                     WHERE u.role = 'student'
                     ORDER BY u.name ASC"
                );
                foreach ($rows as $r) {
                    $data[] = [
                        $r['name'],
                        $r['email'],
                        ucfirst($r['status']),
                        $r['programme_code'] ?? 'None',
                        $r['plan_name'] ?? 'None',
                        $r['created_at']
                    ];
                }
                break;

            case 'courses':
                $title = 'Academic Courses Roster';
                $headers = ['Course Code', 'Course Title', 'Lecturer', 'Programme', 'Materials Count'];
                $rows = $courseModel->query(
                    "SELECT c.*, p.code AS programme_code, 
                            (SELECT COUNT(*) FROM materials m WHERE m.course_id = c.id) AS materials_count
                     FROM courses c
                     JOIN programmes p ON c.programme_id = p.id
                     ORDER BY c.code ASC"
                );
                foreach ($rows as $r) {
                    $data[] = [
                        $r['code'],
                        $r['name'],
                        $r['lecturer'],
                        $r['programme_code'],
                        $r['materials_count']
                    ];
                }
                break;

            case 'materials':
                $title = 'Learning Materials Catalog';
                $headers = ['Material Title', 'Type', 'Course Code', 'File Size', 'Downloads Count', 'Uploader'];
                $rows = $materialModel->query(
                    "SELECT m.*, c.code AS course_code, u.name AS uploader_name
                     FROM materials m
                     JOIN courses c ON m.course_id = c.id
                     JOIN users u ON m.uploader_id = u.id
                     ORDER BY m.title ASC"
                );
                foreach ($rows as $r) {
                    $data[] = [
                        $r['title'],
                        strtoupper($r['material_type']),
                        $r['course_code'],
                        Helper::formatBytes($r['file_size']),
                        $r['downloads_count'],
                        $r['uploader_name']
                    ];
                }
                break;

            case 'subscriptions':
                $title = 'Premium Subscriptions Billings';
                $headers = ['User Name', 'Email', 'Plan Tier', 'Price', 'Billing Status', 'Starts At', 'Expires At'];
                $rows = $subModel->getActiveSubscriptions();
                foreach ($rows as $r) {
                    $data[] = [
                        $r['user_name'],
                        $r['user_email'],
                        $r['plan_name'],
                        '$' . number_format($r['plan_price'], 2),
                        strtoupper($r['status']),
                        $r['starts_at'],
                        $r['expires_at']
                    ];
                }
                break;

            default:
                $session->setFlash('error', 'Invalid report export requested.');
                $this->redirect('/admin/reports');
                return;
        }

        // Output formatting
        if ($format === 'excel') {
            // Excel Export (clean XLS format table)
            header('Content-Type: application/vnd.ms-excel; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . strtolower($reportType) . '_report_' . date('Ymd_His') . '.xls"');
            
            echo "<table border='1'>";
            echo "<tr><th colspan='" . count($headers) . "' style='font-size: 16px; font-weight: bold; background-color: #f1f5f9; padding: 10px;'>{$title} - Generated: " . date('Y-m-d H:i:s') . "</th></tr>";
            echo "<tr>";
            foreach ($headers as $h) {
                echo "<th style='background-color: #1e3a8a; color: #ffffff; padding: 5px;'>{$h}</th>";
            }
            echo "</tr>";
            foreach ($data as $row) {
                echo "<tr>";
                foreach ($row as $val) {
                    echo "<td style='padding: 5px;'>{$val}</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
            exit;
        } elseif ($format === 'pdf') {
            // Printer-friendly PDF layout (using browser printer view)
            $this->render('reports/print_layout', [
                'title' => $title,
                'headers' => $headers,
                'data' => $data
            ], 'raw');
            exit;
        }

        $this->redirect('/admin/reports');
    }
}
