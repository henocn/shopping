// Gestion de la sélection des packs
function selectPack(packId, packPrice, quantity) {
  document.querySelectorAll(".pack-card").forEach((card) => {
    card.classList.remove("selected");
  });

  event.currentTarget.classList.add("selected");

  const select = document.getElementById("packSelection");
  if (select) {
    select.value = packId;
  }
  document.getElementById("selectedPackId").value = packId;

  const packInfo = document.getElementById("selectedPackInfo");
  const packName = document.getElementById("packName");
  const packQuantity = document.getElementById("packQuantity");
  const packPriceElement = document.getElementById("packPrice");

  const selectedCard = event.currentTarget;
  const name = selectedCard.querySelector(".pack-name").textContent;

  packName.textContent = name;
  packQuantity.textContent = quantity + " unités";
  packPriceElement.textContent =
    new Intl.NumberFormat("fr-FR").format(packPrice) + " FCFA";

  packInfo.style.display = "block";

  if (typeof trackEvent !== 'undefined') {
    trackEvent('ViewContent', {
      content_ids: [packId.toString()],
      content_type: 'product',
      contents: [{
        'id': packId.toString(),
        'quantity': quantity,
        'item_price': packPrice
      }],
      currency: 'XOF',
      value: packPrice
    });

    trackEvent('PackSelected', {
      content_ids: [packId.toString()],
      content_name: name,
      value: packPrice,
      currency: 'XOF',
      quantity: quantity,
      pack_type: 'product_bundle'
    });
  }

  openOrderForm();
}

// Ouverture du formulaire de commande via la fonction globale si disponible
function openOrderForm() {
  if (typeof window.openOrderForm === 'function') {
    window.openOrderForm();
    return;
  }
  const modal = new bootstrap.Modal(document.getElementById('orderModal'));
  modal.show();
}

// Fonction pour mettre à jour la sélection de pack via le dropdown
function updatePackSelection() {
  const select = document.getElementById("packSelection");
  const selectedOption = select.options[select.selectedIndex];

  if (selectedOption.value === "") {
    document.getElementById("selectedPackId").value = "";
    document.getElementById("selectedPackInfo").style.display = "none";

    document.querySelectorAll(".pack-card").forEach((card) => {
      card.classList.remove("selected");
    });
  } else {
    const packId = selectedOption.value;
    const packPrice = selectedOption.dataset.price;
    const packQuantity = selectedOption.dataset.quantity;
    const packName = selectedOption.dataset.name;

    document.getElementById("selectedPackId").value = packId;

    const packInfo = document.getElementById("selectedPackInfo");
    const packNameElement = document.getElementById("packName");
    const packQuantityElement = document.getElementById("packQuantity");
    const packPriceElement = document.getElementById("packPrice");

    packNameElement.textContent = packName;
    packQuantityElement.textContent = packQuantity + " unités";
    packPriceElement.textContent =
      new Intl.NumberFormat("fr-FR").format(packPrice) + " FCFA";

    packInfo.style.display = "block";

    if (typeof trackEvent !== 'undefined') {
      trackEvent('PackSelectedDropdown', {
        content_ids: [packId],
        content_name: packName,
        value: parseInt(packPrice),
        currency: 'XOF',
        quantity: parseInt(packQuantity),
        selection_method: 'dropdown'
      });
    }

    document.querySelectorAll(".pack-card").forEach((card) => {
      card.classList.remove("selected");
      if (card.dataset.packId === packId) {
        card.classList.add("selected");
      }
    });
  }
}
