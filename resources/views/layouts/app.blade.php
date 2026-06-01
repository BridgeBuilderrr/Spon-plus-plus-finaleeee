<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Spon++') }} - Dashboard</title>
    
    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Cropper.js -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>

    <!-- UI Enhancements Style -->
    <style>
        :root {
            --primary-color: #6366f1; /* Indigo-600 */
            --primary-light: #818cf8;
            --primary-rgb: 99, 102, 241;
            --secondary-color: #8b5cf6; /* Violet-500 */
            --bg-color: #f8fafc; /* Slate-50 */
            --card-bg: #ffffff;
            --sidebar-bg: #ffffff;
            --text-color: #0f172a; /* Slate-900 */
            --text-muted: #64748b; /* Slate-500 */
            --border-color: #e2e8f0; /* Slate-200 */
            --sidebar-hover: rgba(99, 102, 241, 0.05);
            --transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            --glass-bg: rgba(255, 255, 255, 0.7);
        }

        [data-bs-theme="dark"] {
            --primary-color: #818cf8; /* Indigo-400 */
            --primary-light: #a5b4fc;
            --primary-rgb: 129, 140, 248;
            --secondary-color: #a78bfa; /* Violet-400 */
            --bg-color: #020617; /* Slate-950 - Deepest */
            --card-bg: #0f172a; /* Slate-900 - Card layer */
            --sidebar-bg: #030712; /* Distinct Sidebar */
            --text-color: #f8fafc; /* Slate-50 */
            --text-muted: #94a3b8; /* Slate-400 */
            --border-color: #1e293b; /* Slate-800 */
            --sidebar-hover: rgba(129, 140, 248, 0.1);
            --glass-bg: rgba(15, 23, 42, 0.7);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            transition: var(--transition);
            overflow-x: hidden;
            letter-spacing: -0.01em;
        }

        /* Premium Scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: var(--bg-color); }
        ::-webkit-scrollbar-thumb { background: var(--border-color); border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--text-muted); }

        /* Sidebar Desktop */
        .sidebar {
            width: 280px;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background-color: var(--sidebar-bg);
            border-right: 1px solid var(--border-color);
            padding: 32px 24px;
            z-index: 1050;
            transition: var(--transition);
        }

        .main-content {
            margin-left: 280px;
            padding: 40px;
            min-height: 100vh;
            transition: var(--transition);
        }

        /* Topbar */
        .topbar {
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            padding: 0 32px;
            margin-bottom: 40px;
            border-radius: 20px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        /* Logo */
        .logo-text {
            font-size: 1.75rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-decoration: none;
            letter-spacing: -0.03em;
        }

        /* Navigation Links */
        .nav-links { margin-top: 40px; }
        .nav-link-custom {
            color: var(--text-muted);
            padding: 14px 18px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            gap: 14px;
            font-weight: 600;
            margin-bottom: 8px;
            transition: var(--transition);
            text-decoration: none;
        }

        .nav-link-custom:hover, .nav-link-custom.active {
            background-color: var(--sidebar-hover);
            color: var(--primary-color);
            transform: translateX(4px);
        }

        .nav-link-custom i { width: 22px; height: 22px; }

        /* Bottom Nav (Mobile) */
        .bottom-nav {
            display: none;
            position: fixed;
            bottom: 20px;
            left: 20px;
            right: 20px;
            height: 70px;
            background-color: rgba(var(--primary-rgb), 0.95);
            backdrop-filter: blur(12px) saturate(180%);
            border-radius: 24px;
            z-index: 1100;
            justify-content: space-around;
            align-items: center;
            box-shadow: 0 12px 32px rgba(99, 102, 241, 0.35);
        }

        .bottom-link {
            color: rgba(255, 255, 255, 0.7);
            padding: 10px;
            border-radius: 16px;
            transition: var(--transition);
            text-decoration: none;
        }

        .bottom-link.active {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.2);
        }

        @media (max-width: 991.98px) {
            .sidebar { display: none; }
            .main-content { margin-left: 0; padding: 24px; padding-bottom: 110px; }
            .topbar { padding: 0 20px; margin-bottom: 24px; }
            .bottom-nav { display: flex; }
        }

        /* Forms & Inputs */
        .form-control, .form-select {
            background-color: var(--bg-color);
            border: 1.5px solid var(--border-color);
            border-radius: 14px;
            padding: 12px 16px;
            font-weight: 500;
            transition: var(--transition);
        }

        .form-control:focus {
            background-color: var(--card-bg);
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(var(--primary-rgb), 0.1);
        }

        /* Buttons */
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            padding: 12px 24px;
            border-radius: 14px;
            font-weight: 700;
            box-shadow: 0 4px 12px rgba(var(--primary-rgb), 0.2);
            transition: var(--transition);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(var(--primary-rgb), 0.3);
            filter: brightness(1.1);
        }

        /* Toast styles */
        .toast-container-custom {
            position: fixed;
            top: 24px;
            right: 24px;
            z-index: 1200;
        }

        .toast-premium {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 18px;
            padding: 16px 24px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 16px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            animation: slideInToast 0.4s cubic-bezier(0.23, 1, 0.32, 1);
            max-width: 400px;
        }

        @keyframes slideInToast {
            from { transform: translateX(100%) scale(0.9); opacity: 0; }
            to { transform: translateX(0) scale(1); opacity: 1; }
        }

        /* Modal Enhancements */
        .modal-content {
            border-radius: 28px;
            border: none;
            background-color: var(--card-bg);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .modal-header { border-bottom: none; padding: 32px 32px 16px; }
        .modal-body { padding: 16px 32px 32px; }
        .modal-footer { border-top: none; padding: 16px 32px 32px; }

        /* Search Bar */
        .search-bar-luxury {
            background: var(--bg-color);
            border: 1.5px solid var(--border-color);
            border-radius: 16px;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            width: 100%;
            max-width: 480px;
            transition: var(--transition);
        }

        .search-bar-luxury:focus-within {
            border-color: var(--primary-color);
            background: var(--card-bg);
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        }

        .search-bar-luxury input {
            border: none;
            background: none;
            outline: none;
            color: var(--text-color);
            width: 100%;
            font-weight: 600;
        }

        .icon-btn-luxury {
            background: var(--bg-color);
            border: 1.5px solid var(--border-color);
            color: var(--text-color);
            padding: 10px;
            border-radius: 14px;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .icon-btn-luxury:hover {
            border-color: var(--primary-color);
            color: var(--primary-color);
            background: var(--sidebar-hover);
        }
    </style>
    @stack('styles')
</head>
<body data-bs-theme="light">

    <!-- Sidebar (Desktop) -->
    <aside class="sidebar">
        <a href="{{ route('dashboard') }}" class="logo-text d-flex align-items-center gap-2">
            <i data-lucide="layers" class="text-primary" style="width:32px;height:32px"></i>
            Spon++
        </a>
        
        <nav class="nav-links">
            <a href="{{ route('dashboard') }}" class="nav-link-custom {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i data-lucide="home"></i> Home
            </a>
            <a href="{{ route('courses.index') }}" class="nav-link-custom {{ request()->routeIs('courses.*') ? 'active' : '' }}">
                <i data-lucide="book-open"></i> My Courses
            </a>
            <a href="{{ route('profile') }}" class="nav-link-custom {{ request()->routeIs('profile') ? 'active' : '' }}">
                <i data-lucide="user"></i> My Profile
            </a>
        </nav>

        <div class="mt-auto" style="position: absolute; bottom: 40px; width: calc(100% - 48px);">
            <div class="p-3 rounded-4 bg-light-subtle border mb-4">
                <div class="d-flex align-items-center gap-3">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=6366f1&color=fff" class="rounded-circle shadow-sm" width="40" height="40">
                    <div class="overflow-hidden">
                        <div class="fw-bold small text-truncate">{{ auth()->user()->name }}</div>
                        <div class="text-muted smaller">@<span></span>{{ auth()->user()->username }}</div>
                    </div>
                </div>
            </div>
            
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="button" class="nav-link-custom text-danger border-0 bg-transparent w-100" onclick="confirmLogout()">
                    <i data-lucide="log-out"></i> Sign Out
                </button>
            </form>
        </div>
    </aside>

    <!-- Bottom Nav (Mobile) -->
    <nav class="bottom-nav">
        <a href="{{ route('dashboard') }}" class="bottom-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i data-lucide="home"></i>
        </a>
        <a href="{{ route('courses.index') }}" class="bottom-link {{ request()->routeIs('courses.*') ? 'active' : '' }}">
            <i data-lucide="book-open"></i>
        </a>
        <a href="{{ route('profile') }}" class="bottom-link {{ request()->routeIs('profile') ? 'active' : '' }}">
            <i data-lucide="user"></i>
        </a>
        <a href="javascript:void(0)" class="bottom-link" onclick="confirmLogout()">
            <i data-lucide="log-out"></i>
        </a>
    </nav>

    <!-- Toast Component -->
    <div class="toast-container-custom" id="toastContainer"></div>

    <div class="main-content">
        <!-- Topbar -->
        <header class="topbar">
            <!-- Mobile Logo -->
            <a href="{{ route('dashboard') }}" class="logo-text d-lg-none">S</a>
            
            <div class="search-bar-luxury d-none d-md-flex">
                <i data-lucide="search" size="18" class="text-muted"></i>
                <input type="text" placeholder="Search for assignments, classes, or people...">
            </div>
            
            <div class="d-flex align-items-center gap-3">
                <button class="icon-btn-luxury" onclick="toggleTheme()" title="Toggle Theme">
                    <i data-lucide="moon" id="theme-icon"></i>
                </button>
                <div class="vr mx-2 d-none d-md-block" style="height: 24px; opacity: 0.1;"></div>
                <button class="icon-btn-luxury position-relative">
                    <i data-lucide="bell" size="20"></i>
                    <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                </button>
            </div>
        </header>

        <main>
            @yield('content')
        </main>
    </div>

    <!-- Confirm Modal -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-body text-center p-5">
                    <div class="mb-4 text-danger bg-danger-subtle d-inline-flex p-3 rounded-circle" id="confirmIconContainer">
                        <i data-lucide="alert-triangle" style="width:32px;height:32px"></i>
                    </div>
                    <h4 class="fw-bold mb-3" id="confirmTitle">Are you sure?</h4>
                    <p class="text-muted mb-4" id="confirmMessage">This action cannot be undone.</p>
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-danger py-3 fw-bold rounded-4" id="confirmActionBtn">Confirm</button>
                        <button type="button" class="btn btn-light py-3 fw-bold rounded-4" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Init Lucide
        lucide.createIcons();

        // Theme Handling
        (function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.body.setAttribute('data-bs-theme', savedTheme);
            updateThemeIcon(savedTheme);
        })();

        function toggleTheme() {
            const body = document.body;
            const currentTheme = body.getAttribute('data-bs-theme');
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            body.setAttribute('data-bs-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateThemeIcon(newTheme);
            lucide.createIcons();
        }

        function updateThemeIcon(theme) {
            const icon = document.getElementById('theme-icon');
            if(theme === 'dark') {
                icon.setAttribute('data-lucide', 'sun');
            } else {
                icon.setAttribute('data-lucide', 'moon');
            }
        }

        // Toast Helper
        function showToast(message, type = 'success') {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            toast.className = 'toast-premium';
            
            const icon = type === 'success' ? 'check-circle' : (type === 'error' ? 'x-circle' : 'alert-circle');
            const iconColor = type === 'success' ? 'text-success' : (type === 'error' ? 'text-danger' : 'text-warning');
            
            toast.innerHTML = `
                <i data-lucide="${icon}" class="${iconColor}" style="width:24px;height:24px;flex-shrink:0"></i>
                <div class="flex-grow-1 fw-bold smaller">${message}</div>
            `;
            
            container.appendChild(toast);
            lucide.createIcons();
            
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(-20px)';
                setTimeout(() => toast.remove(), 400);
            }, 4000);
        }

        // Confirmation Helper
        function showConfirm({title, message, btnText, btnClass, onConfirm}) {
            const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
            document.getElementById('confirmTitle').textContent = title;
            document.getElementById('confirmMessage').textContent = message;
            const btn = document.getElementById('confirmActionBtn');
            btn.textContent = btnText || 'Confirm';
            btn.className = `btn py-3 fw-bold rounded-4 ${btnClass || 'btn-danger'}`;
            
            btn.onclick = () => {
                onConfirm();
                modal.hide();
            };
            modal.show();
        }

        function confirmLogout() {
            showConfirm({
                title: 'Sign Out',
                message: 'Are you sure you want to log out of your session?',
                btnText: 'Yes, Sign Out',
                onConfirm: () => {
                    const form = document.querySelector('form[action="{{ route('logout') }}"]');
                    form.submit();
                }
            });
        }

        // Idle Session Logic (Professional Touch)
        let idleTimer;
        const IDLE_TIMEOUT = 10 * 60 * 1000; // 10 Minutes

        function resetIdleTimer() {
            clearTimeout(idleTimer);
            idleTimer = setTimeout(() => {
                showToast('Session idle. Auto-logging out for security...', 'warning');
                setTimeout(() => {
                    document.querySelector('form[action="{{ route('logout') }}"]').submit();
                }, 1500);
            }, IDLE_TIMEOUT);
        }

        document.addEventListener('mousemove', resetIdleTimer);
        document.addEventListener('keypress', resetIdleTimer);
        resetIdleTimer();

        // Flash Messages
        @if(session('success')) showToast("{{ session('success') }}", 'success'); @endif
        @if(session('error')) showToast("{{ session('error') }}", 'error'); @endif
    </script>
    @stack('scripts')
</body>
</html>
