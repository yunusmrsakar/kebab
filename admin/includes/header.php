<?php require_once __DIR__ . '/auth.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - <?= $pageTitle ?? 'Dashboard' ?></title>
    <link rel="stylesheet" href="<?= ADMIN_URL ?>/assets/admin.css">
    <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
</head>
<body>
    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h2>Admin Panel</h2>
            </div>
            <nav class="sidebar-nav">
                <a href="<?= ADMIN_URL ?>/index.php" class="nav-link <?= ($currentPage ?? '') === 'dashboard' ? 'active' : '' ?>">
                    Dashboard
                </a>
                <div class="nav-group">
                    <div class="nav-group-title">Home Page Design</div>
                    <a href="<?= ADMIN_URL ?>/pages/homepage-main.php" class="nav-link sub <?= ($currentPage ?? '') === 'hp-main' ? 'active' : '' ?>">Main</a>
                    <a href="<?= ADMIN_URL ?>/pages/homepage-offers.php" class="nav-link sub <?= ($currentPage ?? '') === 'hp-offers' ? 'active' : '' ?>">Offers</a>
                    <a href="<?= ADMIN_URL ?>/pages/homepage-sliders.php" class="nav-link sub <?= ($currentPage ?? '') === 'hp-sliders' ? 'active' : '' ?>">Sliders</a>
                    <a href="<?= ADMIN_URL ?>/pages/homepage-images.php" class="nav-link sub <?= ($currentPage ?? '') === 'hp-images' ? 'active' : '' ?>">Images</a>
                    <a href="<?= ADMIN_URL ?>/pages/homepage-menu.php" class="nav-link sub <?= ($currentPage ?? '') === 'hp-menu' ? 'active' : '' ?>">Our Menu</a>
                </div>
                <div class="nav-group">
                    <div class="nav-group-title">Settings</div>
                    <a href="<?= ADMIN_URL ?>/pages/opening-hours.php" class="nav-link sub <?= ($currentPage ?? '') === 'hours' ? 'active' : '' ?>">Opening Hours</a>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="topbar">
                <button class="sidebar-toggle" id="sidebarToggle">&#9776;</button>
                <span>Welcome, <?= sanitize($_SESSION['admin_user'] ?? 'Admin') ?></span>
                <a href="<?= ADMIN_URL ?>/logout.php" class="btn btn-sm btn-outline">Logout</a>
            </header>
            <div class="content-area">
