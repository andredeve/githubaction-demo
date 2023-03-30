<?php /** @noinspection PhpUnused */

namespace App\Model;

use Core\Exception\BusinessException;
use Core\Model\AppModel;

/**
 * @Entity
 * @Table(name="status_processo")
 */
class StatusProcesso extends AppModel
{
    const ARQUIVADO = 1;
    const EM_ANDAMENTO = 2;
    const PENDENTE = 3;

    /**
     * @Id
     * @Column(type="integer",name="id")
     * @GeneratedValue(strategy="NONE")
     */
    private $id;

    /**
     * @Column(type="string",name="descricao",unique=true)
     */
    private $descricao;

    /**
     * @Column(type="string",name="cor")
     */
    private $cor;

    /**
     * @Column(type="boolean",name="is_status_arquivamento");
     */
    private $isArquivamento;

    /**
     * @Column(type="boolean",name="is_status_devolvido_origem");
     */
    private $isDevolvidoOrigem;

    /**
     * @Column(type="boolean", options={"default" : 1})
     */
    private $ativo;

    public function __construct()
    {
        $this->ativo = true;
    }

    function getId(): ?int
    {
        return $this->id;
    }

    function getDescricao()
    {
        return $this->descricao;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    function setDescricao($descricao)
    {
        $this->descricao = $descricao;
    }

    function getCor()
    {
        return $this->cor;
    }

    function setCor($cor)
    {
        $this->cor = $cor;
    }

    public function getAtivo(): bool
    {
        return $this->ativo;
    }

    public function setAtivo($ativo)
    {
        $this->ativo = $ativo;
    }

    public function getIsArquivamento()
    {
        return $this->isArquivamento;
    }

    public function setIsArquivamento($isArquivamento)
    {
        $this->isArquivamento = $isArquivamento;
    }

    function getIsDevolvidoOrigem() {
        return $this->isDevolvidoOrigem;
    }

    function setIsDevolvidoOrigem($isDevolvidoOrigem) {
        $this->isDevolvidoOrigem = $isDevolvidoOrigem;
    }

    private function getKernel(): array
    {
        return array(
            array('id' => 1, 'descricao' => "Arquivado", "cor" => "#ccc", 'isArquivamento' => true, 'isDevolvidoOrigem' => false),
            array('id' => 2, 'descricao' => "Em Andamento", "cor" => "#d9edf7", 'isArquivamento' => false, 'isDevolvidoOrigem' => false),
            array('id' => 3, 'descricao' => "Pendente", "cor" => "#fcf8e3", 'isArquivamento' => false, 'isDevolvidoOrigem' => false),
            array('id' => 4, 'descricao' => "Devolver a Origem", "cor" => "#fcf8e2", 'isArquivamento' => false, 'isDevolvidoOrigem' => true),
        );
    }

    /**
     * @throws BusinessException
     */
    function seed()
    {
        $result = $this->listar();
        if (count($result) == 0) {
            foreach ($this->getKernel() as $st) {
                $status = new StatusProcesso();
                $status->setId($st['id']);
                $status->setDescricao(mb_strtoupper($st['descricao']));
                $status->setCor($st['cor']);
                $status->setIsArquivamento($st['isArquivamento']);
                $status->setIsDevolvidoOrigem($st['isDevolvidoOrigem']);
                $status->inserir();
            }
        }
    }

    public function __toString()
    {
        return $this->descricao;
    }

    static function criarBotao(Tramite $tramite, Processo $processo): string
    {
        return "<button tramite_id='{$tramite->getId()}' processo='$processo' title='Clique para alterar status' type='button' style='background-color: {$tramite->getStatus()->getCor()}' class='btn btn-xs btn-alterar-status btn-block text-white'><small>{$tramite->getStatus()}</small></button>";
    }
}
