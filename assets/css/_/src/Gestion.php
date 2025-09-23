<?php

namespace src;

use PDO;
use PDOException;

class Gestion
{
    private $bd;

    public function __construct(PDO $bd)
    {
        $this->bd = $bd;
    }

    
}