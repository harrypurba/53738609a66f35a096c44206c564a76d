<?php
namespace App\Api;

use App\Email;
use App\User;

class EmailApi {
    public static function Send($requestBody) {
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json");

        if ($requestBody === null) {
            http_response_code(400); 
            echo json_encode(["error" => "Invalid body"]);
            return;
        }

        $fromEmail = $requestBody['fromEmail'];
        $fromName = $requestBody['fromName'];
        $toEmail = $requestBody['toEmail'];
        $toName = $requestBody['toName'];
        $subject = $requestBody['subject'];
        $body = $requestBody['body'];

        $email = new Email();
        $email->fromEmail = $fromEmail;
        $email->fromName = $fromName;
        $email->toEmail = $toEmail;
        $email->toName = $toName;
        $email->subject = $subject;
        $email->body = $body;

        $email->pushEmailTask();

        $response = ["message" => "Event sent"];
        echo json_encode($response);
    }
}
