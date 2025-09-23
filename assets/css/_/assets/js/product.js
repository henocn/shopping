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

  // ========== Modal commande: ouverture et accessibilité ==========
  window.openOrderForm = function () {
    const orderModalEl = document.getElementById("orderModal");
    const orderModal = new bootstrap.Modal(orderModalEl);
    orderModal.show();
  };

  // Appliquer l'animation quand le modal est effectivement affiché
  const orderModalEl = document.getElementById("orderModal");
  if (orderModalEl) {
    orderModalEl.addEventListener("shown.bs.modal", function () {
      const modalContent = orderModalEl.querySelector(".modal-content");
      if (!modalContent) return;
      modalContent.style.transform = "scale(0.8)";
      modalContent.style.opacity = "0";
      // Forcer un reflow pour que la transition s'applique proprement
      // eslint-disable-next-line no-unused-expressions
      modalContent.offsetHeight;
      setTimeout(() => {
        modalContent.style.transition = "all 0.3s ease";
        modalContent.style.transform = "scale(1)";
        modalContent.style.opacity = "1";
      }, 50);
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

document.addEventListener("DOMContentLoaded", function () {
  const toastEl = document.getElementById("liveToast");
  const toastBody = document.getElementById("toastMessage");

  const message = toastBody.textContent.trim();

  if (message !== "") {
    toastEl.className = "toast align-items-center text-white border-0";

    if (/succès|success/i.test(message)) {
      toastEl.classList.add("bg-success");
    } else {
      toastEl.classList.add("bg-danger");
    }

    const toast = new bootstrap.Toast(toastEl, { delay: 8000 });
    toast.show();
  }
});

function showToast(message, type = "success") {
  const toastEl = document.getElementById("liveToast");
  const toastBody = document.getElementById("toastMessage");

  toastBody.textContent = message;

  toastEl.className = "toast align-items-center text-white border-0";

  if (type === "success") {
    toastEl.classList.add("bg-success");
  } else {
    toastEl.classList.add("bg-danger");
  }

  const toast = new bootstrap.Toast(toastEl, { delay: 5000 });
  toast.show();
}
