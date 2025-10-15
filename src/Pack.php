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

}
