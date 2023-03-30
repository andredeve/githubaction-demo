<?php /** @noinspection PhpUnused */

namespace App\Model;

use App\Enum\DestinacaoDocumento;
use Core\Interfaces\EntityInterface;
use Core\Model\AppModel;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\Exception\ORMException;

/**
 * @Entity
 * @Table(name="classificacao")
 */
class Classificacao extends AppModel implements EntityInterface
{

    /**
     * @Id
     * @Column(type="integer",name="id")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Column(type="string",name="codigo_conarq")
     */
    private $codigo;

    /**
     * @Column(type="string",name="titulo")
     */
    private $titulo;

    /**
     * @Column(type="string",name="fase_corrente")
     */
    private $faseCorrente;

    /**
     * @Column(type="string",name="fase_intermediaria")
     */
    private $faseIntermediaria;

    /** @Column(type="string", columnDefinition="ENUM('a', 's')",nullable=true) */
    private $tipo;

    /** @Column(type="string", columnDefinition="ENUM('permanente', 'eliminacao')",nullable=true) */
    private $destinacaoFinal;

    /**
     * @ManyToOne(targetEntity="Classificacao", cascade={"all"}, fetch="LAZY")
     */
    private $classificacaoPai;

    /**
     * @OneToMany(targetEntity="Classificacao", mappedBy="classificacaoPai", cascade={"persist"}, orphanRemoval=true)
     */
    private $classificacoes;


    /**
     * @Column(type="datetime",name="data_cadastro",nullable=true)
     */
    private $dataCadastro;

    /**
     * @Column(type="datetime",name="ultima_alteracao",nullable=true)
     */
    private $ultimaAlteracao;

    /**
     * @Column(type="text",nullable=true)
     */
    private $observacoes;

    function __construct()
    {
        $this->classificacoes = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * @param mixed $tipo
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    }


    function getObservacoes()
    {
        return $this->observacoes;
    }

    function setObservacoes($observacoes)
    {
        $this->observacoes = $observacoes;
    }

    function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return Collection<Classificacao>
     */
    function getClassificacoes(): ?Collection
    {
        return $this->classificacoes;
    }

    function setClassificacoes($classificacoes)
    {
        $this->classificacoes = $classificacoes;
    }

    function getCodigo()
    {
        return $this->codigo;
    }

    function getTitulo()
    {
        return $this->titulo;
    }

    function getClassificacaoPai(): Classificacao
    {
        if ($this->classificacaoPai == null) {
            return new Classificacao();
        }
        return $this->classificacaoPai;
    }


    function getDestinacaoFinal($formatar = false): ?string
    {
        if ($formatar) {
            return DestinacaoDocumento::getDescricao($this->destinacaoFinal);
        }
        return $this->destinacaoFinal;
    }

    function setCodigo($codigo)
    {
        $this->codigo = $codigo;
    }

    function setTitulo($titulo)
    {
        $this->titulo = $titulo;
    }

    function setClassificacaoPai($classificacaoPai)
    {
        $this->classificacaoPai = $classificacaoPai;
    }


    function setDestinacaoFinal($destinacaoFinal)
    {
        $this->destinacaoFinal = $destinacaoFinal;
    }

    function getDataCadastro()
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

    /**
     * @throws Exception
     * @throws \Doctrine\ORM\ORMException
     * @throws ORMException
     */
    function listarPais(): ?array
    {
        return $this->listarPorCampos(array('classificacaoPai' => null), array('codigo' => 'ASC'));
    }

    public function getFaseCorrente()
    {
        return $this->faseCorrente;
    }

    public function setFaseCorrente($faseCorrente)
    {
        $this->faseCorrente = $faseCorrente;
    }

    public function getFaseIntermediaria()
    {
        return $this->faseIntermediaria;
    }

    public function setFaseIntermediaria($faseIntermediaria)
    {
        $this->faseIntermediaria = $faseIntermediaria;
    }

    public function __toString()
    {
        return $this->codigo . " - " . strtoupper($this->getTitulo());
    }
}
