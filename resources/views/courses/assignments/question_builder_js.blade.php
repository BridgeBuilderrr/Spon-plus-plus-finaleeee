<script>
    let questionCount = 0;

    window.addQuestionField = function(data = null) {
        const container = document.getElementById('questions-container');
        const index = questionCount++;

        const card = document.createElement('div');
        card.className = 'card border p-4 rounded-4 mb-3 bg-light-subtle position-relative question-card';
        card.id = `q-card-${index}`;
        card.dataset.index = index;

        card.addEventListener('click', function() {
            document.querySelectorAll('.question-card').forEach(c => c.classList.remove('active-card'));
            card.classList.add('active-card');
        });

        const qText    = data ? data.text    : '';
        const qType    = data ? data.type    : 'multiple_choice';
        const qOptions = data ? data.options : ['Option 1'];
        const qCorrect = data ? data.correct : [0];
        const qImg     = data ? (data.image || '') : '';

        const isText = qType === 'text';

        card.innerHTML = `
            <div class="q-drag-handle" title="Drag to reorder">
                <svg width="24" height="12" viewBox="0 0 24 12" fill="currentColor">
                    <circle cx="4"  cy="3" r="1.5"/><circle cx="12" cy="3" r="1.5"/><circle cx="20" cy="3" r="1.5"/>
                    <circle cx="4"  cy="9" r="1.5"/><circle cx="12" cy="9" r="1.5"/><circle cx="20" cy="9" r="1.5"/>
                </svg>
            </div>

            <input type="hidden" name="questions[${index}][required]" value="1">

            <div class="d-flex gap-2 mb-3 align-items-start">
                <input type="text" name="questions[${index}][text]"
                       class="form-control border-0 border-bottom rounded-0 px-0 fw-semibold q-text-input flex-grow-1"
                       placeholder="Question" value="${qText}" required>
                <div class="d-flex align-items-center gap-1 flex-shrink-0 ms-2">
                    <button type="button" class="btn btn-sm btn-light rounded-3 p-1 q-img-btn"
                            title="Add image" onclick="window.triggerQImage(${index})">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/>
                            <polyline points="21 15 16 10 5 21"/>
                        </svg>
                    </button>
                    <input type="file" accept="image/*" class="d-none q-img-input" id="q-img-input-${index}"
                           onchange="window.handleQImage(${index}, this)">
                    <select name="questions[${index}][type]"
                            class="form-select form-select-sm border rounded-3 q-type-select"
                            style="min-width:140px;"
                            onchange="window.changeQuestionType(${index}, this.value)">
                        <option value="multiple_choice" ${qType==='multiple_choice'?'selected':''}>Multiple Choice</option>
                        <option value="checkboxes"      ${qType==='checkboxes'?'selected':''}>Checkboxes</option>
                        <option value="text"            ${qType==='text'?'selected':''}>Text (Short Answer)</option>
                    </select>
                </div>
            </div>

            <input type="hidden" name="questions[${index}][image]" class="q-img-value" value="${qImg}">
            <div class="q-img-preview mb-3 ${qImg ? '' : 'd-none'}">
                ${qImg ? `<img src="${qImg}" class="rounded-3" style="max-height:140px;">` : ''}
            </div>

            <div class="mb-3 q-options-section ${isText ? 'd-none' : ''}">
                <div class="small fw-bold text-muted mb-2">Options :  check the correct answer(s)</div>
                <div class="d-flex flex-column gap-1" id="q-${index}-options-container"></div>
                <div class="mt-2">
                    <button type="button" class="btn btn-sm btn-link text-decoration-none fw-bold p-0 text-primary"
                            onclick="window.addOptionField(${index})">
                        <i data-lucide="plus" size="14" class="me-1"></i> Add option
                    </button>
                </div>
            </div>

            <div class="q-text-preview ${isText ? '' : 'd-none'}">
                <input type="text" disabled placeholder="Short answer text (student fills in max 500 chars)"
                       class="form-control border-0 border-bottom rounded-0 px-0 text-muted fst-italic" style="pointer-events:none;">
            </div>

            <hr class="my-3 text-muted opacity-25">
            <div class="d-flex justify-content-end align-items-center gap-3">
                <button type="button" class="btn btn-link text-decoration-none text-muted p-0 d-flex align-items-center gap-1 small fw-bold"
                        onclick="window.duplicateQuestion(${index})">
                    <i data-lucide="copy" size="16"></i> Duplicate
                </button>
                <button type="button" class="btn btn-link text-decoration-none text-danger p-0 d-flex align-items-center gap-1 small fw-bold"
                        onclick="window.removeQuestionField(${index})">
                    <i data-lucide="trash-2" size="16"></i> Delete
                </button>
            </div>
        `;

        container.appendChild(card);

        if (!isText) {
            const optCont = document.getElementById(`q-${index}-options-container`);
            qOptions.forEach((optText, optIdx) => {
                const isCorrect = Array.isArray(qCorrect) ? qCorrect.includes(optIdx) : (qCorrect == optIdx);
                window.addOptionFieldEx(index, optText, isCorrect, qType);
            });
            // SortableJS on option rows
            if (typeof Sortable !== 'undefined') {
                Sortable.create(optCont, {
                    animation: 120,
                    handle: '.opt-drag-handle',
                    ghostClass: 'sortable-ghost',
                    onEnd: () => {
                        const curIdx = parseInt(card.dataset.index);
                        if (!isNaN(curIdx)) {
                            window.renumberOptions(curIdx);
                        }
                    }
                });
            }
        }

        document.querySelectorAll('.question-card').forEach(c => c.classList.remove('active-card'));
        card.classList.add('active-card');
        if (typeof lucide !== 'undefined') lucide.createIcons();
    };

    // Image upload helpers
    window.triggerQImage = function(qIndex) {
        document.getElementById(`q-img-input-${qIndex}`)?.click();
    };
    window.handleQImage = function(qIndex, input) {
        const file = input.files[0];
        if (!file) return;
        const card = document.getElementById(`q-card-${qIndex}`);
        const preview = card.querySelector('.q-img-preview');
        const hidden  = card.querySelector('.q-img-value');
        const reader = new FileReader();
        reader.onload = e => {
            preview.innerHTML = `<img src="${e.target.result}" class="rounded-3" style="max-height:140px;">`;
            preview.classList.remove('d-none');
            // Upload to server
            const fd = new FormData();
            fd.append('file', file);
            fd.append('_token', document.querySelector('meta[name=csrf-token]')?.content || '');
            fetch('{{ route("upload") }}', { method:'POST', body: fd })
                .then(r => r.json()).then(resp => { if (hidden) hidden.value = resp.url || resp.path || ''; })
                .catch(() => {});
        };
        reader.readAsDataURL(file);
    };

    window.addOptionFieldEx = function(qIndex, text = '', isCorrect = false, qType = 'multiple_choice') {
        const container = document.getElementById(`q-${qIndex}-options-container`);
        if (!container) return;
        
        const optRows = container.querySelectorAll('.option-row');
        const newOptIdx = optRows.length;
        
        const row = document.createElement('div');
        row.className = 'd-flex option-row align-items-center mb-2 gap-3';
        
        const isChecked = isCorrect ? 'checked' : '';
        
        if (qType === 'checkboxes') {
            row.innerHTML = `
                <span class="opt-drag-handle d-flex align-items-center px-1" style="cursor:move;color:#9ca3af;">
                    <svg width="12" height="16" viewBox="0 0 12 16" fill="currentColor">
                        <circle cx="3" cy="4" r="1.4"/><circle cx="9" cy="4" r="1.4"/>
                        <circle cx="3" cy="8" r="1.4"/><circle cx="9" cy="8" r="1.4"/>
                        <circle cx="3" cy="12" r="1.4"/><circle cx="9" cy="12" r="1.4"/>
                    </svg>
                </span>
                <span class="d-flex align-items-center px-1">
                    <input type="checkbox" name="questions[${qIndex}][correct][]" value="${newOptIdx}" ${isChecked} class="form-check-input opt-correct-input" style="width:1.1em;height:1.1em;margin-top:0;">
                </span>
                <input type="text" name="questions[${qIndex}][options][${newOptIdx}]" class="form-control border rounded-3 p-2 flex-grow-1" placeholder="Option ${newOptIdx + 1}" value="${text}" required>
                <button type="button" class="btn btn-outline-danger btn-remove-option rounded-3" onclick="window.removeOptionField(${qIndex}, this)">
                    <i data-lucide="trash-2" size="16"></i>
                </button>
            `;
        } else {
            row.innerHTML = `
                <span class="opt-drag-handle d-flex align-items-center px-1" style="cursor:move;color:#9ca3af;">
                    <svg width="12" height="16" viewBox="0 0 12 16" fill="currentColor">
                        <circle cx="3" cy="4" r="1.4"/><circle cx="9" cy="4" r="1.4"/>
                        <circle cx="3" cy="8" r="1.4"/><circle cx="9" cy="8" r="1.4"/>
                        <circle cx="3" cy="12" r="1.4"/><circle cx="9" cy="12" r="1.4"/>
                    </svg>
                </span>
                <span class="d-flex align-items-center px-1">
                    <input type="radio" name="questions[${qIndex}][correct]" value="${newOptIdx}" ${isChecked} class="form-check-input opt-correct-input" style="width:1.1em;height:1.1em;margin-top:0;">
                </span>
                <input type="text" name="questions[${qIndex}][options][${newOptIdx}]" class="form-control border rounded-3 p-2 flex-grow-1" placeholder="Option ${newOptIdx + 1}" value="${text}" required>
                <button type="button" class="btn btn-outline-danger btn-remove-option rounded-3" onclick="window.removeOptionField(${qIndex}, this)">
                    <i data-lucide="trash-2" size="16"></i>
                </button>
            `;
        }
        container.appendChild(row);

        // Auto-fill blank option text on blur
        const textInput = row.querySelector('input[type="text"]');
        if (textInput) {
            textInput.addEventListener('blur', function() {
                if (this.value.trim() === '') {
                    const allRows = container.querySelectorAll('.option-row');
                    const myIdx = Array.from(allRows).indexOf(row);
                    this.value = `Option ${myIdx + 1}`;
                }
            });
        }

        window.renumberOptions(qIndex);
        if (typeof lucide !== 'undefined') lucide.createIcons();
    };

    window.addOptionField = function(qIndex, text = '') {
        const card = document.getElementById(`q-card-${qIndex}`);
        const qType = card.querySelector('.q-type-select').value;
        window.addOptionFieldEx(qIndex, text, false, qType);
    };

    window.removeOptionField = function(qIndex, btn) {
        const row = btn.closest('.option-row');
        if (row) {
            row.remove();
            window.renumberOptions(qIndex);
        }
    };

    window.renumberOptions = function(qIndex) {
        const container = document.getElementById(`q-${qIndex}-options-container`);
        if (!container) return;
        
        const rows = container.querySelectorAll('.option-row');
        const showDelete = rows.length > 1;
        const card = document.getElementById(`q-card-${qIndex}`);
        const qType = card ? card.querySelector('.q-type-select').value : 'multiple_choice';
        
        rows.forEach((row, idx) => {
            const radio = row.querySelector('input[type="radio"]');
            const checkbox = row.querySelector('input[type="checkbox"]');
            
            if (radio) radio.value = idx;
            if (checkbox) checkbox.value = idx;
            
            const textInput = row.querySelector('input[type="text"]');
            if (textInput) {
                textInput.name = `questions[${qIndex}][options][${idx}]`;
                textInput.placeholder = `Option ${idx + 1}`;
            }
            
            const deleteBtn = row.querySelector('.btn-remove-option');
            if (deleteBtn) {
                deleteBtn.style.setProperty('display', showDelete ? 'block' : 'none', 'important');
            }
        });
        
        if (qType === 'multiple_choice') {
            const checkedRadio = container.querySelector('input[type="radio"]:checked');
            if (!checkedRadio && rows.length > 0) {
                const firstRadio = rows[0].querySelector('input[type="radio"]');
                if (firstRadio) firstRadio.checked = true;
            }
        }
    };

    window.changeQuestionType = function(qIndex, newType) {
        const card = document.getElementById(`q-card-${qIndex}`);
        if (!card) return;

        const optSection = card.querySelector('.q-options-section');
        const textPreview = card.querySelector('.q-text-preview');
        const container = document.getElementById(`q-${qIndex}-options-container`);

        if (newType === 'text') {
            optSection?.classList.add('d-none');
            textPreview?.classList.remove('d-none');
        } else {
            optSection?.classList.remove('d-none');
            textPreview?.classList.add('d-none');

            if (container) {
                const rows = container.querySelectorAll('.option-row');
                if (rows.length === 0) {
                    window.addOptionFieldEx(qIndex, 'Option 1', true, newType);
                } else {
                    rows.forEach((row, idx) => {
                        const correctInput = row.querySelector('.opt-correct-input');
                        const wasChecked = correctInput ? correctInput.checked : false;
                        const isChecked = wasChecked ? 'checked' : '';
                        
                        const prepends = row.querySelectorAll('.input-group-text');
                        if (prepends.length >= 2) {
                            if (newType === 'checkboxes') {
                                prepends[1].innerHTML = `<input type="checkbox" name="questions[${qIndex}][correct][]" value="${idx}" ${isChecked} class="form-check-input opt-correct-input">`;
                            } else {
                                prepends[1].innerHTML = `<input type="radio" name="questions[${qIndex}][correct]" value="${idx}" ${isChecked} class="form-check-input opt-correct-input">`;
                            }
                        }
                    });
                }

                if (!container._sortableInitialized && typeof Sortable !== 'undefined') {
                    container._sortableInitialized = true;
                    Sortable.create(container, {
                        animation: 120,
                        handle: '.opt-drag-handle',
                        ghostClass: 'sortable-ghost',
                        onEnd: () => {
                            const curIdx = parseInt(card.dataset.index);
                            if (!isNaN(curIdx)) {
                                window.renumberOptions(curIdx);
                            }
                        }
                    });
                }
            }
            window.renumberOptions(qIndex);
        }
    };

    window.removeQuestionField = function(index) {
        const card = document.getElementById(`q-card-${index}`);
        if (card) {
            card.remove();
            window.renumberQuestions();
        }
    };

    window.renumberQuestions = function() {
        const cards = Array.from(document.querySelectorAll('.question-card'));

        cards.forEach((card, newIdx) => {
            const optCont = card.querySelector('[id$="-options-container"]');
            if (optCont) optCont.id = `q-tmp-${newIdx}-options-container`;

            const reqInput = card.querySelector('[id^="req-"]');
            const reqLabel = card.querySelector('[for^="req-"]');
            if (reqInput) reqInput.id = `req-tmp-${newIdx}`;
            if (reqLabel) reqLabel.setAttribute('for', `req-tmp-${newIdx}`);

            card.id = `q-card-tmp-${newIdx}`;
            card.dataset.index = `tmp-${newIdx}`;
        });

        cards.forEach((card, newIdx) => {
            const optCont = card.querySelector('[id^="q-tmp-"][id$="-options-container"]');
            if (optCont) optCont.id = `q-${newIdx}-options-container`;

            const reqInput = card.querySelector(`#req-tmp-${newIdx}`);
            const reqLabel = card.querySelector(`[for="req-tmp-${newIdx}"]`);
            if (reqInput) reqInput.id = `req-${newIdx}`;
            if (reqLabel) reqLabel.setAttribute('for', `req-${newIdx}`);

            card.querySelectorAll('[name]').forEach(el => {
                el.name = el.name.replace(/^questions\[\d+\]/, `questions[${newIdx}]`);
            });

            card.querySelectorAll('[onclick]').forEach(el => {
                el.setAttribute('onclick', el.getAttribute('onclick').replace(/\(\d+/g, `(${newIdx}`));
            });

            card.querySelectorAll('[onchange]').forEach(el => {
                el.setAttribute('onchange', el.getAttribute('onchange').replace(/\(\d+/g, `(${newIdx}`));
            });

            card.id = `q-card-${newIdx}`;
            card.dataset.index = newIdx;

            window.renumberOptions(newIdx);
        });
    };

    window.duplicateQuestion = function(qIndex) {
        const card = document.getElementById(`q-card-${qIndex}`);
        if (!card) return;
        
        const text = card.querySelector('.q-text-input').value;
        const type = card.querySelector('.q-type-select').value;
        const image = card.querySelector('.q-img-value')?.value || '';
        
        const optionInputs = card.querySelectorAll('.option-row input[type="text"]');
        const options = Array.from(optionInputs).map(inp => inp.value);
        
        const correctInputs = card.querySelectorAll('.opt-correct-input');
        const correct = Array.from(correctInputs)
            .map((inp, idx) => inp.checked ? idx : null)
            .filter(idx => idx !== null);
            
        window.addQuestionField({
            text: text,
            type: type,
            options: options,
            correct: correct,
            image: image
        });
    };
</script>
