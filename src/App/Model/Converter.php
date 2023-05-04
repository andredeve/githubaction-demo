<?php /** @noinspection PhpUnused */

namespace App\Model;

use App\Controller\UsuarioController;
use App\Enum\TipoLog;
use App\Enum\TipoUsuario;
use Core\Controller\AppController;
use Core\Exception\BusinessException;
use Core\Exception\SecurityException;
use Core\Model\AppModel;
use Core\Util\Functions;
use DateTime;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\Exception\ORMException;

/**
 * @Entity
 * @HasLifecycleCallbacks
 * @Table(name="converter")
 */
class Converter extends AppModel
{
    /**
     * @type int
     * @Id
     * @Column(type="integer",name="id")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @type Anexo
     * @ManyToOne(targetEntity="Anexo")
     * @JoinColumn(name="anexo_id", referencedColumnName="id",nullable=false,onDelete="CASCADE")
     */
    private $anexo;

    /**
     * @type DateTime
     * @Column(type="datetime",name="data_cadastro",nullable=false)
     */
    private $dataCadastro;
    
    /**
     * @type DateTime
     * @Column(type="datetime",name="data_iniciio_convercao",nullable=true)
     */
    private $dataInicio;
    
    /**
     * @type DateTime
     * @Column(type="datetime",name="data_inicio_ocr",nullable=true)
     */
    private $dataInicioOCR;
    
    /**
     * @type DateTime
     * @Column(type="datetime",name="data_termino_convercao",nullable=true)
     */
    private $dataTermino;
    
    /**
     * @type float
     * @Column(type="decimal",name="ultimo_tamanho",nullable=true)
     */
    private $ultimaTamanho;
    
    /**
     * @type string
     * @Column(type="string",name="observacao",nullable=true)
     */
    private $observacao;

    function __construct()
    {
        $this->dataCadastro = new DateTime();
    }

    function getId(): ?int {
        return $this->id;
    }

    function getAnexo() {
        return $this->anexo;
    }

    function getDataCadastro(): DateTime
    {
        return $this->dataCadastro;
    }
    function getDataInicio() {
        return $this->dataInicio;
    }

    function getUltimaTamanho() {
        return $this->ultimaTamanho;
    }

    function getDataTermino() {
        return $this->dataTermino;
    }
    
    function getDataInicioOCR() {
        return $this->dataInicioOCR;
    }
    
    function getObservacao() {
        return $this->observacao;
    }

    function setObservacao($observacao) {
        $this->observacao = $observacao;
    }

    function setDataInicioOCR($dataInicioOCR) {
        $this->dataInicioOCR = $dataInicioOCR;
    }

        
    function setDataTermino($dataTermino) {
        $this->dataTermino = $dataTermino;
    }
    
    function setUltimaTamanho($ultimaTamanho) {
        $this->ultimaTamanho = $ultimaTamanho;
    }

        
    function setDataInicio($dataInicio) {
        $this->dataInicio = $dataInicio;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    function setAnexo($anexo) {
        $this->anexo = $anexo;
    }

    function setDataCadastro($dataCadastro) {
        $this->dataCadastro = $dataCadastro;
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws ORMException
     * @throws Exception
     */
    public static function listarConversoesNaFila(): array
    {
        $converter = new Converter();
        return $converter->listarPorCampos(array("dataTermino" => null));
    }
    
    public static function listarConvercoesIniciadas(): array
    {
        $converter = new Converter();
        $lista = $converter->listar();
        $iniciados = array();
        foreach ($lista as $converter){
            if($converter->getDataInicio()){
                $iniciados[] = $converter;
            }
        }
        return $iniciados;
    }
    
    public function convercaoIniciada(): bool
    {
        return !empty($this->getDataInicio());
    }

    /** @PrePersist
     * @throws BusinessException
     */
    public function alreadyExists()
    {
        $existe = $this->buscarPorCampos(array("anexo"=> $this->getAnexo()));
        if($existe){
            throw new BusinessException("Anexo já foi inserido na lista para converter.");
        }
    }

    public function inserir($validarSomenteLeitura = true, bool $considerarPermissoes = true): ?int
    {
        try{
            return $this->getDAO()->inserir($this, $validarSomenteLeitura);
        } catch (\Exception $e){
            error_log($e->getMessage());
            error_log($e->getTraceAsString());
        }
        return 0;
    }


    public function atualizar(bool $validarSomenteLeitura = true,$validarPermissao = true)
    {
        echo "File: ".__FILE__. " Linha: ". __LINE__;
        echo "<pre>";
        var_dump("Enviar para assinatura", (new \Exception())->getTraceAsString());
        echo "<pre>";
        die();
        $this->getDAO()->merge();
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws ORMException
     * @throws Exception
     */
    public function listarNaoIniciada(){
        return $this->getDAO()->listarNaoIniciada();
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws ORMException
     * @throws Exception
     */
    function getTextoFila(): string
    {
        return  "O arquivo está na fila de conversão, antes dele existe {$this->getPosicaoFila()} arquivo(s). Para ver a posição atualizada na fila pressione F5. ";
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws ORMException
     * @throws Exception
     */
    function getPosicaoFila(): int
    {
        $posicaoFila = 0;
        if(!$this->getDataTermino()){
            foreach($this->listarConversoesNaFila() as $converterFila ){
                if(intval($converterFila->getId()) >= intval($this->getId())){
                    break;
                }
                $posicaoFila++;
            }
        }
        return $posicaoFila;
    }

    public function jsonSerialize(): ?array
    {
        return [
            "id" => $this->id,
            "anexo_id" => is_null($this->anexo) ? "" : $this->anexo->getId(),
            "data_cadastro" => Functions::formatarData($this->dataCadastro),
            "data_inicio_convercao" => Functions::formatarData($this->dataInicio),
            "data_inicio_ocr" => is_null($this->dataInicioOCR) ? "" : Functions::formatarData($this->dataInicioOCR),
            "data_termino_convercao" => is_null($this->dataTermino) ? "" : Functions::formatarData($this->dataTermino),
            "ultimoTamanho" => $this->ultimaTamanho,
            "observacao" => $this->observacao
        ];
    }

    public function imprimir(): string
    {
        return json_encode($this, JSON_PRETTY_PRINT);
    }
}
