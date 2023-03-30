<?php
/**
 * Created by PhpStorm.
 * User: ander
 * Date: 12/12/2018
 * Time: 13:57
 */

namespace App\Controller;

use App\Enum\TipoLog;
use App\Model\Log;
use App\Model\Remessa;
use App\Model\Tramite;
use App\Util\GuiaRemessa;
use Core\Controller\AppController;
use Core\Enum\TipoMensagem;
use Core\Exception\AppException;
use Core\Exception\BusinessException;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

class RemessaController extends AppController
{
    function __construct()
    {
        parent::__construct(get_class());
    }

    public function inserir()
    {
        try {
            $qtde_selecionados = $_POST['tramite_id'];
            if (count($qtde_selecionados) > 0) {
                $remessa = new Remessa();
                foreach ($_POST['tramite_id'] as $i => $tramite_id) {
                    $tramite = (new Tramite())->buscar($tramite_id);
                    if ($i == 0) {
                        $remessa->setSetorOrigem($tramite->getSetorAnterior());
                        $remessa->setSetorDestino($tramite->getSetorAtual());
                        $remessa->setHorario(new \DateTime());
                        $remessa->setResponsavelOrigem($tramite->getUsuarioEnvio());
                        $remessa->setResponsavelDestino($tramite->getUsuarioDestino());
                    }
                    $remessa->adicionaTramite($tramite);
                    $tramite->setRemessa($remessa);
                }
                $objeto_id = $remessa->inserir();
                Log::registrarLog(TipoLog::ACTION_INSERT, $remessa->getTableName(), "Registro criado", null, null, $remessa->imprimir());
                self::setMessage(TipoMensagem::SUCCESS, 'Registro cadastrado com sucesso!', $objeto_id, true);
            } else {
                throw new BusinessException("Seleciono menos 1 processo para gerar remessa.");
            }
        } catch (UniqueConstraintViolationException $e) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao inserir registro. Mensagem: registro já cadastrado!", null, true);
        }  catch (AppException $e) {
            self::setMessage(TipoMensagem::ERROR, $e->getMessage(), null, true);
        } catch (\Exception $e) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao inserir registro. Erro: {$e->getMessage()}.", null, true);
            parent::registerLogError($e);
        }
    }

    function buscar()
    {
        $_REQUEST['breadcrumb'] = array(
            array('link' => 'remessa', 'title' => 'Remessa'),
            array('link' => null, 'title' => 'Buscar')
        );
        return $this->load('remessa', 'buscar');
    }

    function imprimir()
    {
        try {
//            require_once APP_PATH . 'lib/fpdf/fpdf.php';
            $args = func_get_args();
            $remessa_id = $args[0];
            $remessa = (new Remessa())->buscar($remessa_id);
            $guia = new GuiaRemessa($remessa);
            $guia->gerar();
        }  catch (DBALException $ex) {
            parent::registerLogError($ex);
            die("Erro ao gerar arquivo.");
        }  catch (AppException $ex) {
            die($ex->getMessage());
        } catch (\Exception $ex) {
            parent::registerLogError($ex);
            die("Erro ao gerar arquivo. Erro: {$ex->getMessage()}.");
        }
    }

    public function listar()
    {
        $_REQUEST['breadcrumb'] = array(array('link' => null, 'title' => $this->getBreadCrumbTitle()));
    }

}