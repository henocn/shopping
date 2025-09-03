// ================================
// Thème dynamique basé sur l'image principale
// ================================
document.addEventListener("DOMContentLoaded", function () {
    const mainImage = document.querySelector(".main-image");

    if (!mainImage) return;

    // Attendre que l'image soit chargée
    mainImage.addEventListener("load", function () {
        const colorThief = new ColorThief();

        try {
            // Récupère la couleur dominante
            const dominantColor = colorThief.getColor(mainImage);

            // Récupère une palette (6 couleurs max)
            const palette = colorThief.getPalette(mainImage, 6);

            // Convertit en CSS rgb()
            const toRgb = (arr) => `rgb(${arr[0]}, ${arr[1]}, ${arr[2]})`;

            // Appliquer les variables CSS dynamiques
            document.documentElement.style.setProperty("--primary", toRgb(dominantColor));
            document.documentElement.style.setProperty("--secondary", toRgb(palette[1] || dominantColor));
            document.documentElement.style.setProperty("--neutral-dark", "#2F2F2F");
            document.documentElement.style.setProperty("--neutral-light", "#F5F5F5");

            console.log("🎨 Thème dynamique appliqué :", dominantColor, palette);
        } catch (err) {
            console.warn("ColorThief error:", err);
        }
    });

    // Si l'image est déjà dans le cache, forcer le déclenchement
    if (mainImage.complete) {
        mainImage.dispatchEvent(new Event("load"));
    }
});
