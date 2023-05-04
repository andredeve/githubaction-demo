<?php

namespace App\Controller;

use App\Enum\TipoLog;
use App\Enum\TipoPessoa;
use App\Enum\TipoUsuario;
use App\Model\Dao\UsuarioDao;
use App\Model\Grupo;
use App\Model\Log;
use App\Model\Pessoa;
use App\Model\PessoaFisica;
use App\Model\PessoaJuridica;
use App\Model\Processo;
use App\Model\Setor;
use App\Model\Usuario;
use App\Util\Email;
use Core\Controller\AppController;
use Core\Enum\TipoMensagem;
use Core\Util\Functions;
use Core\Util\Report;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Internal\Hydration\AbstractHydrator;
use Exception;
use Core\Exception\AppException;
use Doctrine\DBAL\DBALException;

use stdClass;
use const APP_PATH;
use const APP_URL;
use const CONFIG_PATH;
use const FILE_PATH;

/**
 * Classe UsuarioController
 * @version 1.0
 * @author Anderson Brandão <batistaoti@gmail.com>
 *
 * @copyright 2016 Lxtec Informática LTDA
 */
class UsuarioController extends AppController
{

    protected $foto_path;

    public function __construct($classe = null)
    {
        parent::__construct($classe == null ? get_class() : $classe);
        $this->foto_path = FILE_PATH . 'usuario/';
        $this->breadcrumb = "Usuários";
    }

    public function index()
    {
        if (self::getUsuarioLogado()->getTipo() != TipoUsuario::USUARIO && self::getUsuarioLogado()->getTipo() != TipoUsuario::VISITANTE) {
            $this->listar(func_get_args());
            $this->load($this->class_path);
        } else {
            $this->error403();
        }
    }

    /**
     * Método genérico de listagem de registros de um objeto/tabela
     * @throws \Doctrine\ORM\ORMException
     */
    public function listar()
    {
        $usuario = new Usuario();
        $_REQUEST['breadcrumb'] = array(array('link' => null, 'title' => $this->getBreadCrumbTitle()));
        $_REQUEST['registros'] = $usuario->listarUsuarios();
    }

    public function visualizar()
    {
        $_REQUEST['breadcrumb'] = array(
            array('link' => $this->class_path, 'title' => $this->getBreadCrumbTitle()),
            array('link' => null, 'title' => 'Visualizar')
        );
        $_REQUEST['usuario'] = (new Usuario())->buscar(func_get_args()[1]);
        $this->load('usuario', 'visualizar');
    }

    public function senha()
    {
        $this->load('usuario', 'senha');
    }

    public function perfil()
    {
        $this->load('usuario', 'perfil');
    }

    public static function getSessionName()
    {
        if(defined("APP_URL")){
            return 'usuario' . self::getSessionId() . md5(APP_URL);
        }
        return 'usuario' . self::getSessionId();
    }

    public static function getSessionId()
    {
        return md5('seg');
    }

    public static function getUserIP()
    {
        return Functions::getUserIp();
    }

    private function criaSessao(Usuario $usuario)
    {
        $_SESSION[self::getSessionName()] = serialize($usuario);
        session_write_close();
    }

    /**
     * Verifica se usuário está logado
     * @return boolean
     */
    public static function isLogado()
    {
        if (!isset($_SESSION[self::getSessionName()])) {
            return false;
        }
        return true;
    }

    /**
     * Realiza logout
     */
    private static function logout()
    {
        $_SESSION = array();
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
        unset($_SESSION[self::getSessionName()]);
        session_destroy();
    }

    public static function atualizarUsuarioLogado()
    {
        $usuario = unserialize($_SESSION[self::getSessionName()]);
        $_SESSION[self::getSessionName()] = serialize((new Usuario())->buscar($usuario->getId()));
    }

    /**
     * @return bool
     */
    public static function isInteressado(): bool
    {
        if (self::isLogado()) {
            return (self::getUsuarioLogadoDoctrine())->isInteressado() ?? false;
        }
        return false;
    }
    /**
     * @return Usuario|null
     */
    public static function getUsuarioLogadoDoctrine()
    {
        if (self::isLogado()) {
            $usuario_logado = self::getUsuarioLogado();
            if ($usuario_logado != null) {
                return (new Usuario())->buscar($usuario_logado->getId());
            }
        }
        return null;
    }

    /**
     * @return Usuario|null
     */
    public static function getUsuarioLogado()
    {
        if (self::isLogado()) {
            return unserialize($_SESSION[self::getSessionName()]);
        }
        return null;
    }

    /**
     * Função para sair do sistema
     * @return string
     */
    public function sair()
    {
        self::logout();
        $this->route('Login');
    }

    function autenticarProcesso()
    {
        $login = filter_input(INPUT_POST, 'login');
        $senha = Usuario::codificaSenha(filter_input(INPUT_POST, 'senha'));
        $processo = (new Processo())->buscar(filter_input(INPUT_POST, 'processo_id'));
        $usuario = new Usuario();
        $usuario_tentativa = $usuario->buscarPorLogin($login);
        $autenticado = $usuario->autenticar($login, $senha);
        if ($autenticado != null) {
            if ($autenticado->getAtivo()) {
                $_SESSION['processo_' . $processo->getId()] = true;
                Log::registrarLog(TipoLog::LOGIN_SUCCESS, 'log', "Realizou autenticação para processo sigiloso: $processo", $autenticado);
                self::setMessage(TipoMensagem::SUCCESS, 'Autorização concedida com sucesso. Redirecionando para o processo...', $processo->getId(), true);
            } else {
                self::setMessage(TipoMensagem::WARNING, 'Usuário com acesso suspenso ao sistema.', null, true);
            }
        } else {
            if ($usuario_tentativa != null) {
                $usuario->registrarLogin($usuario_tentativa, false);
            }
            self::setMessage(TipoMensagem::ERROR, 'Usuário e/ou senha inválidos.', null, true);
        }
    }
    /**
     * Função sanitiza dados de login para pesquisa
     * @return string|numeric
     */
    private function sanitizeLogin(){
        $semMascara = Functions::limparCpfCnpj(filter_input(INPUT_POST, 'login',FILTER_SANITIZE_NUMBER_INT));

        if(is_numeric($semMascara)){
            return Functions::sanitizeNumber(
                filter_input(INPUT_POST, 'login',FILTER_SANITIZE_NUMBER_INT)
            );
        }
        return  filter_input(INPUT_POST, 'login',FILTER_SANITIZE_STRING);
    }

    /**
     * Função que realiza a autenticação de usuários
     */
    public function autenticar()
    {
        $resposta = $_POST['captcha'];
        $captcha = $_SESSION['captcha'];
        if($captcha != $resposta){
            self::setMessage(TipoMensagem::WARNING, ('Por favor, digite o código da imagem corretamente.'), null, true);
            return;
        }
        $login = $this->sanitizeLogin();
        $senha = Usuario::codificaSenha(filter_input(INPUT_POST, 'senha'));
        $usuario = new Usuario();
        $usuario_tentativa = $usuario->buscarPorLogin($login);
        $autenticado = $usuario->autenticar($login, $senha);


        if (!is_null($autenticado)) {
            if ($autenticado->getAtivo()) {
                $this->criaSessao($autenticado);
                $usuario->registrarLogin($autenticado, true);
                //return $this->route();
                $attemp_url_session_name = 'actual_link_' . self::getSessionName();
                if (isset($_SESSION[$attemp_url_session_name])) {
                    $url_redirect = $_SESSION[$attemp_url_session_name];
                    unset($_SESSION[$attemp_url_session_name]);
                } else {
                    $url_redirect = null;
                }
                self::setMessage(TipoMensagem::SUCCESS, 'Login realizado com sucesso. Redirecionando...', $url_redirect, true);
            } else {
                self::setMessage(TipoMensagem::WARNING, 'Usuário com acesso suspenso ao sistema.', null, true);
            }
        } else {
            if ($usuario_tentativa) {
                $usuario->registrarLogin($usuario_tentativa, false);
            }
            self::setMessage(TipoMensagem::ERROR, 'Usuário e/ou senha inválidos.', null, true);

        }
    }

    /**
     * Método para imprimir lista de usuário cadastrados
     */
    public function imprimir()
    {
        $tipo = func_get_args()[0];
//        require_once APP_PATH . 'lib/fpdf/fpdf.php';
        $report = new Report("Relatório de Usuários cadastrados");
//        $report->AliasNbPages();
        $report->AddPage();
        $report->SetFont("times", "B", "12");
        $report->Cell(0, 10, 'Relaçao de usuários cadastrados no sistema:', 0, 1);
        $report->SetFont("times", "B", "11");
        $report->SetAligns(array('L', 'L', 'C', 'C'));
        $report->SetWidths(array(75, 50, 27, 38)); //Total 190 para Retrato
        $report->setTableHeader(array('Nome', 'Cargo', 'Dt.Cadastro', 'Último Login'));
        $usuario = new Usuario();
        $fill = false;
        $report->SetFillColor(224, 235, 255);
        $report->SetFont("times", "", "9");
        foreach ($usuario->listarUsuarios($tipo) as $usuario) {
            $ultimo_login = $usuario->getUltimoLogin();
            $report->Row(
                array(
                    $usuario->getPessoa()->getNome(),
                    $usuario->getCargo(),
                    $usuario->getDataCadastro()->format('d/m/Y'),
                    !empty($ultimo_login) ? $ultimo_login->format('d/m/Y H:i:s') : 'Não registrado'
                )
            );
            $fill = !$fill;
        }
        $report->Ln(5);
        $report->Output();
    }

    public function alterarStatus()
    {
        $args = func_get_args();
        $usuario_id = $args[0];
        $ativo = $args[1] == "true" ? 1 : 0;
        try {
            $usuario = (new Usuario())->buscar($usuario_id);
            $usuario->setAtivo($ativo);
            $usuario->atualizar();
            self::setMessage(TipoMensagem::SUCCESS, 'Status de usuário foi alterado com sucesso.');
        }  catch (DBALException $ex) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao alterar status de usuário.");
            $this->registerLogError($ex);
        }  catch (AppException $ex) {
            self::setMessage(TipoMensagem::ERROR, $ex->getMessage());
        } catch (Exception $ex) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao alterar status de usuário. Erro: {$ex->getMessage()}.");
            $this->registerLogError($ex);
        }
        return $this->route($this->class_path, 'visualizar/id/' . $usuario_id);
    }

    public function atualizarPerfil()
    {
        try {
            $usuario = (new Usuario())->buscar(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT));
            $usuario->getPessoa()->setNome(filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING));
            $usuario->getPessoa()->setEmail(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
            $usuario->setCargo(filter_input(INPUT_POST, 'cargo', FILTER_SANITIZE_STRING));
            $usuario->getPessoa()->setTelefone(filter_input(INPUT_POST, 'telefone', FILTER_SANITIZE_STRING));
            $usuario->getPessoa()->setCelular(filter_input(INPUT_POST, 'celular', FILTER_SANITIZE_STRING));
            /*$parametro_ini_path = CONFIG_PATH . 'parametros.ini';
            $parametro['tema'] = $_POST['tema'];
            $parametro['tema_navbar'] = $_POST['tema_navbar'];
            $parametro['cor_faixa'] = $_POST['cor_faixa'];
            Functions::write_ini_file($parametro, $parametro_ini_path, false);*/
            //$this->uploadFoto($usuario);
            $usuario->atualizar();
            $this->atualizarUsuarioLogado();
            self::setMessage(TipoMensagem::SUCCESS, 'Informações atualizadas com sucesso!');
        }  catch (DBALException $ex) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao atualizar informações.");
            $this->registerLogError($ex);
        }  catch (AppException $ex) {
            self::setMessage(TipoMensagem::ERROR, $ex->getMessage());
        } catch (Exception $ex) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao atualizar informações. Erro: {$ex->getMessage()}.");
            $this->registerLogError($ex);
        }
        $this->route();
    }

    function gerarSenha()
    {
        try {
            $usuario = (new Usuario())->buscar(filter_input(INPUT_POST, 'usuario_id'));
            $senha_gerada = Functions::geraSenha(8, true, true);
            $usuario->setSenha(Usuario::codificaSenha($senha_gerada));
           
            
            //Enviar e-mail de boas vindas com a senha gerada
            (new Email())->enviarSenhaUsuario($usuario, $senha_gerada);
            Log::registrarLog(
                TipoLog::ACTION_UPDATE, 
                'usuário', 
                'Uma senha temporária foi enviada para solicitante. Usuário id '.
                $usuario->getId().' nome '.$usuario->getPessoa()->getNome()
            );
            $usuario->atualizar();
            //Enviar e-mail de boas vindas com a senha gerada
            self::setMessage(TipoMensagem::SUCCESS, 'Uma senha temporária foi enviada para solicitante.', null, true);
        } catch (DBALException $ex) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao solicitar uma nova senha.", null, true);
            $this->registerLogError($ex);
        }  catch (AppException $ex) {
            self::setMessage(TipoMensagem::ERROR, $ex->getMessage(), null, true);
        } catch (Exception $ex) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao solicitar uma nova senha. Erro: {$ex->getMessage()}.", null, true);
            $this->registerLogError($ex);
        }
    }


    public function inserir()
    {
        $isAjax = isset($_REQUEST['ajax']);
        try {
            $_POST['dataCadastro'] = new DateTime();
            $_POST['ultimaAlteracao'] = null;
            $usuario = new Usuario();
            $this->setUsuario($usuario);
            (new PessoaController())->setPessoa();
            $usuario = $this->getValues($usuario);
            $usuario_id = $usuario->inserir();
            $usuario->postPersistAndUpdateSetor((new Usuario())->buscar($usuario_id));
            Log::registrarLog(TipoLog::ACTION_INSERT, $usuario->getTableName(), "Registro inserido", null, null, $usuario->imprimir());
            self::setMessage(TipoMensagem::SUCCESS, 'Registro cadastrado com sucesso!', $usuario_id, $isAjax);
            if (!$isAjax) {
                return $this->route($this->class_path);
            }
        } catch (UniqueConstraintViolationException $e) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao inserir registro. Mensagem: registro já cadastrado!", null, $isAjax);
        }  catch (DBALException $e) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao inserir registro. ", null, $isAjax);
            $this->registerLogError($e);
        }  catch (AppException $e) {
            self::setMessage(TipoMensagem::ERROR, $e->getMessage(), null, $isAjax);
        } catch (Exception $e) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao inserir registro. Erro: {$e->getMessage()}.", null, $isAjax);
            $this->registerLogError($e);
        }
        if (!$isAjax) {
            return $this->route($this->class_path, 'cadastrar');
        }
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
            (new PessoaController())->setPessoa();
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
            return $this->route($this->class_path);
        }
    }

    public function excluir()
    {
        $isAjax = isset($_REQUEST['ajax']) ? true : false;
        try {
            $usuario = new $this->classe();
            //$this->verificarExclusaoFoto($usuario->getFoto());
            $args = func_get_args();
            $usuario->remover($args[1]);
            self::setMessage(TipoMensagem::SUCCESS, 'Usuário removido com sucesso!', null, $isAjax);
        } catch (ForeignKeyConstraintViolationException $e) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao remover usuário. Mensagem: este registro está relacionado a outro. Você não pode excluí-lo.", null, $isAjax);
        }  catch (DBALException $e) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao remover usuário. ", null, $isAjax);
            $this->registerLogError($e);
        }  catch (AppException $e) {
            self::setMessage(TipoMensagem::ERROR, $e->getMessage(), null, $isAjax);
        } catch (Exception $e) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao remover usuário. Erro: {$e->getMessage()}.", null, $isAjax);
            $this->registerLogError($e);
        }
        if (!$isAjax) {
            return $this->route($this->class_path, 'index/' . (isset($args[2]) ? $args[2] : ''));
        }
    }



    public function setUsuario(Usuario &$usuario)
    {
        if (!empty($_POST['id'])) {
            $usuario = (new Usuario())->buscar(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT));
            if (!empty($_POST['senha'])) {
                $_POST['senha'] = Usuario::codificaSenha($_POST['senha']);
            } else {
                $_POST['senha'] = $usuario->getSenha();
            }
        } else {
            $_POST['senha'] = Usuario::codificaSenha($_POST['senha']);
            $_POST['ultimoLogin'] = null;
        }
        if (!empty($_POST['nomePastaDigitalizacao'])) {
            $pasta_old = DIGITALIZACAO_PATH . $usuario->getNomePastaDigitalizacao();
            if ($usuario->getNomePastaDigitalizacao() != null && $usuario->getNomePastaDigitalizacao() != $_POST['nomePastaDigitalizacao'] && is_dir($pasta_old)) {
                rmdir($pasta_old);
            }
            $pasta = DIGITALIZACAO_PATH . $_POST['nomePastaDigitalizacao'];
            if (!is_dir($pasta)) {
                $oldmask = umask(0);
                mkdir($pasta, 0777, true);
                umask($oldmask);
            }
        }
        $this->setGrupo($usuario);
        $this->setSetores($usuario);
    }

    private function setGrupo(Usuario $usuario)
    {
        if (!empty($_POST['grupo_id'])) {
            $grupo = (new Grupo())->buscar($_POST['grupo_id']);
            $usuario->setGrupo($grupo);
//            $grupo->adicionaUsuario($usuario);
        }
    }

    private function setSetores(Usuario $usuario)
    {
        if (!empty($_POST['setores_id'])) {
            $setores = new ArrayCollection();
            $antigos_setores = $usuario->getSetoresIds();
            $novos_setores = $_POST['setores_id'];
            foreach ($novos_setores as $setor_id) {
                $setor = (new Setor())->buscar($setor_id);
                $setores->add($setor);
//                $setor->adicionaUsuario($usuario);
//                $usuario->adicionaSetor($setor);
            }
            //Remove vínculo de setores desmarcados
            foreach ($antigos_setores as $setor_id) {
                if (!in_array($setor_id, $novos_setores)) {
                    if(!empty($usuario->getId())){
                        $tramite = new \App\Model\Tramite();
                        $tramites = $tramite->listarTramitesNaoRecebidos($usuario->getId(), $setor_id);
                        foreach($tramites as $tramite){
                            $tramite->setUsuarioDestino(null);
                            $tramite->atualizar();
                        }
                    }
                    $setor = (new Setor())->buscar($setor_id);
                    $setor->removeUsuario($usuario);
                }
            }
            $_POST['setores'] = $setores;
        }
    }

    public function cadastrar()
    {
        $usuario_logado = self::getUsuarioLogado();
        if ($usuario_logado != null) {
            if ($usuario_logado->getTipo() != TipoUsuario::USUARIO && $usuario_logado->getTipo() != TipoUsuario::VISITANTE) {
                return parent::cadastrar();
            }
            return $this->error403();
        }
        return $this->route('login');
    }

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
                        array('link' => $this->class_path, 'title' => $this->getBreadCrumbTitle()),
                        array('link' => null, 'title' => 'Editar')
                    );
                    return $this->load($this->class_path, 'editar');
                }
            }
            return $this->error404();
        }
        return $this->route('login');
    }

    public static function getTipos()
    {
        $options = array();
        $options[""] = "Selecione";
        $options[TipoUsuario::ADMINISTRADOR] = "Administrador";
        $options[TipoUsuario::USUARIO] = "Usuário Comum";
        $options[TipoUsuario::VISITANTE] = "Visitante";
        $usuario_logado = UsuarioController::getUsuarioLogado();
        if ($usuario_logado->getTipo() == TipoUsuario::MASTER) {
            $options[TipoUsuario::MASTER] = "Master";
        }
        return $options;
    }

    /**
     * Método que atualiza a senha do usuário logado
     */
    public function atualizarSenha()
    {
        $args = func_get_args();
        try {
            $usuario = (new Usuario())->buscar(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT));
            $usuario->setSenha(Usuario::codificaSenha(filter_input(INPUT_POST, 'senha')));
//            $usuario->atualizar();
            Log::registrarLog(TipoLog::ACTION_UPDATE, 'usuario', 'Senha atualizada com sucesso. Usuário alterado: '. $usuario->getPessoa()->getNome().' id: '.$usuario->getId() );
            self::atualizarUsuarioLogado();
            self::setMessage(TipoMensagem::SUCCESS, 'Senha atualizada com sucesso.');
        } catch (DBALException $e) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao alterar senha. ");
            $this->registerLogError($e);
        } catch (AppException $e) {
            self::setMessage(TipoMensagem::ERROR, $e->getMessage());
        } catch (Exception $e) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao alterar senha. Erro: {$e->getMessage()}.");
            $this->registerLogError($e);
        }
        if (isset($args[0])) {
            return $this->route($this->class_path, 'visualizar/id/' . $usuario->getId());
        }
        return $this->route();
    }

    /**
     * Método para recuperar senha do usuário
     */
    public static function recuperarSenha()
    {
        $login = $_POST['login'];
        if(preg_match('/^\d{2}.\d{3}\.\d{3}\/\d{4}-\d{2}$/', $login) || preg_match('/^\d{3}.\d{3}\.\d{3}-\d{2}$/', $login)) {
            $login = str_replace(array(".","\\","-"),array(""), $login);
       }
        $usuario = (new Usuario())->buscarporLogin($login);
        try {
            if ($usuario != null) {
                $senha_temp = Functions::geraSenha(8, true, true);
                $usuario->setSenha(Usuario::codificaSenha($senha_temp));
                Log::registrarLog(TipoLog::ACTION_UPDATE, 'usuario', "Sua nova senha foi enviada para o seu e-mail.",$usuario);
                $usuario->atualizar();
                $email = new Email();
                $enviado = $email->enviarSenhaUsuario($usuario, $senha_temp);
                if ($enviado === true) {
                    $msg = "Sua nova senha foi enviada para o seu e-mail.";
                    $tipo = TipoMensagem::SUCCESS;
                } else {
                    throw new Exception($enviado);
                }
            } else {
                $msg = "**E-mail não cadastrado no sistema.";
                $tipo = TipoMensagem::WARNING;
            }
        } catch (DBALException $ex) {
            $msg = "Erro ao enviar senha para o e-mail.";
            $tipo = TipoMensagem::ERROR;
            parent::registerLogError($ex);
        } catch (AppException $ex) {
            $msg = $ex->getMessage();
            $tipo = TipoMensagem::ERROR;
        } catch (Exception $ex) {
            $msg = "Erro ao enviar senha para o e-mail. Erro: {$ex->getMessage()}.";
            $tipo = TipoMensagem::ERROR;
            parent::registerLogError($ex);
        }
        self::setMessage($tipo, $msg, null, true);
    }

    /**
     * Método para verificar se senha atual informada é igual a senha do usuário
     */
    public static function verificarSenha()
    {
        $usuario_logado = self::getUsuarioLogado();
        $senha_informada = Usuario::codificaSenha(filter_input(INPUT_POST, 'senha'));
        if ($senha_informada != $usuario_logado->getSenha()) {
            self::setMessage(TipoMensagem::ERROR, 'Senha atual informada inválida!', null, true);
        } else {
            self::setMessage(TipoMensagem::SUCCESS, 'Senha válida', null, true);
        }
    }

    /**
     * Método para verificar se é permitido desvincular o setor do usuário
     */
    public static  function verificarRemocaoSetor(){
        $usuario_id = filter_input(INPUT_POST, 'usuario_id');
        $setor_id = filter_input(INPUT_POST, 'setor_id');

        $tramite = new \App\Model\Tramite();
        $tramites = $tramite->listarTramitesNaoRecebidos($usuario_id, $setor_id);

        if(count($tramites) > 0 ){
            self::setMessage(TipoMensagem::INFO, " Este usuário está como destinatário em um ou mais trâmites não recebidos, "
                . " ao desvincular o setor deste usuário o sistema desvinculará "
                . " o usuário dos trâmites não recebidos. Deseja continuar?",null, true);

            return;
        }
        self::setMessage(TipoMensagem::SUCCESS, "",null,true);
    }

    public function buscar() {
        $dao = new UsuarioDao();
        if (!empty($_GET["search"])) {
            $termo = "%" . $_GET["search"] . "%";
            $campos = ["id", "text" => "nome"];
            $resultado = $dao->pesquisarPorNome($termo, $campos, AbstractQuery::HYDRATE_ARRAY);
        } else {
            $termo = "%%";
            $campos = ["id", "text" => "nome"];
            $resultado = $dao->pesquisarPorNome($termo, $campos, AbstractQuery::HYDRATE_ARRAY);
        }
        echo json_encode(["results" => $resultado, "pagination" => ["more" => false]], JSON_PRETTY_PRINT);
    }
}
