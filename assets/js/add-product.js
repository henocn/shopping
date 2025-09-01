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
    const isHidden = getComputedStyle(sectionElement).display === 'none';
    
    // Cache toutes les sections d'abord
    ['carousel', 'characteristics', 'videos'].forEach(s => {
        const el = document.getElementById(`${s}Section`);
        if (el) el.style.display = 'none';
    });
    
    // Affiche la section sélectionnée si elle était cachée
    if (isHidden) {
        sectionElement.style.display = 'block';
    }
    
    // Mise à jour visuelle des boutons
    document.querySelectorAll('.btn-group .btn').forEach(btn => {
        btn.style.opacity = '0.7';
    });
    
    if (isHidden) {
        const button = document.querySelector(`[onclick="toggleSection('${section}')"]`);
        if (button) button.style.opacity = '1';
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
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
            'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
            'insertdatetime', 'media', 'table', 'help', 'wordcount'
        ],
        toolbar: 'undo redo | formatselect | ' +
            'bold italic backcolor | alignleft aligncenter alignright alignjustify | ' +
            'bullist numlist outdent indent | removeformat | help',
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

    // Gestion du formulaire
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
