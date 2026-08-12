document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('hamburgerBtn');
    const menu = document.getElementById('navMenu');

    if (!btn || !menu) return;

    btn.addEventListener('click', () => {
        const estOuvert = menu.classList.toggle('active');
        btn.classList.toggle('active');
        btn.setAttribute('aria-expanded', estOuvert);
    });

    menu.querySelectorAll('.nav-link, .btn').forEach((lien) => {
        lien.addEventListener('click', () => {
            menu.classList.remove('active');
            btn.classList.remove('active');
            btn.setAttribute('aria-expanded', 'false');
        });
    });
});