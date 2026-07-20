<?php
/**
 * SmartHUB Configuration File
 */

// Application Settings
define('APP_ROOT', dirname(__DIR__));
define('URL_ROOT', 'http://localhost/SMARTHUB/public');
define('APP_NAME', 'SmartHUB Digital Library');

// Database Settings
define('DB_HOST', 'localhost');
define('DB_PORT', '3307');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'smarthub_db');

// Encryption Settings
define('ENCRYPTION_KEY', 'SmartHUBDigitalLibrarySecureKey#2026!'); // 32-byte key for AES-256-CBC
define('ENCRYPTION_CIPHER', 'aes-256-cbc');

// Session Settings
define('SESSION_LIFETIME', 1800); // 30 minutes in seconds

// Upload Settings
define('UPLOAD_DIR', dirname(__DIR__, 2) . '/uploads');
define('MAX_FILE_SIZE', 52428800); // 50MB in bytes
define('ALLOWED_EXTENSIONS', ['pdf', 'docx', 'ppt', 'zip', 'mp4', 'jpg', 'png']);
define('ALLOWED_MIME_TYPES', [
    'application/pdf' => 'pdf',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
    'application/msword' => 'doc',
    'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'ppt',
    'application/vnd.ms-powerpoint' => 'ppt',
    'application/zip' => 'zip',
    'application/x-zip-compressed' => 'zip',
    'video/mp4' => 'mp4',
    'image/jpeg' => 'jpg',
    'image/png' => 'png'
]);

// Error Reporting (development mode)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
