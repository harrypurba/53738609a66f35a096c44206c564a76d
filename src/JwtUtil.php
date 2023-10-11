<?php
namespace App;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtUtil {
    public static string $key;

    public static function initiateStatics(){
        self::$key = $_ENV['JWT_PRIVATE_KEY'];
    }

    public static function generateJwt($payload){
        $now = time();
        $payload = array_merge([
            'iss' => 'send-email-api',
            'aud' => 'app-user',
            'iat' => $now,
            'exp' => $now + 60 * $_ENV['JWT_LIFESPAN_MINUTE']
        ], $payload);

        $jwt = JWT::encode($payload, self::$key, 'HS256');
        return $jwt;
    }

    public static function isValid($jwt){
        $payload = [];
        try {
            $payload = JWT::decode($jwt, new Key(self::$key, 'HS256'));
            if ($payload->exp < time()) return false;
            $userId = $payload->userId;
            return User::userExist($userId);
        } catch (\Throwable $th) {
            return false;
        }
    }
}
