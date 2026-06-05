@extends('layouts.app')

@section('content')
<div class="row mb-5">
    <div class="col-12">
        <h2 class="fw-extrabold mb-1 d-flex align-items-center gap-3">
            <div class="p-2 bg-primary-soft rounded-3 text-primary">
                <i data-lucide="search" size="32"></i>
            </div>
            Search Results
        </h2>
        <p class="text-muted fw-medium mt-2">
            Showing results for <span class="text-primary fw-bold">"{{ $query }}"</span>
        </p>
    </div>
</div>

@if($classrooms->isEmpty() && $assignments->isEmpty() && $users->isEmpty())
    <div class="text-center py-5 my-5">
        <div class="mb-4">
            <i data-lucide="search-x" class="text-muted opacity-25" style="width:120px;height:120px"></i>
        </div>
        <h3 class="fw-bold mb-3 text-main">No Matches Found</h3>
        <p class="text-muted mb-5 mx-auto" style="max-width: 400px;">
            We couldn't find anything matching your query. Try searching for something else or use tags like 
            <code class="bg-light-subtle px-2 py-1 rounded">class:math</code> or <code class="bg-light-subtle px-2 py-1 rounded">user:admin</code>.
        </p>
        <a href="{{ route('dashboard') }}" class="btn btn-primary rounded-pill px-5 fw-bold">Back to Dashboard</a>
    </div>
@else
    <div class="row g-5">
        <!-- Classrooms -->
        @if($classrooms->count() > 0)
        <div class="col-12">
            <h4 class="fw-extrabold mb-4 d-flex align-items-center gap-2 text-main">
                <i data-lucide="layout" class="text-primary"></i>
                Learning Spaces ({{ $classrooms->count() }})
            </h4>
            <div class="row g-4">
                @foreach($classrooms as $classroom)
                <div class="col-md-6 col-lg-4 col-xl-3">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 luxury-card">
                        <div class="course-banner-area" style="height: 100px; background: {{ $classroom->banner ? 'url('.asset('storage/'.$classroom->banner).')' : 'linear-gradient(135deg, var(--primary-color), var(--secondary-color))' }}; background-size: cover; background-position: center;"></div>
                        <div class="p-4">
                            <h5 class="fw-extrabold mb-1 text-truncate text-main">{{ $classroom->title }}</h5>
                            <div class="small fw-medium text-muted mb-3 italic">Taught by {{ $classroom->teacher->name }}</div>
                            <div class="d-grid mt-4">
                                <a href="{{ route('courses.show', $classroom) }}" class="btn btn-luxury-light rounded-pill fw-bold">Enter Space</a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Assignments -->
        @if($assignments->count() > 0)
        <div class="col-12">
            <h4 class="fw-extrabold mb-4 d-flex align-items-center gap-2 text-main">
                <i data-lucide="file-check" class="text-primary"></i>
                Assignments ({{ $assignments->count() }})
            </h4>
            <div class="row g-4">
                @foreach($assignments as $assignment)
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100 luxury-card">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="p-2 bg-primary-soft rounded-3 text-primary">
                                <i data-lucide="file-text" size="20"></i>
                            </div>
                            <div>
                                <h5 class="fw-extrabold m-0 text-main">{{ $assignment->title }}</h5>
                                <div class="smallest text-muted fw-bold ls-1 uppercase">{{ $assignment->classroom->title }}</div>
                            </div>
                        </div>
                        <p class="text-muted small mb-4">{{ Str::limit(strip_tags($assignment->description), 100) }}</p>
                        <div class="d-flex justify-content-between align-items-center mt-auto">
                            <div class="text-muted smallest fw-medium">
                                <i data-lucide="clock" size="12"></i> Due: {{ $assignment->due_date?->format('M d, Y') ?? 'No deadline' }}
                            </div>
                            <a href="{{ route('courses.show', $assignment->classroom) }}" class="btn btn-link text-primary text-decoration-none fw-bold small p-0">View Assignment</a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Users -->
        @if($users->count() > 0)
        <div class="col-12">
            <h4 class="fw-extrabold mb-4 d-flex align-items-center gap-2 text-main">
                <i data-lucide="users" class="text-primary"></i>
                People ({{ $users->count() }})
            </h4>
            <div class="row g-4">
                @foreach($users as $u)
                <div class="col-md-4 col-xl-3">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100 luxury-card text-center">
                        <div class="mb-3">
                            <img src="{{ $u->avatar_path ? asset('storage/'.$u->avatar_path) : 'https://ui-avatars.com/api/?name='.urlencode($u->name).'&background=6366f1&color=fff' }}" 
                                 class="rounded-circle shadow-sm border border-3 border-white" width="70" height="70">
                        </div>
                        <h6 class="fw-extrabold mb-1 text-main">{{ $u->name }}</h6>
                        <div class="text-muted smaller mb-0">@<span></span>{{ $u->username }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
@endif

<style>
    .luxury-card {
        transition: var(--transition);
        background: var(--card-bg);
        border: 1px solid var(--border-color) !important;
    }
    .luxury-card:hover { 
        transform: translateY(-6px); 
        border-color: var(--primary-color) !important;
        box-shadow: 0 15px 30px -10px rgba(0,0,0,0.1) !important;
    }
    .text-main { color: var(--text-color); }
    .bg-primary-soft { background: rgba(var(--primary-rgb), 0.1); }
    .ls-1 { letter-spacing: 1px; }
</style>
@endsection
