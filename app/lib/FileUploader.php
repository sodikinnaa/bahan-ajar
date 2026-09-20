<?php
class FileUploader {
    private $uploadsPath;
    private $allowedTypes = ['application/pdf', 'image/png', 'image/jpeg'];
    private $maxFileSize = 10 * 1024 * 1024; // 10MB

    public function __construct($uploadsPath) {
        $this->uploadsPath = $uploadsPath;
        if (!is_dir($this->uploadsPath)) {
            mkdir($this->uploadsPath, 0755, true);
        }
    }

    public function upload($fileInput) {
        if (!isset($_FILES[$fileInput]) || $_FILES[$fileInput]['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('File upload failed or no file provided');
        }

        $file = $_FILES[$fileInput];
        $mimeType = mime_content_type($file['tmp_name']);

        if (!in_array($mimeType, $this->allowedTypes)) {
            throw new Exception('Invalid file type. Allowed: PDF, PNG, JPEG');
        }

        if ($file['size'] > $this->maxFileSize) {
            throw new Exception('File size exceeds 10MB limit');
        }

        $fileName = bin2hex(random_bytes(16)) . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
        $filePath = $this->uploadsPath . '/' . $fileName;

        if (!move_uploaded_file($file['tmp_name'], $filePath)) {
            throw new Exception('Failed to move uploaded file');
        }

        return [
            'name' => $file['name'],
            'path' => $filePath,
            'file' => $fileName,
            'type' => $mimeType,
            'size' => $file['size'],
            'uploaded_at' => date('c')
        ];
    }

    public function extractTextFromPDF($filePath) {
        if (!file_exists($filePath)) {
            throw new Exception('File not found');
        }

        $cmd = sprintf('pdftotext %s -', escapeshellarg($filePath));
        $output = shell_exec($cmd);

        if ($output === null) {
            throw new Exception('Failed to extract text from PDF. Ensure poppler-utils is installed.');
        }

        return trim($output);
    }

    public function getFileInfo($filePath) {
        if (!file_exists($filePath)) {
            throw new Exception('File not found');
        }

        return [
            'path' => $filePath,
            'file' => basename($filePath),
            'size' => filesize($filePath),
            'type' => mime_content_type($filePath),
            'exists' => true
        ];
    }

    public function cleanup($filePath) {
        if (file_exists($filePath)) {
            return unlink($filePath);
        }
        return true;
    }
}
?>
