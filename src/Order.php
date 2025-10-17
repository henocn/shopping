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
        (product_id, pack_id, purchase_price, total_price, quantity, client_name, client_country, client_adress, client_phone, client_note, newstat, manager_id) 
        VALUES 
        (:product_id, :pack_id, :purchase_price, :total_price, :quantity, :client_name, :client_country, :client_adress, :client_phone, :client_note, :newstat, :manager_id)
    ");

        $req->execute([
            'product_id'     => (int)($data['product_id'] ?? 0),
            'pack_id'        => (int)($data['pack_id'] ?? 0),
            'purchase_price' => (int)($data['purchase_price'] ?? 0),
            'total_price'    => (int)($data['total_price'] ?? 0),
            'quantity'       => (int)($data['quantity'] ?? 1),
            'client_name'    => $data['client_name'] ?? '',
            'client_country' => $data['client_country'],
            'client_adress'  => $data['client_adress'] ?? '',
            'client_phone'   => $data['client_phone'] ?? '',
            'client_note'    => $data['client_note'] ?? null,
            'newstat'         => 'new',
            'manager_id'     => (int)($data['manager_id'] ?? 0),
        ]);
        return true;
    }

    public function getOrderById($id)
    {
        $sql = "SELECT * FROM orders WHERE id = :id LIMIT 1";
        $req = $this->bd->prepare($sql);
        $req->execute(['id' => $id]);
        return $req->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllOrders()
    {
        $sql = "SELECT
    orders.id AS order_id,
    orders.product_id,
    orders.pack_id,
    orders.quantity,
    orders.purchase_price,
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
    products.selling_price AS unit_price,
    COALESCE(MAX(products.name), 'Produit supprimé') AS product_name,
    COALESCE(MAX(products.image), '') AS product_image,
    COALESCE(MAX(product_packs.name), '') AS pack_name
FROM orders
LEFT JOIN products ON products.id = orders.product_id
LEFT JOIN product_packs ON product_packs.id = orders.pack_id
GROUP BY orders.id
ORDER BY orders.id DESC;

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
        return true;
    }

    public function getOrdersByUserId($userId)
{
    $sql = "
        SELECT
            o.id AS order_id,
            o.product_id,
            o.pack_id,
            o.quantity,
            o.purchase_price,
            o.total_price,
            o.client_name,
            o.client_country,
            o.client_phone,
            o.client_adress,
            o.client_note,
            o.manager_note,
            o.manager_id,
            o.newstat,
            o.created_at,
            o.updated_at,
            p.selling_price AS unit_price,
            COALESCE(MAX(p.name), 'Produit supprimé') AS product_name,
            COALESCE(MAX(p.image), '') AS product_image,
            COALESCE(MAX(pp.titre), '') AS pack_name
        FROM orders o
        LEFT JOIN products p ON p.id = o.product_id
        LEFT JOIN product_packs pp ON pp.id = o.pack_id
        WHERE o.manager_id = :manager_id
        GROUP BY 
            o.id, o.product_id, o.pack_id, o.quantity, o.purchase_price, o.total_price,
            o.client_name, o.client_country, o.client_phone, o.client_adress,
            o.client_note, o.manager_note, o.manager_id, o.newstat, o.created_at, o.updated_at
        ORDER BY o.id DESC;
    ";

    $req = $this->bd->prepare($sql);
    $req->execute(['manager_id' => $userId]);
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
            COALESCE(products.image, '') AS product_image
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


    public function getOrdersToDayByUserId($userId)
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
            orders.manager_id,
            orders.newstat,
            orders.updated_at,
            COALESCE(products.name, 'Produit supprimé') AS product_name,
            COALESCE(products.image, '') AS product_image
        FROM orders
        LEFT JOIN products ON products.id = orders.product_id
        WHERE orders.updated_at >= CURDATE()
          AND orders.updated_at < CURDATE() + INTERVAL 1 DAY
          AND orders.newstat = 'deliver'
          AND orders.manager_id = :manager_id
        ORDER BY orders.id DESC
    ";

        $req = $this->bd->prepare($sql);
        $req->execute(['manager_id' => $userId]);
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }
}
