<?php /** @noinspection PhpUnused */

namespace App\Model;

use Core\Interfaces\EntityInterface;
use Core\Model\AppModel;
use Core\Util\Functions;
use function mb_strtoupper;

/**
 * @Entity
 * @Table(name="tipo_anexo")
 */
class TipoAnexo extends AppModel implements EntityInterface {

    /**
     * @Id
     * @Column(type="integer",name="id")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /** @Column(type="integer",name="codigo_fiorilli",nullable=true) */
    private $codigoFiorilli;

    /**
     * @Column(type="string",name="descricao",unique=true)
     */
    private $descricao;
    
    /**
     * @Column(type="boolean",name="altera_vencimento_processo",nullable=true)
     */
    private $alteraVencimento;
    
    /**
     * @Column(type="date",name="data_cadastro")
     */
    private $dataCadastro;

    /**
     * @Column(type="datetime",name="ultima_alteracao",nullable=true)
     */
    private $ultimaAlteracao;

    /**
     * @type bool
     * @Column(type="boolean", name="ativo", options={"default": 1})
     */
    private $ativo;

    public function __construct()
    {
        $this->ativo = true;
        $this->alteraVencimento = false;
    }


    function getId(): ?int {
        return $this->id;
    }

    function getDescricao($normalize = false): string
    {
        if ($normalize) {
            return Functions::sanitizeString($this->descricao);
        }
        return (string) mb_strtoupper($this->descricao, "UTF-8");
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    function getCodigoFiorilli() {
        return $this->codigoFiorilli;
    }

    function setCodigoFiorilli($codigoFiorilli) {
        $this->codigoFiorilli = $codigoFiorilli;
    }

    function setDescricao($descricao) {
        $this->descricao = $descricao;
    }

    function getDataCadastro() {
        return $this->dataCadastro;
    }

    function getUltimaAlteracao() {
        return $this->ultimaAlteracao;
    }

    function setDataCadastro($data_cadastro) {
        $this->dataCadastro = $data_cadastro;
    }

    function setUltimaAlteracao($ultima_alteracao) {
        $this->ultimaAlteracao = $ultima_alteracao;
    }
    
    function getAlteraVencimento() {
        return $this->alteraVencimento;
    }

    function setAlteraVencimento($alteraVencimento) {
        $this->alteraVencimento = $alteraVencimento;
    }

    public function getAtivo(): bool
    {
        return $this->ativo;
    }

    public function setAtivo($ativo): void
    {
        $this->ativo = $ativo;
    }
        
    function __toString() {
        return $this->getDescricao();
    }
}
