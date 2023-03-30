<?php

namespace App\Model;

use Core\Model\AppModel;

/**
 * @Entity
 * @Table(name="imagem_digitalizada")
 */
class ImagemDigitalizada extends AppModel {

    /**
     * @Id
     * @Column(type="integer",name="id")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Column(type="string",name="arquivo",length=80)
     */
    private $arquivo;

    /**
     * @ManyToOne(targetEntity="Anexo")
     * @JoinColumn(name="anexo_id", referencedColumnName="id",nullable=false,onDelete="CASCADE")
     */
    private $anexo;

    function getId(): ?int {
        return $this->id;
    }

    function getArquivo() {
        return $this->arquivo;
    }

    function getAnexo() {
        return $this->anexo;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    function setArquivo($arquivo) {
        $this->arquivo = $arquivo;
    }

    function setAnexo($anexo) {
        $this->anexo = $anexo;
    }

}
