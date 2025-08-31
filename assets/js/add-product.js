function validateStep(step) {
    const currentStepElement = document.querySelector(`#step${step}`);
    const requiredFields = currentStepElement.querySelectorAll('[required]');
    let isValid = true;
    let errorMessage = '';

    requiredFields.forEach(field => {
        if (!field.value) {
            isValid = false;
            errorMessage += `Le champ "${field.previousElementSibling.textContent.trim()}" est requis.\n`;
        }
    });

    if (!isValid) {
        alert(errorMessage);
    }
    
    return isValid;
}

function nextStep(currentStep) {
    if (!validateStep(currentStep)) {
        return;
    }
    
    document.querySelector(`#step${currentStep}`).classList.remove('active');
    document.querySelector(`#step${currentStep + 1}`).classList.add('active');
    
    document.querySelectorAll('.step')[currentStep].classList.add('active');
}

function prevStep(currentStep) {
    document.querySelector(`#step${currentStep}`).classList.remove('active');
    document.querySelector(`#step${currentStep - 1}`).classList.add('active');
    
    document.querySelectorAll('.step')[currentStep - 1].classList.remove('active');
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
            <label class="form-label">URL de la vidéo</label>
            <input type="url" class="form-control" name="video_url[]" required>
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

// Gestion du drag & drop des images
document.addEventListener('DOMContentLoaded', function() {
    // Initialisation de TinyMCE
    tinymce.init({
        selector: '#description',
        plugins: 'lists link image table code help wordcount',
        toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright | indent outdent | bullist numlist | code',
        height: 300
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

    document.getElementById('productForm').addEventListener('submit', function(e) {
        e.preventDefault();
        if (validateForm()) {
            // Ajoutez ici la logique pour envoyer les données au serveur
            console.log('Formulaire soumis');
        }
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
