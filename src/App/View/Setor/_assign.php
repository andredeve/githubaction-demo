<?php
/**
 * Created by PhpStorm.
 * User: ander
 * Date: 17/12/2018
 * Time: 15:41
 */

use App\Model\Setor;
use Core\Controller\AppController;

$smarty->assign("setores", (new Setor())->listarSetoresPai());
$smarty->assign('file_version', AppController::getConfig('file_version'));