<?php /** @noinspection PhpUnused */

namespace App\Model;

use App\Util\FormularioEletronico;
use Core\Exception\BusinessException;
use Core\Model\AppModel;
use Core\Util\Functions;
use DateTime;
use DateTimeZone;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\PersistentCollection;
use Doctrine\ORM\TransactionRequiredException;
use Exception;

/**
 * @Entity
 * @HasLifecycleCallbacks
 * @Table(name="tramite",
 *     uniqueConstraints={@UniqueConstraint(name="tramite_unique",columns={"processo_id","data_envio"})},
 * indexes={
 *     @Index(name="fase_index", columns={"numero_fase"}),
 *     @Index(name="usuario_envio_index", columns={"usuario_envio_id"}),
 *     @Index(name="recebido_index", columns={"is_recebido"}),
 *     @Index(name="despachado_index", columns={"is_despachado"}),
 *     @Index(name="cancelado_index", columns={"is_cancelado"})
 * })
 */
class Tramite extends AppModel
{

    /**
     * @Id
     * @Column(type="integer",name="id")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ManyToOne(targetEntity="Processo")
     * @JoinColumn(name="processo_id", referencedColumnName="id",nullable=false,onDelete="CASCADE")
     */
    private $processo;

    /**
     * @ManyToOne(targetEntity="Assunto")
     * @JoinColumn(name="assunto_id", referencedColumnName="id",nullable=true)
     */
    private $assunto;
    
    /**
     * @OneToMany(targetEntity="DocumentoRequerido", mappedBy="tramiteCadastro")
     */
    private $documentosRequerimentosCadastrados;
    
    /**
     * @OneToMany(targetEntity="DocumentoRequerido", mappedBy="tramiteValidar")
     */
    private $documentosRequerimentosValidar;
    
    /**
     * @Column(type="integer",name="numero_fase")
     */
    private $numeroFase;

    /**
     * @ManyToOne(targetEntity="StatusProcesso")
     * @JoinColumn(name="status_id", referencedColumnName="id",nullable=false)
     * @var StatusProcesso $status
     */
    private $status;

    /**
     * @ManyToOne(targetEntity="Setor")
     * @JoinColumn(name="setor_anterior_id", referencedColumnName="id",nullable=true)
     */
    private $setorAnterior;


    /**
     * @ManyToOne(targetEntity="Setor")
     * @JoinColumn(name="setor_atual_id", referencedColumnName="id",nullable=true)
     */
    private $setorAtual;

    /**
     * @ManyToOne(targetEntity="Usuario")
     * @JoinColumn(name="usuario_destino_id", referencedColumnName="id",nullable=true)
     */
    private $usuarioDestino;

    /**
     * Se não for nulo, somente este usuário poderá visualizar o protocolo no tramite corrente
     * @ManyToOne(targetEntity="Usuario")
     * @JoinColumn(name="usuario_envio_id", referencedColumnName="id",nullable=true)
     */
    private $usuarioEnvio;

    /**
     * @ManyToOne(targetEntity="Usuario")
     * @JoinColumn(name="usuario_recebimento_id", referencedColumnName="id",nullable=true)
     */
    private $usuarioRecebimento;

    /**
     * @Column(type="datetime",name="data_envio",nullable=true)
     */
    private $dataEnvio;

    /**
     * @Column(type="date",name="data_vencimento",nullable=true)
     */
    private $dataVencimento;

    /**
     * @Column(type="datetime",name="data_recebimento",nullable=true)
     */
    private $dataRecebimento;


    /**
     * @Column(type="text",name="parecer",nullable=true)
     */
    private $parecer;

    /**
     * @Column(type="boolean",name="fora_fluxograma",nullable=true)
     */
    private $foraFluxograma;

    /**
     * @Column(type="boolean",name="cancelou_decisao",nullable=true)
     */
    private $cancelouDecisao;

    /**
     * @Column(type="boolean",name="is_despachado",nullable=false)
     */
    private $isDespachado;

    /**
     * @Column(type="datetime",name="data_despacho",nullable=true)
     */
    private $dataDespacho;
    /**
     * @Column(type="string",name="relator",nullable=true)
     */
    private $relator;
    /**
     * @Column(type="string",name="loginEnvio",nullable=true)
     */
    private $loginEnvio;
    /**
     * @Column(type="boolean",name="is_recebido",nullable=false)
     */
    private $isRecebido;

    /**
     * @Column(type="boolean",name="is_cancelado",nullable=true)
     */
    private $isCancelado;

    /**
     * @Column(type="text",name="justificativa_cancelamento",nullable=true)
     */
    private $justificativaCancelamento;

    /**
     * @ManyToOne(targetEntity="Usuario")
     * @JoinColumn(name="usuario_cancelamento_id", referencedColumnName="id",nullable=true)
     */
    private $usuarioCancelamento;

    /**
     * @ManyToOne(targetEntity="Remessa",inversedBy="tramites", cascade={"persist"})
     * @JoinColumn(name="remessa_id", referencedColumnName="id")
     */
    private $remessa;
    /**
     * @OneToMany(targetEntity="RespostaCampo", mappedBy="tramite",cascade={"persist"})
     * @OrderBy({"data" = "ASC"})
     */
    private $respostasCampo;

    /**
     * @OneToMany(targetEntity="RespostaPergunta", mappedBy="tramite",cascade={"persist"})
     * @OrderBy({"data" = "ASC"})
     */
    private $respostasPergunta;

    /**
    * @OneToMany(targetEntity="Componente", mappedBy="tramite", cascade={"persist","remove"})
    */ 
    private $componente;

    function __construct()
    {
        $this->respostasCampo = new ArrayCollection();
        $this->respostasPergunta = new ArrayCollection();
        $this->componente = new ArrayCollection();
        $this->documentosRequerimentosCadastrados = new ArrayCollection();
        $this->documentosRequerimentosValidar = new ArrayCollection();
        $this->cancelouDecisao = false;
        $this->foraFluxograma = false;
        $this->isCancelado = false;
    }

    /**
     * @return Collection<DocumentoRequerido>
     */
    function getDocumentosRequerimentosCadastrados(): ?Collection
    {
        return $this->documentosRequerimentosCadastrados;
    }

    function setDocumentosRequerimentosCadastrados($documentosRequerimentosCadastrados) {
        $this->documentosRequerimentosCadastrados = $documentosRequerimentosCadastrados;
    }

    /**
     * @return Collection<DocumentoRequerido>
     */
    function getDocumentosRequerimentosValidar(): ?Collection
    {
        return $this->documentosRequerimentosValidar;
    }

    function setDocumentosRequerimentosValidar($documentosRequerimentosValidar) {
        $this->documentosRequerimentosValidar = $documentosRequerimentosValidar;
    }

    function getRequirimentosObrigaroriosNaoCumpridos(): ?array
    {
        $retorno = array();
           
        foreach($this->getDocumentosRequerimentosValidar() as $documento){
            if( !$documento->isCumprido()){
                $retorno[] = $documento;
            }
        }
        return $retorno;
    }
    
    function getRequirimentosSemObrigaroriedadeNaoCumpridos(): ?array
    {
        $retorno = array();
        if(!empty($this->getDocumentosRequerimentosValidar())){
            foreach($this->getDocumentosRequerimentosValidar() as $documento){
                if(!$documento->getIsObrigatorio() && !$documento->getIsAssinaturaObrigatoria() && !$documento->getAnexo()->getArquivo()){
                    $retorno[] = $documento;
                }
            }
        }
        return $retorno;
    }

    public function getCancelouDecisao(): ?bool
    {
        return $this->cancelouDecisao ?? false;
    }

    public function setCancelouDecisao($cancelouDecisao)
    {
        $this->cancelouDecisao = $cancelouDecisao;
    }

    public function getForaFluxograma(): ?bool
    {
        return $this->foraFluxograma ?? false;
    }

    public function setForaFluxograma($foraFluxograma)
    {
        $this->foraFluxograma = $foraFluxograma;
    }

    public function getJustificativaCancelamento()
    {
        return $this->justificativaCancelamento;
    }

    public function setJustificativaCancelamento($justificativaCancelamento)
    {
        $this->justificativaCancelamento = $justificativaCancelamento;
    }

    public function getUsuarioCancelamento()
    {
        return $this->usuarioCancelamento;
    }

    public function setUsuarioCancelamento($usuarioCancelamento)
    {
        $this->usuarioCancelamento = $usuarioCancelamento;
    }

    public function getFaseAnterior(): ?int
    {
        $faseAtual = $this->numeroFase;
        $processo = $this->processo;
        $tramiteAnterior = $processo->getTramiteAnterior($this);
        if (empty($tramiteAnterior)) {
            return 0;
        }
        $faseAnterior = $tramiteAnterior->getNumeroFase();
        if ($faseAnterior == $faseAtual) {
            return $tramiteAnterior->getFaseAnterior();
        }
        return $faseAnterior;
    }

    function getSetorAnteriorFase(): ?Setor
    {
        $faseAnterior = $this->getFaseAnterior();
        if ($faseAnterior == 0) {
            return $this->getSetorAnterior();
        }
        $tramites = $this->processo->getTramites($faseAnterior);
        return $tramites instanceof Collection || $tramites instanceof PersistentCollection ? $tramites->last()->getSetorAtual() : $tramites->getSetorAtual();
    }

    /**
     * @return Setor|null
     */
    public function getSetorAnterior(): ?Setor
    {
        return $this->setorAnterior;
    }

    /**
     * @param mixed $setorAnterior
     */
    public function setSetorAnterior($setorAnterior)
    {
        $this->setorAnterior = $setorAnterior;
    }

    function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getRemessa()
    {
        return $this->remessa;
    }

    /**
     * @param mixed $remessa
     */
    public function setRemessa($remessa)
    {
        $this->remessa = $remessa;
    }

    /**
     * @return mixed
     */
    public function getRelator()
    {
        return $this->relator;
    }

    /**
     * @param mixed $relator
     */
    public function setRelator($relator)
    {
        $this->relator = $relator;
    }

    /**
     * @return mixed
     */
    public function getLoginEnvio()
    {
        return $this->loginEnvio;
    }

    /**
     * @param mixed $loginEnvio
     */
    public function setLoginEnvio($loginEnvio)
    {
        $this->loginEnvio = $loginEnvio;
    }

    /**
     * @return Processo
     */
    public function getProcesso(): Processo
    {
        return $this->processo;
    }

    /**
     * @return mixed
     */
    public function getAssunto()
    {
        return $this->assunto;
    }

    /**
     * @param mixed $assunto
     */
    public function setAssunto($assunto)
    {
        $this->assunto = $assunto;
    }

    /**
     * @return StatusProcesso|string
     */
    function getStatus()
    {
        if ($this->isCancelado) {
            return 'Cancelado';
        }
        return $this->status;
    }

    /**
     * @return Usuario|null
     */
    function getUsuarioEnvio()
    {
        return $this->usuarioEnvio;
    }

    function getDataEnvio($formatar = false, $hora = false)
    {
        if ($formatar) {
            return $this->dataEnvio != null ? ($hora ? $this->dataEnvio->format('d/m/Y - H:i:s') : $this->dataEnvio->format('d/m/Y')) : null;
        }
        return $this->dataEnvio;
    }

    function getHoraEnvio()
    {
        return $this->dataEnvio != null ? $this->dataEnvio->format('H:i:s') : null;
    }

    /**
     * @return Usuario|null
     */
    function getUsuarioRecebimento(): ?Usuario
    {
        return $this->usuarioRecebimento;
    }

    /**
     * @param $formatar boolean
     * @param $hora boolean
     * @return DateTime|string
     */
    function getDataRecebimento(bool $formatar = false, bool $hora = false)
    {
        if ($formatar) {
            return $this->dataRecebimento != null ? ($hora ? $this->dataRecebimento->format('d/m/Y - H:i:s') : $this->dataRecebimento->format('d/m/Y')) : null;
        }
        return $this->dataRecebimento;
    }

    function getComponente(): ?Collection
    {
        return $this->componente;
    }

    function setComponente($componente) {
        $this->componente = $componente;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    function setProcesso($processo)
    {
        if ($processo != null) {
            $this->assunto = $processo->getAssunto();
        }
        $this->processo = $processo;
    }

    function setStatus($status)
    {
        $this->status = $status;
    }

    function setUsuarioEnvio($usuarioEnvio)
    {
        $this->usuarioEnvio = $usuarioEnvio;
    }

    function setDataEnvio($dataEnvio)
    {
        $this->dataEnvio = $dataEnvio;
    }

    function setUsuarioRecebimento($usuarioRecebimento)
    {
        $this->usuarioRecebimento = $usuarioRecebimento;
    }

    function setDataRecebimento($dataRecebimento)
    {
        $this->dataRecebimento = $dataRecebimento;
    }

    function getParecer()
    {
        if ($this->isCancelado) {
            return $this->justificativaCancelamento;
        }
        return $this->parecer;
    }

    function setParecer($parecer)
    {
        $this->parecer = $parecer;
    }

    /**
     * @return Usuario|null
     */
    function getUsuarioDestino(): ?Usuario
    {
        return $this->usuarioDestino;
    }

    function setUsuarioDestino($usuarioDestino)
    {
        $this->usuarioDestino = $usuarioDestino;
    }

    /**
     * @return Setor
     */
    function getSetorAtual(): ?Setor
    {
        return $this->setorAtual;
    }

    function setSetorAtual($setorAtual)
    {
        $this->setorAtual = $setorAtual;
    }

    function getResponsavel()
    {
        if ($this->isCancelado) {
            return $this->usuarioCancelamento;
        }
        if ($this->usuarioRecebimento == null) {
            return $this->usuarioEnvio;
        }
        return $this->usuarioRecebimento;
    }

    /**
     * @return int|mixed
     */
    function getNumeroFase(): ?int
    {
        return $this->numeroFase;
    }

    function setNumeroFase($numeroFase)
    {
        $this->numeroFase = $numeroFase;
    }

    function getIsCancelado(): bool
    {
        return $this->isCancelado ?? false;
    }

    function setIsCancelado($isCancelado)
    {
        $this->isCancelado = $isCancelado;
    }

    function getIsRecebido()
    {
        return $this->isRecebido;
    }

    function setIsRecebido($isRecebido)
    {
        $this->isRecebido = $isRecebido;
    }

    function getDataVencimento()
    {
        return $this->dataVencimento;
    }

    function getRespostasCampo(): ?Collection
    {
        return $this->respostasCampo;
    }

    function getRespostasPergunta(): ?Collection
    {
        return $this->respostasPergunta;
    }

    function setDataVencimento($dataVencimento)
    {
        $this->dataVencimento = $dataVencimento;
    }

    function setRespostasCampo($respostasCampo)
    {
        $this->respostasCampo = $respostasCampo;
    }

    function setRespostasPergunta($respostasPergunta)
    {
        $this->respostasPergunta = $respostasPergunta;
    }

    function getIsDespachado()
    {
        return $this->isDespachado;
    }

    function getDataDespacho()
    {
        return $this->dataDespacho;
    }

    function setIsDespachado($isDespachado)
    {
        $this->isDespachado = $isDespachado;
    }

    function setDataDespacho($dataDespacho)
    {
        $this->dataDespacho = $dataDespacho;
    }

    function getNomeFormularioEletronico(): string
    {
        return "formulario_eletronico_tramite_$this->id.pdf";
    }

    function gerarFormularioEletronico(): bool
    {
        if ($this->respostasPergunta->count() > 0 || $this->respostasCampo->count() > 0) {
            if(!class_exists("FPDF")){
                require_once APP_PATH . 'lib/fpdf/fpdf.php';
            }
            return (new FormularioEletronico($this))->gerar('F', $this->processo->getAnexosPath() . $this->getNomeFormularioEletronico());
        }
        return false;
    }

    /**
     * @PrePersist
     *
     * @throws Exception
     */
    function setVencimento()
    {
        if ($this->dataVencimento == null) {
            $assunto = $this->processo->getAssunto();
            if ($assunto != null && $assunto->getFluxograma() != null && $assunto->getFluxograma()->getFases($this->numeroFase)) {
                $this->dataVencimento = new DateTime($assunto->getFluxograma()->getFases($this->numeroFase)->getVencimento($this->dataEnvio->format('Y-m-d'), $this->setorAtual));
            } else {
                $this->dataVencimento = $this->processo->getDataVencimento();
            }
        }
    }

    function getVencimento()
    {
        if ($this->processo->getAssunto()->getFluxograma() != null) {
            $fase = $this->processo->getAssunto()->getFluxograma()->getFases($this->numeroFase);
            if ($fase != null) {
                return Functions::converteData($fase->getVencimento($this->dataEnvio->format('Y-m-d'), $this->setorAtual));
            }
        }
        return $this->processo->getDataVencimento()->format('d/m/Y');
    }

    /**
     * @throws Exception
     */
    function getDiasVencidos($data_vencimento = null): string
    {
        $timezone = new DateTimeZone('America/Campo_Grande');
        return (new DateTime(Date('Y-m-d'), $timezone))->diff($this->dataVencimento)->format('%r%a');
    }

    function getEnvioTramite($numero_fase): ?string
    {
        $maior_time_fase = "";
        foreach ($this->processo->getTramite($numero_fase) as $tramite) {
            $timestamp_tramite = $tramite->getDataEnvio() != null ? $tramite->getDataEnvio()->getTimestamp() : null;
            if ($timestamp_tramite > $maior_time_fase) {
                $maior_time_fase = $timestamp_tramite;
            }
        }
        return $maior_time_fase;
    }

    private function getProximoTramite()
    {
        $tramites = $this->processo->getTramites();
        foreach ($this->processo->getTramites() as $i => $tramite) {
            if ($tramite->getId() == $this->id) {
                return $tramites->get($i + 1);
            }
        }
        return null;
    }

    function temFluxograma(): bool
    {
        return $this->processo->getAssunto()->getFluxograma() != null;
    }

    function getTempoGasto()
    {
        if ($this->dataEnvio == null) {
            return null;
        }
        $timestamp_envio = $this->dataEnvio->getTimestamp();
        // Se o trâmite for o último
        if ($this->id == $this->processo->getTramites()->last()->getId()) {
            if ($this->processo->getIsArquivado()) {
                return $this->processo->getDataArquivamento() != null ? Functions::timerFormat($timestamp_envio, $this->processo->getDataArquivamento()->getTimestamp()) : null;
            }
            return Functions::timerFormat($timestamp_envio, time());
        }
        //Senão calcula a partir do próximo trâmite
        if ($this->getProximoTramite()->getDataEnvio() != null) {
            return Functions::timerFormat($timestamp_envio, $this->getProximoTramite()->getDataEnvio()->getTimestamp());
        }
        return null;
    }

    function adicionaRespostaCampo(RespostaCampo $resposta): Tramite
    {
        if (!$this->respostasCampo->contains($resposta)) {
            $this->respostasCampo->add($resposta);
        }
        return $this;
    }

    function adicionaRespostaPergunta(RespostaPergunta $resposta): Tramite
    {
        if (!$this->respostasPergunta->contains($resposta)) {
            $this->respostasPergunta->add($resposta);
        }
        return $this;
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws ORMException
     * @throws \Doctrine\DBAL\Exception
     */
    function buscarRemessa($dataIni, $dataFim, $setorOrigemId, $responsavelOrigemId, $setorDestinoId, $responsavelDestinoId = null)
    {
        return $this->getDAO()->buscarRemessa($dataIni, $dataFim, $setorOrigemId, $responsavelOrigemId, $setorDestinoId, $responsavelDestinoId);
    }

    public static function getTramitesSetor($tramites, $setor): ?array
    {
        $setor_id = $setor->getId();
        $tramites_setor = array();
        foreach ($tramites as $tramite) {
            if ($tramite->getSetorAtual()->getId() == $setor_id) {
                $tramites_setor[] = $tramite;
            }
        }
        return $tramites_setor;
    }

    /**
     * @throws ORMException
     * @throws \Doctrine\DBAL\Exception
     * @throws \Doctrine\ORM\ORMException
     */
    public function listarTramitesNaoRecebidos($usuario_id, $setor_id){
        return $this->getDAO()->listarTramitesNaoRecebidos($usuario_id, $setor_id);
    }

    /**
     * @throws ORMException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\DBAL\Exception
     * @throws BusinessException
     */
    public function buscarLxSignIdDosAnexos($tramiteId = null): ?array
    {
        if (!is_null($tramiteId)) {
            $result = $this->getDAO()->buscarLxSignIdDosAnexos($tramiteId);
        } else if (is_null($this->id)) {
            $result = $this->getDAO()->buscarLxSignIdDosAnexos($this->id);
        } else {
            throw new BusinessException("Informe o id do trâmite para essa operação");
        }
        return $result;
    }

    /**
     * Caso: Em algum momento, com alguns clientes, ocorre um erro de JS ao submeter o formulário de tramitação,
     * fazendo com que interrompa o fluxo do JS. Ao acontecer isso, os usuários estavam recarregando a página,
     * submetendo novamente o formulário, gerando uma duplicidade. Esse método visa evitar essa duplicidade, validando
     * a entrada de dados do formulário com a tramitação atual do processo.
     *
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     * @throws \Doctrine\ORM\ORMException
     */
    public function ehDuplicado(Array $criterio): bool
    {
        $processo = (new Processo())->buscar($criterio["processo_id"]);
        $tramiteAtual = $processo->getTramiteAtual();
        return $tramiteAtual->getSetorAtual()->getId() == $criterio['setor_destino_id']
            && (
                (is_null($tramiteAtual->getSetorAnterior()) && empty($criterio['setor_origem_id']))
                || ($tramiteAtual->getSetorAnterior()->getId() == $criterio['setor_origem_id'])
            ) && ($tramiteAtual->getStatus() !== 'Cancelado'
                && $tramiteAtual->getStatus()->getId() == $criterio['status_processo_id'])
            && (
                (is_null($tramiteAtual->getUsuarioDestino()) && empty($criterio['usuario_destino_id']))
                    || (!is_null($tramiteAtual->getUsuarioDestino())
                            && $tramiteAtual->getUsuarioDestino()->getId() == $criterio['usuario_destino_id'])
            ) && (
                (!empty($tramiteAtual->getParecer()) && $tramiteAtual->getParecer() == $criterio['descricao_tramite'])
                    || (empty($tramiteAtual->getParecer()) && empty($criterio['descricao_tramite']))
            );
    }
    
    public function getNomeArquivoFormularioEletronico(): string
    {
        $nomeArquivoCarimbado = "formulario_eletronico_tramite_".$this->getId()."_carimbado.pdf";
        if(file_exists($this->processo->getAnexosPath().$nomeArquivoCarimbado)){
            return $nomeArquivoCarimbado;
        }
        return "formulario_eletronico_tramite_".$this->getId().".pdf";
    }

    public function getSetoresId() : ?array
    {
        return $this->getDAO()->getSetoresId($this->getProcesso()->getId());
    }
    
    public function __toString() {
        return "Trâmite: " . $this->id;
    }
}
