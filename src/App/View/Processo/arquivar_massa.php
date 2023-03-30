<?php
include '../../../../bootstrap.php';
include_once '../../../../_config/smarty.config.php';

use App\Controller\UsuarioController;
use App\Enum\TipoUsuario;
use App\Model\Grupo;
use App\Model\Local;
use App\Model\SubTipoLocal;
use App\Model\TipoLocal;
use App\Model\Tramite;
use Core\Controller\AppController;
use Doctrine\Common\Collections\ArrayCollection;

$usuario_logado = UsuarioController::getUsuarioLogadoDoctrine();
$grupo = $usuario_logado->getGrupo();
if ($grupo->getArquivar() || $usuario_logado->getTipo() == TipoUsuario::MASTER) {
    $smarty->template_dir = APP_PATH . '/src/App/View/Processo/Templates/';
    $processos = new ArrayCollection();
    foreach ($_POST['tramite_id_sel'] as $tramite_id) {
        $tramite = (new Tramite())->buscar($tramite_id);
        $processo = $tramite->getProcesso();
        if (!$processos->contains($processo)) {
            $processos->add($processo);
        }
    }
    $smarty->assign("locais", (new Local())->listar());
    $smarty->assign("tipos_local", (new TipoLocal())->listar());
    $smarty->assign("subtipos_local", (new SubTipoLocal())->listar());
    $smarty->assign('nomenclatura', AppController::getParametosConfig('nomenclatura'));
    $smarty->assign('processos', $processos);
    $smarty->assign('app_url', APP_URL);
    $smarty->display('arquivar_massa.tpl');
} else {
    echo Grupo::createNoPermissisionError();
}