@extends('layouts.guest')

@section('content')
    <h4 class="auth-title">Set New Password</h4>
    <p class="text-center small text-muted mb-4 px-2">
        Please choose a strong new password below.
    </p>

    @if($errors->any())
        <div class="alert alert-danger border-0 rounded-4 mb-4 text-center small fw-bold text-danger bg-danger-subtle">
            {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('password.update') }}" method="POST">
        @csrf

        <div class="row">
            <div class="col-md-6 mb-4">
                <label for="password" class="form-label">New Password</label>
                <div class="password-wrapper">
                    <input type="password" class="form-control" id="password" name="password" placeholder="••••••••" required autofocus>
                    <span class="toggle-password" onclick="togglePassword('password')">
                        <i data-lucide="eye" style="width:20px;height:20px"></i>
                    </span>
                </div>
            </div>
            <div class="col-md-6 mb-5">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <div class="password-wrapper">
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="••••••••" required>
                    <span class="toggle-password" onclick="togglePassword('password_confirmation')">
                        <i data-lucide="eye" style="width:20px;height:20px"></i>
                    </span>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary w-100 mb-4 py-3 shadow-lg">Reset Password</button>
    </form>
@endsection
