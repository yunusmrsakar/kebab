<?php
$pageTitle = 'Dashboard';
$currentPage = 'dashboard';
require_once __DIR__ . '/includes/header.php';
?>

<h1>Dashboard</h1>
<p>Welcome to the admin panel. Use the sidebar to manage your home page design.</p>

<div class="dashboard-cards">
    <a href="pages/homepage-main.php" class="dash-card">
        <h3>Main Page</h3>
        <p>Shop title, logo, colors</p>
    </a>
    <a href="pages/homepage-offers.php" class="dash-card">
        <h3>Offers</h3>
        <p>Main offer, animated banner</p>
    </a>
    <a href="pages/homepage-sliders.php" class="dash-card">
        <h3>Sliders</h3>
        <p>Homepage image sliders</p>
    </a>
    <a href="pages/homepage-images.php" class="dash-card">
        <h3>Images</h3>
        <p>Marketing images 1 & 2</p>
    </a>
    <a href="pages/homepage-menu.php" class="dash-card">
        <h3>Our Menu</h3>
        <p>Category images & names</p>
    </a>
    <a href="pages/opening-hours.php" class="dash-card">
        <h3>Opening Hours</h3>
        <p>Collection & delivery times</p>
    </a>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
