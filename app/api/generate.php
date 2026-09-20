<?php
require_once '../config/config.php';
require_once '../lib/Auth.php';
require_once '../lib/OpenAIClient.php';
require_once '../lib/Database.php';
require_once '../lib/FileUploader.php';
require_once '../lib/PDFGenerator.php';

header('Content-Type: application/json');

if (!Auth::isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

try {
    $baseUrl = $_POST['base_url'] ?? '';
    $apiKey = $_POST['api_key'] ?? '';
    $model = $_POST['model'] ?? '';
    $prompt = $_POST['prompt'] ?? '';
    $projectName = $_POST['project_name'] ?? 'Generated Module';
    $projectCode = $_POST['project_code'] ?? bin2hex(random_bytes(4));

    if (empty($baseUrl) || empty($apiKey) || empty($model) || empty($prompt)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Missing required parameters'
        ]);
        exit;
    }

    $db = Database::getInstance(DB_PATH);
    $uploader = new FileUploader(UPLOADS_PATH);

    // Collect context from uploaded files
    $context = [
        'project_name' => $projectName,
        'project_code' => $projectCode
    ];

    // Process file uploads if any
    $uploadedFiles = [];
    if (!empty($_FILES)) {
        foreach ($_FILES as $inputName => $file) {
            if ($file['error'] === UPLOAD_ERR_OK) {
                try {
                    $uploaded = $uploader->upload($inputName);
                    $uploadedFiles[] = $uploaded;

                    // If PDF, extract text for context
                    if ($uploaded['type'] === 'application/pdf') {
                        $pdfText = $uploader->extractTextFromPDF($uploaded['path']);
                        $context['pdf_content'] = substr($pdfText, 0, 2000); // First 2000 chars
                    }
                } catch (Exception $e) {
                    // Continue even if file processing fails
                    error_log('File processing error: ' . $e->getMessage());
                }
            }
        }
    }

    // Generate content using OpenAI
    $client = new OpenAIClient($baseUrl, $apiKey, $model);
    $generatedContent = $client->generateSlideContent($prompt, $context);

    // Create project record
    $project = $db->addProject([
        'name' => $projectName,
        'code' => $projectCode,
        'prompt' => $prompt,
        'model' => $model,
        'api_base' => $baseUrl,
        'uploaded_files' => $uploadedFiles,
        'status' => 'generated'
    ]);

    // Generate PDF
    $outputFile = OUTPUT_PATH . '/' . $projectCode;
    $pdfGen = new PDFGenerator($projectName, $projectCode, $outputFile);
    $pdfGen->setSlides($generatedContent);
    $pdfFile = $pdfGen->generatePDF();

    // Update project with PDF info
    $db->updateProject($project['id'], [
        'pdf_file' => basename($pdfFile),
        'slide_count' => $pdfGen->getSlideCount(),
        'slides_data' => $generatedContent,
        'status' => 'completed'
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Slides generated successfully',
        'project' => [
            'id' => $project['id'],
            'name' => $projectName,
            'code' => $projectCode,
            'slide_count' => $pdfGen->getSlideCount(),
            'pdf_file' => basename($pdfFile)
        ]
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Generation failed: ' . $e->getMessage()
    ]);
}
?>
