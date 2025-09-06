function validateForm() {
    const requiredFields = document.querySelectorAll('[required]');
    let isValid = true;
    let errorMessages = [];

    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            isValid = false;
            const label = field.closest('.mb-3').querySelector('.form-label').textContent.trim();
            errorMessages.push(`Le champ "${label}" est obligatoire`);
        }
    });

    if (!isValid) {
        alert(errorMessages.join('\n'));
    }

    return isValid;
}

function toggleSection(section) {
    const sectionElement = document.getElementById(`${section}Section`);
    const button = document.querySelector(`[onclick="toggleSection('${section}')"]`);
    const isHidden = window.getComputedStyle(sectionElement).display === 'none';
    
    if (isHidden) {
        document.querySelectorAll('[id$="Section"]').forEach(el => el.style.display = 'none');
        document.querySelectorAll('.floating-btn').forEach(btn => btn.classList.remove('active'));
        sectionElement.style.display = 'block';
        button.classList.add('active');
    } else {
        sectionElement.style.display = 'none';
        button.classList.remove('active');
    }
}

function addCharacteristic() {
    const characteristicDiv = document.createElement('div');
    characteristicDiv.className = 'characteristic-item';
    characteristicDiv.innerHTML = `
        <div class="mb-3">
            <label class="form-label">Titre</label>
            <input type="text" class="form-control" name="characteristic_title[]" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Image</label>
            <input type="file" class="form-control" name="characteristic_image[]">
        </div>
        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea class="form-control" name="characteristic_description[]" rows="3"></textarea>
        </div>
        <button type="button" class="btn btn-danger" onclick="this.parentElement.remove()">
            <i class='bx bx-trash'></i> Supprimer
        </button>
    `;
    document.getElementById('characteristicsList').appendChild(characteristicDiv);
}

function addVideo() {
    const videoDiv = document.createElement('div');
    videoDiv.className = 'characteristic-item';
    videoDiv.innerHTML = `
        <div class="mb-3">
            <label class="form-label">Vidéo</label>
            <input type="file" class="form-control" name="video[]" accept="video/*" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Texte</label>
            <textarea class="form-control" name="video_text[]" rows="3"></textarea>
        </div>
        <button type="button" class="btn btn-danger" onclick="this.parentElement.remove()">
            <i class='bx bx-trash'></i> Supprimer
        </button>
    `;
    document.getElementById('videosList').appendChild(videoDiv);
}


function addPack(){
    const packDiv = document.createElement('div');
    packDiv.className = 'characteristic-item';
    packDiv.innerHTML = `
        <div class="mb-3">
            <label class="form-label">Nom du Pack</label>
            <input type="text" class="form-control" name="pack_titre[]" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Description</label>
            <input type="text" class="form-control" name="pack_description[]" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Quantité</label>
            <input type="number" class="form-control" name="pack_quantity[]" min="0" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Prix de reduction</label>
            <input type="number" class="form-control" name="pack_price_reduction[]" min="0" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Prix normal</label>
            <input type="number" class="form-control" name="pack_price[]" min="0" required>
        </div>
        <button type="button" class="btn btn-danger" onclick="this.parentElement.remove()">
            <i class='bx bx-trash'></i> Supprimer
        </button>
    `;
    document.getElementById('packsList').appendChild(packDiv);
}



// Gestion du drag & drop des images
document.addEventListener('DOMContentLoaded', function() {
    ClassicEditor
        .create(document.querySelector('#description'), {
            toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|', 'outdent', 'indent', '|', 'blockQuote', 'insertTable', 'undo', 'redo'],
            heading: {
                options: [
                    { model: 'paragraph', title: 'Paragraphe', class: 'ck-heading_paragraph' },
                    { model: 'heading1', view: 'h1', title: 'Titre 1', class: 'ck-heading_heading1' },
                    { model: 'heading2', view: 'h2', title: 'Titre 2', class: 'ck-heading_heading2' }
                ]
            },
            language: 'fr'
        })
        .then(editor => {
            window.editor = editor;
        })
        .catch(error => {
            console.error(error);
        });

    // Gestion des zones de dépôt d'images
    ['mainImageUpload', 'carouselImageUpload'].forEach(id => {
        const element = document.getElementById(id);
        const inputId = id === 'mainImageUpload' ? 'mainImageInput' : 'carouselImagesInput';
        const fileInput = document.getElementById(inputId);
        
        element.addEventListener('dragover', e => {
            e.preventDefault();
            element.style.background = 'var(--paper)';
        });

        element.addEventListener('dragleave', () => {
            element.style.background = 'transparent';
        });

        element.addEventListener('drop', e => {
            e.preventDefault();
            element.style.background = 'transparent';
            const files = e.dataTransfer.files;
            handleFiles(files, id);
        });

        element.addEventListener('click', () => {
            fileInput.click();
        });

        fileInput.addEventListener('change', (e) => {
            handleFiles(e.target.files, id);
        });
    });

});

function validateForm() {
    let isValid = true;
    let errorMessages = [];

    // Validation des champs requis de l'étape actuelle
    const activeStep = document.querySelector('.step-content.active');
    const requiredFields = activeStep.querySelectorAll('[required]');
    
    requiredFields.forEach(field => {
        if (!field.value) {
            isValid = false;
            const labelText = field.previousElementSibling.textContent.trim();
            errorMessages.push(`Le champ "${labelText}" est requis.`);
        }
    });

    if (!isValid) {
        alert(errorMessages.join('\n'));
    }

    return isValid;
}

function handleFiles(files, uploaderId) {
    const previewerId = uploaderId === 'mainImageUpload' ? 'mainImagePreview' : 'carouselPreview';
    const preview = document.getElementById(previewerId);
    
    if (uploaderId === 'mainImageUpload') {
        preview.innerHTML = '';
        const file = files[0];
        if (file) {
            const img = document.createElement('img');
            img.className = 'preview-image';
            img.file = file;
            preview.appendChild(img);

            const reader = new FileReader();
            reader.onload = e => img.src = e.target.result;
            reader.readAsDataURL(file);
        }
    } else {
        preview.innerHTML = ''; // Réinitialiser la prévisualisation
        Array.from(files).slice(0, 5).forEach(file => {
            const img = document.createElement('img');
            img.className = 'preview-image';
            img.file = file;
            preview.appendChild(img);

            const reader = new FileReader();
            reader.onload = e => img.src = e.target.result;
            reader.readAsDataURL(file);
        });
    }
}
