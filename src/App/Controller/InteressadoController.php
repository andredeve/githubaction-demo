<?php /** @noinspection PhpUnused */

namespace App\Controller;

use App\Enum\TipoLog;
use App\Enum\TipoUsuario;
use App\Model\Interessado;
use App\Model\Log;
use App\Model\Pessoa;
use App\Model\Setor;
use App\Model\Usuario;
use Core\Controller\AppController;
use Core\Enum\TipoMensagem;
use Core\Exception\BusinessException;
use Core\Util\Functions;
use DateTime;
use Doctrine\DBAL\ConnectionException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\TransactionRequiredException;
use Exception;
use Serializable;

class InteressadoController extends AppController
{

    public function __construct()
    {
        parent::__construct(get_class());
        $this->breadcrumb = 'Interessados';
        $this->text_method = 'getNome';
    }

    /**
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     * @throws ORMException
     */
    function inserir()
    {
        $isAjax = !empty($_REQUEST['ajax']);
        $interessado = new Interessado();
        try{
            /*
            $CpfOuCnpj = empty($_POST['cpf']) ? $_POST['cnpj'] : $_POST['cpf'];
            $CpfOuCnpj = Functions::sanitizeNumber(
                filter_var($CpfOuCnpj,FILTER_SANITIZE_NUMBER_INT)
            );
            $cpf = !empty($_POST['cpf']) ? $CpfOuCnpj : $_POST['cpf'];
            $cnpj = !empty($_POST['cnpj']) ? $CpfOuCnpj : $_POST['cnpj'];
            $pessoa = new Pessoa();
            $pessoa_recebe = $pessoa->buscarPorCampos(array('cpf'=>$cpf, 'cnpj'=>$cnpj));


            \Doctrine\Common\Util\Debug::dump(!empty($pessoa_recebe) == false);
            die();

            if (!empty($pessoa_recebe) == false){
               throw new BusinessException('Erro: Usuário já registrado. Verifique!');
            }
            */
            $interessado->beginTransaction();
            $this->setInteressado($interessado);
            //$this->setUsuario();
            $interessado->inserir(true, false);
            $interessado->commit();
            Log::registrarLog(TipoLog::ACTION_INSERT, $interessado->getTableName(), "Registro inserido", null, null, $interessado->imprimir());
            self::setMessage(TipoMensagem::SUCCESS, 'Registro cadastrado com sucesso!', $interessado->getId(), $isAjax);
            if (!$isAjax) {
                $this->route($this->class_path);
                return ;
            }

        } catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException $ex) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao inserir registro. Mensagem: registro já cadastrado!", null, $isAjax);       
            self::registerLogError($ex);
        } catch (BusinessException $ex){
            self::setMessage(TipoMensagem::ERROR, $ex->getMessage(), null, $isAjax);          
            self::registerLogError($ex);
        } catch (Exception $ex) {
            $interessado->rollback();
            self::setMessage(TipoMensagem::ERROR, "Erro ao processar dados. Erro: {$ex->getMessage()}.", null, true);    
            self::registerLogError($ex);
        }
    }

    /**
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     * @throws ORMException
     */
    function atualizar()
    {
        $interessado = (new Interessado())->buscar($_POST["id"]);
        try{
            $isAjax = !empty($_REQUEST['ajax']);
            $interessado->beginTransaction();
            $this->setInteressado($interessado);
            $interessado->atualizar();
            $interessado->commit();

            Log::registrarLog(TipoLog::ACTION_INSERT, $interessado->getTableName(), "Registro inserido", null, null, $interessado->imprimir());
            self::setMessage(TipoMensagem::SUCCESS, 'Registro cadastrado com sucesso!', $interessado->getId(), $isAjax);

            if (!$isAjax) {
                return $this->route($this->class_path);
            }
        }catch (Exception $ex) {
            $interessado->rollback();
            self::setMessage(TipoMensagem::ERROR, "Erro ao processar dados. Erro: {$ex->getMessage()}.", null, true);
            self::registerLogError($ex);
        }

    }


    /**
     * Método genérico de listagem de registros de um objeto/tabela
     */
    public function listar()
    {
        $_REQUEST['breadcrumb'] = array(array('link' => null, 'title' => $this->getBreadCrumbTitle()));
    }

    /**
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     * @throws ORMException
     * @throws Exception
     */
    private function setInteressado(Interessado $interessado) :void
    {
        $_POST['ativo'] = true;
        $_POST['isExterno'] = ($_POST['isExterno'] == true) ? 1 : 0;
        $_POST['dataNascimento'] = !empty($_POST['dataNascimento']) ? new DateTime(Functions::converteDataParaMysql($_POST['dataNascimento'])) : null;
        $_POST['sexo'] = isset($_POST['sexo']) && !empty($_POST['sexo'])? $_POST['sexo']: null;
        if(isset($_POST['setor']) && !empty($_POST['setor'])){
            $_POST['setor'] = (new Setor())->buscar($_POST['setor']);
        }else{
            $_POST['setor'] = null;
        }

        (new PessoaController())->setPessoa();
        EnderecoController::setEndereco();
        $this->getValues($interessado);
    }
}
