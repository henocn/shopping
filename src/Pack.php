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

    


    
}
