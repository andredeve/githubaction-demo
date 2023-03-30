<?php

use Core\Controller\AppController;

        
$app_config = AppController::getConfig();
$smarty->assign('contribuinteHabilitado', AppController::contribuinteHabilitado());
$smarty->assign('page_title', 'Login do Sistema');
$smarty->assign('app_url', APP_URL);
$smarty->assign('app_config', $app_config);


/* Key for Server 173.224.112.144 */
//$smarty->assign("data_site_key", "6LfsQEMUAAAAAFq-4X8TFpwg2m0gh1r0JF2JEg0F");
/* Key for locahost */
$smarty->assign("data_site_key", $app_config['data_site_key']);



