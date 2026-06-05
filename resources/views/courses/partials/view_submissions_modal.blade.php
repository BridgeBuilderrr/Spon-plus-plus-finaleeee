{{-- resources/views/courses/partials/view_submissions_modal.blade.php --}}
<div class="modal fade" id="viewSubmissionsModal{{ $activity->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content overflow-hidden shadow-luxury rounded-5 border-0">
            <div class="modal-header border-0 p-5 pb-2">
                <h3 class="fw-extrabold text-main m-0 d-flex align-items-center gap-3">
                    <div class="p-2 bg-primary-soft rounded-3 text-primary d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i data-lucide="users" size="24"></i>
                    </div>
                    Submissions
                </h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-5 pt-3">
                <div class="luxury-submission-list">
                    @forelse($activity->submissions as $submission)
                        <div class="d-flex align-items-center justify-content-between p-3 rounded-4 border bg-card mb-3 transition-all hover-border-primary">
                            <div class="d-flex align-items-center gap-3">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($submission->user->name) }}&background=6366f1&color=fff" class="rounded-circle shadow-sm" width="45" height="45">
                                <div>
                                    <div class="fw-bold text-main d-block">{{ $submission->user->name }}</div>
                                    <div class="text-muted smallest">{{ $submission->created_at->format('M d, Y • H:i') }}</div>
                                </div>
                            </div>
                            
                            <div class="d-flex align-items-center gap-3">
                                @if(!empty($submission->files))
                                    @foreach($submission->files as $file)
                                        @php $f = is_array($file) ? $file : json_decode($file, true); @endphp
                                        <a href="{{ route('download.file', ['path' => $f['path'], 'assignment_id' => $activity->id]) }}" 
                                           class="btn btn-luxury-light rounded-pill px-3 py-1 fw-bold smallest border transition-all"
                                           title="{{ $f['name'] }}">
                                            <i data-lucide="download" size="12" class="me-1"></i> {{ Str::limit($f['name'], 15) }}
                                        </a>
                                    @endforeach
                                @else
                                    <span class="text-muted smallest fw-medium">No files attached</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <i data-lucide="inbox" size="48" class="text-muted opacity-25 mb-3"></i>
                            <p class="text-muted m-0 fw-medium">No submissions yet.</p>
                        </div>
                    @endforelse
                </div>
            </div>
            <div class="modal-footer border-0 p-5 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
