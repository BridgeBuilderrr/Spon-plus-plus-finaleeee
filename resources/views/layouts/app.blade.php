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

    <!-- Dropzone.js (Stable Version) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.js"></script>
    <script>Dropzone.autoDiscover = false;</script>

    <!-- TinyMCE (Safari Compatible CDN) -->
    <script src="https://cdn.tiny.cloud/1/uzyi3qni0rl59wmj5i3t38v3cebtp184ygnuw2vto9ugxut5/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>

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

        /* Dark Mode Utility Fixes */
        [data-bs-theme="dark"] .text-main { color: var(--text-color) !important; }
        [data-bs-theme="dark"] .bg-light-subtle, 
        [data-bs-theme="dark"] .bg-light { 
            background-color: rgba(255, 255, 255, 0.05) !important; 
            color: var(--text-color) !important;
        }
        [data-bs-theme="dark"] .card { background-color: var(--card-bg); }
        [data-bs-theme="dark"] .border-dashed { border-color: var(--border-color) !important; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            transition: var(--transition);
            overflow-x: hidden;
            letter-spacing: -0.01em;
        }

        .text-main { color: var(--text-color); }
        .text-muted { color: var(--text-muted); }

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
            z-index: 1030;
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
            color: var(--text-color);
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
            animation: pulse-glow 1.8s ease-in-out 1;
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
            transform: scale(1.1) rotate(-5deg);
        }

        /* Luxury Light/Glass Button */
        .btn-luxury-light {
            background: var(--bg-color);
            border: 1.5px solid var(--border-color);
            color: var(--text-color);
            padding: 10px 24px;
            border-radius: 14px;
            font-weight: 700;
            transition: var(--transition);
        }

        .btn-luxury-light:hover {
            background: var(--sidebar-hover);
            border-color: var(--primary-color);
            color: var(--primary-color);
            transform: translateY(-2px);
        }

        .btn-luxury-light:active { transform: scale(0.96); }

        /* Bootstrap light button override for dark mode */
        [data-bs-theme="dark"] .btn-light {
            background-color: rgba(255, 255, 255, 0.05) !important;
            border-color: rgba(255, 255, 255, 0.1) !important;
            color: #fff !important;
        }
        [data-bs-theme="dark"] .btn-light:hover {
            background-color: rgba(255, 255, 255, 0.1) !important;
            border-color: var(--primary-color) !important;
            color: #fff !important;
        }

        /* ============================================================
           SPON++ — Animation & Motion Layer
           ============================================================ */

        /* ── 1. KEYFRAMES ─────────────────────────────────────────── */

        @keyframes fadeSlideUp {
            from { opacity: 0; transform: translateY(18px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeSlideIn {
            from { opacity: 0; transform: translateX(-14px); }
            to   { opacity: 1; transform: translateX(0); }
        }

        @keyframes sidebarItemIn {
            from { opacity: 0; transform: translateX(-20px); }
            to   { opacity: 1; transform: translateX(0); }
        }

        @keyframes topbarSlideDown {
            from { opacity: 0; transform: translateY(-24px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 0 0 rgba(var(--primary-rgb), 0); }
            50%       { box-shadow: 0 0 0 8px rgba(var(--primary-rgb), 0.12); }
        }

        @keyframes shimmer {
            0%   { background-position: -600px 0; }
            100% { background-position: 600px 0; }
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        @keyframes ripple {
            to { transform: scale(4); opacity: 0; }
        }

        @keyframes tabIndicatorSlide {
            from { transform: scaleX(0); }
            to   { transform: scaleX(1); }
        }

        @keyframes badgePop {
            0%   { transform: scale(0.5); opacity: 0; }
            70%  { transform: scale(1.25); }
            100% { transform: scale(1); opacity: 1; }
        }

        @keyframes logoGlow {
            0%, 100% { filter: drop-shadow(0 0 0px rgba(var(--primary-rgb), 0)); }
            50%       { filter: drop-shadow(0 0 10px rgba(var(--primary-rgb), 0.5)); }
        }

        @keyframes cardEntrance {
            from { opacity: 0; transform: translateY(24px) scale(0.97); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        @keyframes selectPop {
            0%   { transform: scale(0.97); opacity: 0.6; }
            60%  { transform: scale(1.02); }
            100% { transform: scale(1); opacity: 1; }
        }

        @keyframes bottomNavIn {
            from { opacity: 0; transform: translateY(30px); }
            to   { opacity: 1; transform: translateY(0); }
        }


        /* ── 2. SIDEBAR ───────────────────────────────────────────── */

        /* Sidebar slides in from left on load */
        .sidebar {
            animation: fadeSlideIn 0.45s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        /* Logo subtle breathing glow */
        .logo-text {
            animation: logoGlow 3.5s ease-in-out infinite;
            transition: var(--transition), letter-spacing 0.3s;
        }

        .logo-text:hover {
            letter-spacing: -0.01em;
        }

        /* Each nav link staggers in */
        .nav-link-custom {
            animation: sidebarItemIn 0.4s cubic-bezier(0.16, 1, 0.3, 1) both;
            position: relative;
            overflow: hidden;
        }

        .nav-link-custom:nth-child(1) { animation-delay: 0.08s; }
        .nav-link-custom:nth-child(2) { animation-delay: 0.15s; }
        .nav-link-custom:nth-child(3) { animation-delay: 0.22s; }
        .nav-link-custom:nth-child(4) { animation-delay: 0.29s; }
        .nav-link-custom:nth-child(5) { animation-delay: 0.36s; }

        /* Active nav link gets a left accent bar */
        .nav-link-custom.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 20%;
            height: 60%;
            width: 3px;
            border-radius: 0 3px 3px 0;
            background: var(--primary-color);
            animation: fadeSlideIn 0.3s ease both;
        }

        /* Ripple on nav link click */
        .nav-link-custom::after {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle, rgba(var(--primary-rgb), 0.15) 10%, transparent 70%);
            transform: scale(0);
            opacity: 0;
            border-radius: inherit;
            transition: transform 0.5s, opacity 0.5s;
        }

        .nav-link-custom:active::after {
            transform: scale(2.5);
            opacity: 1;
            transition: 0s;
        }

        /* Icon micro-bounce on hover */
        .nav-link-custom:hover i,
        .nav-link-custom.active i {
            animation: badgePop 0.3s cubic-bezier(0.34, 1.56, 0.64, 1) both;
        }

        /* Sidebar user card fade-up */
        .sidebar .mt-auto > div {
            animation: fadeSlideUp 0.5s 0.4s cubic-bezier(0.16, 1, 0.3, 1) both;
        }


        /* ── 3. TOPBAR ────────────────────────────────────────────── */

        .topbar {
            animation: topbarSlideDown 0.45s cubic-bezier(0.16, 1, 0.3, 1) both;
            position: relative;
        }

        /* Topbar subtle shimmer border on hover */
        .topbar::after {
            content: '';
            position: absolute;
            inset: -1px;
            border-radius: 21px;
            background: linear-gradient(90deg,
                transparent 0%,
                rgba(var(--primary-rgb), 0.25) 50%,
                transparent 100%
            );
            background-size: 300% 100%;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.4s;
            z-index: -1;
        }

        .topbar:hover::after {
            opacity: 1;
            animation: shimmer 2s linear infinite;
        }

        /* Search bar focus pulse */
        .search-bar-luxury:focus-within {
            animation: pulse-glow 1.8s ease-in-out 1;
        }

        /* Icon buttons: springy hover */
        .icon-btn-luxury {
            transition: var(--transition), transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .icon-btn-luxury:hover {
            transform: scale(1.1) rotate(-5deg);
        }

        .icon-btn-luxury:active {
            transform: scale(0.92);
        }

        /* Notification badge pop */
        .icon-btn-luxury .position-absolute {
            animation: badgePop 0.4s cubic-bezier(0.34, 1.56, 0.64, 1) 0.6s both;
        }


        /* ── 4. CARDS ─────────────────────────────────────────────── */

        /* Cards enter with stagger — apply .anim-card class to your card elements,
           or use the nth-child auto-stagger below */
        .card,
        [class*="card-"] {
            animation: cardEntrance 0.45s cubic-bezier(0.16, 1, 0.3, 1) both;
            transition: var(--transition), box-shadow 0.25s, transform 0.25s;
        }

        /* Stagger up to 8 cards on the same page */
        .card:nth-child(1), [class*="card-"]:nth-child(1) { animation-delay: 0.05s; }
        .card:nth-child(2), [class*="card-"]:nth-child(2) { animation-delay: 0.10s; }
        .card:nth-child(3), [class*="card-"]:nth-child(3) { animation-delay: 0.15s; }
        .card:nth-child(4), [class*="card-"]:nth-child(4) { animation-delay: 0.20s; }
        .card:nth-child(5), [class*="card-"]:nth-child(5) { animation-delay: 0.25s; }
        .card:nth-child(6), [class*="card-"]:nth-child(6) { animation-delay: 0.30s; }
        .card:nth-child(7), [class*="card-"]:nth-child(7) { animation-delay: 0.35s; }
        .card:nth-child(8), [class*="card-"]:nth-child(8) { animation-delay: 0.40s; }

        .card:hover,
        [class*="card-"]:hover {
            transform: translateY(-4px) scale(1.01);
            box-shadow: 0 16px 40px -8px rgba(var(--primary-rgb), 0.18);
        }

        .card:active,
        [class*="card-"]:active {
            transform: translateY(-1px) scale(0.99);
        }


        /* ── 5. BUTTONS ───────────────────────────────────────────── */

        /* Ripple wrapper — add class `btn-ripple` to any button for the effect */
        .btn-ripple {
            position: relative;
            overflow: hidden;
        }

        .btn-ripple .ripple-circle {
            position: absolute;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.35);
            transform: scale(0);
            animation: ripple 0.6s linear;
            pointer-events: none;
            margin: -20px 0 0 -20px;
        }

        /* Primary button: shimmer sweep on hover */
        .btn-primary {
            position: relative;
            overflow: hidden;
            transition: var(--transition), transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0; left: -100%;
            width: 60%;
            height: 100%;
            background: linear-gradient(120deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s ease;
        }

        .btn-primary:hover::before { left: 150%; }

        .btn-primary:active {
            transform: scale(0.96) translateY(1px) !important;
        }

        /* Secondary / outline buttons */
        .btn-outline-primary,
        .btn-outline-secondary {
            transition: var(--transition), transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .btn-outline-primary:hover,
        .btn-outline-secondary:hover {
            transform: translateY(-2px);
        }

        .btn-outline-primary:active,
        .btn-outline-secondary:active {
            transform: scale(0.96);
        }


        /* ── 6. LOADING SKELETON ──────────────────────────────────── */

        .skeleton {
            border-radius: 10px;
            background: linear-gradient(
                90deg,
                var(--border-color) 25%,
                rgba(var(--primary-rgb), 0.08) 50%,
                var(--border-color) 75%
            );
            background-size: 600px 100%;
            animation: shimmer 1.6s infinite linear;
        }

        .skeleton-text  { height: 14px; width: 80%; margin-bottom: 10px; }
        .skeleton-title { height: 22px; width: 55%; margin-bottom: 14px; }
        .skeleton-thumb { height: 180px; width: 100%; border-radius: 16px; }
        .skeleton-avatar {
            width: 42px; height: 42px;
            border-radius: 50%;
            flex-shrink: 0;
        }
        .skeleton-badge { height: 28px; width: 80px; border-radius: 50px; }

        /* Full-screen page loader overlay */
        .page-loader {
            position: fixed;
            inset: 0;
            background: var(--bg-color);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 18px;
            z-index: 9999;
            transition: opacity 0.5s, visibility 0.5s;
        }

        .page-loader.hidden {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }

        .loader-spinner {
            width: 44px;
            height: 44px;
            border: 3px solid var(--border-color);
            border-top-color: var(--primary-color);
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        .loader-dots {
            display: flex;
            gap: 8px;
        }

        .loader-dots span {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--primary-color);
            animation: pulse-glow 1.2s ease-in-out infinite;
        }

        .loader-dots span:nth-child(2) { animation-delay: 0.2s; }
        .loader-dots span:nth-child(3) { animation-delay: 0.4s; }


        /* ── 7. TABS ──────────────────────────────────────────────── */

        .nav-tabs {
            position: relative;
            border-bottom: 2px solid var(--border-color);
        }

        .nav-tabs .nav-link {
            color: var(--text-muted);
            font-weight: 600;
            border: none;
            border-bottom: 2px solid transparent;
            margin-bottom: -2px;
            padding: 10px 20px;
            border-radius: 0;
            transition: color 0.25s, border-color 0.25s;
        }

        .nav-tabs .nav-link:hover {
            color: var(--primary-color);
        }

        .nav-tabs .nav-link.active {
            color: var(--primary-color);
            background: transparent;
            border-bottom-color: var(--primary-color);
            animation: tabIndicatorSlide 0.25s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        .nav-pills .nav-link {
            color: var(--text-muted);
            font-weight: 600;
            border-radius: 12px;
            transition: var(--transition);
        }

        .nav-pills .nav-link:hover {
            background: var(--sidebar-hover);
            color: var(--primary-color);
        }

        .nav-pills .nav-link.active {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: #fff;
            animation: selectPop 0.3s cubic-bezier(0.34, 1.56, 0.64, 1) both;
            box-shadow: 0 4px 14px rgba(var(--primary-rgb), 0.3);
        }

        .tab-content .tab-pane.active {
            animation: fadeSlideUp 0.35s cubic-bezier(0.16, 1, 0.3, 1) both;
        }


        /* ── 8. SELECTION / CHECKBOXES / RADIOS ───────────────────── */

        .form-check-input {
            transition: var(--transition), transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            transform: scale(1.15);
            box-shadow: 0 0 0 4px rgba(var(--primary-rgb), 0.15);
        }

        .form-check-input:focus {
            box-shadow: 0 0 0 4px rgba(var(--primary-rgb), 0.15);
            border-color: var(--primary-color);
        }

        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(var(--primary-rgb), 0.1);
            animation: selectPop 0.25s cubic-bezier(0.34, 1.56, 0.64, 1) both;
        }


        /* ── 9. BOTTOM NAV (mobile) ───────────────────────────────── */

        .bottom-nav {
            animation: bottomNavIn 0.5s 0.3s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        .bottom-link {
            transition: var(--transition), transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .bottom-link:hover {
            transform: translateY(-4px);
        }

        .bottom-link.active {
            animation: badgePop 0.35s cubic-bezier(0.34, 1.56, 0.64, 1) both;
        }


        /* ── 10. TOASTS ───────────────────────────────────────────── */

        @keyframes slideInToast {
            from { transform: translateX(110%) scale(0.9); opacity: 0; }
            to   { transform: translateX(0) scale(1); opacity: 1; }
        }

        .toast-premium {
            animation: slideInToast 0.4s cubic-bezier(0.23, 1, 0.32, 1) both;
            transition: opacity 0.3s, transform 0.3s;
        }


        /* ── 11. MODAL ENTRANCE ───────────────────────────────────── */

        .modal.show .modal-dialog {
            animation: fadeSlideUp 0.35s cubic-bezier(0.16, 1, 0.3, 1) both;
        }


        /* ── 12. FORM INPUTS ──────────────────────────────────────── */

        .form-control:focus {
            animation: pulse-glow 1.5s ease-in-out 1;
        }

        /* Label float effect: wrap input + label in .float-label-group */
        .float-label-group {
            position: relative;
        }

        .float-label-group label {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 0.875rem;
            font-weight: 500;
            pointer-events: none;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            background: var(--card-bg);
            padding: 0 4px;
        }

        .float-label-group input:focus ~ label,
        .float-label-group input:not(:placeholder-shown) ~ label {
            top: 0;
            font-size: 0.72rem;
            color: var(--primary-color);
            transform: translateY(-50%);
        }


        /* ── 13. PAGE CONTENT STAGGER ─────────────────────────────── */

        .page-enter > * {
            animation: fadeSlideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        .page-enter > *:nth-child(1) { animation-delay: 0.0s; }
        .page-enter > *:nth-child(2) { animation-delay: 0.06s; }
        .page-enter > *:nth-child(3) { animation-delay: 0.12s; }
        .page-enter > *:nth-child(4) { animation-delay: 0.18s; }
        .page-enter > *:nth-child(5) { animation-delay: 0.24s; }
        .page-enter > *:nth-child(6) { animation-delay: 0.30s; }


        /* ── 14. THEME TRANSITION ─────────────────────────────────── */

        *, *::before, *::after {
            transition:
                background-color 0.35s cubic-bezier(0.16, 1, 0.3, 1),
                border-color     0.35s cubic-bezier(0.16, 1, 0.3, 1),
                color            0.25s ease,
                box-shadow       0.25s ease;
        }

        .btn, .nav-link-custom, .card, .icon-btn-luxury, .bottom-link {
            transition: var(--transition),
                transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1),
                box-shadow 0.25s,
                background-color 0.35s,
                border-color 0.35s,
                color 0.25s;
        }

        /* ── Fix: Modal stacking context ─────────────────────────────── */
        /* The .page-enter > * animation rule creates a CSS stacking context  */
        /* on every child, trapping modals below the backdrop. These rules    */
        /* reset the stacking context for Bootstrap modals so they always     */
        /* render at the root level above all other content.                  */
        .modal,
        .modal-backdrop {
            animation: none !important;
        }
        .modal {
            isolation: auto !important;
        }
    </style>
    @stack('styles')
</head>
<body data-bs-theme="light">

    <!-- Sidebar (Desktop) -->
    <aside class="sidebar">
        <a href="{{ route('dashboard') }}" class="logo-text d-flex align-items-center gap-2">
            <img src="{{ asset('assets/logo/sponlogoonly.png') }}" alt="Logo" style="width:32px;height:32px;object-fit:contain">
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
                    <img src="{{ auth()->user()->avatar_path ? asset('storage/' . auth()->user()->avatar_path) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=6366f1&color=fff' }}" class="rounded-circle shadow-sm object-fit-cover" width="40" height="40">
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
            <a href="{{ route('dashboard') }}" class="logo-text d-lg-none">
                <img src="{{ asset('assets/logo/sponlogoonly.png') }}" alt="S" style="width:30px;height:30px;object-fit:contain">
            </a>
            
            <form action="{{ route('search') }}" method="GET" class="search-bar-luxury d-none d-md-flex">
                <i data-lucide="search" size="18" class="text-muted"></i>
                <input type="text" name="q" placeholder="Search assignment, class, or people..." value="{{ request('q') }}" autocomplete="off">
            </form>
            
            <div class="d-flex align-items-center gap-3">
                <button class="icon-btn-luxury" onclick="toggleTheme()" title="Toggle Theme">
                    <i data-lucide="moon" id="theme-icon"></i>
                </button>
                <div class="dropdown d-none"> <!-- Hidden old dropdown trigger -->
                    <button class="icon-btn-luxury position-relative" data-bs-toggle="dropdown" aria-expanded="false">
                        <i data-lucide="bell" size="20"></i>
                    </button>
                </div>

                <button class="icon-btn-luxury position-relative" type="button" data-bs-toggle="offcanvas" data-bs-target="#notificationSidebar" aria-controls="notificationSidebar">
                    <i data-lucide="bell" size="20"></i>
                    <span id="unread-notification-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-2 border-white p-1 {{ auth()->user()->unreadNotifications->count() > 0 ? '' : 'd-none' }}" style="font-size: 0.6rem;">
                        {{ auth()->user()->unreadNotifications->count() }}
                    </span>
                </button>
            </div>
        </header>

        <main class="page-enter">
            @yield('content')
        </main>
    </div>

    <!-- Body-level modals: rendered here so they sit outside .page-enter animation stacking context -->
    @stack('body_modals')

    <!-- Notification Sidebar (Offcanvas) -->
    <div class="offcanvas offcanvas-end border-0 shadow-lg" tabindex="-1" id="notificationSidebar" aria-labelledby="notificationSidebarLabel" style="width: 400px; background: var(--bg-color);">
        <div class="offcanvas-header bg-primary text-white p-4">
            <h5 class="offcanvas-title fw-extrabold" id="notificationSidebarLabel">Notifications</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-0">
            <div class="p-4 border-bottom bg-light-subtle d-flex justify-content-between align-items-center">
                <span id="offcanvas-unread-count" class="smaller fw-bold text-muted">{{ auth()->user()->unreadNotifications->count() }} Unread</span>
                @if(auth()->user()->unreadNotifications->count() > 0)
                    <form action="{{ route('notifications.read-all') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-link btn-sm text-primary text-decoration-none p-0 smallest fw-bold">Mark all as read</button>
                    </form>
                @endif
            </div>

            <div class="notification-scroll-area" style="height: calc(100vh - 160px); overflow-y: auto;">
                @forelse(auth()->user()->notifications()->latest()->take(20)->get() as $notification)
                    <a href="{{ route('courses.show', $notification->data['classroom_id']) }}" class="text-decoration-none block p-4 border-bottom hover-bg-light transition-all d-block @if($notification->unread()) bg-primary-soft @endif">
                        <div class="d-flex gap-3">
                            <div class="rounded-circle bg-primary-soft text-primary p-2 flex-shrink-0" style="width: 45px; height: 45px; display: grid; place-items: center;">
                                @php
                                    $icon = match($notification->data['type'] ?? '') {
                                        'assignment' => 'file-text',
                                        'material' => 'book-open',
                                        'announcement' => 'megaphone',
                                        default => 'bell'
                                    };
                                @endphp
                                <i data-lucide="{{ $icon }}" size="20"></i>
                            </div>
                            <div class="overflow-hidden">
                                <div class="smaller fw-extrabold text-main mb-1">{{ $notification->data['message'] }}</div>
                                <div class="smallest text-muted d-flex align-items-center gap-2 mt-1">
                                    <span class="badge bg-primary-soft text-primary rounded-pill px-2">{{ $notification->data['type'] }}</span>
                                    <span>{{ $notification->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="smallest text-primary fw-bold mt-2 d-flex align-items-center gap-1">
                                    <i data-lucide="layers" size="12"></i>
                                    {{ $notification->data['classroom_name'] }}
                                </div>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="p-5 text-center mt-5">
                        <div class="bg-light-subtle rounded-circle d-inline-flex p-4 mb-4">
                            <i data-lucide="bell-off" class="text-muted opacity-25" size="64"></i>
                        </div>
                        <h5 class="fw-bold text-muted">All caught up!</h5>
                        <p class="text-muted small">No new notifications at the moment.</p>
                    </div>
                @endforelse
            </div>
        </div>
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
                        <button type="button" class="btn btn-danger py-3 fw-bold rounded-4 btn-ripple" id="confirmActionBtn" onclick="addRipple(event, this)">Confirm</button>
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

        // Ripple Effect
        function addRipple(e, btn) {
            const circle = document.createElement('span');
            circle.className = 'ripple-circle';
            const rect = btn.getBoundingClientRect();
            circle.style.left = (e.clientX - rect.left) + 'px';
            circle.style.top  = (e.clientY - rect.top)  + 'px';
            btn.appendChild(circle);
            circle.addEventListener('animationend', () => circle.remove());
        }

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
            if(!icon) return;
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
            btn.className = `btn py-3 fw-bold rounded-4 btn-ripple ${btnClass || 'btn-danger'}`;
            
            btn.onclick = (e) => {
                addRipple(e, btn);
                onConfirm();
                setTimeout(() => modal.hide(), 200);
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

        // Page Entrance Animation - Children stagger automatically
        window.addEventListener('DOMContentLoaded', () => {
            const page = document.querySelector('.page-enter');
            if(page) page.classList.add('active'); // CSS doesn't strictly need .active based on provided CSS but good practice
        });

        // Flash Messages
        @if(session('success')) showToast("{{ session('success') }}", 'success'); @endif
        @if(session('error')) showToast("{{ session('error') }}", 'error'); @endif

        // Real-time Notification Polling (SSE Alternative)
        (function() {
            let lastFirstNotificationId = null;
            
            function pollNotifications() {
                fetch('{{ route("notifications.unread") }}')
                    .then(res => {
                        if (!res.ok) throw new Error('Unauthenticated');
                        return res.json();
                    })
                    .then(data => {
                        if (data.error) return;
                        
                        // Update header count badge
                        const badge = document.getElementById('unread-notification-badge');
                        if (badge) {
                            if (data.unread_count > 0) {
                                badge.textContent = data.unread_count;
                                badge.classList.remove('d-none');
                            } else {
                                badge.classList.add('d-none');
                            }
                        }

                        // Update offcanvas unread header count
                        const offcanvasCount = document.getElementById('offcanvas-unread-count');
                        if (offcanvasCount) {
                            offcanvasCount.textContent = data.unread_count + ' Unread';
                        }

                        // Update notification list container
                        const scrollArea = document.querySelector('.notification-scroll-area');
                        if (scrollArea && data.notifications) {
                            // Check if a new notification is received
                            const currentFirst = data.notifications[0];
                            if (currentFirst && lastFirstNotificationId !== null && currentFirst.id !== lastFirstNotificationId) {
                                if (typeof showToast === 'function') {
                                    showToast(currentFirst.message, 'info');
                                }
                            }
                            if (currentFirst) {
                                lastFirstNotificationId = currentFirst.id;
                            } else {
                                lastFirstNotificationId = '';
                            }

                            if (data.notifications.length === 0) {
                                scrollArea.innerHTML = `
                                    <div class="p-5 text-center mt-5">
                                        <div class="bg-light-subtle rounded-circle d-inline-flex p-4 mb-4">
                                            <i data-lucide="bell-off" class="text-muted opacity-25" size="64"></i>
                                        </div>
                                        <h5 class="fw-bold text-muted">All caught up!</h5>
                                        <p class="text-muted small">No new notifications at the moment.</p>
                                    </div>`;
                            } else {
                                let html = '';
                                data.notifications.forEach(n => {
                                    const unreadClass = n.unread ? 'bg-primary-soft' : '';
                                    html += `
                                        <a href="/classes/${n.classroom_id}" class="text-decoration-none block p-4 border-bottom hover-bg-light transition-all d-block ${unreadClass}">
                                            <div class="d-flex gap-3">
                                                <div class="rounded-circle bg-primary-soft text-primary p-2 flex-shrink-0" style="width: 45px; height: 45px; display: grid; place-items: center;">
                                                    <i data-lucide="${n.icon}" size="20"></i>
                                                </div>
                                                <div class="overflow-hidden w-100">
                                                    <div class="smaller fw-extrabold text-main mb-1">${n.message}</div>
                                                    <div class="smallest text-muted d-flex align-items-center gap-2 mt-1">
                                                        <span class="badge bg-primary-soft text-primary rounded-pill px-2">${n.type}</span>
                                                        <span>${n.created_at_human}</span>
                                                    </div>
                                                    <div class="smallest text-primary fw-bold mt-2 d-flex align-items-center gap-1">
                                                        <i data-lucide="layers" size="12"></i>
                                                        ${n.classroom_name}
                                                    </div>
                                                </div>
                                            </div>
                                        </a>`;
                                });
                                scrollArea.innerHTML = html;
                            }
                            if (typeof lucide !== 'undefined') {
                                lucide.createIcons();
                            }
                        }
                    })
                    .catch(err => {
                        // Silent fail
                    });
            }

            // Set initial first ID on load
            const firstLink = document.querySelector('.notification-scroll-area a');
            if (firstLink) {
                // Parse or extract ID from link/attributes if needed, but fetching initial payload is safer
            }
            
            // Wait slightly for first load, then start polling
            setTimeout(() => {
                pollNotifications();
                // Set interval to 7 seconds
                setInterval(pollNotifications, 7000);
            }, 1000);
        })();
    </script>
    @stack('scripts')
</body>
</html>
