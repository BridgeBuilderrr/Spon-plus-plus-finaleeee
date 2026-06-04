<!-- Spon++ Modal Activities -->

<!-- Create Assignment Modal -->
<div class="modal fade" id="createAssignmentModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content overflow-hidden">
            <div class="modal-header border-0 p-5 pb-2">
                <h3 class="fw-extrabold text-main m-0 d-flex align-items-center gap-3">
                    <div class="p-2 bg-primary-soft rounded-3 text-primary">
                        <i data-lucide="file-plus" size="24"></i>
                    </div>
                    Assign New Task
                </h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('assignments.store', $classroom) }}" method="POST" id="form-assignment">
                @csrf
                <div class="modal-body p-5 pt-3">
                    <div class="mb-4">
                        <label class="form-label">Task Title</label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. Weekly Reflection" required autofocus>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Detailed Instructions</label>
                        <textarea name="description" id="assignment-editor" class="form-control"></textarea>
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Activation Date (Optional)</label>
                            <div class="input-group luxury-input-group">
                                <span class="input-group-text border-0 bg-transparent ps-3"><i data-lucide="calendar" size="18" class="text-primary"></i></span>
                                <input type="datetime-local" name="open_date" class="form-control border-0 bg-transparent ps-2 py-3 date-min-now">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Submission Deadline</label>
                            <div class="input-group luxury-input-group">
                                <span class="input-group-text border-0 bg-transparent ps-3"><i data-lucide="clock" size="18" class="text-danger"></i></span>
                                <input type="datetime-local" name="due_date" class="form-control border-0 bg-transparent ps-2 py-3 date-min-now" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label">Reference Material (Max 10 files, 50MB/each)</label>
                        <div class="neon-drop-card" id="drop-card-assignment">
                            <div id="dz-assignment" class="neon-dropzone"></div>
                            <div class="neon-stats" id="stats-assignment">
                                <div class="neon-stat">
                                    <div class="neon-stat-num" id="count-assignment-total">0</div>
                                    <div class="neon-stat-label">FILES</div>
                                </div>
                                <div class="neon-stat">
                                    <div class="neon-stat-num neon-success" id="count-assignment-success">0</div>
                                    <div class="neon-stat-label">UPLOADED</div>
                                </div>
                                <div class="neon-stat">
                                    <div class="neon-stat-num neon-danger" id="count-assignment-error">0</div>
                                    <div class="neon-stat-label">FAILED</div>
                                </div>
                            </div>
                        </div>
                        <div id="inputs-assignment"></div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-5 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Discard</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow">Publish Assignment</button>
                </div>
            </form>
        </div>
    </div>
</div>

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
                            <div id="dz-material" class="neon-dropzone"></div>
                            <div class="neon-stats" id="stats-material">
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
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow">Save Resources</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Init TinyMCE
        if (typeof tinymce !== 'undefined') {
            tinymce.init({
                selector: '#assignment-editor',
                height: 250,
                menubar: false,
                skin: (document.body.getAttribute('data-bs-theme') === 'dark' ? "oxide-dark" : "oxide"),
                content_css: (document.body.getAttribute('data-bs-theme') === 'dark' ? "dark" : "default"),
                plugins: 'lists link emoticons image code',
                toolbar: 'bold italic underline | numlist bullist | link image emoticons | code',
                setup: editor => editor.on('change', () => editor.save())
            });
        }

        // Neon Dropzone Init
        const initNeonDZ = (dzSelector, cardId, prefix) => {
            const el = document.querySelector(dzSelector);
            if (!el || el.dropzone) return;

            let total = 0, success = 0, error = 0;
            const card = document.getElementById(cardId);
            const inputsContainer = document.getElementById('inputs-' + prefix);

            const dz = new Dropzone(dzSelector, {
                url: "{{ route('upload') }}",
                maxFilesize: 50,
                maxFiles: 10,
                autoProcessQueue: true,
                addRemoveLinks: true,
                dictRemoveFile: "✕ Remove",
                dictDefaultMessage: '',
                headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                paramName: "file",
                parallelUploads: 2,
                previewTemplate: `
                    <div class="neon-preview">
                        <img data-dz-thumbnail />
                        <div class="neon-preview-details">
                            <div class="neon-preview-name"><span data-dz-name></span></div>
                            <div class="neon-preview-size" data-dz-size></div>
                        </div>
                        <div class="neon-progress-bar"><span class="neon-progress-fill" data-dz-uploadprogress></span></div>
                        <div class="neon-success-mark">✔</div>
                        <div class="neon-error-mark">✘</div>
                        <div class="neon-error-msg"><span data-dz-errormessage></span></div>
                        <a class="dz-remove" href="javascript:undefined;" data-dz-remove>✕ Remove</a>
                    </div>
                `
            });

            // Inject custom message safely
            el.insertAdjacentHTML('afterbegin', `<div class="neon-dz-message">
                <div class="neon-icon-wrap">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 16v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2"/>
                        <polyline points="16 12 12 8 8 12"/>
                        <line x1="12" y1="8" x2="12" y2="20"/>
                    </svg>
                </div>
                <h6 class="mb-1 fw-bold">Drop files here</h6>
                <p class="neon-dz-sub">or <span class="neon-browse-btn">browse files</span></p>
            </div>`);

            dz.on('dragenter', () => card.classList.add('neon-drag-active'));
            dz.on('dragleave', () => card.classList.remove('neon-drag-active'));
            dz.on('drop', () => card.classList.remove('neon-drag-active'));

            dz.on('addedfile', () => {
                total++;
                document.getElementById(\`count-\${prefix}-total\`).textContent = total;
            });

            dz.on('success', (file, response) => {
                success++;
                document.getElementById(\`count-\${prefix}-success\`).textContent = success;
                const hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = 'files[]';
                hidden.value = JSON.stringify(response);
                hidden.id = 'file-input-' + file.upload.uuid;
                inputsContainer.appendChild(hidden);
                showToast(\`✔ "\${file.name}" uploaded\`, 'success');
            });

            dz.on('error', (file, errorMsg) => {
                error++;
                document.getElementById(\`count-\${prefix}-error\`).textContent = error;
                showToast(typeof errorMsg === 'string' ? errorMsg : errorMsg.message, 'error');
            });

            dz.on('removedfile', (file) => {
                total = Math.max(0, total - 1);
                document.getElementById(\`count-\${prefix}-total\`).textContent = total;
                const hidden = document.getElementById('file-input-' + file.upload.uuid);
                if (hidden) hidden.remove();
            });

            return dz;
        };

        // Initialize for Create modals
        initNeonDZ('#dz-assignment', 'drop-card-assignment', 'assignment');
        initNeonDZ('#dz-material', 'drop-card-material', 'material');

        lucide.createIcons();
    });
</script>

<style>
    /* === NEON DROPZONE === */
    .neon-drop-card {
        background: var(--bg-color, #131519);
        border: 2px dashed var(--border-color, #242830);
        border-radius: 16px;
        overflow: hidden;
        transition: border-color 0.25s, background 0.25s;
        position: relative;
    }
    .neon-drop-card::after {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(ellipse at 50% 0%, rgba(var(--primary-rgb, 99,102,241), 0.05) 0%, transparent 70%);
        pointer-events: none;
    }
    .neon-drop-card.neon-drag-active {
        border-color: var(--primary-color);
        background: rgba(var(--primary-rgb, 99,102,241), 0.04);
    }

    .neon-dropzone {
        background: transparent !important;
        border: none !important;
        min-height: 160px;
        display: flex;
        flex-wrap: wrap;
        align-items: flex-start;
        padding: 1.25rem;
        cursor: pointer;
        position: relative;
        z-index: 1;
    }
    .neon-dropzone.dz-drag-hover { background: rgba(var(--primary-rgb, 99,102,241), 0.04) !important; }

    /* Message */
    .neon-dz-message {
        width: 100%;
        text-align: center;
        padding: 1.5rem 1rem;
        pointer-events: none;
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
    .neon-dropzone:hover .neon-icon-wrap {
        transform: translateY(-4px);
        background: rgba(var(--primary-rgb, 99,102,241), 0.18);
    }
    .neon-icon-wrap svg {
        width: 24px;
        height: 24px;
        stroke: var(--primary-color, #6366f1);
    }
    .neon-dz-sub { font-size: 0.82rem; color: var(--text-muted, #6b7280); margin: 0; }
    .neon-browse-btn {
        color: var(--primary-color, #6366f1);
        font-weight: 700;
        cursor: pointer;
        text-decoration: underline;
        text-underline-offset: 2px;
        pointer-events: all;
    }

    /* Preview items */
    .neon-preview {
        background: var(--card-bg, #1e2025);
        border: 1px solid var(--border-color, #242830);
        border-radius: 12px;
        margin: 0.4rem;
        width: 150px;
        overflow: hidden;
        position: relative;
        flex-shrink: 0;
    }
    .neon-preview img[data-dz-thumbnail] {
        width: 100%;
        height: 72px;
        object-fit: cover;
        display: block;
    }
    .neon-preview-details { padding: 0.5rem 0.6rem 0.4rem; }
    .neon-preview-name {
        font-size: 0.72rem;
        color: var(--text-color, #e8eaf0);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .neon-preview-size { font-size: 0.68rem; color: var(--text-muted, #6b7280); margin-top: 2px; }
    .neon-progress-bar { height: 3px; background: var(--border-color, #242830); }
    .neon-progress-fill {
        display: block;
        height: 100%;
        background: var(--primary-color, #6366f1);
        transition: width 0.3s ease;
        width: 0;
    }
    .neon-preview.dz-success .neon-progress-fill { background: #3dffb0; }
    .neon-success-mark, .neon-error-mark {
        display: none;
        position: absolute;
        top: 0.4rem;
        right: 0.4rem;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        font-size: 0.6rem;
        font-weight: 700;
        align-items: center;
        justify-content: center;
    }
    .neon-preview.dz-success .neon-success-mark { display: flex; background: #3dffb0; color: #0b0c0f; }
    .neon-preview.dz-error .neon-error-mark { display: flex; background: #ff5252; color: #fff; }
    .neon-error-msg { display: none; font-size: 0.68rem; color: #ff5252; padding: 0 0.6rem 0.4rem; }
    .neon-preview.dz-error .neon-error-msg { display: block; }
    .neon-preview .dz-remove {
        display: block;
        text-align: center;
        font-size: 0.68rem;
        color: #ff5252 !important;
        font-weight: 700;
        text-decoration: none !important;
        padding: 3px 0 5px;
        border-top: 1px solid var(--border-color, #242830);
    }

    /* Stats bar */
    .neon-stats {
        display: flex;
        border-top: 1px solid var(--border-color, #242830);
        background: rgba(var(--primary-rgb, 99,102,241), 0.02);
        position: relative;
        z-index: 1;
    }
    .neon-stat {
        flex: 1;
        text-align: center;
        padding: 0.6rem 0.5rem;
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
    .neon-stat-num.neon-danger { color: #ff5252; }
    .neon-stat-label {
        font-size: 0.62rem;
        color: var(--text-muted, #6b7280);
        letter-spacing: 0.1em;
        margin-top: 2px;
    }
</style>