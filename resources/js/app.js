import { Modal } from 'bootstrap';
import './main.js';
import './contact.js';

window.addEventListener('DOMContentLoaded', async () => {
    const siteBackground = document.getElementById('site-bg');

    if (siteBackground) {
        const threeModule = await import('three');
        window.THREE = threeModule;
        const vantaModule = await import('vanta/dist/vanta.dots.min.js');
        const DOTS = vantaModule.default.default;

        DOTS({
            el: siteBackground,
            mouseControls: false,
            touchControls: false,
            gyroControls: false,
            minHeight: 1900,
            minWidth: 1900,
            scale: 1.0,
            scaleMobile: 5.0,
            spacing: 33,
            size: 3,
            color: 0x569CD6,
            backgroundColor: 0x191919,
            showLines: false,
        });
    }

    const hash = window.location.hash;
    if (hash === '#attestato') {
        const modal = document.getElementById('attestatoModal');
        if (modal) {
            new Modal(modal).show();
        }
    } else if (hash === '#cv') {
        const modal = document.getElementById('cvModal');
        if (modal) {
            new Modal(modal).show();
        }
    }
});
