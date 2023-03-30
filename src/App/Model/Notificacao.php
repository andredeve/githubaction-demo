<?php /** @noinspection PhpUnused */

namespace App\Model;

use App\Controller\UsuarioController;
use App\Enum\TipoUsuario;
use Core\Model\AppModel;
use Core\Util\Functions;
use DateTime;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

/**
 * @Entity
 * @HasLifecycleCallbacks
 * @Table(name="notificacao")
 */
class Notificacao extends AppModel {

    /**
     * @Id
     * @Column(type="integer",name="id")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Column(type="integer",name="numero",nullable=true)
     */
    private $numero;

    /**
     * @Column(type="string",name="assunto",nullable=false)
     */
    private $assunto;

    /**
     * @Column(type="text",name="texto",nullable=false)
     */
    private $texto;

    /**
     * @ManyToOne(targetEntity="Usuario")
     * @JoinColumn(name="usuario_abertura_id", referencedColumnName="id",nullable=false)
     */
    private $usuarioAbertura;

    /**
     * @ManyToOne(targetEntity="Usuario")
     * @JoinColumn(name="usuario_destino_id", referencedColumnName="id",nullable=false)
     */
    private $usuarioDestino;

    /**
     * @Column(type="date",name="prazo_resposta")
     */
    private $prazoResposta;

    /**
     * @Column(type="integer",name="prazo_dias")
     */
    private $prazoDias;

    /**
     * @Column(type="boolean",name="is_prazo_dia_util")
     */
    private $isPrazoDiaUtil;

    /**
     * @Column(type="datetime",name="data_criacao")
     */
    private $dataCriacao;

    /**
     * @Column(type="boolean",name="is_respondida",nullable=true)
     */
    private $isRespondida;

    /**
     * @Column(type="text",name="resposta",nullable=true)
     */
    private $resposta;

    /**
     * @Column(type="datetime",name="data_resposta",nullable=true)
     */
    private $dataResposta;

    /**
     * @Column(type="boolean",name="is_visualizada",nullable=true)
     */
    private $isVisualizada;

    /**
     * @Column(type="datetime",name="data_visualizacao",nullable=true)
     */
    private $dataVisualizacao;

    /**
     * @Column(type="boolean",name="is_arquivada",nullable=true)
     */
    private $isArquivada;

    /**
     * @Column(type="datetime",name="data_arquivamento",nullable=true)
     */
    private $dataArquivamento;

    /**
     * @ManyToOne(targetEntity="Tramite")
     * @JoinColumn(name="tramite_id", referencedColumnName="id")
     */
    private $tramite;

    function getId(): ?int {
        return $this->id;
    }

    function getNumero() {
        return $this->numero;
    }

    function getAssunto() {
        return $this->assunto;
    }

    function getTexto() {
        return $this->texto;
    }

    function getResposta() {
        return $this->resposta;
    }

    function getDataResposta() {
        return $this->dataResposta;
    }

    function getUsuarioAbertura() {
        return $this->usuarioAbertura;
    }

    function getUsuarioDestino() {
        return $this->usuarioDestino;
    }

    function getPrazoResposta() {
        return $this->prazoResposta;
    }

    function getPrazoDias() {
        return $this->prazoDias;
    }

    function getIsPrazoDiaUtil() {
        return $this->isPrazoDiaUtil;
    }

    function getDataCriacao() {
        return $this->dataCriacao;
    }

    function getIsVisualizada() {
        return $this->isVisualizada;
    }

    function getDataVisualizacao() {
        return $this->dataVisualizacao;
    }

    function getIsArquivada() {
        return $this->isArquivada;
    }

    function getDataArquivamento() {
        return $this->dataArquivamento;
    }

    function getTramite() {
        return $this->tramite;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    function setNumero($numero) {
        $this->numero = $numero;
    }

    function setAssunto($assunto) {
        $this->assunto = $assunto;
    }

    function setTexto($texto) {
        $this->texto = $texto;
    }

    function setResposta($resposta) {
        $this->resposta = $resposta;
    }

    function setDataResposta($dataResposta) {
        $this->dataResposta = $dataResposta;
    }

    function setUsuarioAbertura($usuarioAbertura) {
        $this->usuarioAbertura = $usuarioAbertura;
    }

    function setUsuarioDestino($usuarioDestino) {
        $this->usuarioDestino = $usuarioDestino;
    }

    function setPrazoResposta($prazoResposta) {
        $this->prazoResposta = $prazoResposta;
    }

    function setPrazoDias($prazoDias) {
        $this->prazoDias = $prazoDias;
    }

    function setIsPrazoDiaUtil($isPrazoDiaUtil) {
        $this->isPrazoDiaUtil = $isPrazoDiaUtil;
    }

    function setDataCriacao($dataCriacao) {
        $this->dataCriacao = $dataCriacao;
    }

    function setIsVisualizada($isVisualizada) {
        $this->isVisualizada = $isVisualizada;
    }

    function setDataVisualizacao($dataVisualizacao) {
        $this->dataVisualizacao = $dataVisualizacao;
    }

    function setIsArquivada($isArquivada) {
        $this->isArquivada = $isArquivada;
    }

    function setDataArquivamento($dataArquivamento) {
        $this->dataArquivamento = $dataArquivamento;
    }

    function setTramite($tramite) {
        $this->tramite = $tramite;
    }

    function getIsRespondida() {
        return $this->isRespondida;
    }

    function setIsRespondida($isRespondida) {
        $this->isRespondida = $isRespondida;
    }

    function getProcesso() {
        return $this->tramite ? $this->tramite->getProcesso(): null;
    }

    /**
     * @throws Exception
     * @throws \Doctrine\ORM\ORMException
     * @throws ORMException
     */
    function listarArquivadas() {
        return $this->getDAO()->listarArquivadas();
    }

    /**
     * @throws Exception
     * @throws \Doctrine\ORM\ORMException
     * @throws ORMException
     */
    function listarEnviadas(): array
    {
        return $this->listarPorCampos(array("usuarioAbertura" => UsuarioController::getUsuarioLogadoDoctrine(), "isArquivada" => false), array("dataCriacao" => "DESC"));
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws ORMException
     * @throws Exception
     */
    function listarRecebidas(): array
    {
        return $this->listarPorCampos(array("usuarioDestino" => UsuarioController::getUsuarioLogadoDoctrine(), "isArquivada" => false), array("dataCriacao" => "DESC"));
    }

    function getStatus(): string
    {
        return $this->isRespondida ? "Respondida" : ($this->isArquivada ? "Arquivada" : ($this->isVisualizada ? "Lida" : "Não lida"));
    }

    function getPermissaoResponder(): bool
    {
        return $this->isRespondida == false && $this->usuarioDestino->getId() == UsuarioController::getUsuarioLogado()->getId();
    }

    function getPermissaoArquivar(): bool
    {
        $usuario_logado = UsuarioController::getUsuarioLogado();
        if (($this->isArquivada == false && ($this->usuarioAbertura->getId() == $usuario_logado->getId() || $usuario_logado->getTipo() != TipoUsuario::USUARIO)) && $usuario_logado->getTipo() != TipoUsuario::VISITANTE) {
            return true;
        }
        return false;
    }

    /**
     * Função que buscar o maior numero no ano atual
     */
    function getMaiorNumero(): ?int
    {
        try {
            $result = $this->getDAO()->getMaiorNumero();
            return $result != null ? (int) $result[0]['numero'] : null;
        } catch (Exception|NoResultException|ORMException|\Doctrine\ORM\ORMException|NonUniqueResultException $e) {
            Functions::escreverLogErro($e);
        }
        return null;
    }

    /**
     * @PrePersist
     */
    function gerarNumero() {
        if (empty($this->numero)) {
            $maior_numero = $this->getMaiorNumero();
            if ($maior_numero != null) {
                $this->numero = $maior_numero + 1;
            } else {
                $this->numero = 1;
            }
        }
    }

    /**
     * @PrePersist
     * @throws \Exception
     */
    function calcularPrazoResposta() {
        $hoje = new DateTime();
        if ($this->isPrazoDiaUtil) {
            $prazo_resposta = Functions::getDataUtil($hoje, $this->prazoDias);
        } else {
            $hoje->modify("+" . $this->prazoDias . " day");
            $prazo_resposta = $hoje->format("Y-m-d");
        }
        $this->prazoResposta = new DateTime($prazo_resposta);
    }
}