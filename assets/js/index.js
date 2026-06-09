document.addEventListener('DOMContentLoaded', function() {
    initializeSwiper();
    initializeColorTheme();
});

function initializeSwiper() {
    const thumbsSwiper = new Swiper('.thumbSwiper', {
        spaceBetween: 10,
        slidesPerView: 4,
        freeMode: true,
        watchSlidesProgress: true,
    });

    const mainSwiper = new Swiper('.mainSwiper', {
        spaceBetween: 10,
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        thumbs: {
            swiper: thumbsSwiper,
        },
    });
}

function initializeColorTheme() {
    const img = new Image();
    img.crossOrigin = "Anonymous";
    img.src = document.querySelector('.main-image').src;
    
    img.onload = function() {
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        canvas.width = img.width;
        canvas.height = img.height;
        ctx.drawImage(img, 0, 0);
        
        const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height).data;
        const colors = getColorPalette(imageData);
        
        document.documentElement.style.setProperty('--product-primary', colors.primary);
        document.documentElement.style.setProperty('--product-secondary', colors.secondary);
        document.documentElement.style.setProperty('--product-accent', colors.accent);
    };
}

function getColorPalette(imageData) {
    const colorCounts = {};
    
    for (let i = 0; i < imageData.length; i += 4) {
        const r = imageData[i];
        const g = imageData[i + 1];
        const b = imageData[i + 2];
        const rgb = `rgb(${r},${g},${b})`;
        
        colorCounts[rgb] = (colorCounts[rgb] || 0) + 1;
    }
    
    const sortedColors = Object.entries(colorCounts)
        .sort(([,a], [,b]) => b - a)
        .map(([color]) => color)
        .slice(0, 3);
    
    return {
        primary: sortedColors[0],
        secondary: sortedColors[1],
        accent: sortedColors[2]
    };
}

function updateQuantity(change) {
    const input = document.getElementById('quantity');
    const newValue = Math.max(1, Math.min(10, parseInt(input.value) + change));
    input.value = newValue;
}

function shareProduct(platform) {
    const url = window.location.href;
    const title = document.title;
    
    switch(platform) {
        case 'facebook':
            window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`);
            break;
        case 'twitter':
            window.open(`https://twitter.com/intent/tweet?url=${encodeURIComponent(url)}&text=${encodeURIComponent(title)}`);
            break;
    }
}

function copyLink() {
    navigator.clipboard.writeText(window.location.href)
        .then(() => {
            const button = document.querySelector('.btn-share:last-child');
            button.innerHTML = '<i class="bx bx-check"></i>';
            setTimeout(() => {
                button.innerHTML = '<i class="bx bx-link"></i>';
            }, 2000);
        });
}
