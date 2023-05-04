<?php

namespace App\Controller;

use App\Enum\TipoCampo;
use App\Enum\TipoHistoricoProcesso;
use App\Model\Anexo;
use App\Model\Assinatura;
use App\Model\Assunto;
use App\Model\HistoricoProcesso;
use App\Model\Processo;
use App\Model\RespostaCampo;
use App\Model\RespostaPergunta;
use App\Model\StatusProcesso;
use App\Model\Tramite;
use Core\Controller\AppController;
use Core\Enum\TipoMensagem;
use Core\Exception\BusinessException;
use Core\Exception\SecurityException;
use Core\Util\Functions;
use Core\Util\Upload;
use DateTime;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\TransactionRequiredException;
use Exception;
use Doctrine\DBAL\DBALException;
use Core\Exception\AppException;

/**
 * Classe TramiteController
 * @version 1.0
 * @author Anderson Brandão Batistoti <anderson@lxtec.com.br>
 * @date   23/01/2018
 * @copyright (c) 2018, Lxtec Informática
 */
class TramiteController extends AppController
{
    private $lxSignToken;
    private $lxSignUrl;

    public function __construct()
    {
        parent::__construct(get_class());
        $this->lxSignUrl = AppController::getConfig()['lxsign_url'];
        $this->lxSignToken = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJjbnBqIjoiMDMuNDM0Ljc5MlwvMDAwMS0wOSJ9.zPJLb7eQwyXL3qbIQdcG_fZTjTEoNh1ha6h1P-t3vYw';
    }

    function cancelar()
    {
        try {
            $tramite_id = func_get_args()[0];
            $tramite = (new Tramite())->buscar($tramite_id);
            $processo = $tramite->getProcesso();
            $tramiteAtual = $processo->getTramiteAtual();
            //Volta fase do processo somente se a fase atual tiver um único trâmite
            if (count($tramiteAtual) == 1) {
                $processo->setNumeroFase($processo->getNumeroFase() - 1);
                $processo->atualizar();
            }
            $usuario = UsuarioController::getUsuarioLogadoDoctrine();
            $tramite->setUsuarioCancelamento($usuario);
            $tramite->setIsCancelado(true);
            $tramite->setJustificativaCancelamento($_POST['justificativaCancelamento']);
            $tramite->atualizar();
            HistoricoProcesso::registrar(TipoHistoricoProcesso::CANCELADO_ENVIO, $processo, $tramite, null, $usuario);
            //self::setMessage(TipoMensagem::SUCCESS, "Envio cancelado com sucesso.", null, true);
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    public function buscarSetorAnterior()
    {
        try {
            $tramite_id = filter_input(INPUT_POST, 'tramite_id', FILTER_VALIDATE_INT);
            $tramite = (new Tramite())->buscar($tramite_id);
            echo json_encode(array('msg' => "Setor anterior buscado com sucesso.", 'tipo' => TipoMensagem::SUCCESS, 'setor_anterior_id' => $tramite->getSetorAnterior()->getId()));
        } catch (DBALException $ex) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao buscar registro. Contate o administrador.");
            parent::registerLogError($ex);
        } catch (AppException $ex) {
            self::setMessage(TipoMensagem::ERROR, $ex->getMessage());
        } catch (Exception $ex) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao buscar registro. Contate o administrador. Erro: {$ex->getMessage()}.");
            parent::registerLogError($ex);
        }
    }

    function alterarStatus()
    {
        try {
            $tramite_id = $_POST['tramite_id'];
            $tramite = (new Tramite())->buscar($tramite_id);
            $status = (new StatusProcesso())->buscar($_POST['status_id']);
            $tramite->setStatus($status);
            $tramite->setParecer($_POST['parecer']);
            $tramite->atualizar();
            if ($status->getIsArquivamento()) {
                (new ProcessoController())->marcarArquivado($tramite->getProcesso()->getId(), $_POST['parecer']);
            }
            self::setMessage(TipoMensagem::SUCCESS, "Status alterado com sucesso.", null, true);
        } catch (DBALException $ex) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao alterar status.", null, true);
            parent::registerLogError($ex);
        } catch (AppException $ex) {
            self::setMessage(TipoMensagem::ERROR, $ex->getMessage(), null, true);
        } catch (Exception $ex) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao alterar status. Erro: {$ex->getMessage()}.", null, true);
            parent::registerLogError($ex);
        }
    }

    private function podeReceber($tramite_id)
    {
        $config = AppController::getConfig();
        if (!isset($config['bloquear_tramite']) && empty($config['bloquear_tramite'])) {
            return true;
        }
        $lxsignIds = (new Tramite())->buscarLxSignIdDosAnexos($tramite_id);
        if (!empty($lxsignIds)) {
            $status = $this->getAssinaturaStatus($lxsignIds);
            return $this->estaFinalizadoProcessoAssinatura($status);
        } else {
            return true;
        }
    }

    /**
     * Função que recebe um ou mais trâmites de processos
     */
    public function receber()
    {
        try {
            if (isset($_POST['receber_processo'])) {
                foreach ($_POST['receber_processo'] as $tramite_id) {
                    if (!$this->podeReceber($tramite_id)) {
                        $tramite = new Tramite();
                        $tramite = $tramite->buscar($tramite_id);
                        self::setMessage(TipoMensagem::WARNING, "Assinatura(s) pendente(s): Há documento(s) que ainda não foi(ram) assinado(s) no processo/protocolo <strong>{$tramite->getProcesso()}</strong>.", null, true);
                        return;
                    }
                }
                foreach ($_POST['receber_processo'] as $tramite_id) {
                    $this->marcarRecebido($tramite_id);
                }
                self::setMessage(TipoMensagem::SUCCESS, "Processo(s) recebido(s) com sucesso.", null, true);
            } else {
                $tramite_id = $_POST['tramite_id'] ?? func_get_args()[1];

                if ($this->podeReceber($tramite_id)) {
                    $this->marcarRecebido($tramite_id);
                    if (!isset($_POST['tramite_id'])) {
                        self::setMessage(TipoMensagem::SUCCESS, "Processo recebido com sucesso.", null, true);
                    }
                } else {
                    self::setMessage(TipoMensagem::WARNING, "Assinatura(s) pendente(s): Há documento(s) que ainda não foi(ram) assinado(s).", null, true);
                }
            }
        } catch (DBALException $ex) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao receber processo.", null, true);
            parent::registerLogError($ex);
        } catch (AppException $ex) {
            self::setMessage(TipoMensagem::ERROR, $ex->getMessage(), null, true);
        } catch (Exception $ex) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao receber processo. Erro: {$ex->getMessage()}.", null, true);
            parent::registerLogError($ex);
        }
    }

    private function getAssinaturaStatus($assinaturaIds)
    {
        $opts = array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-type: application/x-www-form-urlencoded',
                'content' => http_build_query(['id' => $assinaturaIds])
            ),
            "ssl" => array(
                "verify_peer" => false,
                "verify_peer_name" => false,
            )
        );
        $context = stream_context_create($opts);
        $url = $this->lxSignUrl . "documento/restAPI/consultar?access_token=" . $this->lxSignToken;
        return json_decode(file_get_contents($url, false, $context));
    }

    private function estaFinalizadoProcessoAssinatura($data)
    {
        if (empty($data)) {
            return true;
        }
        $estaFinalizado = true;
        foreach ($data as $item) {
            if ($item->status !== 'Finalizado' && $item->status !== 'Excluído') {
                $estaFinalizado = false;
            }
        }
        return $estaFinalizado;
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     * @throws SecurityException
     * @throws TransactionRequiredException
     * @throws BusinessException
     * @throws Exception
     */
    private function marcarRecebido($tramite_id): void
    {
        $tramite = (new Tramite())->buscar($tramite_id);
        $tramite->setIsRecebido(true);
        $tramite->setUsuarioRecebimento(UsuarioController::getUsuarioLogadoDoctrine());
        $tramite->setDataRecebimento(new DateTime());
        $tramite->atualizar();
        HistoricoProcesso::registrar(TipoHistoricoProcesso::RECEBIDO, $tramite->getProcesso(), $tramite);
    }

    /**
     * @throws Exception
     */
    function setRespostasCampo(Tramite $tramite, $assunto, Processo $processo, $numero_fase)
    {
        $usuario_logado = UsuarioController::getUsuarioLogadoDoctrine();
        $setor_atual = $tramite->getSetorAtual();
        if (!is_null($setor_atual) && !is_null($assunto->getFluxograma())) {
            $fase = $assunto->getFluxograma()->getFases($numero_fase);
            $setor_fase = $fase->getSetoresFase($setor_atual->getId());
            if (!is_null($setor_fase)) {
                foreach ($setor_fase->getCampos() as $campo) {
                    if (isset($_POST['campo_' . $campo->getId()]) || isset($_FILES['campo_' . $campo->getId()])) {
                        if ($campo->getTipo() == TipoCampo::ARQUIVO && isset($_FILES['campo_' . $campo->getId()]) && !empty($_FILES['campo_' . $campo->getId()]['name'])) {
                            $limite = count($_FILES['campo_' . $campo->getId()]["name"]);
                            $contador = 0;
                            do{

                                $anexoAux = new Anexo();
                                $anexoAux->setDataCadastro(new DateTime());
                                $anexoAux->setTipo($campo->getTipoTemplate());
                                $anexoAux->setData(new DateTime());
                                $anexoAux->setDescricao($campo->getNome());
                                $anexoAux->setProcesso($processo);
                                $anexoAux->setIsDigitalizado(false);

                                $_FILES['arquivo'] = array(
                                    "name" => $_FILES['campo_' . $campo->getId()]["name"][$contador],
                                    "type" => $_FILES['campo_' . $campo->getId()]["type"][$contador],
                                    "tmp_name" => $_FILES['campo_' . $campo->getId()]["tmp_name"][$contador],
                                    "error" => $_FILES['campo_' . $campo->getId()]["error"][$contador],
                                    "size"=>$_FILES['campo_' . $campo->getId()]["size"][$contador]);
    
                                
                                $resposta_campo = (new Upload('arquivo', $anexoAux->getPath(), array('pdf')))->upload();
                                if (!empty($resposta_campo) && is_array($resposta_campo)) {
                                    foreach ($resposta_campo as $arquivo) {
                                        $anexo = new Anexo();
                                        $anexo->setExercicio($_POST['ano_campo_' . $campo->getId()][$contador]);
                                        $anexo->setDataCadastro(new DateTime());
                                        $anexo->setIsCirculacaoInterna($campo->getCirculacaoInterna());
                                        $anexo->setTipo($campo->getTipoTemplate());
                                        if (isset($_POST['data_campo_' . $campo->getId()][$contador])) {
                                            $data = new DateTime(Functions::converteDataParaMysql($_POST['data_campo_' . $campo->getId()][$contador]));
                                        } else {
                                            $data = new DateTime();
                                        }
                                        $anexo->setData($data);
                                        $anexo->setDescricao($campo->getNome());
                                        $anexo->setProcesso($processo);
                                        $anexo->setArquivo($arquivo);
                                        $anexo->setIsDigitalizado(false);
                                        $anexo->setUsuario($usuario_logado);
                                        if ($campo->getAssinaturaObrigatoria()) {
                                            $anexo->setIsAutoNumeric(true);
                                            $assinatura = new Assinatura();
                                            $assinatura->setDataCadastro(new DateTime);
                                            $assinatura->setExercicio($_POST['ano_campo_' . $campo->getId()][$contador]);
                                            $assinatura->setUsuario($usuario_logado);
                                            $assinatura->setGrupo(implode(",", $_POST["grupo_assinatura_campo_{$campo->getId()}"][$contador]));
                                            $assinatura->setSignatarios(implode(",", $_POST["signatario_assinatura_campo_{$campo->getId()}"][$contador]));
                                            $assinatura->setTipoDocumento($_POST["tipo_documento_campo_{$campo->getId()}"][$contador]);
                                            $assinatura->setEmpresa($_POST["empresa_campo_{$campo->getId()}"][$contador]);
                                            $dataLimitiAssinatura = Functions::converteDataParaMysql($_POST["data_assinatura_campo_{$campo->getId()}"][$contador]);
                                            $assinatura->setDataLimiteAssinatura((new DateTime($dataLimitiAssinatura)));
                                            $assinatura->setAnexo($anexo);
                                            $anexo->removerTodos();
                                            $anexo->adicionaAssinatura($assinatura);
                                        }
                                        $processo->adicionaAnexo($anexo);
                                    }
                                    $resposta_campo = implode(",", $resposta_campo);
                                } else if (!empty($resposta_campo) && !is_array($resposta_campo)){
                                        $anexo = new Anexo();
                                        $anexo->setExercicio($_POST['ano_campo_' . $campo->getId()][$contador]);
                                        $anexo->setDataCadastro(new DateTime());
                                        $anexo->setIsCirculacaoInterna($campo->getCirculacaoInterna());
                                        $anexo->setTipo($campo->getTipoTemplate());
                                        if (isset($_POST['data_campo_' . $campo->getId()][$contador])) {
                                            $data = new DateTime(Functions::converteDataParaMysql($_POST['data_campo_' . $campo->getId()][$contador]));
                                        } else {
                                            $data = new DateTime();
                                        }
                                        $anexo->setData($data);
                                        $anexo->setDescricao($campo->getNome());
                                        $anexo->setProcesso($processo);
                                        $anexo->setArquivo($resposta_campo);
                                        $anexo->setIsDigitalizado(false);
                                        $anexo->setUsuario($usuario_logado);
                                        if (!empty($_POST['documento_auto_numeric_' . $campo->getId()][$contador]) && $_POST['documento_auto_numeric_' . $campo->getId()][$contador] == 1) {
                                            $anexo->setIsAutoNumeric(true);
                                        } elseif (isset($_POST['numero_campo_' . $campo->getId()][$contador])) {
                                            $anexo->setNumero($_POST['numero_campo_' . $campo->getId()][$contador]);
                                        }
                                        $anexo->setExercicio($_POST['ano_campo_' . $campo->getId()][$contador]);
                                        if ($campo->getAssinaturaObrigatoria()) {
                                            $assinatura = new Assinatura();
                                            $assinatura->setDataCadastro(new DateTime);
                                            $assinatura->setExercicio($_POST['ano_campo_' . $campo->getId()][$contador]);
                                            $assinatura->setUsuario($usuario_logado);
                                            $assinatura->setGrupo(implode(",", $_POST["grupo_assinatura_campo_{$campo->getId()}"][$contador]));
                                            $assinatura->setSignatarios(implode(",", $_POST["signatario_assinatura_campo_{$campo->getId()}"][$contador]));
                                            $assinatura->setTipoDocumento($_POST["tipo_documento_campo_{$campo->getId()}"][$contador]);
                                            $assinatura->setEmpresa($_POST["empresa_campo_{$campo->getId()}"][$contador]);
                                            $dataLimitiAssinatura = Functions::converteDataParaMysql($_POST["data_assinatura_campo_{$campo->getId()}"][$contador]);
                                            $assinatura->setDataLimiteAssinatura((new DateTime($dataLimitiAssinatura)));
                                            $anexo->removerTodos();
                                            $anexo->adicionaAssinatura($assinatura);
                                        }
                                        $processo->adicionaAnexo($anexo);
                                }

                                // if (((!empty($_POST['numero_campo_' . $campo->getId()][$contador])) && !empty($_POST['ano_campo_' . $campo->getId()][$contador])) || (isset($_POST['documento_auto_numeric_' . $campo->getId()][$contador]) && $_POST['documento_auto_numeric_' . $campo->getId()][$contador])) {
                                //     $anexo->setExercicio($_POST['ano_campo_' . $campo->getId()][$contador]);
                                //     if ($campo->getAssinaturaObrigatoria() && isset($_FILES['campo_' . $campo->getId()]) && !empty($_FILES['campo_' . $campo->getId()]['name'][$contador])) {
                                //         $assinatura = new Assinatura();
                                //         $assinatura->setDataCadastro(new DateTime);
                                //         $assinatura->setExercicio($_POST['ano_campo_' . $campo->getId()][$contador]);
                                //         $assinatura->setUsuario($usuario_logado);
                                //         $assinatura->setGrupo(implode(",", $_POST["grupo_assinatura_campo_{$campo->getId()}"][$contador]));
                                //         $assinatura->setSignatarios(implode(",", $_POST["signatario_assinatura_campo_{$campo->getId()}"][$contador]));
                                //         $assinatura->setTipoDocumento($_POST["tipo_documento_campo_{$campo->getId()}"][$contador]);
                                //         $assinatura->setEmpresa($_POST["empresa_campo_{$campo->getId()}"][$contador]);
                                //         $dataLimitiAssinatura = Functions::converteDataParaMysql($_POST["data_assinatura_campo_{$campo->getId()}"][$contador]);
                                //         $assinatura->setDataLimiteAssinatura((new DateTime($dataLimitiAssinatura)));
                                //         $anexo->removerTodos();
                                //         $anexo->adicionaAssinatura($assinatura);
                                //     }
                                // }

                                // $anexo->setTipo($campo->getTipoTemplate());
                                // $anexo->setDescricao($campo->getNome());
                                // $anexo->setIsDigitalizado(false);
                                // $anexo->setUsuario($usuario_logado);

                                // $anexo->setArquivo($resposta_campo);
                                // $processo->adicionaAnexo($anexo);
                                
                                $contador++;

                            } while($contador < $limite);
                        } else if ($campo->getTipo() == TipoCampo::PROCESSO) {
                            $processoLincado = new Processo();
                            $processoLincado = $processoLincado->buscar($_POST['campo_' . $campo->getId()]);
                            $_POST['vincula'] = 1;
                            $_POST['campo_id'] = $campo->getId();
                            $resposta_campo = isset($_POST['campo_' . $campo->getId()]) && !empty($_POST['campo_' . $campo->getId()]) ? "{$processoLincado->getNumero()}/{$processoLincado->getExercicio()}" : null;
                        } else if ($campo->getTipo() == TipoCampo::ARQUIVO_MULTIPLO && isset($_FILES['campo_' . $campo->getId()]) && !empty($_FILES['campo_' . $campo->getId()]['name'])) {
                            $anexoAux = new Anexo();
                            $anexoAux->setDataCadastro(new DateTime());
                            $anexoAux->setTipo($campo->getTipoTemplate());
                            $anexoAux->setData(new DateTime());
                            $anexoAux->setDescricao($campo->getNome());
                            $anexoAux->setProcesso($processo);
                            $anexoAux->setIsDigitalizado(false);
                            $resposta_campo = (new Upload('campo_' . $campo->getId(), $anexoAux->getPath(), array('pdf')))->upload();
                            if (!empty($resposta_campo) && is_array($resposta_campo)) {
                                foreach ($resposta_campo as $arquivo) {
                                    $anexo = new Anexo();
                                    $anexo->setExercicio($_POST['ano_campo_' . $campo->getId()]);
                                    $anexo->setDataCadastro(new DateTime());
                                    $anexo->setTipo($campo->getTipoTemplate());
                                    $anexo->setData(new DateTime());
                                    $anexo->setDescricao($campo->getNome());
                                    $anexo->setProcesso($processo);
                                    $anexo->setArquivo($arquivo);
                                    $anexo->setIsDigitalizado(false);
                                    $anexo->setUsuario($usuario_logado);
                                    if ($campo->getAssinaturaObrigatoria()) {
                                        $anexo->setIsAutoNumeric(true);
                                        $assinatura = new Assinatura();
                                        $assinatura->setDataCadastro(new DateTime);
                                        $assinatura->setExercicio($_POST['ano_campo_' . $campo->getId()]);
                                        $assinatura->setUsuario($usuario_logado);
                                        $assinatura->setGrupo(implode(",", $_POST["grupo_assinatura_campo_{$campo->getId()}"]));
                                        $assinatura->setSignatarios(implode(",", $_POST["signatario_assinatura_campo_{$campo->getId()}"]));
                                        $assinatura->setTipoDocumento($_POST["tipo_documento_campo_{$campo->getId()}"]);
                                        $assinatura->setEmpresa($_POST["empresa_campo_{$campo->getId()}"]);
                                        $dataLimitiAssinatura = Functions::converteDataParaMysql($_POST["data_assinatura_campo_{$campo->getId()}"]);
                                        $assinatura->setDataLimiteAssinatura((new DateTime($dataLimitiAssinatura)));
                                        $assinatura->setAnexo($anexo);
                                        $anexo->removerTodos();
                                        $anexo->adicionaAssinatura($assinatura);
                                    }
                                    $processo->adicionaAnexo($anexo);
                                }
                                $resposta_campo = implode(",", $resposta_campo);
                            }
                        } else {
                            $resposta_campo = $_POST['campo_' . $campo->getId()];
                        }
                        $resposta = new RespostaCampo();
                        $resposta->setCampo($campo);
                        $resposta->setCampoTxt($campo->getNome());
                        $resposta->setResposta($resposta_campo);
                        $resposta->setData(new DateTime());
                        $resposta->setTramite($tramite);
                        if (isset($processoLincado) && !empty($processoLincado)) {
                            $resposta->setProcessoLincado($processoLincado);
                        }
                        $tramite->adicionaRespostaCampo($resposta);
                    }
                }
            }
        }
    }

    function setRespostasPergunta(Tramite $tramite, Assunto $assunto, $numero_fase)
    {
        $setor_atual = $tramite->getSetorAtual();
        if (!is_null($setor_atual) && !is_null($assunto->getFluxograma())) {
            $fase = $assunto->getFluxograma()->getFases($numero_fase);
            $setores_fase = $fase->getSetoresFase($setor_atual->getId());
            if (!empty($setores_fase)) {
                if (is_array($setores_fase)){
                    foreach ($setores_fase as $setor_fase) {
                        if ($setor_fase != null) {
                            foreach ($setor_fase->getPerguntas() as $pergunta) {
                                if (isset($_POST['resposta_pergunta' . $pergunta->getId()])) {
                                    $resposta = new RespostaPergunta();
                                    $resposta->setPergunta($pergunta);
                                    $resposta->setPerguntaTxt($pergunta->getDescricao());
                                    $resposta->setResposta($_POST['resposta_' . $pergunta->getId()]);
                                    $resposta->setObservacoes($_POST['observacoes_' . $pergunta->getId()]);
                                    $resposta->setData(new DateTime());
                                    $resposta->setTramite($tramite);
                                    $tramite->adicionaRespostaPergunta($resposta);
                                }
                            }
                        }
                    }
                } else {
                    foreach ($setores_fase->getPerguntas() as $pergunta) {
                        if (isset($_POST['resposta_pergunta' . $pergunta->getId()])) {
                            $resposta = new RespostaPergunta();
                            $resposta->setPergunta($pergunta);
                            $resposta->setPerguntaTxt($pergunta->getDescricao());
                            $resposta->setResposta($_POST['resposta_' . $pergunta->getId()]);
                            $resposta->setObservacoes($_POST['observacoes_' . $pergunta->getId()]);
                            $resposta->setData(new DateTime());
                            $resposta->setTramite($tramite);
                            $tramite->adicionaRespostaPergunta($resposta);
                        }
                    }
                }
            }
        }
    }
}
