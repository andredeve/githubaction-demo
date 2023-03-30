<?php /** @noinspection PhpUnused */

namespace App\Model;

use Core\Model\AppModel;

/**
 * @Entity
 * @Table(name="tarefa",uniqueConstraints={@UniqueConstraint(name="tarefa_unique", columns={"descricao", "setor_fase_id"})})
 */
class Tarefa extends AppModel {

    /**
     * @Id
     * @Column(type="integer",name="id")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Column(type="integer",name="ordem")
     */
    private $ordem;

    /**
     * @Column(type="string",name="descricao")
     */
    private $descricao;

    /**
     * @Column(type="text",name="orientacao")
     */
    private $orientacao;

    /**
     * @ManyToOne(targetEntity="SetorFase")
     * @JoinColumn(name="setor_fase_id", referencedColumnName="id",nullable=true,onDelete="CASCADE")
     */
    private $setorFase;

    /**
     * @Column(type="boolean",name="is_ativa")
     */
    private $isAtiva;

    function getId(): ?int {
        return $this->id;
    }

    function getDescricao() {
        return $this->descricao;
    }

    function getOrientacao() {
        return $this->orientacao;
    }

    function getIsAtiva() {
        return $this->isAtiva;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    function setDescricao($descricao) {
        $this->descricao = $descricao;
    }

    function setOrientacao($orientacao) {
        $this->orientacao = $orientacao;
    }

    function setIsAtiva($isAtiva) {
        $this->isAtiva = $isAtiva;
    }

    function getOrdem() {
        return $this->ordem;
    }

    function setOrdem($ordem) {
        $this->ordem = $ordem;
    }

    function getSetorFase() {
        return $this->setorFase;
    }

    function setSetorFase($setorFase) {
        $this->setorFase = $setorFase;
    }

    public function __toString() {
        return $this->descricao;
    }

}
