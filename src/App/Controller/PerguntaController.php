<?php

namespace App\Controller;

use App\Model\Pergunta;
use App\Model\SetorFase;
use Core\Controller\AppController;
use Core\Enum\TipoMensagem;
use Exception;
use Doctrine\DBAL\DBALException;
use Core\Exception\AppException;

/**
 * Classe PerguntaController
 * @version 1.0
 * @author Anderson Brandão Batistoti <anderson@lxtec.com.br>
 * @date   30/01/2018
 * @copyright (c) 2018, Lxtec Informática
 */
class PerguntaController extends AppController {

    function __construct() {
        parent::__construct(get_class());
    }

    /**
     * Método genérico de inserção no banco de dados
     */
    public function editar() {
        try {
            $object = new $this->classe();
            $args = func_get_args();
            $_REQUEST['objeto'] = $object->buscar($args[1]);
            return $this->load($this->class_path, 'editar');
        } catch (DBALException $e) {
            parent::registerLogError($e);
            die("Erro ao editar registro. ");
        }  catch (AppException $e) {
            die($e->getMessage());
        } catch (Exception $e) {
            parent::registerLogError($e);
            die("Erro ao editar registro. Erro: {$e->getMessage()}");
        }
    }

    function inserir() {
        $_POST['setorFase'] = (new SetorFase())->buscar($_POST['setor_fase_id']);
        return parent::inserir();
    }

    function excluir() {
        $args = func_get_args();
        $object_id = $args[1];
        $object = new $this->classe();
        $object = $object->buscar($object_id);
        $this->reordenarCampos($object->getSetorFase());
        return call_user_func_array('parent::excluir', $args);
    }

    private function reordenarCampos(SetorFase $setor_fase) {
        $ordem = 1;
        foreach ($setor_fase->getCampos() as $campo) {
            $campo->setOrdem($ordem);
            $ordem++;
        }
        $setor_fase->atualizar();
    }

    function ordenar() {
        try {
            $ordem = 1;
            foreach ($_POST['item'] as $pergunta_id) {
                $pergunta = (new Pergunta())->buscar($pergunta_id);
                $pergunta->setOrdem($ordem);
                $pergunta->atualizar();
                $ordem++;
            }
            self::setMessage(TipoMensagem::SUCCESS, "Ordem de perguntas alterada.", $pergunta->getSetorFase()->getId(), true);
        } catch (DBALException $ex) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao alterar a ordem das perguntas.", null, true);
            parent::registerLogError($ex);
        } catch (AppException $ex) {
            self::setMessage(TipoMensagem::ERROR, $ex->getMessage(), null, true);
        } catch (Exception $ex) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao alterar a ordem das perguntas. Erro: {$ex->getMessage()}", null, true);
            parent::registerLogError($ex);
        }
    }

}
