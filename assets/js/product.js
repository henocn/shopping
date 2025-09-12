document.addEventListener("DOMContentLoaded", function () {
  function generateDynamicTheme() {
    const heroImage = document.getElementById("mainImage");
    if (!heroImage || !window.Vibrant) return;

    const img = new Image();
    img.crossOrigin = "anonymous";
    img.onload = function () {
      const vibrant = new Vibrant(img);
      const swatches = vibrant.swatches();

      if (swatches) {
        const root = document.documentElement;

        // Couleur principale (Vibrant)
        if (swatches.Vibrant) {
          const vibrantColor = swatches.Vibrant.getHex();
          root.style.setProperty("--primary", vibrantColor);
          root.style.setProperty(
            "--primary-dark",
            adjustBrightness(vibrantColor, -20)
          );
          root.style.setProperty(
            "--primary-light",
            adjustBrightness(vibrantColor, 30)
          );
        }

        // Couleur secondaire (Muted)
        if (swatches.Muted) {
          const mutedColor = swatches.Muted.getHex();
          root.style.setProperty("--secondary", mutedColor);
        }

        // Couleur de fond (Light Vibrant)
        if (swatches.LightVibrant) {
          const lightColor = swatches.LightVibrant.getHex();
          root.style.setProperty("--surface", lightColor);
        }

        // Mettre à jour les gradients
        updateGradients();
      }
    };
    img.src = heroImage.src;
  }

  function adjustBrightness(hex, percent) {
    const num = parseInt(hex.replace("#", ""), 16);
    const amt = Math.round(2.55 * percent);
    const R = (num >> 16) + amt;
    const G = ((num >> 8) & 0x00ff) + amt;
    const B = (num & 0x0000ff) + amt;
    return (
      "#" +
      (
        0x1000000 +
        (R < 255 ? (R < 1 ? 0 : R) : 255) * 0x10000 +
        (G < 255 ? (G < 1 ? 0 : G) : 255) * 0x100 +
        (B < 255 ? (B < 1 ? 0 : B) : 255)
      )
        .toString(16)
        .slice(1)
    );
  }

  function updateGradients() {
    const root = document.documentElement;
    const primary = getComputedStyle(root).getPropertyValue("--primary").trim();
    const primaryDark = getComputedStyle(root)
      .getPropertyValue("--primary-dark")
      .trim();

    root.style.setProperty(
      "--gradient-primary",
      `linear-gradient(135deg, ${primary} 0%, ${primaryDark} 100%)`
    );
  }

  // ========== Swiper amélioré ==========
  const thumbSwiper = new Swiper(".thumbSwiper", {
    spaceBetween: 15,
    slidesPerView: 4,
    freeMode: true,
    watchSlidesProgress: true,
    breakpoints: {
      320: { slidesPerView: 3, spaceBetween: 10 },
      480: { slidesPerView: 4, spaceBetween: 12 },
      768: { slidesPerView: 5, spaceBetween: 15 },
      1024: { slidesPerView: 6, spaceBetween: 20 },
    },
  });

  const mainSwiper = new Swiper(".mainSwiper", {
    spaceBetween: 0,
    navigation: {
      nextEl: ".swiper-button-next",
      prevEl: ".swiper-button-prev",
    },
    thumbs: {
      swiper: thumbSwiper,
    },
    effect: "fade",
    fadeEffect: {
      crossFade: true,
    },
    autoplay: {
      delay: 5000,
      disableOnInteraction: false,
    },
    loop: true,
    speed: 800,
  });

  // ========== Navigation scroll ==========
  function handleNavbarScroll() {
    const navbar = document.querySelector(".navbar");
    if (window.scrollY > 100) {
      navbar.classList.add("scrolled");
    } else {
      navbar.classList.remove("scrolled");
    }
  }

  window.addEventListener("scroll", handleNavbarScroll);

  // ========== Partage amélioré ==========
  window.shareProduct = function (platform) {
    const url = encodeURIComponent(window.location.href);
    const title = encodeURIComponent(document.title);
    const description = encodeURIComponent(
      document.querySelector('meta[property="og:description"]')?.content || ""
    );
    const image = encodeURIComponent(
      document.querySelector('meta[property="og:image"]')?.content || ""
    );

    let shareUrl = "";

    switch (platform) {
      case "facebook":
        shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${url}`;
        break;
      case "twitter":
        shareUrl = `https://twitter.com/intent/tweet?url=${url}&text=${title}`;
        break;
      case "whatsapp":
        shareUrl = `https://api.whatsapp.com/send?text=${title}%20-%20${url}`;
        break;
      case "linkedin":
        shareUrl = `https://www.linkedin.com/sharing/share-offsite/?url=${url}`;
        break;
      case "pinterest":
        shareUrl = `https://pinterest.com/pin/create/button/?url=${url}&media=${image}&description=${title}`;
        break;
    }

    if (shareUrl) {
      window.open(shareUrl, "_blank", "width=600,height=400");
    }
  };

  window.copyLink = function () {
    const url = window.location.href;
    navigator.clipboard
      .writeText(url)
      .then(() => {
        showNotification("Lien copié dans le presse-papiers !", "success");
      })
      .catch(() => {
        showNotification("Erreur lors de la copie", "error");
      });
  };

  function showNotification(message, type = "info") {
    const notification = document.createElement("div");
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${
              type === "success"
                ? "#10b981"
                : type === "error"
                ? "#ef4444"
                : "#3b82f6"
            };
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            z-index: 10000;
            transform: translateX(100%);
            transition: transform 0.3s ease;
            font-weight: 600;
        `;

    document.body.appendChild(notification);

    setTimeout(() => {
      notification.style.transform = "translateX(0)";
    }, 100);

    setTimeout(() => {
      notification.style.transform = "translateX(100%)";
      setTimeout(() => {
        document.body.removeChild(notification);
      }, 300);
    }, 3000);
  }

  // ========== Modal commande amélioré ==========
  window.openOrderForm = function () {
    const orderModal = new bootstrap.Modal(
      document.getElementById("orderModal")
    );
    orderModal.show();

    // Animation d'entrée du modal
    const modalContent = document.querySelector("#orderModal .modal-content");
    modalContent.style.transform = "scale(0.8)";
    modalContent.style.opacity = "0";

    setTimeout(() => {
      modalContent.style.transition = "all 0.3s ease";
      modalContent.style.transform = "scale(1)";
      modalContent.style.opacity = "1";
    }, 100);
  };

  // ========== Gestion formulaire amélioré ==========
  const orderForm = document.getElementById("orderForm");

  if (orderForm) {
    orderForm.addEventListener("submit", function (e) {
      e.preventDefault();

      const submitBtn = this.querySelector('button[type="submit"]');
      const originalText = submitBtn.innerHTML;

      // Bouton en mode "loading"
      submitBtn.innerHTML =
        '<i class="bx bx-loader-alt bx-spin"></i> Envoi en cours...';
      submitBtn.disabled = true;

      // Préparer les données
      const formData = new FormData(this);
      formData.append("valider", "commander");
      fetch("management/orders/save.php", {
        method: "POST",
        headers: {
          "X-Requested-With": "XMLHttpRequest",
        },
        body: formData,
      })
        .then((res) => {
          if (!res.ok) {
            throw new Error("Erreur réseau");
          }
          return res.json();
        })
        .then((data) => {
          if (data.success) {
            Swal.fire({
              title: "Succès ✅",
              text:
                data.message ??
                "Merci ! Votre commande a été enregistrée avec succès !",
              icon: "success",
              confirmButtonText: "OK",
            }).then(() => {
              // Fermer la modal Bootstrap
              const modal = bootstrap.Modal.getInstance(
                document.getElementById("orderModal")
              );
              if (modal) modal.hide();

              // Réinitialiser le formulaire
              orderForm.reset();
            });
          } else {
            Swal.fire({
              title: "Erreur ❌",
              text: data.message ?? "Impossible d'enregistrer la commande",
              icon: "error",
              confirmButtonText: "OK",
            });
          }
        })
        .catch((err) => {
          Swal.fire({
            title: "Erreur serveur ❌",
            text: err.message || "Problème lors de l'envoi de la commande",
            icon: "error",
            confirmButtonText: "OK",
          });
        })
        .finally(() => {
          // Remettre le bouton normal
          submitBtn.innerHTML = originalText;
          submitBtn.disabled = false;
        });
    });
  }

  // ========== Animations d'entrée ==========
  function animateOnScroll() {
    const elements = document.querySelectorAll(
      ".feature-card, .video-card, .product-info"
    );

    elements.forEach((element) => {
      const elementTop = element.getBoundingClientRect().top;
      const elementVisible = 150;

      if (elementTop < window.innerHeight - elementVisible) {
        element.style.opacity = "1";
        element.style.transform = "translateY(0)";
      }
    });
  }

  // Initialiser les animations
  document
    .querySelectorAll(".feature-card, .video-card, .product-info")
    .forEach((element) => {
      element.style.opacity = "0";
      element.style.transform = "translateY(30px)";
      element.style.transition = "all 0.6s ease";
    });

  window.addEventListener("scroll", animateOnScroll);
  animateOnScroll(); // Appel initial

  // ========== Lazy loading des images ==========
  function lazyLoadImages() {
    const images = document.querySelectorAll("img[data-src]");
    const imageObserver = new IntersectionObserver((entries, observer) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          const img = entry.target;
          img.src = img.dataset.src;
          img.classList.remove("lazy");
          imageObserver.unobserve(img);
        }
      });
    });

    images.forEach((img) => imageObserver.observe(img));
  }

  // ========== Initialisation ==========
  // Générer le thème dynamique après le chargement de l'image
  setTimeout(() => {
    generateDynamicTheme();
  }, 1000);

  // Lazy loading
  lazyLoadImages();

  // ========== Smooth scroll pour les ancres ==========
  document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
    anchor.addEventListener("click", function (e) {
      e.preventDefault();
      const target = document.querySelector(this.getAttribute("href"));
      if (target) {
        target.scrollIntoView({
          behavior: "smooth",
          block: "start",
        });
      }
    });
  });
});
