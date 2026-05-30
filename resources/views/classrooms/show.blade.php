@extends('layouts.app')

@push('head')
    <style>
        /* Dropzone Global Styles */
        .dropzone {
            border: 2px dashed var(--border) !important;
            background: rgba(var(--primary-rgb, 118, 75, 162), 0.05) !important;
            border-radius: 1rem !important;
            min-height: 120px !important;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem !important;
            transition: all 0.2s ease;
            position: relative;
        }
        .dropzone:hover {
            border-color: var(--primary) !important;
            background: rgba(var(--primary-rgb, 118, 75, 162), 0.1) !important;
        }
        .dropzone .dz-message {
            margin: 0 !important;
        }
        .dropzone .dz-preview {
            margin: 5px !important;
        }
        .dropzone .dz-preview .dz-image {
            width: 80px !important;
            height: 80px !important;
            border-radius: 0.5rem !important;
        }
        /* Banner Dropzone Styles */
        .banner-upload-overlay {
            position: absolute;
            top: 1rem;
            right: 1rem;
            z-index: 10;
        }
        .banner-dz-area {
            background: rgba(255, 255, 255, 0.9);
            border: 2px dashed #cbd5e1;
            padding: 8px 16px;
            border-radius: 50px;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            align-items: center;
            transition: all 0.2s;
            color: #475569;
        }
        .banner-dz-area:hover {
            background: #fff;
            border-color: var(--primary);
            color: var(--primary);
            transform: scale(1.05);
        }
        [data-theme="dark"] .banner-dz-area {
            background: rgba(30, 41, 59, 0.8);
            border-color: #475569;
            color: #f1f5f9;
        }
    </style>
@endpush

@section('content')
    <!-- Classroom Banner -->
    <div class="classroom-banner">
        @if($classroom->banner_path)
            <img src="{{ asset($classroom->banner_path) }}?v={{ time() }}" alt="Class Banner" class="classroom-banner__img">
        @else
            <div class="classroom-banner__placeholder classroom-thumb-gradient">
                <div class="banner-text">
                    <h2 class="m-0 fw-bold">{{ $classroom->name }}</h2>
                    <p class="m-0 opacity-75 small">Level up your learning with Spon++</p>
                </div>
            </div>
        @endif

        @if($userRole === 'teacher')
            <div class="banner-upload-overlay">
                <form action="{{ route('classrooms.banner.update', $classroom) }}" method="POST" enctype="multipart/form-data" id="banner-dropzone-form">
                    @csrf
                    <div id="banner-dropzone" class="banner-dz-area" title="Drag and drop or click to change banner">
                        <i data-lucide="camera" class="mb-1" style="width:24px;height:24px"></i>
                        <span class="smaller fw-bold">Change Banner</span>
                    </div>
                </form>
            </div>

            @if($classroom->banner_path)
                <div class="classroom-banner__actions">
                    <form method="POST" action="{{ route('classrooms.banner.destroy', $classroom) }}" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-banner-delete d-inline-flex align-items-center gap-2" onclick="return confirm('Remove banner?')">
                            <i data-lucide="trash-2" style="width:14px;height:14px"></i> Remove
                        </button>
                    </form>
                </div>
            @endif
        @endif
    </div>

    <!-- Breadcrumb -->
    <nav class="breadcrumb-nav">
        <a href="{{ route('classrooms.index') }}">My Classrooms</a>
        <span class="breadcrumb-sep">/</span>
        <span class="breadcrumb-current">{{ $classroom->name }}</span>
    </nav>

    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="classroom-title fw-bold m-0 text-main">{{ $classroom->name }}</h1>
                @if($userRole === 'teacher')
                    <div class="d-flex align-items-center gap-2 mt-2">
                        <span class="class-code-badge border px-3 py-1 rounded-pill small">Code: <strong>{{ $classroom->join_code }}</strong></span>
                        <a href="{{ route('classrooms.edit', $classroom) }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 d-flex align-items-center gap-2">
                            <i data-lucide="settings" style="width:14px;height:14px"></i> Edit Class
                        </a>
                        <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3 d-flex align-items-center gap-2"
                            onclick="showConfirmModal({
                                title: 'Delete Class',
                                message: 'Are you sure you want to delete this class? All materials, announcements, assignments, and members will be permanently removed. This cannot be undone.',
                                onConfirm: () => document.getElementById('delete-class-form-{{ $classroom->id }}').submit()
                            })">
                            <i data-lucide="trash-2" style="width:14px;height:14px"></i> Delete Class
                        </button>
                        <form id="delete-class-form-{{ $classroom->id }}" action="{{ route('classrooms.destroy', $classroom) }}" method="POST" style="display:none;">
                            @csrf @method('DELETE')
                        </form>
                    </div>
                @endif
            </div>
            <div class="classroom-header-right">
                @if($userRole === 'teacher')
                    <button class="btn btn-primary rounded-pill px-4 d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#newPostModal">
                        <i data-lucide="plus-circle" style="width:18px;height:18px"></i> New Activity
                    </button>
                @else
                    <button type="button" class="btn btn-outline-danger rounded-pill px-4 d-flex align-items-center gap-2" 
                        onclick="showConfirmModal({
                            title: 'Leave Class',
                            message: 'Are you sure you want to leave this class?',
                            onConfirm: () => document.getElementById('leave-class-form').submit()
                        })">
                        <i data-lucide="log-out" style="width:18px;height:18px"></i> Leave Class
                    </button>
                    <form id="leave-class-form" action="{{ route('classrooms.leave', $classroom) }}" method="POST" style="display:none;">
                        @csrf
                    </form>
                @endif
            </div>
        </div>
        <p class="text-muted mt-3 mb-2">{{ $classroom->description }}</p>
        <div class="small text-muted d-flex align-items-center flex-wrap gap-2">
            <span>Created by: <span class="fw-bold text-main">{{ $creator->name ?? 'Unknown' }}</span> • {{ $classroom->created_at->format('d M Y') }}</span>
            <div class="d-flex flex-wrap gap-1 ms-1">
                @foreach($classroom->tags as $tag)
                    <span class="badge rounded-pill px-2 py-1" style="font-size: 0.65rem; background: rgba(var(--primary-rgb, 118, 75, 162), 0.1); color: var(--primary); border: 1px solid rgba(var(--primary-rgb, 118, 75, 162), 0.2);">
                        {{ $tag->name }}
                    </span>
                @endforeach
            </div>
        </div>
    </div>


    <!-- Tabs -->
    <div class="class-tabs mb-4">
        <button class="class-tab active" data-tab="announcements">
            Announcements ({{ $announcements->count() }})
        </button>
        <button class="class-tab" data-tab="members">
            Members ({{ $members->count() }})
        </button>
    </div>

    @php
        $iconMap = [
            'pdf'          => ['icon' => 'file-text',      'color' => '#ef4444'],
            'document'     => ['icon' => 'file-text',      'color' => '#3b82f6'],
            'spreadsheet'  => ['icon' => 'file-spreadsheet','color' => '#22c55e'],
            'presentation' => ['icon' => 'presentation',   'color' => '#9f1239'],
            'image'        => ['icon' => 'image',           'color' => '#8b5cf6'],
            'archive'      => ['icon' => 'archive',         'color' => '#f97316'],
            'link'         => ['icon' => 'link-2',          'color' => '#8b5cf6'],
            'assignment'   => ['icon' => 'clipboard-list', 'color' => '#eab308'],
            'txt'          => ['icon' => 'file-text',       'color' => '#64748b'],
            'drawio'       => ['icon' => 'workflow',        'color' => '#14b8a6'],
            'announcement' => ['icon' => 'megaphone',       'color' => '#8b5cf6'],
        ];
    @endphp

    <div class="tab-content" id="classroomTabsContent">
        <!-- Stream Tab -->
        <div class="tab-pane fade show active shadow-none border-0 p-0" id="tab-announcements" role="tabpanel">
            <div class="stream-feed" id="stream-container">
                @forelse($announcements as $item)
                    @php
                        $now = now();
                        $isLocked = ($item->due_date && $now->gt($item->due_date));
                        $icon = $iconMap[$item->type] ?? $iconMap['document'];
                        $isAssignment = $item->type === 'assignment';
                        $isMaterial = in_array($item->type, ['document', 'pdf', 'spreadsheet', 'presentation', 'image', 'archive', 'txt', 'drawio']);
                    @endphp
                    <div class="card p-0 mb-4 border shadow-sm rounded-4 stream-item" 
                         @if($userRole === 'teacher') draggable="true" @endif
                         data-id="{{ $item->id }}"
                         ondragstart="dragStart(event)"
                         ondragover="dragOver(event)"
                         ondrop="dropItem(event)">
                        
                        <div class="p-4">
                            <div class="d-flex align-items-start gap-3">
                                <div class="activity-icon-wrapper type-{{ $item->type }} rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;flex-shrink:0">
                                    <i data-lucide="{{ $icon['icon'] }}" style="color: {{ $icon['color'] }}; width: 20px; height: 20px;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between">
                                        <h5 class="fw-bold mb-1 text-main">{{ $item->title }}</h5>
                                        @if($userRole === 'teacher')
                                            <div class="d-flex gap-1 justify-content-end">
                                                <button class="btn btn-icon-only btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#editModal{{ $item->id }}" title="Edit">
                                                    <i data-lucide="pencil" style="width:16px;height:16px"></i>
                                                </button>
                                                <button type="button" class="btn btn-icon-only btn-sm rounded-pill text-danger" onclick="showConfirmModal({
                                                    title: 'Delete Item',
                                                    message: 'Are you sure you want to delete this from the stream?',
                                                    onConfirm: () => document.getElementById('delete-item-{{ $item->id }}').submit()
                                                })" title="Delete">
                                                    <i data-lucide="trash-2" style="width:16px;height:16px"></i>
                                                </button>
                                                <form id="delete-item-{{ $item->id }}" action="{{ route('announcements.destroy', $item) }}" method="POST" style="display:none;">
                                                    @csrf @method('DELETE')
                                                </form>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <div class="stream-item-content text-muted small mt-1">
                                        {!! $item->description !!}
                                    </div>

                                    @if($isAssignment && $item->due_date)
                                        <div class="mt-2 text-danger small fw-bold d-flex align-items-center gap-1">
                                            <i data-lucide="calendar" style="width:12px;height:12px"></i> Due: {{ $item->due_date->format('M d, Y h:i A') }}
                                        </div>
                                    @endif

                                    <div class="mt-3 d-flex justify-content-between align-items-center">
                                        <div class="text-secondary smaller d-flex align-items-center gap-1">
                                            <i data-lucide="user" style="width:12px;height:12px"></i>
                                            Posted by: {{ $item->author->name }} &bull; {{ $item->created_at->format('M d, Y') }}
                                        </div>

                                        <div class="d-flex gap-2 justify-content-end">
                                            @if($isAssignment)
                                                @if($userRole === 'teacher')
                                                    <button class="btn btn-light btn-sm rounded-pill px-3 d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#viewSubmissionsModal{{ $item->id }}">
                                                        <i data-lucide="layers" style="width:14px;height:14px"></i> {{ $item->submissions->count() }} submitted
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 d-flex align-items-center gap-2" 
                                                            onclick="openSubmitModal({{ $item->id }}, '{{ addslashes($item->title) }}')">
                                                        @if(isset($memberSubmissions[$item->id]))
                                                            <i data-lucide="check-circle" style="width:14px;height:14px"></i> Resubmit
                                                        @else
                                                            <i data-lucide="upload" style="width:14px;height:14px"></i> Submit Work
                                                        @endif
                                                    </button>
                                                @endif
                                            @elseif($item->file_path || $isMaterial)
                                                @if($item->file_path)
                                                <a href="{{ route('announcements.download', $item) }}" class="btn btn-light btn-sm rounded-pill px-3 d-flex align-items-center gap-1">
                                                    <i data-lucide="download" style="width:14px;height:14px"></i> Download
                                                </a>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="p-4 bg-light-subtle rounded-bottom-4 border-top comment-section">
                            <!-- Class Comments -->
                            <div class="comments-section">
                                <h6 class="fw-bold mb-3 d-flex align-items-center gap-2 small text-main">
                                    <i data-lucide="message-square" style="width:14px;height:14px"></i> Class Comments
                                </h6>
                                
                                <div class="comments-list">
                                    @foreach($item->comments as $comment)
                                        <div class="comment-item mb-3">
                                            <div class="d-flex gap-2">
                                                <div class="flex-shrink-0">
                                                    <div class="bg-primary-subtle rounded-circle d-flex align-items-center justify-content-center" style="width:30px;height:30px">
                                                        <i data-lucide="user" class="text-primary" style="width:14px;height:14px"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <div class="d-flex justify-content-between align-items-start">
                                                        <div>
                                                            <span class="fw-bold text-main small">{{ $comment->user->name }}</span>
                                                            <span class="text-muted smaller ms-2">{{ $comment->created_at->diffForHumans() }}</span>
                                                        </div>
                                                        @if($comment->user_id === auth()->id())
                                                            <div class="dropdown">
                                                                <button class="btn btn-link text-muted p-0" data-bs-toggle="dropdown">
                                                                    <i data-lucide="more-vertical" style="width:14px;height:14px"></i>
                                                                </button>
                                                                <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm rounded-3">
                                                                    <li><button class="dropdown-item small" onclick="editComment({{ $comment->id }}, '{{ addslashes($comment->body) }}')"><i data-lucide="pencil" style="width:12px;height:12px" class="me-2"></i> Edit</button></li>
                                                                    <li><button class="dropdown-item small text-danger" onclick="showConfirmModal({
                                                                        title: 'Delete Comment',
                                                                        message: 'Delete this comment?',
                                                                        onConfirm: () => document.getElementById('delete-comment-{{ $comment->id }}').submit()
                                                                    })"><i data-lucide="trash-2" style="width:12px;height:12px" class="me-2"></i> Delete</button></li>
                                                                </ul>
                                                                <form id="delete-comment-{{ $comment->id }}" action="{{ route('comments.destroy', $comment) }}" method="POST" style="display:none;">@csrf @method('DELETE')</form>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="comment-body small text-main mt-1" id="comment-text-{{ $comment->id }}">
                                                        {!! $comment->body !!}
                                                    </div>
                                                    
                                                    <button class="btn btn-link btn-sm text-primary p-0 mt-1 smaller d-flex align-items-center gap-1" onclick="toggleReplyForm({{ $comment->id }})">
                                                        <i data-lucide="reply" style="width:12px;height:12px"></i> Reply
                                                    </button>

                                                    <div class="replies-list mt-2 ms-4 border-start ps-2">
                                                        @foreach($comment->replies as $reply)
                                                            <div class="reply-item mb-2">
                                                                <div class="d-flex justify-content-between">
                                                                    <span class="fw-bold text-main smaller">{{ $reply->user->name }}</span>
                                                                    <span class="text-muted smaller">{{ $reply->created_at->diffForHumans() }}</span>
                                                                </div>
                                                                <div class="reply-body smaller text-main mt-1" id="comment-text-{{ $reply->id }}">
                                                                    {!! $reply->body !!}
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                        
                                                        <div class="reply-form mt-2 d-none" id="reply-form-{{ $comment->id }}">
                                                            <form action="{{ route('announcements.comments.store', $item) }}" method="POST" class="comment-form">
                                                                 @csrf
                                                                 <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                                                 <div class="comment-input-row">
                                                                     <input type="text" name="body" placeholder="Write a reply..." class="comment-input" autocomplete="off" required>
                                                                     <button type="submit" class="comment-send-btn">
                                                                         <i data-lucide="send" style="width:14px;height:14px"></i>
                                                                     </button>
                                                                 </div>
                                                             </form>
                                                         </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="mt-3">
                                    <form action="{{ route('announcements.comments.store', $item) }}" method="POST" class="comment-form">
                                        @csrf
                                        <div class="comment-input-row">
                                            <input type="text" name="body" placeholder="Add a class comment..." class="comment-input" autocomplete="off" required>
                                            <button type="submit" class="comment-send-btn">
                                                <i data-lucide="send" style="width:14px;height:14px"></i>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5">
                        <i data-lucide="megaphone" class="text-muted mb-3" style="width:48px;height:48px;opacity:0.3"></i>
                        <p class="text-muted">Stream is empty. Post something to get started!</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Members Tab -->
        <div class="tab-pane fade" id="tab-members" role="tabpanel" style="display:none;">
            <div class="card p-4 border-0 shadow-sm rounded-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr class="text-muted smaller fw-bold border-bottom">
                                <th class="pb-3 px-3">NAME</th>
                                <th class="pb-3">ROLE</th>
                                <th class="pb-3 text-end px-3">@if($userRole === 'teacher') ACTIONS @endif</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($members as $member)
                                <tr>
                                    <td class="py-3 px-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="bg-primary-subtle rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px">
                                                <i data-lucide="user" class="text-primary"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-main">{{ $member->name }}</div>
                                                <div class="smaller text-muted">{{ $member->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge {{ $member->pivot->role === 'teacher' ? 'bg-success' : 'bg-primary' }} rounded-pill px-3">
                                            {{ ucfirst($member->pivot->role) }}
                                        </span>
                                    </td>
                                    <td class="text-end px-3">
                                        @if($userRole === 'teacher' && $member->id !== auth()->id())
                                            <div class="d-flex justify-content-end gap-2">
                                                <button class="btn btn-icon-only btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#editMemberModal{{ $member->id }}" title="Edit Info">
                                                    <i data-lucide="pencil" style="width:16px;height:16px"></i>
                                                </button>
                                                <button type="button" class="btn btn-icon-only btn-sm rounded-pill text-danger"
                                                    onclick="showConfirmModal({
                                                        title: 'Kick Member',
                                                        message: 'Remove {{ $member->name }} from class?',
                                                        onConfirm: () => document.getElementById('kick-form-{{ $member->id }}').submit()
                                                    })" title="Kick">
                                                    <i data-lucide="user-x" style="width:16px;height:16px"></i>
                                                </button>
                                                <form id="kick-form-{{ $member->id }}" action="{{ route('classrooms.kick', [$classroom, $member]) }}" method="POST" style="display:none;">
                                                    @csrf @method('DELETE')
                                                </form>
                                            </div>

                                            <div class="modal fade" id="editMemberModal{{ $member->id }}" tabindex="-1">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content border-0 rounded-4 shadow">
                                                        <form action="{{ route('classrooms.updateMember', [$classroom, $member]) }}" method="POST">
                                                            @csrf @method('PUT')
                                                            <div class="modal-header border-0 pb-0">
                                                                <h5 class="modal-title fw-bold">Member Notes</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <textarea name="notes" class="form-control rounded-3" rows="3" placeholder="Private notes...">{{ $member->pivot->notes }}</textarea>
                                                            </div>
                                                            <div class="modal-footer border-0 justify-content-end">
                                                                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                                                                <button type="submit" class="btn btn-primary rounded-pill px-4">Save Changes</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @if($userRole === 'teacher')
        <!-- Unified New Post Modal -->
        <div class="modal fade" id="newPostModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 rounded-4 shadow">
                    <form id="new-activity-form" action="{{ route('announcements.store', $classroom) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="type" id="post_type_input" value="announcement">
                        <div class="modal-header border-0 pb-0">
                            <h5 class="modal-title fw-bold" id="post_modal_title">Create Announcement</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <!-- Type Tabs (Inside Modal) -->
                            <div class="d-flex gap-2 mb-4 p-1 bg-sidebar-hover-responsive rounded-pill" style="width: fit-content; margin: 0 auto;">
                                <button type="button" class="btn btn-sm rounded-pill px-4 type-tab active" onclick="setPostType('announcement', this)">Announcement</button>
                                <button type="button" class="btn btn-sm rounded-pill px-4 type-tab" onclick="setPostType('material', this)">Material</button>
                                <button type="button" class="btn btn-sm rounded-pill px-4 type-tab" onclick="setPostType('assignment', this)">Assignment</button>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted smaller fw-bold">TITLE</label>
                                <input type="text" name="title" class="form-control rounded-3" placeholder="Enter title" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted smaller fw-bold">DESCRIPTION</label>
                                <textarea id="description-editor" class="form-control rounded-3"></textarea>
                                <input type="hidden" name="description" id="description-hidden">
                            </div>
                            
                            <div id="date_fields" class="row d-none">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted smaller fw-bold">DUE DATE</label>
                                    <input type="datetime-local" name="due_date" id="due_date" class="form-control rounded-3">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted smaller fw-bold">OPEN DATE (OPTIONAL)</label>
                                    <input type="datetime-local" name="open_date" id="open_date" class="form-control rounded-3">
                                </div>
                            </div>

                            <div id="file_field" class="mb-3 d-none">
                                <label class="form-label text-muted smaller fw-bold">ATTACHMENT (OPTIONAL)</label>
                                <div id="activity-dropzone" class="dropzone">
                                    <div class="dz-message d-flex flex-column align-items-center">
                                        <i data-lucide="upload" class="text-primary mb-2" style="width:32px;height:32px"></i>
                                        <span class="fw-bold small text-main">Choose a file to upload</span>
                                        <span class="text-muted smaller">Max 20MB (PDF, DOCX, Images, etc.)</span>
                                    </div>
                                </div>
                                <input type="file" name="file" id="file-input-activity" style="display:none">
                            </div>
                        </div>
                        <div class="modal-footer border-0 justify-content-end">
                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary rounded-pill px-4">Post Now</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Modals -->
            @foreach($announcements as $item)
                <div class="modal fade" id="editModal{{ $item->id }}" tabindex="-1">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content border-0 rounded-4 shadow">
                            <form action="{{ route('announcements.update', $item) }}" method="POST" enctype="multipart/form-data">
                                @csrf @method('PUT')
                                <div class="modal-header border-0 pb-0">
                                    <h5 class="modal-title fw-bold">Edit Stream Item</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label text-muted smaller fw-bold">TITLE</label>
                                        <input type="text" name="title" class="form-control rounded-3" value="{{ $item->title }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-muted smaller fw-bold">DESCRIPTION</label>
                                        <textarea name="description" class="form-control rounded-3" rows="5">{{ $item->description }}</textarea>
                                    </div>
                                    @if($item->type === 'assignment')
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label text-muted smaller fw-bold">DUE DATE</label>
                                                <input type="datetime-local" name="due_date" class="form-control rounded-3" value="{{ $item->due_date ? $item->due_date->format('Y-m-d\TH:i') : '' }}">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label text-muted smaller fw-bold">OPEN DATE</label>
                                                <input type="datetime-local" name="open_date" class="form-control rounded-3" value="{{ $item->open_date ? $item->open_date->format('Y-m-d\TH:i') : '' }}">
                                            </div>
                                        </div>
                                    @endif


                                    <div class="mb-3">
                                        <label class="form-label text-muted smaller fw-bold">REPLACE ATTACHMENT (OPTIONAL)</label>
                                        <div id="edit-dropzone-{{ $item->id }}" class="dropzone">
                                            <div class="dz-message d-flex flex-column align-items-center">
                                                <i data-lucide="upload-cloud" class="text-primary mb-2" style="width:32px;height:32px"></i>
                                                <span class="fw-bold small text-main">Drop file here or click to upload</span>
                                                <span class="text-muted smaller">Max 20MB</span>
                                            </div>
                                        </div>
                                        <input type="file" name="file" id="file-input-edit-{{ $item->id }}" style="display:none">
                                    </div>
                                </div>
                                <div class="modal-footer border-0 justify-content-end">
                                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary rounded-pill px-4">Update</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            @if($item->type === 'assignment')
                <div class="modal fade" id="viewSubmissionsModal{{ $item->id }}" tabindex="-1">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content border-0 rounded-4 shadow">
                            <div class="modal-header border-0 pb-0">
                                <h5 class="modal-title fw-bold">Submissions: {{ $item->title }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <table class="table table-hover align-middle mb-0">
                                    <thead>
                                        <tr class="smaller fw-bold text-muted border-bottom">
                                            <th class="pb-3 px-3">MEMBER</th>
                                            <th class="pb-3">SUBMITTED</th>
                                            <th class="pb-3 text-end px-3">FILE</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($item->submissions as $sub)
                                            <tr>
                                                <td class="py-3 px-3">
                                                    <div class="fw-bold text-main">{{ $sub->user->name }}</div>
                                                    <div class="smaller text-muted">{{ $sub->user->email }}</div>
                                                </td>
                                                <td class="smaller">{{ $sub->submitted_at->format('M d, h:i A') }}</td>
                                                <td class="text-end px-3">
                                                    <a href="{{ route('submissions.download', $sub) }}" class="btn btn-light btn-sm rounded-pill px-3">
                                                        <i data-lucide="download" style="width:14px;height:14px"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center py-4 text-muted">No submissions yet.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    @endif

    <!-- Member: Submit Assignment Modal -->
    <div id="submit-modal" class="modal fade" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow">
                <form id="submit-form" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold" id="submit-modal-title">Submit Assignment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label text-muted smaller fw-bold">YOUR SUBMISSION</label>
                            <div id="submission-dropzone" class="dropzone">
                                <div class="dz-message d-flex flex-column align-items-center">
                                    <i data-lucide="file-up" class="text-primary mb-2" style="width:32px;height:32px"></i>
                                    <span class="fw-bold small text-main">Drop work file here or click to upload</span>
                                    <span class="text-muted smaller">Max 20MB (PDF, DOCX, ZIP, etc.)</span>
                                </div>
                            </div>
                            <input type="file" name="file" id="file-input-submission" style="display:none">
                        </div>
                    </div>
                    <div class="modal-footer border-0 justify-content-end">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">Submit Assignment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .text-main { color: var(--text); }
            [data-theme="dark"] .text-main { color: #f8fafc; }

            .bg-sidebar-hover-responsive { background-color: rgba(0,0,0,0.05); }
            [data-theme="dark"] .bg-sidebar-hover-responsive { background-color: rgba(255,255,255,0.05); }
            [data-theme="dark"] .bg-light-subtle { background-color: rgba(255,255,255,0.03) !important; }
            
            .breadcrumb-nav { font-size: 0.8rem; display: flex; gap: 8px; margin-bottom: 8px; }
            .breadcrumb-nav a { 
                color: var(--primary); 
                text-decoration: none; 
                font-weight: 500; 
            }
            .breadcrumb-nav a:hover { 
                text-decoration: underline; 
                color: var(--primary); 
            }
            .breadcrumb-sep { color: var(--text-muted); }
            .breadcrumb-current { color: var(--text); font-weight: 600; }
            [data-theme="dark"] .breadcrumb-nav a { color: #a78bfa; }
            [data-theme="dark"] .breadcrumb-nav a:hover { color: #c4b5fd; }

            .classroom-banner {
                height: 240px;
                background: var(--card);
                border-radius: 20px;
                overflow: hidden;
                position: relative;
                margin-bottom: 24px;
                border: 1px solid var(--border);
            }
            .classroom-banner__img { width: 100%; height: 100%; object-fit: cover; }
            .classroom-banner__placeholder { 
                width: 100%; height: 100%; 
                display: flex; align-items: flex-end; padding: 40px;
            }
            .classroom-thumb-gradient {
                background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
            }
            .banner-text { color: white; text-shadow: 0 2px 4px rgba(0,0,0,0.2); }
            
            .classroom-banner__actions {
                position: absolute; bottom: 20px; right: 20px;
                display: flex; gap: 10px;
            }
            .btn-banner-upload, .btn-banner-delete {
                background: rgba(0,0,0,0.6); color: white; border: none;
                padding: 8px 16px; border-radius: 8px; font-size: 0.85rem;
                font-weight: 600; backdrop-filter: blur(4px); transition: 0.2s;
                text-decoration: none;
            }
            .btn-banner-upload:hover, .btn-banner-delete:hover { background: rgba(0,0,0,0.8); transform: translateY(-1px); color: white; }

            .stream-item { transition: transform 0.2s, box-shadow 0.2s; position: relative; }
            .stream-item.dragging { opacity: 0.5; }
            .stream-item.drag-over { border: 2px dashed var(--primary) !important; }

            .btn-icon-only { width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; background: transparent; border: none; transition: 0.2s; }
            .btn-icon-only:hover { background: var(--border); }
            
            .nav-pills .nav-link.active { background-color: var(--primary); color: white !important; }
            .nav-pills .nav-link { color: var(--text-muted); font-weight: 600; }

            /* Dark Mode Fixes */
            [data-theme="dark"] .class-code-badge {
                border-color: #475569;
                color: #f1f5f9;
                background: rgba(255,255,255,0.05);
            }
            [data-theme="light"] .class-code-badge {
                border-color: #cbd5e1;
                color: #1e293b;
                background: rgba(0,0,0,0.03);
            }
            .class-code-badge {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                background: transparent;
                border: 1px solid var(--border);
                border-radius: 8px;
                padding: 4px 12px;
                font-family: monospace;
                font-size: 0.85rem;
                font-weight: 600;
                color: var(--text);
            }

            [data-theme="dark"] .comment-box-wrapper {
                background-color: var(--bg) !important;
                border-color: var(--border) !important;
            }

            [data-theme="dark"] .btn-icon-only {
                color: #94a3b8;
            }
            [data-theme="dark"] .btn-icon-only:hover {
                color: var(--text);
                background-color: rgba(255,255,255,0.08);
            }

            [data-theme="dark"] .bg-light-subtle {
                background-color: var(--card) !important;
            }

            [data-theme="dark"] input[type="datetime-local"] {
                background-color: var(--card);
                color: var(--text);
                border: 1px solid var(--border);
                color-scheme: dark;
            }

            /* Choose-file Styles */
            .choose-file-area { position: relative; }
            .choose-file-trigger { 
                cursor: pointer; 
                transition: transform 0.1s, border-color 0.2s; 
                border-width: 2px !important;
                border: 2px dashed var(--border);
                border-radius: 12px;
            }
            .choose-file-trigger:hover { border-color: var(--primary) !important; transform: translateY(-1px); }
            .choose-file-trigger:active { transform: translateY(0); }
            .pointer { cursor: pointer; }
            .bg-primary-subtle { background-color: rgba(124, 58, 237, 0.12) !important; }
            [data-theme="dark"] .bg-primary-subtle { background-color: rgba(124, 58, 237, 0.2) !important; }
            .border-primary-subtle { border: 1px solid rgba(124, 58, 237, 0.3) !important; }
            .choose-file-trigger.drag-over { 
                border-color: var(--primary) !important; 
                background-color: rgba(124, 58, 237, 0.05) !important; 
            }
            [data-theme="dark"] .comment-section {
                background-color: var(--card);
                border-top: 1px solid var(--border);
            }
            [data-theme="dark"] .comment-item .fw-bold { color: var(--text); }
            [data-theme="dark"] .text-muted.smaller { color: #64748b !important; }
            [data-theme="dark"] .comment-body { color: var(--text); }
            [data-theme="dark"] .reply-btn, [data-theme="dark"] .btn-link { color: #818cf8 !important; }

            [data-theme="dark"] .form-label.text-muted,
            [data-theme="dark"] .form-label.smaller.text-muted {
                color: #cbd5e1 !important;
                opacity: 0.9;
            }
            [data-theme="dark"] .bg-light {
                background-color: var(--sidebar-hover) !important;
                color: var(--text);
            }
            [data-theme="dark"] .type-tab:not(.active) {
                color: var(--text-secondary);
            }
            [data-theme="dark"] .type-tab:not(.active):hover {
                color: var(--text);
            }
            [data-theme="dark"] .nav-pills .nav-link:not(.active) {
                color: #94a3b8;
            }
            [data-theme="dark"] .nav-pills .nav-link:not(.active):hover {
                color: var(--text);
            }

            .visibility-member-list {
                max-height: 160px;
                overflow-y: auto;
                border: 1px solid var(--border);
                border-radius: 6px;
                padding: 8px;
                background-color: var(--card);
                width: 100%;
                max-width: 360px;
            }

            .visibility-member-list label {
                display: flex;
                align-items: center;
                gap: 8px;
                padding: 6px 4px;
                cursor: pointer;
                color: var(--text);
                font-size: 14px;
                border-radius: 4px;
            }

            .visibility-member-list label:hover {
                background-color: rgba(124, 58, 237, 0.08);
            }

            [data-theme="dark"] .visibility-member-list label { color: var(--text); }
            [data-theme="dark"] .visibility-member-list { border-color: var(--border); background-color: var(--card); }
            
            .activity-icon-wrapper.type-pdf          { background: rgba(239,68,68,0.12); }
            .activity-icon-wrapper.type-spreadsheet  { background: rgba(34,197,94,0.12); }
            .activity-icon-wrapper.type-presentation { background: rgba(159,18,57,0.12); }
            .activity-icon-wrapper.type-image        { background: rgba(139,92,246,0.12); }
            .activity-icon-wrapper.type-archive      { background: rgba(249,115,22,0.12); }
            .activity-icon-wrapper.type-link         { background: rgba(139,92,246,0.12); }
            .activity-icon-wrapper.type-txt          { background: rgba(100,116,139,0.12); }
            .activity-icon-wrapper.type-drawio       { background: rgba(20,184,166,0.12); }
            
            [data-theme="dark"] .activity-icon-wrapper.type-pdf          { background: rgba(239,68,68,0.2); }
            [data-theme="dark"] .activity-icon-wrapper.type-spreadsheet  { background: rgba(34,197,94,0.2); }
            [data-theme="dark"] .activity-icon-wrapper.type-presentation { background: rgba(159,18,57,0.2); }
            [data-theme="dark"] .activity-icon-wrapper.type-image        { background: rgba(139,92,246,0.2); }
            [data-theme="dark"] .activity-icon-wrapper.type-archive      { background: rgba(249,115,22,0.2); }
            [data-theme="dark"] .activity-icon-wrapper.type-link         { background: rgba(139,92,246,0.2); }
            [data-theme="dark"] .activity-icon-wrapper.type-txt          { background: rgba(100,116,139,0.2); }
            [data-theme="dark"] .activity-icon-wrapper.type-drawio       { background: rgba(20,184,166,0.2); }

            /* Comment Styles */
            .comment-input-row { display: flex; gap: 8px; align-items: center; }
            .comment-input { flex: 1; background: var(--card); color: var(--text); border: 1px solid var(--border); border-radius: 8px; padding: 10px 14px; font-size: 0.875rem; transition: border-color 0.2s; }
            .comment-input::placeholder { color: var(--text-muted); }
            .comment-input:focus { outline: none; border-color: var(--primary); }
            .comment-send-btn { background: var(--primary); color: white; border: none; border-radius: 8px; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: opacity 0.2s; }
            .comment-send-btn:hover { opacity: 0.85; }

            /* Tab base */
            .class-tab {
                padding: 8px 20px;
                border-radius: 999px;
                font-size: 14px;
                font-weight: 500;
                cursor: pointer;
                border: none;
                background: none;
                color: #94a3b8;
                transition: background-color 0.2s, color 0.2s;
            }

            /* Active tab — Spon++ purple */
            .class-tab.active {
                background-color: #7c3aed;
                color: #ffffff !important;
            }

            /* Hover on inactive */
            .class-tab:hover:not(.active) {
                background-color: rgba(124, 58, 237, 0.08);
                color: #7c3aed;
            }

            /* Dark mode */
            [data-theme="dark"] .class-tab {
                color: #64748b;
            }
            [data-theme="dark"] .class-tab.active {
                background-color: #7c3aed;
                color: #ffffff !important;
            }
            [data-theme="dark"] .class-tab:hover:not(.active) {
                background-color: rgba(124, 58, 237, 0.15);
                color: #a78bfa;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            document.querySelectorAll('.class-tab').forEach(tab => {
                tab.addEventListener('click', function () {
                    // Remove active from all tabs
                    document.querySelectorAll('.class-tab')
                    .forEach(t => t.classList.remove('active'));

                    // Add active to clicked tab
                    this.classList.add('active');

                    // Show/hide tab content
                    const target = this.dataset.tab;
                    document.querySelectorAll('.tab-pane')
                    .forEach(c => {
                        c.style.display = 'none';
                        c.classList.remove('show', 'active');
                    });
                    const activeTab = document.getElementById(`tab-${target}`);
                    activeTab.style.display = 'block';
                    activeTab.classList.add('show', 'active');
                    
                    // Trigger lucide icons refresh if needed
                    if (window.lucide) {
                        window.lucide.createIcons();
                    }
                });
            });
            // Post Type Handling
            function setPostType(type, btn) {
                const input = document.getElementById('post_type_input');
                const title = document.getElementById('post_modal_title');
                const dateFields = document.getElementById('date_fields');
                const fileField = document.getElementById('file_field');

                input.value = type;
                title.textContent = 'Create ' + type.charAt(0).toUpperCase() + type.slice(1);
                
                dateFields.classList.toggle('d-none', type !== 'assignment');
                fileField.classList.toggle('d-none', type === 'announcement');

                // Toggle tab active state
                document.querySelectorAll('.type-tab').forEach(b => b.classList.remove('active', 'btn-primary'));
                document.querySelectorAll('.type-tab').forEach(b => b.classList.add('btn-light'));
                if (btn) {
                    btn.classList.add('active', 'btn-primary');
                    btn.classList.remove('btn-light');
                }
            }

            // Drag and Drop
            let draggedEl = null;
            function dragStart(e) {
                draggedEl = e.currentTarget;
                e.currentTarget.classList.add('dragging');
            }
            function dragOver(e) {
                e.preventDefault();
                const target = e.currentTarget;
                if (target !== draggedEl && target.classList.contains('stream-item')) {
                    target.classList.add('drag-over');
                }
            }
            function dropItem(e) {
                e.preventDefault();
                const target = e.currentTarget;
                target.classList.remove('drag-over');
                draggedEl.classList.remove('dragging');

                if (target !== draggedEl && target.classList.contains('stream-item')) {
                    const parent = target.parentNode;
                    const items = [...parent.querySelectorAll('.stream-item')];
                    const dragIdx = items.indexOf(draggedEl);
                    const dropIdx = items.indexOf(target);

                    if (dragIdx < dropIdx) {
                        parent.insertBefore(draggedEl, target.nextSibling);
                    } else {
                        parent.insertBefore(draggedEl, target);
                    }

                    // Save new order to backend
                    const newOrder = [...parent.querySelectorAll('.stream-item')]
                        .map((el, index) => ({ id: el.dataset.id, order: index }));

                    fetch('{{ route('announcements.reorder') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ order: newOrder })
                    });
                }
            }

            // Other UI Helpers
            function toggleReplyForm(id) {
                const form = document.getElementById('reply-form-' + id);
                form.classList.toggle('d-none');
            }
            function openSubmitModal(id, title) {
                document.getElementById('submit-modal-title').textContent = 'Submit: ' + title;
                document.getElementById('submit-form').action = `/announcements/${id}/submit`;
                new bootstrap.Modal(document.getElementById('submit-modal')).show();
            }
            function editComment(id, bodyHtml) {
                const bodyDiv = document.getElementById('comment-text-' + id);
                const currentHtml = bodyDiv.innerHTML;
                
                bodyDiv.innerHTML = `
                    <form class="comment-edit-form mt-2" action="/comments/${id}" method="POST">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="_method" value="PUT">
                        <input type="hidden" name="body" class="comment-body-input">
                        
                        <div class="comment-box-wrapper rounded-3 border p-2 bg-white">
                            <div class="editor" contenteditable="true" style="min-height: 40px; outline: none; font-size: 0.85rem;">${currentHtml}</div>
                        </div>
                        <div class="d-flex justify-content-end gap-2 mt-2">
                            <button type="button" class="btn btn-light btn-sm rounded-pill px-3" onclick="location.reload()">Cancel</button>
                            <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3">Save</button>
                        </div>
                    </form>
                `;
                const form = bodyDiv.querySelector('form');
                form.addEventListener('submit', (e) => {
                    const editor = form.querySelector('.editor');
                    const hidden = form.querySelector('.comment-body-input');
                    if (editor && hidden) hidden.value = editor.innerHTML;
                });
            }

            function setPostType(type, btn) {
                const hiddenInput = document.getElementById('post_type_input');
                const titleLabel = document.getElementById('post_modal_title');
                if (hiddenInput) hiddenInput.value = type;
                if (titleLabel) titleLabel.textContent = 'Create ' + type.charAt(0).toUpperCase() + type.slice(1);
                
                document.querySelectorAll('.type-tab').forEach(t => t.classList.remove('active'));
                if (btn) btn.classList.add('active');
                
                const dateFields = document.getElementById('date_fields');
                const fileFields = document.getElementById('file_field');
                
                if (dateFields && fileFields) {
                    if (type === 'announcement') {
                        dateFields.classList.add('d-none');
                        fileFields.classList.add('d-none');
                    } else if (type === 'material') {
                        dateFields.classList.add('d-none');
                        fileFields.classList.remove('d-none');
                    } else if (type === 'assignment') {
                        dateFields.classList.remove('d-none');
                        fileFields.classList.remove('d-none');
                    }
                }
            }

            // TinyMCE Initialization
            function initTinyMCE(selector) {
                const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
                tinymce.init({
                    selector: selector,
                    height: 300,
                    menubar: false,
                    plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table code help wordcount',
                    toolbar: 'undo redo | blocks | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
                    skin: isDark ? 'oxide-dark' : 'oxide',
                    content_css: isDark ? 'dark' : 'default',
                    setup: function (editor) {
                        editor.on('change', function () {
                            editor.save(); // Sync to textarea
                        });
                    }
                });
            }

            // Dropzone Global Config
            Dropzone.autoDiscover = false;

            function initCustomDropzone(config) {
                const dz = new Dropzone(config.selector, {
                    url: "#", // Manual handling
                    maxFiles: 1,
                    autoProcessQueue: false,
                    acceptedFiles: config.acceptedFiles || null,
                    addRemoveLinks: true,
                    dictDefaultMessage: config.message || "Drop file here",
                    init: function() {
                        this.on("addedfile", function(file) {
                            if (this.files.length > 1) {
                                this.removeFile(this.files[0]);
                            }
                            // Trigger binary transfer to hidden input for regular form submit
                            const dataTransfer = new DataTransfer();
                            dataTransfer.items.add(file);
                            document.querySelector(config.inputSelector).files = dataTransfer.files;
                        });
                        this.on("removedfile", function() {
                            document.querySelector(config.inputSelector).value = "";
                        });
                    }
                });
                return dz;
            }

            function setMinDateTime() {
                const now = new Date();
                const pad = n => String(n).padStart(2, '0');
                const minVal = `${now.getFullYear()}-${pad(now.getMonth() + 1)}-${pad(now.getDate())}T${pad(now.getHours())}:${pad(now.getMinutes())}`;
                if (document.getElementById('open_date')) document.getElementById('open_date').min = minVal;
                if (document.getElementById('due_date')) document.getElementById('due_date').min = minVal;
            }

            function showFieldError(fieldId, message) {
                const field = document.getElementById(fieldId);
                if (!field) return;
                let err = field.nextElementSibling;
                if (!err || !err.classList.contains('field-error')) {
                    err = document.createElement('p');
                    err.classList.add('field-error', 'text-danger', 'smaller', 'mt-1');
                    field.parentNode.insertBefore(err, field.nextSibling);
                }
                err.textContent = message;
            }

            document.addEventListener('DOMContentLoaded', () => {
                lucide.createIcons();
                initTinyMCE('#description-editor');
                setMinDateTime();
                
                // Initialize Dropzones
                @if($userRole === 'teacher')
                    initCustomDropzone({
                        selector: "#activity-dropzone",
                        inputSelector: "#file-input-activity",
                        message: "Drop attachment here"
                    });

                    // Banner Dropzone
                    const bannerDz = new Dropzone("#banner-dropzone", {
                        url: "{{ route('classrooms.banner.update', $classroom) }}",
                        paramName: "banner",
                        headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                        maxFiles: 1,
                        acceptedFiles: "image/*",
                        init: function() {
                            this.on("success", function() { location.reload(); });
                            this.on("error", function(file, res) { 
                                alert(typeof res === 'string' ? res : (res.message || "Upload failed"));
                                this.removeFile(file);
                            });
                        }
                    });

                    // Edit Modals Dropzones
                    @foreach($announcements as $item)
                        initCustomDropzone({
                            selector: "#edit-dropzone-{{ $item->id }}",
                            inputSelector: "#file-input-edit-{{ $item->id }}",
                            message: "Drop new file here"
                        });
                    @endforeach
                @else
                    if (document.getElementById('submission-dropzone')) {
                        initCustomDropzone({
                            selector: "#submission-dropzone",
                            inputSelector: "#file-input-submission",
                            message: "Drop your work here"
                        });
                    }
                @endif

                const activityForm = document.getElementById('new-activity-form');
                if (activityForm) {
                    activityForm.addEventListener('submit', function(e) {
                        const openDateEl = document.getElementById('open_date');
                        const dueDateEl = document.getElementById('due_date');
                        if (openDateEl && dueDateEl && openDateEl.value && dueDateEl.value) {
                            if (new Date(dueDateEl.value) <= new Date(openDateEl.value)) {
                                e.preventDefault();
                                showFieldError('due_date', 'Due date must be after open date.');
                            }
                        }
                    });
                }

                // If you need icon refresh after dynamic content, call it manually in the success handlers
            });
        </script>
    @endpush
@endsection
