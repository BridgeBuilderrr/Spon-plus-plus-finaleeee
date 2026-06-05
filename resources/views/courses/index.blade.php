@extends('layouts.app')

@section('content')
    <div class="row mb-5 align-items-center">
        <div class="col-md-6 mb-3 mb-md-0">
            <h2 class="fw-extrabold mb-1 d-flex align-items-center gap-2">
                <i data-lucide="book-open" class="text-primary" style="width:32px;height:32px"></i>
                My Learning Spaces
            </h2>
            <p class="text-muted fw-medium mb-0">Manage and explore your enrolled and created classrooms.</p>
        </div>
        <div class="col-md-6 text-md-end">
            <div class="d-flex gap-3 justify-content-md-end">
                <button class="btn btn-luxury-light rounded-pill px-4 fw-bold border-0 shadow-sm btn-ripple" data-bs-toggle="modal" data-bs-target="#joinClassModal" onclick="addRipple(event, this)">
                    <i data-lucide="plus" class="me-2" size="18"></i>
                    Join Class
                </button>
                <button class="btn btn-primary rounded-pill px-4 fw-bold shadow" data-bs-toggle="modal" data-bs-target="#createClassModal">
                    <i data-lucide="plus-circle" class="me-2" size="18"></i>
                    Create Space
                </button>
            </div>
        </div>
    </div>

    @if($classrooms->isEmpty() && !request('search'))
        <div class="text-center py-5 my-5">
            <div class="luxury-empty-state mb-4">
                <i data-lucide="layout" class="text-primary opacity-25" style="width:120px;height:120px"></i>
            </div>
            <h3 class="fw-bold mb-3">No Classrooms Yet</h3>
            <p class="text-muted mb-5 mx-auto" style="max-width: 400px;">Every great journey starts with a single step. Join an existing class or create your own learning space to begin.</p>
            <div class="d-flex justify-content-center gap-3">
                <button class="btn btn-primary btn-lg rounded-pill px-5 fw-bold" data-bs-toggle="modal" data-bs-target="#createClassModal">Create Now</button>
                <button class="btn btn-luxury-light btn-lg rounded-pill px-5 fw-bold btn-ripple" data-bs-toggle="modal" data-bs-target="#joinClassModal" onclick="addRipple(event, this)">Join One</button>
            </div>
        </div>
    @else
        <!-- Filter Bar -->
        <div class="card border-0 shadow-sm rounded-4 p-3 mb-5 luxury-filter-bar" style="background: var(--card-bg);">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <form action="{{ route('courses.index') }}" method="GET" class="d-flex gap-3 flex-grow-1 align-items-center" style="max-width: 600px;">
                    <div class="luxury-search-wrapper flex-grow-1">
                        <i data-lucide="search" size="18" class="text-muted"></i>
                        <input type="text" name="search" placeholder="Search by classroom name, tags, or teacher..." value="{{ request('search') }}">
                    </div>
                    <select name="sort" class="form-select luxury-select" onchange="this.form.submit()">
                        <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Sort by Name</option>
                        <option value="last_accessed" {{ request('sort') == 'last_accessed' ? 'selected' : '' }}>Recently Accessed</option>
                    </select>
                </form>
                
                <div class="luxury-view-switcher position-relative d-flex align-items-center p-1">
                    <div class="switcher-active-pill" id="switcher-pill"></div>
                    <button class="btn luxury-view-btn p-2 rounded-circle position-relative"
                            onclick="setView('grid')" id="btn-grid" title="Grid View">
                        <i data-lucide="grid-3x3" size="18"></i>
                    </button>
                    <button class="btn luxury-view-btn p-2 rounded-circle position-relative"
                            onclick="setView('list')" id="btn-list" title="List View">
                        <i data-lucide="list" size="18"></i>
                    </button>
                </div>
            </div>
        </div>

        @if($classrooms->isEmpty() && request('search'))
            <div class="text-center py-5">
                <i data-lucide="search-x" class="text-muted mb-3" size="48"></i>
                <h5>No results found for "{{ request('search') }}"</h5>
                <a href="{{ route('courses.index') }}" class="btn btn-link text-primary text-decoration-none fw-bold">Clear Filters</a>
            </div>
        @endif

        <!-- Grid View -->
        <div id="courses-grid" class="row g-4 d-none transition-fade">
            @foreach($classrooms as $classroom)
            <div class="col-md-6 col-lg-4 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 course-luxury-card">
                    <div class="position-absolute top-0 end-0 p-3 z-1">
                        <span class="badge rounded-pill bg-white text-dark shadow-sm fw-bold border" style="font-size: 0.7rem; opacity: 0.9;">{{ $classroom->pivot->role }}</span>
                    </div>
                    
                    <div class="course-banner-area" style="height: 140px; background: {{ $classroom->banner ? 'url('.asset('storage/'.$classroom->banner).')' : 'linear-gradient(135deg, var(--primary-color), var(--secondary-color))' }}; background-size: cover; background-position: center;">
                        <div class="banner-glass-overlay"></div>
                    </div>

                    <div class="p-4 pt-0 position-relative">
                        <div class="teacher-avatar-container" style="margin-top: -32px;">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($classroom->teacher->name) }}&background=6366f1&color=fff" 
                                 class="rounded-circle border border-4 border-white shadow-sm" width="64" height="64">
                        </div>
                        
                        <h5 class="fw-extrabold mt-3 mb-1 text-truncate">{{ $classroom->title }}</h5>
                        <div class="small fw-medium text-muted mb-3">Taught by <b>{{ $classroom->teacher->name }}</b></div>
                        
                        <div class="d-flex flex-wrap gap-1 mb-4 h-auto" style="min-height: 20px;">
                            @if($classroom->tags)
                                @foreach($classroom->tags as $tag)
                                    <span class="badge rounded-pill luxury-tag">{{ $tag }}</span>
                                @endforeach
                            @endif
                        </div>

                        <div class="d-grid mt-auto">
                            <a href="{{ route('courses.show', $classroom) }}" class="btn btn-primary rounded-pill fw-bold py-2 px-4 shadow-sm">
                                Enter Classroom
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- List View -->
        <div id="courses-list" class="d-none transition-fade">
            <div class="card border-0 shadow-sm rounded-5 overflow-hidden" style="background: var(--card-bg);">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr class="bg-light-subtle">
                                <th class="ps-4 py-4 text-muted small fw-bold">#</th>
                                <th class="py-4 text-muted small fw-bold">SPACE NAME</th>
                                <th class="py-4 text-muted small fw-bold">ROLE</th>
                                <th class="py-4 text-muted small fw-bold">TAGS</th>
                                <th class="py-4 text-muted small fw-bold">BIO</th>
                                <th class="pe-4 py-4 text-end text-muted small fw-bold">ACTION</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($classrooms as $index => $classroom)
                            <tr class="cursor-pointer" onclick="window.location='{{ route('courses.show', $classroom) }}'">
                                <td class="ps-4 text-muted fw-bold">{{ ($classrooms->currentPage()-1) * $classrooms->perPage() + $index + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-circle border border-primary border-2 p-1">
                                            <div class="rounded-circle" style="width: 32px; height: 32px; background: {{ $classroom->banner ? 'url('.asset('storage/'.$classroom->banner).')' : 'linear-gradient(45deg, var(--primary-color), var(--secondary-color))' }}; background-size: cover;"></div>
                                        </div>
                                        <div>
                                            <span class="fw-extrabold d-block">{{ $classroom->title }}</span>
                                            <span class="smaller text-muted fw-medium">{{ $classroom->teacher->name }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge rounded-pill fw-bold" style="background: rgba(var(--primary-rgb), 0.1); color: var(--primary-color);">{{ $classroom->pivot->role }}</span>
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap gap-1">
                                        @if($classroom->tags)
                                            @foreach($classroom->tags as $tag)
                                                <span class="badge rounded-pill bg-light text-muted border smallest">{{ $tag }}</span>
                                            @endforeach
                                        @endif
                                    </div>
                                </td>
                                <td class="text-muted smaller fw-medium" style="max-width: 200px;">{{ Str::limit($classroom->description, 50) }}</td>
                                <td class="pe-4 text-end">
                                    <a href="{{ route('courses.show', $classroom) }}" class="btn btn-outline-primary btn-sm rounded-pill px-4 fw-bold border-2">Enter</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-5 d-flex justify-content-center align-items-center">
            <div class="luxury-pagination">
                {{ $classrooms->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @endif

    @include('courses.partials.modals_create_join')

    <script>
        function setView(view) {
            const grid = document.getElementById('courses-grid');
            const list = document.getElementById('courses-list');
            const btnGrid = document.getElementById('btn-grid');
            const btnList = document.getElementById('btn-list');
            const pill = document.getElementById('switcher-pill');

            if (view === 'grid') {
                grid.classList.remove('d-none');
                list.classList.add('d-none');
                btnGrid.classList.add('active-view');
                btnList.classList.remove('active-view');
                if(pill) pill.style.transform = 'translateX(0)';
            } else {
                grid.classList.add('d-none');
                list.classList.remove('d-none');
                btnGrid.classList.remove('active-view');
                btnList.classList.add('active-view');
                if(pill) pill.style.transform = 'translateX(36px)';
            }
            localStorage.setItem('courses_view', view);
            lucide.createIcons();
        }

        const savedView = localStorage.getItem('courses_view') || 'grid';
        setView(savedView);
    </script>

    <style>
        .course-luxury-card {
            transition: var(--transition);
            border: 1px solid var(--border-color) !important;
            background: var(--card-bg);
            display: flex;
            flex-direction: column;
        }
        .course-luxury-card:hover { 
            border-color: var(--primary-color) !important; 
            transform: translateY(-8px); 
            box-shadow: 0 20px 40px -15px rgba(0,0,0,0.2) !important;
        }
        .luxury-filter-bar { border: 1px solid var(--border-color); }
        .luxury-search-wrapper {
            background: var(--bg-color);
            border-radius: 14px;
            padding: 8px 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            border: 1px solid var(--border-color);
            transition: var(--transition);
        }
        .luxury-search-wrapper:focus-within {
            border-color: var(--primary-color);
            background: var(--card-bg);
            box-shadow: 0 0 0 4px rgba(var(--primary-rgb), 0.1);
        }
        .luxury-search-wrapper input {
            border: none;
            background: none;
            outline: none;
            width: 100%;
            font-weight: 600;
            font-size: 0.95rem;
            color: var(--text-color);
        }
        .luxury-select {
            width: auto;
            border-radius: 14px;
            padding: 10px 16px;
            font-weight: 600;
            border: 1px solid var(--border-color);
            background-color: var(--bg-color);
            cursor: pointer;
            transition: var(--transition);
        }
        .luxury-view-switcher {
            background: var(--bs-light-bg-subtle, rgba(0,0,0,0.05));
            border-radius: 999px;
            gap: 0;
            width: fit-content;
        }
        .switcher-active-pill {
            position: absolute;
            top: 4px;
            left: 4px;
            width: 36px;
            height: 36px;
            background: var(--primary-color);
            border-radius: 50%;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 0;
            box-shadow: 0 4px 12px rgba(var(--primary-rgb), 0.35);
            pointer-events: none;
        }
        .luxury-view-btn {
            width: 36px;
            height: 36px;
            z-index: 1;
            transition: color 0.2s ease;
            color: var(--text-muted);
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .luxury-view-btn.active-view {
            color: #fff !important;
            background: transparent !important;
        }
        .luxury-view-btn:not(.active-view):hover {
            color: var(--primary-color);
            background: transparent;
        }
        .luxury-tag { background: rgba(var(--primary-rgb), 0.1); color: var(--primary-color); font-size: 0.65rem; font-weight: 700; border: 1px solid rgba(var(--primary-rgb), 0.05); }
        
        /* Pagination Luxury Styling */
        .luxury-pagination .pagination { margin-bottom: 0; gap: 5px; }
        .luxury-pagination .page-link {
            border-radius: 10px !important;
            padding: 8px 16px;
            font-weight: 700;
            color: var(--text-muted);
            border: 1px solid var(--border-color);
            transition: all 0.2s ease;
        }
        .luxury-pagination .page-item.active .page-link {
            background: var(--primary-color);
            border-color: var(--primary-color);
            color: #fff;
            box-shadow: 0 4px 10px rgba(var(--primary-rgb), 0.3);
        }
        .luxury-pagination .page-link:hover {
            background: var(--primary-soft);
            color: var(--primary-color);
        }

        .ls-1 { letter-spacing: 1px; }
        .uppercase { text-transform: uppercase; }
    </style>
@endsection
