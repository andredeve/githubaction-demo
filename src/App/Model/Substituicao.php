<?php /** @noinspection PhpUnused */

/** @noinspection PhpPropertyOnlyWrittenInspection */

namespace App\Model;

use App\Model\Dao\SubstituicaoDao;
use Core\Model\AppModel;
use Core\Util\Functions;
use DateTime;
use Exception;
use App\Model\AnexoAlteracao;
use Oro\ORM\Query\AST\Platform\Functions\Mysql\Date;

/**
 * @Entity()
 * @Table(name = "substituicao", indexes={ @Index(columns={ "responsavel", "anexo_anterior_id", "anexo_id" }) })
 */
class Substituicao extends AppModel
{
    /**
     * @Id
     * @Column(type="bigint")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ManyToOne(targetEntity="Usuario")
     * @JoinColumn(name="responsavel", referencedColumnName="id", nullable=false)
     */
    private $responsavel;

    /**
     * @Column(type="string", name="motivo", nullable=true)
     */
    private $motivo;

    /**
     * @type Anexo
     * @ManyToOne(targetEntity="Anexo", cascade={"persist","remove"})
     * @JoinColumn(name="anexo_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    private $anexo;

    /**
     * @ManyToOne(targetEntity="AnexoSubstituicao", cascade={"persist","remove"})
     * @JoinColumn(name="anexo_anterior_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    private $anexoAnterior;

    /**
     * @Column(type="datetime", name="data", nullable=false, options={"default": "CURRENT_TIMESTAMP"})
     * @type DateTime
     */
    private $data;

    /**
     * @Column(type="datetime", name="modificado_em", nullable=false, options={"default": "CURRENT_TIMESTAMP"})
     * @type DateTime
     */
    private $modificadoEm;

    public function __construct()
    {
        $this->data = new DateTime();
        $this->modificadoEm = new DateTime();
    }

    public static function getQtdeListagem(): string
    {
        /**
         * @var SubstituicaoDao $dao
         */
        $dao = (new Substituicao())->getDAO();
        try {
            $result = $dao->buscarQuantidade();
        } catch (Exception $e) {
            Functions::escreverLogErro($e);
            return "?";
        }
        return !empty($result) ? strval($result) : "0";
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getResponsavel(): Usuario
    {
        return $this->responsavel;
    }

    public function getMotivo()
    {
        return $this->motivo;
    }

    public function setResponsavel(Usuario $responsavel)
    {
        $this->responsavel = $responsavel;
    }

    public function setMotivo(?string $motivo)
    {
        $this->motivo = $motivo;
    }

    public function getData(): DateTime
    {
        return $this->data;
    }

    public function setData(DateTime $data)
    {
        $this->data = $data;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getAnexoAnterior(): ?AnexoSubstituicao
    {
        return $this->anexoAnterior;
    }

    public function setAnexoAnterior(AnexoSubstituicao $anexoAnterior)
    {
        $this->anexoAnterior = $anexoAnterior;
    }

    public function getAnexo(): ?Anexo
    {
        return $this->anexo;
    }

    /**
     * @param Anexo $anexo
     */
    public function setAnexo(Anexo $anexo)
    {
        $this->anexo = $anexo;
    }

    /**
     * @return DateTime
     */
    public function getModificadoEm(): DateTime
    {
        return $this->modificadoEm;
    }

    /**
     * @param DateTime $modificadoEm
     */
    public function setModificadoEm(DateTime $modificadoEm): void
    {
        $this->modificadoEm = $modificadoEm;
    }

    public function aprovar() {
        /**
         * @var SubstituicaoDao $dao
         */
        $dao = $this->getDAO();
        $dao->aprovar($this);
    }

    public function buscarSubstituicoesAnexo(int $anexo_id){
        return $this->getDAO()->buscarSubstituicoesAnexo($anexo_id);
    }
    
    public function __serialize(): array
    {
        return [
          "id" => $this->id,
          "responsavel" => is_null($this->responsavel) ? "" : $this->responsavel->getId(),
          "motivo" => $this->motivo,
          "anexo_anterior" => is_null($this->anexoAnterior) ? "" : $this->anexoAnterior->getId(),
          "anexo_novo" => is_null($this->anexo) ? "" : $this->anexo->getId(),
          "data" => Functions::formatarData($this->data)
        ];
    }

    function jsonSerialize()
    {
        return json_encode($this->__serialize());
    }


}