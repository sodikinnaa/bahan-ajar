<?php
require_once '../config/config.php';
require_once '../lib/Auth.php';
require_once '../lib/Database.php';

header('Content-Type: application/json');

if (!Auth::isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

try {
    $db = Database::getInstance(DB_PATH);

    if ($action === 'list') {
        $projects = $db->getAllProjects();
        echo json_encode([
            'success' => true,
            'projects' => $projects,
            'count' => count($projects)
        ]);
    } elseif ($action === 'get') {
        $projectId = $_GET['id'] ?? '';
        if (empty($projectId)) {
            throw new Exception('Project ID required');
        }

        $project = $db->getProject($projectId);
        if (!$project) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Project not found']);
            exit;
        }

        echo json_encode([
            'success' => true,
            'project' => $project
        ]);
    } elseif ($action === 'revise') {
        $projectId = $_POST['id'] ?? '';
        $revisionPrompt = $_POST['revision_prompt'] ?? '';
        $baseUrl = $_POST['base_url'] ?? '';
        $apiKey = $_POST['api_key'] ?? '';
        $model = $_POST['model'] ?? '';

        if (empty($projectId) || empty($revisionPrompt)) {
            throw new Exception('Missing project ID or revision prompt');
        }

        $project = $db->getProject($projectId);
        if (!$project) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Project not found']);
            exit;
        }

        // Add revision record
        $revision = $db->addRevision($projectId, [
            'prompt' => $revisionPrompt,
            'changes' => $_POST['changes'] ?? [],
            'status' => 'pending'
        ]);

        echo json_encode([
            'success' => true,
            'message' => 'Revision recorded',
            'revision' => $revision
        ]);
    } elseif ($action === 'delete') {
        $projectId = $_POST['id'] ?? '';
        if (empty($projectId)) {
            throw new Exception('Project ID required');
        }

        $project = $db->getProject($projectId);
        if (!$project) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Project not found']);
            exit;
        }

        // Delete associated PDF file
        if (!empty($project['pdf_file'])) {
            $pdfPath = OUTPUT_PATH . '/' . $project['pdf_file'];
            if (file_exists($pdfPath)) {
                unlink($pdfPath);
            }
        }

        $db->deleteProject($projectId);
        echo json_encode([
            'success' => true,
            'message' => 'Project deleted'
        ]);
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
