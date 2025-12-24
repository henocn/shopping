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