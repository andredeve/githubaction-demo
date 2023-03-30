<?php /** @noinspection PhpUnused */

namespace App\Model;

use App\Controller\ProcessoController;
use Core\Model\AppModel;
use DateTime;

/**
 * @Entity
 * @Table(name="documento_requerido")
 */
class DocumentoRequerido extends AppModel
{

    /**
     * @Id
     * @Column(type="integer",name="id")
     * @GeneratedValue(strategy="AUTO")
     */
    public $id;

    /**
     * @ManyToOne(targetEntity="Tramite", inversedBy="documentosRequerimentosCadastrados")
     * @JoinColumn(name="tramite_cadastro_id", referencedColumnName="id",nullable=false)
     */
    public $tramiteCadastro;
    
    /**
     * @ManyToOne(targetEntity="Tramite", inversedBy="documentosRequerimentosValidar")
     * @JoinColumn(name="tramite_validacao_id", referencedColumnName="id",nullable=true)
     */
    public $tramiteValidar;
    
    
    /**
     * @ManyToOne(targetEntity="Anexo")
     * @JoinColumn(name="anexo_id", referencedColumnName="id", unique=true)
     */
    public $anexo;

    
    /**
     * @ManyToOne(targetEntity="Usuario")
     * @JoinColumn(name="usuario_id", referencedColumnName="id",nullable=true)
     */
    public $usuario;
    
    /**
     * @Column(type="boolean",name="is_obrigatorio")
     */
    public $isObrigatorio; 
    
    /**
     * @Column(type="boolean",name="is_assinatura_obrigatoria")
     */
    public $isAssinaturaObrigatoria;
    
    /**
     * @Column(type="datetime",name="data_cadastro",nullable=false)
     */
    public $dataCadastro;

    function __construct()
    {
        $this->dataCadastro = new DateTime();

    }

    function getTramiteValidar() {
        return $this->tramiteValidar;
    }

    function setTramiteValidar($tramiteValidar) {
        $this->tramiteValidar = $tramiteValidar;
    }
        
    function getId(): ?int {
        return $this->id;
    }

    function getAnexo() {
        return $this->anexo;
    }

    function getUsuario() {
        return $this->usuario;
    }

    function getIsObrigatorio() {
        return $this->isObrigatorio;
    }

    function getIsAssinaturaObrigatoria() {
        return $this->isAssinaturaObrigatoria;
    }

    function getDataCadastro(): ?DateTime
    {
        return $this->dataCadastro;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    function getTramiteCadastro() {
        return $this->tramiteCadastro;
    }

    function setTramiteCadastro($tramiteCadastro) {
        $this->tramiteCadastro = $tramiteCadastro;
    }

    
    function setAnexo($anexo) {
        $this->anexo = $anexo;
    }

    function setUsuario($usuario) {
        $this->usuario = $usuario;
    }

    function setIsObrigatorio($isObrigatorio) {
        $this->isObrigatorio = $isObrigatorio;
    }

    function setIsAssinaturaObrigatoria($isAssinaturaObrigatoria) {
        $this->isAssinaturaObrigatoria = $isAssinaturaObrigatoria;
    }

    function setDataCadastro($dataCadastro) {
        $this->dataCadastro = $dataCadastro;
    }

    function isCumprido(): bool
    {

        if(!$this->getIsObrigatorio() && !$this->getIsAssinaturaObrigatoria()){
            // Não é obrigatório
            return true;
        }

        if($this->getIsObrigatorio() && !$this->getIsAssinaturaObrigatoria() && $this->getAnexo()->getArquivo()){
            //É obrigatório porem nenhum arquivo foi informado
            return true;
        }
        
        if($this->getIsAssinaturaObrigatoria() &&  $this->getAnexo()->getArquivo()  ){
            
            
            
            $anexosStatus = array();
            
            
            $lxSignAnexosIds = $this->getAnexo()->getProcesso()->buscarLxSignIdDosAnexos();
            
            if (!empty($lxSignAnexosIds)) {
                $anexosStatusTemp = ProcessoController::buscarStatusAssinaturas($lxSignAnexosIds);
                foreach ($anexosStatusTemp as $status) {
                    $anexosStatus[$status->id] = $status->status;
                }
            }
            
            
            $assinatura = Assinatura::buscarPorAnexo($this->getAnexo());
            
            if (!is_null($assinatura) && isset($anexosStatus[$assinatura->getLxsign_id()])) {
                if($anexosStatus[$assinatura->getLxsign_id()] == "Finalizado"){
                    return true;
                }
            }
        }
        
        return false;
    }
}