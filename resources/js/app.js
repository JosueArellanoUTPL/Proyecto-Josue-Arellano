import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Guardar posicion del menu lateral.
document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.querySelector('[data-sidebar]');

    if (!sidebar) {
        return;
    }

    const key = 'sidebar-scroll';
    const savedScroll = Number(localStorage.getItem(key) || 0);

    requestAnimationFrame(() => {
        sidebar.scrollTop = savedScroll;
    });

    sidebar.addEventListener('scroll', () => {
        requestAnimationFrame(() => {
            localStorage.setItem(key, sidebar.scrollTop);
        });
    }, { passive: true });

    sidebar.querySelectorAll('a.side-link').forEach((link) => {
        link.addEventListener('click', () => {
            localStorage.setItem(key, sidebar.scrollTop);
        });
    });
});
