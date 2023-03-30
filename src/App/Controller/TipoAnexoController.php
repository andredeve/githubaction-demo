<?php

namespace App\Controller;

use Core\Controller\AppController;

class TipoAnexoController extends AppController
{

    function __construct()
    {
        parent::__construct(get_class());
        $this->text_method = 'getDescricao';
        $this->breadcrumb = "Tipos de Anexo";
    }
    
    function inserir() {
        $this->setTipoAnexo();
        parent::inserir();
    }
    
    function atualizar() {
        $this->setTipoAnexo();
        parent::atualizar();
    }
    
    function setTipoAnexo()
    {
        $_POST['alteraVencimento'] = isset($_POST['alteraVencimento']);
        $_POST['ativo'] = isset($_POST['ativo']);
    }

}
