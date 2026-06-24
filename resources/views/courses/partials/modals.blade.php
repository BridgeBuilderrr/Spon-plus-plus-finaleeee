<!-- Spon++ Modal Activities -->

<!-- Upload Material Modal -->
<div class="modal fade" id="uploadMaterialModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content overflow-hidden">
            <div class="modal-header border-0 p-5 pb-2">
                <h3 class="fw-extrabold text-main m-0 d-flex align-items-center gap-3">
                    <div class="p-2 bg-success-soft rounded-3 text-success">
                        <i data-lucide="book-copy" size="24"></i>
                    </div>
                    Add Material
                </h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('materials.store', $classroom) }}" method="POST" id="form-material">
                @csrf
                <div class="modal-body p-5 pt-3">
                    <div class="mb-4">
                        <label class="form-label">Material Name</label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. Chapter 4" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Brief Overview</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Explain focus..."></textarea>
                    </div>

                    <div class="mb-0">
                        <label class="form-label">Resource Files (Max 10 files, 50MB/each)</label>
                        <div class="neon-drop-card" id="drop-card-material">
                            <div class="neon-dz-message" id="msg-material">
                                <div class="neon-icon-wrap">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M4 16v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2"/>
                                        <polyline points="16 12 12 8 8 12"/>
                                        <line x1="12" y1="8" x2="12" y2="20"/>
                                    </svg>
                                </div>
                                <h6 class="mb-1 fw-bold">Drop files here</h6>
                                <p class="neon-dz-sub mb-0">or <span class="neon-browse-btn">browse files</span></p>
                            </div>
                            <div id="dz-material-previews" class="neon-previews-wrap"></div>
                            <div id="dz-material" class="neon-dropzone-hidden"></div>
                            <div class="neon-stats">
                                <div class="neon-stat">
                                    <div class="neon-stat-num" id="count-material-total">0</div>
                                    <div class="neon-stat-label">FILES</div>
                                </div>
                                <div class="neon-stat">
                                    <div class="neon-stat-num neon-success" id="count-material-success">0</div>
                                    <div class="neon-stat-label">UPLOADED</div>
                                </div>
                                <div class="neon-stat">
                                    <div class="neon-stat-num neon-danger" id="count-material-error">0</div>
                                    <div class="neon-stat-label">FAILED</div>
                                </div>
                            </div>
                        </div>
                        <div id="inputs-material"></div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-5 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold btn-ripple" data-bs-dismiss="modal" onclick="addRipple(event, this)">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow btn-ripple" onclick="addRipple(event, this)">Save Resources</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Share Announcement Modal -->
<div class="modal fade" id="createAnnouncementModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content overflow-hidden">
            <div class="modal-header border-0 p-5 pb-2">
                <h3 class="fw-extrabold text-main m-0 d-flex align-items-center gap-3">
                    <div class="p-2 bg-warning-soft rounded-3 text-warning">
                        <i data-lucide="megaphone" size="24"></i>
                    </div>
                    Share Announcement
                </h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('announcements.store', $classroom) }}" method="POST">
                @csrf
                <div class="modal-body p-5 pt-3">
                    <div class="mb-4">
                        <label class="form-label">Announcement Title</label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. Schedule for Final Exams" required autofocus>
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Detailed Message</label>
                        <textarea name="description" id="announcement-editor" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-5 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold btn-ripple" data-bs-dismiss="modal" onclick="addRipple(event, this)">Discard</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow btn-ripple" onclick="addRipple(event, this)">Post Announcement</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // ─── Dropzone Registry ───────────────────────────────────────────────────────
    // Keeps track of all initialized instances so we can destroy before reinit.
    window._dzInstances = window._dzInstances || {};

    /**
     * Create a Dropzone instance.
     *
     * @param {string} prefix        - e.g. 'assignment' | 'material'
     * @param {string} cardId        - wrapper card element id
     * @param {string} previewsId    - previews container id
     * @param {string} hiddenTriggerId - the invisible div used as DZ element
     * @param {string} inputsId      - hidden inputs container id
     * @param {string} msgId         - the message div id (hidden once files added)
     */
    function initNeonDZ(prefix, cardId, previewsId, hiddenTriggerId, inputsId, msgId) {
        // Safety check if Dropzone is even loaded
        if (typeof Dropzone === 'undefined') {
            console.error('Dropzone library not loaded!');
            return;
        }

        // Destroy previous instance if exists
        if (window._dzInstances[prefix]) {
            try { window._dzInstances[prefix].destroy(); } catch(e) {}
            delete window._dzInstances[prefix];
        }

        const card        = document.getElementById(cardId);
        const previewsEl  = document.getElementById(previewsId);
        const triggerEl   = document.getElementById(hiddenTriggerId);
        const inputsCont  = document.getElementById(inputsId);
        const msgEl       = document.getElementById(msgId);
        const browseBtn   = msgEl ? msgEl.querySelector('.neon-browse-btn') : null;

        if (!card || !triggerEl) return;

        // Reset counters
        let total = 0, success = 0, error = 0;
        ['total','success','error'].forEach(t => {
            const el = document.getElementById(`count-${prefix}-${t}`);
            if (el) el.textContent = '0';
        });

        // Clear previous previews & hidden inputs
        previewsEl.innerHTML = '';
        inputsCont.innerHTML = '';

        // ── Build the Dropzone ──────────────────────────────────────────────────
        const dz = new Dropzone('#' + hiddenTriggerId, {
            url: "{{ route('upload') }}",
            maxFilesize: 50,        // MB
            maxFiles: 10,
            autoProcessQueue: true,
            addRemoveLinks: false,  // We render our own remove button
            headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
            paramName: 'file',
            parallelUploads: 2,
            clickable: '#' + hiddenTriggerId,   // only the hidden div is the DZ clickable
            previewsContainer: '#' + previewsId,
            createImageThumbnails: true,
            previewTemplate: `
                <div class="neon-preview">
                    <img data-dz-thumbnail alt="" />
                    <div class="neon-preview-details">
                        <div class="neon-preview-name" data-dz-name></div>
                        <div class="neon-preview-size" data-dz-size></div>
                    </div>
                    <div class="neon-progress-bar">
                        <span class="neon-progress-fill" data-dz-uploadprogress></span>
                    </div>
                    <div class="neon-marks">
                        <span class="neon-success-mark">✔</span>
                        <span class="neon-error-mark">✘</span>
                    </div>
                    <div class="neon-error-msg" data-dz-errormessage></div>
                    <button type="button" class="neon-remove-btn" data-dz-remove>✕</button>
                </div>
            `
        });

        window._dzInstances[prefix] = dz;

        // ── Drag visual feedback ────────────────────────────────────────────────
        dz.on('dragenter', () => card.classList.add('neon-drag-active'));
        dz.on('dragleave', () => card.classList.remove('neon-drag-active'));
        dz.on('drop',      () => card.classList.remove('neon-drag-active'));

        // ── File added ──────────────────────────────────────────────────────────
        dz.on('addedfile', () => {
            total++;
            document.getElementById(`count-${prefix}-total`).textContent = total;
            if (msgEl) msgEl.style.display = 'none';   // hide placeholder
        });

        // ── Upload success ──────────────────────────────────────────────────────
        dz.on('success', (file, response) => {
            success++;
            document.getElementById(`count-${prefix}-success`).textContent = success;

            // Store as hidden input so form submission picks it up
            const hidden = document.createElement('input');
            hidden.type  = 'hidden';
            hidden.name  = 'files[]';
            hidden.value = JSON.stringify(response);
            hidden.dataset.uuid = file.upload.uuid;
            inputsCont.appendChild(hidden);

            if (typeof showToast === 'function') showToast(`✔ "${file.name}" uploaded`, 'success');
        });

        // ── Upload error ────────────────────────────────────────────────────────
        dz.on('error', (file, errMsg) => {
            error++;
            document.getElementById(`count-${prefix}-error`).textContent = error;
            const msg = typeof errMsg === 'string' ? errMsg : (errMsg.message || 'Upload failed');
            if (typeof showToast === 'function') showToast(msg, 'error');
        });

        // ── File removed ────────────────────────────────────────────────────────
        dz.on('removedfile', (file) => {
            total = Math.max(0, total - 1);
            document.getElementById(`count-${prefix}-total`).textContent = total;

            // Remove matching hidden input
            const inp = inputsCont.querySelector(`[data-uuid="${file.upload ? file.upload.uuid : ''}"]`);
            if (inp) inp.remove();

            // Show placeholder again if no files left
            if (dz.files.length === 0 && msgEl) msgEl.style.display = '';
        });

        // ── "Browse files" link inside the message area ─────────────────────────
        // We manually trigger DZ's hidden input when clicked
        if (browseBtn) {
            browseBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                // Find the hidden file input Dropzone created and click it
                const fileInput = triggerEl.querySelector('input[type=file]');
                if (fileInput) fileInput.click();
            });
        }

        // ── Whole card is also a drop target ────────────────────────────────────
        // Clicking anywhere on the card (outside previews) opens file picker
        card.addEventListener('click', (e) => {
            // Don't trigger if clicking a remove button or inside a preview
            if (e.target.closest('.neon-preview') || e.target.closest('.neon-stats')) return;
            const fileInput = triggerEl.querySelector('input[type=file]');
            if (fileInput) fileInput.click();
        });

        return dz;
    }

    // ─── Init on modal shown ─────────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function () {
        // TinyMCE
        if (typeof tinymce !== 'undefined') {
            tinymce.init({
                selector: '#announcement-editor',
                height: 300,
                menubar: false,
                skin: (document.body.getAttribute('data-bs-theme') === 'dark' ? 'oxide-dark' : 'oxide'),
                content_css: (document.body.getAttribute('data-bs-theme') === 'dark' ? 'dark' : 'default'),
                plugins: 'lists link emoticons image code table',
                toolbar: 'undo redo | blocks | bold italic underline | alignleft aligncenter alignright | numlist bullist | link image emoticons | table code',
                setup: editor => editor.on('change', () => editor.save())
            });
        }

        document.getElementById('uploadMaterialModal').addEventListener('shown.bs.modal', function () {
            initNeonDZ('material', 'drop-card-material', 'dz-material-previews', 'dz-material', 'inputs-material', 'msg-material');
            if (typeof lucide !== 'undefined') lucide.createIcons();
        });

        document.getElementById('uploadMaterialModal').addEventListener('hidden.bs.modal', function () {
            if (window._dzInstances['material']) {
                try { window._dzInstances['material'].destroy(); } catch(e) {}
                delete window._dzInstances['material'];
            }
        });
    });
</script>

<style>
/* ═══════════════════════════════════════════════════
   NEON DROPZONE  —  fixed version
   ═══════════════════════════════════════════════════ */

/* ── Outer card ─────────────────────────────────── */
.neon-drop-card {
    background: var(--bg-color, #131519);
    border: 2px dashed var(--border-color, #242830);
    border-radius: 16px;
    overflow: hidden;
    transition: border-color 0.25s, background 0.25s;
    position: relative;
    cursor: pointer;          /* whole card is clickable */
    user-select: none;
}
.neon-drop-card::after {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(ellipse at 50% 0%, rgba(var(--primary-rgb, 99,102,241), 0.05) 0%, transparent 70%);
    pointer-events: none;     /* let clicks pass through */
    z-index: 0;
}
.neon-drop-card.neon-drag-active {
    border-color: var(--primary-color);
    background: rgba(var(--primary-rgb, 99,102,241), 0.04);
}

/* ── Placeholder message ────────────────────────── */
.neon-dz-message {
    padding: 2rem 1rem 1.25rem;
    text-align: center;
    position: relative;
    z-index: 1;
    pointer-events: none;     /* pass drag events to card */
}
.neon-dz-message .neon-browse-btn {
    pointer-events: all;      /* but browse link is clickable */
}
.neon-icon-wrap {
    width: 56px;
    height: 56px;
    background: rgba(var(--primary-rgb, 99,102,241), 0.1);
    border: 1.5px solid rgba(var(--primary-rgb, 99,102,241), 0.25);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    transition: transform 0.3s, background 0.3s;
}
.neon-drop-card:hover .neon-icon-wrap {
    transform: translateY(-4px);
    background: rgba(var(--primary-rgb, 99,102,241), 0.18);
}
.neon-icon-wrap svg {
    width: 24px; height: 24px;
    stroke: var(--primary-color, #6366f1);
}
.neon-dz-sub   { font-size: 0.82rem; color: var(--text-muted, #6b7280); }
.neon-browse-btn {
    color: var(--primary-color, #6366f1);
    font-weight: 700;
    cursor: pointer;
    text-decoration: underline;
    text-underline-offset: 2px;
}

/* ── Hidden Dropzone trigger element ────────────── */
/* DZ needs a real element; we hide it visually but keep it functional */
.neon-dropzone-hidden {
    position: absolute;
    inset: 0;
    opacity: 0;
    z-index: 2;               /* sits above message, captures drops */
    cursor: pointer;
}
/* The file input DZ injects must also be invisible but present */
.neon-dropzone-hidden input[type=file] {
    position: absolute;
    inset: 0;
    opacity: 0;
    cursor: pointer;
    width: 100%;
    height: 100%;
}

/* ── Previews container ─────────────────────────── */
.neon-previews-wrap {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    padding: 0.75rem 0.75rem 0;
    position: relative;
    z-index: 3;               /* above the hidden trigger so remove btn works */
}

/* ── Individual preview tile ────────────────────── */
.neon-preview {
    background: var(--card-bg, #1e2025);
    border: 1px solid var(--border-color, #242830);
    border-radius: 12px;
    width: 150px;
    overflow: hidden;
    position: relative;
    flex-shrink: 0;
    cursor: default;
}
.neon-preview img[data-dz-thumbnail] {
    width: 100%;
    height: 72px;
    object-fit: cover;
    display: block;
    background: var(--border-color);
}
.neon-preview-details {
    padding: 0.4rem 0.6rem 0.3rem;
}
.neon-preview-name {
    font-size: 0.72rem;
    font-weight: 600;
    color: var(--text-color, #e8eaf0);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.neon-preview-size {
    font-size: 0.66rem;
    color: var(--text-muted, #6b7280);
    margin-top: 1px;
}

/* ── Progress bar ───────────────────────────────── */
.neon-progress-bar {
    height: 3px;
    background: var(--border-color, #242830);
    position: relative;
    overflow: hidden;
}
.neon-progress-fill {
    display: block;
    height: 100%;
    width: 0%;
    background: var(--primary-color, #6366f1);
    transition: width 0.2s ease;
}
/* Dropzone sets width via style attribute on [data-dz-uploadprogress] */
[data-dz-uploadprogress] { width: 0%; }

/* ── Success / error marks ──────────────────────── */
.neon-marks {
    position: absolute;
    top: 0.4rem;
    right: 0.4rem;
}
.neon-success-mark,
.neon-error-mark {
    display: none;
    width: 18px; height: 18px;
    border-radius: 50%;
    font-size: 0.6rem;
    font-weight: 700;
    align-items: center;
    justify-content: center;
}
.dz-success .neon-success-mark { display: flex; background: #3dffb0; color: #0b0c0f; }
.dz-error   .neon-error-mark   { display: flex; background: #ff5252; color: #fff; }

.neon-error-msg {
    font-size: 0.66rem;
    color: #ff5252;
    padding: 0 0.6rem 0.3rem;
    display: none;
}
.dz-error .neon-error-msg { display: block; }

/* ── Remove button ──────────────────────────────── */
.neon-remove-btn {
    display: block;
    width: 100%;
    background: none;
    border: none;
    border-top: 1px solid var(--border-color, #242830);
    font-size: 0.68rem;
    color: #ff5252;
    font-weight: 700;
    padding: 3px 0 5px;
    cursor: pointer;
    text-align: center;
    transition: background 0.15s;
}
.neon-remove-btn:hover { background: rgba(255, 82, 82, 0.08); }

/* ── Stats bar ──────────────────────────────────── */
.neon-stats {
    display: flex;
    border-top: 1px solid var(--border-color, #242830);
    background: rgba(var(--primary-rgb, 99,102,241), 0.02);
    position: relative;
    z-index: 1;
    margin-top: 0.5rem;
}
.neon-stat {
    flex: 1;
    text-align: center;
    padding: 0.55rem 0.5rem;
    border-right: 1px solid var(--border-color, #242830);
}
.neon-stat:last-child { border-right: none; }
.neon-stat-num {
    font-size: 1.1rem;
    font-weight: 800;
    color: var(--primary-color, #6366f1);
    line-height: 1;
}
.neon-stat-num.neon-success { color: #3dffb0; }
.neon-stat-num.neon-danger  { color: #ff5252; }
.neon-stat-label {
    font-size: 0.6rem;
    color: var(--text-muted, #6b7280);
    letter-spacing: 0.1em;
    margin-top: 2px;
}

/* ── Luxury input group (date fields) ───────────── */
.luxury-input-group {
    background: var(--bg-color);
    border: 1.5px solid var(--border-color);
    border-radius: 14px;
    transition: all 0.2s;
}
.luxury-input-group:focus-within {
    border-color: var(--primary-color);
    background: var(--card-bg);
    box-shadow: 0 4px 12px rgba(0,0,0,0.03);
}
</style>