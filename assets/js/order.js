function updateOrdersCount() {
  const count = document.querySelectorAll('[id^="order-card-"]').length;
  const counter = document.getElementById("orders-count");
  if (counter) counter.textContent = count;
}

function updateOrder(event, orderId) {
  event.preventDefault();

  const form = event.target;
  const submitBtn = form.querySelector('button[type="submit"]');
  const originalText = submitBtn.innerHTML;
  submitBtn.innerHTML =
    '<i class="bx bx-loader-alt bx-spin me-2"></i>Enregistrement...';
  submitBtn.disabled = true;

  const formData = new FormData(form);
  formData.set("order_id", orderId);
  formData.set("valider", "update");

  fetch("save.php", {
    method: "POST",
    headers: { "X-Requested-With": "fetch" },
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        const modal = bootstrap.Modal.getInstance(
          document.getElementById("detailsModal" + orderId) ||
            document.getElementById("detailsModalAction" + orderId)
        );
        if (modal) modal.hide();

        location.reload();

        showNotification("Commande mise à jour avec succès", "success");
      } else {
        throw new Error(data.message || "Erreur inconnue");
      }
    })
    .catch((error) => {
      console.error("Erreur:", error);
      showNotification("Erreur lors de la mise à jour", "error");
    })
    .finally(() => {
      submitBtn.innerHTML = originalText;
      submitBtn.disabled = false;
    });
}

function showNotification(message, type = "info") {
  const notification = document.createElement("div");
  notification.className = `alert alert-${
    type === "success" ? "success" : "danger"
  } alert-dismissible fade show position-fixed`;
  notification.style.cssText =
    "top: 20px; right: 20px; z-index: 1060; max-width: 300px;";
  notification.innerHTML = `
                <i class='bx ${
                  type === "success" ? "bx-check-circle" : "bx-error-circle"
                } me-2'></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;

  document.body.appendChild(notification);

  setTimeout(() => notification.remove(), 5000);
}

document.addEventListener("DOMContentLoaded", function () {
  updateOrdersCount();

});

window.updateOrder = updateOrder;
window.showNotification = showNotification;
