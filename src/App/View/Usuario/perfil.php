<?php

use App\Controller\UsuarioController;

$smarty->assign('page_title', 'Perfil');
$smarty->assign('page_description', 'altere suas informações pessoais');
$smarty->assign('page_icon', 'fa fa-info-circle');
$smarty->assign("usuario", UsuarioController::getUsuarioLogadoDoctrine());
/*$temas = array(array('value' => 'default', 'description' => 'Padrão'));
$dir = APP_PATH . '/lib/themes/';
$open = opendir($dir);
while ($arquivo = readdir($open)) {
    if ($arquivo != '.' && $arquivo != '..') {
        if (is_dir($dir . $arquivo)) {
            $tema['value'] = $arquivo;
            $tema['description'] = ucfirst($arquivo);
            $temas[] = $tema;
        }
    }
}
$smarty->assign('temas', $temas);*/
