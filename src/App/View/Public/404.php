<?php
if (is_null($smarty->getTemplateVars('page_title'))) {
    $smarty->assign('page_title', 'Página não encontrada');
}
if (is_null($smarty->getTemplateVars('page_title'))) {
    $smarty->assign('page_icon', 'fa fa-exclamation');
}if (is_null($smarty->getTemplateVars('page_content'))) {
    $smarty->assign('page_content', 'A página que você buscou não foi encontrada.');
}
http_response_code(404);