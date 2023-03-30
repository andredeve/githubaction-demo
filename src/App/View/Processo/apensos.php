<?php

use App\Model\Processo;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

include '../../../../bootstrap.php';
include_once '../../../../_config/smarty.config.php';
$processo = !empty($_POST['processo_id']) ? (new Processo())->buscar($_POST['processo_id']) : unserialize($_SESSION['processo']);
$smarty->template_dir = APP_PATH . '/src/App/View/Processo/Templates/';
$smarty->assign('app_url', APP_URL);
$apensos = new ArrayCollection();
if (isset($_POST['apensos'])) {
    foreach ($_POST['apensos'] as $apenso_id) {
        $apensos->add((new Processo())->buscar($apenso_id));
    }
} else if (!is_null($processo)) {
    foreach ($processo->getApensos() as $apenso) {
        if (!$apensos->contains($apenso)) {
            $apensos->add($apenso);
        }
    }       
}
$smarty->assign('resultado', $apensos->toArray());
$smarty->display('listar.tpl');

