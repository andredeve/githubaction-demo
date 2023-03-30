<?php /** @noinspection PhpUnused */

namespace App\Model;

use Core\Model\AppModel;

/**
 * @Entity
 * @Table(name="endereco")
 */
class Endereco extends AppModel
{

    /**
     * @Id
     * @Column(type="integer",name="id")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Column(type="string",name="cep",length=9,nullable=true)
     */
    private $cep;

    /**
     * @Column(type="string",name="rua",nullable=true)
     */
    private $rua;

    /**
     * @Column(type="integer",name="numero",length=4,nullable=true)
     */
    private $numero;

    /**
     * @Column(type="string",name="bairro",nullable=true)
     */
    private $bairro;
    
    /** @Column(type="integer",name="codigo_nea",nullable=true) */
    private $codigoNea;
    
    /**
     * @ManyToOne(targetEntity="Cidade")
     * @JoinColumn(name="cidade_id", referencedColumnName="id",nullable=true)
     */
    private $cidade;

    /**
     * @Column(type="text",name="complemento",nullable=true)
     */
    private $complemento;

    function getId(): ?int
    {
        return $this->id;
    }

    function getCep()
    {
        return $this->cep;
    }

    function getRua()
    {
        return $this->rua;
    }

    function getNumero(): ?int
    {
        if ($this->numero != 0) {
            return $this->numero;
        }
        return null;
    }

    function getBairro()
    {
        return $this->bairro;
    }

    function getCidade()
    {
        return $this->cidade;
    }

    function getEstado()
    {
        if ($this->cidade != null) {
            return $this->getCidade()->getEstado();
        }
        return null;
    }
    
    function getCodigoNea() {
        return $this->codigoNea;
    }

    function setCodigoNea($codigoNea) {
        $this->codigoNea = $codigoNea;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    function setCep($cep)
    {
        $this->cep = $cep;
    }

    function setRua($rua)
    {
        $this->rua = $rua;
    }

    function setNumero($numero)
    {
        $this->numero = $numero;
    }

    function setBairro($bairro)
    {
        $this->bairro = $bairro;
    }

    function setCidade($cidade)
    {
        $this->cidade = $cidade;
    }

    function getComplemento()
    {
        return $this->complemento;
    }

    function setComplemento($complemento)
    {
        $this->complemento = $complemento;
    }

    public function __toString()
    {
        $uf = $this->getEstado() != null ? $this->getEstado()->getUf() : null;
        if (!empty($this->rua) && !empty($this->cidade) && !empty($uf)) {
            return $this->rua . ", " . $this->numero . ". " . $this->cidade . " " . $uf;
        }
        return "";
    }

}
