<?php

namespace Core\Log;

use Core\Util\Functions;
use DateTime;
use Exception;
use PDO;

abstract class LogHelper
{
    /**
     * @var PDO $db
     */
    private $db;
    /**
     * @var int $version
     */
    private $version;
    /**
     * @var string $db_path
     */
    private $db_path;
    private $debug;

    public function __construct($db_name = "log.sqlite", $version = 1)
    {
        if (defined("DEBUG")) {
            $this->debug = constant("DEBUG");
        } else {
            $this->debug = false;
        }
        $this->db_path = LOG_PATH . $db_name;
        $this->db = new PDO("sqlite:" . $this->db_path, null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_PERSISTENT => false,
        ]);
        $this->version = $version;
        $this->init();
    }

    /**
     * @return PDO
     */
    public function getDatabase(): PDO {
        return $this->db;
    }

    private function init() {
        $current_version = intval($this->db->query("PRAGMA user_version")->fetch(PDO::FETCH_COLUMN));
        if ($current_version === 0) {
            $this->onCreate($this->db);
            $this->db->exec("PRAGMA user_version = 1");
        } else if ($current_version < $this->version) {
            try {
                $date = new DateTime();
                copy($this->db_path, substr($this->db_path, 0, strrpos($this->db_path, '.')) . "_" . $date->format("d-M-y") . ".sqlite");
                $this->onUpgrade($current_version, $this->version, $this->db);
                $this->db->exec("PRAGMA user_version = {$this->version}");
            } catch (Exception $e) {
                error_log($e);
                $this->db->exec("PRAGMA user_version = $current_version");
                if ($this->debug) {
                    echo "Falha: Não foi possível atualizar o banco de dados da versão $current_version para {$this->version}." . PHP_EOL;
                    echo $e->getMessage() . PHP_EOL;
                    echo $e->getTraceAsString() . PHP_EOL;
                }
            }
        }
    }

    /**
     * @param PDO $db
     */
    abstract protected function onCreate(PDO $db);

    /**
     * @param $old_version int
     * @param $new_version int
     * @param $db PDO
     * @return void
     * @throws Exception
     */
    abstract protected function onUpgrade(int $old_version, int $new_version, PDO $db);
    abstract public function registerCreate();
    abstract public function registerUpdate();
    abstract public function registerDelete();

    /**
     * @param array $params
     * @param string $operationType
     * @return array
     */
    public function getCreateLog(Array $params, string $operationType = 'AND'): array {
        return $this->find("_create", $params, $operationType);
    }

    /**
     * @param array $params
     * @param string $operationType
     * @return array
     */
    public function getUpdateLog(Array $params, string $operationType = 'AND'): array {
        return $this->find("_update", $params, $operationType);
    }

    /**
     * @param array $params
     * @param string $operationType
     * @return array
     */
    public function getDeleteLog(Array $params, string $operationType = 'AND'): array {
        return $this->find("_delete", $params, $operationType);
    }

    /**
     * @param $table_name
     * @param array $params
     * @param $operationType
     * @return array
     */
    private function find($table_name, Array $params, $operationType): array {
        $columns = array();
        $values = array();
        foreach ($params as $key => $value) {
            $columns[] = $key . " ?";
            $values[] = $value;
        }
        $query = "SELECT * FROM $table_name WHERE " . implode(" $operationType ", $columns);
        $stmt = $this->db->prepare($query);
        foreach ($values as $i => $value) {
            $stmt->bindParam($i+1, $value);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}