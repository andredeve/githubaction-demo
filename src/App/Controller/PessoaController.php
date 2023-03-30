<?php

namespace App\Controller;

use App\Model\Pessoa;
use Core\Controller\AppController;

/**
 * @author Bruno Pereira
 */
class PessoaController extends AppController
{
    public function __construct()
    {
        parent::__construct(get_class());
    }

    public function setPessoa() :void
    {      
        if(empty($_POST['pessoa'])){
            $pessoa = new Pessoa();
            
            if(!empty($_POST['pessoa_id'])){
                $res = $pessoa->buscar($_POST['pessoa_id']);
                if(!empty($res)){
                    $pessoa = $res;
                }
            }
            $_POST['pessoa'] = $this->getValues($pessoa);

            if (empty($_POST['cpf'])){
                $_POST['pessoa']->setCpf(null);
            }
            if (empty($_POST['cnpj'])){
                $_POST['pessoa']->setCnpj(null);
            }
            
        }
        
    }

}
