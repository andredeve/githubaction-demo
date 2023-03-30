<?php

namespace App\Model;

use Core\Interfaces\EntityInterface;
use Core\Model\AppModel;

/**
 * @Entity
 * @Table(name="categoria_documento")
 */
class CategoriaDocumento extends AppModel implements EntityInterface
{
    /**
     * @Id
     * @Column(type="integer",name="id")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
     * @Column(type="string",name="descricao",unique=true)
     */
    private $descricao;

    /**
     * @Column(type="datetime",name="data_cadastro",nullable=true)
     */
    private $dataCadastro;

    /**
     * @Column(type="datetime",name="ultima_alteracao",nullable=true)
     */
    private $ultimaAlteracao;

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
        return (string) $this->descricao;
    }
}