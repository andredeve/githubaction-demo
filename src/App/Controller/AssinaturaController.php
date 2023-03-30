<?php

namespace App\Controller;

use App\Enum\TipoHistoricoAnexo;
use App\Exception\LxSignException;
use App\Log\HistoricoAnexo;
use App\Model\Anexo;
use App\Model\Assinatura;
use App\Model\Usuario;
use Core\Controller\AppController;
use Core\Enum\TipoMensagem;
use Core\Exception\AppException;
use Core\Exception\BusinessException;
use Core\Exception\SecurityException;
use Core\Exception\TechnicalException;
use Core\Util\Functions;
use Core\Util\Http\Client\Builder;
use Core\Util\Http\Client\ContentType;
use Core\Util\Http\HTTP_METHOD;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\TransactionRequiredException;
use Exception;

/**
 * Classe AssuntoController
 * @version 1.0
 * @author Anderson Brandão Batistoti <anderson@lxtec.com.br>
 * @date   08/01/2018
 * @copyright (c) 2018, Lxtec Informática
 */
class AssinaturaController extends AppController
{

    private $urlLxSign;
    private $token;

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     * @throws SecurityException
     * @throws TransactionRequiredException
     * @throws BusinessException
     */
    public function __construct()
    {
        parent::__construct(get_class());
        $app =  AppController::getConfig();
        $this->urlLxSign = $app["lxsign_url"];
        $this->text_method = "getDescricao";
        $this->list_method = "listarAtivos";
        $this->token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJjbnBqIjoiMDMuNDM0Ljc5MlwvMDAwMS0wOSJ9.zPJLb7eQwyXL3qbIQdcG_fZTjTEoNh1ha6h1P-t3vYw';

        $_REQUEST["toker_user"] = null;
        if(!isset($_SESSION["execucao_script"]) || !$_SESSION["execucao_script"]) {
            $usuario_logado = UsuarioController::getUsuarioLogadoDoctrine();
            if (is_null($usuario_logado)) {
                throw new BusinessException("Usuário não autenticado.");
            }
            $usuario = new Usuario();
            $usuario = $usuario->buscar($usuario_logado->getId());
            if (is_null($usuario->getToken())) {
                try {
                    $usuario->setToken();
                    $usuario->atualizar(false);
                } catch (BusinessException $e) {
                    error_log($e->getMessage());
                    self::setMessage(TipoMensagem::ERROR, $e->getMessage());
                }
            }
            $_REQUEST["toker_user"] = $usuario->getToken();
        }

    }

    /**
     * Método para imprimir lista de usuário cadastrados
     */
    public function signatarios(){
        $_REQUEST["url_lxsign"] = $this->urlLxSign."signatario";
        $this->load($this->class_path);
    }

    public function emProcesso(){
        $_REQUEST["url_lxsign"] = $this->urlLxSign."documento/index/em-processo";
        $this->load($this->class_path);
    }

    public function finalizados(){
        $_REQUEST["url_lxsign"] = $this->urlLxSign."documento/index/finalizado";
        $this->load($this->class_path);
    }

    public function usuarios(){
        $_REQUEST["url_lxsign"] = $this->urlLxSign."usuario";
        $this->load($this->class_path);
    }

    public function gruposSignarios(){
        $_REQUEST["url_lxsign"] = $this->urlLxSign."grupoSignatario";
        $this->load($this->class_path);
    }
    public function empresa(){
        $_REQUEST["url_lxsign"] = $this->urlLxSign."empresa";
        $this->load($this->class_path);
    }
    public function modelosDocumento(){
        $_REQUEST["url_lxsign"] = $this->urlLxSign."modeloDocumento";
        $this->load($this->class_path);
    }
    public function tiposDocumento(){
        $_REQUEST["url_lxsign"] = $this->urlLxSign."tipoDocumento";
        $this->load($this->class_path);
    }

    public function cadastrar() {
        $usuario_id = func_get_args()[1];
        $_REQUEST["usuario_id"] = $usuario_id;
        parent::cadastrar();
    }

    public function sair() {
        $_REQUEST["url_lxsign"] = $this->urlLxSign."usuario/sair";
        $this->load($this->class_path);
    }

    public function listarGruposAssinatura(){
        $url = $this->urlLxSign. "GrupoSignatario/api/listar";
        return (new Builder($url))
            ->setParameters(['access_token' => $this->token])
            ->addHeader('Accept: application/json')
            ->setMethod(HTTP_METHOD::GET)
            ->verifySSL(false)
            ->build()
            ->send()
            ->getBody()
            ->toObject();
    }

    public function listarSignatarios(){
        $url = $this->urlLxSign. "signatario/restAPI/listar";
        return (new Builder($url))
            ->setMethod(HTTP_METHOD::POST)
            ->setParameters(['access_token' => $this->token])
            ->addHeader('Accept: application/json')
            ->verifySSL(false)
            ->build()
            ->send()
            ->getBody()
            ->toObject();
    }

    public function listarTiposDocumetos(){
        $url = $this->urlLxSign. "TipoDocumento/api/listar";
        return (new Builder($url))
            ->setMethod(HTTP_METHOD::GET)
            ->setParameters(['access_token' => $this->token])
            ->addHeader('Accept: application/json')
            ->verifySSL(false)
            ->build()
            ->send()
            ->getBody()
            ->toObject();
    }

    public function listarEmpresas(){
        $url = $this->urlLxSign. "empresa/api/listar";
        return (new Builder($url))
            ->setMethod(HTTP_METHOD::GET)
            ->setParameters(['access_token' => $this->token])
            ->addHeader('Accept: application/json')
            ->verifySSL(false)
            ->build()
            ->send()
            ->getBody()
            ->toObject();
    }

    public function cadastrarSignatario(){
        $_REQUEST["url_lxsign"] = $this->urlLxSign."signatario/cadastrar";
        $usuario_id = func_get_args()[0];
        $_REQUEST['usuario'] =  (new Usuario())->buscar($usuario_id);
        $this->load($this->class_path);
    }

    public function inserir() {
        try{
            if(!empty($_POST["anexo_id"])){
                $this->setAssinatura();
                $anexo = new Anexo();
                $anexo = $anexo->buscar($_POST["anexo_id"]);
                if(!empty($_POST['auto_numero_doc']) && $_POST['auto_numero_doc'] == 1) {
                    $_POST['numero'] = $anexo->getNumero(true);
                }
                $anexo->setAssinatura(new ArrayCollection());
                $anexo->adicionaAssinatura(parent::getValues(new Assinatura()));
                if($anexo->podeMandarParaAssinatura()){
                    $this->enviarParaAssinatura($anexo);
                }else{
                    $_POST['preenvio'] = true;
                    $this->preenviarParaAssinatura($anexo);
                }
                parent::inserir();
            }else{
                $processo =  unserialize($_SESSION['processo']);
                $anexo = $processo->getAnexos()->get($_POST['anexo_indice']);
                if(!empty($_POST['auto_numero_doc']) && $_POST['auto_numero_doc'] == 1){
                    $_POST['numero'] = $anexo->getNumero(true);
                }
                $this->setAssinatura();
                $assinatura = $this->getValues(new Assinatura());

                $usuario = new Usuario();
                $usuario = $usuario->buscar(UsuarioController::getUsuarioLogado()->getId());
                $assinatura->setUsuario($usuario);

                $anexo->removerTodos();
                $anexo->adicionaAssinatura($assinatura);
                $_SESSION['processo'] = serialize($processo);
                self::setMessage(TipoMensagem::SUCCESS, "Envio pré-cadastrado com sucesso, ao concluir o cadastro do processo/protocolo o documento será enviado para assinatura digital.", null, 1);
            }
            HistoricoAnexo::registrar(TipoHistoricoAnexo::UPDATE, null, "Anexo enviado para assinatura.", $anexo, $anexo);
        }catch(Exception $e){
            Functions::escreverLogErro($e);
            self::setMessage(TipoMensagem::ERROR, $e->getMessage(), null, 1);
        }
    }

    /**

     * @throws Exception
     */
    protected function setAssinatura(){
        $_POST["anexo"] = (new Anexo())->buscar($_POST["anexo_id"]);
        $_POST["usuario"] = UsuarioController::getUsuarioLogadoDoctrine();
        $dataLimitiAssinatura = Functions::converteDataParaMysql($_POST['data_limite_assinatura']);
        $_POST["dataLimiteAssinatura"] = (new DateTime($dataLimitiAssinatura));
        $_POST["grupo"] = implode(",", $_POST['grupo']);
        $_POST["signatarios"] = implode(",", $_POST['signatarios']);
    }

    /**
     * @throws LxSignException
     */
    public  function consultarAssinatura(Assinatura $assinatura){
        $url = $this->urlLxSign. "documento/restAPI/consultar/{$assinatura->getLxsign_id()}";
        $request = (new Builder($url))
            ->setParameters(['access_token'=> $this->token])
            ->setContentType(ContentType::toJson())
            ->verifySSL(false)
            ->setMethod(HTTP_METHOD::POST)
            ->build();
        $response = $request->send();
        $statusCode = $response->getStatusCode();
        if ($statusCode >= 500) {
            throw new LxSignException("Não foi possível consultar a assinatura de identificação {$assinatura->getLxsign_id()}. Status Code: {$response->getStatusCode()}. Resposta: {$response->getBody()->toScalar()}." . PHP_EOL);
        }
        if ($statusCode >= 400) {
             return null;
         }
        return $response->getBody()->toObject();
    }

    public  function adicionarSignatario(){
        $anexo = (new Anexo())->buscar($_POST['anexo_id']);
        $idSignatario = $_POST['documento_signatario'];
        $assinatura = (new Assinatura())->buscar($_POST['assinatura_id']);
        $idDocumento = $assinatura->getLxsign_id();
        try{
            $url = $this->urlLxSign. "documento/restAPI/adicionar_signatario?access_token=" . $this->token;
            $postdata = [
                "idDocumento" => $idDocumento,
                "idSignatario" => $idSignatario
            ];
            $opts = array(
                'http' => array(
                    'method' => 'POST',
                    'header' => "Content-Type: application/json,Accept: application/json",
                    'content' => json_encode($postdata),
                ),
                "ssl"=>array(
                    "verify_peer"=>false,
                    "verify_peer_name"=>false,
                )
            );
            $context = stream_context_create($opts);
            $result = file_get_contents($url, false, $context);
            $objeto = json_decode($result);

            if(isset($objeto->document)){
                HistoricoAnexo::registrar(TipoHistoricoAnexo::UPDATE, null, "Adicionado novo signatário ao documento.", $anexo);
                self::setMessage(TipoMensagem::SUCCESS, 'Signatário adicionado com sucesso.', null, 1);
            }else{
                self::setMessage(TipoMensagem::ERROR, 'Não foi possível incluir signatário ao documento.', null, 1);
            }

        }catch(Exception $e){
            self::setMessage(TipoMensagem::ERROR, $e->getMessage(), null, 1);
        }
    }

    public function visualizar(){
        $assinatura_id = func_get_args()[0];
        $_REQUEST["url_lxsign"] = $this->urlLxSign."documento/visualizar/id/{$assinatura_id}";
        $this->load($this->class_path);
    }
    public function visualizarByAnexo(){
        $anexo_id = func_get_args()[0];
        $anexo  = new Anexo();
        $anexo = $anexo->buscar($anexo_id);
        $assinatura = Assinatura::buscarPorAnexo($anexo);
        $_REQUEST["url_lxsign"] = $this->urlLxSign."documento/visualizar/id/{$assinatura->getLxsign_id()}";
        $this->load($this->class_path);
    }

    /**
     * @throws Exception
     */
    public function preenviarParaAssinatura(Anexo $anexo){
        $postdata = $this->tratarDadosParaEnvioSemArquivo($anexo);
        $objeto = $this->fazerRequisicaoDeEnvioParaAssinatura($postdata, "precadastro");
        $_POST['lxsign_id'] = isset($objeto->document)? $objeto->document->id:null;
        if(isset($objeto->message)){
            throw new Exception($objeto->message);
        }
        return $objeto;
    }

    public function concluirEnvioParaAssinatura(Assinatura $assinatura){
        $postdata = ["document" => [
            "id" => $assinatura->getLxsign_id(),
            "content_base64" => $this->getArquivoEncodadoParaEnvio($assinatura->getAnexo())
        ]];
        $this->fazerRequisicaoDeEnvioParaAssinatura($postdata, "concluir_cadastro");
        $assinatura->setPreenvio(0);
        $assinatura->atualizar();
        return true;
    }

    /**
     *
     * @param Anexo $anexo
     * @return array postdata com as dados para a requisicao sem arquivos
     */
    public function tratarDadosParaEnvioSemArquivo(Anexo $anexo): array
    {
        /**
         * @var $assinatura Assinatura
         */
        $assinatura = $anexo->getAssinatura()->get(0);
        return [
            "document" => [
                'deadline_at' => $assinatura->getDataLimiteAssinatura()->format('Y-m-d'),
                'number' => $assinatura->getNumero(),
                'year' => $assinatura->getExercicio(),
                'grupos_signatarios' => $assinatura->getGrupoAsArray()->toArray(),
                'signatarios' => $assinatura->getSignatariosAsArray()->toArray(),
                'tipo_documento' => $assinatura->getTipoDocumento(),
                'empresa' => $assinatura->getEmpresa(),
                'date' => (new DateTime())->format("Y-m-d"),
                'type' => ($anexo->getTipo()->getDescricao()),
                'description' => "Descrição do Anexo: ".
                ($anexo->getDescricao()). " Objeto/Requerimento do Processo: "
                .$anexo->getProcesso()->getObjeto(),
                'description_origin' => "Processo: " .$anexo->getProcesso(),
                'auto_close' => 1,
                'auto_sign' => 0,
                'path' => '',
                'filename' => (str_replace("/", "-",
                        $anexo->getTipo()->getDescricao()).
                        "_{$assinatura->getNumero()}_".
                        "{$assinatura->getExercicio()}_".
                        time() . ".pdf")
            ]
        ];
    }

    /**
     *
     * @param Anexo $anexo
     * @return array postdata Array com os parametros para envio da requisicao
     */
    public function tratarDadosParaEnvioComArquivo(Anexo $anexo){
        $postdata = $this->tratarDadosParaEnvioSemArquivo($anexo);
        $postdata["document"]['content_base64'] =  $this->getArquivoEncodadoParaEnvio($anexo);
        return $postdata;
    }

    public function getArquivoEncodadoParaEnvio(Anexo $anexo){
        return "data:application/pdf;base64,".
                base64_encode(
                        file_get_contents($anexo->getArquivo(false,false, true)));
    }

    /**
     *
     * @param $postdata
     * @param string $operacao
     * @return object Respose da requisicao sem formato de objeto
     * @throws TechnicalException
     * @throws Exception
     */
    public function fazerRequisicaoDeEnvioParaAssinatura($postdata, string $operacao = "criar"){
        $url = $this->urlLxSign. "documento/restAPI/{$operacao}";
        ini_set ('default_socket_timeout', 240);
        $response = (new Builder($url))
            ->setParameters(["access_token" => $this->token])
            ->setBody($postdata)
            ->setMethod(HTTP_METHOD::POST)
            ->setContentType(ContentType::toJson())
            ->verifySSL(false)
            ->build()
            ->send();
        if($response->getStatusCode() >= 400){
            $msg = "{$response->getStatusCode()} - {$response->getBody()->toObject()->message}.";
            throw new TechnicalException($msg, null, $response->getStatusCode());
        }
        $objeto = $response->getBody()->toObject();
        $_POST['lxsign_id'] = isset($objeto->document)? $objeto->document->id:null;
        if(isset($objeto->message)){
            throw new Exception($objeto->message);
        }
        return $objeto;
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     * @throws TransactionRequiredException
     * @throws BusinessException
     * @throws Exception
     */
    public  function enviarParaAssinatura(Anexo $anexo = null){
        if($anexo == null){
            $anexo = new Anexo();
            $anexo = $anexo->buscar($_POST["anexo_id"]);
        }
        if(!$anexo->podeMandarParaAssinatura()){
            $_POST['preenvio'] = true;
            $_POST['lxsign_id'] = null;
            return null;
        }
        $postdata = $this->tratarDadosParaEnvioComArquivo($anexo);
        $objeto = $this->fazerRequisicaoDeEnvioParaAssinatura($postdata);
        $_POST['lxsign_id'] = isset($objeto->document)? $objeto->document->id:null;
        if(isset($objeto->message)){
            throw new Exception($objeto->message);
        }
        return $objeto;
    }

    public function reenviarParaAssinatura($id = null){
        try{
            $assinatura_id = $id ?? $_POST["assinatura_id"];
            $assinatura = new Assinatura();
            $assinatura = $assinatura->buscar($assinatura_id);
            $anexo = $assinatura->getAnexo();
            $_POST["anexo_id"] = $anexo->getId();
            if (isset($_POST["auto_numero_doc"]) && $_POST["auto_numero_doc"]) {
                $anexo->setIsAutoNumeric(true);
            } else if (!empty($_POST['numero'])) {
                $anexo->setNumero($_POST['numero']);
            }
            if (isset($_POST["exercicio"]) && !empty($_POST["exercicio"])) {
                $anexo->setExercicio($_POST["exercicio"]);
            }
            $dataLimitiAssinatura = Functions::converteDataParaMysql($_POST['data_limite_assinatura']);
            $_POST["dataLimiteAssinatura"] = (new DateTime($dataLimitiAssinatura));
            $_POST["data_limite_assinatura"] =$dataLimitiAssinatura;
            if($assinatura->getLxsign_id()){
                try {
                    $objeto = $this->consultarAssinatura($assinatura);
                    if(!is_null($objeto) && isset($objeto->document)){
                        throw new Exception("Não é possível reenviar, pois o documento já existe no sistema de assinatura.");
                    }
                } catch (LxSignException $e) {
                    Functions::escreverLogErro($e);
                }
            }
            $anexo->setAssinatura(new ArrayCollection());
            $anexo->adicionaAssinatura($assinatura);
            $assinatura->setNumero($anexo->getNumero());
            $assinatura->setExercicio($anexo->getExercicio());
            $assinatura->setGrupo(implode(",", $_POST["grupo"]));
            $assinatura->setSignatarios(implode(",", $_POST["signatarios"]));
            $objeto = $this->enviarParaAssinatura($anexo);
            $lxsign_id = isset($objeto->document)? $objeto->document->id:null;
            $assinatura->setLxsign_id($lxsign_id);
            $assinatura->atualizar();
            self::setMessage(TipoMensagem::SUCCESS, "Arquivo reenviado com sucesso.", $assinatura_id, true);
        } catch (AppException $e) {
            if ($e->getCode() >= 400) {
                http_response_code($e->getCode());
            }
            self::setMessage(TipoMensagem::ERROR, $e->getMessage(), $assinatura->getId(), true);
        } catch(Exception $e){
            $objeto_id = null;
            self::setMessage(TipoMensagem::ERROR, $e->getMessage(), $objeto_id, true);
        }
    }

    public function qtdRequisicaoAssinatura($usuarioId, $tokenUser){
        $config = IndexController::getConfig();
        if(!$config['lxsign']){
            return null;
        }
        $get_data = http_build_query([
            "usuario" => $usuarioId,
            "token" => $tokenUser,
            "token_integracao" => IndexController::getConfig()['token_integracao']
        ]);
        $url = $this->urlLxSign. "src/App/Ajax/Documento/listar_qtd_assinatura.php?" . $get_data;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Accept: application/json"
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYSTATUS, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result ? json_decode($result) : null;
    }

    public function gerarNumeroDocumento(){
        $app =  AppController::getConfig();
        $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJjbnBqIjoiMDMuNDM0Ljc5MlwvMDAwMS0wOSJ9.zPJLb7eQwyXL3qbIQdcG_fZTjTEoNh1ha6h1P-t3vYw';
        $url =  $app["lxsign_url"]. "documento/restAPI/gerar_numero_documento?access_token=" . $token;
        $opts = array(
            'http' => array(
                'method' => 'POST',
                'header' => "Content-Type: application/json,Accept: application/json"
            ),
            "ssl"=>array(
                "verify_peer"=>false,
                "verify_peer_name"=>false,
            )
        );
        $context = stream_context_create($opts);
        $result = file_get_contents($url, false, $context);
        return json_decode($result);
    }
}
