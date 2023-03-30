<?php

namespace App\Controller;

use App\Enum\TipoLog;
use App\Enum\TipoUsuario;
use App\Model\Log;
use App\Model\Notificacao;
use App\Model\Processo;
use App\Model\Tramite;
use App\Model\Usuario;
use Core\Controller\AppController;
use Core\Enum\TipoMensagem;
use Core\Exception\BusinessException;
use Core\Util\Report;
use DateTime;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Exception;
use Core\Exception\AppException;
use Doctrine\DBAL\DBALException;

use const APP_PATH;

/**
 * Classe NotificacaoController
 * @version 1.0
 * @author Anderson Brandão Batistoti <anderson@lxtec.com.br>
 * @date   26/03/2018
 * @copyright (c) 2018, Lxtec Informática
 */
class NotificacaoController extends AppController
{

    function __construct()
    {
        parent::__construct(get_class());
        $this->breadcrumb = "Notificações";
    }

    function imprimir()
    {
        $args = func_get_args();
        if (isset($args[1]) && is_numeric($args[1])) {
            $notificacao = new Notificacao();
            $notificacao = $notificacao->buscar($args[1]);
            $title = "Notificação #" . $notificacao->getNumero() . " | " . $notificacao->getStatus();
            $report = new Report($title);
            //            $report->AliasNbPages();
            $report->AddPage();
            $report->SetY($report->GetY() + 25);
            $report->SetFont("times", "B", "14");
            $report->Cell(0, 10, $title, 0, 1, "L");
            if ($notificacao->getProcesso() != null) {
                $w = array(30, 80, 80);
                $a = array('C', 'L', 'L');
                $report->SetFont("times", "B", "10");
                $report->Cell(0, 5, "DADOS DO PROCESSO", 1, 1, "C");
                $headers = array("Nº", "Assunto", "Interessado");
                $qtde_h = count($headers);
                $report->SetFont("times", "B", "10");
                foreach ($headers as $i => $h) {
                    $report->Cell($w[$i], 5, $h, "LTR", $qtde_h == $i + 1 ? 1 : 0, "L");
                }
                $processo = $notificacao->getProcesso();
                $values = array(
                    $processo->getNumero() . "/" . $processo->getExercicio(),
                    $processo->getAssunto()->getDescricao(),
                    $processo->getInteressado()->getPessoa()->getNome()
                );
                $report->SetFont("times", "", "10");
                $report->SetWidths($w);
                $report->SetAligns($a);
                foreach ($values as $i => $val) {
                    $report->Cell($w[$i], 5, $val, "LBR", $qtde_h == $i + 1 ? 1 : 0, "L");
                    //$report->Row($val, false, "LBR");
                }
                $report->SetFont("times", "B", "10");
                $report->Cell(0, 5, "Objeto:", "LTR", 1);
                $report->SetFont("times", "", "10");
                $report->MultiCell(0, 5, $processo->getObjeto(), "LBR", 1);
                $report->Ln(3);
            }
            $report->SetFont("times", "B", "10");
            $report->Cell(0, 5, "DADOS DA NOTIFICAÇÃO", 1, 1, "C");
            $w = array(25, 35, 25, 35, 35, 35);
            $a = array('C', 'C', 'C', 'C', 'C', 'C');
            $headers = array("Número", "Criada em", "Prazo", "Lida em", "Respondida em", "Arquivada em");
            $qtde_h = count($headers);
            $report->SetFont("times", "B", "10");
            foreach ($headers as $i => $h) {
                $report->Cell($w[$i], 5, $h, "LTR", $qtde_h == $i + 1 ? 1 : 0, "L");
            }
            $values = array(
                $notificacao->getNumero(),
                $notificacao->getDataCriacao()->format('d/m/Y - H:i'),
                $notificacao->getPrazoResposta()->format('d/m/Y'),
                $notificacao->getDataVisualizacao() != null ? $notificacao->getDataVisualizacao()->format('d/m/Y - H:i') : '-',
                $notificacao->getDataResposta() != null ? $notificacao->getDataResposta()->format('d/m/Y - H:i') : '-',
                $notificacao->getDataArquivamento() != null ? $notificacao->getDataArquivamento()->format('d/m/Y - H:i') : '-'
            );
            $report->SetFont("times", "", "10");
            foreach ($values as $i => $val) {
                $report->Cell($w[$i], 5, $val, "LBR", $qtde_h == $i + 1 ? 1 : 0, "L");
            }
            $report->Ln(3);
            $report->SetFont("times", "B", "10");
            $report->Cell(0, 5, "Remetente:", "LTR", 1);
            $report->SetFont("times", "", "10");
            $report->MultiCell(0, 5, $notificacao->getUsuarioAbertura(), "LBR", 1);
            $report->Ln(3);
            $report->SetFont("times", "B", "10");
            $report->Cell(0, 5, "Destinatário:", "LTR", 1);
            $report->SetFont("times", "", "10");
            $report->MultiCell(0, 5, $notificacao->getUsuarioDestino(), "LBR", 1);
            $report->Ln(3);
            $report->SetFont("times", "B", "10");
            $report->Cell(0, 5, "Assunto:", "LTR", 1);
            $report->SetFont("times", "", "10");
            $report->MultiCell(0, 5, $notificacao->getAssunto(), "LBR", 1);
            $report->Ln(3);
            $report->SetFont("times", "B", "10");
            $report->Cell(0, 5, "Conteúdo:", 1, 1);
            $report->SetFont("times", "", "10");
            $report->WriteHTML($notificacao->getTexto());
            if ($notificacao->getIsRespondida()) {
                $report->Ln(5);
                $report->SetFont("times", "B", "10");
                $report->Cell(0, 5, "Resposta:", 1, 1);
                $report->SetFont("times", "", "10");
                $report->WriteHTML($notificacao->getResposta());
            }
            $report->Ln(5);
            $report->Output();
        }
    }

    function responder()
    {
        try {
            if (isset($_POST['notificacao_id']) && is_numeric($_POST['notificacao_id'])) {
                $notificacao = (new Notificacao())->buscar(filter_input(INPUT_POST, 'notificacao_id', FILTER_SANITIZE_NUMBER_INT));
                $notificacao->setIsRespondida(true);
                $notificacao->setResposta(filter_input(INPUT_POST, 'resposta', FILTER_SANITIZE_STRING));
                $notificacao->setDataResposta(new DateTime());
                $notificacao->atualizar();
                self::setMessage(TipoMensagem::SUCCESS, "Notificação respondida com sucesso!", null, true);
            } else {
                throw new BusinessException("Sem identificação da notificação.");
            }
        } catch (DBALException $ex) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao responder notificação.", null, true);
            parent::registerLogError($ex);
        } catch (AppException $ex) {
            self::setMessage(TipoMensagem::ERROR, $ex->getMessage(), null, true);
        } catch (Exception $ex) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao responder notificação. Erro: {$ex->getMessage()}", null, true);
            parent::registerLogError($ex);
        }
    }

    function visualizar()
    {
        try {
            $args = func_get_args();
            if (isset($args[1]) && is_numeric($args[1])) {
                $_REQUEST['breadcrumb'] = array(
                    array('link' => $this->class_path, 'title' => $this->getBreadCrumbTitle()),
                    array('link' => null, 'title' => 'Visualizar')
                );
                $nofiticacao = new Notificacao();
                $nofiticacao = $nofiticacao->buscar($args[1]);
                if ($nofiticacao->getUsuarioDestino()->getId() == UsuarioController::getUsuarioLogado()->getId()) {
                    //Salvar a primeira visualização da notificação pelo usuário de destino
                    if (!$nofiticacao->getIsVisualizada()) {
                        $nofiticacao->setIsVisualizada(true);
                        $nofiticacao->setDataVisualizacao(new DateTime());
                        $nofiticacao->atualizar();
                    }
                }
                $_REQUEST['notificacao'] = $nofiticacao;
                $this->load($this->class_path, 'visualizar');
            } else {
                throw new BusinessException("Sem identificação da notificação.");
            }
        } catch (Exception $ex) {
            return $this->error404();
        }
    }

    //    function cadastrar()
    //    {
    //        $_REQUEST['breadcrumb'] = array(
    //            array('link' => $this->class_path, 'title' => $this->getBreadCrumbTitle()),
    //            array('link' => null, 'title' => 'Cadastrar')
    //        );
    //        $usuario_logado = UsuarioController::getUsuarioLogado();
    //        $prosseguir = $usuario_logado != null && $usuario_logado->getTipo() != TipoUsuario::USUARIO;
    //        if ($prosseguir) {
    //            $this->load($this->class_path, 'cadastrar');
    //        } else {
    //            $this->error403();
    //        }
    //    }

    private function arquivarNotificacao($notificacao_id)
    {
        $notificacao = new Notificacao();
        $notificacao = $notificacao->buscar($notificacao_id);
        $notificacao->setIsArquivada(true);
        $notificacao->setDataArquivamento(new \DateTime());
        $notificacao->atualizar();
        Log::registrarLog(TipoLog::ACTION_UPDATE, $notificacao->getTableName(), "Notificação arquivada", null, $notificacao->imprimir());
    }

    private function removerNotificacao($notificaco_id)
    {
        $notificacao = new Notificacao();
        $notificacao->remover($notificaco_id);
        Log::registrarLog(TipoLog::ACTION_DELETE, $notificacao->getTableName(), "Registro deletado", null, $notificacao->imprimir());
    }

    function excluir()
    {
        try {
            $args = func_get_args();
            if (isset($args[1])) {
                $this->removerNotificacao($args[1]);
                self::setMessage(TipoMensagem::SUCCESS, 'Notificação removida com sucesso!', null, true);
            } else {
                foreach ($_POST['notificacao_id'] as $notificacao_id) {
                    $this->removerNotificacao($notificacao_id);
                }
                self::setMessage(TipoMensagem::SUCCESS, 'Notificações removidas com sucesso!', null, true);
            }
        } catch (ForeignKeyConstraintViolationException $e) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao remover registro. Mensagem: este registro está relacionado a outro. Você não pode excluí-lo.", null, true);
        } catch (DBALException $e) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao remover registro. ", null, true);
            parent::registerLogError($e);
        } catch (AppException $e) {
            self::setMessage(TipoMensagem::ERROR, $e->getMessage(), null, true);
        } catch (Exception $e) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao remover registro. Erro: {$e->getMessage()}", null, true);
            parent::registerLogError($e);
        }
    }

    function arquivar()
    {
        try {
            if (is_array($_POST['notificacao_id'])) {
                foreach ($_POST['notificacao_id'] as $notificacao_id) {
                    $this->arquivarNotificacao($notificacao_id);
                }
                self::setMessage(TipoMensagem::SUCCESS, 'Notificações arquivadas com sucesso!', null, true);
            } else {
                $this->arquivarNotificacao($_POST['notificacao_id']);
                self::setMessage(TipoMensagem::SUCCESS, 'Notificação arquivada com sucesso!', null, true);
            }
        } catch (DBALException $e) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao arquivar notificação. ", null, true);
            parent::registerLogError($e);
        } catch (AppException $e) {
            self::setMessage(TipoMensagem::ERROR, $e->getMessage(), null, true);
        } catch (Exception $e) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao arquivar notificação. Erro: {$e->getMessage()}", null, true);
            parent::registerLogError($e);
        }
    }

    function inserir()
    {
        $_POST['dataCriacao'] = new DateTime();
        $_POST['isArquivada'] = false;
        $_POST['isRespondida'] = false;
        $_POST['isVisualizada'] = false;
        $_POST['usuarioAbertura'] = UsuarioController::getUsuarioLogadoDoctrine();
        if (count($_POST['destinatario_id']) > 1) {
            $destinatarios = array();
            $destinatarios = $_POST['destinatario_id'];
            foreach ($destinatarios as $destinatario) {
                $this->setNotificacaoArray($destinatario);
                parent::inserir();
            }
        } else {
            $this->setNotificacao();
            return parent::inserir();
        }
    }

    private function setNotificacao()
    {
        if (isset($_POST['processo_id']) && !empty($_POST['processo_id'])) {
            $processo =  new Processo();
            $processo = $processo->buscar(filter_input(INPUT_POST, 'processo_id', FILTER_SANITIZE_NUMBER_INT));
            $_POST['tramite'] = $processo->getTramiteAtualSemApenso();
        }
        $_POST['usuarioDestino'] = (new Usuario())->buscar(filter_input(INPUT_POST, 'destinatario_id', FILTER_SANITIZE_NUMBER_INT));
    }

    private function setNotificacaoArray($destinatario)
    {
        $processo =  new Processo();
        $processo = $processo->buscar(filter_input(INPUT_POST, 'processo_id', FILTER_SANITIZE_NUMBER_INT));
        $_POST['tramite'] = $processo->getTramiteAtualSemApenso();
        $_POST['usuarioDestino'] = (new Usuario())->buscar($destinatario);
    }

    function index()
    {
        $_REQUEST['breadcrumb'] = array(array('link' => null, 'title' => $this->getBreadCrumbTitle()));
        $args = func_get_args();
        if (!empty($args) && isset($args[0])) {
            $selected = $args[0];
        } else {
            $selected = 'recebidas';
        }
        $_REQUEST['selected'] = $selected;
        $this->load($this->class_path);
    }
}
