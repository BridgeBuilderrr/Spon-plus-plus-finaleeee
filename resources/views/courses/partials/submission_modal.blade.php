{{-- resources/views/courses/partials/submission_modal.blade.php --}}
@php
    $submission = $activity->submissions->where('user_id', auth()->id())->first();
    $isEdit = (bool)$submission;
    $modalId = $isEdit ? "editSubmissionModal{$activity->id}" : "submitAssignmentModal{$activity->id}";
    $actionUrl = $isEdit 
        ? route('submissions.update', [$classroom, $activity, $submission]) 
        : route('submissions.store', [$classroom, $activity]);
@endphp

<div class="modal fade" id="{{ $modalId }}" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content overflow-hidden shadow-luxury rounded-5 border-0">
            <div class="modal-header border-0 p-5 pb-2">
                <h3 class="fw-extrabold text-main m-0 d-flex align-items-center gap-3">
                    <div class="p-2 bg-primary-soft rounded-3 text-primary d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i data-lucide="{{ $isEdit ? 'edit-3' : 'send' }}" size="24" style="transform: translate(-1.5px, 1.5px)"></i>
                    </div>
                    {{ $isEdit ? 'Update Your Work' : 'Turn In Work' }}
                </h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
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
                        <button type="button" class="btn btn-outline-danger rounded-pill px-4 fw-bold me-auto" onclick="confirmDeleteSubmission('{{ $activity->id }}')">Delete</button>
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
        </div>
    </div>
</div>

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

            // Load existing files if editing
            if (existingFiles && existingFiles.length > 0) {
                const inputsCont = document.getElementById('inputs-sub-' + actId);
                existingFiles.forEach(f => {
                    const file = typeof f === 'string' ? JSON.parse(f) : f;
                    const mock = { name: file.name, size: file.size, accepted: true, upload: { uuid: Math.random().toString(36).substr(2, 9) } };
                    dz.displayExistingFile(mock, `{{ asset('storage') }}/${file.path}`);
                    
                    // Add hidden input for existing file
                    const hidden = document.createElement('input');
                    hidden.type = 'hidden'; hidden.name = 'files[]'; hidden.value = JSON.stringify(file);
                    hidden.dataset.uuid = mock.upload.uuid;
                    inputsCont.appendChild(hidden);
                });
                
                // Update counters
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