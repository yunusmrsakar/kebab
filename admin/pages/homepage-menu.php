<?php
$pageTitle = 'Home Page - Our Menu';
$currentPage = 'hp-menu';
require_once __DIR__ . '/../includes/header.php';

$success = '';

// Get all categories for dropdown
$categories = $pdo->query("SELECT * FROM menu_categories ORDER BY sort_order ASC")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update each menu slot
    for ($i = 1; $i <= 4; $i++) {
        $catId = !empty($_POST["category_$i"]) ? (int)$_POST["category_$i"] : null;

        // Handle image upload for this slot
        $imagePath = null;
        if (!empty($_FILES["menu_image_$i"]['name'])) {
            $result = uploadImage($_FILES["menu_image_$i"], 'menu');
            if (isset($result['path'])) {
                $imagePath = $result['path'];
            }
        }

        // Update slot
        if ($imagePath) {
            $stmt = $pdo->prepare("UPDATE homepage_menu SET category_id = ?, image_path = ? WHERE slot_number = ?");
            $stmt->execute([$catId, $imagePath, $i]);
        } else {
            $stmt = $pdo->prepare("UPDATE homepage_menu SET category_id = ? WHERE slot_number = ?");
            $stmt->execute([$catId, $i]);
        }
    }

    // View full menu button color
    if (isset($_POST['menu_btn_color'])) {
        saveSetting($pdo, 'menu_btn_color', $_POST['menu_btn_color']);
    }

    $success = 'Menu section saved successfully!';
}

// Get current menu slots
$menuSlots = $pdo->query("SELECT * FROM homepage_menu ORDER BY slot_number ASC")->fetchAll();
$settings = getSettings($pdo);
?>

<h1>Home Page Design - Our Menu</h1>

<?php if ($success): ?>
    <div class="alert alert-success"><?= $success ?></div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
    <div class="form-card">
        <h2>Our Menu</h2>
        <p class="form-hint">Select a category name to show on the homepage. The dropdown shows all categories from Menu > List menu.</p>

        <div class="menu-slots-grid">
            <?php foreach ($menuSlots as $slot): ?>
                <div class="menu-slot">
                    <?php if ($slot['image_path']): ?>
                        <img src="<?= UPLOAD_URL . sanitize($slot['image_path']) ?>" alt="" class="slot-image">
                    <?php else: ?>
                        <div class="slot-placeholder">No image</div>
                    <?php endif; ?>
                    <div class="form-group">
                        <input type="file" name="menu_image_<?= $slot['slot_number'] ?>" accept="image/*">
                    </div>
                    <div class="form-group">
                        <select name="category_<?= $slot['slot_number'] ?>" class="form-control">
                            <option value="">-- Select Category --</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= ($slot['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                                    <?= sanitize($cat['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="form-card">
        <h2>View Full Menu Button</h2>
        <div class="form-group">
            <label>Button colour</label>
            <input type="color" name="menu_btn_color" value="<?= sanitize($settings['menu_btn_color'] ?? '#cc0000') ?>">
        </div>
    </div>

    <button type="submit" class="btn btn-save">Save</button>
</form>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
