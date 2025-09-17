<!DOCTYPE html>
<html lang="en">

<head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>produit</title>
      <link rel="stylesheet" href="assets/css/product2.css">
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

</head>

<body>
      <div class="container">
            <div class="row justify-content-center">
                  <div class="col-12 col-md-10 col-lg-8">
                        <div class="product">

                              <!-- Image Principale -->
                              <div class="mainImage">
                                    <div class="card">
                                          <img src="uploads/main/1756941574_OIP.webp" class="card-img-top" alt="Image principale">
                                    </div>
                              </div>

                              <!-- Carousel -->
                              <div class="carousel-container text-center">
                                    <div id="carouselExample" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3000">
                                          <div class="carousel-inner">
                                                <div class="carousel-item active">
                                                      <img src="uploads/main/1756941574_OIP.webp" class="d-block w-100" alt="...">
                                                </div>
                                                <div class="carousel-item">
                                                      <img src="uploads/main/1756941594_images.jpeg" class="d-block w-100" alt="...">
                                                </div>
                                                <div class="carousel-item">
                                                      <img src="uploads/main/1757011115_medoc1.jpeg" class="d-block w-100" alt="...">
                                                </div>
                                          </div>
                                          <!-- Contrôles -->
                                          <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
                                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                <span class="visually-hidden">Précédent</span>
                                          </button>
                                          <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
                                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                <span class="visually-hidden">Suivant</span>
                                          </button>
                                    </div>

                                    <!-- Miniatures -->
                                    <div class="carousel-thumbnails d-flex justify-content-center mt-3">
                                          <img src="uploads/main/1756941574_OIP.webp" data-bs-target="#carouselExample" data-bs-slide-to="0" class="img-thumbnail active-thumb" alt="Miniature 1">
                                          <img src="uploads/main/1756941594_images.jpeg" data-bs-target="#carouselExample" data-bs-slide-to="1" class="img-thumbnail" alt="Miniature 2">
                                          <img src="uploads/main/1757011115_medoc1.jpeg" data-bs-target="#carouselExample" data-bs-slide-to="2" class="img-thumbnail" alt="Miniature 3">
                                    </div>
                              </div>

                              <!--Nos Offres-->
                              <div class="packs container my-4">
                                    <h3 class="mb-4 text-center">Nos Offres</h3>
                                    <div class="row justify-content-center">

                                          <!-- Pack 1 -->
                                          <div class="col-md-4">
                                                <label class="pack-card">
                                                      <input type="radio" name="pack" value="pack1" hidden>
                                                      <div class="card shadow-sm">
                                                            <div class="selection-indicator"></div>
                                                            <div class="card-body text-center">
                                                                  <h5 class="card-title">Pack 1</h5>
                                                                  <div class="pack-img-container">
                                                                        <img src="https://images.unsplash.com/photo-1560472354-b33ff0c44a43?w=400&h=300&fit=crop&crop=center" class="pack-img" alt="Pack 1">
                                                                  </div>
                                                            </div>
                                                            <div class="price-container text-center">
                                                                  <div class="price-reduction">
                                                                        <p><strong>30 000 FCFA</strong></p>
                                                                  </div>
                                                                  <div class="price-main">
                                                                        <p><strike>60 000 FCFA</strike></p>
                                                                  </div>
                                                            </div>
                                                      </div>
                                                </label>
                                          </div>

                                          <!-- Pack 2 (Recommandé) -->
                                          <div class="col-md-4">
                                                <label class="pack-card">
                                                      <input type="radio" name="pack" value="pack2" hidden checked>
                                                      <div class="card shadow-sm recommended">
                                                            <div class="badge-reco">
                                                                  <i class="fas fa-crown"></i>
                                                                  RECOMMANDÉ
                                                            </div>
                                                            <div class="selection-indicator"></div>
                                                            <div class="sparkle" style="top: 30%; right: 15%;"><i class="fas fa-sparkles"></i></div>
                                                            <div class="sparkle" style="top: 60%; left: 10%; animation-delay: 1s;"><i class="fas fa-star"></i></div>
                                                            <div class="card-body text-center">
                                                                  <h5 class="card-title">Pack 2</h5>
                                                                  <div class="pack-img-container">
                                                                        <img src="https://images.unsplash.com/photo-1560472354-b33ff0c44a43?w=400&h=300&fit=crop&crop=center" class="pack-img" alt="Pack 2">
                                                                  </div>
                                                            </div>
                                                            <div class="price-container text-center">
                                                                  <div class="price-reduction">
                                                                        <p><strong>85 000 FCFA</strong></p>
                                                                  </div>
                                                                  <div class="price-main">
                                                                        <p><strike>100 000 FCFA</strike></p>
                                                                  </div>
                                                            </div>
                                                      </div>
                                                </label>
                                          </div>

                                          <!-- Pack 3 -->
                                          <div class="col-md-4">
                                                <label class="pack-card">
                                                      <input type="radio" name="pack" value="pack3" hidden>
                                                      <div class="card shadow-sm">
                                                            <div class="selection-indicator"></div>
                                                            <div class="card-body text-center">
                                                                  <h5 class="card-title">Pack 3</h5>
                                                                  <div class="pack-img-container">
                                                                        <img src="https://images.unsplash.com/photo-1560472354-b33ff0c44a43?w=400&h=300&fit=crop&crop=center" class="pack-img" alt="Pack 3">
                                                                  </div>
                                                            </div>
                                                            <div class="price-container text-center">
                                                                  <div class="price-reduction">
                                                                        <p><strong>150 000 FCFA</strong></p>
                                                                  </div>
                                                                  <div class="price-main">
                                                                        <p><strike>200 000 FCFA</strike></p>
                                                                  </div>
                                                            </div>
                                                      </div>
                                                </label>
                                          </div>
                                    </div>
                              </div>

                              <!--formulaire de commande-->
                              <div class="container my-2">
                                    <div class="formulaire-card">
                                          <h3>Commander ce produit</h3>

                                          <form class="row g-3" method="POST" action="traitement_commande.php">

                                                <div class="col-md-12">
                                                      <label for="inputName" class="form-label">Nom</label>
                                                      <input type="text" class="form-control" id="inputName" name="nom" placeholder="Votre nom" required>
                                                </div>

                                                <div class="col-md-12">
                                                      <label for="inputNumero" class="form-label">Numéro</label>
                                                      <input type="text" class="form-control" id="inputNumero" name="numero" placeholder="Votre numéro" required>
                                                </div>

                                                <div class="col-12">
                                                      <label for="inputAddress" class="form-label">Adresse</label>
                                                      <input type="text" class="form-control" id="inputAddress" name="adresse" placeholder="1234 Main St" required>
                                                </div>

                                                <div class="col-12">
                                                      <label for="inputNote" class="form-label">Note</label>
                                                      <textarea class="form-control" id="inputNote" name="note" placeholder="Votre note" rows="3"></textarea>
                                                </div>

                                                <div class="d-grid gap-2">
                                                      <button class="btn btn-commande" type="submit">Commander</button>
                                                </div>
                                          </form>
                                    </div>
                              </div>

                              <!--Caractéristiques-->
                              <div class="container my-4">
                                    <h4 class="mb-4">Caractéristiques</h4>
                                    <div class="row g-4">

                                          <div class="col-md-6">
                                                <div class="card h-100 shadow-sm border-0 custom-card">
                                                      <div class="card-body">
                                                            <h5 class="card-title">Titre</h5>
                                                            <p class="card-text">Description de la caractéristique.</p>
                                                      </div>
                                                </div>
                                          </div>

                                          <div class="col-md-6">
                                                <div class="card h-100 shadow-sm border-0 custom-card">
                                                      <div class="card-body">
                                                            <h5 class="card-title">Titre</h5>
                                                            <p class="card-text">Description de la caractéristique.</p>
                                                      </div>
                                                </div>
                                          </div>

                                          <div class="col-md-6">
                                                <div class="card h-100 shadow-sm border-0 custom-card">
                                                      <div class="card-body">
                                                            <h5 class="card-title">Titre</h5>
                                                            <p class="card-text">Description de la caractéristique.</p>
                                                      </div>
                                                </div>
                                          </div>

                                          <div class="col-md-6">
                                                <div class="card h-100 shadow-sm border-0 custom-card">
                                                      <div class="card-body">
                                                            <h5 class="card-title">Titre</h5>
                                                            <p class="card-text">Description de la caractéristique.</p>
                                                      </div>
                                                </div>
                                          </div>

                                    </div>
                              </div>

                              <!--Videos-->
                              <div class="container my-2">
                                    <h4 class="mb-4">Vidéos</h4>
                                    <div class="row g-4">

                                          <div class="col-md-6">
                                                <div class="card shadow-sm border-0 video-card">
                                                      <video class="card-img-top" src="uploads/videos/1756947320_WhatsApp Video 2025-08-31 at 11.54.51.mp4" controls></video>
                                                      <div class="card-body">
                                                            <p class="card-text">Description de la vidéo.</p>
                                                      </div>
                                                </div>
                                          </div>

                                          <div class="col-md-6">
                                                <div class="card shadow-sm border-0 video-card">
                                                      <video class="card-img-top" src="uploads/videos/1756947320_WhatsApp Video 2025-08-31 at 11.54.51.mp4" controls></video>
                                                      <div class="card-body">
                                                            <p class="card-text">Description de la vidéo.</p>
                                                      </div>
                                                </div>
                                          </div>

                                    </div>
                              </div>

                              <!--Descriptions-->
                              <div class="descriptions">
                                    <h4>Descriptions</h4>
                                    <div class="card" style="width: 100%;">
                                          <div class="card-body">
                                                <h1><strong>Puissance et fiabilité avec Dell</strong></h1>
                                                <p>Découvrez la performance au service de votre quotidien avec le <strong>PC Dell</strong>. Conçu pour allier <strong>vitesse, élégance et robustesse</strong>, ce PC répond parfaitement aux besoins des professionnels comme des particuliers.</p>
                                                <p>Grâce à ses <strong>processeurs de dernière génération</strong>, une <strong>mémoire vive performante</strong> et un <strong>espace de stockage généreux</strong>, il vous garantit une fluidité exceptionnelle, même lors des tâches les plus exigeantes.</p>
                                                <p>Le design moderne et épuré de Dell apporte une touche de style à votre espace de travail, tandis que la qualité de fabrication Dell assure une <strong>durabilité à toute épreuve</strong>. Que ce soit pour le télétravail, les études, le divertissement ou le gaming léger, le PC Dell est votre allié au quotidien.</p>
                                                <p><strong>Pourquoi choisir Dell ?</strong></p>
                                                <ul>
                                                      <li>Performance rapide et fiable</li>
                                                      <li>Conception robuste et durable</li>
                                                      <li>Idéal pour le travail, l’étude et le multimédia</li>
                                                      <li>Service et garantie Dell reconnus mondialement</li>
                                                </ul>
                                                <p><strong>Offrez-vous l’efficacité et la tranquillité d’esprit avec un PC Dell – la technologie au service de vos ambitions.</strong></p>
                                          </div>
                                    </div>
                              </div>
                        </div>
                  </div>
            </div>






            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
            <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
            <script>
                  document.addEventListener("DOMContentLoaded", () => {
                        const thumbnails = document.querySelectorAll(".carousel-thumbnails img");
                        const carousel = document.querySelector("#carouselExample");

                        carousel.addEventListener("slid.bs.carousel", (e) => {
                              thumbnails.forEach(img => img.classList.remove("active-thumb"));
                              thumbnails[e.to].classList.add("active-thumb");
                        });
                  });
            </script>

</body>

</html>