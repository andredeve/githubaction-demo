<?php
/**
 * Created by PhpStorm.
 * User: ander
 * Date: 11/01/2019
 * Time: 13:54
 */
namespace App\Controller;

use App\Model\Processo;
use Core\Controller\AppController;
use Core\Exception\AppException;
use Doctrine\DBAL\DBALException;
use Exception;

class ConsultaController extends AppController
{
    public function __construct()
    {
        parent::__construct(get_class());
    }

    public function processo()
    {
        try {
            $processo = new Processo();
            $args = func_get_args();
            if (empty($args[1])) {
                return $this->error404();
            }

            if (count($args) > 2){
                $_REQUEST['processo'] = $processo->buscarPorCampos(["numero" => $args[1], "exercicio" => $args[2]]);
            } else {
                $_REQUEST['processo'] = $processo->buscar($args[1]);
            }
            
            if ($_REQUEST['processo'] == null) {
                return $this->error404();
            }
        } catch (DBALException $e) {
            parent::registerLogError($e);
            die("Erro ao buscar processo. ");
        } catch (AppException $e) {
            die($e->getMessage());
        } catch (Exception $e) {
            parent::registerLogError($e);
            die("Erro ao buscar processo. Erro: {$e->getMessage()}");
        }
        $this->load('Public', 'processo', true, true);
    }

    public function interessado()
    {
        try {
            $processo = new Processo();
            $args = func_get_args();
            if (empty($args[1])) {
                return $this->error404();
            }
            $processo = $processo->buscar($args[1]);
            if($processo->getTokenInterecao() != $args[2] || !$processo->interessadoPodeInteragir()){
                $this->error403();
            }
            $_REQUEST['processo'] = $processo;
            if ($_REQUEST['processo'] == null) {
                return $this->error404();
            }
        } catch (DBALException $e) {
            parent::registerLogError($e);
            die("Erro ao buscar processo. ");
        } catch (AppException $e) {
            die($e->getMessage());
        } catch (Exception $e) {
            parent::registerLogError($e);
            die("Erro ao buscar processo. Erro: {$e->getMessage()}");
        }
        $this->load('Public', 'interessado', true, true);
    }

    public function index()
    {
        $this->load('Public', 'consulta', true, true);
    }
}