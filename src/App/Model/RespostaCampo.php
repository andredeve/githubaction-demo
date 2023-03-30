<?php /** @noinspection PhpUnused */

namespace App\Model;

use Core\Model\AppModel;

/**
 * @Entity
 * @Table(name="resposta_campo")
 */
class RespostaCampo extends AppModel {

    /**
     * @Id
     * @Column(type="integer",name="id")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Column(type="string",name="campo_txt",nullable=false)
     */
    private $campoTxt;

    /**
     * @ManyToOne(targetEntity="Campo")
     * @JoinColumn(name="campo_id", referencedColumnName="id",nullable=false)
     */
    private $campo;

    /**
     * @ManyToOne(targetEntity="Tramite")
     * @JoinColumn(name="tramite_id", referencedColumnName="id",nullable=false,onDelete="CASCADE")
     */
    private $tramite;

    /**
     * @Column(type="text",name="resposta",nullable=true)
     */
    private $resposta;
    
    /**
     * @ManyToOne(targetEntity="Processo")
     * @JoinColumn(name="processo_lincado_id", referencedColumnName="id")
     */
    private $processoLincado;

    /**
     * @Column(type="datetime",name="data",nullable=false)
     */
    private $data;

    function getId(): ?int {
        return $this->id;
    }

    function getCampoTxt() {
        return $this->campoTxt;
    }

    function getCampo() {
        return $this->campo;
    }

    function getResposta() {
        return $this->resposta;
    }

    function getData() {
        return $this->data;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    function setCampoTxt($campoTxt) {
        $this->campoTxt = $campoTxt;
    }

    function setCampo($campo) {
        $this->campo = $campo;
    }

    function setResposta($resposta) {
        $this->resposta = $resposta;
    }

    function setData($data) {
        $this->data = $data;
    }

    function getTramite() {
        return $this->tramite;
    }

    function setTramite($tramite) {
        $this->tramite = $tramite;
    }
    
    function getProcessoLincado() {
        return $this->processoLincado;
    }

    function setProcessoLincado($processoLincado) {
        $this->processoLincado = $processoLincado;
    }


}
