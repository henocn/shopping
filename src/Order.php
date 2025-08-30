<?php
namespace src;

use PDO;
use PDOException;

class Order{
      private $bd;

    public function __construct(PDO $bd)
    {
        $this->bd = $bd;
    }


    public function CreateCommande($data)
    {
        // Implémentation de la fonction pour passer une commande
        $req = $this->bd->prepare("INSERT INTO orders (product_id, pack_id, quantity,client_name,client_country,client_adress,client_note, manager_note, status) VALUES (:product_id, :pack_id, :quantity, :client_name, :client_country, :client_adress, :client_note, :manager_note, :status)");
        $req->execute([
            'product_id'   => $data['product_id'],
            'pack_id'    => $data['pack_id'],
            'quantity' => $data['quantity'],
            'client_name'     => $data['client_name'],
            'client_country'     => $data['client_country'],
            'client_adress'     => $data['client_adress'],
            'client_note'     => $data['client_note'],
            'manager_note'     => $data['manager_note'],
            'status'  => $data['status'],
        ]);
        
        // Retourner l'ID du produit créé
        return $this->bd->lastInsertId();
    }

    public function GetCommandes()
    {
        // Implémentation de la fonction pour récupérer les commandes
    }

    public function GetCommandeByCountry($country)
    {
        // Implémentation de la fonction pour récupérer une commande par son ID
    }
    


}