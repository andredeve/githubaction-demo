<?php /** @noinspection PhpUnused */

namespace App\Model;

use Core\Interfaces\EntityInterface;
use Core\Model\AppModel;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @Entity
 * @Table(name="grupo")
 */
class Grupo extends AppModel implements EntityInterface
{

    /**
     * @Id
     * @Column(type="integer",name="id")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Column(type="string",name="nome")
     */
    private $nome;

    /**
     * Define se usuários do grupo podem gerar relatórios
     * @Column(type="boolean",name="gerar_relatorios")
     */
    private $relatorios;

    /**
     *  Define se usuários do grupo podem tramitar/encaminhar processos
     * @Column(type="boolean",name="tramitar")
     */
    private $tramitar;

    /**
     * Define se usuários do grupo podem arquivar processos
     * @Column(type="boolean",name="arquivar")
     */
    private $arquivar;

    /**
     * Define se usuários do grupo podem cadastrar processos retroativo
     * @Column(type="boolean",name="retroativo")
     */
    private $retroativo;

    /**
     * @OneToMany(targetEntity="PermissaoEntidade", mappedBy="grupo",cascade={"persist"})
     */
    private $permissoesEntidade;

    /**
     * @OneToMany(targetEntity="Usuario", mappedBy="grupo")
     */
    private $usuarios;

    /**
     * @Column(type="date",name="data_cadastro")
     */
    private $dataCadastro;

    /**
     * @Column(type="datetime",name="ultima_alteracao",nullable=true)
     */
    private $ultimaAlteracao;

    public function __construct()
    {
        $this->usuarios = new ArrayCollection();
        $this->permissoesEntidade = new ArrayCollection();
    }

    function getId(): ?int
    {
        return $this->id;
    }

    function getNome()
    {
        return $this->nome;
    }

    /**
     * @return Collection<Usuario>
     */
    function getUsuarios(): ?Collection
    {
        return $this->usuarios;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    function setNome($nome)
    {
        $this->nome = $nome;
    }

    /**
     * @param Collection<Usuario> $usuarios
     * @return void
     */
    function setUsuarios(Collection $usuarios)
    {
        $this->usuarios = $usuarios;
    }

    function getRelatorios()
    {
        return $this->relatorios;
    }

    function getTramitar()
    {
        return $this->tramitar;
    }

    function getArquivar()
    {
        return $this->arquivar;
    }

    function setRelatorios($relatorios)
    {
        $this->relatorios = $relatorios;
    }

    function setTramitar($tramitar)
    {
        $this->tramitar = $tramitar;
    }

    function setArquivar($arquivar)
    {
        $this->arquivar = $arquivar;
    }

    function getDataCadastro()
    {
        return $this->dataCadastro;
    }

    function getUltimaAlteracao()
    {
        return $this->ultimaAlteracao;
    }

    /**
     * @param $codigo_entidade
     * @return ArrayCollection|PermissaoEntidade[]|null
     */
    function getPermissoesEntidade($codigo_entidade = null)
    {
        if ($codigo_entidade != null) {
            foreach ($this->permissoesEntidade as $permissao) {
                if ($permissao->getCodigoEntidade() == $codigo_entidade) {
                    return $permissao;
                }
            }
            return null;
        }
        return $this->permissoesEntidade;
    }

    function setPermissoesEntidade($permissoesEntidade)
    {
        $this->permissoesEntidade = $permissoesEntidade;
    }

    function setDataCadastro($data_cadastro)
    {
        $this->dataCadastro = $data_cadastro;
    }

    function setUltimaAlteracao($ultima_alteracao)
    {
        $this->ultimaAlteracao = $ultima_alteracao;
    }

    function adicionaUsuario(Usuario $usuario)
    {
        if (!$this->usuarios->contains($usuario)) {
            $this->usuarios->add($usuario);
        }
    }

    function removeUsuario(Usuario $usuario)
    {
        $this->usuarios->removeElement($usuario);
    }

    public function __toString()
    {
        return (string)$this->nome;
    }

    function adicionaPermissaoEntidade(PermissaoEntidade $permissao)
    {
        $achou = false;
        foreach ($this->permissoesEntidade as $p) {
            if ($p->getCodigoEntidade() == $permissao->getCodigoEntidade()) {
                $achou = true;
            }
        }
        if (!$achou) {
            $this->permissoesEntidade->add($permissao);
        }
    }

    static function createNoPermissisionError(): string
    {
        return '<div class="row">'
            . '<div class="col-md-12">'
            . '<div class="alert alert-danger">'
            . '<h3><font face="Tahoma" color="red"><i class="fa fa-ban"></i> Erro 403</font><small>: sem permissão</small></h3>'
            . '<br />'
            . '<p class="lead">Desculpe, mas você não tem permissão para realizar essa operação</p>'
            . '</div>'
            . '</div>'
            . '</div>';
    }

	/**
	 * Define se usuários do grupo podem cadastrar processos retroativo
	 * @return mixed
	 */
	public function getRetroativo() {
		return $this->retroativo;
	}
	
	/**
	 * Define se usuários do grupo podem cadastrar processos retroativo
	 * @param mixed $retroativo Define se usuários do grupo podem cadastrar processos retroativo
	 */
	public function setRetroativo($retroativo) {
		$this->retroativo = $retroativo;
	}
}
