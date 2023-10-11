<?php
namespace App;

use Doctrine\DBAL\Connection;
use Ulid\Ulid;

class Task {
    public string $id;
    public string $taskname;
    public string $payload;
    public int $failed;

    public function markComplete(){
        self::$db
        ->createQueryBuilder()
        ->update(AppConst::$tasksQueueTable)
        ->set('status', "'Completed'")
        ->where("id = :taskId")
        ->setParameter('taskId', $this->id)
        ->executeQuery();
    }

    public function markFailed(){
        self::$db
        ->createQueryBuilder()
        ->update(AppConst::$tasksQueueTable)
        ->set('status', "'Failed'")
        ->set('failed', $this->failed + 1)
        ->where("id = :taskId")
        ->setParameter('taskId', $this->id)
        ->executeQuery();
    }

    // static methods

    private static Connection $db;
    public static function initiateStatics(){
        self::$db = Database::getInstance()->getDb();
    }

    public static function createTask($taskname, $payload){
        $task = [
            'id' => Ulid::generate(),
            'taskname' => $taskname,
            'payload' => $payload,
            'status' => 'Pending',
            'failed' => 0,
        ];
        self::$db->insert(AppConst::$tasksQueueTable, $task);   
    }

    public static function getNextTask() : ?Task {
        $query = self::$db
        ->createQueryBuilder()
        ->select('*')
        ->from(AppConst::$tasksQueueTable)
        ->where("failed < :sendEmailRetry and status != :completedStatus")
        ->setParameter('sendEmailRetry', $_ENV['SEND_EMAIL_RETRY'])
        ->setParameter('completedStatus', 'Completed')
        ->orderBy('id')
        ->setMaxResults(1)
        ->executeQuery();

        $nextTaskDb = $query->fetchAllAssociative();
        if(!$nextTaskDb) return null;
        $nextTaskDb = $nextTaskDb[0];
        $nextTask = new Task();
        $nextTask->id = $nextTaskDb['id'];
        $nextTask->taskname = $nextTaskDb['taskname'];
        $nextTask->payload = $nextTaskDb['payload'];
        $nextTask->failed = $nextTaskDb['failed'];
        return $nextTask;
    }
}
