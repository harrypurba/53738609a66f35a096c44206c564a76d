<?php
namespace App;

use Doctrine\DBAL\Connection;
use Ulid\Ulid;

class User {
    public string $id;
    public string $username;
    public function getAccessToken(){
        return JwtUtil::generateJwt(['userId' => $this->id]);
    }

    private static Connection $db;
    public static function initiateStatics(){
        self::$db = Database::getInstance()->getDb();
    }

    public static function createUser($username, $password){
        $user = [
            'id' => Ulid::generate(),
            'username' => $username,
            'password' => $password,
        ];
        self::$db->insert(AppConst::$usersTableName, $user);   
    }

    public static function getUser($username, $password) : ?User {
        $query = self::$db
        ->createQueryBuilder()
        ->select('*')
        ->from(AppConst::$usersTableName)
        ->where('username = :username and password = :password')
        ->setParameter('username', $username)
        ->setParameter('password', $password)
        ->executeQuery();
        
        $userDb = $query->fetchAllAssociative();
        if(!$userDb) return null;
        $userDb = $userDb[0];
        $user = new User();
        $user->id = $userDb['id'];
        $user->username = $userDb['password'];
        return $user;
    }

    public static function userExist($id) : bool {
        $query = self::$db
        ->createQueryBuilder()
        ->select('*')
        ->from(AppConst::$usersTableName)
        ->where('id = :id')
        ->setParameter('id', $id)
        ->executeQuery();
        
        $userDb = $query->fetchAllAssociative();
        if(!$userDb) return false;
        return true;
    }
}
