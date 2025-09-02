// Configuration Swiper
document.addEventListener('DOMContentLoaded', function() {
    const mainSwiper = new Swiper(".mainSwiper", {
        spaceBetween: 0,
        effect: "fade",
        pagination: {
            el: ".swiper-pagination",
            clickable: true,
        },
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
    });

    const thumbSwiper = new Swiper(".thumbSwiper", {
        spaceBetween: 10,
        slidesPerView: "auto",
        freeMode: true,
        watchSlidesProgress: true,
        breakpoints: {
            320: {
                slidesPerView: 3,
            },
            480: {
                slidesPerView: 4,
            },
            768: {
                slidesPerView: 5,
            }
        }
    });

    mainSwiper.controller.control = thumbSwiper;
    thumbSwiper.controller.control = mainSwiper;
});

// Fonctions de partage
function shareProduct(platform, url, title) {
    switch(platform) {
        case 'facebook':
            window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`, '_blank');
            break;
        case 'twitter':
            window.open(`https://twitter.com/intent/tweet?url=${encodeURIComponent(url)}&text=${encodeURIComponent(title)}`, '_blank');
            break;
        case 'whatsapp':
            window.open(`https://api.whatsapp.com/send?text=${encodeURIComponent(title + ' ' + url)}`, '_blank');
            break;
    }
}

function copyLink() {
    navigator.clipboard.writeText(window.location.href)
        .then(() => {
            const tooltip = document.getElementById('copyTooltip');
            tooltip.style.opacity = '1';
            setTimeout(() => {
                tooltip.style.opacity = '0';
            }, 2000);
        });
}

// Formulaire de commande
function openOrderForm() {
    const orderModal = new bootstrap.Modal(document.getElementById('orderModal'));
    orderModal.show();
}

document.addEventListener('DOMContentLoaded', function() {
    const orderForm = document.getElementById('orderForm');
    if (orderForm) {
        orderForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            fetch(this.action, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Votre commande a été envoyée avec succès ! Nous vous contacterons bientôt.');
                    bootstrap.Modal.getInstance(document.getElementById('orderModal')).hide();
                } else {
                    alert('Une erreur est survenue : ' + data.message);
                }
            })
            .catch(error => {
                alert('Une erreur est survenue lors de l\'envoi de la commande.');
            });
        });
    }
});
