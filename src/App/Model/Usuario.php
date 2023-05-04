<?php /** @noinspection PhpUnused */

namespace App\Model;

use App\Controller\IndexController;
use App\Enum\TipoLog;
use App\Enum\TipoUsuario;
use App\Util\Email;
use Core\Controller\AppController;
use Core\Exception\BusinessException;
use Core\Exception\SecurityException;
use Core\Model\AppModel;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\ConnectionException;
use Doctrine\ORM\ORMException;
use Exception;
use Symfony\Component\Console\Exception\InvalidArgumentException;

/**
 * @Entity
 * @HasLifecycleCallbacks
 * @Table(name="usuario")
 */
class Usuario extends AppModel
{

    /**
     * @Id
     * @Column(type="integer",name="id")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /** @Column(type="integer",name="codigo_fiorilli",nullable=true) */
    private $codigoFiorilli;

    /** @Column(type="integer",name="codigo_nea",nullable=true) */
    private $codigoNea;

    /**
     * @Column(type="string",name="login",nullable=false,unique=true)
     */
    private $login;

    /**
     * @Column(type="string",name="senha",length=32)
     */
    private $senha;

    /**
     * @Column(type="string",name="nome_pasta_digitalizacao",length=100,nullable=true)
     */
    private $nomePastaDigitalizacao;

    /**
     * @Column(type="boolean",name="is_ativo")
     */
    private $ativo;

    /**
     * @Column(type="date",name="data_cadastro")
     */
    private $dataCadastro;

    /**
     * @Column(type="datetime",name="ultimo_login",nullable=true)
     * @var DateTime $ultimoLogin
     */
    private $ultimoLogin;

    /**
     * @Column(type="datetime",name="ultima_alteracao",nullable=true)
     */
    private $ultimaAlteracao;

    /**
     * @ManyToMany(targetEntity="Setor", mappedBy="usuarios")
     * @JoinTable(name="setores_usuarios")
     */
    private $setores;

    /**
     * @ManyToOne(targetEntity="Grupo", inversedBy="usuarios")
     * @JoinColumn(name="grupo_id", referencedColumnName="id")
     * @var Grupo $grupo
     */
    private $grupo;

    /**
     * @Column(type="string",name="cargo",nullable=true)
     */
    private $cargo;

    /** @Column(type="string", nullable=false, columnDefinition="ENUM('admin', 'usuario','master', 'visitante', 'interessado')") */
    private $tipo;

    /**
     * @Column(type="string",name="token",nullable=true )
     */
    private $token;

    /**
     * @ManyToOne (targetEntity="Pessoa", inversedBy="usuarios", cascade={"persist"})
     * @JoinColumn(name="pessoa_id", referencedColumnName="id")
     */
    private $pessoa;

    /**
     * @Column(type="string",name="token_ativacao",nullable=true, unique=true)
     */
    private $tokenAtivacao;

    public function __construct()
    {
        $this->ativo = true;
        $this->setores = new ArrayCollection();
        $this->dataCadastro = new DateTime();
    }

    public function getId(): ?int
    {

        return $this->id;
    }

    public function setId($id) :void
    {
        $this->id = $id;
    }

    /**
     * @param bool $formatar
     * @return DateTime|string|null
     */
    function getDataCadastro(bool $formatar = false)
    {
        if (!empty($this->dataCadastro) && $formatar) {
            return $this->dataCadastro->format('d/m/Y');
        }
        return $this->dataCadastro;
    }

    function setDataCadastro($dataCadastro): void
    {
        $this->dataCadastro = $dataCadastro;
    }

    /**
     * @param bool $formatar
     * @return DateTime|string|null
     */
    public function getUltimaAlteracao(bool $formatar = false)
    {
        if (!empty($this->ultimaAlteracao) && $formatar) {
            return $this->ultimaAlteracao->format('d/m/Y - H:i:s');
        }
        return $this->ultimaAlteracao;
    }

    public function setUltimaAlteracao($ultimaAlteracao): void
    {
        $this->ultimaAlteracao = $ultimaAlteracao;
    }

    public function getCodigoNea()
    {
        return $this->codigoNea;
    }

    public function setCodigoNea($codigoNea): void
    {
        $this->codigoNea = $codigoNea;
    }

    public function getCodigoFiorilli()
    {
        return $this->codigoFiorilli;
    }

    public function setCodigoFiorilli($codigoFiorilli): void
    {
        $this->codigoFiorilli = $codigoFiorilli;
    }

    public function getNomePastaDigitalizacao()
    {
        return $this->nomePastaDigitalizacao;
    }

    public function setNomePastaDigitalizacao($nomePastaDigitalizacao): void
    {
        $this->nomePastaDigitalizacao = $nomePastaDigitalizacao;
    }

    public function getCargo()
    {
        return $this->cargo;
    }

    public function setCargo($cargo): void
    {
        $this->cargo = $cargo;
    }

    public function getTipo($formatar = false): ?string
    {
        if ($formatar) {
            return TipoUsuario::getDescricao($this->tipo);
        }
        return $this->tipo;
    }

    public function setTipo($tipo): void
    {
        if (!in_array($tipo, TipoUsuario::getTipos(), true)) {
            throw new InvalidArgumentException("Tipo de usuário inválido");
        }
        $this->tipo = $tipo;
    }

    public function getLogin()
    {
        return $this->login;
    }

    public function setLogin($login): void
    {
        $this->login = $login;
    }

    public function getSenha()
    {
        return $this->senha;
    }

    public function setSenha($senha): void
    {
        $this->senha = $senha;
    }

    /**
     * @param bool $formatar
     * @return string|boolean
     */
    public function getAtivo(bool $formatar = false)
    {
        if ($formatar) {
            return $this->ativo ? "Sim" : "Não";
        }
        return $this->ativo;
    }

    public function setAtivo($ativo): void
    {
        $this->ativo = $ativo;
    }

    /**
     * @param bool $formatar
     * @return string|DateTime
     */
    public function getUltimoLogin(bool $formatar = false)
    {
        if (!empty($this->ultimoLogin) && $formatar) {
            return $this->dataCadastro->format('d/m/Y - H:i:s');
        }
        return $this->ultimoLogin;
    }

    public function setUltimoLogin($ultimo_login): void
    {
        $this->ultimoLogin = $ultimo_login;
    }

    /**
     * @return Setor[]|Collection|null
     * @throws Exception
     * @throws ORMException
     */
    public function getSetores()
    {
        if ($this->tipo == TipoUsuario::MASTER) {
            return (new Setor())->listarAtivos();
        }
        $setores = new ArrayCollection();
        foreach ($this->setores as $setor) {
            if ($setor->getIsAtivo()) {
                $setores->add($setor);
            }
        }
        return $setores;
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     * @throws ORMException
     */
    public function listarSelect2($busca, $pagina)
    {
        /**
         * @var InteressadoDao $dao
         */
        $dao = $this->getDAO();
        return $dao->listarSelect2($busca, $pagina);
    }

    public function setSetores($setores): void
    {
        $this->setores = $setores;
    }

    /**
     * @return Grupo
     */
    public function getGrupo(): Grupo
    {
        return $this->grupo ?? new Grupo();
    }

    /**
     * @param Grupo $grupo
     * @return void
     */
    public function setGrupo(Grupo $grupo): void
    {
        $this->grupo = $grupo;
    }

    public function getPessoa(): ?Pessoa
    {
        return $this->pessoa;
    }

    public function setPessoa(Pessoa $pessoa): void
    {
        $this->pessoa = $pessoa;
    }

    public function getSetoresIds($text = false)
    {
        $ids = array();
        foreach ($this->setores as $setor) {
            $ids[] = $setor->getId();
        }
        return $text ? implode(',', $ids) : $ids;
    }

    /**
     * @return string
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * @return void
     * token id + email + app_salt
     */
    public function setToken(): void
    {

        $this->token = sha1($this->getId() . $this->getPessoa()->getEmail() . IndexController::getConfig()['token_integracao']);
    }

    public function __toString()
    {
        return is_null($this->pessoa) || empty($this->pessoa->getNome()) ? "" : $this->pessoa->getNome();
    }
    /**
     * @throws ORMException
     * @throws ORMException
     * @throws Exception
     */
    public function bloquearUsuario(Usuario $usuario): ?bool
    {
        // Todas as tentativas de login são contadas dentro do intervalo das últimas 2 horas.
        $time = time() - (2 * 60 * 60);
        $valid_attempts = new DateTime();
        $valid_attempts->setTimestamp($time);
        $result = $this->getDAO()->listarTentativas($usuario, $valid_attempts);
        return count($result) > 5;
    }

    /**
     * @throws Exception
     * @throws ORMException
     * @throws ORMException
     * @return Usuario[]|ArrayCollection|null
     */
    public function listarUsuarios()
    {
        return $this->getDAO()->listarUsuarios();
    }

    public function adicionaSetor(Setor $setor)
    {
        if (!$this->setores->contains($setor)) {
            $this->setores->add($setor);
        }
    }

    /**
     * @throws ORMException
     * @throws ORMException
     * @throws Exception
     */
    public function listarUsuariosDigitalizacao()
    {
        return $this->getDAO()->listarUsuariosDigitalizacao();
    }

    /**
     * @throws ORMException
     * @throws ORMException
     * @throws SecurityException
     * @throws Exception
     * @throws BusinessException
     * @throws Exception
     */
    function registrarLogin(Usuario $usuario, $sucesso)
    {
        $tabela = $usuario->getTableName();
        if ($sucesso) {
            Log::registrarLog(TipoLog::LOGIN_SUCCESS, $tabela, "Login realizado com sucesso.", $usuario);
            $usuario->setUltimoLogin(new DateTime());
            $usuario->atualizar(false);
        } else {
            Log::registrarLog(TipoLog::LOGIN_ATTEMPT, $tabela, "Login inválido: erro de senha.", $usuario);
//            if ($this->bloquearUsuario($usuario)) {
                // Log::registrarLog(TipoLog::ACTION_UPDATE, $tabela,"Usuário bloqueado por errar a senha por mais de 5 vezes em menos de 2 horas.", $usuario);
                // $usuario->setAtivo(false);
                // $usuario->atualizar(false);
//            }
        }
    }

    /**
     * @param int $codigo_entidade
     * @return PermissaoEntidade|PermissaoEntidade[]|ArrayCollection|null
     */
    public function getPermissoesEntidade($codigo_entidade = null)
    {
        if ($codigo_entidade != null) {
            $permissoes = ($this->grupo === null) ? $this->getPermissao() : $this->grupo->getPermissoesEntidade();
            foreach ($permissoes as $permissao) {
                if ($permissao->getCodigoEntidade() == $codigo_entidade) {
                    return $permissao;
                }
            }
            return null;
        }
        return $this->grupo->getPermissoesEntidade();
    }

    public function getPermissoesRelatorio($codigo_relatorio = null)
    {
        if ($codigo_relatorio != null) {
            foreach (($this->grupo)->getPermissoesEntidade() as $permissao) {
                if ($permissao->getCodigoEntidade() == $codigo_relatorio) {
                    return $permissao;
                }
            }
            return null;
        }
        return $this->grupo->getPermissoesEntidade();
    }

    public function postPersistAndUpdateSetor(Usuario $usuario)
    {
        foreach($_POST['setores'] as $setor){
            $setor->adicionaUsuario($usuario);
            $setor->atualizar();
        }
    }

    private static function getSalt()
    {
        return AppController::getConfig()['app_salt'];
    }

    public static function codificaSenha($senha): ?string
    {
        return md5(self::getSalt() . $senha);
    }

    /**
     * @throws ORMException
     * @throws ORMException
     * @throws Exception
     */
    public function buscarPorNome($nome)
    {
        $result = $this->getDAO()->buscarPorNome($nome);
        return count($result) > 0 ? $result[0] : null;
    }

    /**
     * @throws ORMException
     * @throws ORMException
     * @throws Exception
     */
    public function buscarPorEmail($email)
    {
        return $this->getDAO()->buscaPorEmail($email);
    }

    public function buscarPorLogin($login)
    {
        $result = $this->getDAO()->buscaPorLogin($login);
        return !empty($result) ? $result : null;
    }

    public function isAdm(): bool
    {
        $tipo = $this->getTipo();
        return $tipo === TipoUsuario::ADMINISTRADOR || $tipo === TipoUsuario::MASTER;
    }

    public function isInteressado(): bool
    {
        $tipo = $this->getTipo();
        return $tipo === TipoUsuario::INTERESSADO;
    }

    /**
     * @throws ORMException
     * @throws ORMException
     * @throws Exception
     */
    public function autenticar($login, $senha)
    {
        return $this->getDAO()->autenticar($login, $senha);
    }

    /**
     * @throws Exception
     * @throws ORMException
     * @throws ORMException
     */
    public function buscaPorEmail($email)
    {
        return $this->getDAO()->buscaPorEmail($email);
    }

    /**
     * @throws ORMException
     * @throws ConnectionException
     */
    public function seed(): void
    {
        $data = [
            ['login'=>'admin', 'nome'=>'Administrador','senha'=>'mudar123', 'pasta'=>'admin', 'email'=>'victor@lxtec.com.br'],
            ['login'=>'lxtec', 'nome'=>'LXTEC','senha'=>'lx121314', 'pasta'=>'lxtec', 'email'=>'atendimento@lxtec.com.br']
        ];
        foreach ($data as $item) {

            $pessoa = new Pessoa();
            $count_pessoa = count($pessoa->buscarPorNome($item['nome'])) == 0;
            $count_user = count($this->buscarPorLogin($item['login'])) == 0;
            if ($count_user && $count_pessoa) {
                try {
                    $pessoa->beginTransaction();
                    $pessoa->setNome($item['nome']);
                    $pessoa->setEmail($item['email']);
                    $pessoa->inserir();
                    $usuario = new Usuario();
                    $usuario->setTipo(TipoUsuario::MASTER);
                    $usuario->setLogin($item['login']);
                    $usuario->setSenha(self::codificaSenha($item['senha']));
                    $usuario->setNomePastaDigitalizacao($item['pasta']);
                    $usuario->setPessoa($pessoa);
                    $usuario->inserir();
                    $pessoa->commit();
                }catch(Exception $e) {
                    ob_start();
                    var_dump($e->getTraceAsString(),$e->getMessage());
                    $content = ob_get_contents();
                    ob_clean();
                    error_log($content);
                    $pessoa->getDAO()->rollback();
                }
            }
        }
    }


    /**
     * @return mixed
     */
    public function getTokenAtivacao()
    {
        return $this->tokenAtivacao;
    }

    /**
     * @param mixed $tokenAtivacao
     */
    public function setTokenAtivacao(): void
    {
        $this->tokenAtivacao = md5($this->getId().$this->getPessoa()->getId().$this->getPessoa()->getEmail());
    }

    /**
     * @param string $tokenAtivacao
     * @return Usuario|null
     */
    public function buscarPorTokenAtivacao(string $tokenAtivacao): ?Usuario
    {
        return $this->getDAO()->buscarPorTokenAtivacao($tokenAtivacao);
    }

    /**
     * @return void
     * @PostPersist
     */
    public function enviarEmailConfirmacao()
    {
        if(isset($_POST['transformar']) || isset($_POST['isInterno'])){
            return;
        }
        if ($this->getTipo() === TipoUsuario::INTERESSADO) {
            $this->setTokenAtivacao();
            (new Email())->enviarConfirmacaoEmailValido($this);

        }
    }

    public function jsonSerialize()
    {
        return [
            "id" => $this->id,
            "codigo_fiorilli" => $this->codigoFiorilli,
            "codigo_nea" => $this->codigoNea,
            "login" => $this->login,
            "senha" => $this->senha,
            "nome_pasta_digitalizacao" => $this->nomePastaDigitalizacao,
            "is_ativo" => $this->ativo,
            "data_cadastro" => $this->dataCadastro,
            "setores_usuarios" => $this->setores,
            "grupo" => $this->grupo,
            "cargo" => $this->cargo,
            "tipo" => $this->tipo,
            "token" => $this->token,
            "pessoa" => $this->pessoa
        ];
    }
}