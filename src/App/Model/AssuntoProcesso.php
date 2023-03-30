<?php

namespace App\Model;

use App\Model\Dao\AssuntoProcessoDao;
use Core\Model\AppModel;
use Doctrine\ORM\ORMException;
use Exception;

/**
 * @Entity
 * @Table(name="assunto_processo")
 * Class AssuntoProcesso
 * @package App\Model
 */
class AssuntoProcesso extends AppModel
{
    /**
     * @type int
     * @Id
     * @Column(type="integer",name="id")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @type Assunto
     * @ManyToOne(targetEntity="Assunto")
     * @JoinColumn(name="assunto_id", referencedColumnName="id",nullable=false)
     */
    private $assunto;

    /**
     * @type Processo
     * @ManyToOne(targetEntity="Processo")
     * @JoinColumn(name="processo_id", referencedColumnName="id",nullable=false,onDelete="CASCADE")
     */
    private $processo;

    /**
     * @throws ORMException
     * @throws Exception
     */
    function buscarPorAssunto($assunto_id, $processo_id)
    {
        /**
         * @var AssuntoProcessoDao $dao
         */
        $dao = $this->getDAO();
        $result = $dao->buscarPorAssunto($assunto_id, $processo_id);
        return is_array($result) ? $result[0] : null;
    }

    /**
     * @return mixed
     */
    public function getAssunto()
    {
        return $this->assunto;
    }

    /**
     * @param mixed $assunto
     */
    public function setAssunto($assunto)
    {
        $this->assunto = $assunto;
    }

    /**
     * @return mixed
     */
    public function getProcesso()
    {
        return $this->processo;
    }

    /**
     * @param mixed $processo
     */
    public function setProcesso($processo)
    {
        $this->processo = $processo;
    }

    /**
     * @return mixed
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function jsonSerialize(): array
    {
        return [
            "id" => $this->id,
            "assunto_id" => is_null($this->assunto) ? "" : $this->assunto->getId(),
            "processo_id" => is_null($this->processo) ? "" : $this->processo->getId()
        ];
    }

    public function imprimir(): string
    {
        return json_encode($this, JSON_PRETTY_PRINT);
    }
}