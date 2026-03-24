<?php
require_once __DIR__ . '/config.php';

$settings = getSettings($pdo);

// Get sliders
$sliders = $pdo->query("SELECT * FROM sliders WHERE is_active = 1 ORDER BY sort_order ASC")->fetchAll();

// Get homepage menu items
$menuItems = $pdo->query("
    SELECT hm.*, mc.name as category_name, mc.slug as category_slug
    FROM homepage_menu hm
    LEFT JOIN menu_categories mc ON hm.category_id = mc.id
    ORDER BY hm.slot_number ASC
")->fetchAll();

// Get opening hours
$hours = $pdo->query("SELECT * FROM opening_hours ORDER BY sort_order ASC")->fetchAll();

// Determine open/closed status
$currentDay = date('D');
$dayMap = ['Mon'=>'Mon','Tue'=>'Tue','Wed'=>'Wed','Thu'=>'Thu','Fri'=>'Fri','Sat'=>'Sat','Sun'=>'Sun'];
$shortDay = $dayMap[$currentDay] ?? 'Mon';
$currentTime = date('H:i');
$isOpen = false;
$todayHours = '';

foreach ($hours as $h) {
    if ($h['day_name'] === $shortDay && !$h['is_closed']) {
        $collOpen = substr($h['collection_open'], 0, 5);
        $collClose = substr($h['collection_close'], 0, 5);
        if ($currentTime >= $collOpen && $currentTime <= $collClose) {
            $isOpen = true;
        }
        $todayHours = $collOpen . ' - ' . $collClose;
    }
}

// Helper for settings with defaults
$s = function($key, $default = '') use ($settings) {
    return $settings[$key] ?? $default;
};

$orderBtnColor = $s('order_btn_color', '#cc0000');
$orderBtnFontColor = $s('order_btn_font_color', '#ffffff');
$containerBgColor = $s('container_bg_color', '#ffffff');
$bottomBarColor = $s('bottom_bar_color', '#0088cc');
$navBarBgColor = $s('nav_bar_bg_color', '#333333');
$aboutBgColor = $s('about_bg_color', '#ffffff');
$bannerBgColor = $s('banner_bg_color', '#cc0000');
$menuBtnColor = $s('menu_btn_color', '#cc0000');
$logoPath = $s('logo_path', '');
$marketing1Path = $s('marketing1_path', '');
$marketing2Path = $s('marketing2_path', '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= strip_tags($s('shop_title', 'Kebab Shop')) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
    <style>
        :root {
            --order-btn-color: <?= sanitize($orderBtnColor) ?>;
            --order-btn-font-color: <?= sanitize($orderBtnFontColor) ?>;
            --container-bg-color: <?= sanitize($containerBgColor) ?>;
            --bottom-bar-color: <?= sanitize($bottomBarColor) ?>;
            --nav-bar-bg-color: <?= sanitize($navBarBgColor) ?>;
            --about-bg-color: <?= sanitize($aboutBgColor) ?>;
            --banner-bg-color: <?= sanitize($bannerBgColor) ?>;
            --menu-btn-color: <?= sanitize($menuBtnColor) ?>;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-top">
            <div class="open-status">
                <?php if ($isOpen): ?>
                    <span class="status-dot open"></span>
                    <span class="status-text open">Open</span>
                    <span class="status-hours">Accepting orders from <?= sanitize($todayHours) ?></span>
                <?php else: ?>
                    <span class="status-dot closed"></span>
                    <span class="status-text closed">Closed</span>
                    <?php if ($todayHours): ?>
                        <span class="status-hours">Opens <?= sanitize($todayHours) ?></span>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <button class="menu-toggle" id="menuToggle" aria-label="Menu">
                <span></span><span></span><span></span>
            </button>
        </div>
        <div class="header-main">
            <?php if ($logoPath): ?>
                <img src="<?= UPLOAD_URL . sanitize($logoPath) ?>" alt="Logo" class="logo" draggable="false" oncontextmenu="return false;">
            <?php endif; ?>
            <div class="shop-title"><?= $s('shop_title', 'Shop Name') ?></div>
        </div>
    </header>

    <!-- Mobile Menu Overlay -->
    <div class="menu-overlay" id="menuOverlay">
        <div class="menu-overlay-content">
            <button class="menu-close" id="menuClose">&times;</button>
            <nav>
                <a href="#" class="menu-link">Home</a>
                <a href="menu.php" class="menu-link">Menu</a>
                <a href="cart.php" class="menu-link">Cart</a>
                <a href="#about-section" class="menu-link" id="aboutLink">About</a>
                <a href="account.php" class="menu-link">Account</a>
            </nav>
        </div>
    </div>

    <!-- Sliders -->
    <?php if (!empty($sliders)): ?>
    <section class="slider-section">
        <div class="slider" id="slider">
            <div class="slider-track">
                <?php foreach ($sliders as $slide): ?>
                    <div class="slide">
                        <img src="<?= UPLOAD_URL . sanitize($slide['image_path']) ?>" alt="" loading="lazy">
                    </div>
                <?php endforeach; ?>
            </div>
            <?php if (count($sliders) > 1): ?>
            <div class="slider-dots">
                <?php for ($i = 0; $i < count($sliders); $i++): ?>
                    <span class="dot <?= $i === 0 ? 'active' : '' ?>" data-index="<?= $i ?>"></span>
                <?php endfor; ?>
            </div>
            <?php endif; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- Main Offer -->
    <?php $mainOffer = $s('main_offer', ''); ?>
    <?php if (trim(strip_tags($mainOffer))): ?>
    <section class="main-offer">
        <div class="offer-text"><?= $mainOffer ?></div>
    </section>
    <?php endif; ?>

    <!-- Order Button -->
    <section class="order-btn-section">
        <a href="menu.php" class="order-btn" style="background-color: var(--order-btn-color); color: var(--order-btn-font-color);">
            View Menu &amp; Order
        </a>
    </section>

    <!-- Animated Banner -->
    <?php $bannerText = $s('banner_text', ''); ?>
    <?php if ($bannerText): ?>
    <section class="banner-section" style="background-color: var(--banner-bg-color);">
        <div class="banner-track">
            <span class="banner-text"><?= sanitize($bannerText) ?></span>
            <span class="banner-text"><?= sanitize($bannerText) ?></span>
            <span class="banner-text"><?= sanitize($bannerText) ?></span>
        </div>
    </section>
    <?php endif; ?>

    <!-- About Section -->
    <section class="about-section" id="about-section" style="background-color: var(--about-bg-color);">
        <div class="about-content">
            <?= $s('about_text', '') ?>
        </div>
    </section>

    <!-- Marketing Image 1 -->
    <?php if ($marketing1Path): ?>
    <section class="marketing-image">
        <div class="marketing-img-wrapper">
            <img src="<?= UPLOAD_URL . sanitize($marketing1Path) ?>" alt="" loading="lazy">
            <div class="marketing-overlay full"></div>
            <div class="marketing-text"><?= sanitize($s('marketing1_text', 'Made Fresh Daily')) ?></div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Features Container -->
    <section class="features-section" style="background-color: var(--container-bg-color);">
        <h2 class="features-title">Making Your Orders Easier</h2>
        <p class="features-subtitle">Enjoy online ordering experience, fresh food, easy payment and quick delivery.</p>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                </div>
                <h3>Easy to order</h3>
                <p>and fast order process</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                </div>
                <h3>Cash &amp; Card</h3>
                <p>Pay with comfort</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                </div>
                <h3>Fast Delivery</h3>
                <p>at selected times</p>
            </div>
        </div>
    </section>

    <!-- Our Menu Section -->
    <section class="menu-section">
        <h2 class="section-title">Our Menu</h2>
        <div class="menu-grid">
            <?php foreach ($menuItems as $item): ?>
                <?php if ($item['category_id']): ?>
                <a href="menu.php?cat=<?= sanitize($item['category_slug'] ?? '') ?>" class="menu-card">
                    <?php if ($item['image_path']): ?>
                        <img src="<?= UPLOAD_URL . sanitize($item['image_path']) ?>" alt="<?= sanitize($item['category_name'] ?? '') ?>" loading="lazy">
                    <?php else: ?>
                        <div class="menu-card-placeholder"></div>
                    <?php endif; ?>
                    <span class="menu-card-label"><?= sanitize($item['category_name'] ?? '') ?></span>
                </a>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <a href="menu.php" class="view-menu-btn" style="border-color: var(--menu-btn-color); color: var(--menu-btn-color);">
            View full menu
        </a>
    </section>

    <!-- Marketing Image 2 -->
    <?php if ($marketing2Path): ?>
    <section class="marketing-image">
        <div class="marketing-img-wrapper">
            <img src="<?= UPLOAD_URL . sanitize($marketing2Path) ?>" alt="" loading="lazy">
            <div class="marketing-overlay bottom"></div>
            <div class="marketing-text bottom-text"><?= sanitize($s('marketing2_text', 'Delicious Food Menu')) ?></div>
            <a href="menu.php" class="marketing-order-btn" style="background-color: var(--order-btn-color); color: var(--order-btn-font-color);">
                Order Online
            </a>
        </div>
    </section>
    <?php endif; ?>

    <!-- Location & Contact -->
    <section class="info-section">
        <?php $address = $s('location_address', ''); ?>
        <?php if ($address): ?>
        <div class="info-block">
            <h3>
                <svg class="info-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                Location
            </h3>
            <p><?= sanitize($address) ?></p>
        </div>
        <?php endif; ?>

        <?php $phone = $s('contact_phone', ''); ?>
        <?php if ($phone): ?>
        <div class="info-block">
            <h3>
                <svg class="info-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg>
                Contact
            </h3>
            <a href="tel:<?= sanitize($phone) ?>" class="phone-link"><?= sanitize($phone) ?></a>
        </div>
        <?php endif; ?>
    </section>

    <!-- Opening Hours -->
    <?php if (!empty($hours)): ?>
    <section class="hours-section">
        <h3>
            <svg class="info-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            Opening hours
        </h3>
        <table class="hours-table">
            <thead>
                <tr>
                    <th>Day</th>
                    <th>Collection</th>
                    <th>Delivery</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($hours as $h): ?>
                <tr>
                    <td><?= sanitize($h['day_name']) ?></td>
                    <td>
                        <?php if ($h['is_closed']): ?>
                            Closed
                        <?php else: ?>
                            <?= substr($h['collection_open'],0,5) ?> - <?= substr($h['collection_close'],0,5) ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($h['is_closed']): ?>
                            Closed
                        <?php else: ?>
                            <?= substr($h['delivery_open'],0,5) ?> - <?= substr($h['delivery_close'],0,5) ?>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>
    <?php endif; ?>

    <!-- Bottom spacing for nav bar -->
    <div class="bottom-spacer"></div>

    <!-- Bottom Navigation Bar -->
    <nav class="bottom-nav" style="--active-color: var(--bottom-bar-color);">
        <a href="index.php" class="nav-item active">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
            <span>Home</span>
        </a>
        <a href="menu.php" class="nav-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
            <span>Menu</span>
        </a>
        <a href="cart.php" class="nav-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
            <span>Cart</span>
        </a>
        <a href="#about-section" class="nav-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>
            <span>About</span>
        </a>
        <a href="account.php" class="nav-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            <span>Account</span>
        </a>
    </nav>

    <script src="<?= SITE_URL ?>/assets/js/main.js"></script>
</body>
</html>
