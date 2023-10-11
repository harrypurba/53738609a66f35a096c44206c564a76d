<?php

use App\AppConst;
use App\Email;
use App\JwtUtil;
use App\Migrate;
use App\Task;
use App\Task\SendEmailTask;
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

function TaskMaster() {
    println("TaskMaster is listening");
    while (true) {
        sleep($_ENV['TASK_MASTER_PULL_INTERVAL']);
        $nextTask = Task::getNextTask();
        if($nextTask == null) continue;

        println("Processing Task: {$nextTask->taskname}");

        switch ($nextTask->taskname) {
            case AppConst::$sendEmailTaskName:
                $isSuccessful = SendEmailTask::run($nextTask->payload);
                if($isSuccessful) $nextTask->markComplete();
                else $nextTask->markFailed();
                break;
            default:
                println("No task handler assosiated with this task: {$nextTask->taskname}");
                break;
        }

        println("Finished Processing Task: {$nextTask->taskname}");
    }
}

function println($msg) {
    echo $msg . "\n";
}

TaskMaster();
