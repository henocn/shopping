<?php

namespace src;

use PDO;
use PDOException;

class Depense
{
    private $bd;

    public function __construct(PDO $bd)
    {
        $this->bd = $bd;
    }

    /**
     * Créer une nouvelle dépense
     */
    public function createDepense($data)
    {
        $sql = "INSERT INTO depense (type, product_id, cout, date, descrption) 
                VALUES (:type, :product_id, :cout, :date, :descrption)";

        $req = $this->bd->prepare($sql);
        return $req->execute([
            'type'        => $data['type'] ?? 'products',
            'product_id'  => $data['product_id'] ?? null,
            'cout'        => $data['cout'],
            'date'        => $data['date'] ?? date('Y-m-d H:i:s'),
            'descrption'  => $data['descrption'] ?? 'Livraison'
        ]);
    }

    /* Récupérer toutes les dépenses
     
    public function getAllDepenses()
    {
        $sql = "SELECT * FROM depense ORDER BY date DESC";
        $req = $this->bd->prepare($sql);
        $req->execute();
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }*/

    /* Récupérer les dépenses par type
     
    public function getDepensesByType($type)
    {
        $sql = "SELECT * FROM depense WHERE type = :type ORDER BY date DESC";
        $req = $this->bd->prepare($sql);
        $req->execute(['type' => $type]);
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }*/

    /* Récupérer les dépenses par produit
     
    public function getDepensesByProductId($productId)
    {
        $sql = "SELECT * FROM depense WHERE product_id = :product_id ORDER BY date DESC";
        $req = $this->bd->prepare($sql);
        $req->execute(['product_id' => $productId]);
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }*/
}
