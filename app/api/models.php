<?php
require_once '../config/config.php';
require_once '../lib/Auth.php';
require_once '../lib/OpenAIClient.php';

header('Content-Type: application/json');

if (!Auth::isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

try {
    $baseUrl = $_POST['base_url'] ?? $_GET['base_url'] ?? '';
    $apiKey = $_POST['api_key'] ?? $_GET['api_key'] ?? '';

    if (empty($baseUrl) || empty($apiKey)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Missing base_url or api_key'
        ]);
        exit;
    }

    $client = new OpenAIClient($baseUrl, $apiKey);
    $models = $client->getModels();

    echo json_encode([
        'success' => true,
        'models' => $models,
        'count' => count($models)
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to fetch models: ' . $e->getMessage()
    ]);
}
?>
