@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">My Classrooms</h2>
        <div>
            <button class="btn btn-outline-primary me-2" data-bs-toggle="modal" data-bs-target="#joinClassModal">Join Class</button>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createClassModal">Create Class</button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 rounded-4 mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger border-0 rounded-4 mb-4">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        @forelse($classrooms as $classroom)
            <div class="col-md-6 mb-4">
                <div class="card h-100 p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <span class="badge {{ $classroom->pivot->role === 'teacher' ? 'bg-success' : 'bg-primary' }} rounded-pill px-3">
                            {{ ucfirst($classroom->pivot->role) }}
                        </span>
                        <small class="text-muted">Code: <strong>{{ $classroom->join_code }}</strong></small>
                    </div>
                    <h4 class="fw-bold mb-2">{{ $classroom->name }}</h4>
                    <p class="text-muted small mb-4">{{ Str::limit($classroom->description, 100) }}</p>
                    <div class="mt-auto">
                        <a href="{{ route('classrooms.show', $classroom) }}" class="btn btn-outline-dark w-100 rounded-pill">Enter Class</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <div class="text-muted">You haven't joined or created any classes yet.</div>
            </div>
        @endforelse
    </div>

    <!-- Create Class Modal -->
    <div class="modal fade" id="createClassModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow">
                <form action="{{ route('classrooms.store') }}" method="POST">
                    @csrf
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold">Create New Class</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">Class Name</label>
                            <input type="text" name="name" class="form-control rounded-3" placeholder="e.g. Mathematics 101" required>
                        </div>
                        <div class="mb-0">
                            <label class="form-label text-muted small fw-bold">Description</label>
                            <textarea name="description" class="form-control rounded-3" rows="3" placeholder="Tell your students about this class..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">Create Class</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Join Class Modal -->
    <div class="modal fade" id="joinClassModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow">
                <form action="{{ route('classrooms.join') }}" method="POST">
                    @csrf
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold">Join Class</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted small">Enter the 8-digit code provided by your teacher.</p>
                        <div class="mb-0">
                            <label class="form-label text-muted small fw-bold">Join Code</label>
                            <input type="text" name="join_code" class="form-control rounded-3 text-center fw-bold fs-4" placeholder="ABC123XY" maxlength="8" required>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">Join Class</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
