<?php
$model = new \App\Model\ModeloDocumento();
$smarty->assign("page_title", 'Relação de Modelo de Documentos');
$smarty->assign('page_icon', 'fa fa-th-list');
$smarty->assign('modelos', $model->listar());