@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Luxury Profile Header -->
            <div class="card border-0 shadow-sm rounded-5 overflow-hidden mb-5 luxury-profile-card">
                <!-- Banner Section -->
                <div class="profile-banner-container position-relative" style="height: 300px; background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));">
                    <img id="current-banner" 
                         src="{{ auth()->user()->profile_banner ? asset('storage/' . auth()->user()->profile_banner) : 'https://images.unsplash.com/photo-1579546929518-9e396f3cc809?auto=format&fit=crop&w=1500&q=80' }}" 
                         class="w-100 h-100 object-fit-cover {{ auth()->user()->profile_banner ? '' : 'opacity-40' }}" 
                         alt="Profile Banner">
                    
                    <div class="banner-glass-bottom p-4 d-flex justify-content-end align-items-end">
                        <button class="btn btn-glass-luxury rounded-pill px-4 shadow-lg" onclick="document.getElementById('banner-input').click()">
                            <i data-lucide="camera" size="18" class="me-2"></i> Update Cover
                        </button>
                        <input type="file" id="banner-input" class="d-none" accept="image/*">
                    </div>
                </div>

                <div class="px-5 pb-5 position-relative">
                    <div class="row align-items-end">
                        <div class="col-auto">
                            <!-- Premium Avatar -->
                            <div class="profile-avatar-wrapper shadow-lg rounded-circle p-1 bg-white" style="margin-top: -75px;">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&size=150&background=6366f1&color=fff" 
                                     class="rounded-circle border border-4 border-white" width="150" height="150" alt="{{ auth()->user()->name }}">
                                <button class="btn btn-primary rounded-circle p-2 position-absolute shadow-sm" style="bottom: 10px; right: 10px;">
                                    <i data-lucide="edit-3" size="16"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col pt-4 pt-md-0">
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                                <div>
                                    <h1 class="fw-extrabold mb-1 display-6">{{ auth()->user()->name }}</h1>
                                    <div class="text-primary fw-bold d-flex align-items-center gap-2">
                                        <i data-lucide="at-sign" size="16"></i>{{ auth()->user()->username }}
                                        <span class="badge rounded-pill bg-primary-soft text-primary px-3 py-1 ms-2" style="font-size: 0.7rem;">{{ auth()->user()->role }}</span>
                                    </div>
                                </div>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                                        <i data-lucide="settings" size="18" class="me-2"></i> Edit Preferences
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Profile Bio & Stats -->
                    <div class="row mt-5 g-4">
                        <div class="col-lg-8">
                            <h5 class="fw-extrabold mb-3 text-main">About Me</h5>
                            <p class="text-muted mb-4 lead font-jakarta" style="line-height: 1.6;">
                                {{ auth()->user()->bio ?? "This user hasn't shared a bio yet. Stay tuned to learn more about their learning journey or teaching passion!" }}
                            </p>

                            <div class="d-flex flex-wrap gap-4 pt-3">
                                <div class="d-flex align-items-center gap-2 px-3 py-2 bg-light-subtle rounded-pill border">
                                    <i data-lucide="calendar" size="16" class="text-primary"></i>
                                    <span class="smaller fw-bold text-muted">Member since {{ auth()->user()->created_at->format('M Y') }}</span>
                                </div>
                                @if(auth()->user()->email)
                                <div class="d-flex align-items-center gap-2 px-3 py-2 bg-light-subtle rounded-pill border">
                                    <i data-lucide="mail" size="16" class="text-primary"></i>
                                    <span class="smaller fw-bold text-muted">{{ auth()->user()->email }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="bg-light-subtle rounded-5 p-4 border border-dashed">
                                <h6 class="fw-bold mb-4 text-center">Learning Statistics</h6>
                                <div class="row g-3 text-center">
                                    <div class="col-6">
                                        <div class="h3 fw-extrabold text-primary mb-0">12</div>
                                        <div class="smaller text-muted fw-bold">COURSES</div>
                                    </div>
                                    <div class="col-6">
                                        <div class="h3 fw-extrabold text-secondary mb-0">84%</div>
                                        <div class="smaller text-muted fw-bold">AVRG SCORE</div>
                                    </div>
                                    <div class="col-12 pt-3">
                                        <div class="h3 fw-extrabold text-success mb-0">24</div>
                                        <div class="smaller text-muted fw-bold">BADGES EARNED</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile Nav Tabs -->
            <div class="card border-0 shadow-sm rounded-5 p-5 mb-5" style="background: var(--card-bg);">
                <ul class="nav nav-pills mb-5 gap-3 justify-content-center luxury-pills" id="profileTab" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active rounded-pill px-5 py-2 fw-bold" data-bs-toggle="pill" data-bs-target="#pills-activity" type="button">
                            <i data-lucide="zap" size="18" class="me-2"></i> Recent Activity
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link rounded-pill px-5 py-2 fw-bold" data-bs-toggle="pill" data-bs-target="#pills-certs" type="button">
                            <i data-lucide="award" size="18" class="me-2"></i> Achievements
                        </button>
                    </li>
                </ul>
                
                <div class="tab-content" id="profileTabContent">
                    <div class="tab-pane fade show active text-center py-5" id="pills-activity">
                        <div class="empty-state-luxury py-4">
                            <i data-lucide="box" size="54" class="text-muted opacity-25 mb-4"></i>
                            <h5 class="fw-bold text-muted">No recent activity logged</h5>
                            <p class="text-muted small">Your posts, comments, and submissions will appear here.</p>
                        </div>
                    </div>
                    <div class="tab-pane fade text-center py-5" id="pills-certs">
                         <div class="empty-state-luxury py-4">
                            <i data-lucide="medal" size="54" class="text-muted opacity-25 mb-4"></i>
                            <h5 class="fw-bold text-muted">No achievements yet</h5>
                            <p class="text-muted small">Complete assignments and participate in classes to earn Many rewards.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals (Edit Profile & Crop) -->
    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content overflow-hidden">
                <div class="modal-header border-0 p-5 pb-2">
                    <h3 class="fw-extrabold text-main m-0">Edit Profile</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    <div class="modal-body p-5 pt-3">
                        <div class="mb-4">
                            <label class="form-label">Display Name</label>
                            <input type="text" name="name" class="form-control" value="{{ auth()->user()->name }}" required>
                        </div>
                        <div class="mb-0">
                            <label class="form-label"> Bio / Description</label>
                            <textarea name="bio" class="form-control" rows="4" placeholder="Share your passions, goals, or expertise...">{{ auth()->user()->bio }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-5 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow">Save Profile Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Banner Crop Modal -->
    <div class="modal fade" id="cropModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content overflow-hidden">
                <div class="modal-header bg-dark text-white p-4">
                    <h5 class="modal-title fw-bold">Maximize Your Profile Cover</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0 bg-black">
                    <img id="crop-image" src="" style="max-width: 100%;">
                </div>
                <div class="modal-footer p-4">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Discard</button>
                    <button type="button" class="btn btn-primary rounded-pill px-5 fw-bold shadow" id="crop-btn">
                        <span id="crop-spinner" class="spinner-border spinner-border-sm d-none me-2"></span>
                        Apply Cover
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let cropper;
            const bannerInput = document.getElementById('banner-input');
            const cropImage = document.getElementById('crop-image');
            const cropModal = new bootstrap.Modal(document.getElementById('cropModal'));
            const cropBtn = document.getElementById('crop-btn');
            const cropSpinner = document.getElementById('crop-spinner');

            bannerInput?.addEventListener('change', function(e) {
                const files = e.target.files;
                if (files && files.length > 0) {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        cropImage.src = event.target.result;
                        cropModal.show();
                    };
                    reader.readAsDataURL(files[0]);
                }
            });

            document.getElementById('cropModal').addEventListener('shown.bs.modal', function() {
                cropper = new Cropper(cropImage, {
                    aspectRatio: 15 / 4,
                    viewMode: 2,
                    guides: true,
                    responsive: true,
                    background: false,
                });
            });

            document.getElementById('cropModal').addEventListener('hidden.bs.modal', function() {
                if (cropper) {
                    cropper.destroy();
                    cropper = null;
                }
                bannerInput.value = '';
            });

            cropBtn?.addEventListener('click', function() {
                if (!cropper) return;
                cropSpinner.classList.remove('d-none');
                cropBtn.disabled = true;

                const canvas = cropper.getCroppedCanvas({ width: 1500, height: 400 });
                const base64Image = canvas.toDataURL('image/png');

                fetch("{{ route('profile.banner') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ banner: base64Image })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showToast('Profile cover updated!', 'success');
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        showToast(data.message, 'error');
                    }
                })
                .finally(() => {
                    cropSpinner.classList.add('d-none');
                    cropBtn.disabled = false;
                });
            });
        });
    </script>

    <style>
        .luxury-profile-card { border: 1.5px solid var(--border-color); background: var(--card-bg); }
        .banner-glass-bottom { position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(0deg, rgba(0,0,0,0.3) 0%, transparent 40%); }
        .profile-avatar-wrapper { position: relative; z-index: 10; width: 158px; transition: transform 0.3s; }
        .profile-avatar-wrapper:hover { transform: scale(1.02); }
        .display-6 { font-weight: 900; letter-spacing: -2px; color: var(--text-color); }
        .bg-primary-soft { background: rgba(var(--primary-rgb), 0.1); }
        .luxury-pills .nav-link { color: var(--text-muted); padding: 12px 30px; border: 1px solid var(--border-color); font-weight: 700; transition: all 0.2s; }
        .luxury-pills .nav-link.active { background: var(--primary-color) !important; color: white !important; border-color: var(--primary-color) !important; box-shadow: 0 10px 20px rgba(var(--primary-rgb), 0.2); }
        .luxury-pills .nav-link:not(.active):hover { border-color: var(--primary-color); color: var(--primary-color); background: var(--sidebar-hover); }
        .border-dashed { border: 2px dashed var(--border-color) !important; }
        .text-main { color: var(--text-color); }
        .font-jakarta { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
@endsection
