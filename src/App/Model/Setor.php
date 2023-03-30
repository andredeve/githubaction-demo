<?php /** @noinspection PhpUnused */

namespace App\Model;

use Core\Interfaces\EntityInterface;
use Core\Model\AppModel;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\ORMException;
use Exception;

/**
 * @Entity
 * @Table(name="setor",uniqueConstraints={@UniqueConstraint(name="setor_unique", columns={"nome", "setor_pai_id"})})
 */
class Setor extends AppModel implements EntityInterface
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
     * @Column(type="string",name="nome")
     */
    private $nome;

    /**
     * @Column(type="string",name="sigla",length=10,nullable=true)
     */
    private $sigla;
    
    /**
     * @Column(type="string",name="orgao",length=2,nullable=true)
     */
    private $orgao;
    
    /**
     * @Column(type="string",name="unidade",length=7,nullable=true)
     */
    private $unidade;
    
    /**
     * @ManyToOne(targetEntity="Setor")
     * @JoinColumn(name="setor_pai_id", referencedColumnName="id",nullable=true)
     */
    private $setorPai;
    /**
     * @Column(type="boolean",name="arquivar",nullable=true)
     */
    private $arquivar;
    /**
     * @Column(type="boolean",name="is_ativo", options={"default" : 1})
     */
    private $isAtivo;
    
    /**
     * @Column(type="boolean",name="disponivel_tramite", options={"default" : 1})
     */
    private $disponivelTramite;

    /**
     * @ManyToMany(targetEntity="Usuario", inversedBy="setores")
     * @JoinTable(name="setores_usuarios")
     * @var Collection $usuarios
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
    
    /**
     *  @Column(type="boolean",name="is_externo")
     */
    private $isExterno;
    
    function __construct()
    {
        $this->usuarios = new ArrayCollection();
        $this->isAtivo = true;
        $this->arquivar = false;
        $this->dataCadastro = new DateTime();
    }

    public function getCodigoNea()
    {
        return $this->codigoNea;
    }

    public function setCodigoNea($codigoNea)
    {
        $this->codigoNea = $codigoNea;
    }

    function getId(): ?int
    {
        return $this->id;
    }

    function getNome()
    {
        return $this->nome;
    }

    function getSigla()
    {
        if (!empty($this->sigla)) {
            return $this->sigla;
        }
        return $this->nome;
    }

    function getSetorPai(): ?Setor
    {
        if ($this->setorPai == null) {
            return new Setor();
        }
        return $this->setorPai;
    }

    function getCodigoFiorilli()
    {
        return $this->codigoFiorilli;
    }

    function setCodigoFiorilli($codigoFiorilli)
    {
        $this->codigoFiorilli = $codigoFiorilli;
    }

    function setSetorPai($setorPai)
    {
        $this->setorPai = $setorPai;
    }

    function getIsAtivo(): ?bool
    {
        return $this->isAtivo;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    function setNome($nome)
    {
        $this->nome = $nome;
    }

    function setSigla($sigla)
    {
        $this->sigla = $sigla;
    }

    function setIsAtivo($isAtivo)
    {
        $this->isAtivo = $isAtivo;
    }

    public function getArquivar(): bool
    {
        return $this->arquivar ?? false;
    }

    public function setArquivar($arquivar)
    {
        $this->arquivar = $arquivar;
    }

    function getDataCadastro(): ?DateTime
    {
        return $this->dataCadastro;
    }

    function getUltimaAlteracao()
    {
        return $this->ultimaAlteracao;
    }

    function setDataCadastro($data_cadastro)
    {
        $this->dataCadastro = $data_cadastro;
    }

    function setUltimaAlteracao($ultima_alteracao)
    {
        $this->ultimaAlteracao = $ultima_alteracao;
    }

    function getUsuarios(): ?Collection
    {
        return $this->usuarios;
    }

    function setUsuarios($usuarios)
    {
        $this->usuarios = $usuarios;
    }
    
    function getOrgao() {
        if($this->getSetorPai() && $this->getSetorPai()->getId()){
            return $this->getSetorPai()->getOrgao();
        }
        return $this->orgao;
        
    }

    function setOrgao($orgao) {
        $this->orgao = $orgao;
    }
    
    function getUnidade() {
        return $this->unidade;
    }

    function setUnidade($unidade) {
        $this->unidade = $unidade;
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

    /**
     * @throws Exception
     * @throws ORMException
     */
    function listarAtivos(): ?array
    {
        return $this->listarPorCampos(array('isAtivo' => true), array('nome' => "ASC"));
    }

    /**
     * Função recursiva que verifica se o existe um nó filho marcado a partir de um nó cabeça.
     * @param $marcados
     * @param Setor $setorPai
     * @param null $buscar_str
     * @return bool
     * @throws Exception
     * @throws ORMException
     * @throws ORMException
     */
    public function temFilhoMarcado($marcados, Setor $setorPai, $buscar_str = null): ?bool
    {
        foreach ($setorPai->getSetoresFilhos() as $filho) {
            if (in_array($filho->getId(), $marcados) || strpos($buscar_str, $filho->getNome()) !== false) {
                return true;
            } else if (count($filho->getSetoresFilhos()) > 0) {
                if ($this->temFilhoMarcado($marcados, $filho, $buscar_str)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @throws Exception
     * @throws ORMException
     * @throws ORMException
     */
    public function getSetoresFilhos(): ?array
    {
        return $this->listarPorCampos(array("setorPai" => $this, 'isAtivo' => true), array("nome" => "ASC"));
    }

    /**
     * @throws Exception
     * @throws ORMException
     * @return Setor[]|null
     */
    public function listarSetoresPai(): ?array
    {
        return $this->listarPorCampos(array("setorPai" => null, 'isAtivo' => true), array("nome" => "ASC"));
    }

    /**
     * @throws ORMException
     * @throws ORMException
     * @throws Exception
     */
    public function buscarPorDescricao($descricao)
    {
        return $this->getDAO()->buscarPorDescricao($descricao);
    }

    public function __toString()
    {
        return strtoupper($this->nome);
    }
    
    
    function getDisponivelTramite() {
        return $this->disponivelTramite;
    }

    function setDisponivelTramite($disponivelTramite) {
        $this->disponivelTramite = $disponivelTramite;
    }

    function getIsExterno() {
        return $this->isExterno;
    }

    function setIsExterno($isExterno) {
        $this->isExterno = $isExterno;
    }
    function seed(){
        $nome = 'CONTRIBUINTE';
        $setor = new Setor();
        if(!$setor->buscarPorDescricao($nome)) {
            $setor->setIsExterno(1);
            $setor->setArquivar(0);
            $setor->setDataCadastro(new DateTime());
            $setor->setNome($nome);
            $setor->setSigla("CONTRIB");
            $setor->setIsAtivo(1);
            $setor->setDisponivelTramite(1);
            $setor->inserir();
            $ini_path = APP_PATH . "_config/parametros.ini";
            $fp = fopen($ini_path, "a+");
            $res_setor = fwrite($fp, "\nprocesso_setor_contribuinte_id=" . $setor->getId());
            fclose($fp);
            echo "<br/>";
            echo "############################################";
            echo "<br/>";
            echo "<h5>Sucesso</h5>";
            echo "Setor gerado: id={$setor->getId()}";
            echo "Verifique o parametros.ini -> <b>processo_setor_contribuinte_id={$setor->getId()}</b>";
            echo "<br/>";
            echo "############################################";
            echo "<br/>";
        }
    }
}
