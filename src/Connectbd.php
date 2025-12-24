<?php
namespace src;

use PDO;
use Exception;

class Connectbd {

    private static function connect() {
        try {
            $file = ".env";
            $config = parse_ini_file(filename: $file, process_sections: true);

            $host = $config['database']['host'];
            $dbname = $config['database']['dbname'];
            $username = $config['database']['username'];
            $password = $config['database']['password'];
            $charset = $config['database']['charset'];

            $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

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

