document.addEventListener('DOMContentLoaded', function() {
    const burger = document.querySelector('.burger-menu');
    const mobileMenu = document.querySelector('.mobile-menu');

    if (!burger || !mobileMenu) return;

    burger.addEventListener('click', () => {
        mobileMenu.classList.toggle('active');
        burger.classList.toggle('active');
    });

    document.addEventListener('click', (e) => {
        if (!mobileMenu.contains(e.target) && !burger.contains(e.target)) {
            mobileMenu.classList.remove('active');
            burger.classList.remove('active');
        }
    });
});