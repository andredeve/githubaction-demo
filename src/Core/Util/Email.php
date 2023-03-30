<?php

namespace Core\Util;

use App\Controller\IndexController;
use App\Controller\UsuarioController;
use App\Model\Configuracao;
use Core\Controller\AppController;
use Core\Enum\TipoMensagem;
use Exception;

/**
 * Classe Email
 * @version 1.0
 * @author Anderson Brandão <batistoti@gmail.com>
 *
 * @copyright 2016 Lxtec Informática LTDA
 */
class Email
{

    protected $host;
    protected $user;
    protected $password;
    protected $timeout;
    protected $port;
    protected static $mail_instance = array();
    protected $text_muted;
    protected $lead;
    protected $table_responsive;
    protected $style_tabela;
    protected $style_coluna;
    protected $style_coluna_header;
    protected $cliente_config;

    function __construct($host = null, $user = null, $password = null, $port = null, $timeout = null)
    {
        $email_config = AppController::getMailConfig();
        $this->cliente_config = IndexController::getClienteConfig();
        $this->host = $host == null ? $email_config['host'] : $host;
        $this->user = $user == null ? $email_config['user'] : $user;
        $this->password = $password == null ? $email_config['password'] : $password;
        $this->timeout = $timeout == null ? $email_config['timeout'] : $timeout;
        $this->port = $port == null ? $email_config['port'] : $port;
        $this->setCss();
    }

    function setCss()
    {
        $this->text_muted = "color: #777;";
        $this->lead = 'margin-bottom: 20px;font-size: 18px;font-weight: 300;line-height: 1.4;color: #333;';
        $this->table_responsive = 'min-height: .01%;overflow-x: auto;';
        $this->style_tabela = 'width: 100%;max-width: 100%;margin-bottom: 20px;border: 1px solid #dddddd;text-align:left;';
        $this->style_coluna = 'padding: 8px;line-height: 1.42857143;vertical-align: top;border: 1px solid #ddd;';
        $this->style_coluna_header = 'vertical-align: bottom;border-bottom: 2px solid #dddddd;font-weight: bold;background-color: #d9edf7;';
    }

    /**
     * Retorna a instância atual da classe
     * PHPMailer se houver, caso contrário instancia uma nova
     * @return type
     */
    protected function getMailerInstance()
    {
        if (self::$mail_instance = !null) {
            self::$mail_instance = new PHPMailer();
        }
        return self::$mail_instance;
    }

    /**
     * Template padrão para envio de e-mails
     * @param type $content
     * @return string
     */
    protected function templateEmail($content)
    {
        $html = "<!DOCTYPE html><html lang='pt-br'>";
        $html .= '<meta charset="utf-8">';
        $html .= "<body>";
        //$html .= $this->getCabecalho();
        $html .= $content;
        /* $html .= '<p>------<br/>'
          . 'Mensagem Automática<br/>'
          . 'Favor não responder a este e-mail.</p>'; */
        $html .= "</body>";
        $html .= "</html>";
        return $html;
    }

    /**
     * Função que envia e-mail com solicitação de suporte do sistema
     * @return type
     */
    function enviarSolicitacaoSuporte()
    {
        $tipo_suporte = filter_input(INPUT_POST, 'tipo');
        $atendimento_primeiro_nivel = array('atendimento@lxtec.com.br');
        $atendimento_segundo_nivel = array('contato@lxtec.com.br');
        $destinatarios = $atendimento_primeiro_nivel;
        switch ($tipo_suporte) {
            case 'erro-bug':
                $tipo_suporte = 'Erro/Bug';
                $destinatarios = $atendimento_segundo_nivel;
                break;
            case 'duvida':
                $tipo_suporte = 'Dúvida';
                break;
            case 'melhorias':
                $tipo_suporte = 'Sugestão de Melhorias';
                break;
            case 'Solicitacao':
                $tipo_suporte = 'Solicitação';
                break;
        }
        $usuario = UsuarioController::getUsuarioLogadoDoctrine();
        $descricao = filter_input(INPUT_POST, 'descricao');
        $app = IndexController::getConfig();
        $cliente_config = $this->cliente_config;
        $content = "<h3>Suporte " . $app['app_name'] . "</h3><hr/>"
            . "<p><b>Cliente:</b> " . $cliente_config['nome'] . "</p>"
            . "<p><b>Nome do Usuário:</b> " . $usuario->getPessoa()->getNome() . "</p>"
            . "<p><b>E-mail:</b> " . $usuario->getPessoa()->getEmail() . "</p>"
            . "<p><b>Tipo</b>: " . $tipo_suporte . "</p>";
        $content .= "<p><b>Descrição</b>: " . $descricao . "</p>";
        $anexos = array();
        if (!empty($_FILES['anexos'])) {
            foreach ($_FILES['anexos']['tmp_name'] as $key => $tmp_name) {
                $anexos[] = array('tmp_name' => $tmp_name, 'name' => $_FILES['anexos']['name'][$key]);
            }
        }
        $enviado = $this->enviarEmail($app['app_name'] . " v." . $app['app_version'] . " - Suporte  [{$tipo_suporte}]", $content, $destinatarios, null, null, $anexos);
        if ($enviado) {
            return AppController::setMessage(TipoMensagem::SUCCESS, $tipo_suporte . ' enviado(a) com sucesso.', null, true);
        }
        return AppController::setMessage(TipoMensagem::ERROR, 'Erro ao solicitar suporte.', null, true);
    }

    /**
     * Função para envio de e-mail
     * @param type $assunto = assunto do email
     * @param type $mensagemHTML = corpo do email
     * @param type $destinatarios = vetor de emails
     * @return type
     * @throws Exception
     */
    public function enviarEmail($assunto, $mensagemHTML, $destinatarios, $remetente = null, $email_remetente = null, $anexos = null)
    {
        
        try {
            $mail = $this->getMailerInstance();
            $mail->IsSMTP(); // Define que a mensagem será SMTP
            $mail->SMTPAuth = true;
            // $mail->SMTPSecure = "tls";
            $mail->Port = $this->port;
            //$mail->SMTPDebug = 4;
            $mail->Host = $this->host; // Endereço do servidor SMTP
            $mail->Username = $this->user; // Usuário do servidor SMTP
            $mail->Password = $this->password; // Senha do servidor SMTP
            $mail->CharSet = 'UTF-8';
            $mail->From = $email_remetente != null ? $email_remetente : $this->user; // Seu e-mail
            $mail->Sender = $this->user; // Seu e-mail
            $app = IndexController::getConfig();
            $mail->FromName = $remetente != null ? $remetente : ($this->cliente_config != null ? $this->cliente_config['nome'] : $app['app_name']); // Seu nome
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );
            foreach ($destinatarios as $destinatario) {
                $mail->AddAddress($destinatario);
            }
            $mail->IsHTML(true); // Define que o e-mail será enviado como HTML
            // Define a mensagem (Texto e Assunto)
// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
            $mail->Subject = $assunto; // Assunto da mensagem
            $mail->Body = $this->templateEmail($mensagemHTML); //Corpo da mensagem
            // =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
            if ($anexos != null) {
                if (is_array($anexos)) {
                    foreach ($anexos as $a) {
                        $mail->AddAttachment($a['tmp_name'], $a['name']);
                    }
                } else if (!empty($anexos)) {
                    $mail->AddAttachment($anexos);
                }
            }
// Envia o e-mail
            $enviado = $mail->Send();
            if ($enviado !== true) {
                throw new Exception($mail->ErrorInfo);
            } else {
                $mail->ClearAllRecipients();
                $mail->ClearAttachments();
                return $enviado;
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

}
