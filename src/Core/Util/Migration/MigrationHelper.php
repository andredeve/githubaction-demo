<?php

namespace Core\Util\Migration;

use Core\Controller\AppController;
use Core\Exception\TechnicalException;
use Exception;
use PDO;

abstract class MigrationHelper
{
    protected $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }


    abstract function run();

    /**
     * @throws TechnicalException
     */
    public function beginTransaction()
    {
        echo "Abrindo transação... ";
        $result = $this->db->beginTransaction();
        if (!$result) {
            echo "Falhou: Não foi possível iniciar a transação." . PHP_EOL;
            throw new TechnicalException("Falhou: Não foi possível iniciar a transação.");
        }
        echo "Concluído." . PHP_EOL;
    }

    /**
     * @throws TechnicalException
     */
    public function commit()
    {
        echo "Gravando alterações... ";
        $result = $this->db->commit();
        if (!$result) {
            echo "Falhou: Não foi possível iniciar a transação." . PHP_EOL;
            throw new TechnicalException("Falhou: Não foi possível iniciar a transação.");
        }
        echo "Concluído." . PHP_EOL;
    }

    /**
     * @throws TechnicalException
     */
    public function rollback()
    {
        echo "Revertendo alterações... ";
        $result = $this->db->rollback();
        if (!$result) {
            echo "Falhou: Não foi possível reverter as alterações." . PHP_EOL;
            throw new TechnicalException("Falhou: Não foi possível reverter as alterações.");
        }
        echo "Concluído." . PHP_EOL;
    }
}