document.addEventListener("DOMContentLoaded", function () {
  const toastEl = document.getElementById("liveToast");
  const toastBody = document.getElementById("toastMessage");

  const message = toastBody.textContent.trim();

  if (message.length > 0) {
    toastEl.className = "toast align-items-center text-white border-0";
    toastEl.classList.add(/succès|success/i.test(message) ? "bg-success" : "bg-danger");

    const toast = new bootstrap.Toast(toastEl);
    toast.show();
  }
});
