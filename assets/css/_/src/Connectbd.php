<?php
namespace src;

use PDO;
use Exception;

class Connectbd {

    private static function connect() {
        try {
            // $file = ".env";
            // $config = parse_ini_file($file, true);

            // $host = $config['database']['host'];
            // $dbname = $config['database']['dbname'];
            // $username = $config['database']['username'];
            // $password = $config['database']['password'];

            $host = "localhost";
            $dbname = "u772108144_shopping01";
            $username = "u772108144_duffatsbernard";
            $password = "&Drift78@Dev@Henoc";
           

            $dsn = "mysql:host=$host;dbname=$dbname;";

            $cnx = new PDO($dsn, $username, $password);
            $cnx->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $cnx;
        } catch (Exception $error) {
            die('Error : ' . $error->getMessage());
        }
    }

    public static function getConnection() {
        return self::connect();
    }
}

