<?php /** @noinspection PhpUnused */

namespace App\Model;

use App\Controller\UsuarioController;
use Core\Model\AppModel;
use Core\Util\Functions;
use DateTime;
use Exception;

/**
 * @Entity
 * @Table(name="log")
 * @InheritanceType("SINGLE_TABLE")
 * @DiscriminatorColumn(name="discr", type="string")
 */
class Log extends AppModel
{

    /**
     * @Id
     * @Column(type="integer",name="id")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Column(type="datetime",name="horario")
     */
    private $horario;

    /**
     * @ManyToOne(targetEntity="Usuario")
     * @JoinColumn(name="usuario_id", referencedColumnName="id",nullable=true,onDelete="CASCADE")
     */
    private $usuario;

    /**
     * @Column(type="string",name="nome_usuario")
     */
    private $nomeUsuario;

    /**
     * @Column(type="string",name="mensagem")
     */
    private $mensagem;

    /**
     * @Column(type="text",name="antigo",length=65532,nullable=true)
     */
    private $antigo;

    /**
     * @Column(type="text",name="novo",length=65532,nullable=true)
     */
    private $novo;

    /**
     * @Column(type="string",name="tabela",nullable=true)
     */
    private $tabela;

    /**
     * @Column(type="string",name="ip")
     */
    private $ip;

    /**
     * @Column(type="string",name="tipo",length=50,columnDefinition="ENUM('login-success', 'login-attempt','insert','update','delete','log')")
     */
    private $tipo;

    function __construct()
    {
        $this->horario = new DateTime();
    }

    function getHorario(): ?DateTime
    {
        return $this->horario;
    }

    function getUsuario()
    {
        return $this->usuario;
    }

    function setHorario($horario)
    {
        $this->horario = $horario;
    }

    function setUsuario($usuario)
    {
        $this->usuario = $usuario;
    }

    function getIp()
    {
        return $this->ip;
    }

    function setIp($ip)
    {
        $this->ip = $ip;
    }

    function getTipo()
    {
        return $this->tipo;
    }

    function setTipo($tipo)
    {
        $this->tipo = $tipo;
    }

    function getMensagem()
    {
        return $this->mensagem;
    }

    function setMensagem($mensagem)
    {
        $this->mensagem = $mensagem;
    }

    function getId(): ?int
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    function getNomeUsuario()
    {
        return $this->nomeUsuario;
    }

    function setNomeUsuario($nomeUsuario)
    {
        $this->nomeUsuario = $nomeUsuario;
    }

    function getAntigo()
    {
        return $this->antigo;
    }

    function getNovo()
    {
        return $this->novo;
    }

    function setAntigo($antigo)
    {
        $this->antigo = $antigo;
    }

    function setNovo($novo)
    {
        $this->novo = $novo;
    }

    function getTabela()
    {
        return $this->tabela;
    }

    function setTabela($tabela)
    {
        $this->tabela = $tabela;
    }

	/**
	 * @param $tipo
	 * @param $tabela
	 * @param $mensagem
	 * @param Usuario|null $usuario
	 * @param mixed|null $antigo
	 * @param mixed|null $novo
	 * @return bool|void
	 * @throws Exception
	 */
	static function registrarLog($tipo, $tabela, $mensagem, Usuario $usuario = null, $antigo = null, $novo = null)
    {
        if(isset($_SESSION["execucao_script"]) && $_SESSION["execucao_script"] == true ){
            return;
        }
        $log = new Log();
        if($tabela == 'log' ){
            return true;
        }
        $usuarioLog = $usuario == null ? (UsuarioController::getUsuarioLogadoDoctrine()) : $usuario;
        $log->setUsuario($usuarioLog);
        $log->setNomeUsuario($usuarioLog?$usuarioLog->getPessoa()->getNome():'');
        $log->setTipo($tipo);
        $log->setMensagem($mensagem);
        $log->setTabela($tabela);
        $log->setAntigo($antigo);
        $log->setNovo($novo);
        $log->setIp(Functions::getUserIp());
        $log->inserir(false);
    }

}
