<!-- Create Class Modal -->
<div class="modal fade" id="createClassModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content overflow-hidden">
            <div class="modal-header border-0 px-5 pt-5 pb-0">
                <h3 class="modal-title fw-extrabold text-main">Create A Space</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('courses.store') }}" method="POST">
                @csrf
                <div class="modal-body p-5">
                    <p class="text-muted small fw-bold mb-4">Set up a new learning environment for your students.</p>
                    
                    <div class="mb-4">
                        <label class="form-label">Classroom Title</label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. Advanced Calculus II" required autofocus>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">Description (Optional)</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Brief info about the class mission and goals..."></textarea>
                    </div>
                    
                    <div class="mb-0">
                        <label class="form-label">Search Tags</label>
                        <div class="input-group luxury-input-group">
                            <span class="input-group-text border-0 bg-transparent ps-3"><i data-lucide="tag" size="18" class="text-muted"></i></span>
                            <input type="text" name="tags" class="form-control border-0 bg-transparent ps-2 py-3" placeholder="e.g. Math, STEM, Grade-11">
                        </div>
                        <div class="form-text smaller ps-2">Separate tags with commas.</div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-5 pb-5 pt-0">
                    <button type="button" class="btn btn-luxury-light rounded-pill px-4 fw-bold btn-ripple" data-bs-dismiss="modal" onclick="addRipple(event, this)">Discard</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow btn-ripple" onclick="addRipple(event, this)">Deploy Classroom</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Join Class Modal -->
<div class="modal fade" id="joinClassModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content overflow-hidden">
             <div class="modal-header border-0 px-5 pt-5 pb-0">
                <h3 class="modal-title fw-extrabold text-main">Join A Space</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('courses.join') }}" method="POST">
                @csrf
                <div class="modal-body p-5 text-center">
                    <div class="mb-5 position-relative d-inline-block">
                        <div class="p-4 bg-primary-soft rounded-circle text-primary shadow-sm">
                            <i data-lucide="key" size="48"></i>
                        </div>
                        <div class="position-absolute top-100 start-50 translate-middle-x mt-3">
                             <div class="pulse-indicator"></div>
                        </div>
                    </div>
                    
                    <h5 class="fw-bold mb-3">Entrance Code</h5>
                    <p class="text-muted mb-4 small px-4">Paste the unique 7-character access code provided by your teacher to enter the classroom.</p>
                    
                    <div class="luxury-code-input-wrapper mb-2">
                        <input type="text" name="code" class="form-control text-center fw-extrabold ls-3 border-0 bg-transparent py-4 text-uppercase" placeholder="XXXXXXX" required maxlength="7" autocomplete="off">
                    </div>
                </div>
                <div class="modal-footer border-0 px-5 pb-5 pt-2 justify-content-center">
                    <button type="submit" class="btn btn-primary rounded-pill px-5 py-3 fw-bold shadow w-100 mb-2 btn-ripple" onclick="addRipple(event, this)">Initialize Entry</button>
                    <button type="button" class="btn btn-link text-decoration-none text-muted smaller fw-bold btn-ripple" data-bs-dismiss="modal" onclick="addRipple(event, this)">Cancel Request</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .ls-3 { letter-spacing: 0.3em; font-size: 1.5rem; }
    .luxury-input-group { background: var(--bg-color); border: 1.5px solid var(--border-color); border-radius: 14px; transition: var(--transition); }
    .luxury-input-group:focus-within { border-color: var(--primary-color); background: var(--card-bg); box-shadow: 0 4px 12px rgba(0,0,0,0.03); }
    .luxury-code-input-wrapper { background: var(--bg-color); border: 2px dashed var(--primary-color); border-radius: 20px; transition: all 0.3s; }
    .luxury-code-input-wrapper:focus-within { border-style: solid; background: #fff; transform: scale(1.02); box-shadow: 0 10px 30px rgba(var(--primary-rgb), 0.1); }
    .pulse-indicator { width: 8px; height: 8px; background: var(--primary-color); border-radius: 50%; box-shadow: 0 0 0 0 rgba(var(--primary-rgb), 0.7); animation: pulse-key 2s infinite; }
    @keyframes pulse-key { 0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(var(--primary-rgb), 0.7); } 70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(var(--primary-rgb), 0); } 100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(var(--primary-rgb), 0); } }
</style>
