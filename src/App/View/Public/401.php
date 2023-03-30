<?php

$smarty->assign('page_title', 'Acesso negado');
$smarty->assign('page_icon', 'fa fa-exclamation-circle');
http_response_code(401);