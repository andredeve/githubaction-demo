<?php
/**
 * Created by PhpStorm.
 * User: ander
 * Date: 11/12/2018
 * Time: 16:05
 */

namespace App\Controller;


use App\Model\Anexo;
use App\Model\Tramite;
use Core\Controller\AppController;
use Core\Exception\BusinessException;
use Core\Exception\SecurityException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\TransactionRequiredException;

class DocumentoRequeridoController extends AppController
{
    function __construct()
    {
        parent::__construct(get_class());
//        $this->breadcrumb = "Documento Requerido";
    }
    
    function inserir() {
        
        $this->setDocumentoRequerido();
        parent::inserir();
           
    }
    
    function atualizar(){
        
        $this->setDocumentoRequerido();
        parent::atualizar();
            
            
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     * @throws SecurityException
     * @throws TransactionRequiredException
     * @throws BusinessException
     */
    public function setDocumentoRequerido(): void
    {
        $anexo = (new Anexo())->buscar($_POST['anexo_id']);
        if($anexo->getProcesso()->getIsExterno()){
            $anexo->setArquivo(null);
            $anexo->atualizar(true, false, "Adicionado como documento requerido.");
        }
        $_POST['anexo'] = $anexo;
        $_POST['tramiteCadastro'] = (new Tramite())->buscar($_POST['tramite_cadastro_id']);
        $_POST['isObrigatorio'] = isset($_POST['isObrigatorio']);
        $_POST['isAssinaturaObrigatoria'] = isset($_POST['isAssinaturaObrigatoria']);
        $_POST['usuario'] = UsuarioController::getUsuarioLogadoDoctrine();
    }
}