import { Modal } from 'bootstrap';
import './main.js';
import './contact.js';

window.addEventListener('DOMContentLoaded', () => {
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

