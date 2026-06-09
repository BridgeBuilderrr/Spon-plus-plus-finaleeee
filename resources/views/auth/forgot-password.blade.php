@extends('layouts.guest')

@section('content')
    <h4 class="auth-title">Reset Password</h4>
    <p class="text-center small text-muted mb-4 px-2">
        Enter your email address and we'll send you a 6-digit OTP code to verify your request.
    </p>

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

    <form action="{{ route('password.sendOtp') }}" method="POST">
        @csrf
        <div class="mb-5">
            <label for="email" class="form-label">Email Address</label>
            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', session('otp_email')) }}" placeholder="john@example.com" required autofocus>
        </div>

        <button type="submit" class="btn btn-primary w-100 mb-4 py-3 shadow-lg">Send Password Reset Link</button>
        
        <p class="text-center small text-muted mb-0 fw-medium">
            Remembered your password? <a href="{{ route('login') }}" class="text-decoration-none fw-extrabold" style="color: #764ba2;">Sign In</a>
        </p>
    </form>
@endsection
