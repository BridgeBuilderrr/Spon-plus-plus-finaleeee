<!-- Assignment & Material Modals -->

<!-- Create Assignment Modal -->
<div class="modal fade" id="createAssignmentModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content overflow-hidden">
            <div class="modal-header border-0 p-5 pb-2">
                <h3 class="fw-extrabold text-main m-0 d-flex align-items-center gap-3">
                    <div class="p-2 bg-primary-soft rounded-3 text-primary">
                        <i data-lucide="file-plus" size="24"></i>
                    </div>
                    Assign New Task
                </h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('assignments.store', $classroom) }}" method="POST" enctype="multipart/form-data" class="upload-box-form">
                @csrf
                <div class="modal-body p-5 pt-3">
                    <div class="mb-4">
                        <label class="form-label">Task Title</label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. Weekly Reflection: Quantum Mechanics" required autofocus>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Detailed Instructions</label>
                        <textarea name="description" id="assignment-editor" class="form-control"></textarea>
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Activation Date (Optional)</label>
                            <div class="input-group luxury-input-group">
                                <span class="input-group-text border-0 bg-transparent ps-3"><i data-lucide="calendar" size="18" class="text-primary"></i></span>
                                <input type="datetime-local" name="open_date" class="form-control border-0 bg-transparent ps-2 py-3 date-min-now">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Submission Deadline</label>
                            <div class="input-group luxury-input-group">
                                <span class="input-group-text border-0 bg-transparent ps-3"><i data-lucide="clock" size="18" class="text-danger"></i></span>
                                <input type="datetime-local" name="due_date" class="form-control border-0 bg-transparent ps-2 py-3 date-min-now" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label">Reference Material (Max 10 files, 20MB/each)</label>
                        <div class="luxury-upload-box p-5 text-center" id="upload-assignment">
                            <input type="file" name="files[]" id="file-assignment" class="d-none" multiple>
                            <label for="file-assignment" class="mb-0 cursor-pointer w-100">
                                <div class="mb-3 text-primary opacity-50 pulse-on-hover">
                                    <i data-lucide="upload-cloud" size="48"></i>
                                </div>
                                <h6 class="fw-bold mb-1">Drag and Drop Files</h6>
                                <p class="text-muted smaller mb-0">or <u>browse your computer</u> to attach documents</p>
                            </label>
                            <div class="upload-progress-list mt-4 text-start"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-5 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Discard</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow">Publish Assignment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Upload Material Modal -->
<div class="modal fade" id="uploadMaterialModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content overflow-hidden">
            <div class="modal-header border-0 p-5 pb-2">
                <h3 class="fw-extrabold text-main m-0 d-flex align-items-center gap-3">
                    <div class="p-2 bg-success-soft rounded-3 text-success">
                        <i data-lucide="book-copy" size="24"></i>
                    </div>
                    Add Material
                </h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('materials.store', $classroom) }}" method="POST" enctype="multipart/form-data" class="upload-box-form">
                @csrf
                <div class="modal-body p-5 pt-3">
                    <div class="mb-4">
                        <label class="form-label">Material Name</label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. Chapter 4: Neural Networks" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Brief Overview</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Explain what students should focus on or learn from this resource..."></textarea>
                    </div>

                    <div class="mb-0">
                        <label class="form-label">Resource Files</label>
                        <div class="luxury-upload-box p-4 text-center" id="upload-material">
                            <input type="file" name="files[]" id="file-material" class="d-none" multiple>
                            <label for="file-material" class="mb-0 cursor-pointer w-100">
                                <i data-lucide="paperclip" size="32" class="text-success opacity-50 mb-2"></i>
                                <div class="text-muted small fw-bold">Attach Resources</div>
                            </label>
                            <div class="upload-progress-list mt-3 text-start"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-5 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow">Save Resources</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.tiny.cloud/1/uzyi3qni0rl59wmj5i3t38v3cebtp184ygnuw2vto9ugxut5/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const now = new Date();
        const formattedNow = now.toISOString().slice(0, 16);
        document.querySelectorAll('.date-min-now').forEach(input => { input.min = formattedNow; });

        tinymce.init({
            selector: '#assignment-editor',
            height: 250,
            menubar: false,
            skin: (document.body.getAttribute('data-bs-theme') === 'dark' ? "oxide-dark" : "oxide"),
            content_css: (document.body.getAttribute('data-bs-theme') === 'dark' ? "dark" : "default"),
            plugins: 'lists link emoticons image code',
            toolbar: 'bold italic underline | numlist bullist | link image emoticons | code',
            br_in_pre: false,
            setup: editor => editor.on('change', () => editor.save())
        });

        const isAdvancedUpload = (function() {
            var div = document.createElement('div');
            return (('draggable' in div) || ('ondragstart' in div && 'ondrop' in div)) && 'FormData' in window && 'FileReader' in window;
        })();

        document.querySelectorAll('.luxury-upload-box').forEach(box => {
            const input = box.querySelector('input[type="file"]');
            const progressList = box.querySelector('.upload-progress-list');
            let uploadedFilesCount = 0;

            if (isAdvancedUpload) {
                ['dragover', 'dragenter'].forEach(ev => box.addEventListener(ev, () => box.classList.add('is-dragover')));
                ['dragleave', 'dragend', 'drop'].forEach(ev => box.addEventListener(ev, () => box.classList.remove('is-dragover')));
                box.addEventListener('drop', e => {
                    e.preventDefault();
                    handleFiles(e.dataTransfer.files);
                });
            }

            input.addEventListener('change', e => handleFiles(e.target.files));

            function handleFiles(files) {
                if (uploadedFilesCount + files.length > 10) {
                    showToast("Max 10 files allowed.", "error");
                    return;
                }
                Array.from(files).forEach(file => {
                    if (file.size > 20 * 1024 * 1024) {
                        showToast(`File ${file.name} exceeds 20MB limit.`, "error");
                        return;
                    }
                    uploadFile(file);
                });
            }

            function uploadFile(file) {
                const formData = new FormData();
                formData.append('file', file);
                formData.append('_token', "{{ csrf_token() }}");

                const item = document.createElement('div');
                item.className = 'd-flex align-items-center gap-3 p-3 rounded-4 bg-white border mb-2 transition-all shadow-sm';
                item.innerHTML = `
                    <div class="bg-light p-2 rounded-3 text-primary"><i data-lucide="file" size="20"></i></div>
                    <div class="flex-grow-1 overflow-hidden">
                        <div class="small fw-extrabold text-main text-truncate mb-1">${file.name}</div>
                        <div class="progress rounded-pill overflow-hidden" style="height: 6px; background: rgba(0,0,0,0.05);">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: 0%"></div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-link text-danger p-0 d-none remove-btn border-0"><i data-lucide="trash-2" size="18"></i></button>
                `;
                progressList.appendChild(item);
                lucide.createIcons();

                const xhr = new XMLHttpRequest();
                xhr.open('POST', "{{ route('upload') }}", true);

                xhr.upload.onprogress = e => {
                    if (e.lengthComputable) {
                        const pct = (e.loaded / e.total) * 100;
                        item.querySelector('.progress-bar').style.width = pct + '%';
                    }
                };

                xhr.onload = () => {
                    if (xhr.status === 200) {
                        const res = JSON.parse(xhr.responseText);
                        item.querySelector('.progress').classList.add('d-none');
                        item.querySelector('.remove-btn').classList.remove('d-none');
                        
                        const hidden = document.createElement('input');
                        hidden.type = 'hidden';
                        hidden.name = 'files[]';
                        hidden.value = JSON.stringify(res);
                        item.appendChild(hidden);

                        item.querySelector('.remove-btn').onclick = () => {
                            item.remove();
                            uploadedFilesCount--;
                        };
                        uploadedFilesCount++;
                    } else {
                        item.classList.add('border-danger');
                        showToast(`Upload failed: ${file.name}`, "error");
                    }
                };
                xhr.send(formData);
            }
        });
    });
</script>

<style>
    .luxury-upload-box { border: 2.5px dashed var(--border-color); border-radius: 24px; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); background: var(--bg-color); }
    .luxury-upload-box:hover, .luxury-upload-box.is-dragover { border-color: var(--primary-color); background: rgba(var(--primary-rgb), 0.05); transform: scale(1.01); }
    .pulse-on-hover:hover { animation: pulse-soft 2s infinite; }
    @keyframes pulse-soft { 0% { transform: scale(1); opacity: 0.5; } 50% { transform: scale(1.1); opacity: 0.8; } 100% { transform: scale(1); opacity: 0.5; } }
    .bg-main-light { background-color: rgba(var(--primary-rgb), 0.03); }
    .tox.tox-tinymce { border-radius: 18px !important; border: 1.5px solid var(--border-color) !important; padding: 4px; }
    .btn-pill-sm { padding: 4px 12px; font-size: 0.75rem; font-weight: 700; }
</style>
