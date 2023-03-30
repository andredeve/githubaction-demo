<?php

namespace App\Controller;

use \App\Model\Anexo;
use \App\Model\Processo;
use \App\Model\Componente;
use App\Model\Tramite;
use Core\Controller\AppController;
use Core\Enum\TipoMensagem;
use Core\Util\Functions;
/**
 * Description of Componente
 *
 * @author victorcanario
 */
class ComponenteController extends AppController {
    const POSICAO_ANTES = 'antes';
    const POSICAO_DEPOIS = 'depois';
    
    public function __construct() {
         parent::__construct(get_class());
    }
    
    public function reordenar(){
        $componente = new Componente();
        $componente = $componente->buscar($_POST['componente_id']);
        $_REQUEST['componentes'] = $componente->getProcesso()->getComponentes();
        $_REQUEST['componente'] = $componente;        
        $this->loadSemTemplate($this->class_path, "reordenar");
    }
    static function adicionarPaginacaoCarimboOld(Processo $processo, $arquivoComCaminhoCompleto, ?Tramite $tramite = null){
        //Página atual apartir dos anteriores
        $componentes = $processo->getComponentes(!is_null($tramite));
        $qtdePaginas = 1;//Começa do 1 pq a capa é o 1
        
        $componenteTramite = null;
        if(!empty($tramite)){
            $componenteTramite = new Componente();
            $componenteTramite = $componenteTramite->buscarPorCampos(array("tramite" => $tramite));
        }
        
        foreach($componentes as $componente){
            if(empty($componente->getQntdePaginas()) ){
                $qtdePaginasPdf = 0;
                if($componente->getAnexo() ){
                    $anexoAux = $componente->getAnexo();
                    if(!empty($anexoAux->getQtdePaginas()) && $arquivoComCaminhoCompleto != $anexoAux->getArquivo(false, true, true)){
                        $qtdePaginasPdf = $anexoAux->getQtdePaginas();
                    }else if ($arquivoComCaminhoCompleto != $anexoAux->getArquivo(false, true, true)){
                        $qtdePaginasPdf = Functions::getQntdePaginasPDF($anexoAux->getArquivo(false, true, true));
                    }
                    if(!$componente->isCarimbado() && $arquivoComCaminhoCompleto != $anexoAux->getArquivo(false, true, true) ){
                        $anexoAuxCaminho = $anexoAux->getArquivo(false, true, true);
                        Functions::adicionarPaginacaoECarimbo($anexoAuxCaminho, IndexController::getClienteConfig(), $qtdePaginas);
                    }
                }else if($componente->getTramite() && ($tramite == null || $componente->getTramite()->getId() != $tramite->getId() )  ){
                    $tramiteAux = $componente->getTramite();
                    $gerou = $tramiteAux->gerarFormularioEletronico();
                    $qtdePaginasPdf = Functions::getQntdePaginasPDF($processo->getAnexosPath() . $tramiteAux->getNomeFormularioEletronico());
                }
                $componente->setQntdePaginas($qtdePaginasPdf);
                $componente->atualizar();
                
                if($componenteTramite && $componente->getOrdem() < $componenteTramite->getOrdem()){
                    $qtdePaginas += $qtdePaginasPdf;
                }else if(!$componenteTramite){
                    $qtdePaginas += $qtdePaginasPdf;
                }
            }else{
                if($componenteTramite && $componente->getOrdem() < $componenteTramite->getOrdem()){
                    $qtdePaginas += $componente->getQntdePaginas();
                }else if(!$componenteTramite){
                    $qtdePaginas += $componente->getQntdePaginas();
                }
            }
        }
        Functions::adicionarPaginacaoECarimbo($arquivoComCaminhoCompleto, IndexController::getClienteConfig(), $qtdePaginas);
    }

    public function reordenarComponentesPorFileGetContents(){
        $processo_id = $_GET['processo_id'];
        $processo = new Processo();
        $processo = $processo->buscar($processo_id);
        $componente = new Componente();
        $componente->reordenarComponentes($processo->getComponentes());
    }
    
    public function inserirPorFileGetContents(){
        $anexo_id = $_GET['anexo_id'];
        $anexo = new Anexo();
        $anexo = $anexo->buscar($anexo_id);
        ob_start();
        
        self::inserirComponente($anexo->getProcesso(), $anexo); 
    }
    
    public static function inserirComponente(Processo $processo, Anexo $anexo = null, Tramite $tramite =null){
        if($tramite && !$tramite->gerarFormularioEletronico()){
            //Tramite sem formulario não gera documento, por tanto não preciso contar 
            return;
        }
        $componente = new Componente();
        $componente->setProcesso($processo);
        $componente->setAnexo($anexo);
        $componente->setTramite($tramite);
        $componente->setOrdem(1);
        $componente->setQntdePaginas(0);
        if($componente->isUnique()){
            $componente->inserir();
        }
    }
    
    public function atualizar() {
        $componente = new Componente();
        $componenteReferencia = $componente->buscar($_POST['componente_referencia_id']);
        //Componente que está tendo a posição alterada
        $componenteAlterada = $componente->buscar($_POST['componente_id']);
        $processo = $componenteAlterada->getProcesso();
        $posicao = $_POST['posicionar_anexo'];
        $newComponente = array();
        if(self::POSICAO_ANTES == $posicao){
            foreach($processo->getComponentes() as $o){
                if($o->getId() == $componenteReferencia->getId()){
                    $newComponente[] = $componenteAlterada;
                    $newComponente[] = $o;
                }else if($o->getId() != $componenteAlterada->getId()){
                    $newComponente[] = $o;
                }                
            }
        }else if(self::POSICAO_DEPOIS == $posicao){
            foreach($processo->getComponentes() as $o){
                if($o->getId() != $componenteAlterada->getId() ){
                    //Se componente não for a q que está sendo alterada
                    $newComponente[] = $o;
                }                
                if($o->getId() == $componenteReferencia->getId()){
                    $newComponente[] = $componenteAlterada;
                }                           
            }
        }
        $componente->reordenarComponentes($newComponente);
        
        self::setMessage(TipoMensagem::SUCCESS, "Ordem atualizada com sucesso.", null, true);
        $this->route("Processo", "visualizarDigital", array($processo->getId()));
    }
    
    
}
