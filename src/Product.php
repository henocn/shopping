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

    // Récupérer une caractéristique par son ID
    public function getCaracteristicById($id)
    {
        $stmt = $this->bd->prepare("SELECT * FROM product_caracteristics WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getVideos($product_id)
    {
        $stmt = $this->bd->prepare("SELECT * FROM product_video WHERE product_id = :product_id");
        $stmt->execute(['product_id' => $product_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer une vidéo par son ID
    public function getVideoById($id)
    {
        $stmt = $this->bd->prepare("SELECT * FROM product_video WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getPacks($product_id)
    {
        $stmt = $this->bd->prepare("SELECT * FROM product_packs WHERE product_id = :product_id");
        $stmt->execute(['product_id' => $product_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer un pack par son ID
    public function getPackById($id)
    {
        $stmt = $this->bd->prepare("SELECT * FROM product_packs WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }




    public function getAllProductInfoById($product_id)
    {
        $product = $this->getProducts($product_id);
        $videos = $this->getVideos($product_id);
        $caracteristics = $this->getCaracteristics($product_id);
        $packs = $this->getPacks($product_id);

        return [
            'product' => $product,
            'videos' => $videos,
            'caracteristics' => $caracteristics,
            'packs' => $packs
        ];
    }


    /**
     * Fonctions de creation des differents éléments liée au produit
     */


    public function createProduct($data)
    {
        $req = $this->bd->prepare("INSERT INTO products (name, ar_name, purchase_price, shipping_price, quantity, image, description, ar_description, carousel1, carousel2, carousel3, carousel4, carousel5) VALUES (:name, :ar_name, :purchase_price, :shipping_price, :quantity, :image, :description, :ar_description, :carousel1, :carousel2, :carousel3, :carousel4, :carousel5)");
        $req->execute([
            'name'   => $data['name'],
            'ar_name' => $data['ar_name'] ?? '',
            'purchase_price'    => $data['purchase_price'],
            'shipping_price'    => $data['shipping_price'],
            'quantity'          => $data['quantity'],
            'image'     => $data['image'],
            'description' => $data['description'],
            'ar_description' => $data['ar_description'] ?? '',
            'carousel1' => $data['carousel1'] ?? '',
            'carousel2' => $data['carousel2'] ?? '',
            'carousel3' => $data['carousel3'] ?? '',
            'carousel4' => $data['carousel4'] ?? '',
            'carousel5' => $data['carousel5'] ?? '',
        ]);
        
        $lastProductId = $this->GetLastProductId();
        
        // Ajouter les managers si fournis
        if (isset($data['manager_ids']) && is_array($data['manager_ids'])) {
            foreach ($data['manager_ids'] as $manager_id) {
                $this->addProductManager($lastProductId, $manager_id);
            }
        }
        
        // Ajouter les pays avec prix de vente si fournis
        if (isset($data['product_countries']) && is_array($data['product_countries'])) {
            foreach ($data['product_countries'] as $country_data) {
                $this->addProductCountry($lastProductId, $country_data['country_id'], $country_data['selling_price']);
            }
        }
    }
    
    public function addProductManager($productId, $managerId)
    {
        $req = $this->bd->prepare("INSERT INTO product_managers (product_id, manager_id) VALUES (:product_id, :manager_id)");
        $req->execute([
            'product_id' => $productId,
            'manager_id' => $managerId
        ]);
    }
    
    public function addProductCountry($productId, $countryId, $sellingPrice)
    {
        $req = $this->bd->prepare("INSERT INTO product_countries (product_id, country_id, selling_price) VALUES (:product_id, :country_id, :selling_price)");
        $req->execute([
            'product_id' => $productId,
            'country_id' => $countryId,
            'selling_price' => $sellingPrice
        ]);
    }
    
    public function removeProductManager($productId, $managerId)
    {
        $req = $this->bd->prepare("DELETE FROM product_managers WHERE product_id = :product_id AND manager_id = :manager_id");
        return $req->execute([
            'product_id' => $productId,
            'manager_id' => $managerId
        ]);
    }
    
    public function removeProductCountry($productId, $countryId)
    {
        $req = $this->bd->prepare("DELETE FROM product_countries WHERE product_id = :product_id AND country_id = :country_id");
        return $req->execute([
            'product_id' => $productId,
            'country_id' => $countryId
        ]);
    }
    
    public function getProductManagers($productId)
    {
        $req = $this->bd->prepare("SELECT u.id, u.name, u.email, u.country FROM users u INNER JOIN product_managers pm ON u.id = pm.manager_id WHERE pm.product_id = :product_id");
        $req->execute(['product_id' => $productId]);
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getProductCountries($productId)
    {
        $req = $this->bd->prepare("SELECT c.id, c.code, c.name, pc.selling_price FROM countries c INNER JOIN product_countries pc ON c.id = pc.country_id WHERE pc.product_id = :product_id");
        $req->execute(['product_id' => $productId]);
        return $req->fetchAll(PDO::FETCH_ASSOC);
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
        $req = $this->bd->prepare("INSERT INTO product_packs (product_id, name, quantity, image, price) VALUES (:product_id, :name, :quantity, :image, :price)");
        $req->execute([
            'product_id' => $data['product_id'],
            'name' => $data['pack_name'],
            'quantity' => $data['pack_quantity'],
            'image' => $data['pack_image'],
            'price' => $data['pack_price']
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
            $tables = ['product_caracteristics', 'product_video', 'product_packs', 'product_managers', 'product_countries'];
            foreach ($tables as $table) {
                $stmt = $this->bd->prepare("DELETE FROM $table WHERE product_id = :id");
                $stmt->execute(['id' => $productId]);
            }

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
        $stmt = $this->bd->prepare("SELECT p.id AS product_id, p.name, p.ar_name, p.image, p.description, p.ar_description 
        FROM products p 
        ORDER BY p.id DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    
    // une fonction qui renvoi a un produit au hazar dans la base de donnée
    public function getRandomProduct(){
        $stmt = $this->bd->prepare("SELECT p.id AS product_id, p.name, p.ar_name, p.image, p.description, p.ar_description
        FROM products p 
        ORDER BY RAND() 
        LIMIT 1");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
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

    public function getProductPacks($productId) {
        $stmt = $this->bd->prepare("
            SELECT * FROM product_packs 
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
        $req = $this->bd->prepare("UPDATE products SET name = :name, ar_name = :ar_name, purchase_price = :purchase_price, shipping_price = :shipping_price, quantity = :quantity, image = :image, description = :description, ar_description = :ar_description, carousel1 = :carousel1, carousel2 = :carousel2, carousel3 = :carousel3, carousel4 = :carousel4, carousel5 = :carousel5 WHERE id = :id");
        $req->execute([
            'id' => $productId,
            'name'   => $data['name'],
            'ar_name' => $data['ar_name'] ?? '',
            'purchase_price'    => $data['purchase_price'],
            'shipping_price'    => $data['shipping_price'],
            'quantity'          => $data['quantity'],
            'image'     => $data['image'],
            'description' => $data['description'],
            'ar_description' => $data['ar_description'] ?? '',
            'carousel1' => $data['carousel1'] ?? '',
            'carousel2' => $data['carousel2'] ?? '',
            'carousel3' => $data['carousel3'] ?? '',
            'carousel4' => $data['carousel4'] ?? '',
            'carousel5' => $data['carousel5'] ?? ''
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

    public function updatePack($packId, $data)
    {
        $req = $this->bd->prepare("UPDATE product_packs SET name = :name, quantity = :quantity, image = :image, price = :price WHERE id = :id");
        $req->execute([
            'name' => $data['pack_name'],
            'quantity' => $data['pack_quantity'],
            'image' => $data['pack_image'],
            'price' => $data['pack_price'],
            'id' => $packId
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

    public function decrementQuantity(int $productId, int $amount)
    {
        $amount = max(0, (int)$amount);
        if ($amount === 0) {
            return true;
        }

        $sql = "UPDATE products SET quantity = CASE WHEN quantity >= :amount THEN quantity - :amount ELSE 0 END WHERE id = :id";
        $req = $this->bd->prepare($sql);
        return $req->execute([
            'amount' => $amount,
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

    public function deletePacks($id)
    {
        $stmt = $this->bd->prepare("DELETE FROM product_packs WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

}
