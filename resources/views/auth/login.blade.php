@extends('layouts.guest')

@section('content')
    <h4 class="auth-title">Welcome Back</h4>

    @if(session('success'))
        <div class="alert alert-success border-0 rounded-4 mb-4 text-center small fw-bold">
            {{ session('success') }}
        </div>
    @endif
    
    @if($errors->any())
        <div class="alert alert-danger border-0 rounded-4 mb-4 text-center small fw-bold text-danger bg-danger-subtle">
            {{ $errors->first() }}
        </div>
    @endif

    <form id="login-form" action="{{ route('login') }}" method="POST">
        @csrf
        <div class="mb-4">
            <label for="login_identifier" class="form-label">Email or Username</label>
            <input type="text" class="form-control" id="login_identifier" name="login" value="{{ old('login') }}" placeholder="e.g. john_doe or john@example.com" required autofocus>
        </div>
        <div class="mb-4">
            <label for="password" class="form-label">Password</label>
            <div class="password-wrapper">
                <input type="password" class="form-control" id="password" name="password" placeholder="••••••••" required>
                <span class="toggle-password" onclick="togglePassword('password')">
                    <i data-lucide="eye" style="width:20px;height:20px"></i>
                </span>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-5">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="remember" id="remember_me">
                <label class="form-check-label small fw-bold text-muted" for="remember_me" style="cursor: pointer;">
                    Keep me signed in
                </label>
            </div>
            <a href="#" class="small text-decoration-none fw-bold" style="color: #667eea;">Forgot Access?</a>
        </div>

        <button type="submit" class="btn btn-primary w-100 mb-4 py-3 shadow-lg">Sign In to Dashboard</button>
        
        <p class="text-center small text-muted mb-0 fw-medium">
            New to the platform? <a href="{{ route('register') }}" class="text-decoration-none fw-extrabold" style="color: #764ba2;">Create Account</a>
        </p>
    </form>

    <script>
        // Remember Email Logic
        window.addEventListener('DOMContentLoaded', () => {
            const savedEmail = localStorage.getItem('spon_remembered_email');
            const identifierInput = document.getElementById('login_identifier');
            const rememberCheckbox = document.getElementById('remember_me');

            if (savedEmail && identifierInput) {
                identifierInput.value = savedEmail;
                if (rememberCheckbox) rememberCheckbox.checked = true;
            }
        });

        document.getElementById('login-form')?.addEventListener('submit', () => {
            const identifierInput = document.getElementById('login_identifier');
            const rememberCheckbox = document.getElementById('remember_me');

            if (rememberCheckbox?.checked) {
                localStorage.setItem('spon_remembered_email', identifierInput.value);
            } else {
                localStorage.removeItem('spon_remembered_email');
            }
        });
    </script>
@endsection
