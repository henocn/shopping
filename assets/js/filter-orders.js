// Script de filtrage et recherche des commandes
document.addEventListener("DOMContentLoaded", function () {
  const searchInput = document.getElementById("searchInput");
  const statusFilter = document.getElementById("statusFilter");
  const orderRows = document.querySelectorAll(".order-row");
  const orderCount = document.getElementById("order-count");

  function filterOrders() {
    const searchTerm = searchInput.value.toLowerCase();
    const statusValue = statusFilter.value;
    let visibleCount = 0;

    orderRows.forEach((row) => {
      const rowStatus = row.getAttribute("data-status");
      const clientName = row.getAttribute("data-client");
      const clientPhone = row.getAttribute("data-phone");
      const productName = row.getAttribute("data-product");

      // Vérifier le statut
      const statusMatch = statusValue === "all" || rowStatus === statusValue;

      // Vérifier la recherche
      const searchMatch =
        searchTerm === "" ||
        clientName.includes(searchTerm) ||
        clientPhone.includes(searchTerm) ||
        productName.includes(searchTerm);

      // Afficher ou masquer la ligne
      if (statusMatch && searchMatch) {
        row.style.display = "";
        visibleCount++;
      } else {
        row.style.display = "none";
      }
    });

    // Mettre à jour le compteur
    orderCount.textContent = visibleCount;
  }

  // Écouter les événements
  searchInput.addEventListener("input", filterOrders);
  statusFilter.addEventListener("change", filterOrders);
});
