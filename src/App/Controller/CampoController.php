<?php

namespace App\Controller;

use App\Model\Campo;
use App\Model\SetorFase;
use App\Model\TipoAnexo;
use Core\Controller\AppController;
use Core\Enum\TipoMensagem;
use Core\Exception\BusinessException;
use Core\Util\Upload;
use Doctrine\DBAL\DBALException;
use Core\Exception\AppException;
use Exception;

use const FILE_PATH;

/**
 * Classe CampoController
 * @version 1.0
 * @author Anderson Brandão Batistoti <anderson@lxtec.com.br>
 * @date   31/01/2018
 * @copyright (c) 2018, Lxtec Informática
 */
class CampoController extends AppController
{

    private $template_path;

    function __construct()
    {
        parent::__construct(get_class());
        $this->template_path = FILE_PATH . "processos/templates/";
    }

    /**
     * Método genérico de inserção no banco de dados
     */
    public function editar()
    {
        try {
            $object = new $this->classe();
            $args = func_get_args();
            $_REQUEST['objeto'] = $object->buscar($args[1]);
            return $this->load($this->class_path, 'editar');
        } catch (DBALException $e) {
            parent::registerLogError($e);
            die("Erro ao editar registro. ");
        } catch (AppException $e) {
            die($e->getMessage());
        } catch (Exception $e) {
            parent::registerLogError($e);
            die("Erro ao editar registro. Erro: {$e->getMessage()}");
        }
    }

    function inserir()
    {
        $_POST['setorFase'] = (new SetorFase())->buscar($_POST['setor_fase_id']);
        $this->setCampo();
        return parent::inserir();
    }

    function atualizar()
    {
        $this->setCampo();
        return parent::atualizar();
    }

    function setCampo()
    {
        $this->setTemplate();
        $this->setValoresCamposSelecao();
        if($_POST['tipo'] == 'arquivo'){
            $_POST['tipoTemplate'] = !empty($_POST['tipoTemplate']) ? (new TipoAnexo())->buscar($_POST['tipoTemplate']) : null;
        } else if ($_POST['tipo'] == 'arquivo-multiplo'){
            $_POST['tipoTemplate'] = !empty($_POST['tipoTemplateMultiplosArquivos']) ? (new TipoAnexo())->buscar($_POST['tipoTemplateMultiplosArquivos']) : $_POST['tipoTemplate'];
        }
        $_POST['numeroTemplateObrigatorio'] = isset($_POST['numeroTemplateObrigatorio']) ? true : false;
        $_POST['assinaturaObrigatoria'] = isset($_POST['assinaturaObrigatoria']) ? true : false;
    }

    private function setTemplate()
    {
        if (isset($_FILES['template']['name'])) {
            $_POST['template'] = (new Upload('template', $this->template_path, array('doc', 'docx')))->upload();
        }
    }

    private function setValoresCamposSelecao()
    {
        if (isset($_POST['valoresSelecao'])) {
            $_POST['valoresSelecao'] = implode(";", $_POST['valoresSelecao']);
        }
    }

    function excluir() {
        try {
            $args = func_get_args();
            $campo_id = $args[1];
            $campo = new $this->classe();
            $campo = $campo->buscar($campo_id);
            $campo->desativar($campo_id);
            $this->reordenarCampos($campo->getSetorFase());
            self::setMessage(TipoMensagem::SUCCESS, 'Registro desativado.', null, true);
        } catch (BusinessException $ex) {
            self::setMessage(TipoMensagem::SUCCESS, $ex->getMessage(), null, true);
        } catch (Exception $ex) {
            self::setMessage(TipoMensagem::SUCCESS, 'Ocorreu uma falha. Por favor, contate o suporte.', null, true);
        }
    }

    function reativar() {
        try {
            $args = func_get_args();
            $campo_id = $args[1];
            $campo = new $this->classe();
            $campo = $campo->buscar($campo_id);
            $campo->reativar($campo_id);
            $this->reordenarCampos($campo->getSetorFase());
            self::setMessage(TipoMensagem::SUCCESS, 'Registro reativado.', null, true);
        } catch (BusinessException $ex) {
            self::setMessage(TipoMensagem::SUCCESS, $ex->getMessage(), null, true);
        } catch (Exception $ex) {
            self::setMessage(TipoMensagem::SUCCESS, 'Ocorreu uma falha. Por favor, contate o suporte.', null, true);
        }
    }

    private function reordenarCampos(SetorFase $setor_fase)
    {
        $ordem = 1;
        foreach ($setor_fase->getCampos() as $campo) {
            $campo->setOrdem($ordem);
            $ordem++;
        }
        $setor_fase->atualizar();
    }

    function ordenar()
    {
        try {
            $ordem = 1;
            foreach ($_POST['item'] as $campo_id) {
                $campo = (new Campo())->buscar($campo_id);
                $campo->setOrdem($ordem);
                $campo->atualizar();
                $ordem++;
            }
            self::setMessage(TipoMensagem::SUCCESS, "Ordem de campos alterada.", $campo->getSetorFase()->getId(), true);
        } catch (DBALException $ex) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao alterar a odernação de campos.", null, true);
            parent::registerLogError($ex);
        } catch (AppException $ex) {
            self::setMessage(TipoMensagem::ERROR, $ex->getMessage(), null, true);
        } catch (Exception $ex) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao alterar a odernação de campos. Erro: {$ex->getMessage()}", null, true);
            parent::registerLogError($ex);
        }
    }

}
