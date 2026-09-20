<?php
require_once '../config/config.php';
require_once '../lib/Auth.php';
require_once '../lib/Database.php';

if (!Auth::isLoggedIn()) {
    http_response_code(401);
    echo 'Not authenticated';
    exit;
}

try {
    $projectId = $_GET['id'] ?? '';
    if (empty($projectId)) {
        throw new Exception('Project ID required');
    }

    $db = Database::getInstance(DB_PATH);
    $project = $db->getProject($projectId);

    if (!$project) {
        http_response_code(404);
        echo 'Project not found';
        exit;
    }

    if (empty($project['pdf_file'])) {
        throw new Exception('PDF not generated yet');
    }

    $pdfPath = OUTPUT_PATH . '/' . $project['pdf_file'];
    if (!file_exists($pdfPath)) {
        throw new Exception('PDF file not found');
    }

    // Set headers for download
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . $project['code'] . '.pdf"');
    header('Content-Length: ' . filesize($pdfPath));

    readfile($pdfPath);
    exit;
} catch (Exception $e) {
    http_response_code(500);
    echo 'Download failed: ' . $e->getMessage();
}
?>
