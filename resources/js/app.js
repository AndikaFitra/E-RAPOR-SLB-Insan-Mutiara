import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

const dirtyForms = new Set();
const confirmedForms = new WeakSet();
let activeDialog = null;

function draftKey(form) {
    const index = Array.from(document.forms).indexOf(form);
    return `e-rapor:draft:${location.pathname}${location.search}:${form.action}:${index}`;
}

function draftFields(form) {
    return Array.from(form.elements).filter((field) =>
        (field instanceof HTMLInputElement || field instanceof HTMLTextAreaElement || field instanceof HTMLSelectElement) &&
        field.name && !field.disabled && !['hidden', 'password', 'file', 'submit', 'button'].includes(field.type));
}

function saveDraft(form) {
    try {
        sessionStorage.setItem(draftKey(form), JSON.stringify(draftFields(form).map((field) => ({
            name: field.name,
            value: field.value,
            checked: field instanceof HTMLInputElement ? field.checked : null,
        }))));
    } catch { /* Browser storage may be unavailable. */ }
}

function clearDraft(form) {
    try { sessionStorage.removeItem(draftKey(form)); } catch { /* Browser storage may be unavailable. */ }
}

function restoreDrafts() {
    document.querySelectorAll('form').forEach((form) => {
        let saved;
        try { saved = sessionStorage.getItem(draftKey(form)); } catch { return; }
        if (!saved) return;
        let values;
        try { values = JSON.parse(saved); } catch { clearDraft(form); return; }
        if (!Array.isArray(values)) { clearDraft(form); return; }
        const fields = draftFields(form);
        values.forEach((entry, index) => {
            const field = fields[index];
            if (!field || field.name !== entry.name) return;
            if (field instanceof HTMLInputElement && ['checkbox', 'radio'].includes(field.type)) {
                field.checked = Boolean(entry.checked);
            } else {
                field.value = entry.value;
            }
            field.dispatchEvent(new Event('input', { bubbles: true }));
            field.dispatchEvent(new Event('change', { bubbles: true }));
        });
        dirtyForms.add(form);
        const notice = document.createElement('div');
        notice.className = 'app-draft-notice';
        notice.setAttribute('role', 'status');
        notice.textContent = 'Isian yang belum disimpan dipulihkan dari tab ini. Periksa kembali sebelum menyimpan.';
        form.prepend(notice);
    });
}

requestAnimationFrame(restoreDrafts);

function confirmAction({ title, message, confirmText, danger = false }) {
    if (activeDialog) return Promise.resolve(false);

    return new Promise((resolve) => {
        const previousFocus = document.activeElement;
        const overlay = document.createElement('div');
        overlay.className = 'app-confirm-backdrop';
        overlay.innerHTML = `
            <div class="app-confirm-dialog" role="dialog" aria-modal="true" aria-labelledby="app-confirm-title" aria-describedby="app-confirm-message" tabindex="-1">
                <div class="app-confirm-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="25" height="25">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3m0 4h.01M10.3 4.7 2.5 18.2A2 2 0 0 0 4.2 21h15.6a2 2 0 0 0 1.7-2.8L13.7 4.7a2 2 0 0 0-3.4 0z"/>
                    </svg>
                </div>
                <h2 id="app-confirm-title"></h2>
                <p id="app-confirm-message"></p>
                <div class="app-confirm-actions">
                    <button type="button" class="app-confirm-cancel">Batal</button>
                    <button type="button" class="app-confirm-accept"></button>
                </div>
            </div>`;
        const dialog = overlay.querySelector('.app-confirm-dialog');
        const cancel = overlay.querySelector('.app-confirm-cancel');
        const accept = overlay.querySelector('.app-confirm-accept');
        overlay.querySelector('#app-confirm-title').textContent = title;
        overlay.querySelector('#app-confirm-message').textContent = message;
        accept.textContent = confirmText;
        if (danger) accept.classList.add('is-danger');

        const close = (accepted) => {
            document.removeEventListener('keydown', onKeydown);
            overlay.remove();
            activeDialog = null;
            if (previousFocus instanceof HTMLElement && previousFocus.isConnected) previousFocus.focus();
            resolve(accepted);
        };
        const onKeydown = (event) => {
            if (event.key === 'Escape') {
                event.preventDefault();
                close(false);
            }
            if (event.key === 'Tab') {
                const first = cancel;
                const last = accept;
                if (event.shiftKey && document.activeElement === first) {
                    event.preventDefault();
                    last.focus();
                } else if (!event.shiftKey && document.activeElement === last) {
                    event.preventDefault();
                    first.focus();
                }
            }
        };
        overlay.addEventListener('click', (event) => {
            if (event.target === overlay) close(false);
        });
        cancel.addEventListener('click', () => close(false));
        accept.addEventListener('click', () => close(true));
        document.addEventListener('keydown', onKeydown);
        document.body.append(overlay);
        activeDialog = overlay;
        cancel.focus();
    });
}

document.addEventListener('input', (event) => {
    const form = event.target.closest('form');
    if (form && (form.getAttribute('method') || 'GET').toUpperCase() !== 'GET') {
        dirtyForms.add(form);
        saveDraft(form);
    }
});

document.addEventListener('submit', async (event) => {
    const form = event.target;
    if (!form.dataset.confirm || confirmedForms.has(form)) {
        confirmedForms.delete(form);
        return;
    }
    event.preventDefault();
    event.stopImmediatePropagation();
    const submitter = event.submitter;
    const accepted = await confirmAction({
        title: 'Konfirmasi tindakan',
        message: form.dataset.confirm,
        confirmText: submitter?.textContent?.trim() || 'Lanjutkan',
    });
    if (accepted) {
        confirmedForms.add(form);
        form.requestSubmit(submitter || undefined);
    }
}, true);

document.addEventListener('submit', (event) => {
    const form = event.target;
    dirtyForms.delete(form);
    clearDraft(form);
    const button = event.submitter;
    if (button) {
        setTimeout(() => { button.disabled = true; button.textContent = 'Sedang menyimpan...'; }, 0);
    }
});

document.addEventListener('click', async (event) => {
    const anchor = event.target.closest('a[href]');
    if (!anchor || !dirtyForms.size || event.defaultPrevented || event.button !== 0 ||
        event.ctrlKey || event.metaKey || event.shiftKey || event.altKey ||
        anchor.target === '_blank' || anchor.hasAttribute('download') ||
        anchor.getAttribute('href').startsWith('#')) return;

    event.preventDefault();
    event.stopImmediatePropagation();
    const accepted = await confirmAction({
        title: 'Perubahan belum disimpan',
        message: 'Jika meninggalkan halaman ini, perubahan yang belum disimpan akan hilang.',
        confirmText: 'Tinggalkan halaman',
        danger: true,
    });
    if (accepted) {
        dirtyForms.forEach(clearDraft);
        dirtyForms.clear();
        window.location.assign(anchor.href);
    }
}, true);

window.addEventListener('pageshow', (event) => { if (event.persisted) window.location.reload(); });
