@extends('layouts.guest')

@section('content')
    <h4 class="auth-title">Create Account</h4>
    
    @if($errors->any())
        <div class="alert alert-danger border-0 rounded-4 mb-4 text-center small fw-bold text-danger bg-danger-subtle">
            {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('register') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="name" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" placeholder="John Doe" required autofocus>
            </div>
            <div class="col-md-6 mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" value="{{ old('username') }}" placeholder="john_doe" required>
            </div>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email Address</label>
            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" placeholder="john@example.com" required>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="password" class="form-label">Password</label>
                <div class="password-wrapper">
                    <input type="password" class="form-control" id="password" name="password" placeholder="••••••••" required>
                    <span class="toggle-password" onclick="togglePassword('password')">
                        <i data-lucide="eye" style="width:20px;height:20px"></i>
                    </span>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <label for="password_confirmation" class="form-label">Confirm</label>
                <div class="password-wrapper">
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="••••••••" required>
                    <span class="toggle-password" onclick="togglePassword('password_confirmation')">
                        <i data-lucide="eye" style="width:20px;height:20px"></i>
                    </span>
                </div>
            </div>
        </div>


        <style>
            .form-check-input:checked {
                background-color: #764ba2 !important;
                border-color: #764ba2 !important;
            }
            .form-check-input:focus {
                border-color: #764ba2 !important;
                box-shadow: 0 0 0 4px rgba(118, 75, 162, 0.25) !important;
            }
        </style>

        <div class="mb-4 text-start">
            <div class="form-check d-flex align-items-center gap-2">
                <input class="form-check-input mt-0" type="checkbox" name="terms" id="terms" required style="cursor: pointer; width: 1.25rem; height: 1.25rem; border: 1.5px solid #cbd5e0; border-radius: 6px; transition: all 0.2s;">
                <label class="form-check-label small text-muted fw-medium mb-0" for="terms" style="cursor: pointer; user-select: none; line-height: 1.2;">
                    I agree to the <a href="#" class="text-decoration-none fw-bold" style="color: #667eea;">Terms of Service</a>
                </label>
            </div>
        </div>

        <button type="submit" class="btn btn-primary w-100 mb-4 py-3 shadow-lg">Start Learning Now</button>
        
        <p class="text-center small text-muted mb-0 fw-medium">
            Already have an account? <a href="{{ route('login') }}" class="text-decoration-none fw-extrabold" style="color: #764ba2;">Sign In</a>
        </p>
    </form>
@endsection
