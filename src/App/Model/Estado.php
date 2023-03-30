<?php /** @noinspection PhpUnused */

namespace App\Model;

use App\Model\Dao\EstadoDao;
use Core\Model\AppModel;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\Exception\ORMException;

/**
 * @Entity
 * @Table(name="estado")
 */
class Estado extends AppModel {

    /**
     * @Id
     * @Column(type="integer",name="id")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Column(type="string",name="nome", length=80)
     */
    private $nome;

    /**
     * @Column(type="string",name="uf", length=2)
     */
    private $uf;

    function getId(): ?int {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    function getNome() {
        return utf8_encode($this->nome);
    }

    function getUf() {
        return $this->uf;
    }

    function setNome($nome) {
        $this->nome = $nome;
    }

    function setUf($uf) {
        $this->uf = $uf;
    }

    /**
     * @throws ORMException
     * @throws Exception
     * @throws \Doctrine\ORM\ORMException
     */
    function getCidades(): ?array
    {
        return $this->getDAO()->getCidades($this);
    }

    public function __toString() {
        return $this->getNome();
    }

    /**
     * @throws Exception
     * @throws \Doctrine\ORM\ORMException
     * @throws ORMException
     */
    function buscarPorUF($uf) {
        return $this->getDAO()->listarPorCampos(['uf' => $uf])[0];
    }
}