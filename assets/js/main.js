// ========================================
// KEBAB SHOP - MAIN JS
// ========================================

document.addEventListener('DOMContentLoaded', function() {
    // Slider
    initSlider();
    // Mobile menu
    initMobileMenu();
    // Bottom nav active state
    initBottomNav();
});

// ========== SLIDER ==========
function initSlider() {
    const slider = document.getElementById('slider');
    if (!slider) return;

    const track = slider.querySelector('.slider-track');
    const slides = slider.querySelectorAll('.slide');
    const dots = slider.querySelectorAll('.dot');

    if (slides.length <= 1) return;

    let currentIndex = 0;
    let startX = 0;
    let isDragging = false;
    let autoplayInterval;

    function goToSlide(index) {
        if (index < 0) index = slides.length - 1;
        if (index >= slides.length) index = 0;
        currentIndex = index;
        track.style.transform = `translateX(-${currentIndex * 100}%)`;
        dots.forEach((d, i) => d.classList.toggle('active', i === currentIndex));
    }

    // Auto-play
    function startAutoplay() {
        autoplayInterval = setInterval(() => goToSlide(currentIndex + 1), 4000);
    }

    function resetAutoplay() {
        clearInterval(autoplayInterval);
        startAutoplay();
    }

    startAutoplay();

    // Touch/swipe support
    track.addEventListener('touchstart', (e) => {
        startX = e.touches[0].clientX;
        isDragging = true;
        clearInterval(autoplayInterval);
    }, { passive: true });

    track.addEventListener('touchend', (e) => {
        if (!isDragging) return;
        isDragging = false;
        const diff = startX - e.changedTouches[0].clientX;
        if (Math.abs(diff) > 50) {
            goToSlide(diff > 0 ? currentIndex + 1 : currentIndex - 1);
        }
        startAutoplay();
    }, { passive: true });

    // Dot clicks
    dots.forEach((dot) => {
        dot.addEventListener('click', () => {
            goToSlide(parseInt(dot.dataset.index));
            resetAutoplay();
        });
    });
}

// ========== MOBILE MENU ==========
function initMobileMenu() {
    const toggle = document.getElementById('menuToggle');
    const overlay = document.getElementById('menuOverlay');
    const close = document.getElementById('menuClose');
    const links = document.querySelectorAll('.menu-link');

    if (!toggle || !overlay) return;

    toggle.addEventListener('click', () => overlay.classList.add('active'));
    close.addEventListener('click', () => overlay.classList.remove('active'));
    overlay.addEventListener('click', (e) => {
        if (e.target === overlay) overlay.classList.remove('active');
    });
    links.forEach(link => {
        link.addEventListener('click', () => overlay.classList.remove('active'));
    });
}

// ========== BOTTOM NAV ==========
function initBottomNav() {
    const navItems = document.querySelectorAll('.bottom-nav .nav-item');
    navItems.forEach(item => {
        item.addEventListener('click', function() {
            navItems.forEach(n => n.classList.remove('active'));
            this.classList.add('active');
        });
    });
}
