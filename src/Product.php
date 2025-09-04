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

    public function getTotalProducts()
    {
        $query = "SELECT COUNT(*) as total FROM products";
        $stmt = $this->bd->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$result['total'];
    }

    public function getAvailableProducts()
    {
        $query = "SELECT COUNT(*) as total FROM products WHERE status = 1";
        $stmt = $this->bd->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$result['total'];
    }


    public function getProducts($id)
    {
        $stmt = $this->bd->prepare("SELECT * FROM products WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getCaracteristics($product_id)
    {
        $stmt = $this->bd->prepare("SELECT * FROM product_caracteristics WHERE product_id = :product_id");
        $stmt->execute(['product_id' => $product_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getVideos($product_id)
    {
        $stmt = $this->bd->prepare("SELECT * FROM product_video WHERE product_id = :product_id");
        $stmt->execute(['product_id' => $product_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPacks($product_id)
    {
        $stmt = $this->bd->prepare("SELECT * FROM product_packs WHERE product_id = :product_id");
        $stmt->execute(['product_id' => $product_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }




    public function getAllProductInfoById($product_id)
    {
        $product = $this->getProducts($product_id);
        $videos = $this->getVideos($product_id);
        $caracteristics = $this->getCaracteristics($product_id);

        return [
            'product' => $product,
            'videos' => $videos,
            'caracteristics' => $caracteristics
        ];
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

    public function deleteProduct($productId)
    {
        try {
            $this->bd->beginTransaction();

            // Supprimer d'abord les enregistrements liés
            $tables = ['product_caracteristics', 'product_video', 'product_packs'];
            foreach ($tables as $table) {
                $stmt = $this->bd->prepare("DELETE FROM $table WHERE product_id = :id");
                $stmt->execute(['id' => $productId]);
            }

            // Supprimer le produit
            $stmt = $this->bd->prepare("DELETE FROM products WHERE id = :id");
            $stmt->execute(['id' => $productId]);

            $this->bd->commit();
            return true;
        } catch (PDOException $e) {
            $this->bd->rollBack();
            throw $e;
        }
    }


    public function getAllProducts()
    {
        $stmt = $this->bd->prepare("SELECT `products`.`id` AS `product_id`, `products`.`name`, `products`.`price`, `products`.`quantity`, `products`.`image`, `products`.`description`, `products`.`status`
        FROM `products` ORDER BY id DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getProductCharacteristics($productId) {
        $stmt = $this->bd->prepare("
            SELECT * FROM product_caracteristics 
            WHERE product_id = :id 
            ORDER BY id ASC
        ");
        $stmt->execute(['id' => $productId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProductVideos($productId) {
        $stmt = $this->bd->prepare("
            SELECT * FROM product_video 
            WHERE product_id = :id 
            ORDER BY id ASC
        ");
        $stmt->execute(['id' => $productId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


     /**
     * Fonctions de Mise à jour des differents éléments liée au produit
     */

    public function updateProduct($productId, $data)
    {
        $req = $this->bd->prepare("UPDATE products SET name = :name, price = :price, quantity = :quantity, image = :image, description = :description, carousel1 = :carousel1, carousel2 = :carousel2, carousel3 = :carousel3, carousel4 = :carousel4, carousel5 = :carousel5 WHERE id = :id");
        $req->execute([
            'name'   => $data['name'],
            'price'    => $data['price'],
            'quantity' => $data['quantity'],
            'image'     => $data['image'],
            'description' => $data['description'],
            'carousel1' => $data['carousel1'],
            'carousel2' => $data['carousel2'],
            'carousel3' => $data['carousel3'],
            'carousel4' => $data['carousel4'],
            'carousel5' => $data['carousel5'],
            'id' => $productId
        ]);
    }

    public function updateCaracteristic($characteristicId, $data)
    {
        $req = $this->bd->prepare("UPDATE product_caracteristics SET title = :title, image = :image, description = :description WHERE id = :id");
        $req->execute([
            'title' => $data['title'],
            'image' => $data['image'],
            'description' => $data['description'],
            'id' => $characteristicId
        ]);
    }

    public function updateVideo($videoId, $data)
    {
        $req = $this->bd->prepare("UPDATE product_video SET video_url = :video_url, texte = :texte WHERE id = :id");
        $req->execute([
            'video_url' => $data['video_url'],
            'texte' => $data['texte'],
            'id' => $videoId
        ]);
    }

    public function updateCaracteristics($productId, $data)
    {
        $req = $this->bd->prepare("UPDATE product_caracteristics SET title = :title, image = :image, description = :description WHERE product_id = :product_id");
        $req->execute([
            'title' => $data['title'],
            'image' => $data['image'],
            'description' => $data['description'],
            'product_id' => $productId
        ]);
    }

    public function updateProductStatus($productId, $newStatus)
    {
        $req = $this->bd->prepare("UPDATE products SET status = :status WHERE id = :id");
        $req->execute([
            'status' => $newStatus,
            'id' => $productId
        ]);
    }


    public function deleteCaracteristic($id)
    {
        $stmt = $this->bd->prepare("DELETE FROM product_caracteristics WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function deleteVideo($id)
    {
        $stmt = $this->bd->prepare("DELETE FROM product_video WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

}
