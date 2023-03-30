<?php
/**
 * Created by PhpStorm.
 * User: ander
 * Date: 22/01/2019
 * Time: 09:36
 */

namespace App\Controller;

use App\Enum\TipoLog;
use App\Model\CategoriaDocumento;
use App\Model\Documento;
use App\Model\Log;
use App\Model\Processo;
use Core\Controller\AppController;
use Core\Enum\TipoMensagem;
use Core\Util\Functions;
use App\Model\ModeloDocumento;

use App\Util\Html2Pdf;

use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\IOFactory;

use Exception;
use Core\Exception\AppException;
use Doctrine\DBAL\DBALException;

class DocumentoController extends AppController
{
    const TEMP_DIR = FILE_PATH . 'documentos/temp/';
    
    function __construct()
    {
        parent::__construct(get_class());
    }

    function inserir()
    {
        $isAjax = isset($_REQUEST['ajax']) ? true : false;
        try {
            $this->setDocumento();
            self::setMessage(TipoMensagem::SUCCESS, 'Documento cadastrado com sucesso!', null, $isAjax);
        } catch (UniqueConstraintViolationException $e) {
            self::setMessage(TipoMensagem::ERROR, "Documento: Registro já cadastrado!", null, $isAjax);
            parent::registerLogError($e);
        } catch (DBALException $e) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao inserir registro. ", null, $isAjax);
            parent::registerLogError($e);
        } catch (AppException $e) {
            self::setMessage(TipoMensagem::ERROR, $e->getMessage(), null, $isAjax);
        } catch (Exception $e) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao inserir registro. Erro: {$e->getMessage()}", null, $isAjax);
            parent::registerLogError($e);
        }
    }

    function atualizar()
    {
        $isAjax = isset($_REQUEST['ajax']);
        try {
            $this->setDocumento();
            self::setMessage(TipoMensagem::SUCCESS, 'Documento atualizado com sucesso!', null, $isAjax);
        } catch (DBALException $e) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao inserir registro. ", null, $isAjax);
            parent::registerLogError($e);
        } catch (AppException $e) {
            self::setMessage(TipoMensagem::ERROR, $e->getMessage(), null, $isAjax);
        } catch (Exception $e) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao inserir registro. Erro: {$e->getMessage()}", null, $isAjax);
            parent::registerLogError($e);
        }
    }

    private function setDocumento()
    {
        $session_processo = isset($_SESSION['processo']) ? unserialize($_SESSION['processo']) : null;
        $processo = !empty($_POST['processo_id']) ? (new Processo())->buscar($_POST['processo_id']) : $session_processo;
        $documento = !empty($_POST['id']) ? (new \App\Model\Documento())->buscar($_POST['id']) : ($session_processo != null ? $session_processo->getDocumentos()->get($_POST['indice']) : null);

        $documento = $documento != null ? $documento : new Documento();
        $documento->setProcesso($processo);
        $documento->setCategoria((new CategoriaDocumento())->buscar($_POST['categoria_id']));
        $documento->setVencimento(new \DateTime(Functions::converteDataParaMysql($_POST['vencimento'])));
        $documento->setData(new \DateTime(Functions::converteDataParaMysql($_POST['data'])));
        $documento->setNumero($_POST['numero']);
        $documento->setExercicio($_POST['exercicio']);
        $documento->setObservacoes($_POST['observacoes']);
        $processo->adicionaDocumento($documento);
        if ($processo->getId() != null) {
            $processo->atualizar();
            Log::registrarLog(TipoLog::ACTION_INSERT, $documento->getTableName(), "Registro criado", null, null, $documento->imprimir());
        } else {
            $_SESSION['processo'] = serialize($processo);
        }
    }

    /**
     * Gerar arquivo temporário a partir de um Modelo de Documento.
     *
     * @return string Resposta da solicição.
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     * @throws \PhpOffice\PhpWord\Exception\CopyFileException
     * @throws \PhpOffice\PhpWord\Exception\CreateTemporaryFileException
     * @throws \PhpOffice\PhpWord\Exception\Exception
     */

    function gerarTemporario()
    {
        $modelo = new ModeloDocumento();
        $modelo = $modelo->buscar($_POST['modelo_id']);
        $nomeArquivo = $_POST['nome_arquivo'] . ".pdf";
        $destinoWord = self::TEMP_DIR . "$nomeArquivo.doc";
        Settings::setPdfRendererPath(APP_PATH . 'vendor/tecnickcom/tcpdf/');
        Settings::setPdfRendererName(Settings::PDF_RENDERER_TCPDF);
        $phpword = new TemplateProcessor($modelo->getArquivo(true));
        foreach ($modelo->getVariaveis() as $variavel) {
            $phpword->setValue($variavel, filter_input(INPUT_POST, \Core\Util\Functions::sanitizeString($variavel)));
        }
        $phpword->saveAs($destinoWord);
        //Load temp file
        $phpWord = IOFactory::load($destinoWord);
        //Save it
        $xmlWriter = IOFactory::createWriter($phpWord, 'PDF');
        $filepath = self::TEMP_DIR . $nomeArquivo;
        $xmlWriter->save($filepath);
        unset($destinoWord);
        return $filepath;
    }

}