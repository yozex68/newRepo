<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Models\Question;
use App\Models\Reply;
use App\Models\AuditLog;
use App\Helpers\Validation;

class QuestionController extends Controller {

    /**
     * List all questions (Students see their own, Admin sees all)
     */
    public function index(Request $request, Response $response): void {
        $session = new Session();
        $userId = $session->get('user_id');
        $role = $session->get('user_role');

        $questionModel = new Question();

        if ($role === 'admin') {
            $questions = $questionModel->getWithUserDetails(0); // All questions
        } else {
            $questions = $questionModel->getWithUserDetails($userId); // Only student's questions
        }

        $this->render('questions/index', [
            'title' => 'Q&A Help Desk Board',
            'questions' => $questions
        ], 'main');
    }

    /**
     * Create new Help Desk Ticket (Student only)
     */
    public function create(Request $request, Response $response): void {
        $session = new Session();
        $userId = $session->get('user_id');
        $role = $session->get('user_role');

        if ($role === 'admin') {
            $session->setFlash('error', 'Administrators cannot create Q&A tickets.');
            $this->redirect('/questions');
            return;
        }

        $validator = new Validation();
        $qModel = new Question();
        $logModel = new AuditLog();

        $data = $request->getBody();

        $rules = [
            'title' => ['required' => true, 'min' => 5, 'max' => 200],
            'content' => ['required' => true, 'min' => 10]
        ];

        if (!$validator->validate($data, $rules)) {
            $session->setFlash('error', 'Ticket creation failed. Double check fields.');
            $questions = $qModel->getWithUserDetails($userId);
            $this->render('questions/index', [
                'title' => 'Q&A Help Desk Board',
                'questions' => $questions,
                'errors' => $validator->getErrors(),
                'old' => $data
            ], 'main');
            return;
        }

        $qId = $qModel->create([
            'user_id' => $userId,
            'title' => $data['title'],
            'content' => $data['content'],
            'status' => 'pending'
        ]);

        if ($qId > 0) {
            $logModel->log($userId, 'Create Question', "Opened help desk ticket ID: {$qId}");
            $session->setFlash('success', 'Ticket successfully created and queued for review.');
        } else {
            $session->setFlash('error', 'Failed to create help ticket.');
        }

        $this->redirect('/questions');
    }

    /**
     * View Question thread and replies
     */
    public function view(Request $request, Response $response, array $params): void {
        $session = new Session();
        $userId = $session->get('user_id');
        $role = $session->get('user_role');
        $qId = (int)($params['id'] ?? 0);

        $qModel = new Question();
        $replyModel = new Reply();

        $question = $qModel->findWithUserDetails($qId);
        if (!$question) {
            $session->setFlash('error', 'Ticket thread not found.');
            $this->redirect('/questions');
            return;
        }

        // Student boundaries: Students can ONLY view their own tickets
        if ($role !== 'admin' && (int)$question['user_id'] !== $userId) {
            $session->setFlash('error', 'Access Alert: You are not authorized to view this ticket.');
            $this->redirect('/questions');
            return;
        }

        $replies = $replyModel->getRepliesByQuestion($qId);

        $this->render('questions/view', [
            'title' => 'Ticket #' . $qId . ' | ' . $question['title'],
            'question' => $question,
            'replies' => $replies
        ], 'main');
    }

    /**
     * Post a response reply on ticket thread
     */
    public function reply(Request $request, Response $response, array $params): void {
        $session = new Session();
        $userId = $session->get('user_id');
        $role = $session->get('user_role');
        $qId = (int)($params['id'] ?? 0);

        $qModel = new Question();
        $replyModel = new Reply();
        $logModel = new AuditLog();

        $question = $qModel->find($qId);
        if (!$question) {
            $session->setFlash('error', 'Ticket thread not found.');
            $this->redirect('/questions');
            return;
        }

        // Student boundary checks
        if ($role !== 'admin' && (int)$question['user_id'] !== $userId) {
            $session->setFlash('error', 'Unauthorized operation.');
            $this->redirect('/questions');
            return;
        }

        $content = $request->input('content');
        if (empty(trim($content))) {
            $session->setFlash('error', 'Reply response content cannot be empty.');
            $this->redirect('/questions/' . $qId);
            return;
        }

        $replyId = $replyModel->create([
            'question_id' => $qId,
            'user_id' => $userId,
            'content' => $content
        ]);

        if ($replyId > 0) {
            // Update ticket status
            // If admin replies, change ticket status to 'replied'
            // If student replies, change status to 'pending'
            $status = ($role === 'admin') ? 'replied' : 'pending';
            $qModel->update($qId, ['status' => $status]);

            $logModel->log($userId, 'Reply Question', "Replied on ticket ID: {$qId}");
            $session->setFlash('success', 'Response reply posted successfully.');
        } else {
            $session->setFlash('error', 'Failed to submit response.');
        }

        $this->redirect('/questions/' . $qId);
    }
}
