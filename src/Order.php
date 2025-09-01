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


    public function CreateOrderde($data)
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


    public function GetOrders()
    {
        $sql = "
        SELECT 
            orders.id AS order_id,
            orders.*,
            products.name,
            products.price AS prix_unitaire,
            product_packs.titre
        FROM orders
        INNER JOIN products ON products.id = orders.product_id
        LEFT JOIN product_packs ON product_packs.id = orders.pack_id
        ORDER BY orders.id DESC
    ";

        $req = $this->bd->prepare($sql);
        $req->execute();
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }


    public function GetOrderByCountry($country)
    {
        $sql = "
        SELECT 
            orders.id AS order_id,
            orders.*,
            products.name,
            products.price AS prix_unitaire,
            product_packs.titre
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


    public function updateOrderStatus($orderId, $status)
    {
        $sql = "UPDATE orders SET status = :status WHERE id = :order_id";
        $req = $this->bd->prepare($sql);
        $req->execute([
            'status'   => $status,
            'id' => $orderId,
        ]);
    }
    

    public function updateOrderAction($orderId, $action)
    {
        $sql = "UPDATE orders SET action = :action WHERE id = :order_id";
        $req = $this->bd->prepare($sql);
        $req->execute([
            'action'   => $action,
            'order_id' => $orderId,
        ]);
    }

    

}
