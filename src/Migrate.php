<?php
namespace App;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\DateType;
use Doctrine\DBAL\Types\IntegerType;
use Doctrine\DBAL\Types\Type;

class Migrate {
    private Connection $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance()->getDb();
    }

    public function Migrate(){
        $this->createUsersTable();
        $this->createEmailsTable();
        $this->createTasksQueueTable();
    }

    public function createUsersTable(){
        $tableName = AppConst::$usersTableName;
        if($this->tableExist($tableName)) return;
        $schema = new Schema();
        $table = $schema->createTable($tableName);
        $table->addColumn('id', 'string');
        $table->addColumn('username', 'string');
        $table->addColumn('password', 'string');
        $table->setPrimaryKey(['id']);
    
        $queries = $schema->toSql($this->db->getDatabasePlatform());
        foreach ($queries as $query) {
            $this->db->executeQuery($query);
        }
    }

    public function createEmailsTable(){
        $tableName = AppConst::$emailsTableName;
        if($this->tableExist($tableName)) return;
        $schema = new Schema();
        $table = $schema->createTable($tableName);
        $table->addColumn('id', 'string');
        $table->addColumn('fromEmail', 'string');
        $table->addColumn('fromName', 'string');
        $table->addColumn('toEmail', 'string');
        $table->addColumn('toName', 'string');
        $table->addColumn('subject', 'string');
        $table->addColumn('body', 'string', ['length' => 3000]);
        $table->setPrimaryKey(['id']);

        $queries = $schema->toSql($this->db->getDatabasePlatform());
        foreach ($queries as $query) {
            $this->db->executeQuery($query);
        }
    }

    public function createTasksQueueTable(){
        $tableName = AppConst::$tasksQueueTable;
        if($this->tableExist($tableName)) return;
        $schema = new Schema();
        $table = $schema->createTable($tableName);
        $table->addColumn('id', 'string');
        $table->addColumn('taskname', 'string');
        $table->addColumn('payload', 'string', ['length' => 3000]);
        $table->addColumn('status', 'string');
        $table->addColumn('failed', "integer");
        $table->setPrimaryKey(['id']);

        $queries = $schema->toSql($this->db->getDatabasePlatform());
        foreach ($queries as $query) {
            $this->db->executeQuery($query);
        }
    }

    private function tableExist($tableName){ 
        $schemaManager = $this->db->createSchemaManager();
        $tables = $schemaManager->listTables();
        foreach ($tables as $table) {
            if ($table->getName() === $tableName) return true;
        }
        return false;
    }

    private static $instance;
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}
