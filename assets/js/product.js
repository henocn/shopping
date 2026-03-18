
document.addEventListener("DOMContentLoaded", function () {
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
  animateOnScroll();


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
