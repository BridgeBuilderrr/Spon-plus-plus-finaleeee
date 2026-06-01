<!-- Assignment Submission Modal -->
<div class="modal fade" id="submitAssignmentModal{{ $activity->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content overflow-hidden">
            <div class="modal-header border-0 p-5 pb-2">
                <h3 class="fw-extrabold text-main m-0 d-flex align-items-center gap-3">
                    <div class="p-2 bg-primary-soft rounded-3 text-primary">
                        <i data-lucide="send" size="24"></i>
                    </div>
                    Turn In Work
                </h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('submissions.store', [$classroom, $activity]) }}" method="POST" enctype="multipart/form-data" class="upload-box-form">
                @csrf
                <div class="modal-body p-5 pt-3">
                    <div class="mb-4">
                        <div class="p-3 rounded-4 border bg-light-subtle d-flex align-items-center gap-3 mb-4">
                            <i data-lucide="info" class="text-primary" size="20"></i>
                            <div class="small fw-bold">You are submitting for: <u>{{ $activity->title }}</u></div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Private comment to teacher (Optional)</label>
                        <textarea name="comment" class="form-control" rows="2" placeholder="Any notes about your submission..."></textarea>
                    </div>

                    <div class="mb-0">
                        <label class="form-label">Submission Files (Max 5 files)</label>
                        <div class="luxury-upload-box p-4 text-center" id="upload-submission-{{ $activity->id }}">
                            <input type="file" name="files[]" id="file-submission-{{ $activity->id }}" class="d-none" multiple>
                            <label for="file-submission-{{ $activity->id }}" class="mb-0 cursor-pointer w-100">
                                <i data-lucide="upload" size="32" class="text-primary opacity-50 mb-2"></i>
                                <div class="text-muted small fw-bold">Select work to upload</div>
                            </label>
                            <div class="upload-progress-list mt-3 text-start"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-5 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 py-3 fw-bold shadow">Submit Assignment Now</button>
                </div>
            </form>
        </div>
    </div>
</div>
