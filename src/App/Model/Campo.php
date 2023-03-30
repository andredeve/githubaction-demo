<?php /** @noinspection PhpUnused */

namespace App\Model;

use Core\Model\AppModel;

/**
 * @Entity
 * @Table(name="campo",uniqueConstraints={@UniqueConstraint(name="campo_unique", columns={"nome", "setor_fase_id"})})
 */
class Campo extends AppModel
{

    /**
     * @type int
     * @Id
     * @Column(type="integer",name="id")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @type int
     * @Column(type="integer",name="ordem")
     */
    private $ordem;

    /**
     * @type string
     * Arquivo de template caso o campo seja do tipo arquivo
     * @Column(type="string",name="template",nullable=true,length=150)
     */
    private $template;

    /**
     * @type TipoAnexo
     * @ManyToOne(targetEntity="TipoAnexo")
     * @JoinColumn(name="tipo_anexo_id", referencedColumnName="id",nullable=true)
     */
    private $tipoTemplate;

    /**
     * @type bool
     * @Column(type="boolean")
     */
    private $numeroTemplateObrigatorio;
    
    /**
     * @type bool
     * @Column(type="boolean")
     */
    private $assinaturaObrigatoria;

    /**
     * @type string
     * @Column(type="string",name="nome",nullable=false,length=50)
     */
    private $nome;

    /**
     * @type string
     * Descrição do campo para melhor entendimento do mesmo
     * @Column(type="string",name="descricao",nullable=true,length=1000)
     */
    private $descricao;

    /**
     * @type string
     * Campo que guarda os valores possíveis de um caixa de seleção quando for o caso, separado
     * por ponto e vírgula
     * @Column(type="text",name="valores_selecao",nullable=true)
     */
    private $valoresSelecao;

    /**
     * @type string
     * Guarda a máscara do campo se houver
     * @Column(type="string",nullable=true, columnDefinition="ENUM('cpf', 'cnpj','telefone','data','hora','moeda','cep','email')")
     */
    private $mascara;

    /**
     * @type string
     * @Column(type="string", columnDefinition="ENUM('texto', 'caixa-texto','numero','data','hora','email','arquivo','caixa-selecao', 'arquivo-multiplo', 'processo')")
     */
    private $tipo;

    /**
     * @type SetorFase
     * @ManyToOne(targetEntity="SetorFase")
     * @JoinColumn(name="setor_fase_id", referencedColumnName="id",nullable=false,onDelete="CASCADE")
     */
    private $setorFase;

    /**
     * @type bool
     * @Column(type="boolean",name="is_obrigatorio")
     */
    private $isObrigatorio;

    /**
     * @type bool
     * @Column(type="boolean", options={"default" : 1})
     */
    private $ativo;

    /**
     * @type bool
     * @Column(type="boolean",name="circulacao_externa", options={"default" : 0})
     */
    public $circulacaoInterna;

    public function __construct()
    {
        $this->numeroTemplateObrigatorio = false;
        $this->ativo = true;
        $this->circulacaoInterna = false;
    }

    function getId(): ?int
    {
        return $this->id;
    }

    function getOrdem()
    {
        return $this->ordem;
    }

    function getNome()
    {
        return $this->nome;
    }

    function getDescricao()
    {
        return $this->descricao;
    }

    function getValoresSelecao()
    {
        return $this->valoresSelecao;
    }

    function getMascara()
    {
        return $this->mascara;
    }

    function getTipo()
    {
        return $this->tipo;
    }

    function getSetorFase()
    {
        return $this->setorFase;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    function setOrdem($ordem)
    {
        $this->ordem = $ordem;
    }

    function setNome($nome)
    {
        $this->nome = $nome;
    }

    function setDescricao($descricao)
    {
        $this->descricao = $descricao;
    }

    function setValoresSelecao($valoresSelecao)
    {
        $this->valoresSelecao = $valoresSelecao;
    }

    function setMascara($mascara)
    {
        $this->mascara = $mascara;
    }

    function setTipo($tipo)
    {
        $this->tipo = $tipo;
    }

    function setSetorFase($setorFase)
    {
        $this->setorFase = $setorFase;
    }

    function getIsObrigatorio()
    {
        return $this->isObrigatorio;
    }

    function setIsObrigatorio($isObrigatorio)
    {
        $this->isObrigatorio = $isObrigatorio;
    }

    function getTemplate()
    {
        return $this->template;
    }

    function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * @return mixed
     */
    public function getTipoTemplate()
    {
        return $this->tipoTemplate;
    }

    /**
     * @param mixed $tipoTemplate
     */
    public function setTipoTemplate($tipoTemplate)
    {
        $this->tipoTemplate = $tipoTemplate;
    }

    public function getNumeroTemplateObrigatorio(): ?bool
    {
        return $this->numeroTemplateObrigatorio ?? false;
    }

    public function setNumeroTemplateObrigatorio($numeroTemplateObrigatorio)
    {
        $this->numeroTemplateObrigatorio = $numeroTemplateObrigatorio;
    }

    function getAssinaturaObrigatoria() {
        return $this->assinaturaObrigatoria;
    }

    function setAssinaturaObrigatoria($assinaturaObrigatoria) {
        $this->assinaturaObrigatoria = $assinaturaObrigatoria;
    }

    public function isAtivo(): ?bool
    {
        return $this->ativo;
    }

    public function getAtivo(): ?bool
    {
        return $this->ativo;
    }

    public function setAtivo($ativo) {
        $this->ativo = $ativo;
    }

    /**
     * @param $circulacaoInterna
     */
    public function setCirculacaoInterna($circulacaoInterna): void
    {
        $this->circulacaoInterna = $circulacaoInterna;
    }

    /**
     * @return bool
     */
    public function getCirculacaoInterna(): bool
    {
        return $this->circulacaoInterna;
    }

    public function jsonSerialize(): array
    {
        return [
            "id" => $this->id,
            "ordem" => $this->ordem,
            "template" => $this->template,
            "tipo_anexo_id" => is_null($this->tipoTemplate) ? "" : $this->tipoTemplate->getId(),
            "numeroTemplateObrigatorio" => $this->numeroTemplateObrigatorio,
            "assinaturaObrigatoria" => $this->assinaturaObrigatoria,
            "nome" => $this->nome,
            "descricao" => $this->descricao,
            "valores_selecao" => $this->valoresSelecao,
            "mascara" => $this->mascara,
            "setor_fase_id" => is_null($this->setorFase) ? "" : $this->setorFase->getId(),
            "tipo" => $this->tipo,
            "is_obrigatorio" => $this->isObrigatorio,
            "ativo" => $this->ativo,
            "circulacao_externa" => $this->circulacaoInterna,
        ];
    }

    public function imprimir(): string
    {
        return json_encode($this, JSON_PRETTY_PRINT);
    }
}
