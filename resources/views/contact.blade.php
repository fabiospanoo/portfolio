<x-layout title="Contact // Fabio Spanò" metaDescription="Get in touch with Fabio Spanò. Send a message about web development, agentic AI or Java projects.">
    <div class="body-style">
        <header>
            <div class="contact-terminal">
                <h1 class="title bolder text-center mt-5 pt-5" data-aos="fade-up">Let's talk</h1>
                <p class="contact-prompt" data-aos="fade-up" data-aos-delay="150">Interested in my computational knowledge of AI agents and Java? Send me a message, I usually reply within a day.</p>
                <p class="contact-links" data-aos="fade-up" data-aos-delay="300">
                    <span class="text-keyword">email</span> = <a class="text-link-custom copy-email" href="copy:fabiospanoo@outlook.it" title="Click to copy">'fabiospanoo [at] outlook (dot) it' ;</a>
                </p>

                @if (session('success'))
                    <div class="alert alert-success py-2 px-3" role="alert">{{ session('success') }}</div>
                @endif

                @php($formId = config('services.formspree.id'))

                <div id="contact-success" class="alert py-3 px-3 d-none" role="alert" style="background-color:#161617;border:1px solid #343434;color:#6A9955;font-family:'Space Mono',monospace;">
                    <span class="text-keyword">&gt;</span> message delivered! I'll get back to you soon.
                </div>
                <div id="contact-error" class="alert py-3 px-3 d-none" role="alert" style="background-color:#161617;border:1px solid #343434;color:#F14C4C;font-family:'Space Mono',monospace;">
                    <span class="text-keyword">&gt;</span> <span id="contact-error-text">something went wrong. try again, or email me directly at fabiospanoo@outlook.it</span>
                </div>

                <form id="contact-form" class="contact-form" data-aos="fade-up" data-aos-delay="450" method="POST"
                    action="{{ $formId ? 'https://formspree.io/f/' . $formId : route('contact.send') }}"
                    enctype="multipart/form-data"
                    accept-charset="UTF-8">
                    @csrf
                    <input type="hidden" name="_subject" value="New message from your website">

                    <div style="position: absolute; left: -9999px; top: -9999px;" aria-hidden="true">
                        <label for="_hp_company">Do not fill this field</label>
                        <input type="text" name="_hp_company" id="_hp_company" tabindex="-1" autocomplete="off" value="">
                    </div>

                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label for="name" class="form-label"><span class="text-comment">./ </span>name</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                            @error('name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="email" class="form-label"><span class="text-comment">./ </span>email</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                            @error('email') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mb-3 mt-3">
                        <label for="message" class="form-label"><span class="text-comment">./ </span>message</label>
                        <textarea class="form-control" id="message" name="message" rows="5" required>{{ old('message') }}</textarea>
                        @error('message') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label for="attachment" class="form-label"><span class="text-comment">./ </span>attachment</label>
                        <div class="file-dropzone" id="file-dropzone">
                            <input type="file" class="file-dropzone-input" id="attachment" name="attachment"
                                accept=".pdf,.doc,.docx,.odt,.txt,.rtf,.png,.jpg,.jpeg,.webp,.svg,.zip">
                            <div class="file-dropzone-content" id="file-dropzone-content">
                                <i class="bi bi-file-earmark-arrow-up file-dropzone-icon"></i>
                                <div class="file-dropzone-text">
                                    <span class="file-dropzone-title">Choose file or drag &amp; drop</span>
                                    <span class="file-dropzone-hint">Documents (PDF, DOC/DOCX, TXT), Images (PNG, JPG, SVG), ZIP &bull; max 2MB</span>
                                </div>
                            </div>
                            <div class="file-dropzone-preview d-none" id="file-dropzone-preview">
                                <div class="d-flex align-items-center gap-2 overflow-hidden me-2">
                                    <i class="bi bi-file-earmark-check file-dropzone-preview-icon"></i>
                                    <div class="d-flex flex-column text-truncate">
                                        <span class="file-dropzone-filename text-truncate" id="file-dropzone-filename"></span>
                                        <span class="file-dropzone-filesize" id="file-dropzone-filesize"></span>
                                    </div>
                                </div>
                                <button type="button" class="file-dropzone-clear" id="file-dropzone-clear" title="Remove file" aria-label="Remove attached file">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                        </div>
                        <div id="attachment-error" class="text-danger small mt-2 d-none" style="font-family:'Space Mono',monospace;"></div>
                        @error('attachment') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <span class="subtitle small me-2">* All fields are required</span>
                        <button type="submit" class="btn btn-quaternary">Send message</button>
                    </div>
                </form>

                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const dropzone = document.getElementById('file-dropzone');
                        const attachmentInput = document.getElementById('attachment');
                        const contentEl = document.getElementById('file-dropzone-content');
                        const previewEl = document.getElementById('file-dropzone-preview');
                        const filenameEl = document.getElementById('file-dropzone-filename');
                        const filesizeEl = document.getElementById('file-dropzone-filesize');
                        const clearBtn = document.getElementById('file-dropzone-clear');
                        const errorEl = document.getElementById('attachment-error');

                        if (!dropzone || !attachmentInput) return;

                        function formatBytes(bytes) {
                            if (bytes < 1024) return bytes + ' B';
                            if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
                            return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
                        }

                        function updatePreview(file) {
                            if (!file) {
                                contentEl.classList.remove('d-none');
                                previewEl.classList.add('d-none');
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
                                contentEl.classList.remove('d-none');
                                previewEl.classList.add('d-none');
                                dropzone.classList.remove('has-file');
                                return;
                            }

                            if (errorEl) {
                                errorEl.textContent = '';
                                errorEl.classList.add('d-none');
                            }

                            filenameEl.textContent = file.name;
                            filesizeEl.textContent = formatBytes(file.size);
                            contentEl.classList.add('d-none');
                            previewEl.classList.remove('d-none');
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
                    });
                </script>

                <script>
                    document.addEventListener('submit', function (e) {
                        if (e.target.id !== 'contact-form') return;
                        e.preventDefault();
                        const form = e.target;
                        const button = form.querySelector('button[type="submit"]');
                        const errorEl = document.getElementById('contact-error');
                        const errorTextEl = document.getElementById('contact-error-text');
                        const successEl = document.getElementById('contact-success');
                        const attachmentInput = document.getElementById('attachment');
                        const hasAttachment = attachmentInput && attachmentInput.files && attachmentInput.files.length > 0;

                        button.disabled = true;
                        if (errorEl) errorEl.classList.add('d-none');

                        const localAction = '{{ route('contact.send') }}';
                        const formspreeAction = '{{ $formId ? "https://formspree.io/f/" . $formId : route("contact.send") }}';
                        const targetUrl = hasAttachment ? localAction : formspreeAction;
                        const formData = new FormData(form);

                        function showError(msg) {
                            if (errorEl) {
                                if (errorTextEl && msg && msg !== 'Submission failed') {
                                    errorTextEl.textContent = msg;
                                }
                                errorEl.classList.remove('d-none');
                            }
                        }

                        const localHeaders = {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        };
                        const csrfToken = form.querySelector('input[name="_token"]')?.value;
                        if (csrfToken) {
                            localHeaders['X-CSRF-TOKEN'] = csrfToken;
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

                        fetch(targetUrl, {
                                method: 'POST',
                                headers: targetUrl === localAction ? localHeaders : { 'Accept': 'application/json' },
                                body: formData
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
                                if (targetUrl !== localAction) {
                                    return fetch(localAction, {
                                        method: 'POST',
                                        headers: localHeaders,
                                        body: formData
                                    })
                                    .then(async r2 => {
                                        const d2 = await r2.json().catch(() => ({}));
                                        if (!r2.ok) {
                                            const errMsg2 = extractErrorMessage(d2) || 'Submission failed';
                                            throw new Error(errMsg2);
                                        }
                                        form.classList.add('d-none');
                                        if (successEl) successEl.classList.remove('d-none');
                                    })
                                    .catch(err2 => {
                                        showError(err2.message || err.message);
                                    });
                                }
                                showError(err.message);
                            })
                            .finally(() => { button.disabled = false; });
                    });
                </script>
            </div>
        </header>
    </div>
</x-layout>