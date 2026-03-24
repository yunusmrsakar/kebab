<?php
$pageTitle = 'Home Page - Main';
$currentPage = 'hp-main';
require_once __DIR__ . '/../includes/header.php';

$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Shop title (rich text)
    if (isset($_POST['shop_title'])) {
        saveSetting($pdo, 'shop_title', $_POST['shop_title']);
    }

    // About text (rich text)
    if (isset($_POST['about_text'])) {
        saveSetting($pdo, 'about_text', $_POST['about_text']);
    }

    // About background color
    if (isset($_POST['about_bg_color'])) {
        saveSetting($pdo, 'about_bg_color', $_POST['about_bg_color']);
    }

    // Colors
    $colorFields = [
        'order_btn_color', 'order_btn_font_color',
        'container_bg_color', 'bottom_bar_color', 'nav_bar_bg_color'
    ];
    foreach ($colorFields as $field) {
        if (isset($_POST[$field])) {
            saveSetting($pdo, $field, $_POST[$field]);
        }
    }

    // Location & Contact
    if (isset($_POST['location_address'])) {
        saveSetting($pdo, 'location_address', $_POST['location_address']);
    }
    if (isset($_POST['contact_phone'])) {
        saveSetting($pdo, 'contact_phone', $_POST['contact_phone']);
    }

    // Logo upload
    if (!empty($_FILES['logo']['name'])) {
        $result = uploadImage($_FILES['logo'], 'logos');
        if (isset($result['path'])) {
            saveSetting($pdo, 'logo_path', $result['path']);
        }
    }

    $success = 'Settings saved successfully!';
}

$settings = getSettings($pdo);
?>

<h1>Home Page Design - Main</h1>

<?php if ($success): ?>
    <div class="alert alert-success"><?= $success ?></div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
    <!-- Shop Title -->
    <div class="form-card">
        <h2>Shop Title</h2>
        <div id="shopTitleEditor"><?= $settings['shop_title'] ?? '' ?></div>
        <input type="hidden" name="shop_title" id="shopTitleInput">
    </div>

    <!-- Logo -->
    <div class="form-card">
        <h2>Logo</h2>
        <?php if (!empty($settings['logo_path'])): ?>
            <div class="current-image">
                <img src="<?= UPLOAD_URL . sanitize($settings['logo_path']) ?>" alt="Current Logo" style="max-width:120px;">
            </div>
        <?php endif; ?>
        <div class="form-group">
            <input type="file" name="logo" accept="image/*">
        </div>
    </div>

    <!-- Colors Row -->
    <div class="form-card">
        <h2>Colors</h2>
        <div class="color-grid">
            <div class="color-item">
                <label>Order Button</label>
                <div class="color-sub">
                    <div>
                        <small>Button colour</small>
                        <input type="color" name="order_btn_color" value="<?= sanitize($settings['order_btn_color'] ?? '#cc0000') ?>">
                    </div>
                    <div>
                        <small>Font colour</small>
                        <input type="color" name="order_btn_font_color" value="<?= sanitize($settings['order_btn_font_color'] ?? '#ffffff') ?>">
                    </div>
                </div>
            </div>
            <div class="color-item">
                <label>Container</label>
                <div class="color-sub">
                    <div>
                        <small>Background colour</small>
                        <input type="color" name="container_bg_color" value="<?= sanitize($settings['container_bg_color'] ?? '#ffffff') ?>">
                    </div>
                </div>
            </div>
            <div class="color-item">
                <label>Bottom Bar</label>
                <div class="color-sub">
                    <div>
                        <small>Colour</small>
                        <input type="color" name="bottom_bar_color" value="<?= sanitize($settings['bottom_bar_color'] ?? '#0088cc') ?>">
                    </div>
                </div>
            </div>
            <div class="color-item">
                <label>Navigation Bar</label>
                <div class="color-sub">
                    <div>
                        <small>Background colour</small>
                        <input type="color" name="nav_bar_bg_color" value="<?= sanitize($settings['nav_bar_bg_color'] ?? '#333333') ?>">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- About Section -->
    <div class="form-card">
        <h2>About</h2>
        <div id="aboutEditor"><?= $settings['about_text'] ?? '' ?></div>
        <input type="hidden" name="about_text" id="aboutTextInput">
        <div class="form-group mt-16">
            <label>Background colour</label>
            <input type="color" name="about_bg_color" value="<?= sanitize($settings['about_bg_color'] ?? '#ffffff') ?>">
        </div>
    </div>

    <!-- Location & Contact -->
    <div class="form-card">
        <h2>Location & Contact</h2>
        <div class="form-group">
            <label>Address</label>
            <input type="text" name="location_address" value="<?= sanitize($settings['location_address'] ?? '') ?>" class="form-control">
        </div>
        <div class="form-group">
            <label>Phone</label>
            <input type="text" name="contact_phone" value="<?= sanitize($settings['contact_phone'] ?? '') ?>" class="form-control">
        </div>
    </div>

    <button type="submit" class="btn btn-save" onclick="syncEditors()">Save</button>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Quill editors
    window.shopTitleQuill = new Quill('#shopTitleEditor', {
        theme: 'snow',
        modules: { toolbar: [['bold', 'italic'], [{ 'align': [] }], [{ 'size': ['small', false, 'large', 'huge'] }], [{ 'color': [] }], ['underline']] }
    });

    window.aboutQuill = new Quill('#aboutEditor', {
        theme: 'snow',
        modules: { toolbar: [['bold', 'italic'], [{ 'align': [] }], [{ 'size': ['small', false, 'large', 'huge'] }], [{ 'color': [] }], ['underline']] }
    });
});

function syncEditors() {
    document.getElementById('shopTitleInput').value = window.shopTitleQuill.root.innerHTML;
    document.getElementById('aboutTextInput').value = window.aboutQuill.root.innerHTML;
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
