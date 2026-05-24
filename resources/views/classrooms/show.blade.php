@extends('layouts.app')

@section('content')
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb small">
                <li class="breadcrumb-item"><a href="{{ route('classrooms.index') }}" class="text-decoration-none">My Classrooms</a></li>
                <li class="breadcrumb-item active">{{ $classroom->name }}</li>
            </ol>
        </nav>
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="fw-bold m-0">{{ $classroom->name }}</h2>
            @if($userRole === 'teacher')
                <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#uploadMaterialModal">Upload Material</button>
            @endif
        </div>
        <p class="text-muted mt-2">{{ $classroom->description }}</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 rounded-4 mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="card p-4">
        <h5 class="fw-bold mb-4">Materials</h3>
        
        @forelse($materialList as $material)
            <div class="border-bottom pb-4 mb-4 last-child-no-border">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h6 class="fw-bold m-0 text-primary">{{ $material->title }}</h6>
                    @if($userRole === 'teacher')
                        <div class="dropdown">
                            <button class="btn btn-light btn-sm rounded-circle" data-bs-toggle="dropdown">
                                ⋮
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">
                                <li>
                                    <button class="dropdown-item small" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editMaterialModal{{ $material->id }}">Edit</button>
                                </li>
                                <li>
                                    <form action="{{ route('materials.destroy', $material) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item small text-danger" onclick="return confirm('Delete this material?')">Delete</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @endif
                </div>
                <div class="text-muted small mb-2">Uploaded by {{ $material->uploader->name }} • {{ $material->created_at->diffForHumans() }}</div>
                <div class="bg-light p-3 rounded-3" style="white-space: pre-line;">{{ $material->content }}</div>
            </div>

            @if($userRole === 'teacher')
                <!-- Edit Material Modal -->
                <div class="modal fade" id="editMaterialModal{{ $material->id }}" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 rounded-4 shadow">
                            <form action="{{ route('materials.update', $material) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-header border-0 pb-0">
                                    <h5 class="modal-title fw-bold">Edit Material</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label text-muted small fw-bold">Title</label>
                                        <input type="text" name="title" class="form-control rounded-3" value="{{ $material->title }}" required>
                                    </div>
                                    <div class="mb-0">
                                        <label class="form-label text-muted small fw-bold">Content</label>
                                        <textarea name="content" class="form-control rounded-3" rows="5" required>{{ $material->content }}</textarea>
                                    </div>
                                </div>
                                <div class="modal-footer border-0">
                                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary rounded-pill px-4">Update Material</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        @empty
            <div class="text-center py-4">
                <p class="text-muted m-0">No materials uploaded yet.</p>
            </div>
        @endforelse
    </div>

    @if($userRole === 'teacher')
        <!-- Upload Material Modal -->
        <div class="modal fade" id="uploadMaterialModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 rounded-4 shadow">
                    <form action="{{ route('materials.store', $classroom) }}" method="POST">
                        @csrf
                        <div class="modal-header border-0 pb-0">
                            <h5 class="modal-title fw-bold">Upload New Material</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label text-muted small fw-bold">Title</label>
                                <input type="text" name="title" class="form-control rounded-3" placeholder="e.g. Chapter 1: Introduction" required>
                            </div>
                            <div class="mb-0">
                                <label class="form-label text-muted small fw-bold">Content / Description</label>
                                <textarea name="content" class="form-control rounded-3" rows="5" placeholder="Enter the material content here..." required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer border-0">
                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary rounded-pill px-4">Upload Material</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <style>
        .last-child-no-border:last-child {
            border-bottom: none !important;
            margin-bottom: 0 !important;
            padding-bottom: 0 !important;
        }
    </style>
@endsection
