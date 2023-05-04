<?php

namespace App\Controller;

use App\Enum\PermissaoStatus;
use App\Enum\TipoHistoricoAnexo;
use App\Enum\TipoLog;
use App\Enum\TipoUsuario;
use App\Log\HistoricoAnexo;
use App\Model\Anexo;
use App\Model\Substituicao;
use App\Model\Classificacao;
use App\Model\Componente;
use App\Model\Converter;
use App\Model\Dao\AnexoDao;
use App\Model\Log;
use App\Model\Dao\ProcessoDao;
use App\Model\Processo;
use App\Model\TipoAnexo;
use App\Model\Tramite;
use App\Util\AnexoModelo;
use App\Util\Html2Pdf;
use Core\Controller\AppController;
use Core\Enum\TipoMensagem;
use Core\Exception\AppException;
use Core\Exception\BusinessException;
use Core\Exception\SecurityException;
use Core\Exception\TechnicalException;
use Core\Util\EntityManagerConn;
use Core\Util\Functions;
use Core\Util\Http\Client\Builder;
use Core\Util\Http\HTTP_METHOD;
use Core\Util\PdfParser\Parser;
use Core\Util\Upload;
use DateTime;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\TransactionRequiredException;
use Exception;
use PDFMerger;
use SmartyException;

/**
 * Classe AnexoController
 * @version 1.0
 * @author Anderson Brandão Batistoti <anderson@lxtec.com.br>
 * @date   17/01/2018
 * @copyright (c) 2018, Lxtec Informática
 */
class AnexoController extends AppController
{
    public function __construct($classe = null)
    {
        parent::__construct($classe == null ? get_class() : $classe);
    }

    /**
     * @return bool
     * @throws exception
     */
    public function merge($arquivos, $destino)
    {
        include APP_PATH . 'lib/pdf-merger/PDFMerger.php';
        $pdf = new PDFMerger();
        foreach ($arquivos as $arquivo) {
            if (is_file($arquivo)) {
                $extensao = strtolower(pathinfo($arquivo, PATHINFO_EXTENSION));
                if ($extensao == 'pdf') {
                    $pdf->addPDF($arquivo);
                } else {
                    throw new TechnicalException("Para realizar a mesclagem, todos os arquivos devem ser no formato PDF.");
                }
            } else {
                throw new TechnicalException("Arquivo $arquivo não foi encontrado.");
            }
        }
        if (!$pdf->merge('file', $destino)) {
            throw new TechnicalException("Merge de arquivos pdf's não foi concluído.");
        }
        return true;
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     * @throws SmartyException
     * @throws TransactionRequiredException
     */
    public function abrirArquivoAnexo($param = null){
        if (is_null($param)) {
            $this->retornoAnexoRemovido();
            exit;
        }
        if (is_numeric($param)) {
            $anexo = new Anexo();
            $anexo = $anexo->buscar($param);
            if (is_null($anexo)) {
                $this->retornoAnexoRemovido();
                exit;
            }

            $url = $anexo->getArquivoUrl();


            if (is_null($url)) {
                $this->retornoAnexoRemovido();
            } else {
                header('Location: ' . $url);
            }

            
        } else {
            $attachPath = ATTACH_TEMP . $param;
            header("Content-Type: application/pdf");
            header("Content-Disposition: inline; filename=" . basename($attachPath));
            readfile($attachPath);
        }
        exit;
    }

    /**
     * Método que realiza a mesclagem de anexos de um processo em um só
     */
    public function mesclar()
    {
        try {
            $anexos_mesclar_ids = json_decode($_POST['anexos_mesclar']);
            if (count($anexos_mesclar_ids) > 1) {
                //Assume que o primeiro anexo da lista será mantido
                $arquivos_mesclar = array();
                $textoOCR = "";
                $processo = null;
                foreach ($anexos_mesclar_ids as $i => $anexo_mesclar_id) {
                    $anexo_mesclar = (new Anexo())->buscar($anexo_mesclar_id);
                    $componente = new Componente();
                    $componente = $componente->buscarPorCampos(array("anexo" => $anexo_mesclar));

                    if ($i == 0) {
                        $processo = $anexo_mesclar->getProcesso();
                    }
                    $arquivos_mesclar [] = $anexo_mesclar->getArquivo(false, true, true);
                    $textoOCR .= $anexo_mesclar->getTextoOCR();
                }
                $anexo_manter = new Anexo();
                $this->setCampos($anexo_manter);
                $anexo_manter->setProcesso($processo);
                $anexo_manter->setArquivo(date("YmdHisu") . uniqid() . date("usiHdmY") . ".pdf");
                $anexo_manter->setTextoOCR($textoOCR);
                if ($this->merge($arquivos_mesclar, $anexo_manter->getPath() . $anexo_manter->getArquivo())) {
                    //array_shift($anexos_mesclar_ids);
                    foreach ($anexos_mesclar_ids as $anexo_remover_id) {
                        (new Anexo())->remover($anexo_remover_id);
                    }
                    foreach ($arquivos_mesclar as $arquivo) {
                        Functions::removerArquivo($arquivo);
                    }
//                    $anexo_manter->setQtdePaginas(Functions::getQntdePaginasPDF($anexo_manter->getArquivo(false, true, true)));
                    $anexo_manter_id = $anexo_manter->inserir();
                    // TODO: Resultado de file_get_contents() não é utilizado. Substituir por uma função mais adequada.
                    file_get_contents(APP_URL . "componente/inserirPorFileGetContents?anexo_id=" . $anexo_manter_id);
                    file_get_contents(APP_URL . "componente/reordenarComponentesPorFileGetContents?processo_id=" . $processo->getId());
                }
                self::setMessage(TipoMensagem::SUCCESS, "Arquivos mesclados com sucesso!", null, true);
            } else {
                throw new TechnicalException("É necessário ao menos 2 arquivos para mesclar.");
            }
        } catch (AppException $ex) {
            self::setMessage(TipoMensagem::ERROR, $ex->getMessage(), null, true);
        } catch (DBALException $ex) {
            self::setMessage(TipoMensagem::ERROR, "Ocorreu um erro gerar o arquivo a partir do modelo.", null, true);
            parent::registerLogError($ex);
        } catch (Exception $ex) {
            self::setMessage(TipoMensagem::ERROR, "Ocorreu um erro ao mesclar os arquivos. ERRO: {$ex->getMessage()}", null, true);
            parent::registerLogError($ex);
        }
    }

    public function importar()
    {

        if (!isset($_POST['id_documento']) || empty($_POST['id_documento'])) {
            throw new Exception("Selecione um ou mais documentos para importação.");
        }
        $empenhos = array();
        $ordensPagamento = array();
        foreach ($_POST['id_documento'] as $documento_id) {
            if ($_POST['tipo_documento_' . $documento_id] == 'empenho') {
                $empenhos[] = $documento_id;
            } else if ($_POST['tipo_documento_' . $documento_id] == 'ordemPagamento') {
                $ordensPagamento[] = $documento_id;
            }
        }

        $postdata = [
            "documentos" => [
                'empenhos' => $empenhos,
                'ordens' => $ordensPagamento
            ]
        ];
        $opts = array('http' =>
            array(
                'method' => 'POST',
                'header' => "Accept-language: en\r\n" .
                    "Content-type: application/x-www-form-urlencoded\r\n",
                'content' => http_build_query($postdata)
            )
        );

        $context = stream_context_create($opts);
        $config = IndexController::getConfig();
        $result = file_get_contents($config['lxfiorilli_interno'] . "index.php?entidade=index&method=exportar", false, $context);
        if (!$result) {
            throw new \Exception($http_response_header[0]);
        }

        $objResult = json_decode($result);
        foreach ($objResult->documentos->empenhos as $key => $empenho) {
            $anexo = new Anexo();

            $configImportacao = IndexController::getConfigImportacao();
            $anexo->setProcesso((new Processo())->buscar($_POST['processo_id']));
            $anexo->setDataCadastro(new DateTime());
            $anexo->setTipo((new TipoAnexo())->buscar($configImportacao['tipo_anexo_empenho']));
            if (!empty($configImportacao['classificacao_anexo_empenho'])) {
                $anexo->setClassificacao((new Classificacao())->buscar($configImportacao['classificacao_anexo_empenho']));
            }
            $anexo->setData(new DateTime($empenho->dataEmissao));
            $anexo->setNovoVencimentoProcesso(null);

            $anexo->setDescricao(substr($empenho->descricao, 0, 100));
            $anexo->setExercicio($anexo->getData()->format('Y'));
            $anexo->setNumero($empenho->numero . "/" . $empenho->exercicio);
            $anexo->setCodigoImportacao("fiorilli" . $empenho->id . "-empenho");
//            $anexo->setValor($empenho->valor);
            $anexo->setValor(empty($empenho->valor) ? 0 : $empenho->valor);

            $pdf_decoded = base64_decode(str_replace('data:application/pdf;base64,', '', $empenho->notaEmpenhoPdf));
            $nomeArquivo = time() . $key . ".pdf";
            $pdf_path = $anexo->getPath() . $nomeArquivo;
            $pdf = fopen($pdf_path, 'w');
            fwrite($pdf, $pdf_decoded);
            fclose($pdf);


            $anexo->setArquivo($nomeArquivo);
            try {
                $anexo->setTextoOCR((new Parser())->lxParseFile($pdf_path));
            } catch (Exception $e) {
                Functions::escreverLogErro($e);
            }
            $anexo->setIsOCRIniciado(1);
            $anexo->setIsOCRFinalizado(1);
            $anexo->setQtdePaginas(1);
            $anexo->setUsuario(UsuarioController::getUsuarioLogadoDoctrine());
            $anexo->inserir();

        }
        foreach ($objResult->documentos->ordens as $key => $ordem) {
            $anexo = new Anexo();

            $configImportacao = IndexController::getConfigImportacao();
            $anexo->setProcesso((new Processo())->buscar($_POST['processo_id']));
            $anexo->setDataCadastro(new DateTime());
            $anexo->setTipo((new TipoAnexo())->buscar($configImportacao['tipo_anexo_ordem']));
            if (!empty($configImportacao['classificacao_anexo_ordem'])) {
                $anexo->setClassificacao((new Classificacao())->buscar($configImportacao['classificacao_anexo_ordem']));
            }
            $anexo->setData(new DateTime($ordem->dataEmissao));
            $anexo->setNovoVencimentoProcesso(null);

            $anexo->setDescricao(substr($ordem->descricao, 0, 100));
            $anexo->setExercicio($anexo->getData()->format('Y'));
            $anexo->setNumero($ordem->numero . "/" . $ordem->exercicio);
            $anexo->setValor(empty($ordem->valor) ? 0 : $ordem->valor);
            $anexo->setCodigoImportacao("fiorilli" . $ordem->id . "-ordemPagamento");

            $pdf_decoded = base64_decode(str_replace('data:application/pdf;base64,', '', $ordem->notaPdf));
            $nomeArquivo = time() . $key . ".pdf";
            $pdf_path = $anexo->getPath() . $nomeArquivo;
            $pdf = fopen($pdf_path, 'w');
            fwrite($pdf, $pdf_decoded);
            fclose($pdf);


            $anexo->setArquivo($nomeArquivo);
            try {
                $anexo->setTextoOCR((new Parser())->lxParseFile($pdf_path));
            } catch (Exception $e) {
                Functions::escreverLogErro($e);
            }
            $anexo->setIsOCRIniciado(1);
            $anexo->setIsOCRFinalizado(1);
            $anexo->setQtdePaginas(1);
            $anexo->setUsuario(UsuarioController::getUsuarioLogadoDoctrine());
            $anexo->inserir();

        }
        self::setMessage(TipoMensagem::SUCCESS, "Documentos importados com sucesso.", NULL, true);
    }

    public function converter()
    {
        $anexo = new Anexo();
        $anexo = $anexo->buscar($_POST["anexo"]);
        $converter = new Converter();
        $estaNaFila = $converter->listarPorCampos(array("anexo" => $anexo->getId()));
        if (count($estaNaFila) > 0) {
            self::setMessage(TipoMensagem::SUCCESS, 'O anexo já está na fila de conversão.', null, true);
        } else {
            $converter->setAnexo($anexo);
            $converter->inserir();
            self::setMessage(TipoMensagem::SUCCESS, 'Anexo adicionado à fila de conversão, esse processo demora alguns minutos. ', null, true);
        }
    }

    /**
     * @throws BusinessException
     * @throws Exception
     */
    private function setCampos(Anexo $anexo)
    {
        $anexo->setTipo((new TipoAnexo())->buscar($_POST['tipo_documento_id']));
        $numeroAnexo = $anexo->getProcesso()->getNumeroAnexo() + 1;
        //O número gerado para ser atribuído ao anexo agora considera o exercício do processo, ao invés do ano corrente
        $novoNumero = $anexo->getProcesso()->getNumero() . substr($anexo->getProcesso()->getExercicio(),2) . $numeroAnexo;
        if (!empty($_POST['classificacao_documento_id'])) {
            $anexo->setClassificacao((new Classificacao())->buscar($_POST['classificacao_documento_id']));
        }
        $anexo->setData(new DateTime(Functions::converteDataParaMysql($_POST['data_doc'])));
        if (isset($_POST['novoVencimentoProcesso']) && !empty($_POST['novoVencimentoProcesso'])) {
            $anexo->setNovoVencimentoProcesso(new DateTime(Functions::converteDataParaMysql($_POST['novoVencimentoProcesso'])));
            if (!isset($_FILES['arquivo_processo']) || empty($_FILES['arquivo_processo']['name'])) {
                throw new BusinessException("É obrigatório informar o arquivo quando o anexo altera o vencimento do processo.");
            }
        } else {
            $anexo->setNovoVencimentoProcesso(null);
        }
        $anexo->setDescricao($_POST['descricao_doc']);
        $anexo->setExercicio($anexo->getData()->format('Y'));
        if(!empty($_POST['auto_numero_doc']) && $_POST['auto_numero_doc'] == 1){
            if (empty($anexo->getProcesso()->getNumero())){
                $anexo->setIsAutoNumeric(true);
            } else {
                if(empty($anexo->buscarPorCampos(array('numero' => $novoNumero)))){
                    $anexo->setIsAutoNumeric(true);
                } else {

                    while(!empty($anexo->buscarPorCampos(array('numero' => $novoNumero)))){
                    
                        $numeroAnexo++;
                        $novoNumero++;
                        
                }

                    $anexo->getProcesso()->setNumeroAnexo($numeroAnexo);
                    $anexo->setNumero($novoNumero);
                }
            }
        }elseif(isset($_POST['numero_doc'])){
            
            if($_POST['numero_doc'] == ($novoNumero)){
                $anexo->setIsAutoNumeric(true);
            } else {
                $anexo->setNumero($_POST['numero_doc']);
            }

        }
        $anexo->setIsCirculacaoInterna(!empty($_POST['is_circulacao_interna']) && $_POST['is_circulacao_interna'] == 1);
        if (!empty($_POST['valor_doc'])) {
            $anexo->setValor(Functions::realToDecimal($_POST['valor_doc']));
        }
        $anexo->setQtdePaginas(isset($_POST['paginas_doc']) && !empty($_POST['paginas_doc']) ? $_POST['paginas_doc'] : null);
        $anexo->setUsuario(UsuarioController::getUsuarioLogadoDoctrine());
        return $anexo;
    }
    public function getServicoOcrArquivo(){
        /*
        Usar fazendo request
        */
        set_time_limit(40);
        $filePath = $_POST['file_path'];
        $text = (new Parser())->lxParseFile($filePath); 
        echo json_encode(array("text" => utf8_encode($text)));
    }

    private function getOrcArquivo($filePath){
        $response = (new Builder(APP_URL."anexo/getServicoOcrArquivo"))
            ->setMethod(HTTP_METHOD::POST)
            ->setBody(['file_path' => $filePath])
            ->addHeader('Accept: application/json')
            ->verifySSL(false)
            ->build()
            ->send()
            ->getBody()
            ->toObject();

        if($response){
            return $response->text;
        }    
        return "";
    }

    /**
     * @throws BusinessException
     * @throws Exception
     */
    private function setAnexo(Anexo $anexo, $filePath = null)
    {
        $this->setCampos($anexo);
        $arquivo_old = false;
        if (!is_null($filePath)) {
            $arquivo_old = $anexo->getArquivo();
            
            if (rename($filePath, $anexo->getPath() . basename($filePath))) {
                $filePath = $anexo->getPath() . basename($filePath);
            } else {
                self::setMessage(TipoMensagem::ERROR, "Falha ao gerar arquivo.", null, true);
                die;
            }
            $anexo->setArquivo(basename($filePath));

        } else if (isset($_FILES['arquivo_processo']) && !empty($_FILES['arquivo_processo']['name'])) {
            $arquivo_old = $anexo->getArquivo();
            $nome_arquivo = (new Upload('arquivo_processo', $anexo->getPath(), array('pdf', 'png', 'gif', 'doc', 'docx', 'jpg', 'jpeg', 'xsl', 'xslx', 'mp3', 'mp4')))->upload();
            $dir_arquivo = $anexo->getPath();
            $anexo->setArquivo($nome_arquivo);
            try {
                $file = $dir_arquivo . $nome_arquivo;
                if (is_file($file) && Functions::isPDF($file) && !Functions::isPDFA($file)) {
                    $anexo->setTextoOCR($this->getOrcArquivo($file));
                    $anexo->setIsDigitalizado(false);                                        
                } else if (is_file($file) && Functions::isPDF($file) && !$anexo->getIsOCRFinalizado() && !Functions::isPDFA($dir_arquivo . $nome_arquivo)) {
                    $anexo->setTextoOCR($this->getOrcArquivo($dir_arquivo . $nome_arquivo));
                    $anexo->setIsOCRIniciado(true);
                    $anexo->setIsOCRFinalizado(true);
                } else if (Functions::isImage($file)) {
                    $anexo->setArquivo(basename(Functions::imageToPdf($file)));
                } else {
                    $anexo->setArquivo(basename($file));
                }
            } catch (TechnicalException $e) {
                Functions::escreverLogErro($e);
            }
            // if (is_file($dir_arquivo . $arquivo_old) && !isset($_POST['mesclar_arquivos'])) {
            //     unlink($dir_arquivo . $arquivo_old);
            // }
        } else if (!empty($_POST['arquivo_processo'])) {
            $nome_arquivo = $_POST['arquivo_processo'];
            $dir_arquivo = Processo::getTempPath();
            // Se a fonte é de digitalização
            $anexo->setIsDigitalizado(true);
        }
        if ($anexo->getArquivo() != null && isset($_POST['mesclar_arquivos'])) {
            //Esta fora do primeiro if ($_FILES) pq o arquivo pode ter sido enviado pelo scanner
            if (!isset($dir_arquivo)) {
                $dir_arquivo = $anexo->getPath();
            }
            if (!isset($nome_arquivo)) {
                $nome_arquivo = $anexo->arquivo;
            }
            $arquivos_mesclar = array(
                !empty($arquivo_old) ? $anexo->getPath() . $arquivo_old : $anexo->getPath() . $anexo->getArquivo(),
                $dir_arquivo . $nome_arquivo
            );
            if ($this->merge($arquivos_mesclar, $anexo->getPath() . $nome_arquivo)) {
                if (isset($_FILES['arquivo_processo'])) {
                    array_pop($arquivos_mesclar);
                }
                foreach ($arquivos_mesclar as $arquivo) {
                    if ($anexo->getArquivo() !== basename($arquivo)) {
                        Functions::removerArquivo($arquivo);
                    }
                }
            }
        }
        if (isset($nome_arquivo) && !empty($nome_arquivo)) {
            //E possível inserir anexo sem arquivo
            $anexo->setArquivo($nome_arquivo);
        }
    }

    function inserir($type = null)
    {
        if ($type === 'multiplos') {
            $this->salvarMultiplosAnexos();
            return;
        }
        try {
            //Se inserção for do tipo upload de arquivo e não existir arquivo:
            if ($_POST['tipo_upload'] == 'upload' && empty($_FILES['arquivo_processo'])) {
                throw new TechnicalException('Selecione ao menos um arquivo para fazer upload.');
            }
	        $processo = !empty($_POST['processo_id']) ? (new Processo())->buscar($_POST['processo_id']) : unserialize($_SESSION['processo']);
	        if (!empty($_POST['processo_id']) && !self::possuiPermissao($processo)) {
                throw new BusinessException(" Permitido manipular anexos quando tramitado para o setor responsável.");
            }
	        $anexo = isset($_SESSION['anexo']) ? unserialize($_SESSION['anexo']) : new Anexo();
            $anexo->setProcesso($processo);
	        $anexo->setDataCadastro(new DateTime());
	        // Se inserção for a partir de um modelo de documento:
	        if ($_POST['tipo_upload'] == 'model') {
                $nomeArquivo = date("Ymd") . uniqid();
                $mpdf = new Html2Pdf($nomeArquivo);
                $mpdf->WriteHTML($_POST['texto']);
                $filePath = FILE_PATH . 'documentos/temp/' . $nomeArquivo . "." . "pdf";
                $mpdf->Output($filePath, 'F');
                $anexo->setTextoOCR($_POST['texto']);
                $this->setAnexo($anexo, $filePath);
	        } else {
		        $this->setAnexo($anexo);
	        }
	        $processo->adicionaAnexo($anexo);
	        if (empty($_POST['processo_id'])) {
		        $_SESSION['processo'] = serialize($processo);
	        } else {
                $anexo->inserir();
                if ($anexo->ehPdf() && isset($_FILES['arquivo_processo']) && !empty($_FILES['arquivo_processo']['name'])) {
                    $anexoConverter = (new AnexoDao())->buscar($anexo->getId());
                    $converter = new Converter();
                    $converter->setAnexo($anexoConverter);
                    $converter->inserir();
                }
            }
	        $output = ['anexo_id' => $anexo->getId(), 'nome_arquivo' => null, 'msg' => "Anexo adicionado com sucesso!", 'tipo' => TipoMensagem::SUCCESS];
        } catch (UniqueConstraintViolationException $ex) {
            Functions::escreverLogErro($ex);
            $output = ['nome_arquivo' => null, 'msg' => "Registro já cadastrado: O tipo de anexo e seu número devem ser únicos no processo.", 'tipo' => TipoMensagem::ERROR];
        } catch (AppException $ex) {
            $output = ['nome_arquivo' => null, 'msg' => $ex->getMessage(), 'tipo' => TipoMensagem::ERROR];
            Functions::escreverLogErro($ex);
        } catch (DBALException $ex) {
            $output = ['nome_arquivo' => null, 'msg' => "Ocorreu um erro ao registrar o arquivo.", 'tipo' => TipoMensagem::ERROR];
            parent::registerLogError($ex);
        } catch (Exception $ex) {
            $output = ['nome_arquivo' => null, 'msg' => "Erro durante o envio do arquivo. ERRO: {$ex->getMessage()}", 'tipo' => TipoMensagem::ERROR];
            parent::registerLogError($ex);
        }
        echo json_encode($output);
    }
    /**********************************/
    /***Última Alteração: 03/02/2023***/
    /*************André****************/
    function atualizar()
    {
        try {
            $processo = !empty($_POST['processo_id']) ? (new Processo())->buscar($_POST['processo_id']) : unserialize($_SESSION['processo']);
            if (!empty($_POST['processo_id']) && !self::possuiPermissao($processo)) {
                throw new BusinessException(" Permitido manipular anexos quando tramitado para o setor responsável.");
            }
            $anexo = !empty($_POST['id']) ? (new Anexo())->buscar($_POST['id']) : $processo->getAnexos()->get($_POST['indice']);
            $anexoOld = clone $anexo;
            if (empty($_POST['processo_id'])) {
                $_SESSION['processo'] = serialize($processo);
            }
            if(isset($_POST['auto_numero_doc'])){
                $anexo->setIsAutoNumeric(true);
            }
            if ($_POST['tipo_upload'] == 'model') {
                $nomeArquivo = $anexo->getArquivo();
                $mpdf = new Html2Pdf($nomeArquivo);
                $mpdf->WriteHTML($_POST['texto']);
                $filePath = FILE_PATH . 'documentos/temp/' . $nomeArquivo;
                $mpdf->Output($filePath, 'F');
                $this->setAnexo($anexo, $filePath);
                if (!empty($anexo->getId())){
                    $anexo->setTextoOCR($_POST['texto']);
                    $anexo->setPaginacao(null);
                    $anexo->atualizar(true, true, $_POST['motivo'] ?? null);
                }
            } else {
                $this->setAnexo($anexo);
                if ($anexoOld->getPath(true, false) != $anexo->getPath(true, false)) {
                    $nameOld = $anexoOld->getArquivo(false, false, true);
                    $nameNew = $anexo->getPath(true, false) . $anexo->getArquivo(false, false, true);
                    if (file_exists($nameOld)) {
                        rename($nameOld, $nameNew);
                    }
                }
                
                if (is_file($anexoOld->getArquivo(true)) && $anexoOld->getArquivo(true) != $anexo->getArquivo(true)){

                    (new SubstituicaoController())->montarSubstituicao($anexo, $anexoOld);

                }
                $anexo->setPaginacao(null);
                if (!is_null($anexo->getId())) {
                    $anexo->atualizar(true, true, $_POST['motivo'] ?? null);
                } else {
                    $processo->setAnexo($_POST['indice'], $anexo);
                    $_SESSION['processo'] = serialize($processo);
                }
            }

            self::setMessage(TipoMensagem::SUCCESS, "Anexo atualizado com sucesso!", null, true);
        } catch (AppException $ex) {
            self::setMessage(TipoMensagem::ERROR, $ex->getMessage(), null, true);
        } catch (DBALException $ex) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao atualizar o anexo.", null, true);
            parent::registerLogError($ex);
        } catch (Exception $ex) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao atualizar o anexo. ERRO: {$ex->getMessage()}", null, true);
            parent::registerLogError($ex);
        }
    }

    function inserirDigitalizacao()
    {
        error_reporting(E_ERROR);
        ignore_user_abort(true);
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        $processo = !empty($_POST['processo_id']) ? (new Processo())->buscar($_POST['processo_id']) : unserialize($_SESSION['processo']);
        $ext = "jpg";
        $filename = uniqid() . '.' . $ext;
        if (file_put_contents($processo->getAnexosPath() . $filename, file_get_contents($_POST['data']))) {
            echo $filename;
        } else {
            echo false;
        }
    }

    function excluir()
    {
        $args = func_get_args();
        try {
            if (isset($args[1])) {
                $anexo = new Anexo();
                $anexo = $anexo->buscar($args[1]);
                $permissaoCod = $this->podeSerRemovido($anexo);
                if ($permissaoCod !== 100) {
                    $this->retornarMensagemNaoPermitido($permissaoCod);
                    return;
                }
                $anexo->remover($args[1]);
                $componente = new Componente();
                $componente->reordenarComponentes($componente->listarPorCampos(array("processo" => $anexo->getProcesso())));
            } else {
                $processo = unserialize($_SESSION['processo']);
                $anexo = $processo->getAnexos()->get($_POST['indice']);
                $processo->removeAnexo($anexo);
                $_SESSION['processo'] = serialize($processo);
            }
            self::setMessage(TipoMensagem::SUCCESS, 'Anexo removido com sucesso!', null, true);
        } catch (ForeignKeyConstraintViolationException $e) {
            if (str_contains($e->getMessage(), "documento_requerido")) {
                $this->retornarMensagemNaoPermitido(423, $anexo->getProcesso()->getId()); // Regra de tramitação impede a exclusão.
            } else {
                $this->retornarMensagemNaoPermitido(409); // Algum relacionamento está impedindo a remoção.
            }
        } catch (SecurityException|AppException $e) {
            self::setMessage(TipoMensagem::ERROR, $e->getMessage(), null, true);
        } catch (DBALException $e) {
            self::setMessage(TipoMensagem::ERROR, "Ocorreu um erro ao remover anexo.", null, true);
            parent::registerLogError($e);
        } catch (Exception $e) {
            self::setMessage(TipoMensagem::ERROR, "Ocorreu um erro ao remover anexo. ERRO: {$e->getMessage()}", null, true);
            parent::registerLogError($e);
        }
    }

    /**
     * Valida as regras de negócio para pode remover um arquivo. Retorna um códio com o status da validação.
     *
     * @param Anexo $anexo
     * @return int 100: Permitido | 424: Necessário informar o motivo | 403: Não permitido
     */
    private function podeSerRemovido(Anexo $anexo): int
    {
        $usuario = UsuarioController::getUsuarioLogadoDoctrine();
        if ($usuario->isAdm()) {
            return 100;
        }
        $bloqueioAtivado = AppController::getConfig('bloquear_anexo');
        if (!$bloqueioAtivado) {
            return 100; // Permitido
        }
        $permissaoExclusao = $anexo->podeSerAlterado($usuario);
        if ($permissaoExclusao === PermissaoStatus::OK || $permissaoExclusao === PermissaoStatus::INDEFINIDO) {
            return 100;
        } else if ($permissaoExclusao === PermissaoStatus::REQUER_MOTIVO ) {
            if (empty($_POST['motivo'])) {
                return 424; // Solicitar motivo
            }
            return 100; // Permitido
        }
        return 403; // Recusado
    }

    /**
     * Realiza OCR de todas as imagens do documento anexado
     */
    function realizarOCR()
    {
        ignore_user_abort(true);
        set_time_limit(0);
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        try {
            $anexo_id = func_get_args()[0];
            $anexo = (new Anexo())->buscar($anexo_id);
            Functions::send_message($anexo->getId(), 'Realizando OCR para anexo ' . $anexo . " ({$anexo->getImagens()->count()} páginas)", 0);
            $anexo->realizarOCR();
            Functions::send_message('FIM', "OCR do para $this realizada com sucesso.", 100);
        } catch (AppException $ex) {
            Functions::send_message('ERRO', $ex->getMessage(), 100);
        } catch (DBALException $ex) {
            Functions::send_message('ERRO', "Erro ao realizar OCR para o documento.", 100);
            parent::registerLogError($ex);
        } catch (Exception $ex) {
            Functions::send_message('ERRO', "Erro ao realizar OCR para o documento. ERRO {$ex->getMessage()}", 100);
            parent::registerLogError($ex);
        }
    }

    private function retornarMensagemNaoPermitido($httpResponseCode, $id = null) {
        http_response_code($httpResponseCode);
        if ($httpResponseCode === 403) {
            self::setMessage(TipoMensagem::ERROR, "Você não é o responsável pelo anexo ou o anexo já passou por alguma tramitação.", null, true);
        } else if ($httpResponseCode === 423) {
            self::setMessage(TipoMensagem::ERROR, "Não permitido. O anexo foi definido como obrigatório durante uma tramitação. Remova ou solicite a remoção da obrigatoriedade do anexo definida durante tramitação para poder prosseguir com a ação.", $id, true);
        } else if ($httpResponseCode === 424) {
            self::setMessage(TipoMensagem::ERROR, "Informe o motivo para a remoção do anexo.", null, true);
        } else if ($httpResponseCode === 409) {
            self::setMessage(TipoMensagem::ERROR, "Não foi possível remover o anexo devido a alguma dependência interna. Por favor, contate o suporte para auxílio.", null, true);
        } else {
            self::setMessage(TipoMensagem::ERROR, "Não foi possível concluir sua solicitação. Por favor, contate o suporte para auxílio.", null, true);
        }
    }

    /**
     * Validar se o usuário pertence ao mesmo setor que o processo.
     *
     * @param Processo $processo
     * @return bool
     * @throws ORMException
     */
    public static function possuiPermissao($processo)
    {
        /**
         * @var Tramite $tramiteAtual
         */
        $usuario = UsuarioController::getUsuarioLogadoDoctrine();
        
        $tramites = $processo->getTramiteAtualSemApenso();
        /**
	* TODO: verifcar sugestão:
	* Erro log:  [20-Sep-2021 09:47:29 America/Campo_Grande] PHP Warning:  count(): Parameter must be an array or an object that implements Countable in /var/www/html/LxProcessos/src/App/Controller/AnexoController.php on line 608
	* O método getTramiteAtual() retorna um Collection, portanto deve ser usado método count()
	* Sugestão:
	* if(!$tramites->isEmpty()){ (linha 615)
	*/
        if((!empty($tramites) && is_array($tramites) && count($tramites) > 1) ||
            ($tramites instanceof \Doctrine\ORM\PersistentCollection && $tramites->count() > 0)) { // Não remover o namespace. Se removido, causará falso negativo.
            $tramiteAtual = $tramites->last();
        } else if(!empty($tramites) && is_object($tramites)){
            $tramiteAtual = $tramites;
        }
        $setorAtual = null;
        if (isset($tramiteAtual)) {
            $setorAtual = $tramiteAtual->getSetorAtual();
            if ($tramiteAtual->getResponsavel()->getId() === $usuario->getId()) {
                return true;
            }
        }
        $tiposUsuario = array(TipoUsuario::ADMINISTRADOR, TipoUsuario::MASTER);
        if (in_array($usuario->getTipo(), $tiposUsuario) || is_null($setorAtual) || is_null($setorAtual->getNome())) {
            return true;
        }

        if($usuario->getTipo() === TipoUsuario::INTERESSADO){
            return AppController::getParametosConfig()['processo_setor_contribuinte_id'] == $setorAtual->getId();
        }
        $setores = $usuario->getSetores();
        // $setores->contains($setorAtual); Em produção não houve o resultado esperado.
        $result = array_filter($setores->toArray(), function ($item) use ($setorAtual){
            return $item->getId() === $setorAtual->getId();
        });
        $possui_permissao = !empty($result);
        if ($possui_permissao) { // Usuário pertence diretamente ao setor atual.
            return true;
        }
        // Caso não pertenca diretamente ao setor atual, verificar se pertence a um subsetor do atual.
        try {
            foreach ($usuario->getSetores() as $setor) {
                $setores_pais = $setor->listarSetoresPai();
                if (!is_null($setores_pais)) {
                    $setores_pais_ids = array_map(function ($item){
                        return $item->getId();
                    }, $setores_pais);
                    if (in_array($setorAtual->getId(), $setores_pais_ids)) {
                        // return true;
                    }
                }
            }
        } catch (Exception $e) {
            Functions::escreverLogErro($e);
        }
        return false;
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     * @throws TransactionRequiredException
     */
    public function historico($anexo_id) {
        $anexo = (new Anexo())->buscar($anexo_id);
        $_REQUEST["historico"] = HistoricoAnexo::historico($anexo);
        if (empty($_REQUEST["historico"])) {
            $_REQUEST["historico"] = HistoricoAnexo::historicoLegado($anexo);
        }
        $this->loadSemTemplate($this->class_path, "historico");
    }

    /**
     * @throws SmartyException
     */
    private function retornoAnexoRemovido() {
        $vars = ["page_title" => "Anexo removido", "page_content" => "O anexo solicitado não está mais disponível."];
        $this->load('Public', '404', true, false, $vars);
    }

    private function salvarMultiplosAnexos() {
        /**
         * @var Processo $processo
         */
        try {
            if (empty($_FILES['arquivos'])) {
                throw new TechnicalException('Nenhum arquivo carregado.');
            }
            if (empty($_POST['processo_id'])) {
                $processo = unserialize($_SESSION['processo']);
            } else {
                $processo = (new ProcessoDao())->buscar($_POST['processo_id']);
            }
            if (!is_null($processo->getId()) && !self::possuiPermissao($processo)) {
                throw new BusinessException(" Permitido manipular anexos quando tramitado para o setor responsável.");
            }
            $anexo = new Anexo();
            $anexo->setProcesso($processo);
            $this->setAnexo($anexo);
            $arquivos = (new Upload('arquivos', $anexo->getPath(), array('pdf', 'png', 'gif', 'doc', 'docx', 'jpg', 'jpeg')))->upload();
            if (isset($_POST['merge']) && $_POST['merge'] === 'on') { // Mesclar documentos.
                $dir = $anexo->getPath();
                $arquivosAux = [];
                foreach ($arquivos as $arquivo) {
                    $arquivosAux[] = $dir . $arquivo;
                }
                $arquivo = uniqid("{$processo->getNumero()}_{$processo->getExercicio()}_") . ".pdf";
                if (!$this->merge($arquivosAux, $dir . $arquivo)) {
                    throw new TechnicalException("A mesclagem dos anexos falhou");
                }
                $arquivos = [$arquivo];
            }
            $qtde = count($arquivos);
            if(isset($_POST['numero_doc'])) {
                if (str_contains($_POST['numero_doc'], ",")) {
                    $numeros = explode(",", $_POST['numero_doc']);
                } else if (str_contains($_POST['numero_doc'], "-")) {
                    $faixa = explode("-", $_POST['numero_doc']);
                    for ($i = intval($faixa[0]); $i <= intval($faixa[1]); $i++) {
                        $numeros[] = $i;
                    }
                }
            }
            if (!empty($numeros)) {
                $anexo->setNumero($numeros[0]);
            }
            $anexo->setDataCadastro(new DateTime());
            $status = TipoMensagem::SUCCESS;
            for ($position = 0; $position < $qtde; $position++) {
                try {
                    $nome_arquivo = $arquivos[$position];
                    if ($position > 0) {
                        $anexo = new Anexo();
                        $anexo->setProcesso($processo);
                        $this->setAnexo($anexo);
                        if (!empty($numeros)) {
                            $anexo->setNumero($numeros[$position]);
                        }
                        $anexo->setDataCadastro(new DateTime());
                    }
                    $dir_arquivo = $anexo->getPath();
                    $anexo->setArquivo($nome_arquivo);
                    $file = $dir_arquivo . $nome_arquivo;
                    if (is_file($file)) {
                        if (Functions::isPDF($file) && !Functions::isPDFA($file)) {
                            $anexo->setTextoOCR($this->getOrcArquivo($file));
                            $anexo->setIsDigitalizado(false);
                        } else if (Functions::isPDF($file) && !$anexo->getIsOCRFinalizado() && !Functions::isPDFA($dir_arquivo . $nome_arquivo)) {
                            $anexo->setTextoOCR($this->getOrcArquivo($dir_arquivo . $nome_arquivo));
                            $anexo->setIsOCRIniciado(true);
                            $anexo->setIsOCRFinalizado(true);
                        } else if (Functions::isImage($file)) {
                            $anexo->setArquivo(basename(Functions::imageToPdf($file)));
                        }
                    }
                    $processo->adicionaAnexo($anexo);
                    /**
                     * @var Anexo[] $anexos
                     */
                    $anexos[] = $anexo;
                    $output[] = ['nome_arquivo' => $_FILES["arquivos"]["name"][$position], 'msg' => "Anexo adicionado com sucesso.", 'tipo' => TipoMensagem::SUCCESS];
                } catch (Exception $e) {
                    $status = TipoMensagem::PARTIAL_SUCCESS;
                    $output[] = ['nome_arquivo' => $_FILES["arquivos"]["name"][$position], 'msg' => "Falha ao adicionar o anexo: " . get_class($e) . ".", 'tipo' => TipoMensagem::ERROR];
                    Functions::escreverLogErro($e);
                }
            }
            if (empty($_POST['processo_id'])) {
                $_SESSION['processo'] = serialize($processo);
            } else {
                $processo->atualizar();
                if (!empty($anexos)) {
                    foreach ($anexos as $anexoConverter) {
                        if (is_file($anexoConverter->getArquivoOriginal())) {
                            $converter = new Converter();
                            $converter->setAnexo($anexoConverter);
                            $converter->inserir();
                        }
                        HistoricoAnexo::registrar(TipoHistoricoAnexo::INSERT, null, null, null, $anexoConverter);
                    }
                }
            }
            echo json_encode(['tipo' => $status, 'data' => $output]);
            exit;
        } catch (AppException $ex) {
            Functions::escreverLogErro($ex);
            $output = ['msg' => $ex->getMessage(), 'tipo' => TipoMensagem::ERROR];
        } catch (DBALException $ex) {
            Functions::escreverLogErro($ex);
            $output = ['msg' => "Ocorreu um erro ao registrar o arquivo.", 'tipo' => TipoMensagem::ERROR];
            parent::registerLogError($ex);
        } catch (Exception $ex) {
            Functions::escreverLogErro($ex);
            $output = ['msg' => "Erro durante o envio dos arquivos. ERRO: {$ex->getMessage()}.", 'tipo' => TipoMensagem::ERROR];
            parent::registerLogError($ex);
        }
        echo json_encode($output);
    }
}
