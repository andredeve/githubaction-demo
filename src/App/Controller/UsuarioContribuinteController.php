<?php

namespace App\Controller;

use App\Enum\TipoLog;
use App\Enum\TipoUsuario;
use App\Model\Log;
use App\Model\Usuario;
use Core\Enum\TipoMensagem;
use Core\Exception\AppException;
use DateTime;
use Doctrine\DBAL\DBALException;
use Exception;

class UsuarioContribuinteController extends UsuarioController{
    public function editar()
    {
        $usuario_logado = self::getUsuarioLogado();
        if ($usuario_logado != null) {
            if ($usuario_logado->getTipo() != TipoUsuario::USUARIO && $usuario_logado->getTipo() != TipoUsuario::VISITANTE) {
                $usuario = new $this->classe;
                $args = func_get_args();
                $usuario = $usuario->buscar($args[1]);
                if ($usuario != null) {
                    $_REQUEST['objeto'] = $usuario;
                    $_REQUEST['breadcrumb'] = array(
                        array('link' => "UsuarioContribuinte", 'title' => IndexController::getParametosConfig()["contribuinte"]),
                        array('link' => null, 'title' => 'Editar')
                    );
                    return $this->load("UsuarioContribuinte", 'editar');
                }
            }
            return $this->error404();
        }
        return $this->route('login');
    }
    public function index(){
        if (self::getUsuarioLogado()->getTipo() != TipoUsuario::USUARIO && self::getUsuarioLogado()->getTipo() != TipoUsuario::VISITANTE) {
            $this->listarContribuintes(func_get_args());            
            $this->load("UsuarioContribuinte");
        } else {
            $this->error403();
        }
    }

    public function listarContribuintes(){
        $usuario = new $this->classe();
        $_REQUEST['breadcrumb'] = array(array('link' => null, 'title' => IndexController::getParametosConfig()["contribuinte"]));
        $_REQUEST['registros'] = $usuario->listarPorCampos(array("tipo" => TipoUsuario::INTERESSADO));
    }

    public function atualizar()
    {
        $isAjax = isset($_REQUEST['ajax']) ? true : false;
        $usuario_id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
        try {
            $_POST['ultimaAlteracao'] = new DateTime();
            $usuario = (new Usuario())->buscar($usuario_id);
            $old = clone $usuario;
            $this->setUsuario($usuario);
            $this->getValues($usuario);
            $new = $usuario;
//            $usuario->atualizar();
            $usuario->postPersistAndUpdateSetor($usuario);
            Log::registrarLog(TipoLog::ACTION_UPDATE, $usuario->getTableName(), "Registro atualizado", null, $old->imprimir(), $new->imprimir());
            self::setMessage(TipoMensagem::SUCCESS, 'Registro atualizado com sucesso!', null, $isAjax);
        } catch (DBALException $e) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao atualizar registro. ", null, $isAjax);
            $this->registerLogError($e);
        } catch (AppException $e) {
            self::setMessage(TipoMensagem::ERROR, $e->getMessage(), null, $isAjax);
        } catch (Exception $e) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao atualizar registro. Erro: {$e->getMessage()}.", null, $isAjax);
            $this->registerLogError($e);
        }
        if (!$isAjax) {
            return $this->route("UsuarioContribuinte");
        }
    }
}