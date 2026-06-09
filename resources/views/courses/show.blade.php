@extends('layouts.app')

@section('content')
    <!-- Class Header Section -->
    <div class="row g-4 mb-4 align-items-center" style="position: relative; z-index: 1025;">
        <div class="col-md-6">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb fw-bold mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('courses.index') }}" class="text-decoration-none text-muted">My Spaces</a></li>
                    <li class="breadcrumb-item active text-primary" aria-current="page">{{ $classroom->title }}</li>
                </ol>
            </nav>
            <h2 class="fw-extrabold m-0 text-main">{{ $classroom->title }}</h2>
        </div>
        <div class="col-md-6 text-md-end" style="position: relative; z-index: 1020;">
            <div class="d-flex gap-2 justify-content-md-end flex-wrap">
                @if(auth()->id() === $classroom->teacher_id)
                    <button class="btn btn-luxury-light rounded-pill px-4 fw-bold border shadow-sm btn-ripple" data-bs-toggle="modal" data-bs-target="#editClassModal" onclick="addRipple(event, this)">
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
                            <li><a class="dropdown-item rounded-3 py-2 fw-bold" href="#" data-bs-toggle="modal" data-bs-target="#createAnnouncementModal"><i data-lucide="megaphone" size="18" class="me-3 text-warning"></i> Announcement</a></li>
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
    <div class="card border-0 shadow-sm rounded-5 overflow-hidden mb-4 luxury-banner-card" style="position: relative; z-index: 10;">
        <div class="classroom-banner-wrapper position-relative" style="height: 240px; background: {{ $classroom->banner ? 'url('.asset('storage/' . $classroom->banner).')' : 'linear-gradient(135deg, var(--primary-color), var(--secondary-color))' }}; background-size: cover; background-position: center; z-index: 10;">
            <div class="banner-overlay-gradient"></div>
            
            @if(auth()->id() === $classroom->teacher_id)
                <input type="file" id="banner-upload-input" class="d-none" accept="image/*">
            @endif

            <div class="position-absolute bottom-0 start-0 p-5 text-white z-2 w-100">
                <div class="d-flex justify-content-between align-items-end">
                    <div>
                        <h1 class="fw-extrabold mb-1" style="font-size: 2.5rem; text-shadow: 0 2px 10px rgba(0,0,0,0.3);">{{ $classroom->title }}</h1>
                        <p class="mb-3 opacity-90 fw-medium" style="max-width: 600px;">{{ $classroom->description ?: 'No description provided.' }}</p>
                        
                        @if($classroom->tags)
                        <div class="d-flex gap-2 flex-wrap">
                            @foreach($classroom->tags as $tag)
                                <span class="badge bg-glass-luxury rounded-pill px-3 py-1 fw-bold smallest">{{ $tag }}</span>
                            @endforeach
                        </div>
                        @endif
                    </div>
                    
                    <div class="banner-actions d-flex gap-2">
                        @if(auth()->id() === $classroom->teacher_id)
                            <div class="dropdown">
                                <button class="btn-info-luxury hover-scale" data-bs-toggle="dropdown" title="Customize banner">
                                    <i data-lucide="image" size="24"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-4 p-2">
                                    <li>
                                        <button class="dropdown-item rounded-3 py-2 fw-bold" onclick="document.getElementById('banner-upload-input').click()">
                                            <i data-lucide="upload" size="18" class="me-3 text-primary"></i> Change Cover
                                        </button>
                                    </li>
                                    @if($classroom->banner)
                                    <li>
                                        <button class="dropdown-item rounded-3 py-2 fw-bold text-danger" onclick="document.getElementById('delete-banner-form').submit()">
                                            <i data-lucide="trash-2" size="18" class="me-3"></i> Reset Default
                                        </button>
                                    </li>
                                    @endif
                                </ul>
                            </div>
                        @endif
                        <button class="btn-info-luxury" onclick="toggleBannerInfo()" id="bannerInfoBtn" title="Show class information">
                            <i data-lucide="info" size="24"></i>
                        </button>
                    </div>
                </div>
            </div>

        </div> <!-- End Wrapper -->

        <!-- Expandable Info Area -->
        <div class="banner-info-panel" id="bannerInfoPanel">
            <div class="p-4 p-md-5">
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="info-item-luxury">
                            <label>Teacher</label>
                            <div class="value">{{ $classroom->teacher->name }}</div>
                        </div>
                    </div>
                    @if(auth()->id() === $classroom->teacher_id)
                    <div class="col-md-4">
                        <div class="info-item-luxury">
                            <label>Class Code</label>
                            <div class="value d-flex align-items-center gap-3">
                                <span class="fw-bold text-primary">{{ $classroom->code }}</span>
                                <button class="btn btn-luxury-light rounded-pill px-3 py-1 smallest transition-all" onclick="copyClassCode('{{ $classroom->code }}')">
                                    <i data-lucide="copy" size="12" class="me-1"></i> Copy
                                </button>
                            </div>
                        </div>
                    </div>
                    @endif
                    <div class="col-md-4">
                        <div class="info-item-luxury">
                            <label>Created At</label>
                            <div class="value">{{ $classroom->created_at->format('F d, Y') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function copyClassCode(code) {
                navigator.clipboard.writeText(code).then(() => {
                    showToast('Class code copied to clipboard!', 'success');
                });
            }
            
            function toggleBannerInfo() {
                const panel = document.getElementById('bannerInfoPanel');
                const btn = document.getElementById('bannerInfoBtn');
                panel.classList.toggle('active');
                btn.classList.toggle('active');
            }
        </script>
    </div>

    <!-- Navigation Area -->
    <div class="card border-0 shadow-sm rounded-4 mb-5 overflow-hidden" style="background: var(--card-bg);">
        <ul class="nav nav-tabs border-0 justify-content-center luxury-tabs-under" id="classTab" role="tablist">
            <li class="nav-item">
                <button class="nav-link active px-5 py-3 fw-bold" id="stream-tab" data-bs-toggle="tab" data-bs-target="#stream">Lessosns</button>
            </li>
            <li class="nav-item">
                <button class="nav-link px-5 py-3 fw-bold" id="people-tab" data-bs-toggle="tab" data-bs-target="#members">Participants</button>
            </li>
        </ul>
    </div>

    <div class="tab-content" id="classTabContent">
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
                        $activities = $classroom->assignments
                            ->concat($classroom->materials)
                            ->concat($classroom->announcements)
                            ->sortByDesc('created_at');
                    @endphp

                    @forelse($activities as $activity)
                        <div class="card border-0 shadow-sm rounded-5 mb-4 activity-luxury-item transition-all overflow-hidden" id="activity-{{ $activity->id }}">
                            <div class="p-4 p-md-5">
                                <div class="d-flex justify-content-between align-items-start mb-4">
                                    <div class="d-flex align-items-center gap-4">
                                        @php
                                            $type = 'material';
                                            if (isset($activity->due_date)) $type = 'assignment';
                                            elseif ($activity instanceof \App\Models\Announcement) $type = 'announcement';
                                            
                                            $icon = 'book-open';
                                            $colorClass = 'bg-success-soft text-success';
                                            if ($type === 'assignment') {
                                                $icon = 'file-text';
                                                $colorClass = 'bg-primary-soft text-primary';
                                            } elseif ($type === 'announcement') {
                                                $icon = 'megaphone';
                                                $colorClass = 'bg-warning-soft text-warning';
                                            }
                                        @endphp
                                        <div class="activity-type-icon shadow-sm rounded-4 p-3 {{ $colorClass }}">
                                            <i data-lucide="{{ $icon }}" size="28"></i>
                                        </div>
                                        <div>
                                            <h4 class="fw-extrabold mb-1 text-main">{{ $activity->title }}</h4>
                                            <div class="text-muted d-flex align-items-center gap-2 smaller font-jakarta fw-medium">
                                                <span>{{ $activity->created_at->format('F d, Y') }}</span>
                                                <span>&bull;</span>
                                                <span class="{{ $type === 'announcement' ? 'text-warning' : ($type === 'assignment' ? 'text-primary' : 'text-success') }}">
                                                    {{ ucfirst($type) }}
                                                </span>
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
                                            @php 
                                                $f = is_array($file) ? $file : json_decode($file, true); 
                                            @endphp
                                            @if(is_array($f) && isset($f['path']))
                                            <div class="col-md-6 col-xl-4 font-jakarta">
                                                <a href="{{ route('download.file', ['path' => $f['path'], 'assignment_id' => isset($activity->due_date) ? $activity->id : null]) }}" class="attachment-luxury-card d-flex align-items-center gap-3 p-3 text-decoration-none">
                                                    <div class="p-2 bg-light rounded-3">
                                                       <i data-lucide="file" class="text-primary" size="20"></i>
                                                    </div>
                                                    <div class="overflow-hidden">
                                                        <div class="fw-bold small text-main text-truncate">{{ $f['name'] ?? 'File' }}</div>
                                                        <div class="text-muted smallest">{{ isset($f['size']) ? round($f['size']/1024/1024, 2) : '0' }} MB</div>
                                                    </div>
                                                </a>
                                            </div>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif

                                <div class="d-flex justify-content-between align-items-center pt-4 border-top">
                                    <div class="interaction-status">
                                        @if(auth()->id() === $classroom->teacher_id && isset($activity->due_date))
                                            <button class="btn btn-primary-soft rounded-pill px-4 py-2 fw-bold border-0 smaller transition-all" data-bs-toggle="modal" data-bs-target="#viewSubmissionsModal{{ $activity->id }}">
                                                <i data-lucide="users" size="16" class="me-2"></i> {{ $activity->submissions->count() }} Submissions
                                            </button>
                                        @elseif(isset($activity->due_date))
                                            @php $submission = $activity->submissions->where('user_id', auth()->id())->first(); @endphp
                                            @if($submission)
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="badge rounded-pill bg-success text-white px-4 py-2 fw-bold shadow-sm">
                                                        <i data-lucide="check" size="14" class="me-2"></i> Handed In
                                                    </span>
                                                    <button class="btn btn-outline-secondary rounded-pill px-3 py-2 fw-bold smaller border-2 transition-all" data-bs-toggle="modal" data-bs-target="#editSubmissionModal{{ $activity->id }}">
                                                        Edit Work
                                                    </button>
                                                </div>
                                            @else
                                                <button class="btn btn-primary rounded-pill px-4 py-2 fw-extrabold shadow-sm btn-ripple" data-bs-toggle="modal" data-bs-target="#submitAssignmentModal{{ $activity->id }}" onclick="addRipple(event, this)">
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
                                <div id="comments-{{ $activity->id }}" class="mt-5 p-4 rounded-5 bg-light-subtle d-none transition-fade border shadow-inner">
                                    <div class="comments-list mb-4">
                                        @foreach($activity->comments()->whereNull('parent_id')->get() as $comment)
                                            <div class="comment-group mb-4" id="comment-{{ $comment->id }}">
                                                <div class="d-flex gap-3 comment-item-luxury position-relative group" data-comment-id="{{ $comment->id }}">
                                                    <img src="{{ $comment->user->avatar_path ? asset('storage/'.$comment->user->avatar_path) : 'https://ui-avatars.com/api/?name='.urlencode($comment->user->name).'&background=6366f1&color=fff' }}" class="rounded-circle shadow-sm" width="40" height="40">
                                                    <div class="flex-grow-1">
                                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                                            <div class="d-flex align-items-center gap-2">
                                                                <span class="fw-bold text-main small">{{ $comment->user->name }}</span>
                                                                <span class="text-muted smallest">• {{ $comment->created_at->diffForHumans() }}</span>
                                                            </div>
                                                            <div class="comment-actions opacity-0 group-hover-opacity-100 transition-all d-flex gap-2">
                                                                <button class="btn btn-sm btn-link p-0 text-muted hover-text-primary" onclick="prepareReply('{{ $comment->id }}', '{{ $comment->user->username }}', '{{ $activity->id }}')" title="Reply">
                                                                    <i data-lucide="reply" size="14"></i>
                                                                </button>
                                                                @if($comment->user_id === auth()->id())
                                                                    <button class="btn btn-sm btn-link p-0 text-muted hover-text-warning" onclick="prepareEdit('{{ $comment->id }}')" title="Edit">
                                                                        <i data-lucide="edit-2" size="14"></i>
                                                                    </button>
                                                                @endif
                                                                @if($comment->user_id === auth()->id() || auth()->id() === $classroom->teacher_id)
                                                                    <button class="btn btn-sm btn-link p-0 text-muted hover-text-danger" onclick="deleteComment('{{ $comment->id }}')" title="Delete">
                                                                        <i data-lucide="trash-2" size="14"></i>
                                                                    </button>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="comment-content text-main small bg-card border rounded-4 p-3 shadow-sm" id="content-{{ $comment->id }}">
                                                            {!! $comment->content !!}
                                                        </div>
                                                        
                                                        <div class="replies-list mt-3 ps-4 border-start border-2">
                                                            @foreach($comment->replies as $reply)
                                                                <div class="d-flex gap-3 mb-3 comment-item-luxury position-relative group" data-comment-id="{{ $reply->id }}">
                                                                    <img src="{{ $reply->user->avatar_path ? asset('storage/'.$reply->user->avatar_path) : 'https://ui-avatars.com/api/?name='.urlencode($reply->user->name).'&background=random' }}" class="rounded-circle shadow-sm" width="32" height="32">
                                                                    <div class="flex-grow-1">
                                                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                                                            <div class="d-flex align-items-center gap-2">
                                                                                <span class="fw-bold text-main smallest">{{ $reply->user->name }}</span>
                                                                                <span class="text-muted smallest">• {{ $reply->created_at->diffForHumans() }}</span>
                                                                            </div>
                                                                            <div class="comment-actions opacity-0 group-hover-opacity-100 transition-all d-flex gap-2">
                                                                                <button class="btn btn-sm btn-link p-0 text-muted hover-text-primary" onclick="prepareReply('{{ $comment->id }}', '{{ $reply->user->username }}', '{{ $activity->id }}')" title="Reply">
                                                                                    <i data-lucide="reply" size="12"></i>
                                                                                </button>
                                                                                @if($reply->user_id === auth()->id())
                                                                                    <button class="btn btn-sm btn-link p-0 text-muted hover-text-warning" onclick="prepareEdit('{{ $reply->id }}')" title="Edit">
                                                                                        <i data-lucide="edit-2" size="12"></i>
                                                                                    </button>
                                                                                @endif
                                                                                @if($reply->user_id === auth()->id() || auth()->id() === $classroom->teacher_id)
                                                                                    <button class="btn btn-sm btn-link p-0 text-muted hover-text-danger" onclick="deleteComment('{{ $reply->id }}')" title="Delete">
                                                                                        <i data-lucide="trash-2" size="12"></i>
                                                                                    </button>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                        <div class="comment-content text-main smallest bg-card-subtle border rounded-3 p-2 shadow-sm" id="content-{{ $reply->id }}">
                                                                            {!! $reply->content !!}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <form action="{{ route('comments.store', $classroom) }}" method="POST" class="ajax-comment-form">
                                        @csrf
                                        <input type="hidden" name="commentable_id" value="{{ $activity->id }}">
                                        <input type="hidden" name="commentable_type" value="{{ get_class($activity) }}">
                                        <input type="hidden" name="parent_id" class="comment-parent-id" value="">
                                        
                                        <div class="reply-to-overlay d-none mb-2 px-3 py-2 bg-primary-soft rounded-4 d-flex justify-content-between align-items-center">
                                            <span class="smallest fw-bold text-primary">Replying to <span class="reply-username"></span></span>
                                            <button type="button" class="btn btn-sm p-0 text-primary" onclick="cancelReply(this)"><i data-lucide="x" size="14"></i></button>
                                        </div>

                                        <div class="comment-input-wrap">
                                            <input type="text" name="content" class="comment-input" placeholder="Add a public comment..." autocomplete="off" required>
                                            <button type="submit" class="comment-send-btn btn-ripple" onclick="addRipple(event, this)">
                                                <i data-lucide="send" style="width:16px;height:16px;transform:translate(-1px,1px)"></i>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                                
                                @push('modals')
                                    @if(isset($activity->due_date))
                                        @include('courses.partials.submission_modal')
                                        @if(auth()->id() === $classroom->teacher_id)
                                            @include('courses.partials.view_submissions_modal')
                                        @endif
                                    @endif
                                @endpush
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

        <div class="tab-pane fade" id="members">
            <div class="max-width-800 mx-auto mt-5">
                <section class="mb-5">
                    <div class="d-flex justify-content-between align-items-center border-bottom border-primary border-3 pb-3 mb-4">
                        <h3 class="fw-extrabold text-primary m-0">Teachers</h3>
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
    <form action="{{ route('courses.delete_banner', $classroom) }}" method="POST" id="delete-banner-form" class="d-none">
        @csrf
    </form>

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
                cropper.getCroppedCanvas({ width: 1000, height: 240 }).toBlob((blob) => {
                    showToast('Saving cropped banner...', 'info');
                    const formData = new FormData();
                    formData.append('banner', blob, 'banner.png');
                    formData.append('_token', '{{ csrf_token() }}');

                    fetch("{{ route('courses.update_banner', $classroom) }}", {
                        method: 'POST',
                        body: formData,
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if(data.success) {
                            showToast('Banner updated!', 'success');
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            showToast(data.message || 'Upload failed', 'error');
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        showToast('System error', 'error');
                    });
                }, 'image/png');
            });

            let touchTimer;
            document.addEventListener('touchstart', (e) => {
                const item = e.target.closest('.comment-item-luxury');
                if (item) {
                    touchTimer = setTimeout(() => {
                        item.classList.add('mobile-active');
                        item.querySelector('.comment-actions').style.opacity = '1';
                        if (navigator.vibrate) navigator.vibrate(50);
                    }, 500);
                }
            });

            document.addEventListener('touchend', (e) => {
                clearTimeout(touchTimer);
            });
            
            document.addEventListener('click', (e) => {
                if (!e.target.closest('.comment-item-luxury')) {
                    document.querySelectorAll('.comment-actions').forEach(a => a.style.opacity = '');
                }
            });

            window.prepareReply = function(id, username, activityId) {
                const container = document.getElementById(`comments-${activityId}`);
                const form = container.querySelector('.ajax-comment-form');
                form.querySelector('.comment-parent-id').value = id;
                form.querySelector('.reply-to-overlay').classList.remove('d-none');
                form.querySelector('.reply-username').textContent = '@' + username;
                const input = form.querySelector('.comment-input');
                input.value = '@' + username + ' ';
                input.focus();
            };

            window.cancelReply = function(btn) {
                const form = btn.closest('form');
                form.querySelector('.comment-parent-id').value = '';
                form.querySelector('.reply-to-overlay').classList.add('d-none');
                form.querySelector('.comment-input').value = '';
            };

            window.prepareEdit = function(id) {
                const contentEl = document.getElementById(`content-${id}`);
                const oldContent = contentEl.innerText.replace(/^@\w+\s/, '');
                const newContent = prompt('Edit your comment:', oldContent);
                if (newContent !== null && newContent.trim() !== '') {
                    fetch(`/comments/${id}/update`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest' },
                        body: JSON.stringify({ content: newContent })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) { contentEl.innerText = data.content; showToast('Comment updated', 'success'); }
                    });
                }
            };

            window.deleteComment = function(id) {
                showConfirm({
                    title: 'Delete Comment',
                    message: 'Are you sure you want to remove this comment?',
                    btnText: 'Delete',
                    onConfirm: () => {
                        fetch(`/comments/${id}`, {
                            method: 'DELETE',
                            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest' }
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                document.getElementById(`comment-${id}`)?.remove();
                                document.querySelector(`[data-comment-id="${id}"]`)?.remove();
                                showToast('Comment deleted', 'success');
                            }
                        });
                    }
                });
            };

            document.querySelectorAll('.ajax-comment-form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const formData = new FormData(this);
                    const parentId = formData.get('parent_id');
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
                            const newCommentHtml = `
                                <div class="d-flex gap-3 mb-3 comment-item-luxury group animation-fade-in" data-comment-id="${data.id}">
                                    <img src="${data.avatar}" class="rounded-circle shadow-sm" width="${parentId ? '32' : '40'}" height="${parentId ? '32' : '40'}">
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="fw-bold text-main small">${data.user_name}</span>
                                                <span class="text-muted smallest">• Just now</span>
                                            </div>
                                            <div class="comment-actions opacity-0 group-hover-opacity-100 transition-all d-flex gap-2">
                                                <button class="btn btn-sm btn-link p-0 text-muted hover-text-primary" onclick="prepareReply('${parentId ? parentId : data.id}', '${data.username}', '${formData.get('commentable_id')}')" title="Reply">
                                                    <i data-lucide="reply" size="${parentId ? '12' : '14'}"></i>
                                                </button>
                                                <button class="btn btn-sm btn-link p-0 text-muted hover-text-warning" onclick="prepareEdit('${data.id}')" title="Edit">
                                                    <i data-lucide="edit-2" size="${parentId ? '12' : '14'}"></i>
                                                </button>
                                                <button class="btn btn-sm btn-link p-0 text-muted hover-text-danger" onclick="deleteComment('${data.id}')" title="Delete">
                                                    <i data-lucide="trash-2" size="${parentId ? '12' : '14'}"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="comment-content text-main small bg-card border rounded-4 p-3 shadow-sm" id="content-${data.id}">
                                            ${data.content}
                                        </div>
                                    </div>
                                </div>`;

                            if(parentId) {
                                const parentGroup = document.getElementById(`comment-${parentId}`);
                                parentGroup.querySelector('.replies-list').insertAdjacentHTML('beforeend', newCommentHtml);
                                cancelReply(this);
                            } else {
                                const list = this.closest('.tab-pane').querySelector(`#comments-${formData.get('commentable_id')} .comments-list`);
                                const wrapper = document.createElement('div');
                                wrapper.className = 'comment-group mb-4';
                                wrapper.id = `comment-${data.id}`;
                                wrapper.innerHTML = newCommentHtml + `<div class="replies-list mt-3 ps-4 border-start border-2"></div>`;
                                list.appendChild(wrapper);
                            }
                            this.reset(); lucide.createIcons();
                        }
                        btn.disabled = false;
                    })
                    .catch(err => { console.error(err); btn.disabled = false; });
                });
            });
        });

        function kickMember(studentId, studentName) {
            showConfirm({
                title: 'Kick Member', message: 'Apakah anda yakin ingin menendang ' + studentName + '?', btnText: 'Yes, Kick Member', btnClass: 'btn-danger',
                onConfirm: () => { document.getElementById(`kick-form-${studentId}`).submit(); }
            });
        }

        function deleteActivity(id, type) {
            showConfirm({
                title: 'Delete Activity', message: 'Are you sure you want to permanently delete this ' + type + '?', btnText: 'Delete Permanently',
                onConfirm: () => {
                    const form = document.createElement('form');
                    form.method = 'POST'; form.action = `/classes/{{ $classroom->id }}/${type}s/${id}`;
                    form.innerHTML = `@csrf @method('DELETE')`; document.body.appendChild(form); form.submit();
                }
            });
        }
    </script>

    <style>
        .banner-overlay-gradient { position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(180deg, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0.6) 100%); }
        .luxury-tabs-under .nav-link { color: var(--text-muted); border: none; border-bottom: 4px solid transparent; transition: all 0.3s; opacity: 0.8; font-size: 0.95rem; }
        .luxury-tabs-under .nav-link.active { color: var(--primary-color); border-bottom-color: var(--primary-color); opacity: 1; }
        .activity-luxury-item { border: 1px solid var(--border-color); background: var(--card-bg); box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
        .attachment-luxury-card { background: var(--bg-color); border-radius: 14px; border: 1px solid var(--border-color); transition: all 0.2s; }
        .attachment-luxury-card:hover { border-color: var(--primary-color); background: var(--card-bg); transform: translateY(-2px); }
        .comment-input-wrap { position: relative; background: var(--bg-color); border: 1.5px solid var(--border-color); border-radius: 50px; padding: 4px; display: flex; align-items: center; }
        .comment-input { border: none; background: transparent; width: 100%; padding: 8px 20px; color: var(--text-color); outline: none; }
        .comment-send-btn { width: 36px; height: 36px; border-radius: 50%; border: none; background: var(--primary-color); color: white; display: flex; align-items: center; justify-content: center; transition: all 0.2s; }
        .group:hover .group-hover-opacity-100 { opacity: 1 !important; }
        .comment-item-luxury.mobile-active .comment-actions { opacity: 1 !important; }
        .bg-card-subtle { background: rgba(var(--primary-rgb), 0.03); }
        .btn-info-luxury { background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(8px); border: 1.5px solid rgba(255, 255, 255, 0.3); color: white; width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; transition: all 0.4s; }
        .btn-info-luxury.active { background: var(--primary-color); transform: rotate(180deg); }
        .banner-info-panel { max-height: 0; overflow: hidden; background: var(--card-bg); transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1); border-bottom: 2px solid var(--border-color); }
        .banner-info-panel.active { max-height: 200px; padding-bottom: 20px; }
        .info-item-luxury label { display: block; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; color: var(--text-muted); margin-bottom: 4px; }
        .info-item-luxury .value { font-size: 1.1rem; font-weight: 700; color: var(--text-color); }
        .bg-glass-luxury { background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.2); color: white; }
        .bg-primary-soft { background: rgba(var(--primary-rgb), 0.1); }
        .bg-success-soft { background: rgba(16, 185, 129, 0.1); }
        .dz-luxury.dropzone { background: var(--bg-color) !important; border: 2px dashed var(--border-color) !important; border-radius: 16px !important; min-height: 120px !important; }
    </style>
    @stack('modals')
@endsection
