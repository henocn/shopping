// ================================
// Gestion produit (carousel, partage, commande)
// ================================
document.addEventListener("DOMContentLoaded", function () {
    // ========== Swiper ==========
    const thumbSwiper = new Swiper(".thumbSwiper", {
        spaceBetween: 10,
        slidesPerView: 4,
        freeMode: true,
        watchSlidesProgress: true,
        breakpoints: {
            320: { slidesPerView: 3 },
            480: { slidesPerView: 4 },
            768: { slidesPerView: 5 }
        }
    });

    const mainSwiper = new Swiper(".mainSwiper", {
        spaceBetween: 10,
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev"
        },
        thumbs: {
            swiper: thumbSwiper
        },
        effect: "fade"
    });

    // ========== Partage ==========
    window.shareProduct = function (platform, url, title) {
        if (platform === "facebook") {
            window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, "_blank");
        } else if (platform === "twitter") {
            window.open(`https://twitter.com/intent/tweet?url=${url}&text=${title}`, "_blank");
        } else if (platform === "whatsapp") {
            window.open(`https://api.whatsapp.com/send?text=${title} - ${url}`, "_blank");
        }
    };

    window.copyLink = function () {
        navigator.clipboard.writeText(window.location.href).then(() => {
            const tooltip = document.getElementById("copyTooltip");
            tooltip.style.opacity = 1;
            setTimeout(() => {
                tooltip.style.opacity = 0;
            }, 2000);
        });
    };

    // ========== Modal commande ==========
    window.openOrderForm = function () {
        var orderModal = new bootstrap.Modal(document.getElementById("orderModal"));
        orderModal.show();
    };

    // ========== Gestion formulaire ==========
    const orderForm = document.getElementById("orderForm");

    if (orderForm) {
        orderForm.addEventListener("submit", function (e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch(this.action, {
                method: "POST",
                body: formData
            })
                .then((res) => res.json())
                .then((data) => {
                    if (data.success) {
                        alert("Votre commande a été envoyée avec succès !");
                        bootstrap.Modal.getInstance(document.getElementById("orderModal")).hide();
                        orderForm.reset();
                    } else {
                        alert("Erreur : " + data.message);
                    }
                })
                .catch(() => {
                    alert("Erreur lors de l'envoi de la commande.");
                });
        });
    }
});
