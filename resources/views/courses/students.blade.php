@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <h3 class="fw-bold mb-4">Students</h3>

            <!-- Teachers Section -->
            <div class="mb-5">
                <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3" style="border-color: var(--primary-color) !important;">
                    <h5 class="fw-bold text-primary mb-0">Teachers</h5>
                    <span class="text-muted small">{{ $teachers->count() }} teacher</span>
                </div>
                
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="background: var(--card-bg); border: 1px solid var(--border-color) !important;">
                    <div class="list-group list-group-flush">
                        @foreach($teachers as $teacher)
                        <div class="list-group-item bg-transparent border-0 py-3 px-4 d-flex align-items-center gap-3 hover-bg">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($teacher->name) }}&background=7B6FD4&color=fff" class="rounded-circle shadow-sm" width="40" height="40">
                            <div class="flex-grow-1">
                                <h6 class="fw-bold mb-0">{{ $teacher->name }}</h6>
                                <small class="text-muted">@<span></span>{{ $teacher->username }}</small>
                            </div>
                            @if($teacher->id === $classroom->teacher_id)
                                <span class="badge rounded-pill" style="background: rgba(123, 111, 212, 0.1); color: var(--primary-color);">Owner</span>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Students Section -->
            <div>
                <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3" style="border-color: var(--primary-color) !important;">
                    <h5 class="fw-bold text-primary mb-0">Students</h5>
                    <div class="d-flex align-items-center gap-3">
                        <span class="text-muted small">{{ $students->count() }} students</span>
                        @if(auth()->id() !== $classroom->teacher_id)
                            <form action="{{ route('courses.exit', $classroom) }}" method="POST" onsubmit="return confirm('Are you sure you want to leave this class?')">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-bold">Leave Class</button>
                            </form>
                        @endif
                    </div>
                </div>
                
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="background: var(--card-bg); border: 1px solid var(--border-color) !important;">
                    @if($students->isEmpty())
                        <div class="p-5 text-center text-muted">
                            <i data-lucide="users" size="48" class="opacity-25 mb-3"></i>
                            <div>No students have joined yet. Share the code: <b>{{ $classroom->code }}</b></div>
                        </div>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($students as $student)
                            <div class="list-group-item bg-transparent border-0 py-3 px-4 d-flex align-items-center gap-3 hover-bg">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($student->name) }}&background=random" class="rounded-circle shadow-sm" width="40" height="40">
                                <div class="flex-grow-1">
                                    <h6 class="fw-bold mb-0">{{ $student->name }}</h6>
                                    <small class="text-muted">Joined {{ $student->pivot->created_at->format('M d, Y') }}</small>
                                </div>
                                @if(auth()->id() === $classroom->teacher_id)
                                    <form action="{{ route('courses.kick', [$classroom, $student]) }}" method="POST" onsubmit="return confirm('Kick this student?')">
                                        @csrf
                                        <button type="submit" class="btn icon-btn text-danger p-2 rounded-circle">
                                            <i data-lucide="user-minus" size="18"></i>
                                        </button>
                                    </form>
                                @endif
                                @if($student->id === auth()->id())
                                    <span class="badge bg-light text-dark rounded-pill">You</span>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <style>
        .hover-bg { transition: background 0.2s; cursor: pointer; }
        .hover-bg:hover { background: rgba(123, 111, 212, 0.05) !important; }
        .icon-btn:hover { background: rgba(220, 53, 69, 0.1); }
    </style>
@endsection
