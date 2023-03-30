<?php /** @noinspection PhpUnused */

namespace App\Model;

use Core\Model\AppModel;

/**
 * @Entity
 * @Table(name="cidade")
 */
class Cidade extends AppModel {

    /**
     * @Id
     * @Column(type="integer",name="id")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @Column(type="string",name="nome", length=150)
     */
    protected $nome;

    /**
     * @ManyToOne(targetEntity="Estado")
     * @JoinColumn(name="estado_id", referencedColumnName="id")
     */
    protected $estado;

    function getId(): ?int {
        return $this->id;
    }

    function getNome() {
        return utf8_encode($this->nome);
    }

    function getEstado(): ?Estado
    {
        if (!empty($this->estado)) {
            return $this->estado;
        }
        return new Estado();
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    function setNome($nome) {
        $this->nome = $nome;
    }

    function setEstado($estado) {
        $this->estado = $estado;
    }

    public function __toString() {
        return $this->getNome();
    }

}
