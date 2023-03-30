<?php

namespace App\Model;

use Core\Interfaces\EntityInterface;
use Core\Model\AppModel;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @Entity
 * @Table(name="fluxograma")
 */
class Fluxograma extends AppModel implements EntityInterface {

    /**
     * @Id
     * @Column(type="integer",name="id")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @OneToOne(targetEntity="Assunto", inversedBy="fluxograma")
     * @JoinColumn(name="assunto_id", referencedColumnName="id")
     */
    private $assunto;

    /**
     * Define se o assunto vinculado seguirá o fluxograma definido por ele para novos processos 
     * @Column(type="boolean",name="is_ativo", options={"default" : 1})
     */
    private $isAtivo;

    /**
     * @OneToMany(targetEntity="Fase", mappedBy="fluxograma",cascade={"persist"})
     * @OrderBy({"numero" = "ASC"})
     */
    private $fases;

    /**
     * @Column(type="date",name="data_cadastro")
     */
    private $dataCadastro;

    /**
     * @Column(type="datetime",name="ultima_alteracao",nullable=true)
     */
    private $ultimaAlteracao;

    function __construct() {
        $this->fases = new ArrayCollection();
        $this->isAtivo = true;
    }

    function getIsAtivo() {
        return $this->isAtivo;
    }

    function setIsAtivo($isAtivo) {
        $this->isAtivo = $isAtivo;
    }

    function getId(): ?int {
        return $this->id;
    }

    function getAssunto() {
        return $this->assunto;
    }

    function getFases($numero = null) {
        if ($numero !== null) {
            foreach ($this->fases as $fase) {
                if ($fase->getNumero() == $numero) {
                    return $fase;
                }
            }
            return null;
        }
        return $this->fases;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    function setAssunto($assunto) {
        $this->assunto = $assunto;
    }

    function setFases($fases) {
        $this->fases = $fases;
    }

    function getDataCadastro() {
        return $this->dataCadastro;
    }

    function getUltimaAlteracao() {
        return $this->ultimaAlteracao;
    }

    function setDataCadastro($data_cadastro) {
        $this->dataCadastro = $data_cadastro;
    }

    function setUltimaAlteracao($ultima_alteracao) {
        $this->ultimaAlteracao = $ultima_alteracao;
    }

    /**
     * Função que calcula o vencimento para um fluxograma
     */
    function getVencimento($data_ref = null) {
        $maior_data = $data_ref != null ? $data_ref : Date('Y-m-d');
        foreach ($this->fases as $fase) {
            $vencimento_fase = $fase->getVencimento($maior_data);
            if (strtotime($vencimento_fase) > strtotime($maior_data)) {
                $maior_data = $vencimento_fase;
            }
        }
        return $maior_data;
    }

}
