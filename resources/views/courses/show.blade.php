@extends('layouts.app')

@section('content')
    <!-- Class Header Section -->
    <div class="row g-4 mb-4 align-items-center">
        <div class="col-md-6">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb fw-bold mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('courses.index') }}" class="text-decoration-none text-muted">My Spaces</a></li>
                    <li class="breadcrumb-item active text-primary" aria-current="page">{{ $classroom->title }}</li>
                </ol>
            </nav>
            <h2 class="fw-extrabold m-0 text-main">{{ $classroom->title }}</h2>
        </div>
        <div class="col-md-6 text-md-end">
            <div class="d-flex gap-2 justify-content-md-end flex-wrap">
                @if(auth()->id() === $classroom->teacher_id)
                    <button class="btn btn-light rounded-pill px-4 fw-bold border shadow-sm" data-bs-toggle="modal" data-bs-target="#editClassModal">
                        <i data-lucide="settings" size="18" class="me-2"></i> Settings
                    </button>
                    
                    <div class="dropdown">
                        <button class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow" data-bs-toggle="dropdown">
                            <i data-lucide="plus-circle" size="18" class="me-2"></i> Post New Activity
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg p-2 rounded-4 mt-2">
                            <li><a class="dropdown-item rounded-3 py-2 fw-bold" href="#" data-bs-toggle="modal" data-bs-target="#createAssignmentModal"><i data-lucide="file-text" size="18" class="me-3 text-primary"></i> Assignment</a></li>
                            <li><a class="dropdown-item rounded-3 py-2 fw-bold" href="#" data-bs-toggle="modal" data-bs-target="#uploadMaterialModal"><i data-lucide="book-open" size="18" class="me-3 text-success"></i> Material</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item rounded-3 py-2 fw-bold" href="#"><i data-lucide="megaphone" size="18" class="me-3 text-warning"></i> Announcement</a></li>
                        </ul>
                    </div>
                @else
                    <form action="{{ route('courses.exit', $classroom) }}" method="POST" id="exit-class-form">
                        @csrf
                        <button type="button" class="btn btn-outline-danger rounded-pill px-4 fw-bold" onclick="confirmExit()">
                            <i data-lucide="log-out" size="18" class="me-2"></i> Exit Space
                        </button>
                    </form>
                    <script>
                        function confirmExit() {
                            showConfirm({
                                title: 'Exit Classroom',
                                message: 'Are you sure you want to leave this classroom? You will need the code to join again.',
                                btnText: 'Yes, Exit',
                                onConfirm: () => document.getElementById('exit-class-form').submit()
                            });
                        }
                    </script>
                @endif
            </div>
        </div>
    </div>

    <!-- Banner Card (Clean Image) -->
    <div class="card border-0 shadow-sm rounded-5 overflow-hidden mb-4 luxury-banner-card">
        <div class="classroom-banner-wrapper position-relative" style="height: 240px; background: {{ $classroom->banner ? 'url('.asset('storage/' . $classroom->banner).')' : 'linear-gradient(135deg, var(--primary-color), var(--secondary-color))' }}; background-size: cover; background-position: center;">
            <div class="banner-overlay-gradient"></div>
            
            @if(auth()->id() === $classroom->teacher_id)
                <div class="position-absolute top-0 end-0 p-4 z-2">
                    <div class="dropdown">
                        <button class="btn btn-glass-luxury rounded-pill px-3 py-2" data-bs-toggle="dropdown">
                            <i data-lucide="image" size="18" class="me-2"></i> Customize
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-4 p-2">
                            <li>
                                <button class="dropdown-item rounded-3 py-2" onclick="document.getElementById('banner-upload-input').click()">
                                    <i data-lucide="upload" size="16" class="me-2 text-primary"></i> Change Cover
                                </button>
                            </li>
                            @if($classroom->banner)
                            <li>
                                <form action="{{ route('courses.delete_banner', $classroom) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item rounded-3 py-2 text-danger">
                                        <i data-lucide="trash-2" size="16" class="me-2"></i> Reset Default
                                    </button>
                                </form>
                            </li>
                            @endif
                        </ul>
                    </div>
                    <input type="file" id="banner-upload-input" class="d-none" accept="image/*">
                </div>
            @endif

            <div class="position-absolute bottom-0 start-0 p-5 text-white z-2">
                <div class="d-flex align-items-center gap-3 opacity-90 fw-bold small mb-2">
                    <div class="bg-white text-primary px-3 py-1 rounded-pill shadow-sm d-flex align-items-center gap-2">
                        <span>CODE: {{ $classroom->code }}</span>
                        <button class="btn btn-link p-0 text-primary border-0" onclick="copyClassCode('{{ $classroom->code }}')" title="Copy Code">
                            <i data-lucide="copy" size="14"></i>
                        </button>
                    </div>
                </div>
                <h3 class="fw-extrabold mb-0 d-flex align-items-center gap-2">
                    Admin: {{ $classroom->teacher->name }}
                </h3>
            </div>

            <script>
                function copyClassCode(code) {
                    navigator.clipboard.writeText(code).then(() => {
                        showToast('Class code copied to clipboard!', 'success');
                    });
                }
            </script>
        </div>
    </div>

    <!-- Navigation Area (Below Banner) -->
    <div class="card border-0 shadow-sm rounded-4 mb-5 overflow-hidden" style="background: var(--card-bg);">
        <ul class="nav nav-tabs border-0 justify-content-center luxury-tabs-under" id="classTab" role="tablist">
            <li class="nav-item">
                <button class="nav-link active px-5 py-3 fw-bold" id="stream-tab" data-bs-toggle="tab" data-bs-target="#stream">Stream Feed</button>
            </li>
            <li class="nav-item">
                <button class="nav-link px-5 py-3 fw-bold" id="people-tab" data-bs-toggle="tab" data-bs-target="#members">Classmates</button>
            </li>
        </ul>
    </div>

    <div class="tab-content" id="classTabContent">
        <!-- Stream Tab -->
        <div class="tab-pane fade show active" id="stream">
            <div class="row g-5">
                <!-- Sidebar Info -->
                <div class="col-lg-3">
                    <div class="card border-0 shadow-sm rounded-5 p-4 mb-4 luxury-sidebar-card">
                        <h6 class="fw-extrabold mb-4 d-flex align-items-center gap-2">
                            <i data-lucide="calendar" class="text-primary" size="20"></i>
                            Deadlines
                        </h6>
                        @php
                            $upcoming = $classroom->assignments->where('due_date', '>', now())->sortBy('due_date')->take(3);
                        @endphp
                        @forelse($upcoming as $task)
                            <div class="mb-4 border-start border-4 border-primary ps-3 py-1">
                                <div class="smaller text-muted fw-extrabold mb-1">{{ $task->due_date->format('M d, H:i') }}</div>
                                <a href="javascript:void(0)" class="text-decoration-none fw-bold small d-block text-main hover-primary">{{ $task->title }}</a>
                            </div>
                        @empty
                            <div class="text-center py-4 bg-light-subtle rounded-4">
                                <i data-lucide="check-circle" class="text-success opacity-50 mb-2" size="28"></i>
                                <p class="small text-muted mb-0 fw-bold px-2">All caught up!</p>
                            </div>
                        @endforelse
                        <button class="btn btn-light rounded-pill w-100 fw-bold mt-2 border-0 smaller py-2">Full Schedule</button>
                    </div>

                    <div class="card border-0 shadow-sm rounded-5 p-4 bg-primary-soft">
                        <h6 class="fw-bold mb-2">Space Stats</h6>
                        <div class="d-flex justify-content-between mb-2 smaller">
                            <span class="text-muted">Members</span>
                            <span class="fw-bold">{{ $classroom->users()->wherePivot('role', 'Member')->count() }}</span>
                        </div>
                        <div class="d-flex justify-content-between smaller">
                            <span class="text-muted">Materials</span>
                            <span class="fw-bold">{{ $classroom->materials->count() }}</span>
                        </div>
                    </div>
                </div>

                <!-- Feed Content -->
                <div class="col-lg-9">
                    @php
                        $activities = $classroom->assignments->concat($classroom->materials)->sortByDesc('created_at');
                    @endphp

                    @forelse($activities as $activity)
                        <div class="card border-0 shadow-sm rounded-5 mb-4 activity-luxury-item transition-all overflow-hidden" id="activity-{{ $activity->id }}">
                            <div class="p-4 p-md-5">
                                <div class="d-flex justify-content-between align-items-start mb-4">
                                    <div class="d-flex align-items-center gap-4">
                                        <div class="activity-type-icon shadow-sm rounded-4 p-3 {{ isset($activity->due_date) ? 'bg-primary-soft' : 'bg-success-soft' }}">
                                            <i data-lucide="{{ isset($activity->due_date) ? 'file-text' : 'book-open' }}" size="28" class="{{ isset($activity->due_date) ? 'text-primary' : 'text-success' }}"></i>
                                        </div>
                                        <div>
                                            <h4 class="fw-extrabold mb-1 text-main">{{ $activity->title }}</h4>
                                            <div class="text-muted d-flex align-items-center gap-2 smaller font-jakarta fw-medium">
                                                <span>{{ $activity->created_at->format('F d, Y') }}</span>
                                                <span>&bull;</span>
                                                <span class="text-primary">{{ isset($activity->due_date) ? 'Assignment' : 'Material' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    @if(auth()->id() === $classroom->teacher_id)
                                    <div class="dropdown">
                                        <button class="btn btn-light rounded-circle p-2 border-0" data-bs-toggle="dropdown">
                                            <i data-lucide="more-vertical" size="18"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-3">
                                            <li><a class="dropdown-item fw-bold" href="javascript:void(0)" onclick="editActivity('{{ $activity->id }}', '{{ isset($activity->due_date) ? 'assignment' : 'material' }}', {{ json_encode($activity->files ?? []) }})"><i data-lucide="edit-2" size="16" class="me-2 text-primary"></i> Edit Post</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item text-danger fw-bold" href="javascript:void(0)" onclick="deleteActivity('{{ $activity->id }}', '{{ isset($activity->due_date) ? 'assignment' : 'material' }}')"><i data-lucide="trash-2" size="16" class="me-2"></i> Delete Post</a></li>
                                        </ul>
                                    </div>
                                    @endif
                                </div>

                                <div class="activity-description mb-4 px-2 text-main" style="font-size: 1.05rem; line-height: 1.6;">
                                    {!! $activity->description !!}
                                </div>

                                @if(!empty($activity->files))
                                    <div class="row g-3 mb-4">
                                        @foreach($activity->files as $file)
                                            @php $f = json_decode($file, true); @endphp
                                            <div class="col-md-6 col-xl-4 font-jakarta">
                                                <a href="{{ route('download.file', ['path' => $f['path'], 'assignment_id' => isset($activity->due_date) ? $activity->id : null]) }}" class="attachment-luxury-card d-flex align-items-center gap-3 p-3 text-decoration-none">
                                                    <div class="p-2 bg-light rounded-3">
                                                       <i data-lucide="file" class="text-primary" size="20"></i>
                                                    </div>
                                                    <div class="overflow-hidden">
                                                        <div class="fw-bold small text-main text-truncate">{{ $f['name'] }}</div>
                                                        <div class="text-muted smallest">{{ round($f['size']/1024/1024, 2) }} MB</div>
                                                    </div>
                                                </a>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                <div class="d-flex justify-content-between align-items-center pt-4 border-top">
                                    <div class="interaction-status">
                                        @if(auth()->id() === $classroom->teacher_id && isset($activity->due_date))
                                            <button class="btn btn-primary-soft rounded-pill px-4 py-2 fw-bold border-0 smaller transition-all">
                                                <i data-lucide="users" size="16" class="me-2"></i> {{ $activity->submissions->count() }} Submissions
                                            </button>
                                        @elseif(isset($activity->due_date))
                                            @php $submission = $activity->submissions->where('user_id', auth()->id())->first(); @endphp
                                            @if($submission)
                                                <span class="badge rounded-pill bg-success text-white px-4 py-2 fw-bold shadow-sm">
                                                    <i data-lucide="check" size="14" class="me-2"></i> Handed In
                                                </span>
                                            @else
                                                <button class="btn btn-primary rounded-pill px-4 py-2 fw-extrabold shadow-sm" data-bs-toggle="modal" data-bs-target="#submitAssignmentModal{{ $activity->id }}">
                                                    Turn In Now
                                                </button>
                                            @endif
                                        @endif
                                    </div>
                                    <button class="btn btn-light-luxury rounded-pill px-4 py-2 smaller fw-bold" onclick="toggleComments('comments-{{ $activity->id }}')">
                                        <i data-lucide="message-circle" size="16" class="me-2"></i> {{ $activity->comments->count() }} Discussion
                                    </button>
                                </div>

                                <!-- Comments -->
                                <div id="comments-{{ $activity->id }}" class="mt-5 p-4 rounded-5 bg-light-subtle d-none transition-fade">
                                    <div class="comments-list mb-4">
                                        @foreach($activity->comments as $comment)
                                            <div class="d-flex gap-3 mb-3">
                                                <img src="https://ui-avatars.com/api/?name={{ urlencode($comment->user->name) }}&background=6366f1&color=fff" class="rounded-circle shadow-sm" width="32" height="32">
                                                <div class="flex-grow-1 p-3 rounded-4 bg-card border shadow-sm">
                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                        <span class="fw-bold small">{{ $comment->user->name }}</span>
                                                        <span class="text-muted smallest">{{ $comment->created_at->diffForHumans() }}</span>
                                                    </div>
                                                    <div class="small">{!! $comment->content !!}</div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <form action="{{ route('comments.store', $classroom) }}" method="POST" class="d-flex gap-3 align-items-center ajax-comment-form">
                                        @csrf
                                        <input type="hidden" name="commentable_id" value="{{ $activity->id }}">
                                        <input type="hidden" name="commentable_type" value="{{ get_class($activity) }}">
                                        <div class="input-group luxury-comment-input flex-grow-1">
                                            <input type="text" name="content" class="form-control border-0 bg-transparent px-4 py-3 shadow-none" placeholder="Add a public comment..." required>
                                            <button class="btn btn-primary rounded-circle m-1" type="submit" style="width: 44px; height: 44px;"><i data-lucide="send" size="18"></i></button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5 rounded-5 border-dashed bg-light-subtle">
                            <i data-lucide="inbox" size="64" class="text-muted opacity-25 mb-4"></i>
                            <h4 class="fw-bold text-muted">No activities posted yet</h4>
                            <p class="text-muted">The stream is quiet... for now.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- People Tab -->
        <div class="tab-pane fade" id="members">
            <div class="max-width-800 mx-auto mt-5">
                <section class="mb-5">
                    <div class="d-flex justify-content-between align-items-center border-bottom border-primary border-3 pb-3 mb-4">
                        <h3 class="fw-extrabold text-primary m-0">Instructors</h3>
                        <i data-lucide="shield-check" class="text-primary opacity-50" size="24"></i>
                    </div>
                    <div class="d-flex align-items-center justify-content-between p-3 rounded-4 bg-card mb-2 shadow-sm border">
                        <div class="d-flex align-items-center gap-3 ps-2">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($classroom->teacher->name) }}&background=6366f1&color=fff" class="rounded-circle shadow-sm" width="48" height="48">
                            <span class="fw-extrabold h5 m-0 text-main">{{ $classroom->teacher->name }}</span>
                        </div>
                    </div>
                </section>

                <section>
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4 mt-5">
                        <h3 class="fw-extrabold text-main m-0">Students</h3>
                        <span class="badge bg-primary-soft text-primary rounded-pill px-3 py-2 fw-bold">{{ $classroom->users()->wherePivot('role', 'Member')->count() }} people</span>
                    </div>
                    @foreach($classroom->users()->wherePivot('role', 'Member')->get() as $student)
                        <div class="d-flex align-items-center justify-content-between p-3 rounded-4 bg-card mb-3 shadow-sm border transition-all hover-border-primary group-item">
                            <div class="d-flex align-items-center gap-3 ps-2">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($student->name) }}&background=random" class="rounded-circle shadow-sm" width="45" height="45">
                                <div>
                                    <span class="fw-bold d-block text-main">{{ $student->name }}</span>
                                    <span class="smaller text-muted">@<span></span>{{ $student->username }}</span>
                                </div>
                            </div>
                            @if(auth()->id() === $classroom->teacher_id)
                                <form action="{{ route('courses.kick', [$classroom, $student]) }}" method="POST" id="kick-form-{{ $student->id }}">
                                    @csrf
                                    <button type="button" class="btn btn-outline-danger rounded-circle p-2 transition-all" 
                                            onclick="kickMember('{{ $student->id }}', '{{ addslashes($student->name) }}')">
                                        <i data-lucide="user-minus" size="18"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endforeach
                </section>
            </div>
        </div>
    </div>

    <!-- Modals -->
    <div class="modal fade" id="bannerCropModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content overflow-hidden">
                <div class="modal-header bg-dark text-white p-4">
                    <h5 class="modal-title fw-bold">Reposition Cover</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0 bg-black">
                    <img id="banner-crop-target" src="" class="img-fluid w-100">
                </div>
                <div class="modal-footer p-4">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary rounded-pill px-5 fw-bold shadow" id="apply-banner-crop">Apply Changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Class Modal -->
    <div class="modal fade" id="editClassModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content overflow-hidden">
                <div class="modal-header border-0 p-5 pb-0">
                    <h3 class="fw-extrabold text-main m-0">Class Information</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('courses.update', $classroom) }}" method="POST">
                    @csrf
                    <div class="modal-body p-5">
                        <div class="mb-4">
                            <label class="form-label">Space Title</label>
                            <input type="text" name="title" class="form-control" value="{{ $classroom->title }}" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Objective / Description</label>
                            <textarea name="description" class="form-control" rows="4">{{ $classroom->description }}</textarea>
                        </div>
                        <div class="mb-0">
                            <label class="form-label">Tags (Meta Data)</label>
                            <input type="text" name="tags" class="form-control" value="{{ implode(', ', $classroom->tags ?? []) }}">
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-5 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow">Save Updates</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @include('courses.partials.modals')

    <script>
        function toggleComments(id) {
            const el = document.getElementById(id);
            el.classList.toggle('d-none');
            lucide.createIcons();
        }

        let currentEditDz = null;
        function editActivity(id, type, files) {
            const modalId = type === 'assignment' ? '#editAssignmentModal' : '#editMaterialModal';
            const modalEl = document.querySelector(modalId);
            const modal = new bootstrap.Modal(modalEl);
            const form = modalEl.querySelector('form');
            form.action = `/classes/{{ $classroom->id }}/${type}s/${id}`;
            const item = document.querySelector(`#activity-${id}`);
            const title = item.querySelector('h4').innerText;
            const description = item.querySelector('.activity-description').innerHTML.trim();
            form.querySelector('[name="title"]').value = title;
            if (type === 'assignment') {
                if (typeof tinymce !== 'undefined' && tinymce.get('edit-assignment-editor')) {
                    tinymce.get('edit-assignment-editor').setContent(description);
                }
            } else {
                form.querySelector('[name="description"]').value = description.replace(/<[^>]*>/g, '').trim();
            }
            if (currentEditDz) { currentEditDz.destroy(); currentEditDz = null; }
            const dzId = type === 'assignment' ? '#dropzone-edit-assignment' : '#dropzone-edit-material';
            const inputsId = type === 'assignment' ? '#edit-assignment-files-container' : '#edit-material-files-container';
            const container = document.querySelector(inputsId);
            container.innerHTML = '';
            currentEditDz = new Dropzone(dzId, {
                url: "{{ route('upload') }}",
                maxFiles: 10,
                maxFilesize: 50,
                addRemoveLinks: true,
                headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                init: function() {
                    files.forEach(f => {
                        const file = typeof f === 'string' ? JSON.parse(f) : f;
                        const mock = { name: file.name, size: file.size, accepted: true };
                        this.displayExistingFile(mock, `{{ asset('storage') }}/${file.path}`);
                        const input = document.createElement('input');
                        input.type = 'hidden'; input.name = 'files[]'; input.value = JSON.stringify(file);
                        container.appendChild(input);
                    });
                },
                success: (file, res) => {
                    const input = document.createElement('input');
                    input.type = 'hidden'; input.name = 'files[]'; input.value = JSON.stringify(res);
                    input.id = 'file-' + file.upload.uuid;
                    container.appendChild(input);
                },
                removedfile: (file) => {
                    if (file.upload) {
                        const el = document.getElementById('file-' + file.upload.uuid);
                        if (el) el.remove();
                    } else {
                        const inputs = container.querySelectorAll('input');
                        inputs.forEach(i => { if (JSON.parse(i.value).name === file.name) i.remove(); });
                    }
                    if (file.previewElement && file.previewElement.parentNode) file.previewElement.parentNode.removeChild(file.previewElement);
                }
            });
            modal.show();
        }

        document.addEventListener('DOMContentLoaded', function() {
            const bannerInput = document.getElementById('banner-upload-input');
            const cropModal = new bootstrap.Modal(document.getElementById('bannerCropModal'));
            const cropTarget = document.getElementById('banner-crop-target');
            let cropper;

            bannerInput?.addEventListener('change', function(e) {
                const files = e.target.files;
                if (files && files.length > 0) {
                    const reader = new FileReader();
                    reader.onload = function() {
                        cropTarget.src = reader.result;
                        cropModal.show();
                    };
                    reader.readAsDataURL(files[0]);
                }
            });

            document.getElementById('bannerCropModal').addEventListener('shown.bs.modal', function() {
                cropper = new Cropper(cropTarget, {
                    aspectRatio: 1000 / 240,
                    viewMode: 2,
                    guides: true,
                    responsive: true,
                    background: false,
                });
            });

            document.getElementById('bannerCropModal').addEventListener('hidden.bs.modal', function() {
                if(cropper) { cropper.destroy(); cropper = null; }
                bannerInput.value = '';
            });

            document.getElementById('apply-banner-crop').addEventListener('click', function() {
                if(!cropper) return;
                const canvas = cropper.getCroppedCanvas({ width: 1000, height: 240 });
                canvas.toBlob((blob) => {
                    const reader = new FileReader();
                    reader.readAsDataURL(blob);
                    reader.onloadend = function() {
                        const base64data = reader.result;
                        fetch("{{ route('courses.update_banner', $classroom) }}", {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({ banner: base64data })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if(data.success) {
                                showToast('Banner updated!', 'success');
                                location.reload();
                            }
                        });
                    };
                });
            });

            // AJAX Comment Handling
            document.querySelectorAll('.ajax-comment-form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const formData = new FormData(this);
                    const list = this.closest('.tab-pane').querySelector(`#comments-${formData.get('commentable_id')} .comments-list`);
                    const btn = this.querySelector('button[type="submit"]');
                    btn.disabled = true;

                    fetch(this.action, {
                        method: 'POST',
                        body: formData,
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if(data.success) {
                            const newComment = `
                                <div class="d-flex gap-3 mb-3 animation-fade-in">
                                    <img src="${data.avatar}" class="rounded-circle shadow-sm" width="32" height="32">
                                    <div class="flex-grow-1 p-3 rounded-4 bg-card border shadow-sm">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="fw-bold small">${data.user_name}</span>
                                            <span class="text-muted smallest">Just now</span>
                                        </div>
                                        <div class="small">${data.content}</div>
                                    </div>
                                </div>
                            `;
                            list.insertAdjacentHTML('beforeend', newComment);
                            this.reset();
                            lucide.createIcons();
                        }
                    })
                    .finally(() => btn.disabled = false);
                });
            });
        });

        function kickMember(studentId, studentName) {
            showConfirm({
                title: 'Kick Member',
                message: 'Apakah anda yakin ingin menendang ' + studentName + '?',
                btnText: 'Yes, Kick Member',
                btnClass: 'btn-danger',
                onConfirm: () => {
                    document.getElementById(`kick-form-${studentId}`).submit();
                }
            });
        }

        function deleteActivity(id, type) {
            showConfirm({
                title: 'Delete Activity',
                message: 'Are you sure you want to permanently delete this ' + type + '? This action cannot be undone.',
                btnText: 'Delete Permanently',
                onConfirm: () => {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/classes/{{ $classroom->id }}/${type}s/${id}`;
                    form.innerHTML = `@csrf @method('DELETE')`;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        function editActivity(id, type, files) {
            const activityCard = document.getElementById(`activity-${id}`);
            const title = activityCard.querySelector('h4').innerText;
            const description = activityCard.querySelector('.activity-description').innerHTML.trim();
            
            let modal, form, dzElement;
            if (type === 'assignment') {
                modal = new bootstrap.Modal(document.getElementById('editAssignmentModal'));
                form = document.getElementById('editAssignmentForm');
                dzElement = "#dropzone-edit-assignment";
                form.action = `/classes/{{ $classroom->id }}/assignments/${id}/update`;
                form.querySelector('[name="title"]').value = title;
                tinymce.get('edit-assignment-editor').setContent(description);
            } else {
                modal = new bootstrap.Modal(document.getElementById('editMaterialModal'));
                form = document.getElementById('editMaterialForm');
                dzElement = "#dropzone-edit-material";
                form.action = `/classes/{{ $classroom->id }}/materials/${id}/update`;
                form.querySelector('[name="title"]').value = title;
                form.querySelector('[name="description"]').value = description.replace(/<[^>]*>?/gm, ''); // strip html for material desc
            }

            // Init or Reset Dropzone
            const existingDz = Dropzone.instances.find(
                dz => dz.element.id === dzElement.replace('#', '')
            );
            if (existingDz) existingDz.destroy();

            const dz = new Dropzone(dzElement, {
                url: "{{ route('upload') }}",
                maxFilesize: 50,
                maxFiles: 10,
                autoProcessQueue: true,
                addRemoveLinks: true,
                dictRemoveFile: "Remove",
                headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                paramName: "file",
                clickable: true
            });

            dz.on("success", (file, response) => {
                const hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = 'files[]';
                hidden.value = JSON.stringify(response);
                file.previewElement.appendChild(hidden);
            });

            // 4. Pre-populate existing files as per guide (emit pattern)
            if (files && files.length > 0) {
                files.forEach(f => {
                    const fileData = typeof f === 'string' ? JSON.parse(f) : f;
                    const mockFile = { name: fileData.name, size: fileData.size, status: Dropzone.ADDED, accepted: true };
                    
                    dz.emit("addedfile", mockFile);
                    dz.emit("thumbnail", mockFile, "https://cdn-icons-png.flaticon.com/512/2991/2991108.png");
                    dz.emit("complete", mockFile);
                    
                    // Add existing hidden input so it's kept on save
                    const hidden = document.createElement('input');
                    hidden.type = 'hidden';
                    hidden.name = 'files[]';
                    hidden.value = JSON.stringify(fileData);
                    mockFile.previewElement.appendChild(hidden);
                });
            }
            
            modal.show();
        }
    </script>

    <!-- Edit Assignment Modal -->
    <div class="modal fade" id="editAssignmentModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content overflow-hidden">
                <div class="modal-header border-0 p-5 pb-2">
                    <h3 class="fw-extrabold text-main m-0 d-flex align-items-center gap-3">
                        <div class="p-2 bg-primary-soft rounded-3 text-primary">
                            <i data-lucide="edit-3" size="24"></i>
                        </div>
                        Edit Assignment
                    </h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editAssignmentForm" method="POST" enctype="multipart/form-data" class="upload-box-form">
                    @csrf
                    <div class="modal-body p-5 pt-3">
                        <div class="mb-4">
                            <label class="form-label">Task Title</label>
                            <input type="text" name="title" class="form-control" placeholder="e.g. Weekly Reflection" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Detailed Instructions</label>
                            <textarea name="description" id="edit-assignment-editor" class="form-control"></textarea>
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
                            <label class="form-label">Update Reference Material (Optional)</label>
                            <div class="dropzone dz-luxury rounded-4" id="dropzone-edit-assignment"></div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-5 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Discard</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Material Modal -->
    <div class="modal fade" id="editMaterialModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content overflow-hidden">
                <div class="modal-header border-0 p-5 pb-2">
                    <h3 class="fw-extrabold text-main m-0 d-flex align-items-center gap-3">
                        <div class="p-2 bg-success-soft rounded-3 text-success">
                            <i data-lucide="book-open" size="24"></i>
                        </div>
                        Edit Material
                    </h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editMaterialForm" method="POST" enctype="multipart/form-data" class="upload-box-form">
                    @csrf
                    <div class="modal-body p-5 pt-3">
                        <div class="mb-4">
                            <label class="form-label">Material Name</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Brief Overview</label>
                            <textarea name="description" class="form-control" rows="4"></textarea>
                        </div>
                        <div class="mb-0">
                            <label class="form-label">Update Resources</label>
                            <div class="dropzone dz-luxury rounded-4" id="dropzone-edit-material"></div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-5 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Discard</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow">Save Material</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof tinymce !== 'undefined') {
                tinymce.init({
                    selector: '#edit-assignment-editor',
                    height: 250,
                    menubar: false,
                    skin: (document.body.getAttribute('data-bs-theme') === 'dark' ? "oxide-dark" : "oxide"),
                    content_css: (document.body.getAttribute('data-bs-theme') === 'dark' ? "dark" : "default"),
                    plugins: 'lists link emoticons image code',
                    toolbar: 'bold italic underline | numlist bullist | link image emoticons | code',
                    setup: editor => editor.on('change', () => editor.save())
                });
            }

            // TintMCE already handled above in editActivity
        });
    </script>

    <style>
        .banner-overlay-gradient { position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(180deg, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0.6) 100%); }
        .btn-glass-luxury { background: rgba(255,255,255,0.1); backdrop-filter: blur(12px); color: white; border: 1px solid rgba(255,255,255,0.2); font-weight: 700; transition: all 0.2s; }
        .btn-glass-luxury:hover { background: rgba(255,255,255,0.25); color: white; transform: translateY(-2px); }
        .luxury-tabs-under .nav-link { color: var(--text-muted); border: none; border-bottom: 4px solid transparent; transition: all 0.3s; opacity: 0.8; font-size: 0.95rem; }
        .luxury-tabs-under .nav-link.active { color: var(--primary-color); border-bottom-color: var(--primary-color); opacity: 1; }
        .activity-luxury-item { border: 1px solid var(--border-color); background: var(--card-bg); box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
        .activity-luxury-item:hover { border-color: var(--primary-color); }
        .attachment-luxury-card { background: var(--bg-color); border-radius: 14px; border: 1px solid var(--border-color); transition: all 0.2s; }
        .attachment-luxury-card:hover { border-color: var(--primary-color); background: var(--card-bg); transform: translateY(-2px); box-shadow: 0 8px 15px rgba(0,0,0,0.05); }
        .luxury-comment-input { background: var(--bg-color); border: 1.5px solid var(--border-color); border-radius: 50px; }
        .max-width-800 { max-width: 800px; }
        .bg-primary-soft { background: rgba(var(--primary-rgb), 0.1); }
        .bg-success-soft { background: rgba(16, 185, 129, 0.1); }
        .text-main { color: var(--text-color); }
        .btn-primary-soft { background: rgba(var(--primary-rgb), 0.1); color: var(--primary-color); border: none; }
        .btn-primary-soft:hover { background: var(--primary-color); color: white; }
        .ls-2 { letter-spacing: 2px; }
        .activity-type-icon { transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
        .activity-luxury-item:hover .activity-type-icon { transform: scale(1.1) rotate(-5deg); }
    </style>
@endsection
