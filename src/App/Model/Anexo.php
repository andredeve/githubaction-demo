<?php

namespace App\Model;

use App\Controller\AssinaturaController;
use App\Controller\ProcessoController;
use App\Controller\SolicitacaoController;
use App\Controller\UsuarioController;
use App\Enum\PermissaoStatus;
use App\Enum\StatusSolicitacao;
use App\Enum\TipoLog;
use App\Log\HistoricoAnexo;
use App\Model\Dao\AnexoDao;
use App\Model\Dao\AssinaturaDao;
use App\Model\Dao\ClassificacaoDao;
use App\Model\Dao\DocumentoRequeridoDao;
use App\Util\Tesseract\TesseractOCR;
use Core\Controller\AppController;
use Core\Exception\BusinessException;
use Core\Exception\TechnicalException;
use Core\Model\AppModel;
use Core\Util\Functions;
use Core\Util\Http\Client\Builder;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Exception;
use FilesystemIterator;
use FPDF;
use Oro\ORM\Query\AST\Platform\Functions\Mysql\Date;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use const FILE_PATH;

/**
 * @Entity
 * @HasLifecycleCallbacks
 * @Table(name="anexo",indexes={ @Index(name="exercicio_index", columns={"exercicio"}),@Index(columns={"descricao","texto_ocr"},flags={"fulltext"})}))
 */
class Anexo extends AppModel
{

    /**
     * @Id
     * @Column(type="integer",name="id")
     * @GeneratedValue(strategy="AUTO")
     */
    public $id;

    /**
     * @Column(type="boolean",name="is_digitalizado")
     */
    public $isDigitalizado;

    /**
     * @Column(type="boolean",name="is_ocr_iniciado")
     */
    public $isOCRIniciado;

    /**
     * @Column(type="boolean",name="is_ocr_finalizado")
     */
    public $isOCRFinalizado;

    /**
     * @Column(type="boolean",name="is_auto_numeric", options={"default" : 0})
     */
    public $isAutoNumeric;

    /**
     * @Column(type="string",name="numero",nullable=true)
     */
    public $numero;

    /**
     * @Column(type="string",name="exercicio",nullable=true)
     */
    public $exercicio;

    /**
     * @type TipoAnexo
     * @ManyToOne(targetEntity="TipoAnexo")
     * @JoinColumn(name="tipo_anexo_id", referencedColumnName="id",nullable=true)
     */
    public $tipo;

    /**
     * @type Classificacao
     * @ManyToOne(targetEntity="Classificacao")
     * @JoinColumn(name="classificacao_id", referencedColumnName="id",nullable=true,onDelete="CASCADE")
     */
    public $classificacao;

    /**
     * @type DateTime
     * @Column(type="date", nullable=true)
     */
    public $dataValidade;

    /**
     * @Column(type="string",name="descricao",nullable=true)
     */
    public $descricao;

    /**
     * @Column(type="string",name="arquivo",length=255,nullable=true)
     */
    public $arquivo;

    /**
     * @type Processo
     * @ManyToOne(targetEntity="Processo", inversedBy="processo")
     * @JoinColumn(name="processo_id", referencedColumnName="id",nullable=false,onDelete="CASCADE")
     */
    private $processo;

    /**
     * @type Usuario
     * @ManyToOne(targetEntity="Usuario")
     * @JoinColumn(name="usuario_id", referencedColumnName="id",nullable=true)
     */
    public $usuario;

    /**
     * @Column(type="date",name="data",nullable=false)
     */
    public $data;

    /**
     * @Column(type="datetime",name="data_cadastro",nullable=false)
     */
    public $dataCadastro;

    /**
     * @Column(type="decimal",precision=10, scale=2,name="valor",nullable=true)
     */
    public $valor;

    /**
     * @Column(type="integer",name="qtde_paginas",nullable=true)
     */
    public $qtdePaginas;

    /**
     * @Column(type="text",name="texto_ocr",nullable=true)
     */
    public $textoOCR;

    /**
     * @type ImagemDigitalizada[]|Collection
     * @OneToMany(targetEntity="ImagemDigitalizada", mappedBy="anexo",cascade={"persist","remove"})
     */
    public $imagens;

    /**
     * OneToMany(targetEntity="Assinatura", mappedBy="anexo", cascade={"persist","remove"})
     * @type Assinatura[]|ArrayCollection
     */
    public $assinatura;

    /**
     * @OneToMany(targetEntity="Componente", mappedBy="anexo", cascade={"persist","remove"})
     * @type  Collection|Componente[]
     */
    private $componente;

    /**
     * @Column(type="text",name="codigo_importacao",nullable=true)
     */
    private $codigoImportacao;

    /**
     * @Column(type="date",name="novo_vencimento_processo",nullable=true)
     */
    public $novoVencimentoProcesso;

    /**
     * @Column(type="string",name="motivo_pendencia",length=255,nullable=true)
     */
    public $motivoPendencia;

    /**
     * @Column(type="boolean",name="is_circulacao_interna", options={"default" : 0})
     */
    public $isCirculacaoInterna;

    /**
     * @Column(type="string",name="paginacao",nullable=true)
     */
    public $paginacao;

    function __construct()
    {
        $this->imagens = new ArrayCollection();
        $this->assinatura = new ArrayCollection();
        $this->componente = new ArrayCollection();
        $this->dataCadastro = new DateTime();
        $this->isOCRIniciado = false;
        $this->isOCRFinalizado = false;
        $this->isDigitalizado = false;
        $this->isAutoNumeric = false;
        $this->isCirculacaoInterna = false;
    }

    /**
     * @param $isAutoNumeric
     */
    public function setIsAutoNumeric($isAutoNumeric): void
    {
        if ($isAutoNumeric && !$this->isAutoNumeric) {
            $this->isAutoNumeric = $isAutoNumeric;
            $this->numero = null;
            $this->gerarNumero();
        } else {
            $this->isAutoNumeric = $isAutoNumeric;
        }
    }

    /**
     * @return boolean
     */
    public function getIsAutoNumeric()
    {
        return $this->isAutoNumeric;
    }

    /**
     * @param $isCirculacaoInterna
     */
    public function setIsCirculacaoInterna($isCirculacaoInterna): void
    {
        $this->isCirculacaoInterna = $isCirculacaoInterna;
    }

    /**
     * @return bool
     */
    public function getIsCirculacaoInterna(): bool
    {
        return $this->isCirculacaoInterna;
    }

    /**
     * @param $motivoPendencia
     */
    function setMotivoPendencia($motivoPendencia)
    {
        $this->motivoPendencia = $motivoPendencia;
    }

    /**
     * @return string
     */
    function getMotivoPendencia(): ?string
    {
        return $this->motivoPendencia;
    }

    function getCodigoImportacao()
    {
        return $this->codigoImportacao;
    }

    function setCodigoImportacao($codigoImportacao)
    {
        $this->codigoImportacao = $codigoImportacao;
    }

    function getArquivoCarimbado($urlBase = false)
    {
        $infoArquivo = pathinfo($this->getArquivo(false, true, true));
        if (strtolower($infoArquivo["extension"]) !== "pdf") {
            return $this->getPathUrl() . $infoArquivo["filename"] . "." . $infoArquivo["extension"];
        }
        $assinatura = (new Assinatura())->buscarPorAnexo($this);
        if ($urlBase) {
//            if(!$this->isConvertido()){
//                return $this->getPathUrl().  $this->getArquivo(false, false, false);
//            }
            $auxCheck = $infoArquivo["dirname"] . "/" . $infoArquivo["filename"] . "_assinado_carimbado." . $infoArquivo["extension"];
            if (!empty($assinatura) && file_exists($auxCheck)) {
                return $this->getPathUrl() . "/" . $infoArquivo["filename"] . "_assinado_carimbado." . $infoArquivo["extension"];
            }
            return $this->getPathUrl() . $infoArquivo["filename"] . "_carimbado." . $infoArquivo["extension"];
        }
        $auxCheck = $infoArquivo["dirname"] . "/" . $infoArquivo["filename"] . "_assinado_carimbado." . $infoArquivo["extension"];
        if (!empty($assinatura) && file_exists($auxCheck)) {
            return $auxCheck;
        }
        return $infoArquivo["dirname"] . "/" . $infoArquivo["filename"] . "_carimbado." . $infoArquivo["extension"];
    }

    function getArquivoParaCarimbar($assinado = false)
    {
        $infoArquivo = pathinfo($this->getArquivoCarimbado());
        $nomeArquivo = $infoArquivo["dirname"] . "/" . $infoArquivo["filename"] . "_assinado." . $infoArquivo["extension"];
        if (($assinado && !file_exists($nomeArquivo))) {
            $infoArquivo = pathinfo($this->getArquivo(false, true, true));
            $nomeArquivo = $infoArquivo["dirname"] . "/" . $infoArquivo["filename"] . "_assinado." . $infoArquivo["extension"];
            return $nomeArquivo;
        } else if (!$assinado) {
            return $this->getArquivo(false, true, true);
        } else if (file_exists($this->getArquivoOriginal())) {
            return $this->getArquivoOriginal();
        }
        return $nomeArquivo;
    }

    function getArquivoOriginal($urlBase = false)
    {
        $infoArquivo = pathinfo($this->getArquivo(false, true, true));
        if ($urlBase) {
            return $this->getPathUrl() . $infoArquivo["filename"] . "_original." . $infoArquivo["extension"];
        }

        return $infoArquivo["dirname"] . "/" . $infoArquivo["filename"] . "_original." . $infoArquivo["extension"];
    }

    function getNovoVencimentoProcesso($formatar = false)
    {
        if (!empty($this->novoVencimentoProcesso) && $formatar) {
            return $this->novoVencimentoProcesso->format('d/m/Y');
        }
        return $this->novoVencimentoProcesso;
    }

    function setNovoVencimentoProcesso($novoVencimentoProcesso)
    {
        $this->novoVencimentoProcesso = $novoVencimentoProcesso;
    }

    function getComponente()
    {
        return $this->componente;
    }

    function setComponente($componente)
    {
        $this->componente = $componente;
    }

    function getAssinatura()
    {
        return $this->assinatura;
    }

    function setAssinatura($assinatura)
    {
        $this->assinatura = $assinatura;
    }

    function removerTodos()
    {
        $this->assinatura = new ArrayCollection();
    }

    /**
     * @param Assinatura|AppModel $assinatura
     * @return void
     */
    function adicionaAssinatura(Assinatura $assinatura)
    {
        if (is_null($this->assinatura)) {
            $this->assinatura = new ArrayCollection();
        } else if (!$this->assinatura->contains($assinatura)) {
            $this->assinatura->add($assinatura);
        }
    }

    function adicionaComponente(Componente $componente)
    {
        if (!$this->componente->contains($componente)) {
            $this->componente->add($componente);
        }
    }

    /**
     * @return mixed
     */
    public function getisOCRIniciado()
    {
        return $this->isOCRIniciado;
    }

    /**
     * @param mixed $isOCRIniciado
     */
    public function setIsOCRIniciado($isOCRIniciado)
    {
        $this->isOCRIniciado = $isOCRIniciado;
    }

    /**
     * @return mixed
     */
    public function getIsOCRFinalizado()
    {
        return $this->isOCRFinalizado;
    }

    /**
     * @param mixed $isOCRFinalizado
     */
    public function setIsOCRFinalizado($isOCRFinalizado)
    {
        $this->isOCRFinalizado = $isOCRFinalizado;
    }

    /**
     * @return mixed
     */
    public function getExercicio()
    {
        return $this->exercicio;
    }

    /**
     * @param mixed $exercicio
     */
    public function setExercicio($exercicio)
    {
        $this->exercicio = $exercicio;
    }

    function getId(): ?int
    {
        return $this->id;
    }

    function setId(?int $id): void {
        $this->id = $id;
    }

    /**
     * @param bool $autonumerar
     * @return string|null
     */
    function getNumero(bool $autonumerar = false): ?string
    {
        if ($autonumerar) {
            $this->setIsAutoNumeric($autonumerar);
        }
        return $this->numero;
    }

    function getDescricao()
    {
        return $this->descricao;
    }

    /**
     * @return DateTime
     */
    function getDataValidade()
    {
        return $this->dataValidade;
    }

    function setDataValidade($dataValidade)
    {
        $this->dataValidade = $dataValidade;
    }

    function getData($formatar = false)
    {
        if (!empty($this->data) && $formatar) {
            return $this->data->format('d/m/Y');
        }
        return $this->data;
    }

    function getTipo()
    {
        if ($this->tipo == null) {
            return new TipoAnexo();
        }
        return $this->tipo;
    }

    function getValor($formatar = false)
    {
        if (!empty($this->valor) && $formatar) {
            return Functions::decimalToReal($this->valor);
        }
        return $this->valor;
    }

    function getQtdePaginas()
    {
        return $this->qtdePaginas;
    }

    function getClassificacao()
    {
        if ($this->classificacao == null) {
            return new Classificacao();
        }
        return $this->classificacao;
    }

    function setClassificacao($classificacao)
    {
        $this->classificacao = $classificacao;
    }

    function setNumero($numero)
    {
        $this->numero = $numero;
        $this->isAutoNumeric = 0;
    }

    function setDescricao($descricao)
    {
        $this->descricao = $descricao;
    }

    function setData($data)
    {
        $this->data = $data;
    }

    function setTipo($tipo)
    {
        $this->tipo = $tipo;
    }

    function setValor($valor)
    {
        $this->valor = $valor;
    }

    function setQtdePaginas($qtdePaginas)
    {
        $this->qtdePaginas = $qtdePaginas;
    }

    /**
     *
     * @return DateTime
     */
    function getDataCadastro()
    {
        return $this->dataCadastro;
    }

    function setDataCadastro($dataCadastro)
    {
        $this->dataCadastro = $dataCadastro;
    }

    /**
     * @return Processo
     */
    function getProcesso()
    {
        return $this->processo;
    }

    function setProcesso($processo)
    {
        $this->processo = $processo;
    }

    function getArquivoPath()
    {
        return $this->getPath() . $this->arquivo;
    }

    private function getNomeArquivo($path, $arquivo)
    {
        if (is_file($path . $arquivo . ".pdf")) {
            return $arquivo . ".pdf";
        } elseif (is_file($path . $arquivo . '.PDF')) {
            return $arquivo . '.PDF';
        }
        return $this->arquivo;
    }

    function getArquivoTratado()
    {
        $nome_arquivo_sem_extensao = pathinfo($this->arquivo)['filename'];
        $arquivo = $this->getNomeArquivo($this->getPath(), $nome_arquivo_sem_extensao);
        if ($arquivo == null) {
            return $this->getNomeArquivo(self::getTempPath(), $nome_arquivo_sem_extensao);
        } else {
            return $arquivo;
        }
        return $this->arquivo;
    }

    function getArquivoUrl()
    {
        $assinatura = (new Assinatura())->buscarPorAnexo($this);
        if (!empty($assinatura)) {
            $app = AppController::getConfig();
            $fileName = $app["lxsign_url"] . "documento/versaoImpressa/" . $assinatura->getLxsign_id();
            $opts = array(
                "ssl" => array(
                    "verify_peer" => false,
                    "verify_peer_name" => false,
                )
            );
            $context = stream_context_create($opts);
            if (strlen(file_get_contents($fileName, false, $context)) > 100) {
                return $app["lxsign_url"] . "documento/versaoImpressa/" . $assinatura->getLxsign_id();
            }
        }

        if ($this->arquivo != null) {
            return $this->getPathUrl() . $this->getArquivoTratado();
        }
        return null;
    }

    function getBaseUrl()
    {
        $assinatura = new Assinatura();
        $assinatura = $assinatura->buscarPorAnexo($this);
        if (!empty($assinatura)) {
            $app = AppController::getConfig();
            $fileName = $app["lxsign_url"] . "documento/documentoAssinado/" . $assinatura->getLxsign_id();
            $opts = array(
                "ssl" => array(
                    "verify_peer" => false,
                    "verify_peer_name" => false,
                )
            );
            $context = stream_context_create($opts);
            if (strlen(file_get_contents($fileName, false, $context)) > 100) {
                return $app["lxsign_url"];
            }
        }

        return APP_URL;
    }

    function getArquivo($pdf = false, $verificar_extensao = false, $path = false, $assinado = false)
    {
        if ($assinado) {
            $info = explode(".", $this->arquivo);
            $file = $info[0] . "_assinado." . $info[1];
            if ($path) {
                $file = $this->getPath() . $file;
            }
            return $file;
        }
        if ($pdf && $this->arquivo != null) {
            $pathinfo = pathinfo($this->arquivo);
            return $pathinfo["filename"] . ".pdf";
        }

        if ($path && $this->arquivo != null) {
            $arquivo_tratado = $this->getArquivoTratado(); //nome do arquivo
            $arquivo_path = $this->getPath() . $arquivo_tratado;
            if (is_file($arquivo_path)) {
                return $arquivo_path;
            }
        }

        return $this->arquivo;
    }

    function getConverter()
    {
        $converter = new Converter();
        return $converter->buscarPorCampos(array('anexo' => $this));
    }

    function setArquivo($arquivo)
    {
        $this->arquivo = $arquivo;
    }

    function getTextoOCR()
    {
        return $this->textoOCR;
    }

    function setTextoOCR($textoOCR)
    {
        $this->textoOCR = $textoOCR;
    }

    /**
     * @return Usuario
     */
    function getUsuario()
    {
        return $this->usuario;
    }

    function setUsuario($usuario)
    {
        $this->usuario = $usuario;
    }

    /**
     * @PreUpdate
     */
    public function antesAtualizar()
    {
        $this->moverArquivos();
    }

    /**
     * @PrePersist
     * @throws BusinessException
     */
    public function antesInserir()
    {
        /**
         * @var AnexoDao $anexoDao
         */
        $permissao = parent::getPermissao();
//        Checar se o anexo é único.
        $anexoDao = $this->getDAO();
        if (!empty($this->getNumero()) && !is_null($this->processo) && !is_null($this->processo->getId()) && !is_null($this->tipo)) {
            $criteria = [
                "processo" => $this->processo,
                "tipo" => $this->tipo,
                "numero" => $this->numero
            ];
            try {
                if ($anexoDao->qtdPorCampos($criteria) > 0) {
                    throw new BusinessException("Já existe um documento cadastrado para esse tipo de documento e número: " . print_r(['processo'=>$this->processo->getId(), 'tipo'=>$this->tipo->getId(), 'numero'=>$this->numero], true));
                }
            } catch (ORMException $e) {
                Functions::escreverLogErro($e);
            }
        }
//        Fim da checagem de anexo único.
        if (!$permissao || !$permissao->getInserir()) {
            throw new BusinessException("Usuário não tem permissão para cadastrar o Anexo/Documento.");
        }

        $this->moverArquivos();
    }

    public function moverArquivos()
    {
        $arquivo = $this->arquivo;
        $path = $this->getPath();
        if ($arquivo != null) {
            //$novo_arquivo = $this->gerarNomeArquivo() . "." . $this->getExtensao();
            if (is_file(Processo::getTempPath() . $arquivo)) {
                rename(Processo::getTempPath() . $arquivo, $path . $arquivo);
            } elseif (!empty($this->id)) {
                $anexo_old = (new Anexo())->buscar($this->id);
                $path_antigo = $anexo_old->getPath();
                if (is_file($path_antigo . $anexo_old->getArquivo())) {
                    rename($path_antigo . $anexo_old->getArquivo(), $path . $arquivo);
                }
            }
        }
        foreach ($this->getImagens() as $imagem) {
            if (is_file(Processo::getTempPath() . $imagem->getArquivo()))
                rename(Processo::getTempPath() . $imagem->getArquivo(), $path . $imagem->getArquivo());
        }
    }

    static function getTempPath()
    {
        return Processo::getTempPath();
    }

    function gerarNomeArquivoOld()
    {
        $tipo = !empty($this->tipo) ? $this->getTipo()->getDescricao(true) : "";
        $numero = !empty($this->numero) ? "_" . Functions::sanitizeString($this->numero) : "";
        return !empty($this->numero) && !empty($this->tipo) ? $tipo . $numero : time();
    }

    function gerarNomeArquivo()
    {
        //$descricao = !empty($this->descricao) ? "_" . Functions::sanitizeString($this->descricao) : "";
        $numero = !empty($this->numero) ? "_" . Functions::sanitizeString($this->numero) : "";
        $tipo = !empty($this->tipo) ? $this->getTipo()->getDescricao(true) : "";
        return !empty($this->numero) && !empty($this->tipo) ? "{$this->id}_" . $tipo . $numero : time();
    }

    function getPath($fullPath = true, $search = true)
    {
        if ($this->processo->getId() == null) {
            $path = Processo::getTempPath();
        } else {
            $assunto = null;
            if ($this->processo->getAssuntos()->count() > 0) {
                foreach ($this->processo->getAssuntos() as $assuntoFluxograma) {
                    $file = $this->processo->getAnexosPath($assuntoFluxograma->getAssunto()) . $this->getTipo()->getDescricao(true) . '/' . $this->getArquivo();

                    if (is_file($file)) {
                        $assunto = $assuntoFluxograma->getAssunto();
                        break;
                    }
                }
            }
            $tipo = $this->tipo != null ? $this->getTipo()->getDescricao(true) . '/' : "";
            $path = $this->processo->getAnexosPath($assunto) . $tipo;

        }

        if (!is_dir($path)) {
            $oldmask = umask(0);
            if (!mkdir($path, 0777, true) && !is_dir($path)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $path));
            }
            umask($oldmask);
        }

        if ($search) {
            $search = $path . '/' . $this->getArquivo();
            if (!file_exists($search)) {
                $processoPath = FILE_PATH . 'processos/' . $this->processo->getPath();
                $arquivo = $this->getArquivo();

                $result = $this->searchFile($processoPath, $arquivo);
                if ($result != false) {
                    $path = $result;
                }
            }
        }

        if ($fullPath) {
            return $path;
        } else {
            $path = str_replace(FILE_PATH . 'processos/', '', $path);
            return $path;
        }

    }


    function getPathUrl($fullPath = true)
    {
        if (is_file(Processo::getTempPath() . $this->arquivo) || empty($this->id)) {
            return Processo::getTempPathUrl();
        }
        $assunto = null;
        if ($this->processo->getAssuntos()->count() > 0) {
            foreach ($this->processo->getAssuntos() as $assuntoFluxograma) {
                $file = $this->processo->getAnexosPath($assuntoFluxograma->getAssunto()) . $this->getTipo()->getDescricao(true) . '/' . $this->getArquivo();
                if (is_file($file)) {
                    $assunto = $assuntoFluxograma->getAssunto();
                    break;
                }
            }
        }

        if ($fullPath) {
            if (date_format($this->getdataCadastro(),"Y") < $this->processo->getExercicio()){
                $path = str_replace('/' . $this->processo->getExercicio() . '/', '/' . date_format($this->getdataCadastro(),"Y") . '/', $this->processo->getAnexoUrl($assunto));
                return $path . $this->getTipo()->getDescricao(true) . '/';
            }
            return $this->processo->getAnexoUrl($assunto) . $this->getTipo()->getDescricao(true) . '/';
        } else {
            if (date_format($this->getdataCadastro(),"Y") < $this->processo->getExercicio()){
                $path = str_replace('/' . $this->processo->getExercicio() . '/', '/' . date_format($this->getdataCadastro(),"Y") . '/', $this->processo->getAnexoUrl());
                return $path . $this->getTipo()->getDescricao(true) . '/';
            }
            return $this->processo->getAnexoUrl() . $this->getTipo()->getDescricao(true) . '/';
        }

    }

    function searchFile($dir, $file)
    {

        $dir = rtrim($dir, "/");

        $search = $dir . '/' . $file;

        // Verifica se o arquivo existe no diretório
        if ((!is_dir($search)) && (file_exists($search))) {
            return $dir;
        } else {
            $scan = scandir($dir);
            //Percorre todos os arquivos e diretórios
            foreach ($scan as $path) {
                $newDir = "$dir/$path";
                if (is_dir($newDir) && $path != '.' && $path != '..') {
                    $result = $this->searchFile($newDir, $file);
//                    var_dump($result,$newDir);
                    if ($result !== false) {
                        return $result;
                    }
                }
            }
            return false; //Retorna falso caso não encontre
        }
    }

    function getImagens($json = false)
    {
        if ($json) {
            $json = array();
            foreach ($this->imagens as $imagem) {
                $json[] = $imagem->getArquivo();
            }
            return json_encode($json);
        }
        return $this->imagens;
    }

    function setImagens($imagens)
    {
        $this->imagens = $imagens;
    }

    function adicionaImagem(ImagemDigitalizada $imagem)
    {
        if (!$this->imagens->contains($imagem)) {
            $this->imagens->add($imagem);
        }
    }

    function convertToPdf()
    {
        $nomeSemExtensao = str_replace(".pdf", "", $this->getArquivo());
        $imgaeFileName = $nomeSemExtensao . "_0.jpeg";

        $im = new \Imagick();
        $im->setResolution(300, 300);     //set the resolution of the resulting jpg
        $im->readImage($this->getPath() . "" . $nomeSemExtensao . '[0].pdf');    //[0] for the first page
        $im->setImageFormat('jpg');
        header('Content-Type: image/jpeg');
        file_get_contents($this->getPath() . "/" . $imgaeFileName, true, $im);
    }

    function removeImagem(ImagemDigitalizada $imagem)
    {
        $this->imagens->removeElement($imagem);
    }

    function getIsDigitalizado()
    {
        return $this->isDigitalizado;
    }

    function setIsDigitalizado($isDigitalizado)
    {
        $this->isDigitalizado = $isDigitalizado;
    }

    static function limparTemp()
    {
        $dir = Processo::getTempPath();
        $di = new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS);
        $ri = new RecursiveIteratorIterator($di, RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($ri as $file) {
            $file->isDir() ? (strpos($file, '_digitalizacao') === false ? rmdir($file) : null) : unlink($file);
        }
        return true;
    }

    function getExtensao($lower = true)
    {
        if (!empty($this->arquivo)) {
            $pathinfo = pathinfo($this->arquivo);
            return $lower ? strtolower($pathinfo["extension"]) : $pathinfo["extension"];
        }
        return null;
    }

    function isImage()
    {
        $file = $this->getPath() . $this->arquivo;
        if (is_file($file)) {
            return is_array(getimagesize($file));
        }
        return false;
    }

    function temOCR()
    {
        return $this->isDigitalizado && !empty($this->textoOCR) && $this->getExtensao() == 'pdf';
    }

    function listarSemOCR($ano = null)
    {
        return $this->getDAO()->listarSemOCR($ano);
    }

    function listarComArquivos($ano = null, $legado = null)
    {
        return $this->getDAO()->listarComArquivos($ano, $legado);
    }

    /**
     * Realiza OCR do arquivo anexo
     */
    function realizarOCR()
    {
        if (!$this->isOCRIniciado) {
            $this->isOCRIniciado = true;
            $this->atualizar();
            $nome_arquivo_pdf = pathinfo($this->getArquivo())['filename'];
            $path = $this->getPath();
            $sourceFile = $path . $this->getArquivo();
            if (is_file($sourceFile) && $this->imagens->count() > 0) {
                $outputFile = $path . $nome_arquivo_pdf;
                $this->textoOCR = (new TesseractOCR($sourceFile))->lang('por')->outPutFile($outputFile)->configFile('pdf')->run();

                $this->arquivo = $nome_arquivo_pdf . ".pdf";
                //Se foi criado pdf com sucesso e foi retirado o conteúdo da digitalização
                if (is_file($path . $nome_arquivo_pdf . ".pdf") && !empty($this->textoOCR)) {
                    $this->isOCRFinalizado = true;
                    $this->atualizar();
                } else {
                    $this->isOCRIniciado = false;
                    $this->atualizar();
                    throw new Exception("Erro ao realizar OCR para $this.");
                }
            } else {

                $this->isOCRIniciado = false;
                $this->atualizar();

                ob_start();
                echo __FILE__ . ' LINHA: ' . __LINE__;
                echo '<pre>';
                var_dump("OCR não realizada. Arquivo fonte $sourceFile não encontrado.");
                echo '</pre>';
                $print_log = ob_get_contents();
                ob_clean($print_log);
                throw new Exception("OCR não realizada. Arquivo fonte $sourceFile não encontrado.");
            }
            $this->atualizar();
        }
    }

    /**
     * Gerar uma pdf a partir de uma imagem
     */
    public function gerarPdf()
    {
        require_once APP_PATH . 'lib/fpdf/fpdf.php';
        $dir = $this->getPath();
        $pathinfo = pathinfo($dir . $this->arquivo);
        $file_pdf = $pathinfo["filename"] . ".pdf";
        if (in_array($pathinfo["extension"], array('jpg', 'jpeg', 'gif', 'png')) && !is_file($dir . $file_pdf)) {
            $pdf = new FPDF();
            $pdf->AddPage();
            $pdf->Image($dir . $this->arquivo, 0, 0, 210);
            $pdf->Output("F", $dir . $file_pdf);
        } else if ($this->imagens->count() > 0) {
            require_once APP_PATH . 'lib/fpdf/fpdf.php';
            $pdf = new FPDF();
            foreach ($this->imagens as $imagem) {
                $pdf->AddPage();
                $pdf->Image($dir . $imagem->getArquivo(), 0, 0, 210);
            }
            $pdf->Output("F", $dir . $file_pdf);
        }
    }

    public function listarQtde($group_by, $tipo_documento_id = null, $data_ini = null, $data_fim = null, $usuario_id = null)
    {
        return $this->getDAO()->listarQtde($group_by, $tipo_documento_id, $data_ini, $data_fim, $usuario_id);
    }

    public function listarAnexos($tipo_documento_id = null, $data_ini = null, $data_fim = null, $usuario_id = null)
    {
        return $this->getDAO()->listarAnexos($tipo_documento_id, $data_ini, $data_fim, $usuario_id);
    }

    public function getTamanho()
    {
        return Functions::getTamanhoArquivo($this->getPath() . $this->arquivo);
    }

    function getPreview()
    {
        if ($this->imagens->count() == 0) {
            return $this->getArquivoUrl();
        }
        $path_url = $this->getPathUrl();
        $imagens = array();
        foreach ($this->imagens as $imagem) {
            $imagens[] = $path_url . $imagem->getArquivo();
        }
        return implode($imagens, ';');
    }

    public function __toString()
    {
        return $this->getTipo() . " - " . $this->descricao . (!empty($this->numero) ? " nº " . $this->numero : "");
    }

    /**
     * @return bool
     * @throws BusinessException
     */
    function podeMandarParaAssinatura(): bool
    {
        try {
            $path = $this->getArquivo(false, false, true);
            if (is_null($path) || !is_file($path)) {
                return false;
            }
            if (Functions::isPDFA($path)) {
                return true;
            }
            if (Functions::isPDAAssinado($path)) {
                return true;
            }
        } catch (TechnicalException $e) {
            Functions::escreverLogErro($e);
            return false;
        }
        $converter = $this->getConverter();
        if ($this->getId() && $converter) {
            if ($converter->getDataTermino()) {
                return true;
            }
            if (!$converter->getDataTermino()) {
                try {
                    if (is_file($this->getArquivoOriginal()) && Functions::isPDAAssinado($this->getArquivoOriginal())) {
                        return true;
                    }
                    if (is_file($this->getArquivo(false, false, true)) && Functions::isPDAAssinado($this->getArquivoOriginal())) {
                        return true;
                    }
                } catch (TechnicalException $e) {
                    Functions::escreverLogErro($e);
                }
            }
        } else if ($this->getId() && !$converter) {
            $converter = new Converter();
            $converter->setAnexo($this);
            $converter->inserir();
        }
        return false;
    }

    function ehDocumentoRequerido()
    {
        $processo = $this->getProcesso();
        $tramiteAtual = $processo->getTramiteAtual();
        $documentos = $tramiteAtual->getRequirimentosObrigaroriosNaoCumpridos();
        foreach ($documentos as $documento) {
            if ($documento->getAnexo()->getId() == $this->getId()) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param Usuario|null $usuario Usuario
     * @return int
     */
//    TODO: Estado cancelado? Tratar.
// TODO: Validar também no backend.
    function podeSerAlterado(?Usuario $usuario = null): int
    {
        if (!AppController::getConfig("bloquear_anexo")) {
            return PermissaoStatus::OK;
        }
        if (is_null($usuario)) $usuario = UsuarioController::getUsuarioLogadoDoctrine();
        if ($usuario->isAdm()) return PermissaoStatus::OK;
        $processo = $this->getProcesso();
        $tramiteAtual = $processo->getTramiteAtual();
        $tramiteAnterior = $processo->getTramiteAnterior($tramiteAtual);
        if (!is_null($tramiteAnterior)) {
            $dataInicial = $tramiteAnterior->getDataRecebimento(false, true);
        }
        if (isset($dataInicial)) { // Possui tramitação anterior.
            if (self::anexoCadastradoEntreTramite($tramiteAnterior, $tramiteAtual)) { // Anexo criado na tramitação corrente.
                return self::ehAutor($usuario) || $usuario->isAdm() ? PermissaoStatus::OK : PermissaoStatus::NEGADO;
            } else if (!self::ehDocumentoRequerido() &&
                $this->ehAutor($usuario) &&
                self::pertenceAoSetor($tramiteAtual, $usuario)) { // Cadastrado em trâmite anterior, documento não obrigatório, usuário autor e pertencente ao setor corrente.
                return PermissaoStatus::NEGADO;
            } else {
                return PermissaoStatus::NEGADO;
            }
        } else {
            if (isset($tramiteAtual) && $tramiteAtual->getIsRecebido()) {
                if (self::pertenceAoSetor($tramiteAtual, $usuario)) {
                    $usuarioDestino = $tramiteAtual->getUsuarioDestino();
                    if (is_null($usuarioDestino)) {
                        return self::ehAutor($usuario) || $usuario->isAdm() ? PermissaoStatus::REQUER_MOTIVO : PermissaoStatus::NEGADO;
                    } else { // Trâmite com usuário de destino.
                        if ((self::ehDestinatario($usuarioDestino, $usuario) && self::ehAutor($usuario)) || $usuario->isAdm()) {
                            return PermissaoStatus::REQUER_MOTIVO;
                        } else {
                            return PermissaoStatus::NEGADO;
                        }
                    }
                } else { // Não pertence ao setor.
                    if ($usuario->isAdm()) {
                        return PermissaoStatus::REQUER_MOTIVO;
                    } else if (self::ehDocumentoRequerido()) {
                        return PermissaoStatus::OK;
                    } else {
                        return PermissaoStatus::NEGADO;
                    }
                }
            } else { // Trâmite não recebido
                return self::ehAutor($usuario) || $usuario->isAdm() ? PermissaoStatus::OK : PermissaoStatus::NEGADO;
            }
        }
    }

    /**
     * @param $usuario Usuario
     * @return boolean
     */
    private function ehAutor(Usuario $usuario): bool
    {
        if ($this->getId() != null)
            return $this->getUsuario()->getId() === $usuario->getId();
        return true;
    }

    /**
     * @param $tramite Tramite
     * @param $usuario Usuario
     * @return boolean
     */
    private static function pertenceAoSetor(Tramite $tramite, Usuario $usuario): bool
    {
        $setor_atual = $tramite->getSetorAtual();
        try {
            foreach ($usuario->getSetores() as $setor) {
                $setores_pais = $setor->listarSetoresPai();
                if (!is_null($setores_pais)) {
                    $setores_pais_ids = array_map(function ($item) {
                        return $item->getId();
                    }, $setores_pais);
                    if (in_array($setor_atual->getId(), $setores_pais_ids)) {
                        return true;
                    }
                }
            }
        } catch (Exception $e) {
            Functions::escreverLogErro($e);
        }
        return in_array($setor_atual->getId(), $usuario->getSetoresIds());
    }

    /**
     * @param $destinatario Usuario
     * @param $solicitante Usuario
     * @return boolean
     */
    private function ehDestinatario(Usuario $destinatario, Usuario $solicitante): bool
    {
        return $destinatario->getId() === $solicitante->getId();
    }

    /**
     * @param $tramiteAnterior Tramite
     * @param $tramiteAtual Tramite
     * @return boolean
     */
    private function anexoCadastradoEntreTramite(Tramite $tramiteAnterior, Tramite $tramiteAtual): bool
    {
        $dataCadastro = $this->getDataCadastro();
        $dataRecebimento = $tramiteAtual->getDataRecebimento(false, true);
        return ($dataCadastro->getTimestamp() > $tramiteAnterior->getDataRecebimento(false, true)->getTimestamp())
            && (!$tramiteAtual->getIsRecebido() || $dataCadastro->getTimestamp() > $dataRecebimento->getTimestamp());
    }

    function isConvertido(): bool
    {
        if ($this->getConverter() && !$this->getConverter()->getDataTermino()) {
            return false;
        }
        return true;
    }

    /**
     * @PrePersist
     */
    function gerarNumero()
    {
        if ($this->getIsAutoNumeric() && empty($this->getNumero()) && !empty($this->getProcesso()->getNumero())) {
            $novoNumero = $this->getProcesso()->getNumeroAnexo() + 1;
            $this->getProcesso()->setNumeroAnexo($novoNumero);
            //O número gerado para ser atribuído ao anexo agora considera o exercício do processo, ao invés do ano corrente
            $this->numero = $this->getProcesso()->getNumero() . substr($this->getProcesso()->getExercicio(),2) . $novoNumero;
        }
    }

    /**
     * @PostPersist
     * @throws Exception
     */
    public function enviarParaAssinatura(LifecycleEventArgs $event)
    {
        if (!empty($this->assinatura) && count($this->assinatura) > 0) {
            /**
             * @var Assinatura $assinatura
             */
            $assinatura = $this->assinatura->get(0);
            if (!is_null($assinatura->getLxsign_id())) {
                return;
            }
            $assinatura->setAnexo($event->getObject());
            $_POST["anexo_id"] = $assinatura->getAnexo()->getId();
            $assinaturaController = new AssinaturaController();
            $numero = $assinatura->getNumero() ?? $assinatura->getAnexo()->getNumero();
            $assinatura->setNumero($numero);
            if ($assinatura->getExercicio() == null) {
                $assinatura->setExercicio($this->getExercicio());
            }
            $preenvio = 0;
            if (!$event->getObject()->podeMandarParaAssinatura()) {
                $preenvio = true;
                $assinaturaController = new AssinaturaController();
                $response = $assinaturaController->preenviarParaAssinatura($this);
            } else {
                $response = $assinaturaController->enviarParaAssinatura($this);
            }
            if (isset($response) && !empty($response)) {
                $lxsign_id = isset($response->document) ? $response->document->id : null;
                $assinatura->setPreenvio($preenvio);
                $assinatura->setUsuario(UsuarioController::getUsuarioLogadoDoctrine());
                $assinatura->setLxsign_id($lxsign_id);
                $assinatura->inserir();
            }
        }
        /**
         * @var Anexo $anexo
         */
        $anexo = $event->getObject();
        $path = $anexo->getArquivo(false, false, true);
        try {
            if (is_file($path) && !Functions::isPDFA($path)) {
                $converter = new Converter();
                $converter->setAnexo($anexo);
                $converter->inserir();
            }
        } catch (TechnicalException $e) {
            Functions::escreverLogErro($e);
        }
    }

    public function remover(?int $id = null)
    {
        try {
            $this->removeDaAssinatura($id);
            (new SolicitacaoController())->removerSolicitacaoAnexo($id); // Remover solicitações de alterações de anexo pendentes.
        } catch (Exception $e) {
            Functions::escreverLogErro($e);
        }
        $result = parent::remover($id);
        $motivo = $_POST['motivo'] ?? null;
//  TODO: Verificar log de aprovação de exlusão de anexo pelo ADM. (Registrar na obs do log a identificação do adm que aprovou a solicitação e no cod do usuário utilizar a identificação do solicitante.)
        HistoricoAnexo::registrarLogAnexoRemovido($this, $motivo, "Anexo removido.", UsuarioController::getUsuarioLogadoDoctrine());

        return $result;
    }

    public function merge(AnexoAlteracao $other)
    {
        $this->setIsAutoNumeric($other->getIsAutoNumeric());
        $this->setTipo($other->getTipo());
        $this->setClassificacao($other->getClassificacao());
        $this->setDescricao($other->getDescricao());
        $this->setData($other->getData());
        $this->setNumero($other->getNumero());
        $this->setExercicio($other->getExercicio());
        $this->setValor($other->getValor());
        $this->setQtdePaginas($other->getQtdePaginas());
        $this->setProcesso($this->getProcesso());
        $this->setUsuario($other->getUsuario());
        $this->setIsDigitalizado($other->getIsDigitalizado());
        $this->setIsCirculacaoInterna($other->getIsCirculacaoInterna());
        $this->setDataValidade($other->getDataValidade());
        if (!is_null($other->getArquivo())) {
            $this->setArquivo($other->getArquivo());
        }
    }

    public function isRequired(): bool
    {
        /**
         * @var AnexoDao $dao
         */
        $dao = $this->getDAO();
        return $dao->isRequiredAttach($this->id, $this->processo->getTramiteAtual()->getId());
    }

    /**
     * @return mixed
     */
    public function getPaginacao()
    {
        return $this->paginacao;
    }

    /**
     * @param mixed $paginacao
     */
    public function setPaginacao($paginacao): void
    {
        $this->paginacao = $paginacao;
    }

    public function getNumeroUltimaPagina(): int
    {
        if (empty($this->paginacao)) {
            return 0;
        }
        $faixa = explode("-", $this->paginacao);
        return intval($faixa[1]);
    }

    private function removeDaAssinatura(?int $id = null)
    {
        try {
//            Excluir no sistema de assinaturas.
            $usuario = UsuarioController::getUsuarioLogadoDoctrine();
            $assinaturaId = (new AssinaturaDao())->getLxSignId($id ?? $this->id);
            $data = [
                "user_id" => $usuario->getId(),
                "user_name" => $usuario->getPessoa()->getNome(),
                "ajax" => true
            ];
            if (!is_null($assinaturaId)) {
                $url = AppController::getConfig('lxsign_url') . "documento/excluirLixeia/id/$assinaturaId";
                $statusCode = (new Builder($url))
                    ->verifySSL(false)
                    ->setParameters($data)
                    ->build()
                    ->send()
                    ->getStatusCode();
                if ($statusCode >= 400) {
                    Functions::escreverLogErro("Não foi possível remover o anexo \"$id\" do sistema de assinaturas.");
                }
            }
        } catch (Exception $e) {
            Functions::escreverLogErro($e);
        }
    }

    public function inserir($validarSomenteLeitura = true, bool $considerarPermissoes = true): ?int
    {
        $this->id = parent::inserir($validarSomenteLeitura, $considerarPermissoes);
        HistoricoAnexo::registrar(TipoLog::ACTION_INSERT, null, "Anexo registrado.", null, $this, UsuarioController::getUsuarioLogadoDoctrine());
        return $this->id;
    }

    public function atualizar(bool $validarSomenteLeitura = true, $considerarPermissoes = true, $motivo = null)
    {
        $antigo = null;
        try {
            $antigo = $this->buscar($this->id);
        } catch (Exception $e) {
            Functions::escreverLogErro($e);
        }
        parent::atualizar($validarSomenteLeitura, $considerarPermissoes);
        HistoricoAnexo::registrar(TipoLog::ACTION_UPDATE, $motivo, null, $antigo, $this, UsuarioController::getUsuarioLogadoDoctrine());
    }

    public function ehPdf(): bool {
        $ext = pathinfo($this->getArquivo(false, false, true), PATHINFO_EXTENSION);
        return strtolower($ext) === "pdf";
    }

    public function jsonSerialize(): array
    {
        $qtd_assinaturas = 0;
        $qtd_componentes = 0;
        if ($this->assinatura instanceof Collection) {
            $qtd_assinaturas = $this->assinatura->count();
        } else if (is_array($this->assinatura)) {
            $qtd_assinaturas = count($this->assinatura);
        }
        if ($this->componente instanceof Collection) {
            $qtd_componentes = $this->componente->count();
        } else if (is_array($this->componente)) {
            $qtd_componentes = count($this->componente);
        }
        return [
            "id" => $this->id,
            "is_digitalizado" => $this->isDigitalizado,
            "is_ocr_iniciado" => $this->isOCRIniciado,
            "is_ocr_finalizado" => $this->isOCRFinalizado,
            "is_auto_numeric" => $this->isAutoNumeric,
            "numero" => $this->numero,
            "exercicio" => $this->exercicio,
            "tipo_anexo_id" => is_null($this->tipo) ? "" : $this->tipo->getId(),
            "classificacao_id" => is_null($this->classificacao) ? "" : $this->classificacao->getId(),
            "dataValidade" => Functions::formatarData($this->dataValidade),
            "descricao" => $this->descricao,
            "arquivo" => $this->arquivo,
            "processo_id" => is_null($this->processo) ? "" : $this->processo->getId(),
            "usuario_id" => is_null($this->usuario) ? "" : $this->usuario->getId(),
            "data" => Functions::formatarData($this->data),
            "data_cadastro" => Functions::formatarData($this->dataCadastro),
            "valor" => $this->valor,
            "qtde_paginas" => $this->qtdePaginas,
            "texto_ocr" => $this->textoOCR,
            "textoOCR" => $this->textoOCR,
            "imagens" => count($this->imagens) > 0 ? array_map(function ($item) {
                return $item->getId();
            }, $this->imagens) : "",
            "assinaturas" => $qtd_assinaturas > 0 ? array_map(function ($item){
                return $item->getId();
            }, $this->assinatura) : "",
            "componentes" => $qtd_componentes > 0 ? array_map(function ($item) {
                return $item->getId();
            }, $this->componente) : "",
            "codigo_importacao" => $this->codigoImportacao,
            "novo_vencimento_processo" => $this->novoVencimentoProcesso,
            "motivo_pendencia" => $this->motivoPendencia,
            "is_circulacao_interna" => $this->isCirculacaoInterna,
            "paginacao" => $this->paginacao,
        ];
    }

    public function imprimir(): string
    {
        return json_encode($this, JSON_PRETTY_PRINT);
    }
}