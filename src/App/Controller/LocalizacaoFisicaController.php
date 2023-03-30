<?php
/**
 * Created by PhpStorm.
 * User: ander
 * Date: 20/12/2018
 * Time: 11:00
 */

namespace App\Controller;


use App\Model\Local;
use App\Model\SubTipoLocal;
use App\Model\TipoLocal;
use Core\Controller\AppController;
use Core\Util\Functions;

class LocalizacaoFisicaController extends AppController
{
    function __construct()
    {
        parent::__construct(get_class());
        $this->breadcrumb = "Localização Física de Processos";
    }

    function inserir()
    {

        $_POST['usuario'] = UsuarioController::getUsuarioLogadoDoctrine();
        $this->setLocalizacao();
        return parent::inserir();
    }

    function atualizar()
    {
        $this->setLocalizacao();
        $_POST['usuarioAlteracao'] = UsuarioController::getUsuarioLogadoDoctrine();
        return parent::atualizar();
    }

    private function setLocalizacao()
    {
        $_POST['local'] = (new Local())->buscar($_POST['local_id']);
        $_POST['tipoLocal'] = (new TipoLocal())->buscar($_POST['tipolocal_id']);
        $_POST['subTipoLocal'] = (new SubTipoLocal())->buscar($_POST['subtipo_local_id']);
        $_POST['dataDocumento'] = (new \DateTime(Functions::converteDataParaMysql($_POST['dataDocumento'])));

    }

    public function index()
    {
        $_REQUEST['breadcrumb'] = array(array('link' => null, 'title' => $this->getBreadCrumbTitle()));
        $this->load($this->class_path);
    }

    /**
     * Método genérico de listagem de registros de um objeto/tabela
     */
    public function listar()
    {
        $_REQUEST['breadcrumb'] = array(array('link' => null, 'title' => $this->getBreadCrumbTitle()));
    }

}