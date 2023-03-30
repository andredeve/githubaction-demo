<?php

namespace App\Model;

use Core\Interfaces\EntityInterface;
use Core\Model\AppModel;

/**
 * @Entity
 * @Table(name="local_fisico")
 * Class Local
 * @package App\Model
 */
class Local extends AppModel implements EntityInterface
{
    /**
     * @Id
     * @Column(type="integer",name="id")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @Column(type="string",name="descricao",nullable=false)
     */
    private $descricao;
    /**
     * @Column(type="date",name="data_cadastro")
     */
    private $dataCadastro;

    /**
     * @Column(type="datetime",name="ultima_alteracao",nullable=true)
     */
    private $ultimaAlteracao;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getDescricao()
    {
        return $this->descricao;
    }

    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;
    }

    public function getDataCadastro()
    {
        return $this->dataCadastro;
    }

    public function setDataCadastro($data_cadastro)
    {
        $this->dataCadastro = $data_cadastro;
    }

    public function getUltimaAlteracao()
    {
        return $this->ultimaAlteracao;
    }

    public function setUltimaAlteracao($ultima_alteracao)
    {
        $this->ultimaAlteracao = $ultima_alteracao;
    }

    public function __toString()
    {
        return (string)$this->descricao;
    }
}