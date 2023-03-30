<?php /** @noinspection PhpUnused */

namespace App\Model;

use Core\Model\AppModel;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @Entity
 * @Table(name="fase")
 */
class Fase extends AppModel
{

    /**
     * @Id
     * @Column(type="integer",name="id")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Column(type="integer",name="numero")
     */
    private $numero;

    /**
     * @OneToMany(targetEntity="SetorFase", mappedBy="fase",cascade={"persist"})
     */
    private $setoresFase;

    /**
     * @ManyToOne(targetEntity="Fluxograma", inversedBy="setores")
     * @JoinColumn(name="fluxograma_id", referencedColumnName="id",onDelete="CASCADE")
     */
    private $fluxograma;

    
    /**
     * @Column(type="boolean",name="ativo", options={"default" : 1})
     */
    private $ativo;

    function __construct()
    {
        $this->setoresFase = new ArrayCollection();
    }

    function getNumero()
    {
        return $this->numero;
    }

    function setNumero($numero)
    {
        $this->numero = $numero;
    }

    function getId(): ?int
    {
        return $this->id;
    }

    function getFluxograma()
    {
        return $this->fluxograma;
    }

    function getAtivo()
    {
        return $this->ativo;
    }

    /**
     * @param mixed $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    function setFluxograma($fluxograma)
    {
        $this->fluxograma = $fluxograma;
    }

    function getSetoresFase($setor_id = null)
    {
        if ($setor_id != null) {
            foreach ($this->setoresFase as $setor_fase) {
                if ($setor_fase->getSetor()->getId() == $setor_id) {
                    return $setor_fase;
                }
            }
            return null;
        }
        return $this->setoresFase;
    }

    function setSetoresFase($setoresFase)
    {
        $this->setoresFase = $setoresFase;
    }

    function adicionaSetorFase(SetorFase $setor_fase)
    {
        if (!$this->setoresFase->contains($setor_fase)) {
            $this->setoresFase->add($setor_fase);
        }
    }

    /**
     * @param $data_ref
     * @param Setor|null $setor
     * @return false|string
     */
    function getVencimento($data_ref = null, Setor $setor = null)
    {
        $maior_data = Date('Y-m-d');
        foreach ($this->setoresFase as $set) {
            $vencimento_setor = $set->getVencimento($data_ref);
            if ($setor != null && $set->getSetor()->getId() == $setor->getId()) {
                return $vencimento_setor;
            } else if (strtotime($vencimento_setor) > strtotime($maior_data)) {
                $maior_data = $vencimento_setor;
            }
        }
        return $maior_data;
    }

    function getSiglaSetores(): ?string
    {
        $siglas = array();
        foreach ($this->setoresFase as $setor_fase) {
            $siglas[] = $setor_fase->getSetor()->getSigla();
        }
        return implode(',', $siglas);
    }

    function setAtivo($ativo)
    {
        $this->ativo = $ativo;
    }

    public function jsonSerialize(): array
    {
        return [

            "id" => $this->id,
            "numero" => $this->numero,
            "setor_fase" => $this->setoresFase,
            "fluxograma" => $this->fluxograma,
            "ativo" => $this->ativo
        ];
    }
}
