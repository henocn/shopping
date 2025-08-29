<?php

namespace src;

use PDO;
use PDOException;

class Product
{
    private $bd;

    public function __construct(PDO $bd)
    {
        $this->bd = $bd;
    }

    /**
     *Methodes get en private pour l'instant
     */
    
    private function getProdiuts($id){

    }
    
    /**
     * Fonctions de creation des differents éléments liée au produit
     */


    private function createProduct($data){
        $req = $this->bd->prepare("INSERT INTO products (name, price, quantity, image, category_id, status) VALUES (:name, :price, :quantity, :image, :category_id, :status)");
        $req->execute([
            'name'   => $data['name'],
            'price'    => $data['price'],
            'quantity' => $data['quantity'],
            'image'     => $data['image'],
            'category_id'  => $data['category_id'],
            'status'  => $data['status'],
        ]);
        
        // Retourner l'ID du produit créé
        return $this->bd->lastInsertId();
    }

    private function createAdditionalDescriptions($data){
        $req = $this->bd->prepare("INSERT INTO product_additional_descriptions (product_id, titre, texte, is_active) VALUES (:product_id, :titre, :texte, :is_active)");
        $req->execute([
            'product_id' => $data['id'],
            'titre' => $data['titre'],
            'texte' => $data['texte'],
            'is_active' => $data['is_active'],
        ]);
    }

    private function createCaracteristics($data){
        $req = $this->bd->prepare("INSERT INTO product_caracteristics (product_id, titre, image, description) VALUES (:product_id, :titre, :image, :description)");
        $req->execute([
            'product_id' => $data['id'],
            'titre' => $data['titre'],
            'image' => $data['image'],
            'description' => $data['description'],
        ]);
    }


    private function createCarousel($data){
        $req = $this->bd->prepare("INSERT INTO product_carousel (product_id, image_url, is_active) VALUES (:product_id, :image_url, :is_active)");
        $req->execute([
            'product_id' => $data['id'],
            'image_url' => $data['image_url'],
            'is_active' => $data['is_active'],
        ]);
    }

    private function createFunctionalities($data){
        $req = $this->bd->prepare("INSERT INTO product_functionalities (product_id, image, titre, elements_1, elements_2, elements_3, elements_4, functionality_order, is_active) VALUES (:product_id, :image, :titre, :elements_1, :elements_2, :elements_3, :elements_4, :functionality_order, :is_active)");
        $req->execute([
            'product_id' => $data['id'],
            'image' => $data['image'],
            'titre' => $data['titre'],
            'elements_1' => $data['elements_1'],
            'elements_2' => $data['elements_2'],
            'elements_3' => $data['elements_3'],
            'elements_4' => $data['elements_4'],
            'functionality_order' => $data['functionality_order'],
            'is_active' => $data['is_active'],
        ]);
    }


    private function createMentions($data){
        $req = $this->bd->prepare("INSERT INTO product_mentions (product_id, video_url, texte, is_active) VALUES (:product_id, :video_url, :texte, :is_active)");
        $req->execute([
            'product_id' => $data['id'],
            'video_url' => $data['video_url'],
            'texte' => $data['texte'],
            'is_active' => $data['is_active'],
        ]);
    }



    private function createDescriptiveImages($data){
        $req = $this->bd->prepare("INSERT INTO product_descriptive_images (product_id, image_url) VALUES (:product_id, :image_url)");
        $req->execute([
            'product_id' => $data['id'],
            'image_url' => $data['image_url'],
        ]);
    }



    private function createPacks($data){
        $req = $this->bd->prepare("INSERT INTO product_packs (product_id, pack_order, titre, description, quantite, image, price_reduction, price_normal, is_active) VALUES (:product_id, :pack_order, :titre, :description, :quantite, :image, :price_reduction, :price_normal, :is_active)");
        $req->execute([
            'product_id' => $data['id'],
            'pack_order' => $data['pack_order'],
            'titre' => $data['titre'],
            'description' => $data['description'],
            'quantite' => $data['quantite'],
            'image' => $data['image'],
            'price_reduction' => $data['price_reduction'],
            'price_normal' => $data['price_normal'],
            'is_active' => $data['is_active'],
        ]);
    }


    private function createQualities($data){
        $req = $this->bd->prepare("INSERT INTO product_qualities (product_id, image, element_1, element_2, element_3, element_4, element_5, is_active) VALUES (:product_id, :image, :element_1, :element_2, :element_3, :element_4, :element_5, :is_active)");
        $req->execute([
            'product_id' => $data['id'],
            'image' => $data['image'],
            'element_1' => $data['element_1'],
            'element_2' => $data['element_2'],
            'element_3' => $data['element_3'],
            'element_4' => $data['element_4'],
            'element_5' => $data['element_5'],
            'is_active' => $data['is_active'],
        ]);
    }


    private function createRecommandation($data){
        $req = $this->bd->prepare("INSERT INTO product_recommandation (product_id, image, conseil_texte, is_active) VALUES (:product_id, :image, :conseil_texte, :is_active)");
        $req->execute([
            'product_id' => $data['id'],
            'image' => $data['image'],
            'conseil_texte' => $data['conseil_texte'],
            'is_active' => $data['is_active'],
        ]);
    }


    

    

    public function createProduits($data){
        if($data != null){
            // 1. Créer le produit principal et récupérer son ID
            $productId = $this->createProduct($data['product']);
            
            // 2. Créer les éléments associés avec l'ID du produit
            if(isset($data['additional_descriptions']) && is_array($data['additional_descriptions'])){
                foreach($data['additional_descriptions'] as $description){
                    $description['id'] = $productId;
                    $this->createAdditionalDescriptions($description);
                }
            }
            
            if(isset($data['caracteristics']) && is_array($data['caracteristics'])){
                foreach($data['caracteristics'] as $caracteristic){
                    $caracteristic['id'] = $productId;
                    $this->createCaracteristics($caracteristic);
                }
            }
            
            if(isset($data['carousel']) && is_array($data['carousel'])){
                foreach($data['carousel'] as $carouselItem){
                    $carouselItem['id'] = $productId;
                    $this->createCarousel($carouselItem);
                }
            }
            
            if(isset($data['functionalities']) && is_array($data['functionalities'])){
                foreach($data['functionalities'] as $functionality){
                    $functionality['id'] = $productId;
                    $this->createFunctionalities($functionality);
                }
            }
            
            if(isset($data['mentions']) && is_array($data['mentions'])){
                foreach($data['mentions'] as $mention){
                    $mention['id'] = $productId;
                    $this->createMentions($mention);
                }
            }
            
            if(isset($data['descriptive_images']) && is_array($data['descriptive_images'])){
                foreach($data['descriptive_images'] as $image){
                    $image['id'] = $productId;
                    $this->createDescriptiveImages($image);
                }
            }
            
            if(isset($data['packs']) && is_array($data['packs'])){
                foreach($data['packs'] as $pack){
                    $pack['id'] = $productId;
                    $this->createPacks($pack);
                }
            }
            
            if(isset($data['qualities']) && is_array($data['qualities'])){
                foreach($data['qualities'] as $quality){
                    $quality['id'] = $productId;
                    $this->createQualities($quality);
                }
            }
            
            if(isset($data['recommandation']) && is_array($data['recommandation'])){
                foreach($data['recommandation'] as $recommandation){
                    $recommandation['id'] = $productId;
                    $this->createRecommandation($recommandation);
                }
            }
            
            return $productId;
        }else{
            return "Données invalides";
        }
    }

}    