<?php

namespace src;

use PDO;
use PDOException;

class Pack
{
    private $bd;

    public function __construct(PDO $bd)
    {
        $this->bd = $bd;
    }

    public function GetPacksByProductId($productId)
    {
        $sql = $this->bd->prepare('
            SELECT `product_packs`.`id` AS `pack_id`, `product_packs`.`product_id`, `product_packs`.`titre`, `product_packs`.`description`, `product_packs`.`quantity`, `product_packs`.`price_reduction`, `product_packs`.`price_normal`
            FROM `product_packs`
            WHERE `product_packs`.`product_id` = :id
        ');
        $sql->execute(['id' => $productId]);
        $packs = $sql->fetchAll(PDO::FETCH_ASSOC);
        return $packs;
    }


    public function getPackById($packId)
    {
        $sql = $this->bd->prepare('
        SELECT * FROM `product_packs`
        WHERE id = :id
    ');
        $sql->execute(['id' => $packId]);
        $pack = $sql->fetch(PDO::FETCH_ASSOC);
        return $pack;
    }



    public function createPack($data)
    {
        $sql = $this->bd->prepare('
            INSERT INTO `product_packs` (`product_id`, `titre`, `description`, `quantity`, `price_reduction`, `price_normal`)
            VALUES (:product_id, :titre, :description, :quantity, :price_reduction, :price_normal)
        ');

        $sql->execute([
            'product_id' => $data['product_id'],
            'titre' => $data['titre'],
            'description' => $data['description'],
            'quantity' => $data['quantity'],
            'price_reduction' => $data['price_reduction'],
            'price_normal' => $data['price_normal']
        ]);
    }
}
