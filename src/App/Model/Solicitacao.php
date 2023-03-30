<?php /** @noinspection PhpUnused */

/** @noinspection PhpPropertyOnlyWrittenInspection */

namespace App\Model;

use App\Model\Dao\SolicitacaoDao;
use Core\Model\AppModel;
use Core\Util\Functions;
use DateTime;
use Exception;
use App\Model\AnexoAlteracao;
use Oro\ORM\Query\AST\Platform\Functions\Mysql\Date;

/**
 * @Entity()
 * @Table(name = "solicitacao", indexes={ @Index(columns={ "solicitante", "anexo_anterior_id", "anexo_novo_id" }) })
 */
class Solicitacao extends AppModel
{
    /**
     * @Id
     * @Column(type="bigint")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ManyToOne(targetEntity="Usuario")
     * @JoinColumn(name="solicitante", referencedColumnName="id", nullable=false)
     */
    private $solicitante;

    /**
     * @Column(type="string", name="motivo", nullable=true)
     */
    private $motivo;

    /**
     * @type Anexo
     * @ManyToOne(targetEntity="Anexo", cascade={"persist","remove"})
     * @JoinColumn(name="anexo_anterior_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    private $anexoAnterior;

    /**
     * @ManyToOne(targetEntity="AnexoAlteracao", cascade={"persist","remove"})
     * @JoinColumn(name="anexo_novo_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    private $anexoNovo;

    /**
     * @Column(type="string", name="status", columnDefinition="ENUM('Pendente', 'Aprovado', 'Recusado')", options={"default": "Pendente"}, nullable=false)
     * @type string
     */
    private $status;

    /**
     * @Column(type="string", name="tipo", columnDefinition="ENUM('Edição', 'Exclusão')", options={"default": "Exclusão"}, nullable=false)
     * @type string
     */
    private $tipo;

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
        $this->status = "Pendente";
        $this->tipo = "Exclusão";
        $this->modificadoEm = new DateTime();
    }

    public static function getQtdeListagem(): string
    {
        /**
         * @var SolicitacaoDao $dao
         */
        $dao = (new Solicitacao())->getDAO();
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

    public function getSolicitante(): Usuario
    {
        return $this->solicitante;
    }

    public function getMotivo(): string
    {
        return $this->motivo;
    }

    public function setSolicitante(Usuario $solicitante)
    {
        $this->solicitante = $solicitante;
    }

    public function setMotivo(?string $motivo)
    {
        $this->motivo = $motivo;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status)
    {
        $this->status = $status;
    }

    public function getData(): DateTime
    {
        return $this->data;
    }

    public function setData(DateTime $data)
    {
        $this->data = $data;
    }

    public function getTipo(): string
    {
        return $this->tipo;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function setTipo(string $tipo): void
    {
        $this->tipo = $tipo;
    }

    public function getAnexoAnterior(): ?Anexo
    {
        return $this->anexoAnterior;
    }

    public function setAnexoAnterior(Anexo $anexoAnterior)
    {
        $this->anexoAnterior = $anexoAnterior;
    }

    public function getAnexoNovo(): ?AnexoAlteracao
    {
        return $this->anexoNovo;
    }

    /**
     * @param AnexoAlteracao $anexoNovo
     */
    public function setAnexoNovo(AnexoAlteracao $anexoNovo)
    {
        $this->anexoNovo = $anexoNovo;
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
         * @var SolicitacaoDao $dao
         */
        $dao = $this->getDAO();
        $dao->aprovar($this);
    }

    public function __serialize(): array
    {
        return [
          "id" => $this->id,
          "solicitante" => is_null($this->solicitante) ? "" : $this->solicitante->getId(),
          "motivo" => $this->motivo,
          "anexo_anterior" => is_null($this->anexoAnterior) ? "" : $this->anexoAnterior->getId(),
          "anexo_novo" => is_null($this->anexoNovo) ? "" : $this->anexoNovo->getId(),
          "data" => Functions::formatarData($this->data),
          "tipo" => $this->tipo,
          "status" => $this->status,
        ];
    }

    function jsonSerialize()
    {
        return json_encode($this->__serialize());
    }


}