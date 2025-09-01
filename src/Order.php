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


    public function CreateCommande($data)
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


    public function GetCommandes()
    {
        $req = $this->bd->prepare('SELECT * FROM orders ORDER BY id DESC');
        $req->execute();
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    public function GetCommandeByCountry($country)
    {
        $req = $this->bd->prepare('SELECT * FROM orders WHERE client_country = :country');
        $req->execute(['country' => $country]);
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    
}
