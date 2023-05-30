<?php

const APP_PATH = __DIR__ . '/';

require_once APP_PATH . "vendor/autoload.php";

set_time_limit(0);
ini_set("memory_limit", "2048M");
//ini_set("log_errors_max_len",0);
header('Content-Type: text/html; charset=UTF-8');

use App\Controller\IndexController;
use App\Controller\UsuarioController;
use Core\Controller\AppController;
use Core\Util\Functions;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Settings;

setlocale(LC_ALL, "pt_BR", "pt_BR.iso-8859-1", "pt_BR.utf-8", "portuguese");
date_default_timezone_set("America/Campo_Grande");

const FILE_PATH = APP_PATH . '_files/';
const DIGITALIZACAO_PATH = FILE_PATH . "processos/temp/_digitalizacao/";
const CACHE_PATH = APP_PATH . '_cache/';
const CONFIG_PATH = APP_PATH . '_config/';
const VIEW_PATH = APP_PATH . 'src/App/View/';
const APP_MODE = \Core\Enum\AppMode::MODE_DEVELOP;
const ATTACH_TEMP = FILE_PATH . 'processos/temp/';
const K_TCPDF_THROW_EXCEPTION_ERROR = true;

$scheme = AppController::getConfig('ssl') ? 'https://' : 'http://';
if (isset($_SERVER['HTTP_HOST'])) {
    $httpHost = explode(':', $_SERVER['HTTP_HOST']);
    $appUrl = $scheme . preg_replace('/[^a-zA-Z0-9.]/i', '', $httpHost[0]) ;
    $appUrl .= count($httpHost)>1? ':'.$httpHost[1].'/':'';
    $appUrl .= str_replace('\\', '/', substr(dirname(__FILE__), strlen($_SERVER['DOCUMENT_ROOT']))) .'/';
    define('APP_URL', $appUrl);
}
/* Tratamento centralizado de todas as sessões criadas no sistema */
if (!isset($_SESSION)) {
    $config = IndexController::getConfig();
    $session_name = UsuarioController::getSessionId();
    $secure = $config['app_secure'];
    // Isso impede que o JavaScript possa acessar a identificação da sessão.
    $httponly = true;
    // Assim força a sessão a usar apenas ‘cookies’.
    if (ini_set('session.use_only_cookies', 1) === FALSE) {
        die("Could not initiate a safe session (ini_set)");
    }
    // Obtém params de ‘cookies’ atualizados.
    $cookieParams = session_get_cookie_params();
    session_set_cookie_params($cookieParams["lifetime"], $cookieParams["path"], $cookieParams["domain"], $secure, $httponly);
    // Estabelece o nome fornecido acima como o nome da sessão.
    session_name($session_name);
    session_start();            // Inicia a sessão PHP
    session_regenerate_id();    // Recupera a sessão e deleta a anterior. 
}
if (!file_exists(FILE_PATH)) {
     mkdir(FILE_PATH);
}
if (!file_exists(FILE_PATH . "templates")) {
    mkdir(FILE_PATH . "templates");
}
if (!file_exists(FILE_PATH . "documentos")) {
    mkdir(FILE_PATH . "documentos");
}
if (!file_exists(FILE_PATH . "documentos" . DIRECTORY_SEPARATOR . "temp")) {
    mkdir(FILE_PATH . "documentos" . DIRECTORY_SEPARATOR . "temp");
}
const DIRETORIO_DOCUMENTOS_REMOVIDOS = FILE_PATH . "documentos" . DIRECTORY_SEPARATOR . "removidos" . DIRECTORY_SEPARATOR;
if (!file_exists(DIRETORIO_DOCUMENTOS_REMOVIDOS)) {
    mkdir(DIRETORIO_DOCUMENTOS_REMOVIDOS, "0766");
}
const LOG_PATH = FILE_PATH . "_log" . DIRECTORY_SEPARATOR;
if (!file_exists(LOG_PATH)) {
    mkdir(LOG_PATH, 0733);
} else if (!is_writable(LOG_PATH) || !is_executable(LOG_PATH)) {
    try {
        chmod(LOG_PATH, 0733);
    } catch (Exception $exc) {
        $log = "##################################################################################################" . PHP_EOL;
        $log = $exc->getMessage() . PHP_EOL;
        $log .= "##################################################################################################" . PHP_EOL;
        Functions::escreverLogErro($log);
        Functions::escreverLogErro($exc->getTraceAsString());
    }
}

// Set PDF renderer.
Settings::setPdfRendererName(Settings::PDF_RENDERER_MPDF);
Settings::setPdfRendererPath(APP_PATH . "vendor/mpdf/mpdf");
Settings::setTempDir("/tmp");