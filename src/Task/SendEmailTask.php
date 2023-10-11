<?php
namespace App\Task;

use App\Email;

class SendEmailTask {
    public static function run($payload)
    {
        $email = Email::parsePayload($payload);
        $isSuccessful = $email->send();
        if ($isSuccessful) $email->saveToDb();
        return $isSuccessful;
    }
}
