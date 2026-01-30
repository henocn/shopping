<?php

namespace src;

use PDO;
use PDOException;

class Country
{
    private $bd;

    public function __construct(PDO $bd)
    {
        $this->bd = $bd;
    }

    /**
     * Créer une nouvelle dépense
     */
    public function getAll()
    {
        $sql = "SELECT * FROM country ORDER BY name ASC";
        $req = $this->bd->prepare($sql);
        $req->execute();
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

}
