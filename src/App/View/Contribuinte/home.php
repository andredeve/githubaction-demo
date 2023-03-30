<?php
use App\Model\StatusProcesso;
$smarty->assign('page_title', 'Início');
$smarty->assign('page_icon', 'fa fa-home');
$smarty->assign('status_processo', (new StatusProcesso())->listarAtivos());