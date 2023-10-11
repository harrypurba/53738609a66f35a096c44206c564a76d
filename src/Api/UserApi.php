<?php
namespace App\Api;

use App\User;

class UserApi {
    public static function Login($requestBody) {
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json");
        
        $username = $requestBody['username'];
        $password = $requestBody['password'];

        if ($requestBody === null) {
            http_response_code(400); 
            echo json_encode(["error" => "Invalid body"]);
            return;
        }

        $user =  User::getUser($username, $password);
        if(!$user){
            http_response_code(400); 
            echo json_encode(["error" => "Invalid credentials"]);
            return;
        }

        $response = ["accessToken" => $user->getAccessToken()];
        echo json_encode($response);
    }

    public static function Register($requestBody) {
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json");
        
        $username = $requestBody['username'];
        $password = $requestBody['password'];
        
        if ($requestBody === null) {
            http_response_code(400); 
            echo json_encode(["error" => "Invalid body"]);
            return;
        }

        User::createUser($username, $password);
        
        $response = ["message" => "Successful"];
        echo json_encode($response);
    }
}
