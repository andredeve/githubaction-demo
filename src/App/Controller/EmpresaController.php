<?php

namespace App\Controller;

use App\Model\Configuracao;
use App\Model\Empresa;
use Core\Controller\AppController;
use Core\Enum\TipoMensagem;
use DateTime;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Exception;
use Core\Exception\AppException;
use Doctrine\DBAL\DBALException;

/**
 * Classe EmpresaController
 * @version 1.0
 * @author Anderson Brandão <batistoti@gmail.com>
 * 
 * @copyright 2016 Lxtec Informática LTDA
 */
class EmpresaController extends AppController {

    public static function setEmpresa() {
        $_POST['empresa'] = (new Empresa())->buscar($_POST['empresa_id']);
    }

    function __construct() {
        parent::__construct(get_class());
    }

    private function setConfiguracao(Empresa $empresa) {
        if (count((new Configuracao())->listarPorCampos(array('empresa' => $empresa))) == 0) {
            $configuracao = new Configuracao();
            $configuracao->setBloquearTicket(0);
            $configuracao->setConcluirTicket(0);
            $configuracao->setEnviarEmails(1);
            $configuracao->setEmpresa($empresa);
            $configuracao->inserir();
        }
    }

    /**
     * @return void
     */
    public function inserir() {
        try {
            $_POST['dataCadastro'] = new DateTime();
            $_POST['ultimaAlteracao'] = null;
            $_POST['endereco'] = ((new EnderecoController())->getEndereco());
            $empresa = new Empresa();
            $this->getValues($empresa);
            $objeto_id = $empresa->inserir();
            $this->setConfiguracao($empresa);
            self::setMessage(TipoMensagem::SUCCESS, 'Empresa cadastrada com sucesso!', $objeto_id);
            return $this->route($this->class_path);
        } catch (UniqueConstraintViolationException $e) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao inserir registro. Mensagem: registro já cadastrado!");
        } catch (DBALException $e) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao inserir registro. ");
            parent::registerLogError($e);
        } catch (AppException $e) {
            self::setMessage(TipoMensagem::ERROR, $e->getMessage());
        } catch (Exception $e) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao inserir registro. Erro: {$e->getMessage()}");
            parent::registerLogError($e);
        }
        $this->route($this->class_path, 'cadastrar');
    }

    /**
     * Método genérico de atualização no banco de dados
     * @return void
     */
    public function atualizar() {
        $empresa_id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
        try {
            $_POST['ultimaAlteracao'] = new DateTime();
            $_POST['endereco'] = ((new EnderecoController())->getEndereco());
            $empresa = (new Empresa())->buscar($empresa_id);
            $this->setImagens();
            $this->getValues($empresa);
            $empresa->atualizar();
            $this->setStatus($empresa);
            $this->setConfiguracao($empresa);
            self::setMessage(TipoMensagem::SUCCESS, 'Registro atualizado com sucesso!');
            $this->route($this->class_path);
            return;
        } catch (DBALException $e) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao atualizar registro. ");
            parent::registerLogError($e);
        } catch (AppException $e) {
            self::setMessage(TipoMensagem::ERROR, $e->getMessage());
        } catch (Exception $e) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao atualizar registro. Erro: {$e->getMessage()}");
            parent::registerLogError($e);
        }
        $this->route($this->class_path, 'editar/id/' . $empresa_id);
    }
}
