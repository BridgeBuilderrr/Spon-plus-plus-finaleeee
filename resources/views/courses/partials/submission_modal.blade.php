{{-- resources/views/courses/partials/submission_modal.blade.php --}}
@php
    $submission = $activity->submissions->where('user_id', auth()->id())->first();
    $isEdit = (bool)$submission;
    $isPilihanGanda = $activity->assignment_type === 'pilihan_ganda';
    $modalId = $isEdit ? "editSubmissionModal{$activity->id}" : "submitAssignmentModal{$activity->id}";
    $actionUrl = $isEdit
        ? route('submissions.update', [$classroom, $activity, $submission])
        : route('submissions.store', [$classroom, $activity]);
    $alreadyGraded = $isEdit && $isPilihanGanda && $submission->status === 'graded';
@endphp

<div class="modal fade" id="{{ $modalId }}" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered {{ $isPilihanGanda ? 'modal-lg' : '' }}">
        <div class="modal-content overflow-hidden shadow-luxury rounded-5 border-0">
            <div class="modal-header border-0 p-5 pb-2">
                <h3 class="fw-extrabold text-main m-0 d-flex align-items-center gap-3">
                    <div class="p-2 bg-primary-soft rounded-3 text-primary d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i data-lucide="{{ $isPilihanGanda ? 'list-checks' : ($isEdit ? 'edit-3' : 'send') }}" size="24"></i>
                    </div>
                    @if($isPilihanGanda && $alreadyGraded)
                        Quiz Results
                    @elseif($isPilihanGanda)
                        Answer Quiz
                    @else
                        {{ $isEdit ? 'Update Your Work' : 'Turn In Work' }}
                    @endif
                </h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            {{-- ─── ALREADY-GRADED MULTIPLE CHOICE: Show result only ─────────── --}}
            @if($isPilihanGanda && $alreadyGraded)
                <div class="modal-body p-5 pt-3">
                    {{-- Score card --}}
                    @php
                        $totalQ = count($activity->questions ?? []);
                        $pct = $submission->grade ?? 0;
                        $correct = $totalQ > 0 ? round($pct * $totalQ / 100) : 0;
                        $scoreColor = $pct >= 80 ? 'success' : ($pct >= 60 ? 'warning' : 'danger');
                    @endphp
                    <div class="text-center mb-5">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle border-4 border-{{ $scoreColor }} mb-3"
                             style="width:100px;height:100px;border:5px solid var(--bs-{{ $scoreColor }});">
                            <span class="fw-extrabold fs-3 text-{{ $scoreColor }}">{{ $pct }}%</span>
                        </div>
                        <div class="fw-bold h5 mb-1 text-main">{{ $correct }} / {{ $totalQ }} Correct</div>
                        <div class="text-muted small">{{ $submission->teacher_comment }}</div>
                    </div>

                    {{-- Question review --}}
                    @foreach($activity->questions as $qi => $q)
                        @php
                            $qType = $q['type'] ?? 'multiple_choice';
                            if ($qType === 'checkboxes') {
                                $correctList = isset($q['correct']) ? (array)$q['correct'] : [];
                                $correctList = array_map('intval', $correctList);
                                sort($correctList);
                                
                                $studentList = isset($submission->answers[$qi]) ? (array)$submission->answers[$qi] : [];
                                $studentList = array_map('intval', $studentList);
                                sort($studentList);
                                
                                $isCorrect = ($studentList === $correctList);
                            } else {
                                $correctIdx = isset($q['correct']) ? (is_array($q['correct']) ? (count($q['correct']) > 0 ? intval($q['correct'][0]) : -1) : intval($q['correct'])) : -1;
                                $studentIdx = isset($submission->answers[$qi]) ? (is_array($submission->answers[$qi]) ? (count($submission->answers[$qi]) > 0 ? intval($submission->answers[$qi][0]) : -2) : intval($submission->answers[$qi])) : -2;
                                $isCorrect = ($studentIdx === $correctIdx);
                            }
                        @endphp
                        <div class="card border rounded-4 p-4 mb-3 {{ $isCorrect ? 'border-success-subtle bg-success-soft' : 'border-danger-subtle bg-danger-soft' }}">
                            <div class="d-flex align-items-start gap-3 mb-3">
                                <span class="badge rounded-pill {{ $isCorrect ? 'bg-success' : 'bg-danger' }} px-3 py-2 fw-bold">{{ $qi + 1 }}</span>
                                <div class="fw-semibold text-main">
                                    {{ $q['text'] }}
                                    @if($q['required'] ?? false)
                                        <span class="text-danger ms-1">*</span>
                                    @endif
                                </div>
                            </div>
                            @if(!empty($q['image']))
                                <div class="mb-3 ps-5">
                                    <img src="{{ $q['image'] }}" class="img-fluid rounded-3 border" style="max-height: 200px;">
                                </div>
                            @endif
                            <div class="ps-5">
                                @if($qType === 'text')
                                    <div class="p-3 rounded-3 border bg-light text-main">
                                        <div class="smallest text-muted mb-1">Your response:</div>
                                        <div class="fw-semibold">{{ $submission->answers[$qi] ?? '(Empty response)' }}</div>
                                    </div>
                                @else
                                    <div class="d-flex flex-column gap-2">
                                        @foreach($q['options'] as $oi => $opt)
                                            @php
                                                if ($qType === 'checkboxes') {
                                                    $correctList = isset($q['correct']) ? (array)$q['correct'] : [];
                                                    $studentList = isset($submission->answers[$qi]) ? (array)$submission->answers[$qi] : [];
                                                    $isCorrectOpt = in_array($oi, $correctList);
                                                    $isStudentOpt = in_array($oi, $studentList);
                                                } else {
                                                    $correctIdx = isset($q['correct']) ? (is_array($q['correct']) ? (count($q['correct']) > 0 ? intval($q['correct'][0]) : -1) : intval($q['correct'])) : -1;
                                                    $studentIdx = isset($submission->answers[$qi]) ? (is_array($submission->answers[$qi]) ? (count($submission->answers[$qi]) > 0 ? intval($submission->answers[$qi][0]) : -2) : intval($submission->answers[$qi])) : -2;
                                                    $isCorrectOpt = ($oi == $correctIdx);
                                                    $isStudentOpt = ($oi == $studentIdx);
                                                }
                                            @endphp
                                            <div class="d-flex align-items-center gap-2 px-3 py-2 rounded-3
                                                {{ $isCorrectOpt ? 'bg-success text-white fw-bold' : ($isStudentOpt && !$isCorrectOpt ? 'bg-danger text-white' : '') }}">
                                                <span class="rounded-circle border d-inline-flex align-items-center justify-content-center flex-shrink-0"
                                                      style="width:22px;height:22px;font-size:0.7rem;font-weight:700;">{{ chr(65+$oi) }}</span>
                                                <span>{{ $opt }}</span>
                                                @if($isCorrectOpt) <i data-lucide="check-circle" size="14" class="ms-auto"></i> @endif
                                                @if($isStudentOpt && !$isCorrectOpt) <i data-lucide="x-circle" size="14" class="ms-auto"></i> @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="modal-footer border-0 p-5 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Close</button>
                </div>

            {{-- ─── MULTIPLE CHOICE QUIZ FORM ────────────────────────────────── --}}
            @elseif($isPilihanGanda)
                <form action="{{ $actionUrl }}" method="POST" id="form-sub-{{ $activity->id }}">
                    @csrf
                    <div class="modal-body p-5 pt-3">
                        {{-- Assignment info --}}
                        <div class="p-4 rounded-4 border bg-primary-soft-bg d-flex align-items-center gap-4 mb-4">
                            <div class="bg-primary text-white p-3 rounded-4 shadow-sm">
                                <i data-lucide="list-checks" size="24"></i>
                            </div>
                            <div class="overflow-hidden">
                                <div class="text-muted smallest ls-1 uppercase mb-1">Multiple Choice Quiz</div>
                                <div class="fw-bold h6 m-0 text-truncate">{{ $activity->title }}</div>
                                <div class="text-muted smallest">{{ count($activity->questions ?? []) }} Questions • Auto-graded instantly</div>
                            </div>
                        </div>

                        {{-- Questions --}}
                        @if(!empty($activity->questions))
                            @foreach($activity->questions as $qi => $q)
                                @php
                                    $qType = $q['type'] ?? 'multiple_choice';
                                    $isRequired = $q['required'] ?? false;
                                @endphp
                                <div class="card border rounded-4 p-4 mb-3">
                                    <div class="fw-semibold text-main mb-3 d-flex gap-2 align-items-start">
                                        <span class="badge bg-primary-soft text-primary rounded-pill px-2 py-1 fw-bold flex-shrink-0">{{ $qi + 1 }}</span>
                                        <span>
                                            {{ $q['text'] }}
                                            @if($isRequired)
                                                <span class="text-danger ms-1">*</span>
                                            @endif
                                        </span>
                                    </div>
                                    @if(!empty($q['image']))
                                        <div class="mb-3">
                                            <img src="{{ $q['image'] }}" class="img-fluid rounded-3 border" style="max-height: 200px;">
                                        </div>
                                    @endif
                                    <div class="d-flex flex-column gap-2">
                                        @if($qType === 'text')
                                            <div class="position-relative">
                                                <textarea name="answers[{{ $qi }}]" 
                                                          class="form-control rounded-3 quiz-text-input" 
                                                          rows="3" 
                                                          maxlength="500" 
                                                          placeholder="Type your response here (max 500 characters)..." 
                                                          required 
                                                          oninput="this.nextElementSibling.querySelector('.char-count').textContent = this.value.length"></textarea>
                                                <div class="position-absolute bottom-0 end-0 p-2 text-muted smallest" style="pointer-events: none;">
                                                    <span class="char-count">0</span>/500
                                                </div>
                                            </div>
                                        @else
                                            @foreach($q['options'] as $oi => $opt)
                                                <label class="d-flex align-items-center gap-3 p-3 rounded-3 border cursor-pointer quiz-option-label"
                                                       style="cursor:pointer; transition: background 0.15s;">
                                                    @if($qType === 'checkboxes')
                                                        <input type="checkbox" name="answers[{{ $qi }}][]" value="{{ $oi }}"
                                                               class="form-check-input m-0 quiz-checkbox">
                                                    @else
                                                        <input type="radio" name="answers[{{ $qi }}]" value="{{ $oi }}"
                                                               class="form-check-input m-0 quiz-radio" {{ $isRequired ? 'required' : '' }}>
                                                    @endif
                                                    <span class="rounded-circle border d-inline-flex align-items-center justify-content-center flex-shrink-0"
                                                          style="width:24px;height:24px;font-size:0.72rem;font-weight:700;">{{ chr(65+$oi) }}</span>
                                                    <span>{{ $opt }}</span>
                                                </label>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center text-muted py-4">No questions found for this quiz.</div>
                        @endif
                    </div>
                    <div class="modal-footer border-0 p-5 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-5 py-3 fw-bold shadow">
                            <i data-lucide="send" size="16" class="me-2"></i> Submit Quiz
                        </button>
                    </div>
                </form>

            {{-- ─── ESSAY / FILE UPLOAD FORM ─────────────────────────────────── --}}
            @else
                <form action="{{ $actionUrl }}" method="POST" id="form-sub-{{ $activity->id }}">
                    @csrf
                    <div class="modal-body p-5 pt-3">
                        <div class="p-4 rounded-4 border bg-primary-soft-bg d-flex align-items-center gap-4 mb-4">
                            <div class="bg-primary text-white p-3 rounded-4 shadow-sm">
                                <i data-lucide="file-check" size="24"></i>
                            </div>
                            <div class="overflow-hidden">
                                <div class="text-muted smallest ls-1 uppercase mb-1">Target Activity</div>
                                <div class="fw-bold h6 m-0 text-truncate">{{ $activity->title }}</div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Private comment to teacher (Optional)</label>
                            <textarea name="content" class="form-control" rows="2" placeholder="Any notes about your work...">{{ $isEdit ? $submission->content : '' }}</textarea>
                        </div>

                        <div class="mb-0">
                            <label class="form-label">Submission Files (Max 10 files, 50MB each)</label>
                            <div class="neon-drop-card" id="drop-card-sub-{{ $activity->id }}">
                                <div class="neon-dz-message" id="msg-sub-{{ $activity->id }}">
                                    <div class="neon-icon-wrap">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M4 16v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2"/>
                                            <polyline points="16 12 12 8 8 12"/>
                                            <line x1="12" y1="8" x2="12" y2="20"/>
                                        </svg>
                                    </div>
                                    <h6 class="mb-1 fw-bold">Attach your work</h6>
                                    <p class="neon-dz-sub mb-0">or <span class="neon-browse-btn">browse files</span></p>
                                </div>
                                <div id="dz-sub-{{ $activity->id }}-previews" class="neon-previews-wrap"></div>
                                <div id="dz-sub-{{ $activity->id }}" class="neon-dropzone-hidden"></div>
                                <div class="neon-stats">
                                    <div class="neon-stat"><div class="neon-stat-num" id="count-sub-{{ $activity->id }}-total">0</div><div class="neon-stat-label">FILES</div></div>
                                    <div class="neon-stat"><div class="neon-stat-num neon-success" id="count-sub-{{ $activity->id }}-success">0</div><div class="neon-stat-label">UPLOADED</div></div>
                                    <div class="neon-stat"><div class="neon-stat-num neon-danger" id="count-sub-{{ $activity->id }}-error">0</div><div class="neon-stat-label">FAILED</div></div>
                                </div>
                            </div>
                            <div id="inputs-sub-{{ $activity->id }}"></div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-5 pt-0">
                        @if($isEdit)
                            <button type="button" class="btn btn-outline-danger rounded-pill px-4 fw-bold me-auto"
                                    onclick="confirmDeleteSubmission('{{ $activity->id }}')">Delete</button>
                        @endif
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-5 py-3 fw-bold shadow">{{ $isEdit ? 'Save Changes' : 'Submit Now' }}</button>
                    </div>
                </form>
                @if($isEdit)
                    <form action="{{ route('submissions.destroy', [$classroom, $activity, $submission]) }}" method="POST" id="delete-sub-form-{{ $activity->id }}" class="d-none">
                        @csrf @method('DELETE')
                    </form>
                @endif
            @endif
        </div>
    </div>
</div>

<style>
.quiz-option-label:hover { background: var(--bg-color) !important; }
.quiz-option-label:has(.quiz-radio:checked),
.quiz-option-label:has(.quiz-checkbox:checked) {
    background: rgba(var(--primary-rgb, 99,102,241), 0.08) !important;
    border-color: var(--primary-color) !important;
}
.bg-success-soft { background: rgba(25, 135, 84, 0.06) !important; }
.bg-danger-soft  { background: rgba(220, 53, 69, 0.06) !important; }
</style>

@if($isPilihanGanda)
<script>
(function() {
    const form = document.getElementById('form-sub-{{ $activity->id }}');
    if (!form) return;

    const storageKey = 'quiz_draft_{{ auth()->id() }}_{{ $activity->id }}';

    // Save form state to localStorage
    function saveDraft() {
        const data = {};
        // radios
        form.querySelectorAll('.quiz-radio:checked').forEach(radio => {
            data[radio.name] = radio.value;
        });
        // checkboxes
        form.querySelectorAll('.quiz-checkbox').forEach(cb => {
            if (!data[cb.name]) data[cb.name] = [];
            if (cb.checked) data[cb.name].push(cb.value);
        });
        // textareas / text inputs
        form.querySelectorAll('.quiz-text-input').forEach(txt => {
            data[txt.name] = txt.value;
        });

        localStorage.setItem(storageKey, JSON.stringify(data));
    }

    // Load form state from localStorage
    function loadDraft() {
        const stored = localStorage.getItem(storageKey);
        if (!stored) return;
        try {
            const data = JSON.parse(stored);
            // restore radios
            form.querySelectorAll('.quiz-radio').forEach(radio => {
                if (data[radio.name] === radio.value) {
                    radio.checked = true;
                }
            });
            // restore checkboxes
            form.querySelectorAll('.quiz-checkbox').forEach(cb => {
                if (data[cb.name] && data[cb.name].includes(cb.value)) {
                    cb.checked = true;
                }
            });
            // restore textareas
            form.querySelectorAll('.quiz-text-input').forEach(txt => {
                if (data[txt.name] !== undefined) {
                    txt.value = data[txt.name];
                    const counter = txt.nextElementSibling?.querySelector('.char-count');
                    if (counter) counter.textContent = txt.value.length;
                }
            });
        } catch (e) {
            console.error('Error loading quiz draft:', e);
        }
    }

    // Load draft on init
    loadDraft();

    // Listen to changes
    form.addEventListener('input', saveDraft);
    form.addEventListener('change', saveDraft);

    // Validate and submit
    form.addEventListener('submit', function(e) {
        let valid = true;
        const cards = form.querySelectorAll('.card.border.rounded-4');
        
        cards.forEach(card => {
            const checkboxes = card.querySelectorAll('.quiz-checkbox');
            const radios = card.querySelectorAll('.quiz-radio');
            const textareas = card.querySelectorAll('.quiz-text-input');

            if (checkboxes.length > 0) {
                const checked = card.querySelectorAll('.quiz-checkbox:checked');
                if (checked.length === 0) {
                    valid = false;
                    card.style.setProperty('border-color', 'var(--bs-danger)', 'important');
                    card.classList.add('shadow-sm');
                } else {
                    card.style.removeProperty('border-color');
                }
            } else if (radios.length > 0) {
                const checked = card.querySelectorAll('.quiz-radio:checked');
                if (checked.length === 0) {
                    valid = false;
                    card.style.setProperty('border-color', 'var(--bs-danger)', 'important');
                    card.classList.add('shadow-sm');
                } else {
                    card.style.removeProperty('border-color');
                }
            } else if (textareas.length > 0) {
                const filled = Array.from(textareas).every(ta => ta.value.trim().length > 0);
                if (!filled) {
                    valid = false;
                    card.style.setProperty('border-color', 'var(--bs-danger)', 'important');
                    card.classList.add('shadow-sm');
                } else {
                    card.style.removeProperty('border-color');
                }
            }
        });
        
        if (!valid) {
            e.preventDefault();
            if (typeof showToast === 'function') {
                showToast('Silakan jawab semua pertanyaan.', 'error');
            } else {
                alert('Silakan jawab semua pertanyaan.');
            }
        } else {
            // Clear draft
            localStorage.removeItem(storageKey);
        }
    });
})();
</script>
@endif

@if(!$isPilihanGanda)
<script>
(function () {
    const actId = '{{ $activity->id }}';
    const prefix = 'sub-' + actId;
    const modalEl = document.getElementById('{{ $modalId }}');
    const existingFiles = {!! json_encode($isEdit ? ($submission->files ?? []) : []) !!};

    modalEl.addEventListener('shown.bs.modal', function () {
        if (typeof initNeonDZ === 'function') {
            const dz = initNeonDZ(
                prefix,
                'drop-card-sub-' + actId,
                'dz-sub-' + actId + '-previews',
                'dz-sub-' + actId,
                'inputs-sub-' + actId,
                'msg-sub-' + actId
            );

            if (existingFiles && existingFiles.length > 0) {
                const inputsCont = document.getElementById('inputs-sub-' + actId);
                existingFiles.forEach(f => {
                    const file = typeof f === 'string' ? JSON.parse(f) : f;
                    const mock = { name: file.name, size: file.size, accepted: true, upload: { uuid: Math.random().toString(36).substr(2, 9) } };
                    dz.displayExistingFile(mock, `{{ asset('storage') }}/${file.path}`);
                    const hidden = document.createElement('input');
                    hidden.type = 'hidden'; hidden.name = 'files[]'; hidden.value = JSON.stringify(file);
                    hidden.dataset.uuid = mock.upload.uuid;
                    inputsCont.appendChild(hidden);
                });
                document.getElementById('count-' + prefix + '-total').textContent = existingFiles.length;
                document.getElementById('count-' + prefix + '-success').textContent = existingFiles.length;
                document.getElementById('msg-sub-' + actId).style.display = 'none';
            }
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }
    });

    modalEl.addEventListener('hidden.bs.modal', function () {
        if (window._dzInstances && window._dzInstances[prefix]) {
            try { window._dzInstances[prefix].destroy(); } catch (e) {}
            delete window._dzInstances[prefix];
        }
    });
})();

function confirmDeleteSubmission(id) {
    showConfirm({
        title: 'Retract Submission',
        message: 'Are you sure you want to delete your submission? This action cannot be undone.',
        btnText: 'Yes, Delete',
        onConfirm: () => document.getElementById('delete-sub-form-' + id).submit()
    });
}
</script>
@endif