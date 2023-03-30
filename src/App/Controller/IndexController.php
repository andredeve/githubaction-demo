<?php

namespace App\Controller;

use Core\Controller\AppController;

/**
 * Classe Index
 * @version 1.0
 * @author Anderson Brandão <batistoti@gmail.com>
 * 
 * @copyright 2016 Lxtec Informática LTDA
 */
class IndexController extends AppController {
    
    public function __construct() {
        parent::__construct(get_class());
    }

    /**
     * @throws \SmartyException
     */
    public function index() {
        $_REQUEST['breadcrumb'] = array(array('link' => null, 'title' => 'Painel de Controle'));
        $this->load('Public', 'home');
    }

    public function error404() {
        parent::error404();
    }

    public static function getNoticacoes() {
        $notificacoes = array();
        return $notificacoes;
    }

}
