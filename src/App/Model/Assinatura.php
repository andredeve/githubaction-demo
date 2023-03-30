<?php /** @noinspection PhpUnused */

namespace App\Model;

use App\Controller\AssinaturaController;
use App\Exception\LxSignException;
use Core\Model\AppModel;
use Core\Util\Functions;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Exception\ORMException;
use Exception;


// TODO: Salvar status da assinatura. Caso o status seja concluído (excluído, totalmente assinado ou afins), não haverá necessidade de ficar reconsultando no LxSign.

/**
 * @Entity
 * @HasLifecycleCallbacks
 * @Table(name="assinatura")
 */
class Assinatura extends AppModel
{

    /**
     * @type int
     * @Id
     * @Column(type="integer",name="id")
     * @GeneratedValue(strategy="AUTO")
     */
    public $id;

    /**
     * @type Setor
     * @ManyToOne(targetEntity="Setor")
     * @JoinColumn(name="setor_id", referencedColumnName="id",nullable=true)
     */
    public $setor;

    /**
     * @type Anexo
     * @ManyToOne(targetEntity="Anexo", inversedBy="assinatura")
     * @JoinColumn(name="anexo_id", referencedColumnName="id", onDelete="CASCADE")
     */
    public $anexo;

    
    /**
     * @type Usuario
     * @ManyToOne(targetEntity="Usuario")
     * @JoinColumn(name="usuario_id", referencedColumnName="id",nullable=true)
     */
    public $usuario;
    
    /**
     * @type int
     * @Column(type="integer",name="numero",nullable=false)
     */
    public $numero;
    
    /**
     * @type string
     * @Column(type="string",name="grupo_signatario",nullable=true)
     */
    public $grupo;

    /**
     * @type string
     * @Column(type="string", name="signatarios", nullable=true)
     */
    public $signatarios;

    /**
     * @type int
     * @Column(type="integer",name="tipo_documento", nullable=true)
     */
    public $tipoDocumento;
    
    /**
     * @Column(type="integer",name="empresa", nullable=true)
     * @type int
     */
    public $empresa;

    /**
     * @type int
     * @Column(type="integer",name="exercicio",nullable=false)
     */
    public $exercicio;
    
    /**
     * @type int
     * @Column(type="integer",name="lxsign_id",nullable=true)
     */
    public $lxsign_id;
    
    
    /**
     * @type DateTime
     * @Column(type="datetime",name="data_limite_assinatura",nullable=false)
     */
    public $dataLimiteAssinatura;
    
    /**
     * @type DateTime
     * @Column(type="datetime",name="data_cadastro",nullable=false)
     */
    public $dataCadastro;
    
    /**
     * @type bool
     * @Column(type="boolean",name="preenvio", nullable=true)
     */
    public $preenvio;
    
    function __construct()
    {
        $this->dataCadastro = new DateTime();

    }

    /**
     * @return ArrayCollection
     */
    function getGrupoAsArray() {
        return new ArrayCollection(array_map(function ($item) {
            return intval($item);
        }, explode(",", $this->grupo)));
    }

    /**
     * @return ArrayCollection
     */
    function getSignatariosAsArray() {
        return new ArrayCollection(array_map(function ($item) {
            return intval($item);
        }, explode(",", $this->signatarios)));
    }
    
    function getId(): ?int {
        return $this->id;
    }

    function getSetor() {
        return $this->setor;
    }

    /**
     * @return Anexo
     */
    function getAnexo() {
        return $this->anexo;
    }

    function getUsuario() {
        return $this->usuario;
    }

    function getDataCadastro(): DateTime
    {
        return $this->dataCadastro;
    }
    
    function getNumero() {
        return $this->numero;
    }

    function getExercicio() {
        return $this->exercicio;
    }

    function getDataLimiteAssinatura() {
        return $this->dataLimiteAssinatura;
    }
    
    function getLxsign_id() {
        return $this->lxsign_id;
    }
    
    function getGrupo(): ?string {
        return $this->grupo;
    }

    function setGrupo($grupo) {
        if (is_array($grupo)) {
            $grupo = implode(",", $grupo);
        }
        $this->grupo = $grupo;
    }

    function getTipoDocumento() {
        return $this->tipoDocumento;
    }

    function setTipoDocumento($tipo) {
        $this->tipoDocumento = $tipo;
    }
        
    function setLxsign_id($lxsign_id) {
        $this->lxsign_id = $lxsign_id;
    }

    
    function setNumero($numero) {
        $this->numero = $numero;
    }

    function setExercicio($exercicio) {
        $this->exercicio = $exercicio;
    }

    function setDataLimiteAssinatura($dataLimiteAssinatura) {
        $this->dataLimiteAssinatura = $dataLimiteAssinatura;
    }


    function setId(?int $id): void {
        $this->id = $id;
    }

    function setSetor($setor) {
        $this->setor = $setor;
    }

    function setAnexo($anexo) {
        $this->anexo = $anexo;
    }

    function setUsuario($usuario) {
        $this->usuario = $usuario;
    }

    function setDataCadastro($dataCadastro) {
        $this->dataCadastro = $dataCadastro;
    }
    
    function getPreenvio() {
        return $this->preenvio;
    }

    function setPreenvio($preenvio) {
        $this->preenvio = $preenvio;
    }

    /**
     * @return int|null
     */
    public function getEmpresa(): ?int
    {
        return $this->empresa;
    }

    /**
     * @param int $empresa
     */
    public function setEmpresa(int $empresa): void
    {
        $this->empresa = $empresa;
    }

    public function getSignatarios(): ?string
    {
        return $this->signatarios;
    }

    public function setSignatarios(?string $signatarios): void
    {
        $this->signatarios = $signatarios;
    }

    public function isAtivoNaAssinatura(): bool
    {
        if($this->getLxsign_id()){
            try {
                $objeto = (new AssinaturaController())->consultarAssinatura($this);
                if(!is_null($objeto) && isset($objeto->document)){
                    return  true;
                }
            } catch (LxSignException $e) {
                Functions::escreverLogErro($e);
            }
        }
        return false;
    }

    /**
     * @PrePersist
     * @return void
     * @throws ORMException
     * @throws \Doctrine\ORM\ORMException
     * @throws Exception
     */
    public function alreadyExists()
    {
        $assinaturas = (new Assinatura())->listarPorCampos(array("anexo"=> $this->anexo));
        if(count($assinaturas) > 0){
            throw new Exception("Anexo já foi enviado para a assinatura digital.");
        }
    }

    public function jsonSerialize(): ?array
    {
        return [
            "id" => $this->id,
            "setor_id" => is_null($this->setor) ? "" : $this->setor->getId(),
            "anexo_id" => is_null($this->anexo) ? "" : $this->anexo->getId(),
            "usuario_id" => is_null($this->usuario) ? "" : $this->usuario->getId(),
            "numero" => $this->numero,
            "grupo_signatario" => $this->grupo,
            "tipo_documento_id" => $this->tipoDocumento,
            "exercicio" => $this->exercicio,
            "lxsign_id" => $this->lxsign_id,
            "empresa" => $this->empresa,
            "data_limite_assinatura" => Functions::formatarData($this->dataLimiteAssinatura),
            "data_cadastro" => Functions::formatarData($this->dataCadastro),
            "preenvio" => $this->preenvio,
        ];
    }

    public function imprimir(): string
    {
        return json_encode($this, JSON_PRETTY_PRINT);
    }

    /**
     * @param Anexo $anexo
     * @return Assinatura|null
     * @throws \Doctrine\ORM\ORMException
     */
    public static function buscarPorAnexo(Anexo $anexo): ?Assinatura
    {
        return (new Assinatura())->buscarPorCampos(array("anexo"=> $anexo));
    }
}
