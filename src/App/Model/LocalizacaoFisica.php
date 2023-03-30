<?php /** @noinspection PhpUnused */

namespace App\Model;

use Core\Interfaces\EntityInterface;
use Core\Model\AppModel;

/**
 * @Entity
 * @Table(name="localizacao_fisica",indexes={@Index(name="exercicio_index", columns={"exercicio_documento"}),@Index(name="numero_index", columns={"numero_documento"})})
 * Class LocalizacaoFisica
 * @package App\Model
 */
class LocalizacaoFisica extends AppModel implements EntityInterface
{
    /**
     * @Id
     * @Column(type="integer",name="id")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Column(type="string",name="numero_documento",nullable=true)
     */
    private $numeroDocumento;
    /**
     * @Column(type="string",name="exercicio_documento",nullable=true)
     */
    private $exercicioDocumento;


    /**
     * @Column(type="date",name="data_documento",nullable=true)
     */
    private $dataDocumento;

    /**
     * @ManyToOne(targetEntity="Local")
     * @JoinColumn(name="local_id", referencedColumnName="id",nullable=true)
     */
    private $local;
    /**
     * @Column(type="string",name="ref_local",nullable=true)
     */
    private $refLocal;
    /**
     * @ManyToOne(targetEntity="TipoLocal")
     * @JoinColumn(name="tipo_local_id", referencedColumnName="id",nullable=true)
     */
    private $tipoLocal;
    /**
     * @Column(type="string",name="ref_tipo_local",nullable=true)
     */
    private $refTipoLocal;
    /**
     * @ManyToOne(targetEntity="SubTipoLocal")
     * @JoinColumn(name="subtipo_local_id", referencedColumnName="id",nullable=true)
     */
    private $subTipoLocal;
    /**
     * @Column(type="string",name="ref_subtipo_local",nullable=true)
     */
    private $refSubTipoLocal;

    /**
     * @Column(type="text",name="ementa",nullable=true)
     */
    private $ementa;

    /**
     * @Column(type="text",name="observacao",nullable=true)
     */
    private $observacao;

    /**
     * @ManyToOne(targetEntity="Usuario")
     * @JoinColumn(name="usuario_id", referencedColumnName="id")
     */
    private $usuario;

    /**
     * @ManyToOne(targetEntity="Usuario")
     * @JoinColumn(name="usuario_alteracao_id", referencedColumnName="id",nullable=true)
     */
    private $usuarioAlteracao;


    /**
     * @Column(type="date",name="data_cadastro")
     */
    private $dataCadastro;

    /**
     * @Column(type="datetime",name="ultima_alteracao",nullable=true)
     */
    private $ultimaAlteracao;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getDataCadastro()
    {
        return $this->dataCadastro;
    }

    public function setDataCadastro($data_cadastro)
    {
        $this->dataCadastro = $data_cadastro;
    }

    public function getUltimaAlteracao()
    {
        return $this->ultimaAlteracao;
    }

    public function setUltimaAlteracao($ultima_alteracao)
    {
        $this->ultimaAlteracao = $ultima_alteracao;
    }

    public function getNumeroDocumento()
    {
        if (empty($this->numeroDocumento)) {
            return $this->getProcesso()->getNumero();
        }
        return $this->numeroDocumento;
    }

    public function setNumeroDocumento($numeroDocumento)
    {
        $this->numeroDocumento = $numeroDocumento;
    }

    public function getProcesso()
    {
        $processo = (new Processo())->buscarPorCampos(array('localizacaoFisica' => $this));
        return $processo == null ? new Processo() : $processo;
    }

    public function getExercicioDocumento()
    {
        if (empty($this->exercicioDocumento)) {
            return $this->getProcesso()->getExercicio();
        }
        return $this->exercicioDocumento;
    }

    public function setExercicioDocumento($exercicioDocumento)
    {
        $this->exercicioDocumento = $exercicioDocumento;
    }

    public function getUsuarioAlteracao()
    {
        return $this->usuarioAlteracao;
    }

    public function setUsuarioAlteracao($usuarioAlteracao)
    {
        $this->usuarioAlteracao = $usuarioAlteracao;
    }

    public function getDataDocumento($formatar = false)
    {
        if (empty($this->dataDocumento)) {
            return $this->getProcesso()->getDataAbertura($formatar);
        }
        if ($formatar) {
            return $this->dataDocumento->format('d/m/Y');
        }
        return $this->dataDocumento;
    }

    public function setDataDocumento($dataDocumento)
    {
        $this->dataDocumento = $dataDocumento;
    }

    public function getLocal(): Local
    {
        if ($this->local == null) {
            return new Local();
        }
        return $this->local;
    }

    public function setLocal($local)
    {
        $this->local = $local;
    }

    public function getRefLocal()
    {
        return $this->refLocal;
    }

    public function setRefLocal($refLocal)
    {
        $this->refLocal = $refLocal;
    }

    public function getTipoLocal(): TipoLocal
    {
        if ($this->tipoLocal == null) {
            return new TipoLocal();
        }
        return $this->tipoLocal;
    }

    public function setTipoLocal($tipoLocal)
    {
        $this->tipoLocal = $tipoLocal;
    }

    public function getRefTipoLocal()
    {
        return $this->refTipoLocal;
    }

    public function setRefTipoLocal($refTipoLocal)
    {
        $this->refTipoLocal = $refTipoLocal;
    }

    public function getSubTipoLocal(): SubTipoLocal
    {
        if ($this->subTipoLocal == null) {
            return new SubTipoLocal();
        }
        return $this->subTipoLocal;
    }

    public function setSubTipoLocal($subTipoLocal)
    {
        $this->subTipoLocal = $subTipoLocal;
    }

    public function getRefSubTipoLocal()
    {
        return $this->refSubTipoLocal;
    }

    public function setRefSubTipoLocal($refSubTipoLocal)
    {
        $this->refSubTipoLocal = $refSubTipoLocal;
    }

    public function getEmenta()
    {
        if (empty($this->ementa)) {
            return $this->getProcesso()->getObjeto();
        }
        return $this->ementa;
    }

    public function setEmenta($ementa)
    {
        $this->ementa = $ementa;
    }

    public function getObservacao()
    {
        return $this->observacao;
    }

    public function setObservacao($observacao)
    {
        $this->observacao = $observacao;
    }

    public function getUsuario()
    {
        return $this->usuario;
    }

    public function setUsuario($usuario)
    {
        $this->usuario = $usuario;
    }
}