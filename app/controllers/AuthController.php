<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Models\User;
use App\Models\Programme;
use App\Models\AuditLog;
use App\Helpers\Validation;

class AuthController extends Controller {

    /**
     * Show Login Page
     */
    public function showLogin(Request $request, Response $response): void {
        $this->render('auth/login', ['title' => 'Login | SmartHUB'], 'auth');
    }

    /**
     * Authenticate User
     */
    public function login(Request $request, Response $response): void {
        $session = new Session();
        $userModel = new User();
        $logModel = new AuditLog();

        $email = $request->input('email');
        $password = $request->input('password');

        $user = $userModel->findByEmail($email);

        if ($user && password_verify($password, $user['password_hash'])) {
            if ($user['status'] === 'inactive') {
                $session->setFlash('error', 'Your account has been deactivated. Contact administration.');
                $this->redirect('/login');
            }

            // Regenerate session ID to prevent session fixation
            $session->regenerate();

            // Set session variables
            $session->set('user_id', $user['id']);
            $session->set('user_name', $user['name']);
            $session->set('user_email', $user['email']);
            $session->set('user_role', $user['role']);
            $session->set('last_activity', time());

            // Log activity
            $logModel->log($user['id'], 'User Login', 'User successfully logged in.');

            $session->setFlash('success', 'Welcome back, ' . $user['name'] . '!');
            $this->redirect('/dashboard');
        } else {
            $session->setFlash('error', 'Invalid email or password.');
            $this->render('auth/login', [
                'title' => 'Login | SmartHUB',
                'email' => $email
            ], 'auth');
        }
    }

    /**
     * Show Registration Page
     */
    public function showRegister(Request $request, Response $response): void {
        $progModel = new Programme();
        $programmes = $progModel->getWithFacultyDetails();

        $this->render('auth/register', [
            'title' => 'Register | SmartHUB',
            'programmes' => $programmes
        ], 'auth');
    }

    /**
     * Register User
     */
    public function register(Request $request, Response $response): void {
        $session = new Session();
        $userModel = new User();
        $progModel = new Programme();
        $logModel = new AuditLog();
        $validator = new Validation();

        $data = $request->getBody();
        $role = $data['role'] ?? 'guest';

        $rules = [
            'name' => ['required' => true, 'min' => 3, 'max' => 100],
            'email' => ['required' => true, 'email' => true, 'unique' => [$userModel, 'email']],
            'phone' => ['required' => true, 'min' => 10, 'max' => 15],
            'password' => ['required' => true, 'min' => 6],
            'confirm_password' => ['required' => true, 'matches' => 'password']
        ];

        if ($role === 'student') {
            $rules['programme_id'] = ['required' => true];
        }

        if (!$validator->validate($data, $rules)) {
            $programmes = $progModel->getWithFacultyDetails();
            $this->render('auth/register', [
                'title' => 'Register | SmartHUB',
                'errors' => $validator->getErrors(),
                'programmes' => $programmes,
                'old' => $data
            ], 'auth');
            return;
        }

        // Save User
        $userData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
            'role' => $role,
            'status' => 'active',
            'programme_id' => ($role === 'student') ? (int)$data['programme_id'] : null,
            'subscription_plan_id' => ($role === 'student') ? 1 : null // Default to Free Student plan for students
        ];

        $userId = $userModel->create($userData);

        if ($userId > 0) {
            $logModel->log($userId, 'User Registration', "New account created as " . ucfirst($role));
            $session->setFlash('success', 'Registration successful! You can now login.');
            $this->redirect('/login');
        } else {
            $session->setFlash('error', 'Something went wrong during registration.');
            $this->redirect('/register');
        }
    }

    /**
     * Show & Edit Profile
     */
    public function profile(Request $request, Response $response): void {
        $session = new Session();
        $userId = $session->get('user_id');

        $userModel = new User();
        $user = $userModel->findWithDetails($userId);
        $user['phone'] = $userModel->getPhone($user);

        $this->render('auth/profile', [
            'title' => 'My Profile',
            'user' => $user
        ], 'main');
    }

    /**
     * Update Profile Info
     */
    public function updateProfile(Request $request, Response $response): void {
        $session = new Session();
        $userId = $session->get('user_id');

        $userModel = new User();
        $logModel = new AuditLog();
        $validator = new Validation();

        $data = $request->getBody();

        // Custom validation ignoring current email
        $rules = [
            'name' => ['required' => true, 'min' => 3, 'max' => 100],
            'phone' => ['required' => true, 'min' => 10, 'max' => 15],
            'email' => ['required' => true, 'email' => true]
        ];

        // Check if email changed and is unique
        $currentUser = $userModel->find($userId);
        if ($data['email'] !== $currentUser['email']) {
            $rules['email']['unique'] = [$userModel, 'email'];
        }

        if (!$validator->validate($data, $rules)) {
            $user = $userModel->findWithDetails($userId);
            $user['phone'] = $userModel->getPhone($user); // keep decrypted phone
            
            $session->setFlash('error', 'Profile update failed. Check validation errors.');
            $this->render('auth/profile', [
                'title' => 'My Profile',
                'user' => $user,
                'errors' => $validator->getErrors()
            ], 'main');
            return;
        }

        $updateData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] // gets encrypted inside User model
        ];

        if ($userModel->update($userId, $updateData)) {
            $session->set('user_name', $data['name']);
            $session->set('user_email', $data['email']);
            
            $logModel->log($userId, 'Profile Update', 'User updated profile information.');
            $session->setFlash('success', 'Profile updated successfully.');
        } else {
            $session->setFlash('error', 'Failed to update profile.');
        }

        $this->redirect('/profile');
    }

    /**
     * Change Password
     */
    public function changePassword(Request $request, Response $response): void {
        $session = new Session();
        $userId = $session->get('user_id');

        $userModel = new User();
        $logModel = new AuditLog();
        $validator = new Validation();

        $data = $request->getBody();

        $rules = [
            'old_password' => ['required' => true],
            'new_password' => ['required' => true, 'min' => 6],
            'confirm_password' => ['required' => true, 'matches' => 'new_password']
        ];

        $user = $userModel->find($userId);

        if (!$validator->validate($data, $rules)) {
            $userWithDetails = $userModel->findWithDetails($userId);
            $userWithDetails['phone'] = $userModel->getPhone($userWithDetails);
            
            $this->render('auth/profile', [
                'title' => 'My Profile',
                'user' => $userWithDetails,
                'password_errors' => $validator->getErrors()
            ], 'main');
            return;
        }

        if (!password_verify($data['old_password'], $user['password_hash'])) {
            $session->setFlash('error', 'Incorrect current password.');
            $this->redirect('/profile');
            return;
        }

        $updateData = [
            'password_hash' => password_hash($data['new_password'], PASSWORD_DEFAULT)
        ];

        if ($userModel->update($userId, $updateData)) {
            $logModel->log($userId, 'Password Change', 'User successfully changed password.');
            $session->setFlash('success', 'Password updated successfully.');
        } else {
            $session->setFlash('error', 'Failed to update password.');
        }

        $this->redirect('/profile');
    }

    /**
     * Destroy Session
     */
    public function logout(Request $request, Response $response): void {
        $session = new Session();
        $userId = $session->get('user_id');
        $logModel = new AuditLog();

        if ($userId) {
            $logModel->log($userId, 'User Logout', 'User logged out.');
        }

        $session->destroy();
        
        // Re-instantiate session to send a flash message
        $newSession = new Session();
        $newSession->setFlash('success', 'You have been successfully logged out.');
        $response->redirect(URL_ROOT . '/login');
    }
}
