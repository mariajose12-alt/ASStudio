<script>
    // ── NAVBAR SCROLL ──
    const navbar = document.getElementById('navbar');
    window.addEventListener('scroll', () => {
        navbar.classList.toggle('scrolled', window.scrollY > 80);
    });

    // ── HAMBURGER MENU ──
    const hamburger  = document.getElementById('hamburger');
    const mobileMenu = document.getElementById('mobileMenu');

    hamburger.addEventListener('click', () => {
        const isOpen = mobileMenu.classList.toggle('open');
        hamburger.classList.toggle('open', isOpen);
        document.body.style.overflow = isOpen ? 'hidden' : '';
    });

    function closeMobileMenu() {
        mobileMenu.classList.remove('open');
        hamburger.classList.remove('open');
        document.body.style.overflow = '';
    }
</script>
