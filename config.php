<?php
// Site configuration
define('SITE_URL', 'http://localhost:8085');
define('ADMIN_URL', SITE_URL . '/admin');
define('UPLOAD_PATH', __DIR__ . '/uploads/');
define('UPLOAD_URL', SITE_URL . '/uploads/');
define('DB_PATH', __DIR__ . '/database.sqlite');

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// SQLite Database connection
try {
    $pdo = new PDO('sqlite:' . DB_PATH, null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);
    $pdo->exec("PRAGMA journal_mode=WAL");
    $pdo->exec("PRAGMA foreign_keys=ON");
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Helper functions
function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function getSetting($pdo, $key) {
    $stmt = $pdo->prepare("SELECT setting_value FROM homepage_settings WHERE setting_key = ?");
    $stmt->execute([$key]);
    $result = $stmt->fetch();
    return $result ? $result['setting_value'] : '';
}

function getSettings($pdo) {
    $stmt = $pdo->query("SELECT setting_key, setting_value FROM homepage_settings");
    $settings = [];
    while ($row = $stmt->fetch()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
    return $settings;
}

function saveSetting($pdo, $key, $value) {
    $stmt = $pdo->prepare("INSERT INTO homepage_settings (setting_key, setting_value) VALUES (?, ?)
        ON CONFLICT(setting_key) DO UPDATE SET setting_value = excluded.setting_value");
    $stmt->execute([$key, $value]);
}

function uploadImage($file, $folder, $maxWidth = 1200) {
    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

    if (!in_array($file['type'], $allowedTypes)) {
        return ['error' => 'Invalid file type. Only JPG, PNG, WebP, and GIF are allowed.'];
    }

    if ($file['size'] > 5 * 1024 * 1024) {
        return ['error' => 'File size must be less than 5MB.'];
    }

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '_' . time() . '.' . $ext;
    $uploadDir = UPLOAD_PATH . $folder . '/';

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $destination = $uploadDir . $filename;

    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return ['success' => true, 'filename' => $filename, 'path' => $folder . '/' . $filename];
    }

    return ['error' => 'Failed to upload file.'];
}
