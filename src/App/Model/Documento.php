<?php /** @noinspection PhpUnused */

namespace App\Model;

use App\Model\Dao\DocumentoDao;
use Core\Model\AppModel;
use DateTime;
use DateTimeZone;
use Exception;

/**
 * @Entity
 * @Table(name="documento")
 */
class Documento extends AppModel
{
    /**
     * @Id
     * @Column(type="integer",name="id")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Column(type="string",name="numero",nullable=false)
     */
    private $numero;
    /**
     * @ManyToOne(targetEntity="CategoriaDocumento")
     * @JoinColumn(name="categoria_documento_id", referencedColumnName="id",nullable=false)
     */
    private $categoria;
    /**
     * @ManyToOne(targetEntity="Processo",inversedBy="documentos")
     * @JoinColumn(name="processo_id", referencedColumnName="id",nullable=false,onDelete="CASCADE")
     */
    private $processo;
    /**
     * @Column(type="string",name="exercicio",nullable=false)
     */
    private $exercicio;

    /**
     * @Column(type="date",name="data",nullable=false)
     */
    private $data;

    /**
     * @Column(type="date",name="vencimento",nullable=false)
     */
    private $vencimento;
    /**
     * @Column(type="text",name="observacaoes",nullable=true)
     */
    private $observacoes;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getNumero()
    {
        return $this->numero;
    }

    public function setNumero($numero)
    {
        $this->numero = $numero;
    }

    public function getCategoria(): CategoriaDocumento
    {
        if ($this->categoria == null) {
            return new CategoriaDocumento();
        }
        return $this->categoria;
    }

    public function setCategoria($categoria)
    {
        $this->categoria = $categoria;
    }

    public function getExercicio()
    {
        return $this->exercicio;
    }

    public function setExercicio($exercicio)
    {
        $this->exercicio = $exercicio;
    }

    public function getData($formatar = false)
    {
        if (!empty($this->data) && $formatar) {
            return $this->data->format('d/m/Y');
        }
        return $this->data;
    }

    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @throws Exception
     */
    function getDiasVencimento(): string
    {
        $timezone = new DateTimeZone('America/Campo_Grande');
        return (new DateTime(Date('Y-m-d'), $timezone))->diff($this->vencimento)->format('%r%a');
    }

    public function getVencimento($formatar = false)
    {
        if (!empty($this->vencimento) && $formatar) {
            return $this->vencimento->format('d/m/Y');
        }
        return $this->vencimento;
    }

    /**
     * @param mixed $vencimento
     */
    public function setVencimento($vencimento)
    {
        $this->vencimento = $vencimento;
    }

    /**
     * @return mixed
     */
    public function getObservacoes()
    {
        return $this->observacoes;
    }

    /**
     * @param mixed $observacoes
     */
    public function setObservacoes($observacoes)
    {
        $this->observacoes = $observacoes;
    }

    /**
     * @return mixed
     */
    public function getProcesso()
    {
        return $this->processo;
    }

    /**
     * @param mixed $processo
     */
    public function setProcesso($processo)
    {
        $this->processo = $processo;
    }

    /**
     * @param int $dias
     * @return mixed
     * @throws Exception
     */
    static function listarVencimentoProximos(int $dias = 15)
    {
        $dataAtual = new DateTime(Date('Y-m-d'));
        $dataFim = new DateTime(Date('Y-m-d'));
        $dataFim->add(new \DateInterval('P'.$dias.'D'));
        return (new DocumentoDao())->listarVencimentoProximos($dataAtual, $dataFim);
    }
}