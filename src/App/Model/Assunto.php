<?php

namespace App\Model;

use Core\Interfaces\EntityInterface;
use Core\Model\AppDao;
use Core\Model\AppModel;
use Core\Util\Functions;
use DateTime;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Exception;

/**
 * @Entity
 * @HasLifecycleCallbacks
 * @Table(name="assunto")
 */
class Assunto extends AppModel implements EntityInterface
{

    /**
     * @type int
     * @Id
     * @Column(type="integer",name="id")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @type int
     * @Column(type="integer",name="codigo_fiorilli",nullable=true)
     */
    private $codigoFiorilli;

    /**
     * @type int
     * @Column(type="integer",name="codigo_nea",nullable=true)
     */
    private $codigoNea;

    /**
     * @type string
     * @Column(type="string",name="nome",unique=true)
     */
    private $descricao;

    /**
     * @type string
     * @Column(type="text",name="shadow_nome",nullable=false)
     */
    private $shadowNome;

    /**
     * @type int
     * @Column(type="integer",name="prazo")
     */
    private $prazo;

    /**
     * @type bool
     * @Column(type="boolean",name="is_prazo_dia_util")
     */
    private $isPrazoDiaUtil;

    /**
     * @type Assunto
     * @ManyToOne(targetEntity="Assunto")
     * @JoinColumn(name="assunto_pai_id", referencedColumnName="id",nullable=true)
     */
    private $assuntoPai;

    /**
     * @type Fluxograma
     * @OneToOne(targetEntity="Fluxograma", mappedBy="assunto")
     */
    private $fluxograma;

    /**
     * @type bool
     * @Column(type="boolean",name="is_ativo", options={"default" : 1})
     */
    private $isAtivo;
    
    /**
     * @type bool
     *  @Column(type="boolean",name="is_externo")
     */
    private $isExterno;

    /**
     * @type DateTime
     * @Column(type="date",name="data_cadastro")
     */
    private $dataCadastro;

    /**
     * @type DateTime
     * @Column(type="datetime",name="ultima_alteracao",nullable=true)
     */
    private $ultimaAlteracao;

    public function __construct()
    {
        $this->isExterno=false;
        $this->isAtivo = true;
        $this->prazo = 10;
        $this->isPrazoDiaUtil = true;
        $this->dataCadastro = new DateTime();
    }

    /**
     * @throws Exception
     * @throws \Doctrine\ORM\ORMException
     * @throws ORMException
     */
    function getSubAssuntos(): ?array
    {
        $subAssuntos = array();
        foreach ((new Assunto())->listarPorCampos(array('assuntoPai' => $this)) as $assunto) {
            if ($assunto->getFluxograma() != null) {
                $subAssuntos[] = $assunto;
            }
        }
        return $subAssuntos;
    }

    function getId(): ?int
    {
        return $this->id;
    }

    public function getCodigoNea()
    {
        return $this->codigoNea;
    }

    public function setCodigoNea($codigoNea)
    {
        $this->codigoNea = $codigoNea;
    }

    function getCodigoFiorilli()
    {
        return $this->codigoFiorilli;
    }

    function setCodigoFiorilli($codigoFiorilli)
    {
        $this->codigoFiorilli = $codigoFiorilli;
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

    function getDescricao($normalize = false): ?string
    {
        if ($normalize) {
            return Functions::sanitizeString($this->descricao);
        }
        return (string)mb_strtoupper($this->descricao, "UTF-8");
    }

    function getAssuntoPai(): ?Assunto
    {
        if ($this->assuntoPai == null) {
            return new Assunto();
        }
        return $this->assuntoPai;
    }

    function getIsAtivo(): ?bool
    {
        return $this->isAtivo;
    }

    function setId(?int $id): void {
        $this->id = $id;
    }

    function setDescricao($descricao)
    {
        $this->descricao = $descricao;
    }

    function setAssuntoPai($assuntoPai)
    {
        $this->assuntoPai = $assuntoPai;
    }

    function setIsAtivo($isAtivo)
    {
        $this->isAtivo = $isAtivo;
    }

    function getPrazo(): ?int
    {
        return $this->prazo;
    }

    function getIsPrazoDiaUtil(): bool
    {
        return $this->isPrazoDiaUtil ?? false;
    }

    function setPrazo($prazo)
    {
        $this->prazo = $prazo;
    }

    function setIsPrazoDiaUtil($isPrazoDiaUtil)
    {
        $this->isPrazoDiaUtil = $isPrazoDiaUtil;
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws ORMException
     * @throws NonUniqueResultException
     * @throws NoResultException
     * @throws Exception
     */
    function listarSelect2($busca, $pagina)
    {
        return $this->getDAO()->listarSelect2($busca, $pagina);
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws ORMException
     * @throws Exception
     */
    function listarAtivos(): ?array
    {
        return $this->listarPorCampos(array('isAtivo' => true, "assuntoPai" => null), array("descricao" => "ASC"));
    }

    /**
     * Função que calcula o vencimento de um assunto.
     * @param bool $formatar
     * @param $data_ref
     * @return DateTime|false|string|null
     * @throws \Exception
     */
    function getVencimento(bool $formatar = false, $data_ref = null)
    {
        $data = $data_ref == null ? new DateTime() : new DateTime($data_ref);
        if ($this->fluxograma != null) {
            $vencimento = $this->fluxograma->getVencimento($data_ref);
        } else if ($this->isPrazoDiaUtil) {
            $vencimento = Functions::getDataUtil($data, $this->prazo);
        } else {
            $data->modify("+" . $this->prazo . " day");
            $vencimento = $data->format("Y-m-d");
        }
        return $formatar ? Functions::converteData($vencimento) : $vencimento;
    }

    /**
     * Lista assuntos sem fluxograma atribuídos.
     * @return array
     * @throws Exception
     * @throws ORMException
     * @throws \Doctrine\ORM\ORMException
     */
    function listarDisponiveis(): ?array
    {
        $assuntos = array();
        foreach ($this->getDAO()->listarDisponiveis() as $a) {
            $assuntos[] = $a[0];
        }
        return $assuntos;
    }

    public function __toString()
    {
        return $this->getDescricao();
    }

    function getFluxograma(): ?Fluxograma
    {
        if ($this->fluxograma != null) {
            return $this->fluxograma->getIsAtivo() ? $this->fluxograma : null;
        }
        return $this->fluxograma;
    }

    function setFluxograma($fluxograma)
    {
        $this->fluxograma = $fluxograma;
    }

    /**
     * @throws Exception
     * @throws \Doctrine\ORM\ORMException
     * @throws ORMException
     */
    function listarPorDescricao($descricao)
    {
        return $this->getDAO()->listarPorDescricao($descricao);
    }

    /**
     * @return mixed
     */
    public function getShadowNome()
    {
        return $this->shadowNome;
    }

    public function setShadowNome($shadowNome)
    {
        $this->shadowNome = $shadowNome;
    }

    /** @PrePersist */
    public function inserirShadowNome()
    {
        $this->shadowNome = Functions::sanitizeString($this->descricao);
    }

    /** @PreUpdate */
    public function atualizarShadowNome()
    {
        $this->shadowNome = Functions::sanitizeString($this->descricao);
    }
    
    function getIsExterno(): bool
    {
        return $this->isExterno ?? false;
    }

    function setIsExterno($isExterno) {
        $this->isExterno = $isExterno;
    }

    public function jsonSerialize(): array
    {
        return [
            "id" => $this->id,
            "codigo_fiorilli" => $this->codigoFiorilli,
            "codigo_nea" => $this->codigoNea,
            "nome" => $this->descricao,
            "shadow_nome" => $this->shadowNome,
            "prazo" => $this->prazo,
            "is_prazo_dia_util" => $this->isPrazoDiaUtil,
            "assunto_pai_id" => is_null($this->assuntoPai) ? "" : $this->assuntoPai->getId(),
            "fluxograma_id" => is_null($this->fluxograma) ? "" : $this->fluxograma->getId(),
            "is_ativo" => $this->isAtivo,
            "is_externo" => $this->isExterno,
            "data_cadastro" => Functions::formatarData($this->dataCadastro),
            "ultima_alteracao" => Functions::formatarData($this->ultimaAlteracao)
        ];
    }

    public function imprimir(): string
    {
        return json_encode($this, JSON_PRETTY_PRINT);
    }

    /**
     * @throws ORMException
     * @throws Exception
     */
    static function getAssuntosExternos(): ?array
    {
        return AppDao::listarPor(get_called_class(), array('isExterno'=>'1'), array('descricao'=>'ASC'));
    }
}
