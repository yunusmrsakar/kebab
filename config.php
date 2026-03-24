<?php
// Auto-detect site URL
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost:8085';
$baseUrl = $protocol . '://' . $host;

define('SITE_URL', $baseUrl);
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
    $needsSetup = !file_exists(DB_PATH);

    $pdo = new PDO('sqlite:' . DB_PATH, null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);
    $pdo->exec("PRAGMA journal_mode=WAL");
    $pdo->exec("PRAGMA foreign_keys=ON");

    // Auto-setup database if it doesn't exist
    if ($needsSetup) {
        autoSetupDatabase($pdo);
    }
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

function autoSetupDatabase($pdo) {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS homepage_settings (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            setting_key TEXT NOT NULL UNIQUE,
            setting_value TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS sliders (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            image_path TEXT NOT NULL,
            sort_order INTEGER DEFAULT 0,
            is_active INTEGER DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS menu_categories (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            slug TEXT NOT NULL,
            sort_order INTEGER DEFAULT 0,
            is_active INTEGER DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS menu_items (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            category_id INTEGER NOT NULL,
            name TEXT NOT NULL,
            description TEXT,
            price REAL NOT NULL,
            image_path TEXT,
            is_active INTEGER DEFAULT 1,
            sort_order INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (category_id) REFERENCES menu_categories(id) ON DELETE CASCADE
        )
    ");
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS homepage_menu (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            slot_number INTEGER NOT NULL,
            category_id INTEGER,
            image_path TEXT,
            sort_order INTEGER DEFAULT 0,
            FOREIGN KEY (category_id) REFERENCES menu_categories(id) ON DELETE SET NULL
        )
    ");
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS opening_hours (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            day_name TEXT NOT NULL,
            collection_open TEXT,
            collection_close TEXT,
            delivery_open TEXT,
            delivery_close TEXT,
            is_closed INTEGER DEFAULT 0,
            sort_order INTEGER DEFAULT 0
        )
    ");
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS admin_users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT NOT NULL UNIQUE,
            password TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");

    // Default settings
    $defaults = [
        'shop_title' => '<p style="text-align:center;">Milano Burgers<br>Liverpool</p>',
        'about_text' => '<p>We are pizza and kebab shop located in the city of Liverpool.</p>',
        'about_bg_color' => '#ffffff',
        'main_offer' => '<p style="text-align:center;">Free Garlic Bread with cheese<br>on orders over £20</p>',
        'banner_text' => '20% OFF ON ALL ORDERS OVER £20',
        'banner_bg_color' => '#cc0000',
        'order_btn_color' => '#cc0000',
        'order_btn_font_color' => '#ffffff',
        'container_bg_color' => '#ffffff',
        'bottom_bar_color' => '#0088cc',
        'nav_bar_bg_color' => '#333333',
        'menu_btn_color' => '#cc0000',
        'location_address' => '10 London Road, Liverpool, L3 6AD',
        'contact_phone' => '01513871125',
        'marketing1_text' => 'Made Fresh Daily',
        'marketing2_text' => 'Delicious Food Menu',
    ];
    $stmt = $pdo->prepare("INSERT OR REPLACE INTO homepage_settings (setting_key, setting_value) VALUES (?, ?)");
    foreach ($defaults as $key => $value) {
        $stmt->execute([$key, $value]);
    }

    // Opening hours
    $days = [
        ['Mon', '04:30', '23:00', '04:30', '23:00', 1],
        ['Tue', '04:30', '23:00', '04:30', '23:00', 2],
        ['Wed', '04:30', '23:00', '04:30', '23:00', 3],
        ['Thu', '04:30', '23:00', '04:30', '23:00', 4],
        ['Fri', '04:30', '23:00', '04:30', '23:00', 5],
        ['Sat', '04:30', '23:00', '04:30', '23:00', 6],
        ['Sun', '04:30', '23:00', '04:30', '23:00', 7],
    ];
    $stmt = $pdo->prepare("INSERT INTO opening_hours (day_name, collection_open, collection_close, delivery_open, delivery_close, sort_order) VALUES (?, ?, ?, ?, ?, ?)");
    foreach ($days as $d) { $stmt->execute($d); }

    // Menu slots & categories
    $stmt = $pdo->prepare("INSERT INTO homepage_menu (slot_number) VALUES (?)");
    for ($i = 1; $i <= 4; $i++) { $stmt->execute([$i]); }

    $cats = [['Pizzas','pizzas',1],['Burgers','burgers',2],['Garlic Breads','garlic-breads',3],['Dishes','dishes',4]];
    $stmt = $pdo->prepare("INSERT INTO menu_categories (name, slug, sort_order) VALUES (?, ?, ?)");
    foreach ($cats as $c) { $stmt->execute($c); }

    $pdo->exec("UPDATE homepage_menu SET category_id = 1 WHERE slot_number = 1");
    $pdo->exec("UPDATE homepage_menu SET category_id = 2 WHERE slot_number = 2");
    $pdo->exec("UPDATE homepage_menu SET category_id = 3 WHERE slot_number = 3");
    $pdo->exec("UPDATE homepage_menu SET category_id = 4 WHERE slot_number = 4");

    // Admin user (admin / admin123)
    $hash = password_hash('admin123', PASSWORD_DEFAULT);
    $pdo->prepare("INSERT INTO admin_users (username, password) VALUES (?, ?)")->execute(['admin', $hash]);
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
