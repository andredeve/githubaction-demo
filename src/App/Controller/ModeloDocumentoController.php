<?php
namespace App\Controller;

use Core\Controller\AppController;
use App\Model\ModeloDocumento;
use Core\Util\Upload;
use App\Model\Dao\ModeloDocumentoDao;
use Exception;
use Doctrine\DBAL\DBALException;
use Core\Exception\AppException;

/**
 * Description of ModeloDocumento
 *
 * @author david
 */
class ModeloDocumentoController extends AppController{
    private $dao;
    
    public function __construct()
    {
        parent::__construct(get_class());
        $this->breadcrumb = "Modelos de Documento";
    }

    function gerarDocumento()
    {
        try {
            $modelo = $this->dao->buscar($_POST['id']);
            $nomeArquivo = $_POST['nome_arquivo'];
            $phpword = new PhpWord\TemplateProcessor($modelo->getArquivo(true));

            foreach ($modelo->getVariaveis() as $variavel) {
                $phpword->setValue($variavel, filter_input(INPUT_POST, \Core\Util\Functions::sanitizeString($variavel)));
            }
            
            $phpword->saveAs(ModeloDocumento::getPath() . "$nomeArquivo.docx");

        }  catch (DBALException $ex) {
            parent::registerLogError($ex);
            die("Erro ao gerar documento.");
        }  catch (AppException $ex) {
            die($ex->getMessage());
        } catch (\Exception $ex) {
           parent::registerLogError($ex);
            die("Erro ao gerar documento. Erro: {$ex->getMessage()}");
        }
    }

    function gerar()
    {
        $_REQUEST['breadcrumb'] = array(
            array('link' => $this->class_path, 'title' => $this->getBreadCrumbTitle()),
            array('link' => null, 'title' => 'Gerar Documento')
        );
        $permissao = $this->getPermissao('Documento');
        $prosseguir = $permissao instanceof PermissaoEntidade ? $permissao->getInserir() : $permissao;
        if ($prosseguir) {
            $classe = $this->classe;
            $args = func_get_args();
            if (empty($args[1])) {
                return $this->error404();
            }
            $_REQUEST['modelo'] = $classe::buscar($args[1]);
            if ($_REQUEST['modelo'] == null) {
                return $this->error404();
            }
            return $this->load($this->class_path, 'gerar', true, "Modelos de Documento", "Gerar documento", 'fas fa-sync-alt', "gerar");
        }
        return $this->error403();

    }

    function setEntidade(ModeloDocumento $modeloDocumento)
    {
        if (isset($_FILES['arquivo_modelo']['name'])) {
            $modeloDocumento->setArquivo((new Upload('arquivo_modelo', ModeloDocumento::getPath(), array('doc', 'docx')))->upload());
        }
    }
    
    /**
     * Busca um registro no na tabela modelo_documento.
     * @param integer $id
     * @return ModeloDocumento|null Retorna a entidade encontrada ou nulo.
     */
    function buscar($id) {
        $model = new ModeloDocumento();
        return $model->buscar($id);
    }
}
