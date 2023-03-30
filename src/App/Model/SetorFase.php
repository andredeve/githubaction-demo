<?php /** @noinspection PhpUnused */

namespace App\Model;

use Core\Model\AppModel;
use Core\Util\Functions;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Exception;

/**
 * @Entity
 * @Table(name="setor_fase")
 */
class SetorFase extends AppModel
{

    /**
     * @Id
     * @Column(type="integer",name="id")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ManyToOne(targetEntity="Setor",fetch="EAGER")
     * @JoinColumn(name="setor_id", referencedColumnName="id",nullable=false)
     */
    private $setor;

    /**
     * @ManyToOne(targetEntity="Fase", inversedBy="setores")
     * @JoinColumn(name="fase_id", referencedColumnName="id",onDelete="CASCADE")
     */
    private $fase;

    /**
     * @OneToMany(targetEntity="Pergunta", mappedBy="setorFase",cascade={"persist"})
     * @OrderBy({"ordem" = "ASC"})
     */
    private $perguntas;

    /**
     * @OneToMany(targetEntity="Tarefa", mappedBy="setorFase",cascade={"persist"})
     * @OrderBy({"ordem" = "ASC"})
     */
    private $tarefas;

    /**
     * @OneToMany(targetEntity="Campo", mappedBy="setorFase",cascade={"persist"})
     * @OrderBy({"ordem" = "ASC"})
     */
    private $campos;

    /**
     * @Column(type="integer",name="prazo",nullable=true)
     */
    private $prazo;

    /**
     * @Column(type="boolean",name="is_prazo_dia_util",nullable=true)
     */
    private $isPrazoDiaUtil;

    function __construct()
    {
        $this->tarefas = new ArrayCollection();
        $this->perguntas = new ArrayCollection();
        $this->campos = new ArrayCollection();
    }

    function getId(): ?int
    {
        return $this->id;
    }

    function getSetor(): Setor
    {
        if ($this->setor == null) {
            return new Setor();
        }
        return $this->setor;
    }

    function getFase()
    {
        return $this->fase;
    }

    function getPrazo()
    {
        return $this->prazo;
    }

    function getIsPrazoDiaUtil()
    {
        return $this->isPrazoDiaUtil;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    function setSetor($setor)
    {
        $this->setor = $setor;
    }

    function setFase($fase)
    {
        $this->fase = $fase;
    }

    function setPrazo($prazo)
    {
        $this->prazo = $prazo;
    }

    function setIsPrazoDiaUtil($isPrazoDiaUtil)
    {
        $this->isPrazoDiaUtil = $isPrazoDiaUtil;
    }

    function getPerguntas(): ?Collection
    {
        return $this->perguntas;
    }

    function getTarefas(): ?Collection
    {
        return $this->tarefas;
    }

    function setPerguntas($perguntas)
    {
        $this->perguntas = $perguntas;
    }

    function setTarefas($tarefas)
    {
        $this->tarefas = $tarefas;
    }

    function getCampos(): ?Collection
    {
        return $this->campos;
    }

    function setCampos($campos)
    {
        $this->campos = $campos;
    }

    function temRequisitos(): bool
    {
        return count($this->campos) > 0 || count($this->perguntas) > 0 || count($this->tarefas) > 0;
    }

    /**
     * Função que calcula o vencimento de um setor da fase.
     * @param null $data_ref
     * @throws Exception
     */
    function getVencimento($data_ref = null)
    {
        $data_atual = new DateTime($data_ref != null ? $data_ref : "");
        if (!empty($this->prazo)) {
            if ($this->isPrazoDiaUtil) {
                return Functions::getDataUtil($data_atual, $this->prazo);
            }
            $data_atual->modify("+" . $this->prazo . " day");
        }
        return $data_atual->format("Y-m-d");
    }

    public function __toString()
    {
        if ($this->setor->getSigla() != null) {
            $this->setor->getSigla();
        }
        return $this->setor->getNome();
    }

}
