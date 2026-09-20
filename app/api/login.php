<?php
require_once '../config/config.php';
require_once '../lib/Auth.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? $_POST['action'] ?? '';

try {
    if ($action === 'login') {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        if (Auth::login($username, $password)) {
            echo json_encode([
                'success' => true,
                'message' => 'Login successful',
                'user' => $username
            ]);
        } else {
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'message' => 'Invalid credentials'
            ]);
        }
    } elseif ($action === 'logout') {
        Auth::logout();
        echo json_encode([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    } elseif ($action === 'check') {
        if (Auth::isLoggedIn()) {
            echo json_encode([
                'success' => true,
                'logged_in' => true,
                'user' => Auth::getCurrentUser()
            ]);
        } else {
            echo json_encode([
                'success' => true,
                'logged_in' => false
            ]);
        }
    } else {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid action'
        ]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
