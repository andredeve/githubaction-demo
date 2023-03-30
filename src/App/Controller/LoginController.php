<?php

namespace App\Controller;

use App\Model\Usuario;
use Core\Controller\AppController;

/**
 * Classe Login
 * @version 1.0
 * @author Anderson Brandão <batistoti@gmail.com>
 * 
 * @copyright 2016 Lxtec Informática LTDA
 */
class LoginController extends AppController {

    public function __construct() {
        parent::__construct(get_class());
    }

    public function index() {
        $this->load('Public', 'login');
    }

    public function ativacao(){

        $tokenAtivacao = func_get_args()[0];

        if (!empty($tokenAtivacao)){
            $usuario = (new Usuario())->buscarPorTokenAtivacao($tokenAtivacao);

            if(empty($usuario)){
                return $this->error404();
            }
            $usuario->setAtivo(true);
            $usuario->atualizar();
            $this->route($this->class_path);
            return ;
        }

        return $this->error404();

    }

}
