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

    public function GetLastProductId()
    {
        $sql = "SELECT MAX(id) AS product_id FROM products";
        $req = $this->bd->prepare($sql);
        $req->execute();
        $result = $req->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['product_id'] : null;
    }


    public function getProducts($id)
    {
        $stmt = $this->bd->prepare("SELECT * FROM products WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Fonctions de creation des differents éléments liée au produit
     */


    public function createProduct($data)
    {
        $req = $this->bd->prepare("INSERT INTO products (name, price, quantity, image,description, status, carousel1, carousel2, carousel3, carousel4, carousel5) VALUES (:name, :price, :quantity, :image, :description, :status, :carousel1, :carousel2, :carousel3, :carousel4, :carousel5)");
        $req->execute([
            'name'   => $data['name'],
            'price'    => $data['price'],
            'quantity' => $data['quantity'],
            'image'     => $data['image'],
            'description' => $data['description'],
            'status'  => $data['status'],
            'carousel1' => $data['carousel1'],
            'carousel2' => $data['carousel2'],
            'carousel3' => $data['carousel3'],
            'carousel4' => $data['carousel4'],
            'carousel5' => $data['carousel5'],
        ]);
    }


    public function createVideos($data)
    {
        $req = $this->bd->prepare("
        INSERT INTO product_video (product_id, video_url, texte) 
        VALUES (:product_id, :video_url, :texte)
    ");
        $req->execute([
            'product_id' => $data['product_id'],
            'video_url'  => $data['video_url'],
            'texte'      => $data['texte'],
        ]);
    }


    public function createPacks($data)
    {
        $req = $this->bd->prepare("INSERT INTO product_packs (product_id, titre, description, quantite, price_reduction, price_normal) VALUES (:product_id, :titre, :description, :quantite, :price_reduction, :price_normal)");
        $req->execute([
            'product_id' => $data['product_id'],
            'titre' => $data['titre'],
            'description' => $data['description'],
            'quantity' => $data['quantity'],
            'price_reduction' => $data['price_reduction'],
            'price_normal' => $data['price_normal'],
        ]);
    }

    public function createCaracteristics($data)
    {
        $req = $this->bd->prepare("INSERT INTO product_caracteristics (product_id, title,image ,description) VALUES (:product_id, :title,:image , :description)");
        $req->execute([
            'product_id' => $data['product_id'],
            'title' => $data['title'],
            'image' => $data['image'],
            'description' => $data['description'],
        ]);
    }


    public function getAllProducts()
    {
        $stmt = $this->bd->prepare("SELECT `products`.`id` AS `product_id`, `products`.`name`, `products`.`price`, `products`.`quantity`, `products`.`image`, `products`.`description`, `products`.`status`
FROM `products` ORDER BY id DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
