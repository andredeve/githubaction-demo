<?php /** @noinspection PhpUnused */

namespace App\Model;

use App\Controller\IndexController;
use Core\Exception\BusinessException;
use Core\Model\AppModel;
use Core\Util\Functions;
use Doctrine\ORM\ORMException;
use Exception;


// TODO: Excluir.
/**
 * @Entity
 * @HasLifecycleCallbacks
 * @Table(name="componente")
 */
class Componente extends AppModel 
{

    /**
     * @Id
     * @Column(type="integer",name="id")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    
    /**
     * @ManyToOne(targetEntity="Anexo")
     * @JoinColumn(name="anexo_id", referencedColumnName="id",onDelete="CASCADE")
     */
    private $anexo;
    
    /**
     * @ManyToOne(targetEntity="Processo", cascade={"persist"})
     * @JoinColumn(name="processo_id", referencedColumnName="id")
     */
    private $processo;
    
    /**
     * @ManyToOne(targetEntity="Tramite")
     * @JoinColumn(name="tramite_id", referencedColumnName="id", nullable=true)
     */
    private $tramite;
    
    /**
     * @Column(type="integer",name="ordem")
     */
    private $ordem;

    /**
    * @Column(type="integer",name="qntde_paginas")
    */
    private $qntdePaginas;
    

    function getId(): ?int {
        return $this->id;
    }

    /**
     * @return Anexo|null
     */
    function getAnexo() {
        return $this->anexo;
    }

    function getOrdem($contarCapa = false): int
    {
        return $contarCapa?$this->ordem+1: $this->ordem;
    }
    
    function getProcesso() {
        return $this->processo;
    }

    function setProcesso($processo) {
        $this->processo = $processo;
    }
     
    function getTramite() {
        return $this->tramite;
    }
    
    function getQntdePaginas() {
        return $this->qntdePaginas;
    }

    function setQntdePaginas($qntdePaginas) {
        $this->qntdePaginas = $qntdePaginas;
    }
    
    function setTramite($tramite) {
        $this->tramite = $tramite;
    }

    /**
     * @param mixed $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    function setAnexo($anexo) {
        $this->anexo = $anexo;
    }

    function setOrdem($ordem) {
        $this->ordem = $ordem;
    }
    
    public function isCarimbado(): bool
    {
        return file_exists($this->anexo->getArquivoOriginal());
    }

    /**
     * @throws ORMException
     * @throws \Doctrine\ORM\Exception\ORMException
     * @throws \Doctrine\DBAL\Exception
     * @throws BusinessException
     */
    public function ordenarComponentes(Processo $processo){
        $index = 1;
        foreach($processo->getComponentesOrdenadoPorData() as $c){
            $componente = new Componente();
            $componente->setProcesso($processo);
            $componente->setOrdem($index);
            $componente->setQntdePaginas(0);
            if($c instanceof Tramite){
                $componente->setTramite($c);
            }else if($c instanceof Anexo && is_file($c->getArquivoPath()) ){
                $componente->setAnexo($c);
                if(!$c->getId()){
                    continue;
                }
            }
            if(!$componente->getAnexo() && !$componente->getTramite()){
                //Anexo sem arquivos entra nessa condição
                continue;
            }
            $index++;
            if($componente->isUnique()){
                $componente->inserir();
            }
        }  
    }

    public function reordenarComponentes($novaListaDeComponentes){
        $contPaginas = 1;
        foreach($novaListaDeComponentes as $key => $componente){
            $newOrdem = $key+1;
            $oldOrdem = $componente->getOrdem();
            $componente->setOrdem($newOrdem);
                        
            if($newOrdem != $oldOrdem){
                if($componente->getAnexo()){
                    try {
                        Functions::adicionarPaginacaoECarimbo($componente->getAnexo()->getArquivo(false, true, true), IndexController::getClienteConfig(), $contPaginas);
                    } catch (Exception $e) {
                        Functions::escreverLogErro($e);
                    }
                }
            }
            $contPaginas+= $componente->getQntdePaginas();
            $componente->atualizar();
        }   
    }
    
    public function proximaIndexOrdem(Processo $processo): int
    {
        return !$processo->getComponentes()->isEmpty()? $processo->getComponentes()->last()->getOrdem()+1:1;
    }

    /**
     * @return bool
     * @throws ORMException
     */
    public function isUnique(): bool
    {
        $componentes = (new Componente())->listarPorCampos(array("anexo"=> $this->anexo, "processo" => $this->processo, "tramite" => $this->tramite));
        $verificaOrdem = (new Componente())->listarPorCampos(array( "processo" => $this->processo,  "ordem" => $this->ordem));
        return (count($componentes) == 0) && (count($verificaOrdem) == 0);
    }


    /**
     * @PrePersist
     * @throws Exception
     */
    public function alreadyExists()
    {
        if(!$this->anexo && !$this->tramite){
            throw new BusinessException("Não é possível cadastrar componente sem anexo e tramite.");
        }
        if(!$this->isUnique()){
            throw new BusinessException("Componente já foi cadastrado.");
        }
    }
}
