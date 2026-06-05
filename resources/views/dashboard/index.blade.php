@extends('layouts.app')

@section('content')
    @php
        $greetings = [
            ['text' => 'Selamat datang kembali', 'lang' => 'Indonesian'],
            ['text' => 'おかえりなさい', 'lang' => 'Japanese'],
            ['text' => '欢迎回来', 'lang' => 'Chinese'],
            ['text' => 'أهلاً بك مجدداً', 'lang' => 'Arabic'],
            ['text' => 'Welcome back', 'lang' => 'English'],
            ['text' => 'Bienvenue à nouveau', 'lang' => 'French'],
            ['text' => 'Bentornato', 'lang' => 'Italian']
        ];
        $random = $greetings[array_rand($greetings)];
    @endphp

    <div class="row g-4 mb-5">
        <div class="col-12">
            <div class="card border-0 rounded-5 p-5 shadow-sm position-relative overflow-hidden" 
                 style="background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color: white;">
                <div class="position-relative z-1">
                    <h1 class="display-5 fw-bold mb-2">{{ $random['text'] }}, {{ auth()->user()->name }}!</h1>
                    <p class="lead opacity-75 mb-0 fw-medium">Ready to continue your learning journey? You have <b>{{ $pendingAssignments->count() }}</b> pending tasks today.</p>
                </div>
                <!-- Abstract Design Elements -->
                <div class="position-absolute" style="top: -50px; right: -50px; width: 200px; height: 200px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
                <div class="position-absolute" style="bottom: -20px; left: 10%; width: 100px; height: 100px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
                <i data-lucide="sparkles" class="position-absolute" style="top: 20px; right: 100px; opacity: 0.2; width: 64px; height: 64px;"></i>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-5 animate-stagger">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-5 p-4 h-100 luxury-stat-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon-wrapper bg-primary-soft text-primary p-3 rounded-4">
                        <i data-lucide="layout-dashboard" size="24"></i>
                    </div>
                    <div>
                        <div class="text-muted smallest ls-1 uppercase fw-bold">Joined Spaces</div>
                        <h4 class="fw-extrabold m-0 text-main">{{ $stats['total_classes'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-5 p-4 h-100 luxury-stat-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon-wrapper bg-success-soft text-success p-3 rounded-4">
                        <i data-lucide="check-circle" size="24"></i>
                    </div>
                    <div>
                        <div class="text-muted smallest ls-1 uppercase fw-bold">Completed Tasks</div>
                        <h4 class="fw-extrabold m-0 text-main">{{ $stats['completed_tasks'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-5 p-4 h-100 luxury-stat-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon-wrapper bg-warning-soft text-warning p-3 rounded-4">
                        <i data-lucide="activity" size="24"></i>
                    </div>
                    <div>
                        <div class="text-muted smallest ls-1 uppercase fw-bold">Active Tasks</div>
                        <h4 class="fw-extrabold m-0 text-main">{{ $stats['active_assignments'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-5 p-4 h-100 luxury-stat-card bg-primary text-white shadow-lg">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon-wrapper bg-white bg-opacity-25 text-white p-3 rounded-4 border border-white border-opacity-25">
                        <i data-lucide="crown" size="24"></i>
                    </div>
                    <div>
                        <div class="text-white-50 smallest ls-1 uppercase fw-bold">Learner Rank</div>
                        <h5 class="fw-extrabold m-0 text-truncate" style="max-width: 120px;">{{ $stats['member_rank'] }}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Recently Accessed -->
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4 px-2">
                <h4 class="fw-extrabold m-0 d-flex align-items-center gap-2 text-main">
                    <i data-lucide="clock" class="text-primary"></i>
                    Recently Visited
                </h4>
                <a href="{{ route('courses.index') }}" class="btn btn-luxury-light rounded-pill px-4 fw-bold border-0 shadow-sm transition-all hover-translate-y btn-ripple" onclick="addRipple(event, this)">Explore All</a>
            </div>

            <div class="row g-4">
                @forelse($recentClassrooms as $classroom)
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 h-100 luxury-card p-0 overflow-hidden">
                        <div class="classroom-card-banner" style="height: 100px; background: {{ $classroom->banner ? 'url('.asset('storage/'.$classroom->banner).')' : 'linear-gradient(45deg, var(--primary-color), var(--secondary-color))' }}; background-size: cover; background-position: center;"></div>
                        
                        <div class="p-4 position-relative">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($classroom->teacher->name) }}&background=6366f1&color=fff" 
                                 class="rounded-circle border border-4 border-white shadow-sm position-absolute" 
                                 width="54" height="54" style="top: -27px; left: 24px;">
                            
                            <div class="pt-4 mt-1">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="badge rounded-pill fw-bold" style="background: rgba(var(--primary-rgb), 0.1); color: var(--primary-color);">{{ $classroom->pivot->role }}</span>
                                    @if($classroom->pivot->is_starred)
                                        <i data-lucide="star" class="text-warning fill-warning" size="14"></i>
                                    @endif
                                </div>
                                <h5 class="fw-extrabold mb-2 text-main">{{ $classroom->title }}</h5>
                                <p class="text-muted small mb-4 line-clamp-2">{{ $classroom->description ?: 'No description available for this classroom.' }}</p>
                                
                                <div class="d-grid">
                                    <a href="{{ route('courses.show', $classroom) }}" class="btn btn-primary rounded-pill fw-bold py-2 px-4 shadow-sm" onclick="addRipple(event, this)">Enter Classroom</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="text-center py-5 rounded-5 border-dashed bg-light-subtle">
                        <i data-lucide="layers" size="48" class="text-muted opacity-25 mb-3"></i>
                        <h5 class="text-muted fw-bold">No recent activity</h5>
                        <p class="text-muted mb-4 small">Join a class using a code to get started!</p>
                        <a href="{{ route('courses.index') }}" class="btn btn-primary rounded-pill px-4 shadow">Get Started</a>
                    </div>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Assignments Sidebar -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 sticky-top h-auto" style="top: 100px; background: var(--card-bg);">
                <h4 class="fw-extrabold mb-4 d-flex align-items-center gap-2 text-main">
                    <i data-lucide="calendar-check" class="text-primary"></i>
                    To-Do List
                </h4>
                
                <div class="d-flex flex-column gap-3">
                    @forelse($pendingAssignments as $assignment)
                    <a href="{{ route('courses.show', $assignment->classroom_id) }}" class="text-decoration-none p-3 rounded-4 bg-light-subtle transition-all hover-scale border border-transparent hover-border-primary">
                        <div class="d-flex align-items-start gap-3">
                            <div class="p-2 rounded-3 text-white shadow-sm" style="background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));">
                                <i data-lucide="file-check" size="20"></i>
                            </div>
                            <div class="overflow-hidden">
                                <div class="fw-bold text-truncate text-main">{{ $assignment->title }}</div>
                                <div class="text-muted smallest d-flex align-items-center gap-1 mt-1 fw-medium">
                                    <i data-lucide="clock" size="12"></i>
                                    Due: {{ $assignment->due_date->format('M d, H:i') }}
                                </div>
                            </div>
                        </div>
                    </a>
                    @empty
                    <div class="text-center py-4">
                        <div class="bg-success-soft d-inline-flex p-3 rounded-circle text-success mb-3">
                            <i data-lucide="check-circle" size="28"></i>
                        </div>
                        <h6 class="fw-bold mb-1 text-main">Excellent Work!</h6>
                        <p class="text-muted smaller mb-0 px-4">You've completed all your pending tasks.</p>
                    </div>
                    @endforelse
                </div>

                @if($pendingAssignments->count() > 0)
                <div class="mt-4 pt-3 border-top d-grid">
                    <button class="btn btn-luxury-light rounded-pill fw-bold text-primary transition-all btn-ripple" onclick="addRipple(event, this)">View Full Schedule</button>
                </div>
                @endif
            </div>
        </div>
    </div>

    <style>
        .luxury-card {
            transition: var(--transition);
            background: var(--card-bg);
            border: 1px solid var(--border-color) !important;
        }
        .luxury-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 24px -10px rgba(0,0,0,0.15) !important;
            border-color: var(--primary-color) !important;
        }
        .luxury-stat-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid var(--border-color) !important;
        }
        .luxury-stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px -10px rgba(0,0,0,0.1) !important;
            border-color: var(--primary-color) !important;
        }
        .bg-white-soft { background: rgba(255, 255, 255, 0.15); }
        .hover-scale {
            transition: var(--transition);
            cursor: pointer;
        }
        .hover-scale:hover {
            transform: scale(1.02);
            background: var(--card-bg) !important;
            border-color: var(--primary-color) !important;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        .ls-1 { letter-spacing: 0.5px; }
        .bg-primary-soft { background-color: rgba(99, 102, 241, 0.1); }
        .bg-success-soft { background-color: rgba(34, 197, 94, 0.1); }
        .bg-warning-soft { background-color: rgba(245, 158, 11, 0.1); }
        .hover-translate-y:hover { transform: translateY(-2px); }
    </style>
@endsection
