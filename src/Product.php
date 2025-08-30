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


    public function createProduct($data){
        $req = $this->bd->prepare("INSERT INTO products (name, price, quantity, image,description, status) VALUES (:name, :price, :quantity, :image, :description, :status)");
        $req->execute([
            'name'   => $data['name'],
            'price'    => $data['price'],
            'quantity' => $data['quantity'],
            'image'     => $data['image'],
            'description' => $data['description'],
            'status'  => $data['status'],
        ]);
    }


    public function createCarousel($data){
        $req = $this->bd->prepare("UPDATE products SET carousel1 = :carousel1, carousel2 = :carousel2, carousel3 = :carousel3, carousel4 = :carousel4, carousel5 = :carousel5 WHERE product_id = :id
        ");

        $req->execute([
            'carousel1' => $data['carousel1'],
            'carousel2' => $data['carousel2'],
            'carousel3' => $data['carousel3'],
            'carousel4' => $data['carousel4'],
            'carousel5' => $data['carousel5'],
            'id'        => $data['id'],
        ]);
    }

    public function createVideos($data){
        $req = $this->bd->prepare("INSERT INTO product_mentions (product_id, video_url, texte) VALUES (:product_id, :video_url, :texte)");
        $req->execute([ 
            'product_id' => $data['id'],
            'video_url' => $data['video_url'],
            'texte' => $data['texte'],
        ]);
    }



    
    public function createPacks($data){
        $req = $this->bd->prepare("INSERT INTO product_packs (product_id, pack_order, titre, description, quantite, image, price_reduction, price_normal, is_active) VALUES (:product_id, :pack_order, :titre, :description, :quantite, :image, :price_reduction, :price_normal, :is_active)");
        $req->execute([
            'product_id' => $data['id'],
            'titre' => $data['titre'],
            'description' => $data['description'],
            'quantity' => $data['quantity'],
            'price_reduction' => $data['price_reduction'],
            'price_normal' => $data['price_normal'],
        ]);
    }


    public function createCaracteristics($data){
        $req = $this->bd->prepare("INSERT INTO product_caracteristics (product_id, title,image ,description) VALUES (:product_id, :title,:image , :description)");
        $req->execute([
            'product_id' => $data['id'],
            'title' => $data['title'],
            'image' => $data['image'],
            'description' => $data['description'],
        ]);
    }


    


    

    

    

}    