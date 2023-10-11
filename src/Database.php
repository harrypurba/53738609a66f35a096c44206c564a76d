<?php
namespace App;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Schema\Schema;

class Database {
    private Connection $db;
    
    private function __construct() {
        try {
            $connectionParams = [
                'host' => $_ENV['DB_HOST'],
                'user' => $_ENV['DB_USER'],
                'password' => $_ENV['DB_PASSWORD'],
                'dbname' => $_ENV['DB_NAME'],
                'driver' => 'pdo_pgsql',
            ];
            $this->db = DriverManager::getConnection($connectionParams);
        } catch (\Throwable $th) {
            echo "failed to connect to database";
            echo $th;
        }
    }
    public function getDb(){
        return $this->db;
    }


    private static $instance;
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}
