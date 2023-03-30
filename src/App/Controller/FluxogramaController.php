<?php

namespace App\Controller;

use App\Enum\TipoLog;
use App\Model\Assunto;
use App\Model\Fase;
use App\Model\Fluxograma;
use App\Model\Log;
use App\Model\Setor;
use App\Model\SetorFase;
use Core\Controller\AppController;
use Core\Enum\TipoMensagem;
use Core\Exception\BusinessException;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Exception;
use Core\Exception\AppException;
use Doctrine\DBAL\DBALException;

/**
 * Classe FluxogramaController
 * @version 1.0
 * @author Anderson Brandão Batistoti <anderson@lxtec.com.br>
 * @date   11/01/2018
 * @copyright (c) 2018, Lxtec Informática
 */
class FluxogramaController extends AppController
{

    function __construct()
    {
        parent::__construct(get_class());
    }

    function estrutura()
    {
        $fluxograma_id = func_get_args()[1];
        $fluxograma = (new Fluxograma())->buscar($fluxograma_id);
        $_REQUEST['breadcrumb'] = array(
            array('link' => $this->class_path, 'title' => $this->getBreadCrumbTitle()),
            array('link' => $this->class_path . '/editar/id/' . $fluxograma_id, 'title' => ucfirst(mb_strtolower($fluxograma->getAssunto()))),
            array('link' => null, 'title' => "Estrutura de Requisitos"));
        $_REQUEST['fluxograma'] = $fluxograma;
        $this->load($this->class_path, 'estrutura');
    }

    /**
     * @return void
     */
    function inserir()
    {
        $isAjax = isset($_REQUEST['ajax']) ? true : false;
        try {
            $_POST['dataCadastro'] = new DateTime();
            $_POST['ultimaAlteracao'] = null;
            $fluxograma = new Fluxograma();
            $this->setFluxograma($fluxograma, true);
            $this->getValues($fluxograma);
            $objeto_id = $fluxograma->inserir();
            Log::registrarLog(TipoLog::ACTION_INSERT, $fluxograma->getTableName(), "Registro criado", null, null, $fluxograma->imprimir());
            self::setMessage(TipoMensagem::SUCCESS, 'Registro cadastrado com sucesso!', $objeto_id, $isAjax);
            if (!$isAjax) {
                $this->route($this->class_path);
                return;
            }
        } catch (UniqueConstraintViolationException $e) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao inserir registro. Mensagem: registro já cadastrado!", null, $isAjax);
        } catch (DBALException $e) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao inserir registro. ", null, $isAjax);
            parent::registerLogError($e);
        } catch (AppException $e) {
            self::setMessage(TipoMensagem::ERROR, $e->getMessage(), null, $isAjax);
        } catch (Exception $e) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao inserir registro. Erro: {$e->getMessage()}", null, $isAjax);
            parent::registerLogError($e);
        }
        if (!$isAjax) {
            $this->route($this->class_path, 'cadastrar');
        }
    }

    /**
     * Método genérico de atualização no banco de dados
     */
    public function atualizar()
    {
        $isAjax = isset($_REQUEST['ajax']) ? true : false;
        try {
            $fluxograma_id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
            $_POST['ultimaAlteracao'] = new DateTime();
            $fluxograma = new Fluxograma();
            $fluxograma = $fluxograma->buscar($fluxograma_id);
            $old = clone $fluxograma;
            $this->setFluxograma($fluxograma);
            $this->getValues($fluxograma);
            $new = $fluxograma;
            $fluxograma->atualizar();
            if ($new != $old) {
                Log::registrarLog(TipoLog::ACTION_UPDATE, $fluxograma->getTableName(), "Registro atualizado", null, $old->imprimir(), $new->imprimir());
            }
            self::setMessage(TipoMensagem::SUCCESS, 'Registro atualizado com sucesso!', null, $isAjax);
            /* if (!$isAjax) {
              return $this->route($this->class_path);
              } */
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
            return $this->route($this->class_path, 'editar/id/' . $fluxograma_id);
        }
    }

    private function setFaseAbertura(Fluxograma $fluxograma, $fases)
    {
        $fase_abertura = new Fase();
        $fase_abertura->setNumero(0);
        $fase_abertura->setFluxograma($fluxograma);
        $setor_fase_abertura = new SetorFase();
        $setor_fase_abertura->setFase($fase_abertura);
        $setor_fase_abertura->setSetor(null);
        $fase_abertura->adicionaSetorFase($setor_fase_abertura);
        $fases->add($fase_abertura);
    }

    private function setFluxograma(Fluxograma $fluxograma, $set_abertura = false)
    {
        if (!isset($_POST['fase'])) {
            throw new BusinessException("Defina as fases antes de salvar");
        }
        $fases = new ArrayCollection();
        /* if ($set_abertura) {
          $this->setFaseAbertura($fluxograma, $fases);
          } */
        $fluxograma->setAssunto((new Assunto())->buscar($_POST['assunto_id']));
        foreach ($_POST['fase'] as $i => $n_fase) {
            $fase = new Fase();
            if (!empty($_POST['fase_id'][$i])) {
                $fase = $fase->buscar($_POST['fase_id'][$i]);
            }
            $fase->setNumero($n_fase);
            $fase->setAtivo($_POST['ativo'][$i]);
            $fase->setFluxograma($fluxograma);
            $setores_fase = new ArrayCollection();
            foreach ($_POST['setor_fase_id'][$i] as $i_s => $setor_fase_id) {
                $setor_fase = new SetorFase();
                if (!empty($setor_fase_id)) {
                    $setor_fase = $setor_fase->buscar($setor_fase_id);
                }
                $setor_fase->setFase($fase);
                $setor_fase->setSetor((new Setor())->buscar($_POST['setor_id'][$i][$i_s]));
                $setor_fase->setPrazo(!empty($_POST['prazo'][$i][$i_s]) ? $_POST['prazo'][$i][$i_s] : null);
                $setor_fase->setIsPrazoDiaUtil(isset($_POST['is_dia_util'][$i][$i_s]) ? true : false);
                $setores_fase->add($setor_fase);
            }
            $fase->setSetoresFase($setores_fase);
            $fases->add($fase);
        }
        $fluxograma->setFases($fases);
    }

}
