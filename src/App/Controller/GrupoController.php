<?php

namespace App\Controller;

use App\Enum\TipoLog;
use App\Enum\TipoUsuario;
use App\Model\Grupo;
use App\Model\Log;
use App\Model\PermissaoEntidade;
use Core\Controller\AppController;
use Core\Enum\TipoMensagem;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\DBAL\DBALException;
use Exception;
use Core\Exception\AppException;

/**
 * Classe GrupoController
 * @version 1.0
 * @author Anderson Brandão Batistoti <anderson@lxtec.com.br>
 * @date   10/01/2018
 * @copyright (c) 2018, Lxtec Informática
 */
class GrupoController extends AppController {

    function __construct() {
        parent::__construct(get_class());
    }

    public function index() {
        if (UsuarioController::getUsuarioLogado()->getTipo() != TipoUsuario::USUARIO && UsuarioController::getUsuarioLogado()->getTipo() != TipoUsuario::VISITANTE) {
            return parent::index();
        }
        return $this->error403();
    }

    public function cadastrar() {
        if (UsuarioController::getUsuarioLogado()->getTipo() != TipoUsuario::USUARIO && UsuarioController::getUsuarioLogado()->getTipo() != TipoUsuario::VISITANTE) {
            return parent::cadastrar();
        }
        return $this->error403();
    }

    function inserir() {
        $isAjax = isset($_REQUEST['ajax']) ? true : false;
        try {
            $_POST['dataCadastro'] = new DateTime();
            $_POST['ultimaAlteracao'] = null;
            $grupo = new Grupo();
            $this->setGrupo($grupo);
            $this->getValues($grupo);
            $grupo_id = $grupo->inserir();
            Log::registrarLog(TipoLog::ACTION_INSERT, $grupo->getTableName(), "Registro criado", null, null, $grupo->imprimir());
            self::setMessage(TipoMensagem::SUCCESS, 'Grupo cadastrado com sucesso!', $grupo_id, $isAjax);
            if (!$isAjax) {
                return $this->route($this->class_path);
            }
        } catch (UniqueConstraintViolationException $e) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao inserir grupo. Mensagem: grupo já cadastrado!", null, $isAjax);
        } catch (DBALException $e) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao inserir grupo. ", null, $isAjax);
            parent::registerLogError($e);
        } catch (AppException $e) {
            self::setMessage(TipoMensagem::ERROR, $e->getMessage(), null, $isAjax);
        } catch (Exception $e) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao inserir grupo. Erro: {$e->getMessage()}", null, $isAjax);
            parent::registerLogError($e);
        }
        if (!$isAjax) {
            return $this->route($this->class_path, 'cadastrar');
        }
    }

    function atualizar() {
        $isAjax = isset($_REQUEST['ajax']) ? true : false;
        try {
            $_POST['ultimaAlteracao'] = new DateTime();
            $grupo_id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
            $grupo = new Grupo();
            $grupo = $grupo->buscar($grupo_id);
            $old = clone $grupo;
            $this->setGrupo($grupo);
            $this->getValues($grupo);
            $new = $grupo;
            $grupo->atualizar();
            if ($new != $old) {
                Log::registrarLog(TipoLog::ACTION_UPDATE, $grupo->getTableName(), "Registro atualizado", null, $old->imprimir(), $new->imprimir());
            }
            self::setMessage(TipoMensagem::SUCCESS, 'Registro atualizado com sucesso!', null, $isAjax);
            if (!$isAjax) {
                $this->route($this->class_path);
                return;
            }
        } catch (DBALException $e) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao atualizar registro. ", null, $isAjax);
            parent::registerLogError($e);
        } catch (AppException $e) {
            self::setMessage(TipoMensagem::ERROR, $e->getMessage(), null, $isAjax);
        } catch (Exception $e) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao atualizar registro. Erro: {$e->getMessage()}", null, $isAjax);
            parent::registerLogError($e);
        }
        if (!$isAjax) {
            $this->route($this->class_path, 'editar/id/' . $grupo_id);
        }
    }

    private function setGrupo(Grupo $grupo) {
        $grupo->setRelatorios(isset($_POST['relatorios']) ? true : false);
        $grupo->setTramitar(isset($_POST['tramitar']) ? true : false);
        $grupo->setArquivar(isset($_POST['arquivar']) ? true : false);
        $this->setPermissoesEntidade($grupo);
    }

    private function setPermissoesEntidade(Grupo $grupo) {
        $permissoes = new ArrayCollection();
        foreach ($_POST['codigo_entidade'] as $i => $codigo) {
            $permissao = new PermissaoEntidade();
            if (!empty($_POST['permissao_menu_id'][$i])) {
                $permissao = $permissao->buscar($_POST['permissao_menu_id'][$i]);
            }
            $permissao->setCodigoEntidade($codigo);
            $permissao->setGrupo($grupo);
            $permissao->setInserir(isset($_POST['is_selecinado_inserir'][$codigo]) ? true : false);
            $permissao->setEditar(isset($_POST['is_selecinado_editar'][$codigo]) ? true : false);
            $permissao->setExcluir(isset($_POST['is_selecinado_excluir'][$codigo]) ? true : false);
            $permissoes->add($permissao);
        }
        $grupo->setPermissoesEntidade($permissoes);
    }

}
