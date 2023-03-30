<?php

$smarty->assign("page_title", 'Listagem de Categorias de Documentos');
$smarty->assign('page_icon', 'fa fa-th-list');
$smarty->assign('categorias', (new \App\Model\CategoriaDocumento())->listar());

