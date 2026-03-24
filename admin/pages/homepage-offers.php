<?php
$pageTitle = 'Home Page - Offers';
$currentPage = 'hp-offers';
require_once __DIR__ . '/../includes/header.php';

$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['main_offer'])) {
        saveSetting($pdo, 'main_offer', $_POST['main_offer']);
    }
    if (isset($_POST['banner_text'])) {
        saveSetting($pdo, 'banner_text', $_POST['banner_text']);
    }
    if (isset($_POST['banner_bg_color'])) {
        saveSetting($pdo, 'banner_bg_color', $_POST['banner_bg_color']);
    }
    $success = 'Offers saved successfully!';
}

$settings = getSettings($pdo);
?>

<h1>Home Page Design - Offers</h1>

<?php if ($success): ?>
    <div class="alert alert-success"><?= $success ?></div>
<?php endif; ?>

<form method="POST">
    <!-- Main Offer -->
    <div class="form-card">
        <h2>Main Offer</h2>
        <p class="form-hint">If there is no text here, this section will not show on the homepage.</p>
        <div id="mainOfferEditor"><?= $settings['main_offer'] ?? '' ?></div>
        <input type="hidden" name="main_offer" id="mainOfferInput">
    </div>

    <!-- Animated Banner -->
    <div class="form-card">
        <h2>Animated Banner</h2>
        <div class="form-group">
            <label>Text</label>
            <div id="bannerTextEditor"><?= sanitize($settings['banner_text'] ?? '') ?></div>
            <input type="hidden" name="banner_text" id="bannerTextInput">
        </div>
        <div class="form-group">
            <label>Background colour</label>
            <input type="color" name="banner_bg_color" value="<?= sanitize($settings['banner_bg_color'] ?? '#cc0000') ?>">
        </div>
    </div>

    <button type="submit" class="btn btn-save" onclick="syncEditors()">Save</button>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    window.mainOfferQuill = new Quill('#mainOfferEditor', {
        theme: 'snow',
        modules: { toolbar: [['bold', 'italic'], [{ 'align': [] }], [{ 'size': ['small', false, 'large', 'huge'] }], [{ 'color': [] }], ['underline']] }
    });

    window.bannerQuill = new Quill('#bannerTextEditor', {
        theme: 'snow',
        modules: { toolbar: [['bold', 'italic'], [{ 'align': [] }], [{ 'size': ['small', false, 'large', 'huge'] }], [{ 'color': [] }], ['underline']] }
    });
});

function syncEditors() {
    document.getElementById('mainOfferInput').value = window.mainOfferQuill.root.innerHTML;
    // For banner, we want plain text
    document.getElementById('bannerTextInput').value = window.bannerQuill.getText().trim();
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
