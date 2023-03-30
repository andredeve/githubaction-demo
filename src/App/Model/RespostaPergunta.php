<?php /** @noinspection PhpUnused */

namespace App\Model;

use Core\Model\AppModel;

/**
 * @Entity
 * @Table(name="resposta_pergunta")
 */
class RespostaPergunta extends AppModel {

    /**
     * @Id
     * @Column(type="integer",name="id")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Column(type="string",name="campo_txt",nullable=false)
     */
    private $perguntaTxt;

    /**
     * @ManyToOne(targetEntity="Pergunta")
     * @JoinColumn(name="pergunta_id", referencedColumnName="id",nullable=false)
     */
    private $pergunta;

    /**
     * @ManyToOne(targetEntity="Tramite")
     * @JoinColumn(name="tramite_id", referencedColumnName="id",nullable=false,onDelete="CASCADE")
     */
    private $tramite;

    /**
     * @Column(type="boolean",name="resposta",nullable=false)
     */
    private $resposta;

    /**
     * @Column(type="text",name="observacoes",nullable=true)
     */
    private $observacoes;

    /**
     * @Column(type="datetime",name="data",nullable=false)
     */
    private $data;

    function getId(): ?int {
        return $this->id;
    }

    function getPerguntaTxt() {
        return $this->perguntaTxt;
    }

    function getPergunta() {
        return $this->pergunta;
    }

    function getTramite() {
        return $this->tramite;
    }

    function getResposta() {
        return $this->resposta;
    }

    function getObservacoes() {
        return $this->observacoes;
    }

    function getData() {
        return $this->data;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    function setPerguntaTxt($perguntaTxt) {
        $this->perguntaTxt = $perguntaTxt;
    }

    function setPergunta($pergunta) {
        $this->pergunta = $pergunta;
    }

    function setTramite($tramite) {
        $this->tramite = $tramite;
    }

    function setResposta($resposta) {
        $this->resposta = $resposta;
    }

    function setObservacoes($observacoes) {
        $this->observacoes = $observacoes;
    }

    function setData($data) {
        $this->data = $data;
    }

}
