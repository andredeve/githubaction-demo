<?php

namespace App\Controller;

use Core\Controller\AppController;

/**
 * Classe RelatorioControler
 * @version 1.0
 * @author Anderson Brandão Batistoti <anderson@lxtec.com.br>
 * @date   08/03/2018
 * @copyright (c) 2018, Lxtec Informática
 */
class RelatorioController extends AppController
{
    private $nomenclatura;

    function __construct()
    {
        $this->nomenclatura = IndexController::getParametosConfig()['nomenclatura'];
        parent::__construct(get_class());
    }

    function index()
    {
        $_REQUEST['breadcrumb'] = array(
            array('link' => null, 'title' => 'Relatórios'),
        );
        return $this->load($this->class_path, 'index');
    }

    function vencimentos()
    {
        $_REQUEST['breadcrumb'] = array(
            array('link' => 'relatorio', 'title' => 'Relatórios'),
            array('link' => null, 'title' => 'Vencimento de ' . $this->nomenclatura . 's')
        );
        return $this->load($this->class_path, 'vencimentos');
    }

    function vencimentosDocumento()
    {
        $_REQUEST['breadcrumb'] = array(
            array('link' => 'relatorio', 'title' => 'Relatórios'),
            array('link' => null, 'title' => 'Vencimento de Documento')
        );
        return $this->load($this->class_path, 'vencimentosDocumento');
    }

    function arquivados()
    {
        $_REQUEST['breadcrumb'] = array(
            array('link' => 'relatorio', 'title' => 'Relatórios'),
            array('link' => null, 'title' => $this->nomenclatura . 's Arquivados')
        );
        return $this->load($this->class_path, 'arquivados');
    }
    function naoRecebidos()
    {
        $_REQUEST['breadcrumb'] = array(
            array('link' => 'relatorio', 'title' => 'Relatórios'),
            array('link' => null, 'title' => $this->nomenclatura . 's não Recebidos')
        );
        return $this->load($this->class_path, 'naoRecebidos');
    }
    function foraFluxograma()
    {
        return $this->load($this->class_path, 'foraFluxograma');
    }
    function abertos()
    {
        $_REQUEST['breadcrumb'] = array(
            array('link' => 'relatorio', 'title' => 'Relatórios'),
            array('link' => null, 'title' => $this->nomenclatura . 's em Aberto')
        );
        return $this->load($this->class_path, 'abertos');
    }

    function anexos()
    {
        $_REQUEST['breadcrumb'] = array(
            array('link' => 'relatorio', 'title' => 'Relatórios'),
            array('link' => null, 'title' => 'Anexos por Período')
        );
        return $this->load($this->class_path, 'anexos');
    }

    function processos()
    {
        $_REQUEST['breadcrumb'] = array(
            array('link' => 'relatorio', 'title' => 'Relatórios'),
            array('link' => null, 'title' => $this->nomenclatura . 's por Período')
        );
        return $this->load($this->class_path, 'processos');
    }

    function movimentacao()
    {
        $_REQUEST['breadcrumb'] = array(
            array('link' => 'relatorio', 'title' => 'Relatórios'),
            array('link' => null, 'title' => 'Movimentação de' . $this->nomenclatura . 's')
        );
        return $this->load($this->class_path, 'movimentacao');
    }

    function tramites()
    {
        $_REQUEST['breadcrumb'] = array(
            array('link' => 'relatorio', 'title' => 'Relatórios'),
            array('link' => null, 'title' => 'Tramitação de' . $this->nomenclatura . 's')
        );
        return $this->load($this->class_path, 'tramites');
    }

    static function getRelatorios()
    {
        $nomenclatura = IndexController::getParametosConfig()['nomenclatura'];
        return array(
            array('link' => APP_URL . 'relatorio/anexos', 'descricao' => 'Anexos por período', 'icon' => ''),
            array('link' => APP_URL . 'relatorio/vencimentos', 'descricao' => 'Vencimento de ' . $nomenclatura . 's', 'icon' => ''),
            //   array('link' => APP_URL . 'relatorio/vencimentosDocumento', 'descricao' => 'Vencimento de Documentos', 'icon' => ''),
            array('link' => APP_URL . 'relatorio/movimentacao', 'descricao' => 'Movimentação de ' . $nomenclatura . 's', 'icon' => ''),
            array('link' => APP_URL . 'relatorio/tramites', 'descricao' => 'Tramitês de ' . $nomenclatura . 's por usuário', 'icon' => ''),
            array('link' => APP_URL . 'relatorio/abertos', 'descricao' => $nomenclatura . 's em aberto', 'icon' => ''),
            array('link' => APP_URL . 'relatorio/naoRecebidos', 'descricao' => $nomenclatura . 's não recebidos', 'icon' => ''),
            array('link' => APP_URL . 'relatorio/arquivados', 'descricao' => $nomenclatura . 's arquivados', 'icon' => ''),
            array('link' => APP_URL . 'relatorio/processos', 'descricao' => $nomenclatura . 's por período', 'icon' => ''),
            array('link' => APP_URL . 'relatorio/foraFluxograma', 'descricao' => $nomenclatura . 's corrigidos', 'icon' => '')
        );
    }

}
