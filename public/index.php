<?php

use App\Api\EmailApi;
use App\Api\UserApi;
use App\Email;
use App\JwtUtil;
use App\Migrate;
use App\Task;
use App\User;
use Dotenv\Dotenv;

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// initiate all necessary static properties of classes
JwtUtil::initiateStatics();
User::initiateStatics();
Email::initiateStatics();
Task::initiateStatics();

// migrate database
Migrate::getInstance()->Migrate();

// main
handleHttpRequest();
function handleHttpRequest() {
    $uri = $_SERVER['REQUEST_URI'];
    $method = $_SERVER['REQUEST_METHOD'];

    if ($method === 'GET') {
        http_response_code(405);
        echo json_encode(["error" => "Method not allowed"]);
        return;
    }

    $requestBody = json_decode(file_get_contents('php://input'), true);

    switch ($uri) {
        case '/api/users/login':
            UserApi::Login($requestBody);
            break;
        case '/api/users/create':
            UserApi::Register($requestBody);
            break;
        case '/api/email/send':
            if (!isAuthorized()) {
                http_response_code(401);
                echo json_encode(["error" => "not authorized"]);
                break;
            }
            EmailApi::Send($requestBody);
            break;
        default:
            http_response_code(404);
            break;
    }
}

function isAuthorized(){
    $token = getallheaders()['Authorization'];
    if($token == null) return false;
    $jwt = trim(str_replace('Bearer', '', $token));
    return JwtUtil::isValid($jwt);
}
