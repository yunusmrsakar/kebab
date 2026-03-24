<?php
$pageTitle = 'Home Page - Images';
$currentPage = 'hp-images';
require_once __DIR__ . '/../includes/header.php';

$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Marketing Image 1
    if (!empty($_FILES['marketing1']['name'])) {
        $result = uploadImage($_FILES['marketing1'], 'marketing');
        if (isset($result['path'])) {
            saveSetting($pdo, 'marketing1_path', $result['path']);
        }
    }

    // Marketing Image 2
    if (!empty($_FILES['marketing2']['name'])) {
        $result = uploadImage($_FILES['marketing2'], 'marketing');
        if (isset($result['path'])) {
            saveSetting($pdo, 'marketing2_path', $result['path']);
        }
    }

    $success = 'Images saved successfully!';
}

$settings = getSettings($pdo);
?>

<h1>Home Page Design - Images</h1>

<?php if ($success): ?>
    <div class="alert alert-success"><?= $success ?></div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
    <!-- Marketing Image 1 -->
    <div class="form-card">
        <h2>Marketing Image 1</h2>
        <p class="form-hint">This image will be dimmed with "Made Fresh Daily" text overlay.</p>
        <?php if (!empty($settings['marketing1_path'])): ?>
            <div class="current-image">
                <img src="<?= UPLOAD_URL . sanitize($settings['marketing1_path']) ?>" alt="Marketing 1" class="preview-image">
            </div>
        <?php endif; ?>
        <div class="form-group">
            <input type="file" name="marketing1" accept="image/*">
        </div>
    </div>

    <!-- Marketing Image 2 -->
    <div class="form-card">
        <h2>Marketing Image 2</h2>
        <p class="form-hint">This image will have a bottom gradient with "Delicious Food Menu" text and an "Order Online" button.</p>
        <?php if (!empty($settings['marketing2_path'])): ?>
            <div class="current-image">
                <img src="<?= UPLOAD_URL . sanitize($settings['marketing2_path']) ?>" alt="Marketing 2" class="preview-image">
            </div>
        <?php endif; ?>
        <div class="form-group">
            <input type="file" name="marketing2" accept="image/*">
        </div>
    </div>

    <button type="submit" class="btn btn-save">Save</button>
</form>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
