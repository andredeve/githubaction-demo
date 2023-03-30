<?php

namespace Core\Model;

use Core\Exception\TechnicalException;
use PDO;

abstract class MigrationHelper
{
    protected $db;
    private $debug;

    public function __construct(PDO $db)
    {
        $this->db = $db;
        $this->debug = constant("DEBUG");
    }


    abstract function run();

    /**
     * @throws TechnicalException
     */
    public function beginTransaction()
    {
        if ($this->debug) {
            echo "Abrindo transação... ";
        }

        $result = $this->db->beginTransaction();
        if (!$result) {
            if ($this->debug) {
                echo "Falhou: Não foi possível iniciar a transação." . PHP_EOL;
            }
            throw new TechnicalException("Falhou: Não foi possível iniciar a transação.");
        }
        if ($this->debug) {
            echo "Concluído." . PHP_EOL;
        }
    }

    /**
     * @throws TechnicalException
     */
    public function commit()
    {
        if ($this->debug) {
            echo "Gravando alterações... ";
        }
        $result = $this->db->commit();
        if (!$result) {
            if ($this->debug) {
                echo "Falhou: Não foi possível iniciar a transação." . PHP_EOL;
            }
            throw new TechnicalException("Falhou: Não foi possível iniciar a transação.");
        }
        if ($this->debug) {
            echo "Concluído." . PHP_EOL;
        }
    }

    /**
     * @throws TechnicalException
     */
    public function rollback()
    {
        if ($this->debug) {
            echo "Revertendo alterações... ";
        }
        $result = $this->db->rollback();
        if (!$result) {
            if ($this->debug) {
                echo "Falhou: Não foi possível reverter as alterações." . PHP_EOL;
            }
            throw new TechnicalException("Falhou: Não foi possível reverter as alterações.");
        }
        if ($this->debug) {
            echo "Concluído." . PHP_EOL;
        }
    }

    abstract function validate(): bool;
}