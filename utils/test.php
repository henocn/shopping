<?php
session_start();

require("../vendor/autoload.php");

use src\Connectbd;
use src\Product;
use src\Order;
use src\Pack;

$cnx = Connectbd::getConnection();
 

$Od = new Order($cnx);

$All = $Od->GetCommandeByCountry(228);

var_dump($All);