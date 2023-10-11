<?php
namespace App;

class AppConst {
    public static $usersTableName = "users";
    public static $emailsTableName = "emails";
    public static $tasksQueueTable = "tasksqueue";
    
    public static $sendEmailTaskName = "send-email";
    public static $sendEmailRetry = 3;
}
