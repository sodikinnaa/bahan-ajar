<?php
define('APP_ROOT', dirname(__DIR__));
define('DB_PATH', APP_ROOT . '/database/projects.json');
define('UPLOADS_PATH', APP_ROOT . '/uploads');
define('OUTPUT_PATH', APP_ROOT . '/output');

// Authentication
define('ADMIN_USER', 'admin');
define('ADMIN_PASS', '@noodu2026');

// OpenAI API Config (will be configured per request)
define('OPENAI_API_TIMEOUT', 60);

// Session config
define('SESSION_TIMEOUT', 3600);

// PDF config
define('PDF_WIDTH', 1280);
define('PDF_HEIGHT', 720);
define('PDF_DPI', 150);

// Ensure directories exist
if (!is_dir(UPLOADS_PATH)) mkdir(UPLOADS_PATH, 0755, true);
if (!is_dir(OUTPUT_PATH)) mkdir(OUTPUT_PATH, 0755, true);
if (!is_dir(dirname(DB_PATH))) mkdir(dirname(DB_PATH), 0755, true);

// Initialize JSON database if not exists
if (!file_exists(DB_PATH)) {
    file_put_contents(DB_PATH, json_encode(['projects' => []], JSON_PRETTY_PRINT));
}
?>
