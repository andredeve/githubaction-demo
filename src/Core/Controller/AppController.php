<?php /** @noinspection PhpIncludeInspection */

namespace Core\Controller;

use App\Controller\UsuarioController;
use App\Enum\TipoLog;
use App\Enum\TipoUsuario;
use App\Model\Log;
use App\Model\PermissaoEntidade;
use App\Model\Usuario;
use Core\Enum\TipoMensagem;
use Core\Exception\BusinessException;
use Core\Model\AppModel;
use Core\Util\Messages;
use Core\Util\Upload;
use DateTime;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Exception;
use ReflectionClass;
use Doctrine\DBAL\DBALException;
use Core\Exception\AppException;
use Core\Exception\SecurityException;


use Smarty;
use SmartyException;
use const APP_PATH;
use const APP_URL;
use const CONFIG_PATH;
use const VIEW_PATH;

/**
 * Classe genérica controle da aplicação
 * @version 1.0
 * @author Anderson Brandão <batistoti@gmail.com>
 */
abstract class AppController
{

    /**
     * Classe filha
     * @var $classe string objeto da classe filha
     */
    protected $classe;
    protected $class_path;
    public $breadcrumb;

    /**
     * @var $text_method string Nome do método de texto da classe filha
     */
    protected $text_method;
    protected $list_method;

    /**
     * Variável que armazena os campos da classe filha
     * @var $fields array
     */
    private $fields;

    /**
     * Variável que armazena os métodos 'set' da classe filha
     * @var $set_methods array
     */
    private $set_methods;

    /**
     * Construtor da classe
     * @param string $classe
     */
    function __construct($classe = null)
    {
        if ($classe != null) {
            $classe = str_replace('Controller', '', str_replace('App\Controller\\', '', $classe));
            $this->class_path = $classe;
            $this->classe = 'App\Model\\' . $classe;
            if (class_exists($this->classe)) {
                $api = new ReflectionClass($this->classe);
                $this->fields = $api->getDefaultProperties();
                $this->setParameters();
            }
        }
    }

    public function index()
    {
        $this->listar();
        $this->load($this->class_path);
    }

    function compararObjetos($objeto1, $objeto2)
    {
        $antes = $depois = null;
        $atributos_alterados = array();
        foreach ($this->fields as $atributo => $valor) {
            if ($atributo != 'ultimaAlteracao') {
                $getMethod = 'get' . ucfirst($atributo);
                if ($objeto1->$getMethod() != $objeto2->$getMethod()) {
                    $atributos_alterados[] = $atributo;
                }
            }
        }
        $alterou = count($atributos_alterados) > 0;
        if ($alterou) {
            $antes = $objeto1->imprimir($atributos_alterados);
            $depois = $objeto2->imprimir($atributos_alterados);
        }
        return array('alterou' => $alterou, 'antes' => $antes, 'depois' => $depois);
    }

    
    function atualizarAtributo()
    {
        try {
            $permissao = $this->getPermissao();
            $prosseguir = !$permissao instanceof PermissaoEntidade || $permissao->getEditar();
            if ($prosseguir) {
                $objeto = (new $this->classe())->buscar($_POST['objeto_id']);
                $setMethod = 'set' . ucfirst($_POST['atributo']);
                $objeto->$setMethod($_POST['valor']);
                $objeto->atualizar();
                self::setMessage(TipoMensagem::SUCCESS, "Registro atualizado com sucesso.", null, true);
            } else {
                self::setMessage(TipoMensagem::WARNING, "Você não tem permissão para realizar essa ação.", null, true);
            }
        }  catch (DBALException $ex) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao atualizar registro.", null, true);
            self::registerLogError($ex);
        }  catch (AppException $ex) {
            self::setMessage(TipoMensagem::ERROR, $ex->getMessage(), null, true);
        } catch (Exception $ex) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao atualizar registro. Erro: {$ex->getMessage()}.", null, true);
            self::registerLogError($ex);
        }
    }


    /**
     * Método genérico de listagem de registros de um objeto/tabela
     */
    public function listar()
    {
        $object = new $this->classe();
        $_REQUEST['breadcrumb'] = array(array('link' => null, 'title' => $this->getBreadCrumbTitle()));
        $_REQUEST['registros'] = $object->listar();
    }

    /**
     * Busca a permissão para entidade atual
     * @return boolean|PermissaoEntidade
     */
    protected function getPermissao()
    {
        $usuario_logado = UsuarioController::getUsuarioLogadoDoctrine();
        if ($usuario_logado != null && $usuario_logado->getTipo() != TipoUsuario::MASTER) {
            if($usuario_logado->getTipo() == TipoUsuario::INTERESSADO && in_array(PermissaoEntidade::getCodigo($this->class_path), array(1,6))){
                $permissao = new PermissaoEntidade();
                $permissao->setEditar(true);
                $permissao->setExcluir(false);
                $permissao->setInserir(true);
                return $permissao;
            }
            return $usuario_logado->getPermissoesEntidade(PermissaoEntidade::getCodigo($this->class_path));
        }
        return false;
    }

    public function cadastrar()
    {
        $this->redirectUserNotLogged();
        $usuarioEhInteressado = UsuarioController::isInteressado();
        $_REQUEST['breadcrumb'] = array(
            array('link' => $usuarioEhInteressado ? 'Contribuinte' : $this->class_path, 'title' => $this->getBreadCrumbTitle()),
            array('link' => null, 'title' => 'Cadastrar')
        );
        $permissao = $this->getPermissao();
        $usuario =  UsuarioController::getUsuarioLogado();
        if($usuario->getTipo() == TipoUsuario::MASTER){
            $prosseguir = true;
        }else{
            $prosseguir = !$permissao instanceof PermissaoEntidade || $permissao->getInserir();
        }

        if ($prosseguir) {
            $this->load($this->class_path, 'cadastrar');
        } else {
            $this->error403();
        }
    }

    /**
     * Método genérico de inserção no banco de dados
     */
    public function editar()
    {
        try {
            $permissao = $this->getPermissao();
            $usuario =  UsuarioController::getUsuarioLogado();
            if($usuario->getTipo() == TipoUsuario::MASTER){
                $prosseguir = true;
            }else{
                $prosseguir = !$permissao instanceof PermissaoEntidade || $permissao->getEditar();
            }
            if ($prosseguir) {
                $object = new $this->classe();
                $args = func_get_args();
                if (empty($args[1])) {
                    return $this->error404();
                }
                $_REQUEST['objeto'] = $object->buscar($args[1]);

                if ($_REQUEST['objeto'] == null) {
                    return $this->error404();
                }
                $_REQUEST['breadcrumb'] = array(
                    array('link' => $this->class_path, 'title' => $this->getBreadCrumbTitle()),
                    array('link' => null, 'title' => 'Editar')
                );
                return $this->load($this->class_path, 'editar');
            }
            $this->error403();
            return false;
        } catch (DBALException $e) {
            self::registerLogError($e);
            die("Erro ao editar registro. ");
        } catch (AppException $e) {
            die($e->getMessage());
        } catch (Exception $e) {
            self::registerLogError($e);
            die("Erro ao editar registro. Erro: {$e->getMessage()}.");
        }
    }

    /**
     * Método genérico de remoção no banco de dados
     */
    public function inserir()
    {
        $isAjax = isset($_REQUEST['ajax']);
        try {
            $_POST['dataCadastro'] = new DateTime();
            $_POST['ultimaAlteracao'] = null;
            $object = new $this->classe();
            $this->getValues($object);
            if (method_exists($this, 'setEntidade')) {
                $this->setEntidade($object);
            }
            $objeto_id = $object->inserir();
            self::setMessage(TipoMensagem::SUCCESS, 'Registro cadastrado com sucesso!', $objeto_id, $isAjax);
            Log::registrarLog(TipoLog::ACTION_INSERT, $object->getTableName(), "Registro inserido", UsuarioController::getUsuarioLogadoDoctrine(), $object->imprimir());
            if (!$isAjax) {
                $this->route($this->class_path);
                return;
            }
        } catch (UniqueConstraintViolationException $e) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao inserir registro. Mensagem: registro já cadastrado!", null, $isAjax);
        } catch (DBALException $e) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao inserir registro. ", null, $isAjax);
            self::registerLogError($e);
        }catch (BusinessException $e) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao inserir registro. Erro: {$e->getMessage()}", null, $isAjax);
            self::registerLogError($e);
        } catch (Exception $e) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao inserir registro. Erro: {$e->getMessage()}.", null, $isAjax);
            self::registerLogError($e);
        }
        if (!$isAjax) {
            $this->route($this->class_path, 'cadastrar');
        }
    }

    public function excluir()
    {
        $isAjax = isset($_REQUEST['ajax']);
        try {
            $permissao = $this->getPermissao();
            $usuario =  UsuarioController::getUsuarioLogado();
            if($usuario->getTipo() == TipoUsuario::MASTER){
                $prosseguir = true;
            }else{
                $prosseguir = $permissao instanceof PermissaoEntidade ? $permissao->getExcluir() : true;
            }
            if (!$prosseguir) {
                throw new SecurityException("Você não têm permissão para realizar essa ação.");
            }
            $object = new $this->classe();
            $args = func_get_args();
            $object->remover($args[1]);
            Log::registrarLog(TipoLog::ACTION_DELETE, $object->getTableName(), "Registro deletado", null, $object->imprimir());
            self::setMessage(TipoMensagem::SUCCESS, 'Registro removido com sucesso!', null, $isAjax);
        } catch (ForeignKeyConstraintViolationException $e) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao remover registro. Mensagem: este registro está relacionado a outro. Você não pode excluí-lo.", null, $isAjax);
            self::registerLogError($e);
        } catch (DBALException $e) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao remover registro. ", null, $isAjax);
            self::registerLogError($e);
        } catch (AppException $e) {
            self::setMessage(TipoMensagem::ERROR, $e->getMessage(), null, $isAjax);
        } catch (Exception $e) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao remover registro. Erro: {$e->getMessage()}.", null, $isAjax);
            self::registerLogError($e);
        }
        if (!$isAjax) {
            $this->route($this->class_path);
        }
    }

    function converter(){
        $isAjax = isset($_REQUEST['ajax']);
        $objeto_id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
        $args = func_get_args();
        try {
            $usuario =  UsuarioController::getUsuarioLogado();
            $_POST['ultimaAlteracao'] = new DateTime();
            $_POST['transformar'] = 1;
            $object = new $this->classe();
            $object = $object->buscar($args[1]);
            $object->beginTransaction();
            $object->setUsuario();
            $object->atualizar();
            $object->commit();
            Log::registrarLog(TipoLog::ACTION_UPDATE, $object->getTableName(), "Registro convertido", null, $object->imprimir());
            self::setMessage(TipoMensagem::SUCCESS, 'Registro convertido com sucesso!', null, $isAjax);
        } catch (ForeignKeyConstraintViolationException $e) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao converter registro. Mensagem: este interessado já é um contribuinte", null, $isAjax);
            self::registerLogError($e);
        } catch (DBALException $e) {
            self::setMessage(TipoMensagem::ERROR, "Interessado já é um contribuinte", null, $isAjax);
            self::registerLogError($e);
        } catch (AppException $e) {
            self::setMessage(TipoMensagem::ERROR, $e->getMessage(), null, $isAjax);
        } catch (Exception $e) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao converter registro. Erro: {$e->getMessage()}.", null, $isAjax);
            self::registerLogError($e);
        }
        if (!$isAjax) {
            $this->route($this->class_path);
        }
    }

    protected function route($controller = null, $method = null, $args = null)
    {
        $args_url = "";
        $separator = "";
        if ($args != null) {
            foreach ($args as $arg) {
                $args_url .= $separator . $arg;
                $separator = "/";
            }
        }
        $controller = $controller != null ? $controller : '';
        $method = $method != null ? '/' . $method : '';
        $args_url = $args != null ? '/' . $args_url : '';
        header('location:' . APP_URL . "$controller$method$args_url");
    }

    /**
     * Método genérico de atualização no bannetco de dados
     */
    public function atualizar()
    {
        $isAjax = isset($_REQUEST['ajax']);
        $objeto_id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
        try {
            $_POST['ultimaAlteracao'] = new DateTime();
            $object = new $this->classe;
            $object = $object->buscar($objeto_id);
            $this->getValues($object);
            self::setMessage(TipoMensagem::SUCCESS, 'Registro atualizado com sucesso!', null, $isAjax);
            $object->atualizar();
            if (!$isAjax) {
                $this->route($this->class_path);
                return;
            }           
        } catch (UniqueConstraintViolationException $ex) {
            self::setMessage(TipoMensagem::ERROR, "Registro já cadastrado.", null, true);
        }catch (DBALException $e) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao atualizar registro.", null, $isAjax);
            self::registerLogError($e);
        } catch (AppException $e) {
            self::setMessage(TipoMensagem::ERROR, $e->getMessage(), null, $isAjax);            
        } catch (Exception $e) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao atualizar registro. Erro: {$e->getMessage()}.", null, $isAjax);
            self::registerLogError($e);
        }
        if (!$isAjax) {
            $this->route($this->class_path, 'editar/id/' . $objeto_id);
        }
    }

    /**
     * Método que seta todos os valores dos atributos de
     * uma classe filha, tratando os valores $_POST do formulário
     * @param AppModel $object
     */
    protected function getValues($object)
    {
        foreach ($this->set_methods as $method) {
            try {
                if (isset($_REQUEST[$method['campo']]) || isset($_POST[$method['campo']])) {
                    $setMethod = $method['metodo'];
                    if (method_exists($object, $setMethod)) {
                        $object->$setMethod($this->tratarVariavel($method['campo']));   
                    }
                } else if (isset($_REQUEST[$this->toSnakeCase($method['campo'])]) || isset($_POST[$this->toSnakeCase($method['campo'])])) {
                    $setMethod = $method['metodo'];
                    if (method_exists($object, $setMethod)) {
                        $object->$setMethod($this->tratarVariavel($this->toSnakeCase($method['campo'])));
                    }
                }
            } catch (DBALException $e) {
                self::registerLogError($e);
                die("Variável " . $method['campo'] . " inválida!. ");
            } catch (AppException $e) {
                die($e->getMessage());
            } catch (Exception $e) {
                self::registerLogError($e);
                die("Variável " . $method['campo'] . " inválida!. Erro: {$e->getMessage()}.");
            }
        }
        return $object;
    }

    /**
     * @param string $input
     * @return string string
     */
    protected function toSnakeCase($input) {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $input));
    }

    /**
     * Método que trata uma variável $_POST
     * @param string $valor
     * @return string
     */
    protected function tratarVariavel($valor)
    {
        return $_POST[$valor] !== ""? $_POST[$valor]:null ;
    }

    /**
     * Método setParameters()
     * Atribui os parametros da classe ao vetor columns
     * e set as métodos set
     */
    private function setParameters()
    {
        foreach ($this->fields as $field => $res) {
            $this->set_methods[] = array('campo' => $field, 'metodo' => "set" . ucfirst($field));
        }
    }

    /**
     * Método que adiciona uma mensagem
     * @param string $tipo
     * @param string $mensagem
     */
    public static function setMessage($tipo, $mensagem, $objeto_id = null, $ajax = false)
    {
        if ($ajax) {
            echo json_encode(array('msg' => ($mensagem), 'tipo' => $tipo, 'objeto_id' => $objeto_id), JSON_PRETTY_PRINT);
        } else {
            $msg = new Messages();
            $msg->add($tipo, $mensagem);
        }
    }

    /**
     * Função que retorna um array para popular select smarty
     * de uma determinada classe
     * @return array
     */
    public function getOptions()
    {
        $obj = new $this->classe();
        $list_method = empty($this->list_method) ? "listar" : $this->list_method;
        $options = array();
        foreach ($obj->$list_method() as $obj) {
            $value = (int)$obj->getId();
            $text = strval($obj);
            $options[$value] = $text;
        }
        return $options;
    }

    protected function deteleFile($dir, $arquivo)
    {
        if (!is_array($arquivo)) {
            unlink($dir . $arquivo);
        }
    }

    /**
     * @throws Exception
     */
    protected function uploadFile($dir, $input_name, $arquivo_atual = null)
    {
        if (!empty($_FILES[$input_name]['name'])) {
            $this->deteleFile($dir, $arquivo_atual);
            $_POST[$input_name] = (new Upload($input_name, $dir))->upload();
            return $_POST[$input_name];
        }
        return null;
    }

    protected function getBreadCrumbTitle()
    {
        return empty($this->breadcrumb) ? $this->class_path : $this->breadcrumb;
    }

    /**
     * @throws SmartyException
     */
    public function error403()
    {
        $this->load('Public', '403');
    }

    /**
     * @throws SmartyException
     */
    public function error404()
    {
        return $this->load('Public', '404');
    }

    /**
     * @throws SmartyException
     */
    public function error401()
    {
        return $this->load('Public', '401');
    }

    /*
     * Metódo usado para carregar modal ou outra tela sem os menus e outras coisas default 
     */
    public function loadSemTemplate($path, $archive, $php_file = null){
        $path = ucfirst($path);
        require_once APP_PATH . '_config/smarty.config.php';
        /** @noinspection PhpUndefinedVariableInspection */
        $smarty->template_dir = VIEW_PATH . "$path/Templates/";
        $php_file = $php_file?:VIEW_PATH . "$path/$archive.php";
        require_once $php_file;
        $smarty->display($archive.'.tpl');
    }

    /**
     * @throws SmartyException
     */
    public function load($path, $archive = null, $path_view = true, $external = false, array $vars = null, $processo_externo = false, $load_base = true)
    {
        $path = ucfirst($path);
        $archive = $archive != null ? $archive : 'index';
        $is_index = ($path == 'Public' && $archive == 'index');
        require_once APP_PATH . '_config/smarty.config.php';
        /** @noinspection PhpUndefinedVariableInspection
         *  @var Smarty $smarty
         */
        $smarty->setTemplateDir(VIEW_PATH . "Public/Templates/");
        $php_file = $path_view ? VIEW_PATH . "$path/$archive.php" : $path;
        //Se ação for cadastrar ou editar, chama o template formulario, se não chama o da acao correspondente
        $template_file = $archive == 'cadastrar' || $archive == 'editar' || $archive == 'converter' ? 'formulario' : $archive;
        $template_path = $is_index ? VIEW_PATH . "$path/Templates/home.tpl" : VIEW_PATH . "$path/Templates/$template_file.tpl";
        if (is_file($template_path)) {
            if (is_readable($php_file)) {
                $this->display($path, $archive, $smarty, $template_path, $is_index, $php_file, $external, $vars, $processo_externo, $load_base);
                return true;
            } else {
                $this->error404();
                return false;
            }
        } else {
            die("Erro: Template não encontrado em: $template_path");
        }
    }

    /**
     * Imprime o conteúdo do site
     * @throws SmartyException
     */
    private function display(string $path, string $archive, Smarty $smarty, string $template_path, bool $is_index, string $php_file, $external, array $vars = null, $processo_externo = false, $load_base = true)
    {
        $msg = new Messages();
        $smarty->assign('messages', $msg->display('all', false));
        if (!empty($vars)) {
            foreach ($vars as $key => $var) {
                $smarty->assign($key, $var);
            }
        }
        if ($archive == 'login') {
            require_once(VIEW_PATH . 'Public/' . $archive . '.php');
            $smarty->display($archive . '.tpl');
        } elseif (!$this->isAcessoPublico($path, $archive) && !UsuarioController::isLogado() && !$processo_externo) {
            if (!UsuarioController::isLogado()) {
                $_SESSION['actual_link_' . UsuarioController::getSessionName()] = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            }
            self::setMessage(TipoMensagem::WARNING, 'Área restrita. Insira sua credencias para acessar.');
            $this->route('login');
        } else {

            $smarty->assign('conteudo', $template_path);
            $smarty->assign('processo_externo', $processo_externo);
            if (!$is_index && $load_base) {
                require_once VIEW_PATH . 'Public/index.php';
            }
            require_once $php_file;
            if ($load_base) {
                $smarty->display('index.tpl');
            }
        }
    }

    /**
     * Verificar se a tela atual é de livre acesso
     * @param string $path
     * @param string $archive
     * @return boolean
     */
    private function isAcessoPublico($path, $archive)
    {
        $path = strtolower($path);
        $acessos = array(
            'public' => array('404', 'consulta', 'processo'),
        );
        foreach ($acessos as $entidade => $acoes) {
            if ($path == $entidade && in_array($archive, $acoes)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Função que retorna o nome do controller de uma entidade
     * @param string $entidade
     * @return string
     */
    public static function getControllerName($entidade)
    {
        $controller = str_replace(" ", "", ucwords(str_replace('-', " ", $entidade)));
        return $controller . "Controller";
    }
    
    public static function getConversaoConfig($param = null){
        $parametros = parse_ini_file(CONFIG_PATH . 'conversao.ini');
        if ($param != null) {
            return $parametros[$param];
        }
        return $parametros;
    }
    
    public static function getParametosConfig($param = null)
    {
        $parametros = parse_ini_file(CONFIG_PATH . 'parametros.ini');
        if ($param != null) {
            return $parametros[$param];
        }
        return $parametros;
    }

    public static function getParametrosDefaultConfig()
    {
        return parse_ini_file(CONFIG_PATH . 'parametros_default.ini');
    }

    public static function getClienteConfig($name = null)
    {
        if (is_null($name)) {
            return parse_ini_file(CONFIG_PATH . 'cliente.ini');
        }
        $config = parse_ini_file(CONFIG_PATH . 'cliente.ini');
        if (isset($config[$name])) {
            return $config[$name];
        }
        return false;
    }

    public static function getMailConfig()
    {
        return parse_ini_file(CONFIG_PATH . 'mail.ini');
    }

    /**
     * @param $name
     * @return array|null|string
     */
    public static function getConfig($name = null)
    {
        if (is_null($name)) {
            return parse_ini_file(CONFIG_PATH . 'app.ini');
        }
        $config = parse_ini_file(CONFIG_PATH . 'app.ini');
        if (isset($config[$name])) {
            return $config[$name];
        }
        return false;
    }
    
    public static function getConfigImportacao()
    {
        return parse_ini_file(CONFIG_PATH . 'importacao.ini');
    }

    public static function getDatabaseConfig()
    {
        return parse_ini_file(CONFIG_PATH . 'database.ini');
    }

    public static function sistemaSomenteLeitura()
    {
        if(isset(AppController::getConfig()['somente_leitura']) && !empty(AppController::getConfig()['somente_leitura'])){
            switch (AppController::getConfig()['somente_leitura']){
                case 'true':
                case 1:
                    return true;
                default:
                    return false;
            }
        }else{
            return false;
        }
    }
    public static function contribuinteHabilitado()
    {
        if(isset(AppController::getConfig()['habilita_contribuinte']) && !empty(AppController::getConfig()['habilita_contribuinte'])){
            switch (AppController::getConfig()['habilita_contribuinte']){
                case 'true':
                case 1:
                    return true;
                default:
                    return false;
            }
        }else{
            return false;
        }
    }
    public static function processosSaoSigilosos(): bool
    {
        if(isset(AppController::getParametosConfig()['processos_sao_sigilosos']) && !empty(AppController::getParametosConfig()['processos_sao_sigilosos'])){
            switch (AppController::getParametosConfig()['processos_sao_sigilosos']){
                case 'true':
                case 1:
                    return true;
                default:
                    return false;
            }
        }else{
            return false;
        }
    }

    /**
     * Erro a ser registrado no arquivo de log, normalmente definido no php.ini.
     * 
     * @param Exception $exception
     */
    public static function registerLogError($exception)
    {
        error_log($exception->getMessage());
        error_log($exception->getTraceAsString());
    }

    public function desativar()
    {
        $isAjax = isset($_REQUEST['ajax']);
        try {
            $permissao = $this->getPermissao();
            $usuario =  UsuarioController::getUsuarioLogado();
            if($usuario->getTipo() == TipoUsuario::MASTER){
                $prosseguir = true;
            }else{
                $prosseguir = $permissao instanceof PermissaoEntidade ? $permissao->getExcluir() : true;
            }
            if (!$prosseguir) {
                throw new SecurityException("Você não têm permissão para realizar essa ação.");
            }
            $object = new $this->classe();
            $args = func_get_args();
            $object->desativar($args[1]);
            Log::registrarLog(TipoLog::ACTION_DELETE, $object->getTableName(), "Registro desativado.", null, $object->imprimir());
            self::setMessage(TipoMensagem::SUCCESS, 'Registro desativado com sucesso!', null, $isAjax);
        } catch (BusinessException $e) {
            self::setMessage(TipoMensagem::ERROR, $e->getMessage(), null, $isAjax);
        } catch (Exception $e) {
            self::setMessage(TipoMensagem::ERROR, "Falha ao desativar registro. Por favor, contate o suporte.", null, $isAjax);
            self::registerLogError($e);
        }
        if (!$isAjax) {
            $this->route($this->class_path);
        }
    }

    function reativar() {
        try {
            $args = func_get_args();
            $id = $args[1];
            $entidade = new $this->classe();
            $entidade = $entidade->buscar($id);
            $entidade->reativar($id);
            self::setMessage(TipoMensagem::SUCCESS, 'Registro reativado.', null, true);
        } catch (BusinessException $ex) {
            self::setMessage(TipoMensagem::SUCCESS, $ex->getMessage(), null, true);
        } catch (Exception $ex) {
            self::setMessage(TipoMensagem::SUCCESS, 'Ocorreu uma falha. Por favor, contate o suporte.', null, true);
        }
    }

    function getUsuario(): ?Usuario {
        return UsuarioController::getUsuarioLogadoDoctrine();
    }

    /**
     * @return void
     */
    protected function redirectUserNotLogged(): void
    {
        if (!UsuarioController::isLogado()) {
            $this->route('Login');
        }
    }

    public function loginValido(): bool {
        $usuario = UsuarioController::getUsuarioLogadoDoctrine();
        return !is_null($usuario) && $usuario->getAtivo();
    }
}
