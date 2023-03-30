<?php /** @noinspection PhpUnused */

namespace App\Model;

use App\Model\Dao\RemessaDao;
use Core\Model\AppModel;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\Exception\ORMException;

/**
 * @Entity
 * @Table(name="remessa")
 * @package App\Model
 * @property int $id
 */
class Remessa extends AppModel
{

    /**
     * @Id
     * @Column(type="integer",name="id")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @Column(type="string",name="setor_origem",nullable=false)
     */
    private $setorOrigem;

    /**
     * @Column(type="string",name="responsavel_origem",nullable=true)
     */
    private $responsavelOrigem;

    /**
     * @Column(type="string",name="responsavel_destino",nullable=true)
     */
    private $responsavelDestino;
    /**
     * @Column(type="string",name="status",nullable=true)
     */
    private $status;
    /**
     * @Column(type="text",name="parecer",nullable=true)
     */
    private $parecer;
    /**
     * @Column(type="datetime",name="horario")
     */
    private $horario;

    /**
     * @Column(type="string",name="setor_destino",nullable=false)
     */
    private $setorDestino;
    /**
     * @OneToMany(targetEntity="Tramite", mappedBy="remessa")
     */
    private $tramites;

    /**
     * Remessa constructor.
     */
    public function __construct()
    {
        $this->tramites = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * @throws Exception
     * @throws \Doctrine\ORM\ORMException
     * @throws ORMException
     */
    function getStatusTramite()
    {
        $tramites = $this->getTramites();
        if (isset($tramites[0])) {
            return $tramites[0]->getStatus();
        }
        return null;
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws ORMException
     * @throws Exception
     */
    function getParecerTramite()
    {

        $tramites = $this->getTramites();
        if (isset($tramites[0])) {
            return $tramites[0]->getParecer();
        }
        return null;
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws ORMException
     * @throws Exception
     */
    public function getParecer()
    {
        if (empty($this->parecer)) {
            return $this->getParecerTramite();
        }
        return $this->parecer;
    }

    /**
     * @throws Exception
     * @throws \Doctrine\ORM\ORMException
     * @throws ORMException
     */
    function getStatus()
    {
        if (empty($this->status)) {
            return $this->getStatusTramite();
        }
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function setParecer($parecer)
    {
        $this->parecer = $parecer;
    }

    /**
     * @param bool $zeros_esquerda
     * @return int|string
     */
    public function getNumero(bool $zeros_esquerda = false)
    {
        if ($zeros_esquerda) {
            return str_pad($this->id, 8, "0", STR_PAD_LEFT);
        }
        return $this->id;

    }

    public function getSetorOrigem()
    {
        return $this->setorOrigem;
    }

    public function setSetorOrigem($setorOrigem)
    {
        $this->setorOrigem = $setorOrigem;
    }

    public function getResponsavelOrigem()
    {
        return $this->responsavelOrigem;
    }

    public function setResponsavelOrigem($responsavelOrigem)
    {
        $this->responsavelOrigem = $responsavelOrigem;
    }

    public function getResponsavelDestino(): ?string
    {
        if (empty($this->responsavelDestino)) {
            return 'Responsável';
        }
        return $this->responsavelDestino;
    }

    public function setResponsavelDestino($responsavelDestino)
    {
        $this->responsavelDestino = $responsavelDestino;
    }

    public function getHorario()
    {
        return $this->horario;
    }

    public function setHorario($horario)
    {
        $this->horario = $horario;
    }

    public function getSetorDestino()
    {
        return $this->setorDestino;
    }

    public function setSetorDestino($setorDestino)
    {
        $this->setorDestino = $setorDestino;
    }

    function getData()
    {
        return $this->horario->format('d/m/Y');
    }

    function getHora()
    {
        return $this->horario->format('H:i:s');
    }

    /**
     * @throws Exception
     * @throws \Doctrine\ORM\ORMException
     * @throws ORMException
     */
    function getTramites(): ?array
    {
        return (new Tramite())->listarPorCampos(array('remessa' => $this));
    }

    /**
     * @throws Exception
     * @throws \Doctrine\ORM\ORMException
     * @throws ORMException
     */
    function getProcessos()
    {
        return $this->getDAO()->getProcessos($this);
    }

    function adicionaTramite(Tramite $tramite)
    {
        if (!$this->tramites->contains($tramite)) {
            $this->tramites->add($tramite);
        }
    }

}