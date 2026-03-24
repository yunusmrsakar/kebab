<?php
$pageTitle = 'Home Page - Sliders';
$currentPage = 'hp-sliders';
require_once __DIR__ . '/../includes/header.php';

$success = '';
$error = '';

// Handle delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("SELECT image_path FROM sliders WHERE id = ?");
    $stmt->execute([$id]);
    $slider = $stmt->fetch();
    if ($slider) {
        $filePath = UPLOAD_PATH . $slider['image_path'];
        if (file_exists($filePath)) unlink($filePath);
        $pdo->prepare("DELETE FROM sliders WHERE id = ?")->execute([$id]);
        $success = 'Slider deleted.';
    }
}

// Handle upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_FILES['slider_image']['name'])) {
    $result = uploadImage($_FILES['slider_image'], 'sliders');
    if (isset($result['path'])) {
        $sortOrder = $pdo->query("SELECT COALESCE(MAX(sort_order),0)+1 FROM sliders")->fetchColumn();
        $stmt = $pdo->prepare("INSERT INTO sliders (image_path, sort_order) VALUES (?, ?)");
        $stmt->execute([$result['path'], $sortOrder]);
        $success = 'Slider image uploaded!';
    } else {
        $error = $result['error'] ?? 'Upload failed.';
    }
}

// Handle reorder
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reorder'])) {
    $orders = $_POST['order'] ?? [];
    foreach ($orders as $id => $order) {
        $pdo->prepare("UPDATE sliders SET sort_order = ? WHERE id = ?")->execute([(int)$order, (int)$id]);
    }
    $success = 'Order updated!';
}

$sliders = $pdo->query("SELECT * FROM sliders ORDER BY sort_order ASC")->fetchAll();
?>

<h1>Home Page Design - Sliders</h1>

<?php if ($success): ?>
    <div class="alert alert-success"><?= $success ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert alert-error"><?= sanitize($error) ?></div>
<?php endif; ?>

<!-- Upload New Slider -->
<div class="form-card">
    <h2>Add Slider Image</h2>
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <input type="file" name="slider_image" accept="image/*" required>
        </div>
        <button type="submit" class="btn btn-save">Upload</button>
    </form>
</div>

<!-- Current Sliders -->
<div class="form-card">
    <h2>Current Sliders</h2>
    <?php if (empty($sliders)): ?>
        <p class="text-muted">No sliders uploaded yet.</p>
    <?php else: ?>
        <form method="POST">
            <input type="hidden" name="reorder" value="1">
            <div class="slider-list">
                <?php foreach ($sliders as $s): ?>
                    <div class="slider-item">
                        <img src="<?= UPLOAD_URL . sanitize($s['image_path']) ?>" alt="" class="slider-thumb">
                        <div class="slider-controls">
                            <label>Order: <input type="number" name="order[<?= $s['id'] ?>]" value="<?= $s['sort_order'] ?>" class="form-control-sm"></label>
                            <a href="?delete=<?= $s['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this slider?')">Delete</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <button type="submit" class="btn btn-save mt-16">Update Order</button>
        </form>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
