// Fonctions de navigation entre les étapes
function validateStep(currentStep) {
    const stepElement = document.querySelector(`#step${currentStep}`);
    const requiredFields = stepElement.querySelectorAll('[required]');
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

// Fonctions pour ajouter des éléments dynamiques
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

// Initialisation lors du chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    // Initialisation de TinyMCE
    tinymce.init({
        selector: '#description',
        height: 300,
        menubar: false,
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
            'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
            'insertdatetime', 'media', 'table', 'help', 'wordcount'
        ],
        toolbar: 'undo redo | blocks | ' +
            'bold italic | alignleft aligncenter alignright alignjustify | ' +
            'bullist numlist outdent indent | removeformat | help'
    });

    // Gestion de l'image principale
    const mainImageInput = document.getElementById('mainImageInput');
    const mainImageZone = document.getElementById('mainImageUpload');

    mainImageZone.addEventListener('click', () => {
        mainImageInput.click();
    });

    mainImageInput.addEventListener('change', function(e) {
        const preview = document.getElementById('mainImagePreview');
        preview.innerHTML = '';
        
        if (this.files && this.files[0]) {
            const img = document.createElement('img');
            img.className = 'preview-image';
            const reader = new FileReader();
            reader.onload = (e) => {
                img.src = e.target.result;
            };
            reader.readAsDataURL(this.files[0]);
            preview.appendChild(img);
        }
    });

    // Gestion des images du carousel
    const carouselInput = document.getElementById('carouselImagesInput');
    const carouselZone = document.getElementById('carouselImageUpload');

    carouselZone.addEventListener('click', () => {
        carouselInput.click();
    });

    carouselInput.addEventListener('change', function(e) {
        const preview = document.getElementById('carouselPreview');
        preview.innerHTML = '';
        
        Array.from(this.files).slice(0, 5).forEach(file => {
            const img = document.createElement('img');
            img.className = 'preview-image';
            const reader = new FileReader();
            reader.onload = (e) => {
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
            preview.appendChild(img);
        });
    });

    // Validation du formulaire
    document.getElementById('productForm').addEventListener('submit', function(e) {
        e.preventDefault();
        if (validateForm()) {
            console.log('Formulaire soumis avec succès');
        }
    });
});

// Fonction de validation globale du formulaire
function validateForm() {
    const activeStep = document.querySelector('.step-content.active');
    const stepNumber = activeStep.id.replace('step', '');
    return validateStep(parseInt(stepNumber));
}
