@extends('layouts.app')

@section('content')
@php
    $isQuiz = $assignment->assignment_type === 'pilihan_ganda';
    $existingQuestions = $assignment->questions ?? [];
    $openDate = $assignment->open_date ? \Carbon\Carbon::parse($assignment->open_date) : null;
    $dueDate  = $assignment->due_date  ? \Carbon\Carbon::parse($assignment->due_date)  : null;
@endphp
<style>
.type-pill { transition: all 0.2s cubic-bezier(0.16,1,0.3,1); border: 2px solid var(--bs-border-color) !important; background: transparent; color: var(--bs-secondary-color); }
.type-pill:hover { border-color: var(--bs-primary) !important; color: var(--bs-primary); }
.type-pill.active-pill { border-color: var(--bs-primary) !important; background: rgba(99,102,241,0.08) !important; color: var(--bs-primary) !important; }
.sortable-ghost { opacity: 0.4; border: 2px dashed var(--bs-primary) !important; background: rgba(99,102,241,0.04) !important; }
.sortable-chosen { box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1) !important; transform: scale(1.01); }
</style>

    <!-- Breadcrumbs -->
    <div class="row g-4 mb-4 align-items-center">
        <div class="col-md-6">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb fw-bold mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('courses.index') }}" class="text-decoration-none text-muted">My Spaces</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('courses.show', $classroom) }}" class="text-decoration-none text-muted">{{ $classroom->title }}</a></li>
                    <li class="breadcrumb-item active text-primary" id="breadcrumb-label">Edit {{ $isQuiz ? 'Quiz' : 'Exercise' }}</li>
                </ol>
            </nav>
            <h2 class="fw-extrabold m-0 text-main" id="page-heading">Edit {{ $isQuiz ? 'Quiz' : 'Exercise' }}</h2>
        </div>
    </div>

    <form action="{{ route('assignments.update', [$classroom, $assignment]) }}" method="POST" id="form-assignment">
        @csrf
        <div class="row g-4">
            <div class="col-lg-8">
                <!-- Title + Description -->
                <div class="card border rounded-4 p-4 mb-4 shadow-sm">
                    <div class="mb-4">
                        <label class="form-label fw-bold text-main">Title</label>
                        <input type="text" name="title" value="{{ $assignment->title }}" class="form-control form-control-lg fw-semibold border-0 border-bottom rounded-0 px-0" required autofocus>
                    </div>
                    <div class="mb-0">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label fw-bold text-main m-0">Description</label>
                            <div id="attachment-trigger-wrapper" class="{{ $isQuiz ? 'd-none' : '' }}">
                                <button type="button" class="btn btn-sm btn-light rounded-pill px-3 border d-flex align-items-center gap-1 text-primary fw-bold" onclick="document.getElementById('attachment-input').click()">
                                    <i data-lucide="paperclip" size="14"></i> Add material
                                </button>
                                <input type="file" id="attachment-input" class="d-none" onchange="window.handleAttachmentUpload(this)">
                            </div>
                        </div>
                        <textarea name="description" id="assignment-editor" class="form-control" rows="4" placeholder="Description of the activity...">{{ $assignment->description }}</textarea>

                        @php $existingFiles = $assignment->files ?? []; @endphp
                        <div id="attachment-preview-container" class="mt-3 {{ count($existingFiles) ? '' : 'd-none' }}">
                            <div class="d-flex align-items-center justify-content-between p-3 rounded-3 border bg-light">
                                <div class="d-flex align-items-center gap-2">
                                    <i data-lucide="file-text" class="text-primary" size="20"></i>
                                    <div>
                                        <div class="fw-semibold text-main small" id="attachment-name">{{ count($existingFiles) ? ($existingFiles[0]['name'] ?? 'File') : '' }}</div>
                                        <div class="text-muted smallest" id="attachment-size">{{ count($existingFiles) ? round(($existingFiles[0]['size'] ?? 0)/1024, 1).' KB' : '' }}</div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-link text-danger text-decoration-none fw-bold p-0" onclick="window.removeAttachment()">Remove</button>
                            </div>
                        </div>
                        <div id="attachment-hidden-input-container">
                            @foreach($existingFiles as $ef)
                                <input type="hidden" name="files[]" value='{{ json_encode($ef) }}'>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Questions (Quiz) -->
                <div class="mb-4 {{ $isQuiz ? '' : 'd-none' }}" id="mc-questions-section">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="m-0 fw-bold text-main d-flex align-items-center gap-2">
                            <i data-lucide="list-checks" class="text-primary"></i> Quiz Questions
                        </h4>
                        <button type="button" class="btn btn-primary rounded-pill px-3 shadow-sm" onclick="window.addQuestionField()">
                            <i data-lucide="plus" size="14" class="me-1"></i> Add Question
                        </button>
                    </div>
                    <div id="questions-container" class="d-flex flex-column gap-3"></div>
                </div>
            </div>

            <!-- Settings -->
            <div class="col-lg-4">
                <div class="card border rounded-4 p-4 mb-4 shadow-sm position-sticky" style="top:100px;">
                    <h5 class="fw-bold text-main mb-4 d-flex align-items-center gap-2">
                        <i data-lucide="sliders" class="text-primary" size="20"></i> Settings
                    </h5>

                    <!-- Type selector (locked on edit) -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Activity Type</label>
                        <div class="d-flex gap-2">
                            <label class="flex-fill text-center border rounded-3 p-2 type-pill {{ !$isQuiz ? 'active-pill' : '' }}" style="cursor:pointer;">
                                <input type="radio" name="assignment_type" value="essay" class="d-none type-radio" {{ !$isQuiz ? 'checked' : '' }}>
                                <i data-lucide="pencil-line" size="18" class="d-block mx-auto mb-1"></i>
                                <span class="small fw-bold">Exercise</span>
                            </label>
                            <label class="flex-fill text-center border rounded-3 p-2 type-pill {{ $isQuiz ? 'active-pill' : '' }}" style="cursor:pointer;">
                                <input type="radio" name="assignment_type" value="pilihan_ganda" class="d-none type-radio" {{ $isQuiz ? 'checked' : '' }}>
                                <i data-lucide="list-checks" size="18" class="d-block mx-auto mb-1"></i>
                                <span class="small fw-bold">Quiz</span>
                            </label>
                        </div>
                    </div>

                    <!-- Activation Date -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Activation Date (Optional)</label>
                        <div class="row g-2">
                            <div class="col-7">
                                <div class="input-group luxury-input-group">
                                    <span class="input-group-text border-0 bg-transparent ps-3"><i data-lucide="calendar" size="16" class="text-primary"></i></span>
                                    <input type="date" id="open_date_date" class="form-control border-0 bg-transparent ps-1 py-2" value="{{ $openDate ? $openDate->format('Y-m-d') : '' }}">
                                </div>
                            </div>
                            <div class="col-5">
                                <div class="input-group luxury-input-group">
                                    <span class="input-group-text border-0 bg-transparent ps-2"><i data-lucide="clock" size="16" class="text-primary"></i></span>
                                    <input type="time" id="open_date_time" class="form-control border-0 bg-transparent ps-1 py-2" value="{{ $openDate ? $openDate->format('H:i') : '00:00' }}">
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="open_date" id="open_date_hidden" value="{{ $assignment->open_date }}">
                    </div>

                    <!-- Due Date -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Submission Deadline <span class="text-danger">*</span></label>
                        <div class="row g-2">
                            <div class="col-7">
                                <div class="input-group luxury-input-group">
                                    <span class="input-group-text border-0 bg-transparent ps-3"><i data-lucide="calendar" size="16" class="text-danger"></i></span>
                                    <input type="date" id="due_date_date" class="form-control border-0 bg-transparent ps-1 py-2" value="{{ $dueDate ? $dueDate->format('Y-m-d') : '' }}" required>
                                </div>
                            </div>
                            <div class="col-5">
                                <div class="input-group luxury-input-group">
                                    <span class="input-group-text border-0 bg-transparent ps-2"><i data-lucide="clock" size="16" class="text-danger"></i></span>
                                    <input type="time" id="due_date_time" class="form-control border-0 bg-transparent ps-1 py-2" value="{{ $dueDate ? $dueDate->format('H:i') : '23:59' }}">
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="due_date" id="due_date_hidden" value="{{ $assignment->due_date }}">
                    </div>

                    <div class="d-flex flex-column gap-2 mt-2">
                        <button type="submit" id="btn-publish" class="btn btn-primary rounded-pill w-100 py-3 fw-bold shadow">
                            <i data-lucide="save" size="18" class="me-2"></i> Save {{ $isQuiz ? 'Quiz' : 'Exercise' }}
                        </button>
                        <a href="{{ route('courses.show', $classroom) }}" class="btn btn-light rounded-pill w-100 py-3 fw-bold border text-center">Discard</a>
                    </div>
                </div>
            </div>
        </div>
    </form>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
// ── Reuse ALL the same question-builder JS from create page ──
// Inline the existing questions from PHP
const EXISTING_QUESTIONS = {!! json_encode($existingQuestions) !!};

// ── Copy all window.* helpers verbatim (addOptionFieldEx, addQuestionField, etc.) ──
// We load them from create.blade.php by including the shared partial below.
// The actual implementations are identical — only the initial data differs.

// ── Type-pill sync ──
document.addEventListener('DOMContentLoaded', function() {
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

    const typeRadios = document.querySelectorAll('.type-radio');
    const attachmentTriggerWrapper = document.getElementById('attachment-trigger-wrapper');
    const mcQuestionsSection = document.getElementById('mc-questions-section');
    const pageHeading = document.getElementById('page-heading');
    const breadcrumbLabel = document.getElementById('breadcrumb-label');
    const btnPublish = document.getElementById('btn-publish');

    function syncType(val) {
        typeRadios.forEach(r => {
            const pill = r.closest('.type-pill');
            r.checked = (r.value === val);
            pill?.classList.toggle('active-pill', r.value === val);
        });
        if (val === 'pilihan_ganda') {
            attachmentTriggerWrapper?.classList.add('d-none');
            mcQuestionsSection?.classList.remove('d-none');
            if (pageHeading) pageHeading.textContent = 'Edit Quiz';
            if (breadcrumbLabel) breadcrumbLabel.textContent = 'Edit Quiz';
            if (btnPublish) btnPublish.innerHTML = '<i data-lucide="save" size="18" class="me-2"></i> Save Quiz';
        } else {
            attachmentTriggerWrapper?.classList.remove('d-none');
            mcQuestionsSection?.classList.add('d-none');
            if (pageHeading) pageHeading.textContent = 'Edit Exercise';
            if (breadcrumbLabel) breadcrumbLabel.textContent = 'Edit Exercise';
            if (btnPublish) btnPublish.innerHTML = '<i data-lucide="save" size="18" class="me-2"></i> Save Exercise';
        }
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    typeRadios.forEach(r => r.addEventListener('change', function() { if(this.checked) syncType(this.value); }));

    // Load existing questions
    if (EXISTING_QUESTIONS && EXISTING_QUESTIONS.length > 0) {
        EXISTING_QUESTIONS.forEach(q => {
            window.addQuestionField({
                text: q.text || '',
                type: q.type || 'multiple_choice',
                options: q.options || [],
                correct: q.correct,
                image: q.image || ''
            });
        });
    }

    // SortableJS on questions container
    const container = document.getElementById('questions-container');
    if (container && typeof Sortable !== 'undefined') {
        Sortable.create(container, {
            animation: 200,
            handle: '.q-drag-handle',
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            dragClass: 'sortable-drag',
            onEnd: function() { window.renumberQuestions && window.renumberQuestions(); }
        });
    }

    // Date min constraints and initial values
    const openDI = document.getElementById('open_date_date');
    const openTI = document.getElementById('open_date_time');
    const dueDI  = document.getElementById('due_date_date');
    const dueTI  = document.getElementById('due_date_time');

    const initialOpenD = openDI?.value;
    const initialOpenT = openTI?.value || '00:00';
    const initialOpenString = initialOpenD ? `${initialOpenD}T${initialOpenT}` : '';

    const initialDueD = dueDI?.value;
    const initialDueT = dueTI?.value || '23:59';
    const initialDueString = initialDueD ? `${initialDueD}T${initialDueT}` : '';

    const now = new Date();
    const todayStr = `${now.getFullYear()}-${String(now.getMonth()+1).padStart(2,'0')}-${String(now.getDate()).padStart(2,'0')}`;

    if (openDI) {
        if (initialOpenD && initialOpenD < todayStr) {
            openDI.min = initialOpenD;
        } else {
            openDI.min = todayStr;
        }
    }
    if (dueDI) {
        if (initialDueD && initialDueD < todayStr) {
            dueDI.min = initialDueD;
        } else {
            dueDI.min = todayStr;
        }
    }
});

// ── Submit: combine date+time ──
document.getElementById('form-assignment').addEventListener('submit', function(e) {
    const openD = document.getElementById('open_date_date')?.value;
    const openT = document.getElementById('open_date_time')?.value || '00:00';
    const dueD  = document.getElementById('due_date_date')?.value;
    const dueT  = document.getElementById('due_date_time')?.value  || '23:59';

    const openDI = document.getElementById('open_date_date');
    const openTI = document.getElementById('open_date_time');
    const dueDI  = document.getElementById('due_date_date');
    const dueTI  = document.getElementById('due_date_time');

    const initialOpenD = openDI?.defaultValue || '';
    const initialOpenT = openTI?.defaultValue || '00:00';
    const initialOpenString = initialOpenD ? `${initialOpenD}T${initialOpenT}` : '';

    const initialDueD = dueDI?.defaultValue || '';
    const initialDueT = dueTI?.defaultValue || '23:59';
    const initialDueString = initialDueD ? `${initialDueD}T${initialDueT}` : '';

    if (openD) {
        const openDateTime = new Date(`${openD}T${openT}`);
        const currentOpenString = `${openD}T${openT}`;
        if (currentOpenString !== initialOpenString && openDateTime < new Date()) {
            e.preventDefault();
            if (typeof showToast === 'function') showToast('Activation Date tidak boleh di masa lalu.', 'error');
            document.getElementById('open_date_time')?.focus();
            return;
        }
        document.getElementById('open_date_hidden').value = `${openD} ${openT}:00`;
    }
    if (dueD) {
        const dueDateTime = new Date(`${dueD}T${dueT}`);
        const currentDueString = `${dueD}T${dueT}`;
        if (currentDueString !== initialDueString && dueDateTime < new Date()) {
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

    // Validate quiz questions
    const activeType = document.querySelector('.type-radio:checked')?.value;
    if (activeType === 'pilihan_ganda') {
        const cards = document.querySelectorAll('.question-card');
        if (cards.length === 0) {
            e.preventDefault();
            if (typeof showToast === 'function') showToast('Quiz harus memiliki minimal 1 pertanyaan.', 'error');
            return;
        }
        let valid = true;
        cards.forEach(card => {
            const qType = card.querySelector('.q-type-select')?.value;
            if (qType !== 'text') {
                const checked = card.querySelectorAll('.opt-correct-input:checked');
                if (checked.length === 0) {
                    valid = false;
                    card.style.border = '2px solid red';
                    card.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });
        if (!valid) {
            e.preventDefault();
            if (typeof showToast === 'function') showToast('Setiap pertanyaan harus memiliki jawaban yang benar.', 'error');
        }
    }
});

// ── Attachment helpers (same as create page) ──
window.handleAttachmentUpload = function(input) {
    const file = input.files[0];
    if (!file) return;
    const previewContainer = document.getElementById('attachment-preview-container');
    const nameEl = document.getElementById('attachment-name');
    const sizeEl = document.getElementById('attachment-size');
    const hiddenContainer = document.getElementById('attachment-hidden-input-container');
    nameEl.textContent = file.name;
    sizeEl.textContent = (file.size / 1024).toFixed(1) + ' KB (Uploading...)';
    previewContainer.classList.remove('d-none');
    const fd = new FormData();
    fd.append('file', file);
    fd.append('_token', document.querySelector('meta[name=csrf-token]')?.content || '');
    fetch('{{ route("upload") }}', { method: 'POST', body: fd })
        .then(r => { if (!r.ok) throw new Error('Upload failed'); return r.json(); })
        .then(resp => {
            sizeEl.textContent = (file.size / 1024).toFixed(1) + ' KB';
            const fileData = { name: file.name, path: resp.path, size: file.size };
            hiddenContainer.innerHTML = `<input type="hidden" name="files[]" value='${JSON.stringify(fileData)}'>`;
            if (typeof showToast === 'function') showToast('Material uploaded!', 'success');
        })
        .catch(() => { sizeEl.textContent = 'Upload failed'; });
};

window.removeAttachment = function() {
    document.getElementById('attachment-preview-container')?.classList.add('d-none');
    document.getElementById('attachment-hidden-input-container').innerHTML = '';
    document.getElementById('attachment-input').value = '';
};
</script>

{{-- Reuse the full question-builder JS from create page by including the shared script partial --}}
@include('courses.assignments.question_builder_js')
@endpush
@endsection
