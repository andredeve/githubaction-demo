<?php

namespace App\Log\Migration;

use Core\Model\MigrationHelper;
use Exception;
use PDO;

class MigracaoLogS1 extends MigrationHelper
{
    private $debug;

    public function __construct(PDO $db)
    {
        parent::__construct($db);
        $this->debug = constant("DEBUG");
    }

    /**
     * @return void
     * @throws Exception
     */
    public function run()
    {
        $this->migrateInserLog();
        $this->migrateUpdateLog();
        $this->migrateDeleteLog();
    }

    /**
     */
    private function migrateInserLog() {
        if ($this->debug) {
            echo "Migrando log da tabela _create do log de anexos." . PHP_EOL;
        }
        $sql = "ALTER TABLE _create RENAME TO _create_old";
        $this->db->exec($sql);
        $ddl = "CREATE TABLE _create(" .
            "id INTEGER PRIMARY KEY  AUTOINCREMENT, " .
            "date TEXT default CURRENT_TIMESTAMP, " .
            "user_cod INTEGER DEFAULT NULL, " .
            "user_name TEXT, " .
            "attach_cod INTEGER DEFAULT NULL, " .
            "file TEXT DEFAULT NULL, " .
            "ip TEXT" .
            ")";
        $this->db->exec($ddl);
        $ddm = "INSERT INTO _create (id, date, user_cod, user_name, attach_cod, file, ip) SELECT id, date, user_cod, user_name, attach_cod, file, ip FROM _create_old";
        $this->db->exec($ddm);
        $sql = "DROP TABLE _create_old";
        $this->db->exec($sql);
        if ($this->debug) {
            echo "Migração do log da tabela _create do log de anexos concluída." . PHP_EOL;
        }
    }

    /**
     */
    private function migrateUpdateLog() {
        if ($this->debug) {
            echo "Migrando log da tabela _update do log de anexos." . PHP_EOL;
        }
        $sql = "ALTER TABLE _update RENAME TO _update_old";
        $this->db->exec($sql);
        $ddl = "CREATE TABLE _update(" .
            "id INTEGER PRIMARY KEY  AUTOINCREMENT, " .
            "date TEXT default CURRENT_TIMESTAMP, " .
            "user_cod INTEGER DEFAULT NULL, " .
            "user_name TEXT, " .
            "attach_cod_old INTEGER DEFAULT NULL, " .
            "attach_cod_new INTEGER DEFAULT NULL, " .
            "file_old TEXT DEFAULT NULL, " .
            "file_new TEXT DEFAULT NULL, " .
            "observation TEXT DEFAULT NULL, " .
            "motive TEXT DEFAULT NULL, " .
            "ip TEXT" .
            ")";
        $this->db->exec($ddl);
        $ddm = "INSERT INTO _update (id, date, user_cod, user_name, attach_cod_old, attach_cod_new, file_old, file_new, observation, motive, ip) SELECT id, date, user_cod, user_name, attach_cod_old, attach_cod_new, file_old, file_new, observation, motive, ip FROM _update_old";
        $this->db->exec($ddm);
        $sql = "DROP TABLE _update_old";
        $this->db->exec($sql);
        if ($this->debug) {
            echo "Migração do log da tabela _update do log de anexos concluída." . PHP_EOL;
        }
    }

    /**
     */
    private function migrateDeleteLog() {
        if ($this->debug) {
            echo "Migrando log da tabela _delete do log de anexos." . PHP_EOL;
        }
        $sql = "ALTER TABLE _delete RENAME TO _delete_old";
        $this->db->exec($sql);
        $ddl = "CREATE TABLE _delete(" .
            "id INTEGER PRIMARY KEY  AUTOINCREMENT, " .
            "date TEXT default CURRENT_TIMESTAMP, " .
            "user_cod INTEGER DEFAULT NULL, " .
            "user_name TEXT, " .
            "process TEXT DEFAULT NULL, " .
            "number TEXT DEFAULT NULL, " .
            "file TEXT DEFAULT NULL, " .
            "ip TEXT, " .
            "motive TEXT DEFAULT NULL, " .
            "observation TEXT DEFAULT NULL" .
            ")";
        $this->db->exec($ddl);
        $ddm = "INSERT INTO _delete (id, date, user_cod, user_name, file, ip, motive, observation) SELECT id, date, user_cod, user_name, file, ip, motive, observation FROM _delete_old";
        $this->db->exec($ddm);
        $sql = "DROP TABLE _delete_old";
        $this->db->exec($sql);
        if ($this->debug) {
            echo "Migração do log da tabela _delete do log de anexos concluída." . PHP_EOL;
        }
    }

    function validate(): bool
    {
        return true;
    }
}