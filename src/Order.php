<?php

namespace src;

use PDO;
use PDOException;

class Order
{
    private $bd;

    public function __construct(PDO $bd)
    {
        $this->bd = $bd;
    }

    //ok
    public function getTotalOrders()
    {
        $query = "SELECT COUNT(*) as total FROM orders";
        $stmt = $this->bd->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$result['total'];
    }

    public function getOrdersByStatus($status)
    {
        $query = "SELECT COUNT(*) as total FROM orders WHERE status = :status";
        $stmt = $this->bd->prepare($query);
        $stmt->execute(['status' => $status]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$result['total'];
    }

    public function CreateOrder($data)
    {
        $req = $this->bd->prepare("
        INSERT INTO orders 
        (product_id, pack_id, unit_price, total_price, quantity, client_name, client_country, client_adress, client_phone, client_note, newstat) 
        VALUES 
        (:product_id, :pack_id, :unit_price, :total_price, :quantity, :client_name, :client_country, :client_adress, :client_phone, :client_note, :newstat)
    ");

        $req->execute([
            'product_id'     => (int)($data['product_id'] ?? 0),
            'pack_id'        => (int)($data['pack_id'] ?? 0),
            'unit_price'     => (int)($data['unit_price'] ?? 0),
            'total_price'    => (int)($data['total_price'] ?? 0),
            'quantity'       => (int)($data['quantity'] ?? 1),
            'client_name'    => $data['client_name'] ?? '',
            'client_country' => $data['client_country'],
            'client_adress'  => $data['client_adress'] ?? '',
            'client_phone'   => $data['client_phone'] ?? '',
            'client_note'    => $data['client_note'] ?? null,
            'newstat'         => 'new',
        ]);
        return true;
    }

    public function getAllOrders()
    {
        $sql = "
            SELECT 
                orders.id AS order_id,
                orders.product_id,
                orders.pack_id,
                orders.quantity,
                orders.unit_price,
                orders.total_price,
                orders.client_name,
                orders.client_country,
                orders.client_phone,
                orders.client_adress,
                orders.client_note,
                orders.manager_note,
                orders.newstat,
                orders.created_at,
                orders.updated_at,
                COALESCE(products.name, 'Produit supprimé') AS product_name,
                COALESCE(products.image, '') AS product_image,
                COALESCE(product_packs.titre, '') AS pack_name,
                CASE WHEN orders.quantity > 0 THEN ROUND(orders.total_price / orders.quantity, 2) ELSE orders.total_price END AS unit_price
            FROM orders
            LEFT JOIN products ON products.id = orders.product_id
            LEFT JOIN product_packs ON product_packs.id = orders.pack_id
            ORDER BY orders.id DESC
        ";

        $req = $this->bd->prepare($sql);
        $req->execute();
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateOrder(array $data)
    {
        $sql = "UPDATE orders 
            SET quantity = :quantity,
                total_price = :total_price,
                newstat = :newstat,
                manager_note = :manager_note,
                updated_at = :updated_at
            WHERE id = :id";

        $req = $this->bd->prepare($sql);
        $req->execute([
            'quantity'     => $data['quantity'],
            'total_price'  => $data['total_price'],
            'newstat'      => $data['newstat'],
            'manager_note' => $data['manager_note'],
            'updated_at'   => $data['updated_at'],
            'id'           => $data['id'],
        ]);
    }

    public function getOrdersByUserId($userId)
    {
        $sql = "
            SELECT 
                orders.id AS order_id,
                orders.product_id,
                orders.pack_id,
                orders.quantity,
                orders.unit_price,
                orders.total_price,
                orders.client_name,
                orders.client_country,
                orders.client_phone,
                orders.client_adress,
                orders.client_note,
                orders.manager_note,
                orders.newstat,
                orders.created_at,
                orders.updated_at,
                COALESCE(products.name, 'Produit supprimé') AS product_name,
                COALESCE(products.image, '') AS product_image,
                COALESCE(product_packs.titre, '') AS pack_name,
                CASE WHEN orders.quantity > 0 THEN ROUND(orders.total_price / orders.quantity, 2) ELSE orders.total_price END AS unit_price
            FROM orders
            LEFT JOIN products ON products.id = orders.product_id
            LEFT JOIN product_packs ON product_packs.id = orders.pack_id
            ORDER BY orders.id DESC
        ";

        $req = $this->bd->prepare($sql);
        $req->execute();
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteOrder($id)
    {
        $sql = "DELETE FROM orders WHERE id = :id";
        $req = $this->bd->prepare($sql);
        return $req->execute(['id' => $id]);
    }


    public function getOrdersToDay()
    {
        $sql = "
        SELECT 
            orders.id AS order_id,
            orders.quantity,
            orders.total_price,
            orders.client_name,
            orders.client_country,
            orders.client_phone,
            orders.client_adress,
            orders.newstat,
            orders.updated_at,
            COALESCE(products.name, 'Produit supprimé') AS product_name,
            COALESCE(products.image, '') AS product_image,
            CASE 
                WHEN orders.quantity > 0 
                THEN ROUND(orders.total_price / orders.quantity, 2) 
                ELSE orders.total_price 
            END AS unit_price
        FROM orders
        LEFT JOIN products ON products.id = orders.product_id
        WHERE orders.updated_at >= CURDATE()
          AND orders.updated_at < CURDATE() + INTERVAL 1 DAY
          AND orders.newstat = 'deliver'
        ORDER BY orders.id DESC
    ";

        $req = $this->bd->prepare($sql);
        $req->execute();
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }
}
