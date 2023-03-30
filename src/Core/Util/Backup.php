<?php

namespace Core\Util;

use App\Controller\IndexController;
use Exception;
use Oro\ORM\Query\AST\Platform\Functions\Postgresql\Date;
use PDO;
use PDOException;

include_once APP_PATH . '/lib/phpsec/SFTPConn.php';

/**
 * Classe Backup
 * Realiza o backup do banco de dados (MySql)
 * @author Anderson
 */
class Backup {

    private $sql, $removeAI;
    private $file;
    private $dir;
    private static $conn;
    private $app_config;
    private $cliente_config;

    function __construct() {
        $this->connect();
        $this->app_config = IndexController::getConfig();
        $this->cliente_config = IndexController::getClienteConfig();
        $this->dir = FILE_PATH . '_backups/';
        $this->file = strtolower($this->app_config['app_name']) . '_' . Date('d') . '_' . Date('m') . '_' . Date('Y') . '.sql';
    }

    private function connect() {
        try {
            $db_config = IndexController::getDatabaseConfig();
            $host = $db_config['db_host'];
            $user = $db_config['db_user'];
            $password = $db_config['db_password'];
            $dbname = $db_config['db_name'];
            $port = $db_config['db_port'];
            self::$conn = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $user, $password, array(
                PDO::ATTR_PERSISTENT => true,
                PDO::ATTR_EMULATE_PREPARES => true
            ));
            self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$conn->setAttribute(PDO::ATTR_ORACLE_NULLS, PDO::NULL_EMPTY_STRING);
        } catch (PDOException $i) {
            die("Erro SQL: <code>" . $i->getMessage() . "</code>");
        }
    }

    private function ln($text = '') {
        $this->sql .= $text . "\n";
    }

    /**
     * Função que faz a exportação do banco de dados do cliente
     */
    public function dump($enviar_arquivos = false) {
        $this->ln("-- Backup Banco de dados " . $this->app_config['app_name']) . "-" . $this->cliente_config['nome'];
        $this->ln("-- Data: " . Date("d/m/Y") . " às " . Date("H:i"));
        $this->ln("SET FOREIGN_KEY_CHECKS=0;");
        $tables = self::$conn->query("SHOW FULL TABLES WHERE Table_Type = 'BASE TABLE'")->fetchAll(PDO::FETCH_BOTH);
        $views = self::$conn->query("SHOW FULL TABLES WHERE Table_Type = 'VIEW'")->fetchAll(PDO::FETCH_BOTH);
        foreach ($tables as $table) {
            $this->export($table[0]);
        }
        foreach ($views as $view) {
            $this->export($view[0], true);
        }
        $this->ln("SET FOREIGN_KEY_CHECKS=1;");
        try {
            file_put_contents($this->dir . $this->file, $this->sql);
            if ($enviar_arquivos) {
                $this->enviarArquivo();
            }
            $this->limparBackupsAntigos();
        } catch (Exception $e) {
            trigger_error($e->getMessage());
        }
    }

    private function export($table, $is_view = false) {
        $tipo = $is_view ? "View" : "Tabela";
        $this->ln("-- Criação da  $tipo: $table");
        $this->ln('DROP TABLE IF EXISTS `' . $table . '`;');
        $schemas = self::$conn->query("SHOW CREATE TABLE `{$table}`")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($schemas as $schema) {
            $schema = isset($schema['Create Table']) ? $schema['Create Table'] : (isset($schema['Create View']) ? $schema['Create View'] : '');
            if ($this->removeAI)
                $schema = preg_replace('/AUTO_INCREMENT=([0-9]+)(\s{0,1})/', '', $schema);
            $this->ln($schema . ";\n\n");
        }
        if (!$is_view) {
            $data = self::$conn->query("SELECT * FROM `{$table}`")->fetchAll(PDO::FETCH_ASSOC);
            $qtde_rows = count($data);
            if ($qtde_rows > 0) {
                $this->ln("-- Registros da Tabela : $table");
                $columns = self::$conn->query("SHOW COLUMNS FROM $table")->fetchAll(PDO::FETCH_ASSOC);
                $insert = "INSERT INTO $table VALUES";
                $aux = 1;
                foreach ($data as $row) {
                    $insert .= " (";
                    $comma = " ";
                    foreach ($columns as $column) {
                        $field = $column['Field'];
                        $type = $column['Type'];
                        $is_string = !is_numeric($row[$field]);
                        $value = str_replace("\n", "\\n", addslashes($row[$field]));
                        $value = ($value != "" || $value != null ? utf8_encode($is_string ? '"' . $value . '"' : $value) : "NULL");
                        $insert .= $comma . $value;
                        $comma = ", ";
                    }
                    $insert .= " )";
                    $insert .= $aux == $qtde_rows ? ";" : ",";
                    $aux++;
                }
                $this->ln($insert);
            }
        }
    }

    private function removeAcentos($string) {
        return preg_replace(array("/(á|à|ã|â|ä)/", "/(Á|À|Ã|Â|Ä)/", "/(é|è|ê|ë)/", "/(É|È|Ê|Ë)/", "/(í|ì|î|ï)/", "/(Í|Ì|Î|Ï)/", "/(ó|ò|õ|ô|ö)/", "/(Ó|Ò|Õ|Ô|Ö)/", "/(ú|ù|û|ü)/", "/(Ú|Ù|Û|Ü)/", "/(ñ)/", "/(Ñ)/"), explode(" ", "a A e E i I o O u U n N"), $string);
    }

    private function getNomeCliente() {
        return strtolower($this->removeAcentos(str_replace(" ", "_", $this->cliente_config['nome'])));
    }

    /**
     * Função que limpa backups com mais de 5 dias
     */
    private function limparBackupsAntigos() {
        $files = glob($this->dir . "*");
        $now = time();
        $days = 5;
        $limit = 60 * 60 * 24 * $days;
        foreach ($files as $file) {
            if (is_file($file)) {
                if ($now - filemtime($file) > $limit) {
                    unlink($file);
                }
            }
        }
    }

    /**
     * Envia backup para servidor remoto
     */
    private function enviarArquivo() {
        $sftp = new SFTPConn('173.224.112.144', 37942);
        $sftp->login('lxtec', 'Krat&ra2011');
        $origem = $this->dir . $this->file;
        $destino = '/var/www/html/' . $this->app_config['app_name'] . '/_backups/' . strtolower($this->app_config['app_name']) . '_' . Date('d') . '_' . Date('m') . '_' . Date('Y') . '_' . $this->getNomeCliente() . '.sql';
        $sftp->sendFile($destino, $origem);
    }

}
