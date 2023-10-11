<?php
namespace App;

use Doctrine\DBAL\Connection;
use Exception;
use PHPMailer\PHPMailer\PHPMailer;
use Ulid\Ulid;

class Email {
    public string $id = '';
    public string $fromEmail = '';
    public string $fromName = '';
    public string $toEmail = '';
    public string $toName = '';
    public string $subject = '';
    public string $body = '';

    public function __construct()
    {
        $this->id = Ulid::generate();
    }

    public function send(){
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = $_ENV['SMTP_HOST'];
            $mail->Port = $_ENV['SMTP_PORT'];
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['SMTP_USER'];
            $mail->Password = $_ENV['SMTP_PASSWORD'];
            $mail->SMTPSecure = $_ENV['SMTP_ENCRYPTION'];
        
            // Sender and recipient
            $mail->setFrom($this->fromEmail, $this->fromName);
            $mail->addAddress($this->toEmail, $this->toName);
        
            // Email subject and body
            $mail->Subject = $this->subject;
            $mail->Body = $this->body;
            $mail->isHTML(true);
        
            // Send email
            $mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function saveToDb(){
        $emailDb = $this->getJsonObject();
        self::$db->insert(AppConst::$emailsTableName, $emailDb);   
    }

    public function pushEmailTask(){
        Task::createTask(AppConst::$sendEmailTaskName, $this->getPayload());
    }

    private function getPayload(){
        return json_encode($this->getJsonObject());
    }

    private function getJsonObject(){
        return  [
            'id' => $this->id,
            'fromEmail' => $this->fromEmail,
            'fromName' => $this->fromName,
            'toEmail' => $this->toEmail,
            'toName' => $this->toName,
            'subject' => $this->subject,
            'body' => $this->body,
        ];
    }

    // static methods
    
    private static Connection $db;
    public static function initiateStatics(){
        self::$db = Database::getInstance()->getDb();
    }
    public static function parsePayload($payload){
        $jsonObj = json_decode($payload);
        $email = new Email();
        $email->id = $jsonObj->id ?? Ulid::generate();
        $email->fromEmail = $jsonObj->fromEmail;
        $email->fromName = $jsonObj->fromName;
        $email->toEmail = $jsonObj->toEmail;
        $email->toName = $jsonObj->toName;
        $email->subject = $jsonObj->subject;
        $email->body = $jsonObj->body;
        return $email;
    }
}
