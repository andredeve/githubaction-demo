<?php /** @noinspection PhpUnused */

namespace App\Model;

use App\Controller\UsuarioController;
use App\Enum\TipoHistoricoProcesso;
use Core\Controller\AppController;
use Core\Exception\TechnicalException;
use Core\Model\AppModel;
use Core\Util\Functions;
use DateTime;
use Doctrine\ORM\ORMException;
use Exception;

/**
 * @Entity
 * @Table(name="historico_processo")
 */
class HistoricoProcesso extends AppModel
{

    /**
     * @type int
     * @Id
     * @Column(type="integer",name="id")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @type DateTime
     * @Column(type="datetime",name="horario")
     */
    private $horario;

    /**
     * @type Usuario
     * @ManyToOne(targetEntity="Usuario")
     * @JoinColumn(name="usuario_id", referencedColumnName="id",nullable=true,onDelete="CASCADE")
     */
    private $usuario;

    /**
     * @type Processo
     * @ManyToOne(targetEntity="Processo")
     * @JoinColumn(name="processo_id", referencedColumnName="id",nullable=true,onDelete="CASCADE")
     */
    private $processo;

    /**
     * @type string
     * @Column(type="string",name="nome_usuario")
     */
    private $nomeUsuario;

    /**
     * @type string
     * @Column(type="string",name="mensagem")
     */
    private $mensagem;

    /**
     * @type string
     * @Column(type="string",name="ip",nullable=true)
     */
    private $ip;

    /**
     * @type string
     * @Column(type="string",name="maquina",nullable=true)
     */
    private $maquina;

    /**
     * @type string
     * @Column(type="string",name="tipo",length=50,columnDefinition="ENUM('criado', 'atualizado','enviado','recebido','novo-anexo','cancelado-envio','visualizado','arquivado', 'email-enviado','email-erro')")
     */
    private $tipo;

    function __construct()
    {
        $this->horario = new DateTime();
    }

    function getId(): ?int
    {
        return $this->id;
    }

    function getHorario(): DateTime
    {
        return $this->horario;
    }

    function getUsuario(): ?Usuario
    {
        return $this->usuario;
    }

    function getProcesso()
    {
        return $this->processo;
    }

    function getNomeUsuario()
    {
        return $this->nomeUsuario;
    }

    function getMensagem()
    {
        return $this->mensagem;
    }

    function getIp()
    {
        return $this->ip;
    }

    function getTipo()
    {
        return $this->tipo;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    function setHorario($horario)
    {
        $this->horario = $horario;
    }

    function setUsuario($usuario)
    {
        $this->usuario = $usuario;
    }

    function setProcesso($processo)
    {
        $this->processo = $processo;
    }

    function setNomeUsuario($nomeUsuario)
    {
        $this->nomeUsuario = $nomeUsuario;
    }

    function setMensagem($mensagem)
    {
        $this->mensagem = $mensagem;
    }

    function setIp($ip)
    {
        $this->ip = $ip;
    }

    function setTipo($tipo)
    {
        $this->tipo = $tipo;
    }

    function getMaquina()
    {
        return $this->maquina;
    }

    function setMaquina($maquina)
    {
        $this->maquina = $maquina;
    }

    /**
     * @throws ORMException
     * @throws \Doctrine\ORM\Exception\ORMException
     * @throws \Doctrine\DBAL\Exception
     */
    function listarProximos($limite = 10)
    {
        return $this->getDAO()->listarProximos($limite);
    }

    /**
     * Função centralizada que salva todo o histórico de um processo
     * @param string $tipo
     * @param Processo $processo
     * @param Tramite|null $tramite
     * @param Anexo|null $anexo
     * @param Usuario|null $usuario
     * @throws Exception
     */
    static function registrar(string $tipo, Processo $processo, Tramite $tramite = null, Anexo $anexo = null, Usuario $usuario = null)
    {
        $usuarioLog = $usuario == null ? (UsuarioController::getUsuarioLogadoDoctrine()) : $usuario;
        $nomenclatura = AppController::getParametosConfig('nomenclatura');
        if ($usuarioLog != null) {
            switch ($tipo) {
                case TipoHistoricoProcesso::VISUALIZADO:
                    $mensagem = " O $nomenclatura nº <strong>$processo</strong> foi visualizado.";
                    break;
                case TipoHistoricoProcesso::RECEBIDO:
                    $mensagem = " O $nomenclatura nº <strong>$processo</strong> foi recebido no setor <strong>{$tramite->getSetorAtual()}</strong> pelo usuário <strong>{$tramite->getUsuarioRecebimento()->getPessoa()->getNome()}</strong>.";
                    break;
                case TipoHistoricoProcesso::CRIADO:
                    $mensagem = ($processo->getNumero() === null)
                        ? "Início da Solicitação de Criação de $nomenclatura."
                        : "O $nomenclatura nº <strong>$processo</strong> foi criado.";
                    break;
                case TipoHistoricoProcesso::ARQUIVADO:
                    $mensagem = "O $nomenclatura nº <strong>$processo</strong> foi arquivado.";
                    break;
                case TipoHistoricoProcesso::ATUALIZADO:
                    $mensagem = "O $nomenclatura nº <strong>$processo</strong> teve suas informações atualizadas.";
                    break;
                case TipoHistoricoProcesso::NOVO_ANEXO:
                    $mensagem = "Foi adicionado um novo documento: <strong>$anexo</strong> ao $nomenclatura nº <strong>$processo</strong>.";
                    break;
                case TipoHistoricoProcesso::CANCELADO_ENVIO:
                    $mensagem = "O envio do $nomenclatura nº $processo para o setor {$tramite->getSetorAtual()} foi cancelado.";
                    break;
                case TipoHistoricoProcesso::ENVIADO:
                    $mensagem = ($processo->getNumero() === null)
                        ? "O $nomenclatura precisa ser aprovado para seguir para o setor <strong>{$tramite->getSetorAtual()}</strong>."
                        : "O $nomenclatura nº $processo foi encaminhado para o setor <strong>{$tramite->getSetorAtual()}</strong>.";
                    break;
                case TipoHistoricoProcesso::EMAIL_ENVIADO:
                    $mensagem = "O interessado foi notificado através do e-mail: <strong>{$processo->getInteressado()->getPessoa()->getEmail()}</strong>.";
                    break;
                case TipoHistoricoProcesso::EMAIL_ERRO:
                    $mensagem = "O interessado não foi notificado, pois não possui e-mail cadastrado.";
                    break;
                default:
                    throw new TechnicalException("Tipo de log não definido: $tipo");
            }
            $log = new HistoricoProcesso();
            $log->setUsuario($usuarioLog);
            $log->setNomeUsuario($usuarioLog->getPessoa()->getNome());
            $log->setTipo($tipo);
            $log->setMensagem($mensagem);
            $log->setProcesso($processo);
            $log->inserir(false);
        }
    }

    public function jsonSerialize(): array
    {
        return [
            "id" => $this->id,
            "horario" => Functions::formatarData($this->horario),
            "usuario_id" => is_null($this->usuario) ? "" : $this->usuario->getId(),
            "processo_id" => is_null($this->processo) ? "" : $this->processo->getId(),
            "nome_usuario" => $this->nomeUsuario,
            "mensagem" => $this->mensagem,
            "ip" => $this->ip,
            "maquina" => $this->maquina,
            "tipo" => $this->tipo,
        ];
    }

    public function imprimir(): string
    {
        return json_encode($this, JSON_PRETTY_PRINT);
    }
}