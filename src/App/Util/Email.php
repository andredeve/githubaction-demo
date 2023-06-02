<?php

namespace App\Util;

use App\Controller\IndexController;
use App\Model\Empresa;
use App\Model\Pessoa;
use App\Model\Processo;
use App\Model\Usuario;
use Core\Controller\AppController;
use Core\Enum\TipoMensagem;
use Exception;
use const APP_URL;

/**
 * Classe Email
 * @version 1.0
 * @author Anderson Brandão <batistoti@gmail.com>
 *
 * @copyright 2016 Lxtec Informática LTDA
 */
class Email extends \Core\Util\Email
{

    public function notificarModificacaoProcesso(Processo $processo)
    {
        $interessado = $processo->getInteressado();
        $tramite_atual = $processo->getTramiteAtual();
        if ($interessado->getPessoa()->getEmail() != null) {
            $mensagemHTML = '<h2 class="lead">' . $this->cliente_config['nome'] . '</h2><hr>';
            $mensagemHTML .= '<p>Sr(a) ' . $interessado . ',</p><br>';
            $mensagemHTML .= '<p>Segue abaixo informações do andamento do seu processo,';
            $mensagemHTML .= '<p><b>Nome Interessado:</b> ' . $interessado . '</p>';
            $mensagemHTML .= '<p><b>Nº Protocolo/Processo:</b> ' . ($processo->getNumero() === null) ? 'Aguardando Aprovação' : $processo . '</p>';
            $mensagemHTML .= '<p><b>Data/hora trâmite:</b> ' . $tramite_atual->getDataEnvio()->format('d/m/Y - H:i:s') . '</p>';
            $mensagemHTML .= '<p><b>Assunto:</b> ' . $tramite_atual->getAssunto() . '</p>';
            $mensagemHTML .= '<p><b>Setor Atual:</b> ' . ($processo->getNumero() === null) ? 'Aguardando Aprovação' : $tramite_atual->getSetorAtual() . '</p>';
            $mensagemHTML .= '<p><b>Prazo trâmite:</b> ' . $tramite_atual->getVencimento() . '</p>';
            $mensagemHTML .= '<p><b>Parecer:</b> ' . $tramite_atual->getStatus() . '</p>';
            $mensagemHTML .= '<p><b>Descrição parecer:</b>' . $tramite_atual->getParecer() . '</p><hr>';
            $urlProcesso = APP_URL . ($processo->getIsExterno() ? "contribuinte" : "consulta");
            $mensagemHTML .= '<p>Para consultar seus processos, e verificar pendências por favor acesse: <a href ="'.$urlProcesso.'" target=\'_blank\'>'.$urlProcesso.'</a></p><hr>';
            return $this->enviarEmail($this->cliente_config['nome'] . " - Movimentação do seu Processo Nº" . $processo, $mensagemHTML, array($interessado->getPessoa()->getEmail()));
        }
        return false;
    }

    function notificarCriacaoProcesso(Processo $processo)
    {
        $tramite_atual = $processo->getTramiteAtual();
        $interessado = $processo->getInteressado();
        if ($interessado->getPessoa()->getEmail() != null) {
            /* Montando a mensagem a ser enviada no corpo do e-mail. */
            $mensagemHTML = '<h2 class="lead">' . $this->cliente_config['nome'] . '</h2><hr>';
            $mensagemHTML .= '<p>Sr(a) ' . $interessado . ',</p><br>';
            $mensagemHTML .= '<h4 class="text-muted">Notificação de Processo criado.</h4>';
            $mensagemHTML .= '<p>Segue abaixo suas informações do protocolo</p>';
            $mensagemHTML .= '<p><b>Nº Processo:</b> ' . ($processo->getNumero() === null ? "Aguardando Aprovação" : $processo) . '</p>';
            $mensagemHTML .= '<p><b>Setor Atual:</b> ' . $tramite_atual->getSetorAtual() . '</p>';
            $mensagemHTML .= '<p><b>Data Processo:</b> ' . $processo->getDataAbertura()->format('d/m/Y') . '</p>';
            $mensagemHTML .= '<p><b>Hora Processo:</b> ' . $tramite_atual->getDataEnvio()->format('H:i') . '</p>';
            $mensagemHTML .= '<p><b>Requerimento/objeto:</b> ' . $processo->getObjeto() . '</p>';
            $urlProcesso = APP_URL . ($processo->getIsExterno() ? "contribuinte" : "consulta");
            $mensagemHTML .= "<p>Para consultar seus protocolos/processos, por favor acesse: <a href ='$urlProcesso' target='_blank'>$urlProcesso</a></p><hr>";
            return $this->enviarEmail($this->cliente_config['nome'] . " - Notificação de processo criado.", $mensagemHTML, array($interessado->getPessoa()->getEmail()));
        }
        return false;
    }

    function enviarContatoSite()
    {
        try {
            $empresa = (new Empresa())->buscar();
            $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $telefone = filter_input(INPUT_POST, 'telefone');
            $assunto = filter_input(INPUT_POST, 'assunto', FILTER_SANITIZE_STRING);
            $mensagem = filter_input(INPUT_POST, 'mensagem', FILTER_SANITIZE_STRING);
            $cliente = IndexController::getClienteConfig();
            $content = "<p>Contato recebido via formulário do site.</p>";
            $content .= "<table>";
            $content .= "<tr><td><b>Nome:</b></td><td>$nome</td></tr>";
            $content .= "<tr><td><b>E-mail:</b></td><td>$email</td></tr>";
            $content .= "<tr><td><b>Telefone:</b></td><td>$telefone</td></tr>";
            $content .= "<tr><td><b>Assunto:</b></td><td>$assunto</td></tr>";
            $content .= "<tr><td><b>Mensagem:</b></td><td>$mensagem</td></tr>";
            $content .= "<table>";
            $this->enviarEmail($cliente['nome'] . ' | Formulário de Contato - Site', $content, array($empresa->getEmail()), $nome, $email);
            AppController::setMessage(TipoMensagem::SUCCESS, "Mensagem enviada com sucesso! Assim que possível entraremos em contato.", null, true);
        } catch (Exception $ex) {
            AppController::setMessage(TipoMensagem::ERROR, "Erro ao enviar mensagem:" . $ex->getMessage(), null, true);
        }
    }

    function enviarNotificacaoErro($anexo)
    {
        $cliente = IndexController::getClienteConfig();
        $content = "<p>Foram detectados erros ou situações que precisam ser verificadas no sistema.<br/>Segue em anexo o arquivo de log do PHP no cliente.</p>";
        $content .= "<p style='$this->text_muted'>** E-mail automático enviado pelo sistema LxControl. Por favor, não responda.</p>";
        return $this->enviarEmail($cliente['nome'] . ' | LxContrato - Arquivo de Log [ERROS]', $content, array('anderson@lxtec.com.br'), null, null, $anexo);
    }

    /**
     * Função para enviar email com a nova senha temporaria do usuario
     * @param Usuario $usuario
     * @param string $senha
     * @return bool
     * @throws Exception
     */
    public function enviarSenhaUsuario(Usuario $usuario, $senha)
    {
        /* Montando a mensagem a ser enviada no corpo do e-mail. */
        $mensagemHTML = '<p>Sr(a) ' . $usuario->getPessoa()->getNome() . ',</p><br>';
        if(isset($_POST['transformar']) || isset($_POST['isInterno'])){
            $mensagemHTML .= '<h4 class="text-muted">Senha de acesso</h4><p>Segue abaixo sua senha de acesso, <a href ="' . APP_URL . '" target=\'_blank\'>Clique aqui para acessar.</a></p><p><b>Senha:</b> ' . $senha . '</p>';
        } else {
            $mensagemHTML .= '<h4 class="text-muted">Recuperação de Senha</h4><p>Segue abaixo sua nova senha de acesso, <a href ="' . APP_URL . '" target=\'_blank\'>Clique aqui para acessar.</a></p><p><b>Senha:</b> ' . $senha . '</p>';
        }
        return $this->enviarEmail($usuario->getPessoa()->getNome() . " - Sua nova senha de acesso", $mensagemHTML, array($usuario->getPessoa()->getEmail()));
    }

    public function testEnviaEmail($email){
            /* Montando a mensagem a ser enviada no corpo do e-mail. */
        $mensagemHTML = '<p>Sr(a) Teste,</p><br>';
        $mensagemHTML .= '<h4 class="text-muted">Recuperação de Senha</h4><p>Segue abaixo sua nova senha de acesso, <a href ="' . APP_URL . '" target=\'_blank\'>Clique aqui para acessar.</a></p><p><b>Senha:</b>' . $senha . '</p>';
        return $this->enviarEmail("Teste ". " - Sua nova senha de acesso", $mensagemHTML, array($email));
    
    }

    function enviarConfirmacaoEmailValido(Usuario $usuario)
    {
        try {
            $urlAtivacao = APP_URL."login/ativacao/".$usuario->getTokenAtivacao();
            $cliente = IndexController::getClienteConfig();
            $app_name = IndexController::getConfig('app_name');
            $dados_email = IndexController::getCorpoEmailConfig();
            $parametros = IndexController::getParametosConfig();
            $assunto = str_replace(array("<app_name>", "<cliente>", "<contribuinte>"), array($app_name, $cliente['nome'], $parametros['contribuinte']), $dados_email['assunto']);
            $content = str_replace(array("<app_name>", "<urlAtivacao>", "<contribuinte>"), array($app_name, $urlAtivacao, $parametros['contribuinte']), $dados_email['conteudo']);
            $this->enviarEmail($assunto, $content, array($usuario->getPessoa()->getEmail()));
//            AppController::setMessage(TipoMensagem::SUCCESS, "Uma confirmação foi enviada para seu email.", null, true);
        } catch (Exception $ex) {

//            AppController::setMessage(TipoMensagem::ERROR, "Erro ao enviar email de confirmação. " . $ex->getMessage(), null, true);
        }
    }
}
