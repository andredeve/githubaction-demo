<?php

namespace App\Controller;

use Core\Controller\AppController;
use Core\Enum\TipoMensagem;

/**
 * Classe Contribuinte
 * @version 1.7
 * @author Bruno Pereira <brunno.pereira7@gmail.com>
 * 
 * @copyright 2022 Lxtec Informática LTDA
 */
class ContribuinteController extends ProcessoController {
    
    public function __construct() {
        parent::__construct(get_class());
    }

    public function index() {
        $this->load('Public', 'home');
    }

    public function signup()
    {
        $this->load('Interessado', 'cadastrar',true, false,  null, true);
    }

    public function home() {
        if(UsuarioController::isLogado()){
            $this->load('Contribuinte', 'home');
        }else{
            self::setMessage(TipoMensagem::ERROR, 'Login não encontrado.');
            $this->route('Login');
        }
    }
}
