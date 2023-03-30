<?php /** @noinspection PhpUnused */

namespace App\Log;

use App\Log\Migration\MigracaoLogS1;
use App\Log\Migration\MigracaoLogS2;
use Core\Exception\TechnicalException;
use Core\Log\LogHelper;
use Core\Model\MigrationHelper;
use Core\Util\Functions;
use DateTime;
use Exception;
use PDO;

class AttachLogger extends LogHelper
{
    private const DB_VERSION = 3;
    /**
     * @var PDO
     */
    private $db;
    /**
     * @var DateTime $date
     */
    private $date;
    /**
     * @var int $userCod
     */
    private $userCod;
    /**
     * @var string $userName
     */
    private $userName;
    /**
     * @var int $attachCod
     */
    private $attachCod;
    /**
     * @var int $attachCodOld
     */
    private $attachCodOld;
    /**
     * @var string $file
     */
    private $file;
    /**
     * @var string $fileOld
     */
    private $fileOld;
    /**
     * @var string ip
     */
    private $ip;
    /**
     * @var string motive
     */
    private $motive;
    /**
     * @var string $observation
     */
    private $observation;
    /**
     * @var string $process
     */
    private $process;
    /**
     * @var string number
     */
    private $number;
    private $debug;

    public function __construct()
    {
        parent::__construct("attach_log.sqlite", self::DB_VERSION);
        $this->db = parent::getDatabase();
        if (defined("DEBUG")) {
            $this->debug = constant("DEBUG");
        } else {
            $this->debug = false;
        }
    }

    /**
     * @throws Exception
     */
    protected function onCreate(PDO $db)
    {
        $ddl = "CREATE TABLE _create(" .
            "id INTEGER PRIMARY KEY AUTOINCREMENT, " .
            "date TEXT default CURRENT_TIMESTAMP, " .
            "user_cod INTEGER DEFAULT NULL, " .
            "user_name TEXT, " .
            "attach_cod INTEGER DEFAULT NULL, " .
            "file TEXT DEFAULT NULL, " .
            "ip TEXT" .
            ")";
        $db->exec($ddl);
        $ddl = "CREATE TABLE _update(" .
            "id INTEGER PRIMARY KEY AUTOINCREMENT, " .
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
        $db->exec($ddl);
        $ddl = "CREATE TABLE _delete(" .
            "id INTEGER PRIMARY KEY AUTOINCREMENT, " .
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
        $db->exec($ddl);
        $this->onUpgrade(1, self::DB_VERSION, $db);
    }

    protected function onUpgrade(int $old_version, int $new_version, PDO $db)
    {
        if ($this->debug) {
            echo "Migrando da verão $old_version para $new_version." . PHP_EOL;
        }
        $migrations = self::getMigrations($db);
        for ($i = $old_version - 1; $i < $new_version - 1; $i++) {
            $migration = $migrations[$i];
            try {
                $this->migrate($migration);
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
        }
    }

    public function setDate(DateTime $date): void
    {
        $this->date = $date;
    }

    public function setUserCod(?int $userCod): void
    {
        $this->userCod = $userCod;
    }

    public function setUserName(?string $userName): void
    {
        $this->userName = $userName;
    }

    public function setAttachCod(?int $attachCod): void
    {
        $this->attachCod = $attachCod;
    }

    public function setFile(?string $file): void
    {
        $this->file = $file;
    }

    public function setIp(?string $ip): void
    {
        $this->ip = $ip;
    }

    /**
     * @return int
     */
    public function getAttachCodOld(): ?int
    {
        return $this->attachCodOld;
    }

    /**
     * @param int|null $attachCodOld
     */
    public function setAttachCodOld(?int $attachCodOld): void
    {
        $this->attachCodOld = $attachCodOld;
    }

    /**
     * @return string|null
     */
    public function getFileOld(): ?string
    {
        return $this->fileOld;
    }

    /**
     * @param string|null $fileOld
     */
    public function setFileOld(?string $fileOld): void
    {
        $this->fileOld = $fileOld;
    }

    /**
     * @param string|null $motive
     */
    public function setMotive(?string $motive): void
    {
        $this->motive = $motive;
    }

    /**
     * @return DateTime
     */
    public function getDate(): DateTime
    {
        return $this->date;
    }

    /**
     * @return int|null
     */
    public function getUserCod(): ?int
    {
        return $this->userCod;
    }

    /**
     * @return string|null
     */
    public function getUserName(): ?string
    {
        return $this->userName;
    }

    /**
     * @return int|null
     */
    public function getAttachCod(): ?int
    {
        return $this->attachCod;
    }

    /**
     * @return string|null
     */
    public function getFile(): ?string
    {
        return $this->file;
    }

    /**
     * @return string|null
     */
    public function getIp(): ?string
    {
        return $this->ip;
    }

    /**
     * @return string|null
     */
    public function getMotive(): ?string
    {
        return $this->motive;
    }

    /**
     * @return string|null
     */
    public function getObservation(): ?string
    {
        return $this->observation;
    }

    /**
     * @param string|null $observation
     */
    public function setObservation(?string $observation): void
    {
        $this->observation = $observation;
    }

    /**
     * @return string
     */
    public function getProcess(): string
    {
        return $this->process;
    }

    /**
     * @param string $process
     */
    public function setProcess(string $process): void
    {
        $this->process = $process;
    }

    /**
     * @return string
     */
    public function getNumber(): string
    {
        return $this->number;
    }

    /**
     * @param string $number
     */
    public function setNumber(string $number): void
    {
        $this->number = $number;
    }

    public function registerCreate()
    {
        $ddl = "INSERT INTO _create(date, user_cod, user_name, attach_cod, file, ip) " .
            "VALUES (:date, :user_cod, :user_name, :attach_cod, :file, :ip)";
        $stmt = $this->db->prepare($ddl);
        $stmt->bindValue(":date", $this->date->getTimestamp(), PDO::PARAM_INT);
        $stmt->bindValue(":user_cod", $this->userCod, PDO::PARAM_INT);
        $stmt->bindValue(":user_name", $this->userName);
        $stmt->bindValue(":attach_cod", $this->attachCod, PDO::PARAM_INT);
        $stmt->bindValue(":file", $this->file);
        $stmt->bindValue(":ip", $this->ip);
        if (!$stmt->execute()) {
            Functions::escreverLogErro("Não foi possível registrar o log de registro de anexo: " . $this);
        }
    }

    public function registerUpdate()
    {
        $ddl = "INSERT INTO _update(date, user_cod, user_name, attach_cod_new, attach_cod_old, file_new, file_old, ip, observation, motive) " .
            "VALUES (:date, :user_cod, :user_name, :attach_cod_new, :attach_cod_old, :file_new, :file_old, :ip, :observation, :motive)";
        $stmt = $this->db->prepare($ddl);
        $stmt->bindValue(":date", $this->date->getTimestamp(), PDO::PARAM_INT);
        $stmt->bindValue(":user_cod", $this->userCod, PDO::PARAM_INT);
        $stmt->bindValue(":user_name", $this->userName);
        $stmt->bindValue(":attach_cod_new", $this->attachCod, PDO::PARAM_INT);
        $stmt->bindValue(":attach_cod_old", $this->attachCodOld, PDO::PARAM_INT);
        $stmt->bindValue(":file_new", $this->file);
        $stmt->bindValue(":file_old", $this->fileOld);
        $stmt->bindValue(":ip", $this->ip);
        $stmt->bindValue(":observation", $this->observation);
        $stmt->bindValue(":motive", $this->motive);
        if (!$stmt->execute()) {
            Functions::escreverLogErro("Não foi possível registrar o log de atualização de anexo: " . $this);
        }
    }

    public function registerDelete()
    {
        $ddl = "INSERT INTO _delete(date, user_cod, user_name, process, number, file, ip, motive, observation) " .
            "VALUES (:date, :user_cod, :user_name, :process, :number, :file, :ip, :motive, :observation)";
        $stmt = $this->db->prepare($ddl);
        $stmt->bindValue(":date", $this->date->getTimestamp(), PDO::PARAM_INT);
        $stmt->bindValue(":user_cod", $this->userCod, PDO::PARAM_INT);
        $stmt->bindValue(":user_name", $this->userName);
        $stmt->bindValue(":file", $this->file);
        $stmt->bindValue(":ip", $this->ip);
        $stmt->bindValue(":motive", $this->motive);
        $stmt->bindValue(":observation", $this->observation);
        $stmt->bindValue(":process", $this->process);
        $stmt->bindValue(":number", $this->number);
        if (!$stmt->execute()) {
            Functions::escreverLogErro("Não foi possível registrar o log de remoção de anexo: " . $this);
        }
    }

    public function __toString()
    {
        return "[" .
            "file_old" . $this->fileOld .
            "user_name" . $this->userName .
            "ip" . $this->ip .
            "attach_cod_old" . $this->attachCodOld .
            "attach_cod" . $this->attachCod .
            "user_cod" . $this->userCod .
            "date" . $this->date->format('d/m/Y') .
            "observation" . $this->observation .
            "motive" . $this->motive .
            "]";
    }

    /**
     * @throws TechnicalException
     * @throws Exception
     */
    private function migrate(MigrationHelper $migration) {
        $debug = isset($argv) && in_array("debug", $argv);
        if ($debug) {
            echo PHP_EOL . "=================================================================================================" . PHP_EOL . PHP_EOL;
        }
        $migration->beginTransaction();
        if ($debug) {
            echo "Iniciando migração do log de anexos... " . PHP_EOL;
        }
        $migration->run();
        if ($debug) {
            echo "Migração do log de anexos concluída." . PHP_EOL. PHP_EOL;
            echo "=================================================================================================" . PHP_EOL . PHP_EOL;
        }
        $migration->commit();
    }

    /**
     * @param int $cod
     * @return array
     * Obs: Log de exclusão não é capturado para visualização porque não existe uma interface para visualizar essa informação.
     */
    public static function getLog(int $cod): array
    {
        $log["create"] = (new AttachLogger())->getCreateLog(["attach_cod = " => $cod]);
        $log["update"] = (new AttachLogger())->getUpdateLog(["attach_cod_new = " => $cod, "attach_cod_old = " => $cod], "OR");
        return $log;
    }

    /**
     * @param PDO $db
     * @return MigrationHelper[]|array
     */
    private static function getMigrations(PDO $db): array {
        return [
            new MigracaoLogS1($db),
            new MigracaoLogS2($db),
        ];
    }
}