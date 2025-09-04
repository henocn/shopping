// Afficher/Masquer mot de passe
document.querySelector('.bx-show').parentElement.addEventListener('click', function() {
    const input = this.previousElementSibling;
    const icon = this.querySelector('.bx');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('bx-show', 'bx-hide');
    } else {
        input.type = 'password';
        icon.classList.replace('bx-hide', 'bx-show');
    }
});

// Gestion du formulaire
document.getElementById('addUserForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const modal = bootstrap.Modal.getInstance(document.getElementById('addUserModal'));
    modal.hide();
});
