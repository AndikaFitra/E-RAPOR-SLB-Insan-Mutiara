import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

// All editable forms warn before losing changes and prevent duplicate submits.
const dirtyForms = new Set();
document.addEventListener('input', (event) => {
    const form = event.target.closest('form');
    if (form && !['GET', 'get'].includes(form.getAttribute('method') || 'GET')) dirtyForms.add(form);
});
window.addEventListener('beforeunload', (event) => {
    if (dirtyForms.size) { event.preventDefault(); event.returnValue = ''; }
});
document.addEventListener('submit', (event) => {
    const form = event.target;
    if (form.dataset.confirm && !window.confirm(form.dataset.confirm)) { event.preventDefault(); return; }
    dirtyForms.delete(form);
    const button = event.submitter;
    if (button) {
        setTimeout(() => { button.disabled = true; button.textContent = 'Sedang menyimpan...'; }, 0);
    }
});
document.addEventListener('click', (event) => {
    const anchor = event.target.closest('a[href]');
    if (!anchor || anchor.target === '_blank' || anchor.getAttribute('href').startsWith('#') || !dirtyForms.size) return;
    if (!window.confirm('Perubahan belum disimpan. Tinggalkan perubahan?')) event.preventDefault();
    else dirtyForms.clear();
});
window.addEventListener('pageshow', (event) => { if (event.persisted) window.location.reload(); });
