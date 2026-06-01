<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Spon++') }}</title>
    
    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            margin: 0;
            letter-spacing: -0.01em;
        }

        .auth-card {
            background: rgba(var(--card-rgb, 255, 255, 255), 0.9);
            backdrop-filter: blur(24px) saturate(200%);
            border: 1px solid rgba(var(--card-rgb, 255, 255, 255), 0.3);
            border-radius: 32px;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
            width: 100%;
            max-width: 480px;
            padding: 48px;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            overflow: hidden;
        }

        @media (prefers-color-scheme: dark) {
            :root {
                --card-rgb: 15, 23, 42;
                --text-main: #f8fafc;
                --text-sec: #94a3b8;
                --border-light: #1e293b;
            }
            body { background: linear-gradient(135deg, #020617 0%, #0f172a 100%); }
            .auth-subtitle { color: var(--text-sec) !important; }
            .auth-title { color: var(--text-main) !important; }
            .form-label { color: var(--text-main) !important; opacity: 0.8; }
            .form-control { background: #020617 !important; border-color: #1e293b !important; color: #fff !important; }
        }

        .auth-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 35px 60px -15px rgba(0,0,0,0.3);
        }

        .brand-logo {
            font-size: 2.75rem;
            font-weight: 800;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-align: center;
            margin-bottom: 8px;
            letter-spacing: -0.05em;
        }

        .auth-subtitle {
            text-align: center;
            color: #4a5568;
            font-weight: 600;
            margin-bottom: 40px;
            font-size: 0.95rem;
            opacity: 0.8;
        }

        .form-label {
            font-weight: 700;
            color: #2d3748;
            font-size: 0.85rem;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .form-control {
            border-radius: 16px;
            padding: 14px 20px;
            border: 1.5px solid #e2e8f0;
            background: #ffffff;
            font-weight: 600;
            transition: all 0.2s;
        }

        .form-control:focus {
            box-shadow: 0 0 0 4px rgba(118, 75, 162, 0.15);
            border-color: #764ba2;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 16px;
            border-radius: 18px;
            font-weight: 800;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            box-shadow: 0 10px 15px -3px rgba(118, 75, 162, 0.3);
        }

        .btn-primary:hover {
            transform: scale(1.02);
            box-shadow: 0 20px 25px -5px rgba(118, 75, 162, 0.4);
            filter: brightness(1.1);
        }

        .auth-title {
            font-weight: 800;
            color: #1a202c;
            margin-bottom: 10px;
            text-align: center;
            font-size: 1.75rem;
            letter-spacing: -0.02em;
        }

        .password-wrapper { position: relative; }
        .toggle-password {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #a0aec0;
            transition: color 0.2s;
            z-index: 10;
        }
        .toggle-password:hover { color: #764ba2; }

        @media (max-width: 576px) {
            .auth-card { padding: 32px 24px; border-radius: 28px; }
            .brand-logo { font-size: 2.25rem; }
        }
    </style>
</head>
<body>
    <div class="auth-card">
        <div class="brand-logo">Spon++</div>
        <p class="auth-subtitle">Empowering the next generation of learners</p>
        @yield('content')
    </div>

    <script>
        lucide.createIcons();

        // Input Normalization Logic
        document.addEventListener('input', function (e) {
            const target = e.target;
            
            // Auto Uppercase for Join Code
            if (target.name === 'join_code' || target.id === 'join_code') {
                target.value = target.value.toUpperCase();
            }

            // Lowercase and sanitize Username
            if (target.id === 'username' || target.name === 'username') {
                target.value = target.value.toLowerCase().replace(/[^a-z0-9_]/g, '');
            }
            
            // Lowercase Email
            if (target.type === 'email' || target.name === 'email' || target.name === 'login') {
                target.value = target.value.toLowerCase();
            }

            // Capitalize Name appropriately (First letter of each word if possible, but let's just keep it simple)
            if (target.id === 'name' || target.name === 'name') {
                target.value = target.value.replace(/[^a-zA-Z\s]/g, '');
            }
        });

        function togglePassword(id) {
            const input = document.getElementById(id);
            const toggle = input.nextElementSibling;
            if (input.type === 'password') {
                input.type = 'text';
                toggle.innerHTML = '<i data-lucide="eye-off" style="width:20px;height:20px"></i>';
            } else {
                input.type = 'password';
                toggle.innerHTML = '<i data-lucide="eye" style="width:20px;height:20px"></i>';
            }
            lucide.createIcons();
        }
    </script>
</body>
</html>
