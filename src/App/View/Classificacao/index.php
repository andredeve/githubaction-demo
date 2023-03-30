<?php

$smarty->assign("page_title", 'Listagem de Classificações de Documentos');
$smarty->assign('page_icon', 'fa fa-th-list');
$smarty->assign('file_version', uniqid());
$smarty->assign('classificacoes', (new \App\Model\Classificacao())->listarPais());

