<?php

use App\Model\Anexo;
use App\Model\Processo;

$smarty->assign("acao", "atualizar");
$smarty->assign('app_url', APP_URL);
$smarty->assign("componentes", $_REQUEST['componentes']);
$smarty->assign("componente", $_REQUEST['componente']);
