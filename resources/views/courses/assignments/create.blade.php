@extends('layouts.app')

@section('content')
<style>
.type-pill {
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    border: 2px solid var(--bs-border-color) !important;
    background: transparent;
    color: var(--bs-secondary-color);
}
.type-pill:hover {
    border-color: var(--bs-primary) !important;
    color: var(--bs-primary);
}
.type-pill.active-pill {
    border-color: var(--bs-primary) !important;
    background: rgba(99, 102, 241, 0.08) !important;
    color: var(--bs-primary) !important;
}
.sortable-ghost {
    opacity: 0.4;
    border: 2px dashed var(--bs-primary) !important;
    background: rgba(99, 102, 241, 0.04) !important;
}
.sortable-chosen {
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1) !important;
    transform: scale(1.01);
}
</style>

    <!-- Breadcrumbs -->
    <div class="row g-4 mb-4 align-items-center" style="position: relative; z-index: 1025;">
        <div class="col-md-6">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb fw-bold mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('courses.index') }}" class="text-decoration-none text-muted">My Spaces</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('courses.show', $classroom) }}" class="text-decoration-none text-muted">{{ $classroom->title }}</a></li>
                    <li class="breadcrumb-item active text-primary" aria-current="page" id="breadcrumb-label">Create Exercise</li>
                </ol>
            </nav>
            <h2 class="fw-extrabold m-0 text-main" id="page-heading">Create New Exercise</h2>
        </div>
    </div>

    <!-- Main Creation Form -->
    <form action="{{ route('assignments.store', $classroom) }}" method="POST" id="form-assignment">
        @csrf
        <div class="row g-4">
            <!-- Left Panel: Details & Questions -->
            <div class="col-lg-8">
                <!-- Header Info Card -->
                <div class="card border rounded-4 p-4 mb-4 shadow-sm">
                    <div class="mb-4">
                        <label class="form-label fw-bold text-main">Title</label>
                        <input type="text" name="title" class="form-control form-control-lg fw-semibold border-0 border-bottom rounded-0 px-0" placeholder="Untitled Exercise" required autofocus>
                    </div>
                    <div class="mb-0">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label fw-bold text-main m-0">Description</label>
                            <div class="d-flex align-items-center" id="attachment-trigger-wrapper">
                                <button type="button" class="btn btn-sm btn-light rounded-pill px-3 border d-flex align-items-center gap-1 text-primary fw-bold" onclick="document.getElementById('attachment-input').click()">
                                    <i data-lucide="paperclip" size="14"></i> Add material
                                </button>
                                <input type="file" id="attachment-input" class="d-none" onchange="window.handleAttachmentUpload(this)">
                            </div>
                        </div>
                        <textarea name="description" id="assignment-editor" class="form-control" rows="4" placeholder="Description of the activity..."></textarea>
                        
                        <div id="attachment-preview-container" class="mt-3 d-none">
                            <div class="d-flex align-items-center justify-content-between p-3 rounded-3 border bg-light">
                                <div class="d-flex align-items-center gap-2">
                                    <i data-lucide="file-text" class="text-primary" size="20"></i>
                                    <div>
                                        <div class="fw-semibold text-main small" id="attachment-name">material.pdf</div>
                                        <div class="text-muted smallest" id="attachment-size">0 KB</div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-link text-danger text-decoration-none fw-bold p-0" onclick="window.removeAttachment()">
                                    Remove
                                </button>
                            </div>
                        </div>
                        <div id="attachment-hidden-input-container"></div>
                    </div>
                </div>

                <!-- Multiple Choice (Quiz) Section -->
                <div class="mb-4 d-none" id="mc-questions-section">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="m-0 fw-bold text-main d-flex align-items-center gap-2">
                            <i data-lucide="list-checks" class="text-primary"></i> Quiz Questions
                        </h4>
                        <button type="button" class="btn btn-primary rounded-pill px-3 shadow-sm" id="btn-add-question" onclick="window.addQuestionField()">
                            <i data-lucide="plus" size="14" class="me-1"></i> Add Question
                        </button>
                    </div>
                    <div id="questions-container" class="d-flex flex-column gap-3">
                        <!-- Dynamic question cards will be added here -->
                    </div>
                </div>
            </div>

            <!-- Right Panel: Settings -->
            <div class="col-lg-4">
                <div class="card border rounded-4 p-4 mb-4 shadow-sm position-sticky" style="top: 100px;">
                    <h5 class="fw-bold text-main mb-4 d-flex align-items-center gap-2">
                        <i data-lucide="sliders" class="text-primary" size="20"></i> Settings
                    </h5>

                    <!-- Type selector — top of settings -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Activity Type</label>
                        <div class="d-flex gap-2">
                            <label class="flex-fill text-center border rounded-3 p-2 type-pill" style="cursor:pointer;">
                                <input type="radio" name="assignment_type" value="essay" class="d-none type-radio" id="type-exercise">
                                <i data-lucide="pencil-line" size="18" class="d-block mx-auto mb-1"></i>
                                <span class="small fw-bold">Exercise</span>
                            </label>
                            <label class="flex-fill text-center border rounded-3 p-2 type-pill" style="cursor:pointer;">
                                <input type="radio" name="assignment_type" value="pilihan_ganda" class="d-none type-radio" id="type-quiz">
                                <i data-lucide="list-checks" size="18" class="d-block mx-auto mb-1"></i>
                                <span class="small fw-bold">Quiz</span>
                            </label>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Activation Date (Optional)</label>
                        <div class="row g-2">
                            <div class="col-7">
                                <div class="input-group luxury-input-group">
                                    <span class="input-group-text border-0 bg-transparent ps-3"><i data-lucide="calendar" size="16" class="text-primary"></i></span>
                                    <input type="date" id="open_date_date" class="form-control border-0 bg-transparent ps-1 py-2">
                                </div>
                            </div>
                            <div class="col-5">
                                <div class="input-group luxury-input-group">
                                    <span class="input-group-text border-0 bg-transparent ps-2"><i data-lucide="clock" size="16" class="text-primary"></i></span>
                                    <input type="time" id="open_date_time" class="form-control border-0 bg-transparent ps-1 py-2" value="00:00">
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="open_date" id="open_date_hidden">
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Submission Deadline <span class="text-danger">*</span></label>
                        <div class="row g-2">
                            <div class="col-7">
                                <div class="input-group luxury-input-group">
                                    <span class="input-group-text border-0 bg-transparent ps-3"><i data-lucide="calendar" size="16" class="text-danger"></i></span>
                                    <input type="date" id="due_date_date" class="form-control border-0 bg-transparent ps-1 py-2" required>
                                </div>
                            </div>
                            <div class="col-5">
                                <div class="input-group luxury-input-group">
                                    <span class="input-group-text border-0 bg-transparent ps-2"><i data-lucide="clock" size="16" class="text-danger"></i></span>
                                    <input type="time" id="due_date_time" class="form-control border-0 bg-transparent ps-1 py-2" value="23:59">
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="due_date" id="due_date_hidden">
                    </div>

                    <div class="d-flex flex-column gap-2 mt-2">
                        <button type="submit" id="btn-publish" class="btn btn-primary rounded-pill w-100 py-3 fw-bold shadow">
                            <i data-lucide="send" size="18" class="me-2"></i> Publish Exercise
                        </button>
                        <a href="{{ route('courses.show', $classroom) }}" class="btn btn-light rounded-pill w-100 py-3 fw-bold border text-center">
                            Discard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <style>
        /* ── Drag-handle ────────────────────────────────── */
        .q-drag-handle {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            padding: 4px 0 8px;
            cursor: move;
            color: var(--text-muted, #9ca3af);
            opacity: 0.35;
            transition: opacity 0.2s;
            user-select: none;
            touch-action: none;
            line-height: 0;
        }
        .question-card:hover .q-drag-handle { opacity: 1; }
        .question-card.sortable-ghost {
            opacity: 0.4;
            background: rgba(var(--primary-rgb, 99,102,241), 0.05) !important;
            border: 2px dashed var(--primary-color) !important;
        }
        .question-card.sortable-chosen { box-shadow: 0 20px 40px -10px rgba(0,0,0,0.18) !important; }
        /* ── Question card base ─────────────────────────── */
        .question-card {
            transition: box-shadow 0.3s cubic-bezier(0.16, 1, 0.3, 1),
                        border-color 0.3s;
            border-left: 4px solid transparent !important;
        }
        .question-card.active-card {
            border-left: 4px solid var(--primary-color) !important;
            box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.08) !important;
        }
        .luxury-input-group {
            background: var(--bg-color);
            border: 1.5px solid var(--border-color);
            border-radius: 14px;
            transition: all 0.3s ease;
        }
        .luxury-input-group:focus-within {
            border-color: var(--primary-color);
            background: var(--card-bg);
            box-shadow: 0 0 0 4px rgba(var(--primary-rgb), 0.1);
        }
        /* Custom switches */
        .form-check-input {
            cursor: pointer;
        }
        /* Neon Dropzone Premium styling compatibility */
        .neon-drop-card {
            border: 2px dashed var(--border-color);
            border-radius: 18px;
            padding: 32px 20px;
            text-align: center;
            background: var(--bg-color);
            cursor: pointer;
            transition: var(--transition);
            position: relative;
        }
        .neon-drop-card:hover, .neon-drop-card.neon-drag-active {
            border-color: var(--primary-color);
            background: var(--sidebar-hover);
        }
        .neon-dz-message svg {
            width: 44px;
            height: 44px;
            stroke: var(--primary-color);
            margin-bottom: 12px;
        }
        .neon-browse-btn {
            color: var(--primary-color);
            font-weight: 700;
            text-decoration: underline;
        }
        .neon-previews-wrap {
            margin-top: 16px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .neon-preview {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 12px;
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 14px;
            text-align: left;
            position: relative;
        }
        .neon-preview img {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            object-fit: cover;
        }
        .neon-preview-details {
            flex-grow: 1;
            min-width: 0;
        }
        .neon-preview-name {
            font-weight: 700;
            font-size: 0.85rem;
            color: var(--text-color);
            text-truncate: true;
        }
        .neon-preview-size {
            font-size: 0.75rem;
            color: var(--text-muted);
        }
        .neon-remove-btn {
            background: none;
            border: none;
            color: var(--text-muted);
            font-weight: 700;
            cursor: pointer;
            transition: var(--transition);
        }
        .neon-remove-btn:hover {
            color: #ef4444;
        }
        .neon-stats {
            display: flex;
            justify-content: space-around;
            margin-top: 16px;
            border-top: 1px solid var(--border-color);
            padding-top: 12px;
        }
        .neon-stat-num {
            font-weight: 800;
            font-size: 1.1rem;
        }
        .neon-stat-label {
            font-size: 0.65rem;
            color: var(--text-muted);
            font-weight: 700;
        }
        .neon-dropzone-hidden {
            display: none;
        }
    </style>

    <script>
        window._dzInstances = window._dzInstances || {};

        function initNeonDZ(prefix, cardId, previewsId, hiddenTriggerId, inputsId, msgId) {
            if (typeof Dropzone === 'undefined') return;

            if (window._dzInstances[prefix]) {
                try { window._dzInstances[prefix].destroy(); } catch(e) {}
                delete window._dzInstances[prefix];
            }

            const card = document.getElementById(cardId);
            const previewsEl = document.getElementById(previewsId);
            const triggerEl = document.getElementById(hiddenTriggerId);
            const inputsCont = document.getElementById(inputsId);
            const msgEl = document.getElementById(msgId);

            if (!card || !triggerEl) return;

            let total = 0, success = 0, error = 0;
            const updateCounters = () => {
                const totEl = document.getElementById(`count-${prefix}-total`);
                const sucEl = document.getElementById(`count-${prefix}-success`);
                const errEl = document.getElementById(`count-${prefix}-error`);
                if (totEl) totEl.textContent = total;
                if (sucEl) sucEl.textContent = success;
                if (errEl) errEl.textContent = error;
            };

            const dz = new Dropzone('#' + hiddenTriggerId, {
                url: "{{ route('upload') }}",
                maxFilesize: 50,
                maxFiles: 10,
                autoProcessQueue: true,
                addRemoveLinks: false,
                headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                paramName: 'file',
                parallelUploads: 2,
                clickable: '#' + cardId,
                previewsContainer: '#' + previewsId,
                previewTemplate: `
                    <div class="neon-preview">
                        <div class="neon-preview-details">
                            <div class="neon-preview-name" data-dz-name></div>
                            <div class="neon-preview-size" data-dz-size></div>
                        </div>
                        <button type="button" class="neon-remove-btn" data-dz-remove>✕</button>
                    </div>
                `
            });

            window._dzInstances[prefix] = dz;

            dz.on('addedfile', () => {
                total++;
                updateCounters();
                if (msgEl) msgEl.style.display = 'none';
            });

            dz.on('success', (file, response) => {
                success++;
                updateCounters();
                
                const hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = 'files[]';
                hidden.value = JSON.stringify(response);
                hidden.dataset.uuid = file.upload.uuid;
                inputsCont.appendChild(hidden);

                if (typeof showToast === 'function') showToast(`✔ "${file.name}" uploaded`, 'success');
            });

            dz.on('error', (file, message) => {
                error++;
                updateCounters();
                let msg = typeof message === 'string' ? message : (message.error || 'Upload failed');
                if (typeof showToast === 'function') showToast(msg, 'error');
            });

            dz.on('removedfile', (file) => {
                total--;
                if (file.status === 'success') success--;
                else error--;
                updateCounters();

                const input = inputsCont.querySelector(`input[data-uuid="${file.upload?.uuid}"]`);
                if (input) input.remove();

                if (total === 0 && msgEl) msgEl.style.display = 'block';
            });
        }

        // TinyMCE
        if (typeof tinymce !== 'undefined') {
            tinymce.init({
                selector: '#assignment-editor',
                height: 300,
                menubar: false,
                skin: (document.body.getAttribute('data-bs-theme') === 'dark' ? 'oxide-dark' : 'oxide'),
                content_css: (document.body.getAttribute('data-bs-theme') === 'dark' ? 'dark' : 'default'),
                plugins: 'link lists media table code wordcount',
                toolbar: 'undo redo | bold italic | alignleft aligncenter alignright | bullist numlist | link code',
                setup: function (editor) {
                    editor.on('change', function () {
                        editor.save();
                    });
                }
            });
        }

        // Assignment Type Change
        const typeSelect = document.getElementById('assignment-type-select');
        if (typeSelect) {
            typeSelect.addEventListener('change', function() {
                const mcSection = document.getElementById('mc-questions-section');
                if (this.value === 'pilihan_ganda') {
                    mcSection.classList.remove('d-none');
                    const container = document.getElementById('questions-container');
                    if (container.children.length === 0) {
                        window.addQuestionField();
                    }
                } else {
                    mcSection.classList.add('d-none');
                }
            });
        }

    </script>
    @include('courses.assignments.question_builder_js')
    <script>












        document.addEventListener('DOMContentLoaded', function () {
            // ── SortableJS drag-and-drop ─────────────────────────────────────
            if (typeof Sortable !== 'undefined') {
                Sortable.create(document.getElementById('questions-container'), {
                    animation: 180,
                    easing: 'cubic-bezier(0.16, 1, 0.3, 1)',
                    handle: '.q-drag-handle',
                    ghostClass: 'sortable-ghost',
                    chosenClass: 'sortable-chosen',
                    dragClass: 'sortable-drag',
                    onEnd: function() {
                        window.renumberQuestions();
                    }
                });
            }

            const typeRadios = document.querySelectorAll('.type-radio');
            const attachmentTriggerWrapper = document.getElementById('attachment-trigger-wrapper');
            const mcQuestionsSection = document.getElementById('mc-questions-section');
            const pageHeading = document.getElementById('page-heading');
            const breadcrumbLabel = document.getElementById('breadcrumb-label');
            const btnPublish = document.getElementById('btn-publish');
            const titleInput = document.querySelector('input[name="title"]');

            // Set Activation Date and Submission Deadline constraints and defaults
            const now = new Date();
            const yyyy = now.getFullYear();
            const mm = String(now.getMonth() + 1).padStart(2, '0');
            const dd = String(now.getDate()).padStart(2, '0');
            const todayStr = `${yyyy}-${mm}-${dd}`;

            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const timeStr = `${hours}:${minutes}`;

            // Set min attribute for dates
            const openDateInput = document.getElementById('open_date_date');
            const dueDateInput = document.getElementById('due_date_date');
            const openTimeInput = document.getElementById('open_date_time');

            // Track whether teacher manually changed the Activation Date/Time
            let openDateManuallyChanged = false;
            if (openDateInput) {
                openDateInput.addEventListener('input', () => { openDateManuallyChanged = true; });
                openDateInput.addEventListener('change', () => { openDateManuallyChanged = true; });
            }
            if (openTimeInput) {
                openTimeInput.addEventListener('input', () => { openDateManuallyChanged = true; });
                openTimeInput.addEventListener('change', () => { openDateManuallyChanged = true; });
            }

            // Helper: get current date/time parts
            function getNowParts() {
                const n = new Date();
                return {
                    date: `${n.getFullYear()}-${String(n.getMonth()+1).padStart(2,'0')}-${String(n.getDate()).padStart(2,'0')}`,
                    time: `${String(n.getHours()).padStart(2,'0')}:${String(n.getMinutes()).padStart(2,'0')}`
                };
            }

            // Initial fill
            (function initDateDefaults() {
                const { date, time } = getNowParts();
                if (openDateInput) openDateInput.min = date;
                if (dueDateInput)  dueDateInput.min  = date;
                if (openDateInput && !openDateInput.value) openDateInput.value = date;
                if (openTimeInput && (!openTimeInput.value || openTimeInput.value === '00:00')) {
                    openTimeInput.value = time;
                }
            })();

            // Live-clock: aligned to the start of each new minute so there is zero lag.
            // 1) Calculate ms remaining until the next full minute.
            // 2) Fire once at that exact moment, then repeat every 60s.
            function tickClock() {
                const { date, time } = getNowParts();
                // Always keep min current so past times become unselectable
                if (openDateInput) openDateInput.min = date;
                if (dueDateInput)  dueDateInput.min  = date;
                // Auto-advance Activation Date/Time only if user hasn't touched them
                if (!openDateManuallyChanged) {
                    if (openDateInput) openDateInput.value = date;
                    if (openTimeInput) openTimeInput.value = time;
                }
            }

            (function startAlignedClock() {
                const now = new Date();
                // ms until the next full minute (e.g. 01:32:28 → wait 32 000 ms)
                const msUntilNextMinute = (60 - now.getSeconds()) * 1000 - now.getMilliseconds();
                setTimeout(function() {
                    tickClock();                         // fire right at the minute boundary
                    setInterval(tickClock, 60000);       // then every exact 60 s
                }, msUntilNextMinute);
            })();

            function syncType(val) {
                typeRadios.forEach(radio => {
                    const pill = radio.closest('.type-pill');
                    if (radio.value === val) {
                        radio.checked = true;
                        pill?.classList.add('active-pill');
                    } else {
                        pill?.classList.remove('active-pill');
                    }
                });

                if (val === 'pilihan_ganda') {
                    attachmentTriggerWrapper?.classList.add('d-none');
                    document.getElementById('attachment-preview-container')?.classList.add('d-none');
                    mcQuestionsSection?.classList.remove('d-none');
                    if (pageHeading) pageHeading.textContent = 'Create New Quiz';
                    if (breadcrumbLabel) breadcrumbLabel.textContent = 'Create Quiz';
                    if (btnPublish) btnPublish.innerHTML = '<i data-lucide="send" size="18" class="me-2"></i> Publish Quiz';
                    if (titleInput && (titleInput.value === '' || titleInput.value === 'Untitled Exercise')) {
                        titleInput.placeholder = 'Untitled Quiz';
                    }

                    const container = document.getElementById('questions-container');
                    if (container && container.children.length === 0) {
                        window.addQuestionField();
                    }
                } else {
                    attachmentTriggerWrapper?.classList.remove('d-none');
                    const hiddenInput = document.querySelector('#attachment-hidden-input-container input');
                    if (hiddenInput) {
                        document.getElementById('attachment-preview-container')?.classList.remove('d-none');
                    }
                    mcQuestionsSection?.classList.add('d-none');
                    if (pageHeading) pageHeading.textContent = 'Create New Exercise';
                    if (breadcrumbLabel) breadcrumbLabel.textContent = 'Create Exercise';
                    if (btnPublish) btnPublish.innerHTML = '<i data-lucide="send" size="18" class="me-2"></i> Publish Exercise';
                    if (titleInput && (titleInput.value === '' || titleInput.value === 'Untitled Quiz')) {
                        titleInput.placeholder = 'Untitled Exercise';
                    }
                }
                if (typeof lucide !== 'undefined') lucide.createIcons();
            }

            typeRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.checked) syncType(this.value);
                });
            });

            const urlParams = new URLSearchParams(window.location.search);
            const typeParam = urlParams.get('type');
            if (typeParam) {
                syncType(typeParam === 'pilihan_ganda' ? 'pilihan_ganda' : 'essay');
            } else {
                const checkedRadio = document.querySelector('.type-radio:checked');
                syncType(checkedRadio ? checkedRadio.value : 'essay');
            }
        });

        // Form Submit Validation + date combiner
        const formAssignment = document.getElementById('form-assignment');
        if (formAssignment) {
            formAssignment.addEventListener('submit', function(e) {
                // ── Combine split date+time fields ──
                const openD = document.getElementById('open_date_date')?.value;
                const openT = document.getElementById('open_date_time')?.value || '00:00';
                const dueD  = document.getElementById('due_date_date')?.value;
                const dueT  = document.getElementById('due_date_time')?.value  || '23:59';

                const nowSubmit = new Date();

                if (openD) {
                    const openDateTime = new Date(`${openD}T${openT}`);
                    const nowCheck = new Date();
                    // If slightly past (e.g. user took time filling the form), snap forward to now
                    if (openDateTime < nowCheck) {
                        const { date: snapDate, time: snapTime } = (function() {
                            const n = new Date();
                            return {
                                date: `${n.getFullYear()}-${String(n.getMonth()+1).padStart(2,'0')}-${String(n.getDate()).padStart(2,'0')}`,
                                time: `${String(n.getHours()).padStart(2,'0')}:${String(n.getMinutes()).padStart(2,'0')}`
                            };
                        })();
                        document.getElementById('open_date_hidden').value = `${snapDate} ${snapTime}:00`;
                    } else {
                        document.getElementById('open_date_hidden').value = `${openD} ${openT}:00`;
                    }
                }
                if (dueD) {
                    const dueDateTime = new Date(`${dueD}T${dueT}`);
                    if (dueDateTime < nowSubmit) {
                        e.preventDefault();
                        if (typeof showToast === 'function') showToast('Submission Deadline tidak boleh di masa lalu.', 'error');
                        document.getElementById('due_date_time')?.focus();
                        return;
                    }
                    document.getElementById('due_date_hidden').value = `${dueD} ${dueT}:00`;
                } else {
                    e.preventDefault();
                    if (typeof showToast === 'function') showToast('Submission Deadline wajib diisi.', 'error');
                    document.getElementById('due_date_date')?.focus();
                    return;
                }

                const activeType = document.querySelector('.type-radio:checked')?.value;
                if (activeType === 'pilihan_ganda') {
                    const cards = document.querySelectorAll('.question-card');
                    if (cards.length === 0) {
                        e.preventDefault();
                        if (typeof showToast === 'function') {
                            showToast('Harap tambahkan setidaknya satu pertanyaan.', 'error');
                        } else {
                            alert('Harap tambahkan setidaknya satu pertanyaan.');
                        }
                        return;
                    }

                    let valid = true;
                    cards.forEach((card) => {
                        const qType = card.querySelector('.q-type-select')?.value;
                        if (qType === 'text') {
                            card.style.removeProperty('border-color');
                            return; // text question always valid
                        }
                        const correctInputs = card.querySelectorAll('.opt-correct-input:checked');
                        if (correctInputs.length === 0) {
                            valid = false;
                            card.style.setProperty('border-color', 'var(--bs-danger)', 'important');
                            card.classList.add('shadow-sm');
                            card.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        } else {
                            card.style.removeProperty('border-color');
                        }
                    });

                    if (!valid) {
                        e.preventDefault();
                        if (typeof showToast === 'function') {
                            showToast('Harap tentukan kunci jawaban (jawaban yang benar) untuk setiap pertanyaan.', 'error');
                        } else {
                            alert('Harap tentukan kunci jawaban (jawaban yang benar) untuk setiap pertanyaan.');
                        }
                    }
                }
            });
        }
    </script>

    {{-- SortableJS for drag-and-drop question reordering --}}
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js"></script>
    <script>
        // Re-init Sortable after SortableJS loads (the DOMContentLoaded above may have
        // already fired, so we check and init here as a fallback).
        (function waitSortable() {
            if (typeof Sortable === 'undefined') {
                setTimeout(waitSortable, 50);
                return;
            }
            const container = document.getElementById('questions-container');
            if (container && !container._sortableInitialized) {
                container._sortableInitialized = true;
                Sortable.create(container, {
                    animation: 180,
                    easing: 'cubic-bezier(0.16, 1, 0.3, 1)',
                    handle: '.q-drag-handle',
                    ghostClass: 'sortable-ghost',
                    chosenClass: 'sortable-chosen',
                    onEnd: function() { window.renumberQuestions(); }
                });
            }
        })();
    </script>
@endsection
