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
        (product_id, pack_id, quantity, total_price, client_name, client_country, client_adress, client_note, status) 
        VALUES 
        (:product_id, :pack_id, :quantity, :total_price, :client_name, :client_country, :client_adress, :client_note, :status)
    ");

        $req->execute([
            'product_id'   => $data['product_id'],
            'pack_id'      => $data['pack_id'],
            'quantity'     => $data['quantity'],
            'total_price'  => $data['total_price'],
            'client_name'  => $data['client_name'],
            'client_country' => $data['client_country'],
            'client_adress' => $data['client_adress'],
            'client_note'   => $data['client_note'],
            'status'        => $data['status'],
        ]);
    }


    public function getAllOrders()
    {
        $sql = "
        SELECT 
            orders.id AS order_id,
            orders.*,
            products.name AS product_name,
            products.image AS product_image,
            products.price AS unit_price,
            product_packs.titre as pack_name
        FROM orders
        INNER JOIN products ON products.id = orders.product_id
        LEFT JOIN product_packs ON product_packs.id = orders.pack_id
        ORDER BY orders.id DESC
    ";

        $req = $this->bd->prepare($sql);
        $req->execute();
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getOrdersByCountry($country)
    {
        $sql = "
        SELECT 
            orders.id AS order_id,
            orders.*,
            products.name AS product_name,
            products.image AS product_image,
            products.price AS unit_price,
            product_packs.titre as pack_name
        FROM orders
        INNER JOIN products ON products.id = orders.product_id
        LEFT JOIN product_packs ON product_packs.id = orders.pack_id
        WHERE orders.client_country = :country
        ORDER BY orders.id DESC
    ";

        $req = $this->bd->prepare($sql);
        $req->execute(['country' => $country]);
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getOrderByCountry($country)
    {
        $sql = "
        SELECT 
            orders.id AS order_id,
            orders.*,
            products.name AS product_name,
            products.image AS product_image,
            products.price AS unit_price,
            product_packs.titre as pack_name
        FROM orders
        INNER JOIN products ON products.id = orders.product_id
        LEFT JOIN product_packs ON product_packs.id = orders.pack_id
        WHERE orders.client_country = :country
        ORDER BY orders.id DESC
    ";

        $req = $this->bd->prepare($sql);
        $req->execute(['country' => $country]);
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }



    public function updateOrder(array $data)
    {
        $sql = "UPDATE orders 
            SET quantity = :quantity,
                total_price = :total_price,
                manager_note = :manager_note,
                updated_at = :updated_at,
                status = :status,
                action = :action
            WHERE id = :id";

        $req = $this->bd->prepare($sql);
        $req->execute([
            'quantity'     => $data['quantity'],
            'total_price'  => $data['total_price'],
            'manager_note' => $data['manager_note'],
            'updated_at'   => $data['updated_at'],
            'status'       => $data['status'],
            'action'       => $data['action'],
            'id'           => $data['id'],
        ]);
    }
}
