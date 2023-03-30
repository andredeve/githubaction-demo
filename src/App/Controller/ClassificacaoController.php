<?php

namespace App\Controller;

use App\Model\Classificacao;
use App\Proxies\__CG__\App\Model\Usuario;
use Core\Controller\AppController;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Classe ClassificacaoController
 * @version 1.0
 * @author Anderson Brandão Batistoti <anderson@lxtec.com.br>
 * @date   19/11/2018
 * @copyright (c) 2018, Lxtec Informática
 */
class ClassificacaoController extends AppController {

    function __construct() {
        parent::__construct(get_class());
        $this->breadcrumb = "Classificação de Documento";
    }

    function inserir() {
        $this->setClassificacao();
        return parent::inserir();
    }

    function atualizar() {
        $this->setClassificacao();
        return parent::atualizar();
    }

    private function setClassificacao() {
        $_POST['classificacaoPai'] = (new Classificacao())->buscar('classificacao_pai_id');
        $_POST['usuariosLeitura'] = $this->getUsuarios('usuario_leitura_id');
        $_POST['usuariosColaboracao'] = $this->getUsuarios('usuario_colaboracao_id');
        $_POST['usuariosControleTotal'] = $this->getUsuarios('usuario_controle_total_id');
        $_POST['usuariosAdministradores'] = $this->getUsuarios('usuario_admin_id');
    }

    private function getUsuarios($variavel) {
        $usuarios = new ArrayCollection();
        if (isset($_POST[$variavel])) {
            foreach ($_POST[$variavel] as $usuario_id) {
                $usuarios->add((new Usuario())->buscar($usuario_id));
            }
        }
        return $usuarios;
    }

}
