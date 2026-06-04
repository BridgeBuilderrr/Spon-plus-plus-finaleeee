<!-- Assignment Submission Modal -->
<div class="modal fade" id="submitAssignmentModal{{ $activity->id }}" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content overflow-hidden shadow-luxury rounded-5 border-0">
            <div class="modal-header border-0 p-5 pb-2">
                <h3 class="fw-extrabold text-main m-0 d-flex align-items-center gap-3">
                    <div class="p-2 bg-primary-soft rounded-3 text-primary">
                        <i data-lucide="send" size="24"></i>
                    </div>
                    Turn In Work
                </h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('submissions.store', [$classroom, $activity]) }}" method="POST" id="form-submission-{{ $activity->id }}">
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
                        <textarea name="comment" class="form-control" rows="2" placeholder="Any notes about your work..."></textarea>
                    </div>

                    <div class="mb-0">
                        <label class="form-label">Submission Files (Max 10 files, 50MB each)</label>
                        <div class="neon-drop-card" id="drop-card-submission-{{ $activity->id }}">
                            <div id="dz-submission-{{ $activity->id }}" class="neon-dropzone"></div>
                            <div class="neon-stats">
                                <div class="neon-stat">
                                    <div class="neon-stat-num" id="count-sub-{{ $activity->id }}-total">0</div>
                                    <div class="neon-stat-label">FILES</div>
                                </div>
                                <div class="neon-stat">
                                    <div class="neon-stat-num neon-success" id="count-sub-{{ $activity->id }}-success">0</div>
                                    <div class="neon-stat-label">UPLOADED</div>
                                </div>
                                <div class="neon-stat">
                                    <div class="neon-stat-num neon-danger" id="count-sub-{{ $activity->id }}-error">0</div>
                                    <div class="neon-stat-label">FAILED</div>
                                </div>
                            </div>
                        </div>
                        <div id="inputs-submission-{{ $activity->id }}"></div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-5 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 py-3 fw-bold shadow">Submit Now</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function() {
    const activityId = '{{ $activity->id }}';
    const dzId = '#dz-submission-' + activityId;
    const cardId = 'drop-card-submission-' + activityId;
    
    const initModalDZ = () => {
        const el = document.querySelector(dzId);
        if (!el || el.dropzone) return;

        const card = document.getElementById(cardId);
        let total = 0, success = 0, error = 0;

        const dz = new Dropzone(dzId, {
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

        // Inject custom message
        el.insertAdjacentHTML('afterbegin', `<div class="neon-dz-message">
            <div class="neon-icon-wrap">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 16v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2"/>
                    <polyline points="16 12 12 8 8 12"/>
                    <line x1="12" y1="8" x2="12" y2="20"/>
                </svg>
            </div>
            <h6 class="mb-1 fw-bold">Attach your work</h6>
            <p class="neon-dz-sub">or <span class="neon-browse-btn">browse files</span></p>
        </div>`);

        dz.on('dragenter', () => card.classList.add('neon-drag-active'));
        dz.on('dragleave', () => card.classList.remove('neon-drag-active'));
        dz.on('drop', () => card.classList.remove('neon-drag-active'));

        dz.on('addedfile', () => {
            total++;
            document.getElementById(\`count-sub-\${activityId}-total\`).textContent = total;
        });

        dz.on('success', (file, response) => {
            success++;
            document.getElementById(\`count-sub-\${activityId}-success\`).textContent = success;
            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = 'files[]';
            hidden.value = JSON.stringify(response);
            hidden.id = 'file-input-' + file.upload.uuid;
            document.getElementById('inputs-submission-' + activityId).appendChild(hidden);
            showToast(\`✔ "\${file.name}" uploaded\`, 'success');
        });

        dz.on('error', (file, errorMsg) => {
            error++;
            document.getElementById(\`count-sub-\${activityId}-error\`).textContent = error;
            showToast(typeof errorMsg === 'string' ? errorMsg : errorMsg.message, 'error');
        });

        dz.on('removedfile', (file) => {
            total = Math.max(0, total - 1);
            document.getElementById(\`count-sub-\${activityId}-total\`).textContent = total;
            const hidden = document.getElementById('file-input-' + file.upload.uuid);
            if (hidden) hidden.remove();
        });
    };

    // Initialize when modal is shown to ensure element is ready
    const modalEl = document.getElementById('submitAssignmentModal' + activityId);
    modalEl.addEventListener('shown.bs.modal', initModalDZ);
})();
</script>