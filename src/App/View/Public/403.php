<?php

$smarty->assign('page_title', 'Acesso proibido');
$smarty->assign('page_icon', 'fa fa-exclamation-circle');
http_response_code(403);