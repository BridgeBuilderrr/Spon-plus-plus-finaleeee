@extends('layouts.guest')

@section('content')
    <h4 class="auth-title">Verify OTP</h4>
    <p class="text-center small text-muted mb-4 px-2">
        An OTP code has been sent to <strong>{{ session('otp_email') }}</strong>. Please enter the code below to proceed.
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

    <form action="{{ route('password.verifyOtp.post') }}" method="POST">
        @csrf
        <div class="mb-5">
            <label for="otp" class="form-label">OTP Code</label>
            <input type="text" class="form-control text-center fs-4 fw-bold @error('otp') is-invalid @enderror" id="otp" name="otp" maxlength="6" placeholder="••••••" style="letter-spacing: 0.5rem;" required autofocus>
            @error('otp')
                <div class="invalid-feedback text-center fw-bold mt-2">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary w-100 mb-4 py-3 shadow-lg">Verify OTP Code</button>
        
    </form>

    <form id="resend-otp-form" action="{{ route('password.resendOtp') }}" method="POST" style="display: none;">
        @csrf
    </form>
    
    <p class="text-center small text-muted mb-0 fw-medium">
        Didn't receive code? <a href="#" onclick="event.preventDefault(); document.getElementById('resend-otp-form').submit();" class="text-decoration-none fw-extrabold" style="color: #764ba2;">Resend OTP</a>
    </p>
@endsection
