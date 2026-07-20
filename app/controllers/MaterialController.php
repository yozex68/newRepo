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
use App\Models\Bookmark;
use App\Models\AuditLog;
use App\Helpers\Validation;
use App\Helpers\Helper;

class MaterialController extends Controller {

    /**
     * Upload File (Admin Only)
     */
    public function upload(Request $request, Response $response): void {
        $session = new Session();
        $validator = new Validation();
        $materialModel = new Material();
        $logModel = new AuditLog();

        $courseId = (int)$request->input('course_id');
        $title = $request->input('title');
        $desc = $request->input('description');
        $materialType = $request->input('material_type');
        
        $files = $request->getFiles();
        $file = $files['material_file'] ?? null;

        if (empty($title) || empty($materialType) || $courseId <= 0 || !$file || $file['error'] !== UPLOAD_ERR_OK) {
            $session->setFlash('error', 'All fields and a valid file are required.');
            $this->redirect('/admin/materials');
            return;
        }

        // Validate File Size & Type
        if (!$validator->validateFile($file, ALLOWED_EXTENSIONS, ALLOWED_MIME_TYPES, MAX_FILE_SIZE)) {
            $errors = $validator->getErrors();
            $session->setFlash('error', implode(' ', $errors));
            $this->redirect('/admin/materials');
            return;
        }

        // MD5 Hash check to prevent duplicate uploads
        $fileHash = md5_file($file['tmp_name']);
        $duplicate = $materialModel->checkDuplicateHash($fileHash);
        if ($duplicate) {
            $session->setFlash('error', 'Upload Rejected: Duplicate file detected. This resource is already cataloged as: "' . $duplicate['title'] . '".');
            $this->redirect('/admin/materials');
            return;
        }

        // Make sure uploads directory exists
        if (!is_dir(UPLOAD_DIR)) {
            mkdir(UPLOAD_DIR, 0755, true);
        }

        // Save File securely
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $safeName = bin2hex(random_bytes(16)) . '.' . $ext;
        $destPath = UPLOAD_DIR . '/' . $safeName;

        if (move_uploaded_file($file['tmp_name'], $destPath)) {
            // Write database record
            $materialId = $materialModel->create([
                'course_id' => $courseId,
                'title' => $title,
                'description' => $desc,
                'material_type' => $materialType,
                'file_path' => $safeName,
                'file_size' => $file['size'],
                'file_hash' => $fileHash,
                'uploader_id' => $session->get('user_id'),
                'downloads_count' => 0
            ]);

            if ($materialId > 0) {
                $logModel->log($session->get('user_id'), 'Upload Material', "Uploaded material: {$title} for course ID: {$courseId}");
                $session->setFlash('success', 'Material uploaded and indexed successfully.');
            } else {
                @unlink($destPath); // Clean up file
                $session->setFlash('error', 'Failed to save material index to database.');
            }
        } else {
            $session->setFlash('error', 'Failed to move uploaded file to target storage.');
        }

        $this->redirect('/admin/materials');
    }

    /**
     * Download File (Enforcing RBAC and Quota constraints)
     */
    public function download(Request $request, Response $response, array $params): void {
        $session = new Session();
        $userId = $session->get('user_id');
        $materialId = (int)($params['id'] ?? 0);

        $materialModel = new Material();
        $userModel = new User();
        $downloadModel = new Download();
        $logModel = new AuditLog();

        $material = $materialModel->findWithDetails($materialId);
        if (!$material) {
            $session->setFlash('error', 'Academic material not found.');
            $this->redirect('/dashboard');
            return;
        }

        // 1. Boundary access check: User must be admin OR belong to course's programme OR have admin override
        if (!$userModel->hasAccessToProgramme($userId, $material['programme_id'])) {
            $session->setFlash('error', 'Security Alert: You are not authorized to download files from ' . $material['programme_name'] . '. Please upgrade your plan.');
            $this->redirect('/subscribe');
            return;
        }

        // 2. Guest download restriction: Guests are blocked from downloading files
        if ($session->get('user_role') === 'guest') {
            $session->setFlash('warning', 'Access Restricted: Guest accounts cannot download academic materials. Please register or select a subscription.');
            $this->redirect('/subscribe');
            return;
        }

        // 3. Check Subscription Limits / Daily quotas for students
        if ($session->get('user_role') === 'student') {
            $student = $userModel->findWithDetails($userId);
            // Default max downloads comes from plan
            // Basic Student has 5 max_downloads, Elite has 0 (unlimited)
            $sqlPlan = "SELECT max_downloads FROM subscription_plans WHERE id = :pid";
            $plan = $userModel->queryRow($sqlPlan, ['pid' => $student['subscription_plan_id']]);
            
            $maxLimit = $plan ? (int)$plan['max_downloads'] : 5;

            if ($maxLimit > 0) {
                // Count downloads today
                $sqlToday = "SELECT COUNT(*) AS count FROM downloads 
                             WHERE user_id = :uid AND DATE(downloaded_at) = CURDATE()";
                $resToday = $downloadModel->queryRow($sqlToday, ['uid' => $userId]);
                $todayCount = $resToday ? (int)$resToday['count'] : 0;

                if ($todayCount >= $maxLimit) {
                    $session->setFlash('warning', "Download quota exceeded! You have reached your limit of {$maxLimit} downloads per day on the Basic Plan. Upgrade to Premium for higher allowances.");
                    $this->redirect('/subscribe');
                    return;
                }
            }
        }

        // File path lookup
        $filePath = UPLOAD_DIR . '/' . $material['file_path'];
        if (!file_exists($filePath)) {
            $session->setFlash('error', 'Physical file was not found on server storage. Contact support.');
            $this->redirect('/dashboard');
            return;
        }

        // 4. Log Download & Increment counts
        $downloadModel->log(
            $userId, 
            $materialId, 
            $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1', 
            $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
        );
        $materialModel->incrementDownloads($materialId);

        // Audit Log
        $logModel->log($userId, 'Download Material', "Downloaded file: " . $material['title']);

        // 5. Output File to Browser
        $cleanTitle = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $material['title']);
        $ext = pathinfo($filePath, PATHINFO_EXTENSION);
        $downloadName = $cleanTitle . '.' . $ext;

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $downloadName . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));
        
        // Clean outputs buffer before reading file to avoid corrupting binaries
        if (ob_get_level()) {
            ob_end_clean();
        }
        
        readfile($filePath);
        exit;
    }

    /**
     * Bookmark or Unbookmark Resource
     */
    public function bookmark(Request $request, Response $response, array $params): void {
        $session = new Session();
        $userId = $session->get('user_id');
        $materialId = (int)($params['id'] ?? 0);

        $bookmarkModel = new Bookmark();
        $materialModel = new Material();

        $material = $materialModel->find($materialId);
        if (!$material) {
            $session->setFlash('error', 'Material not found.');
            $this->redirect('/dashboard');
            return;
        }

        if ($bookmarkModel->isBookmarked($userId, $materialId)) {
            $bookmarkModel->removeBookmark($userId, $materialId);
            $session->setFlash('success', 'Bookmark removed successfully.');
        } else {
            $bookmarkModel->create([
                'user_id' => $userId,
                'material_id' => $materialId
            ]);
            $session->setFlash('success', 'Material bookmarked successfully.');
        }

        // Redirect back to previous page
        $referer = $_SERVER['HTTP_REFERER'] ?? URL_ROOT . '/dashboard';
        $response->redirect($referer);
    }

    /**
     * Delete Material (Admin Only)
     */
    public function delete(Request $request, Response $response, array $params): void {
        $session = new Session();
        $materialModel = new Material();
        $logModel = new AuditLog();
        $id = (int)($params['id'] ?? 0);

        $mat = $materialModel->find($id);
        if ($mat) {
            // Delete physical file
            $filePath = UPLOAD_DIR . '/' . $mat['file_path'];
            if (file_exists($filePath)) {
                @unlink($filePath);
            }

            if ($materialModel->delete($id)) {
                $logModel->log($session->get('user_id'), 'Delete Material', "Deleted learning material: " . $mat['title']);
                $session->setFlash('success', 'Material successfully deleted.');
            } else {
                $session->setFlash('error', 'Failed to delete material from database.');
            }
        } else {
            $session->setFlash('error', 'Material not found.');
        }

        $this->redirect('/admin/materials');
    }
}
