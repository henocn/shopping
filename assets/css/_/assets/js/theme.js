document.addEventListener("DOMContentLoaded", () => {
    const img = document.getElementById("mainImage");

    img.addEventListener("load", () => {
        Vibrant.from(img).getPalette().then(palette => {
            if (!palette.Vibrant) return;

            const primary = palette.Vibrant.hex;
            const primaryDark = palette.DarkVibrant ? palette.DarkVibrant.hex : primary;
            const primaryLight = palette.LightVibrant ? palette.LightVibrant.hex : primary;
            const surface = palette.Muted ? palette.Muted.hex : "#f5f5f5";
            const text = palette.DarkMuted ? palette.DarkMuted.hex : "#333";

            document.documentElement.style.setProperty("--primary", primary);
            document.documentElement.style.setProperty("--primary-dark", primaryDark);
            document.documentElement.style.setProperty("--primary-light", primaryLight);
            document.documentElement.style.setProperty("--surface", surface);
            document.documentElement.style.setProperty("--text", text);
            // Styles dynamiques appliqués automatiquement
        });
    });

    if (img.complete) {
        img.dispatchEvent(new Event("load"));
    }
});
