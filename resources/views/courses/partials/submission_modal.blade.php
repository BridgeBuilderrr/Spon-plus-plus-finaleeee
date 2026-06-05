{{-- resources/views/courses/partials/submission_modal.blade.php --}}
<div class="modal fade" id="submitAssignmentModal{{ $activity->id }}" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content overflow-hidden shadow-luxury rounded-5 border-0">
            <div class="modal-header border-0 p-5 pb-2">
                <h3 class="fw-extrabold text-main m-0 d-flex align-items-center gap-3">
                    <div class="p-2 bg-primary-soft rounded-3 text-primary d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i data-lucide="send" size="24" style="transform: translate(-1.5px, 1.5px)"></i>
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
                        <div class="neon-drop-card" id="drop-card-sub-{{ $activity->id }}">
                            <!-- Placeholder -->
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
                            <!-- Previews -->
                            <div id="dz-sub-{{ $activity->id }}-previews" class="neon-previews-wrap"></div>
                            <!-- Hidden DZ trigger -->
                            <div id="dz-sub-{{ $activity->id }}" class="neon-dropzone-hidden"></div>
                            <!-- Stats -->
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
                        <div id="inputs-sub-{{ $activity->id }}"></div>
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
(function () {
    const actId   = '{{ $activity->id }}';
    const prefix  = 'sub-' + actId;
    const modalEl = document.getElementById('submitAssignmentModal' + actId);

    modalEl.addEventListener('shown.bs.modal', function () {
        // Use the shared initNeonDZ helper defined in modals.blade.php
        if (typeof initNeonDZ === 'function') {
            initNeonDZ(
                prefix,
                'drop-card-sub-' + actId,
                'dz-sub-' + actId + '-previews',
                'dz-sub-' + actId,
                'inputs-sub-' + actId,
                'msg-sub-' + actId
            );
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
</script>