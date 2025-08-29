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


    public function CreateCommande()
    {
        // Implémentation de la fonction pour passer une commande
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