<?php
/**
 * Created by PhpStorm.
 * User: ander
 * Date: 12/12/2018
 * Time: 10:49
 */

include '../../../../bootstrap.php';
echo json_encode(
    array(
        'qtde_processos_enviados' => \App\Model\Processo::getQtdeListagem('enviados'),
        'qtde_processos_receber' => \App\Model\Processo::getQtdeListagem('receber'),
        'qtde_processos_abertos' => \App\Model\Processo::getQtdeListagem('abertos'),
        'qtde_processos_arquivados' => \App\Model\Processo::getQtdeListagem('arquivados'),
        'qtde_processos_vencidos' => \App\Model\Processo::getQtdeListagem('vencidos'),
        'qtde_processos_contribuintes' => \App\Model\Processo::getQtdeListagem('contribuintes'),
    )
);