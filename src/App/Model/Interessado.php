<?php /** @noinspection PhpUnused */

namespace App\Model;

use App\Util\Email;
use Core\Util\Functions;
use App\Enum\TipoUsuario;
use App\Model\Dao\InteressadoDao;
use Core\Exception\BusinessException;
use Core\Model\AppModel;
use DateTime;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\ORMException;

/**
 * @Entity
 * @HasLifecycleCallbacks
 * @Table(name="interessado",uniqueConstraints={@UniqueConstraint(name="interessado_unique", columns={"id", "pessoa_id"})})
 */
class Interessado extends AppModel
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
     * @Column(type="datetime",name="ultima_alteracao",nullable=true)
     */
    private $ultimaAlteracao;

    /**
     * @Column(type="date",name="data_cadastro")
     */
    private $dataCadastro;

    /**
     * @Column(type="boolean",name="is_ativo", options={"default" : 1})
     */
    private $isAtivo;

    /**
     *  @Column(type="boolean",name="is_externo", options={"default" : 0})
     */
    private $isExterno;

    /**
     * @ManyToOne (targetEntity="Pessoa", inversedBy="interessados", cascade={"persist"})
     * @JoinColumn(name="pessoa_id", referencedColumnName="id")
     */
    private $pessoa;

    public function __construct()
    {
        $this->dataCadastro = new DateTime();
        $this->isAtivo = true;
        $this->isExterno = false;
    }


    public function __toString()
    {
        return is_null($this->pessoa) || empty($this->pessoa->getNome()) ? "" : $this->pessoa->getNome();
    }

    /**
     * @param bool $formatar
     * @return DateTime|string|null
     */
    function getDataCadastro(bool $formatar = false)
    {
        if (!empty($this->dataCadastro) && $formatar) {
            return $this->dataCadastro->format('d/m/Y - H:i:s');
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

    public function getIsAtivo($formatar = false)
    {
        if ($formatar) {
            return $this->isAtivo ? "Sim" : "Não";
        }
        return $this->isAtivo;
    }

    public function setIsAtivo($isAtivo): void
    {
        $this->isAtivo = $isAtivo;
    }

    public function getCodigoFiorilli()
    {
        return $this->codigoFiorilli;
    }

    public function setCodigoFiorilli($codigoFiorilli): void
    {
        $this->codigoFiorilli = $codigoFiorilli;
    }

    public function getCodigoNea()
    {
        return $this->codigoNea;
    }

    public function setCodigoNea($codigoNea): void
    {
        $this->codigoNea = $codigoNea;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @param bool $isExterno
     */
    public function setIsExterno(bool $isExterno): void
    {
        $this->isExterno = $isExterno;
    }

    /**
     * @return bool
     */
    public function getIsExterno(): bool
    {
        return $this->isExterno;
    }

    public function getPessoa(): Pessoa
    {
        return $this->pessoa;
    }

    public function setPessoa(Pessoa $pessoa): void
    {
        $this->pessoa = $pessoa;
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

    public function inserir($validarSomenteLeitura = true, bool $considerarPermissoes = true): ?int
    {
        if ($_POST['isExterno']){
            return $this->getDAO()->inserir($this, $validarSomenteLeitura);
        }else{
            return parent::inserir($validarSomenteLeitura, $considerarPermissoes);
        }
    }

    /**
     * @PostPersist
     * @throws BusinessException
     */
    public function setUsuario(): void
    {
        /*
         * TODO: criar/enviar enviar email com senha ou refazer senha para usuários cadastrados internamente
         *
        */
        $usuario = new Usuario();
        $usuario->setTipo(TipoUsuario::INTERESSADO);
        $usuario->setLogin($this->getPessoa()->getCpf() ?? $this->getPessoa()->getCnpj());
        $usuario->setCargo('Contribuinte');
        $usuario->setPessoa($this->getPessoa());
        if(isset($_POST['transformar'])){
            $senha_temp = Functions::geraSenha(8, true, true);
            $usuario->setSenha(Usuario::codificaSenha($senha_temp));
            (new Email())->enviarSenhaUsuario($usuario, $senha_temp);
        } else {
            $usuario->setSenha(Usuario::codificaSenha($_POST['senha_confirma']??''));
        }
        $usuario->inserir();
    }
}
