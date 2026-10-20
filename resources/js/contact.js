export function setupContactForm() {
    const form = document.getElementById('contact-form');
    const dropzone = document.getElementById('file-dropzone');
    const attachmentInput = document.getElementById('attachment');

    if (dropzone && attachmentInput) {
        const contentEl = document.getElementById('file-dropzone-content');
        const previewEl = document.getElementById('file-dropzone-preview');
        const filenameEl = document.getElementById('file-dropzone-filename');
        const filesizeEl = document.getElementById('file-dropzone-filesize');
        const clearBtn = document.getElementById('file-dropzone-clear');
        const errorEl = document.getElementById('attachment-error');

        function formatBytes(bytes) {
            if (bytes < 1024) return bytes + ' B';
            if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
            return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
        }

        function updatePreview(file) {
            if (!file) {
                contentEl?.classList.remove('d-none');
                previewEl?.classList.add('d-none');
                dropzone.classList.remove('has-file');
                return;
            }

            const maxBytes = 2 * 1024 * 1024; // 2MB
            if (file.size > maxBytes) {
                if (errorEl) {
                    errorEl.textContent = '> File size exceeds 2MB limit (' + formatBytes(file.size) + '). Please choose a smaller file.';
                    errorEl.classList.remove('d-none');
                }
                attachmentInput.value = '';
                contentEl?.classList.remove('d-none');
                previewEl?.classList.add('d-none');
                dropzone.classList.remove('has-file');
                return;
            }

            if (errorEl) {
                errorEl.textContent = '';
                errorEl.classList.add('d-none');
            }

            if (filenameEl) filenameEl.textContent = file.name;
            if (filesizeEl) filesizeEl.textContent = formatBytes(file.size);
            contentEl?.classList.add('d-none');
            previewEl?.classList.remove('d-none');
            dropzone.classList.add('has-file');
        }

        attachmentInput.addEventListener('change', function () {
            if (this.files && this.files[0]) {
                updatePreview(this.files[0]);
            } else {
                updatePreview(null);
            }
        });

        if (clearBtn) {
            clearBtn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                attachmentInput.value = '';
                updatePreview(null);
            });
        }

        ['dragenter', 'dragover'].forEach(function (eventName) {
            dropzone.addEventListener(eventName, function (e) {
                e.preventDefault();
                e.stopPropagation();
                dropzone.classList.add('is-dragover');
            });
        });

        ['dragleave', 'drop'].forEach(function (eventName) {
            dropzone.addEventListener(eventName, function (e) {
                e.preventDefault();
                e.stopPropagation();
                dropzone.classList.remove('is-dragover');
            });
        });

        dropzone.addEventListener('drop', function (e) {
            if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length > 0) {
                attachmentInput.files = e.dataTransfer.files;
                updatePreview(e.dataTransfer.files[0]);
            }
        });
    }

    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const button = form.querySelector('button[type="submit"]');
            const errorEl = document.getElementById('contact-error');
            const errorTextEl = document.getElementById('contact-error-text');
            const successEl = document.getElementById('contact-success');

            if (button) button.disabled = true;
            if (errorEl) errorEl.classList.add('d-none');

            const actionUrl = form.action || form.getAttribute('action');
            const formData = new FormData(form);

            function showError(msg) {
                if (errorEl) {
                    if (errorTextEl && msg && msg !== 'Submission failed') {
                        errorTextEl.textContent = msg;
                    }
                    errorEl.classList.remove('d-none');
                }
            }

            const headers = {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            };
            const csrfToken = form.querySelector('input[name="_token"]')?.value;
            if (csrfToken) {
                headers['X-CSRF-TOKEN'] = csrfToken;
            }

            function extractErrorMessage(data) {
                if (!data) return null;
                if (typeof data.error === 'string') return data.error;
                if (data.errors) {
                    if (Array.isArray(data.errors) && data.errors.length > 0) {
                        return data.errors[0]?.message || data.errors[0];
                    }
                    if (typeof data.errors === 'object') {
                        const firstKey = Object.keys(data.errors)[0];
                        if (firstKey && data.errors[firstKey]?.length) {
                            return data.errors[firstKey][0];
                        }
                    }
                }
                return data.message || null;
            }

            fetch(actionUrl, {
                method: 'POST',
                headers: headers,
                body: formData,
            })
                .then(async r => {
                    const data = await r.json().catch(() => ({}));
                    if (!r.ok) {
                        const errMsg = extractErrorMessage(data) || 'Submission failed';
                        throw new Error(errMsg);
                    }
                    form.classList.add('d-none');
                    if (successEl) successEl.classList.remove('d-none');
                })
                .catch(err => {
                    showError(err.message);
                })
                .finally(() => {
                    if (button) button.disabled = false;
                });
        });
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', setupContactForm);
} else {
    setupContactForm();
}
