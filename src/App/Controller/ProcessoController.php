<?php /** @noinspection PhpRedundantCatchClauseInspection */

/** @noinspection PhpUnused */

namespace App\Controller;

use App\Enum\SigiloProcesso;
use App\Enum\TipoHistoricoAnexo;
use App\Enum\TipoHistoricoProcesso;
use App\Enum\TipoLog;
use App\Enum\TipoUsuario;
use App\Log\HistoricoAnexo;
use App\Model\Anexo;
use App\Model\Assinatura;
use App\Model\Assunto;
use App\Model\AssuntoProcesso;
use App\Model\CategoriaDocumento;
use App\Model\Classificacao;
use App\Model\HistoricoProcesso;
use App\Model\Interessado;
use App\Model\Local;
use App\Model\LocalizacaoFisica;
use App\Model\Log;
use App\Model\PermissaoEntidade;
use App\Model\Processo;
use App\Model\Remessa;
use App\Model\Setor;
use App\Model\StatusProcesso;
use App\Model\SubTipoLocal;
use App\Model\TipoAnexo;
use App\Model\TipoLocal;
use App\Model\Tramite;
use App\Model\Usuario;
use App\Util\CapaProcesso;
use App\Util\Email;
use App\Util\GuiaRemessa;
use Core\Controller\AppController;
use Core\Enum\TipoMensagem;
use Core\Exception\AppException;
use Core\Exception\BusinessException;
use Core\Exception\SecurityException;
use Core\Exception\TechnicalException;
use Core\Util\Functions;
use Core\Util\Report;
use Core\Util\Upload;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\PersistentCollection;
use Doctrine\ORM\TransactionRequiredException;
use Exception;
use FPDF;
use PDFMerger;
use SmartyException;
use TCPDF;
use ZipArchive;
use function mb_strtoupper;
use const APP_PATH;

class ProcessoController extends AppController
{
    const LX_SIGN_TOKEN = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJjbnBqIjoiMDMuNDM0Ljc5MlwvMDAwMS0wOSJ9.zPJLb7eQwyXL3qbIQdcG_fZTjTEoNh1ha6h1P-t3vYw';

    public function __construct()
    {
        parent::__construct(get_class());
        $this->breadcrumb = self::getParametosConfig('nomenclatura') . 's';
        $this->lxSignUrl = AppController::getConfig()['lxsign_url'];
    }

    public static function getExercicioAtual()
    {
        if(isset($_SESSION['exercicio']) &&  $_SESSION['exercicio'] != 'todos'){
            return $_SESSION['exercicio'];
        }
        return null;
    }

    /**
     * @return void
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     */
    function gerarCapa()
    {
        $processo_id = func_get_args()[1];
        $processo = (new Processo())->buscar($processo_id);
        $capa = new CapaProcesso($processo);
        $capa->gerar();
    }

    /**
     * Gerar guia de remessa eletrônica
     */
    function gerarGTE()
    {
        try {
            $args = func_get_args();
            $processo_id = $args[1];
            $fase = $args[2];
            $processo = (new Processo())->buscar($processo_id);
            $tramites = $processo->getTramites($fase);
            $tramites = !is_array($tramites)? array($tramites):$tramites;
            foreach ($tramites as $tramite) {
                $remessa = new Remessa();
                $remessa->setHorario(new DateTime());
                $remessa->setResponsavelOrigem($tramite->getResponsavel());
                $remessa->setSetorOrigem($tramite->getSetorAnterior());
                $remessa->setSetorDestino($tramite->getSetorAtual());
                $remessa->setResponsavelDestino($tramite->getUsuarioDestino());
                $tramite->setRemessa($remessa);
                $tramite->atualizar();
            }
            $guia = new GuiaRemessa($remessa);
            $guia->gerar();
        }  catch (DBALException $ex) {
            parent::registerLogError($ex);
            die("ERRO ao gerar guia de remessa eletrônica.");
        }  catch (AppException $ex) {
            parent::registerLogError($ex);
            die($ex->getMessage());
        } catch (Exception $ex) {
            parent::registerLogError($ex);
            die("ERRO ao gerar guia de remessa eletrônica. Erro: {$ex->getMessage()}.");
        }
    }

    function index()
    {
        return $this->error404();
    }

    function movimentacao()
    {
        try {
            $args = func_get_args();
            $report = new Report("Relatório de Movimentação de Processos", "L");
            $report->AddPage();
            $report->SetY($report->GetY() + 25);
            //Filtros
            $label_offset = 30;
            $report->mostrarLinha('Período:', $_POST['periodoIni'] . ' até ' . $_POST['periodoFim'], false, false, false, $label_offset);
            $setor = !empty($_POST['setor_id']) ? (new Setor())->buscar($_POST['setor_id']) : null;
            $interessado = !empty($_POST['interessado_id']) ? (new Interessado())->buscar($_POST['interessado_id']) : null;
            $assunto = !empty($_POST['assunto_id']) ? (new Assunto())->buscar($_POST['assunto_id']) : null;
            if (!empty($setor)) {
                $report->mostrarLinha('Setor:', $setor, false, false, false, $label_offset);
            }
            if (!empty($assunto)) {
                $report->mostrarLinha('Assunto:', $assunto, false, false, false, $label_offset);
            }
            if (!empty($interessado)) {
                $report->mostrarLinha('Interessado:', $interessado, false, false, false, $label_offset);
            }
            $w = array(25, 17, 15, 37, 17, 15, 37, 25, 45, 44);
            $a = array('C', 'C', 'C', 'L', 'C', 'C', 'L', 'C', 'L', 'L');
            $report->SetWidths($w);
            $report->SetAligns($a);
            //Movimentações
            $dataIni = Functions::converteDataParaMysql($_POST['periodoIni']);
            $dataFim = Functions::converteDataParaMysql($_POST['periodoFim']);
            $tramites = (new Processo())->listarMovimentacao($dataIni, $dataFim, $setor, $assunto, $interessado);
            $setores = new ArrayCollection();
            foreach ($tramites as $tramite) {
                $setor = $tramite->getSetorAtual();
                if (!$setores->contains($setor)) {
                    $setores->add($setor);
                }
            }
            $report->SetFont('times', '', 8);
            foreach ($setores as $setor) {
                $report->Ln();
                $report->SetFont('times', 'B', 9);
                $report->Cell(0, 5, $setor, 'T', 1);
                $report->Cell($w[0], 5, "", 'T', 0);
                $report->Cell($w[1] + $w[2] + $w[3], 5, "Envio", 'LTR', 0, "C");
                $report->Cell($w[4] + $w[5] + $w[6], 5, "Recebimento", 'LTR', 0, "C");
                $report->Cell(0, 5, "", 'T', 1);
                $headers = array("Processo", "Data", "Hora", "Responsável", "Data", "Hora", "Responsável", "Permanência", "Assunto", "Interessado");
                $qtde_header = count($headers);
                foreach ($headers as $i => $header) {
                    $last = $i == ($qtde_header - 1);
                    $report->Cell($w[$i], 5, $header, $last ? 'TB' : 'RTB', $last ? 1 : 0, $a[$i]);
                }
                $report->SetFont('times', '', 8);
                foreach (Tramite::getTramitesSetor($tramites, $setor) as $tramite) {
                    $processo = $tramite->getProcesso();
                    $report->Row(array(
                        $processo->getNumero(true) . '/' . $processo->getExercicio(),
                        $tramite->getDataEnvio(true),
                        $tramite->getHoraEnvio(),
                        $tramite->getUsuarioEnvio(),
                        $tramite->getIsRecebido() ? $tramite->getDataRecebimento()->format('d/m/Y') : "",
                        $tramite->getIsRecebido() ? $tramite->getDataRecebimento()->format('H:i:s') : "",
                        $tramite->getIsRecebido() ? $tramite->getUsuarioRecebimento() : "",
                        $tramite->getTempoGasto(),
                        $processo->getAssunto(),
                        $processo->getInteressado()
                    ), false);
                }
            }
            $report->Output();
        }  catch (DBALException $ex) {
            parent::registerLogError($ex);
            die("Erro ao gerar relatório de movimentação de processos.");
        }  catch (AppException $ex) {
            parent::registerLogError($ex);
            die($ex->getMessage());
        } catch (Exception $ex) {
            parent::registerLogError($ex);
            die("Erro ao gerar relatório de movimentação de processos. Erro: {$ex->getMessage()}.");
        }
    }
    
    function tramites()
    {
        try {
            $args = func_get_args();
            $report = new Report("Relatório de Movimentação de Processos", "L");
            $report->AddPage();
            $report->SetY($report->GetY() + 25);
            //Filtros
            $label_offset = 30;
            $report->mostrarLinha('Período:', $_POST['periodoIni'] . ' até ' . $_POST['periodoFim'], false, false, false, $label_offset);
            $setor = !empty($_POST['setor_id']) ? (new Setor())->buscar($_POST['setor_id']) : null;
            $usuario = !empty($_POST['usuario_id']) ? (new Usuario())->buscar($_POST['usuario_id']) : null;
            $assunto = !empty($_POST['assunto_id']) ? (new Assunto())->buscar($_POST['assunto_id']) : null;
            if (!empty($setor)) {
                $report->mostrarLinha('Setor:', $setor, false, false, false, $label_offset);
            }
            if (!empty($assunto)) {
                $report->mostrarLinha('Assunto:', $assunto, false, false, false, $label_offset);
            }
            if (!empty($usuario)) {
                $report->mostrarLinha('Usuario:', $usuario, false, false, false, $label_offset);
            }
            $w = array(25, 17, 15, 37, 17, 15, 37, 25, 45, 44);
            $a = array('C', 'C', 'C', 'L', 'C', 'C', 'L', 'C', 'L', 'L');
            $report->SetWidths($w);
            $report->SetAligns($a);
            //Movimentações
            $dataIni = Functions::converteDataParaMysql($_POST['periodoIni']);
            $dataFim = Functions::converteDataParaMysql($_POST['periodoFim']);
            $tramites = (new Processo())->listarTramites($dataIni, $dataFim, $setor, $assunto, $usuario);
            $setores = new ArrayCollection();
            foreach ($tramites as $tramite) {
                $setor = $tramite->getSetorAtual();
                if(!$setor || !$setor->getId()){
                    continue;
                }
                if (!$setores->contains($setor)) {
                    $setores->add($setor);
                }
            }
            $report->SetFont('times', '', 8);
            foreach ($setores as $setor) {
                $report->Ln();
                $report->SetFont('times', 'B', 9);
                $report->Cell(0, 5, $setor, 'T', 1);
                $report->Cell($w[0], 5, "", 'T', 0);
                $report->Cell($w[1] + $w[2] + $w[3], 5, "Envio", 'LTR', 0, "C");
                $report->Cell($w[4] + $w[5] + $w[6], 5, "Recebimento", 'LTR', 0, "C");
                $report->Cell(0, 5, "", 'T', 1);
                $headers = array("Processo", "Data", "Hora", "Responsável", "Data", "Hora", "Responsável", "Permanência", "Assunto", "Interessado");
                $qtde_header = count($headers);
                foreach ($headers as $i => $header) {
                    $last = $i == ($qtde_header - 1);
                    $report->Cell($w[$i], 5, $header, $last ? 'TB' : 'RTB', $last ? 1 : 0, $a[$i]);
                }
                $report->SetFont('times', '', 8);
                // echo "<pre>";
                // \Doctrine\Common\Util\Debug::dump($setor);
                // echo "</pre>";
                foreach (Tramite::getTramitesSetor($tramites, $setor) as $tramite) {
                    $processo = $tramite->getProcesso();
                    $report->Row(array(
                        $processo->getNumero(true) . '/' . $processo->getExercicio(),
                        $tramite->getDataEnvio(true),
                        $tramite->getHoraEnvio(),
                        $tramite->getUsuarioEnvio(),
                        $tramite->getIsRecebido() ? $tramite->getDataRecebimento()->format('d/m/Y') : "",
                        $tramite->getIsRecebido() ? $tramite->getDataRecebimento()->format('H:i:s') : "",
                        $tramite->getIsRecebido() ? $tramite->getUsuarioRecebimento() : "",
                        $tramite->getTempoGasto(),
                        $processo->getAssunto(),
                        $processo->getInteressado()
                    ), false);
                }
            }
            $report->Output();
        }  catch (DBALException $ex) {
            parent::registerLogError($ex);
            die("Erro ao gerar relatório de movimentação de processos.");
        }  catch (AppException $ex) {
            parent::registerLogError($ex);
            die($ex->getMessage());
        } catch (Exception $ex) {
            parent::registerLogError($ex);
            die("Erro ao gerar relatório de movimentação de processos. Erro: {$ex->getMessage()}.");
        }
    }
    
    function podeEditarSetorOrigem(): bool
    {
        $usuarioLogado = UsuarioController::getUsuarioLogado();
        if(!empty($usuarioLogado)){
            if($usuarioLogado->getTipo() == TipoUsuario::MASTER || $usuarioLogado->getTipo() == TipoUsuario::ADMINISTRADOR){
                return true;
            }
        }
        return false;
    }

    function editar()
    {
        try {
            $usuarioLogado = UsuarioController::getUsuarioLogadoDoctrine();
            if($usuarioLogado === null){
                $this->route('Login');
            }
            $usuarioEhInteressado = UsuarioController::isInteressado();

            $_REQUEST['breadcrumb'] = array(
                array('link' => $usuarioEhInteressado ? 'Contribuinte' : $this->class_path, 'title' => $this->getBreadCrumbTitle()),
                array('link' => null, 'title' => 'Editar')
            );
            $_REQUEST['podeEditarSetorOrigem'] = $this->podeEditarSetorOrigem();
            $permissao = $this->getPermissao();
            $prosseguir = !$permissao instanceof PermissaoEntidade || $permissao->getEditar();
            if ($prosseguir) {
                $processo = new Processo();
                if (isset($_GET["processo"])) {
                    $args = explode("/", $_GET["processo"]);
                    $processo = $processo->buscarPorCampos(["numero" => $args[0], "exercicio" => $args[1]]);
                } else {
                    $args = func_get_args();
                    if (empty($args[1])) return $this->error404();
                    $processo = $processo->buscar($args[1]);
                }
                if ($processo != null) {
                    if($usuarioEhInteressado && $processo->getInteressado()->getId() !== $usuarioLogado->getPessoa()->getInteressados()[0]->getId()){
                        return $this->error404();
                    }
                    $_REQUEST['objeto'] = $processo;
                    if(!$processo->usuarioTemPermissaoProcesso()){
                        return $this->load('public', 'sem_acesso_sigiloso');
                    }
                    $autenticado = isset($_SESSION['processo_' . $processo->getId()]);
                    if ($processo->getSigilo() == SigiloProcesso::SIGILOSO && !$autenticado) {
                        return $this->load('processo', 'autenticar');
                    }
                    HistoricoProcesso::registrar(TipoHistoricoProcesso::VISUALIZADO, $processo);
                    return $this->load($this->class_path, 'editar');
                } else {
                    return $this->error404();
                }
            }
            $this->error403();
        }  catch (DBALException $e) {
            parent::registerLogError($e);
            die("Erro ao editar registro. ");
        }  catch (AppException $e) {
            parent::registerLogError($e);
            die($e->getMessage());
        } catch (Exception $e) {
            parent::registerLogError($e);
            die("Erro ao editar registro. Erro: {$e->getMessage()}");
        }
        return true;
    }

    function pesquisar()
    {
        $_REQUEST['breadcrumb'] = array(array('link' => null, 'title' => $this->getBreadCrumbTitle()), array('link' => null, 'title' => 'Pesquisar'));
        $this->load($this->class_path, 'pesquisar');
    }

    function finalizado()
    {
        $args = func_get_args();
        $_REQUEST['breadcrumb'] = array(array('link' => null, 'title' => $this->getBreadCrumbTitle()), array('link' => null, 'title' => 'Finalizado'));
        $_REQUEST['processo_id'] = $args[1];
        $this->load($this->class_path, 'finalizado');
    }

    function abertos()
    {
        $_REQUEST['breadcrumb'] = array(array('link' => null, 'title' => $this->getBreadCrumbTitle()), array('link' => null, 'title' => 'Em Aberto'));
        $this->load($this->class_path, 'abertos');
    }

    function receber()
    {
        $_REQUEST['breadcrumb'] = array(array('link' => null, 'title' => $this->getBreadCrumbTitle()), array('link' => null, 'title' => 'A Receber'));
        $this->load($this->class_path, 'receber');
    }

    function arquivados()
    {
        $_REQUEST['breadcrumb'] = array(array('link' => null, 'title' => $this->getBreadCrumbTitle()), array('link' => null, 'title' => 'Arquivados'));
        $this->load($this->class_path, 'arquivados');
    }

    function enviados()
    {
        $_REQUEST['breadcrumb'] = array(array('link' => null, 'title' => $this->getBreadCrumbTitle()), array('link' => null, 'title' => 'Enviados'));
        $this->load($this->class_path, 'enviados');
    }

    function contribuintes()
    {
        $_REQUEST['breadcrumb'] = array(array('link' => null, 'title' => $this->getBreadCrumbTitle()), array('link' => null, 'title' => 'A Receber (Contribuintes)'));
        $this->load($this->class_path, 'contribuintes');
    }

    function setarInformacoes()
    {
        try {
            $processo = !empty($_POST['id']) ? (new Processo())->buscar($_POST['id']) : unserialize($_SESSION['processo']);
            $this->setProcesso();
            $this->getValues($processo);
            $_SESSION['processo'] = serialize($processo);
            self::setMessage(TipoMensagem::SUCCESS, "Dados de processo setados.", null, true);
        }  catch (DBALException $ex) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao processar dados. \{{basename($ex->getFile())}:{$ex->getLine()}\}.", null, true);
            parent::registerLogError($ex);
        }  catch (AppException $ex) {
            self::setMessage(TipoMensagem::ERROR, $ex->getMessage(), null, true);
        } catch (Exception $ex) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao processar dados. Erro: {$ex->getMessage()}.", null, true);
            parent::registerLogError($ex);
        }
    }

    /**
     * Realiza OCR de todos os anexos pendentes de um processo
     */
    function realizarOCR()
    {
        ignore_user_abort(true);
        set_time_limit(0);
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        $processo_id = func_get_args()[1];
        $processo = (new Processo())->buscar($processo_id);
        try {
            $anexos = $processo->getAnexos(true);
            $qtde_anexos = count($anexos);
            $a = 1;
            foreach ($anexos as $anexo) {
                $porcentagem_anexo = Functions::getPorcentagem($a, $qtde_anexos);
                Functions::send_message($a, 'Realizando OCR para anexo ' . $anexo->getDescricao() . " ($a de $qtde_anexos)", 0);
                $anexo->realizarOCR();
                Functions::send_message($a, "OCR realizado para anexo: " . $anexo->getDescricao() . "(" . $a . ' de ' . $qtde_anexos . ")", $porcentagem_anexo);
                //sleep(1);
                Functions::escreverLogEvento("OCR realizado para anexo: " . $anexo->getDescricao() . "(" . $a . ' de ' . $qtde_anexos . ")");
                $a++;
            }
            Functions::send_message('CLOSE', "OCR do processo $processo realizada com sucesso.", 100);
        }  catch (DBALException $ex) {
            echo "Erro ao realizar OCR para o processo $processo. Erro {$ex->getCode()}.";
            parent::registerLogError($ex);
        }  catch (AppException $ex) {
            echo $ex->getMessage();
        } catch (Exception $ex) {
            echo "Erro ao realizar OCR para o processo $processo. Erro: {$ex->getMessage()}.";
            parent::registerLogError($ex);
        }
    }

    /**
     * Realiza a criação de um arquivo pdf para os arquivos digitalizados para
     * cada anexo de um processo
     */
    function gerarPdf()
    {
        ignore_user_abort(true);
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        $processo = (new Processo())->buscar($_POST['processo_id']);
        try {
            foreach ($processo->getAnexos() as $anexo) {
                if ($anexo->getArquivo() == null) {
                    $anexo->setArquivo($this->montarPdf($anexo->getImagens(), $processo));
                    $anexo->atualizar();
                }
            }
            echo "Arquivos PDF's gerados com sucessos para o processo $processo.";
        }  catch (DBALException $ex) {
            echo "Erro ao gerar PDF para o processo $processo.";
            parent::registerLogError($ex);
        }  catch (AppException $ex) {
            echo $ex->getMessage();
        } catch (Exception $ex) {
            echo "Erro ao gerar PDF para o processo $processo. Erro: {$ex->getMessage()}.";
            parent::registerLogError($ex);
        }
    }

    /**
     * Realiza a criação de um arquivo pdf a partir de uma lista de imagens jpg
     */
    private function montarPdf($imagens, Processo $processo): string
    {
        require_once APP_PATH . 'lib/fpdf/fpdf.php';
        $pdf = new FPDF();
//        $pdf->AliasNbPages();
        $temp_dir = Processo::getTempPath();
        foreach ($imagens as $imagem) {
            $img_file = $temp_dir . $imagem->getArquivo();
            list($width, $height) = getimagesize($img_file);
            $image_p = imagecreatetruecolor($width, $height);
            $image = Functions::imagecreatefrombmp($img_file);
            if ($image) {
                imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width, $height);
                imagejpeg($image_p, $img_file);
                imagedestroy($image_p);
            }
            $pdf->Image($img_file, null, null, 200, 300);
        }
        $filename = date("YmdHisu") . uniqid() . date("usiHdmY") . ".pdf";
        $pdf->Output('F', $processo->getAnexosPath() . $filename);
        return $filename;
    }

    function desarquivar()
    {
        if (!$this->loginValido()) {
            $this->error401();
            return;
        }
        $usuario = $this->getUsuario();
        $grupo = $usuario->getGrupo();
        if (!$usuario->isAdm() && (is_null($grupo) || !$grupo->getArquivar())) {
            $this->error403();
            return;
        }
        $processo_id = func_get_args()[1];
        try {
            $processo = (new Processo())->buscar($processo_id);
            if ($processo != null) {
                $processo->setIsArquivado(false);
                $processo->setDataArquivamento(null);
                $processo->setJustificativaEncerramento(null);
                $tramiteAtual = $processo->getTramiteAtual();
                $tramiteAtual->setStatus((new StatusProcesso())->buscar(2));
                $processo->atualizar();
                self::setMessage(TipoMensagem::SUCCESS, "Processo desarquivado com sucesso!", null, false);
            } else {
                throw new TechnicalException("Processo não encontrado");
            }
        }  catch (DBALException $ex) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao desarquivar processo.", null, false);
            parent::registerLogError($ex);
        }  catch (AppException $ex) {
            self::setMessage(TipoMensagem::ERROR, $ex->getMessage(), null, false);
        } catch (Exception $ex) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao desarquivar processo. Erro: {$ex->getMessage()}", null, false);
            parent::registerLogError($ex);
        }
        $this->route($this->class_path, 'editar/id/' . $processo_id);
    }

    /**
     * Arquiva processo(s)
     * @throws SmartyException
     */
    function arquivar()
    {
        if (!$this->loginValido()) {
            $this->error401();
            return;
        }
        $usuario = $this->getUsuario();
        $grupo = $usuario->getGrupo();
        if (!$usuario->isAdm() && (is_null($grupo) || !$grupo->getArquivar())) {
            $this->error403();
            return;
        }
        try {
            if (isset($_POST['processo_id'])) {
                if (is_array($_POST['processo_id'])) {
                    foreach ($_POST['processo_id'] as $processo_id) {
                        $this->marcarArquivado($processo_id, $_POST['justificativa']);
                    }
                    self::setMessage(TipoMensagem::SUCCESS, "Processo(s) arquivado(s) com sucesso.", null, true);
                } else {
                    $processo_id = $_POST['processo_id'];
                    $this->marcarArquivado($processo_id, $_POST['justificativa']);
                    self::setMessage(TipoMensagem::SUCCESS, "Processo arquivado com sucesso.", null, true);
                }
            } else {
                throw new BusinessException("Selecione ao menos um processo para arquivar.");
            }
        }  catch (DBALException $ex) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao arquivar processo.", null, true);
            parent::registerLogError($ex);
        }  catch (AppException $ex) {
            self::setMessage(TipoMensagem::ERROR, $ex->getMessage(), null, true);
        } catch (Exception $ex) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao arquivar processo. Erro: {$ex->getMessage()}", null, true);
            parent::registerLogError($ex);
        }
    }

    /**
     * Função para marcar processo como arquivado
     * @param int $processo_id
     * @param $justificativa
     * @throws BusinessException
     * @throws Exception
     */
    function marcarArquivado(int $processo_id, $justificativa)
    {
        $processo = (new Processo())->buscar($processo_id);
        $this->apensar($processo);
        $processo->setDataArquivamento(new DateTime());
        $processo->setJustificativaEncerramento($justificativa);
        $processo->setIsArquivado(true);
        $this->setLocalizacaoFisica($processo);
        $tramiteAtual = $processo->getTramiteAtual();
        if (is_array($tramiteAtual) || $tramiteAtual instanceof Collection) {
            foreach ($processo->getTramiteAtual() as $tramite) {
                $tramite->setStatus((new StatusProcesso())->buscar(StatusProcesso::ARQUIVADO));
            }
        } else if ($tramiteAtual) {
            $tramiteAtual->setStatus((new StatusProcesso())->buscar(StatusProcesso::ARQUIVADO));
        }
        $processo->atualizar();
        HistoricoProcesso::registrar(TipoHistoricoProcesso::ARQUIVADO, $processo);
    }

    function inserirApenso()
    {
        try {
            $_POST['dataCadastro'] = new DateTime();
            $_POST['ultimaAlteracao'] = null;
            $_POST['isArquivado'] = false;
            $_POST['usuarioAbertura'] = UsuarioController::getUsuarioLogadoDoctrine();
            $processoPai = !empty($_POST['processo_pai_id']) ? (new Processo())->buscar($_POST['processo_pai_id']) : unserialize($_SESSION['processo']);
            $apenso = new Processo();
            $apenso->setNumeroFase(1);
            $apenso->setApensado($processoPai);
            $this->setProcesso();
            $this->getValues($apenso);
            $processoPai->adicionaApenso($apenso);
            if ($processoPai->getId() != null) {
                $processoPai->atualizar();
            } else {
                $_SESSION['processo'] = serialize($processoPai);
            }
            self::setMessage(TipoMensagem::SUCCESS, 'Processo apenso adicionado com sucesso!', null, true);
        }  catch (DBALException $ex) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao inserir processo apenso.", null, true);
            parent::registerLogError($ex);
        }  catch (AppException $ex) {
            self::setMessage(TipoMensagem::ERROR, $ex->getMessage(), null, true);
        } catch (Exception $ex) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao inserir processo apenso. {$ex->getMessage()}", null, true);
            parent::registerLogError($ex);
        }
    }

    /**
     * Insere um novo processo no banco de dados
     */
    function inserir()
    {
        /**
         * @var Processo $processo
         */
        try {
            $_POST['usuarioAbertura'] = $usuario = UsuarioController::getUsuarioLogadoDoctrine();
            $_POST['dataCadastro'] = new DateTime();
            $_POST['ultimaAlteracao'] = null;
            $_POST['isArquivado'] = false;
            $processo = unserialize($_SESSION['processo']);
            $this->setProcesso();
            $this->getValues($processo);
            $this->setApensos($processo);
            $this->setCI($processo);
            $fluxograma = $processo->getAssunto()->getFluxograma();
            if (!is_null($fluxograma)) {
                $fases = $fluxograma->getFases();
            }
            $numero_fase = 1;
            while(isset($fases) && $numero_fase < count($fases) && !$fases->get($numero_fase - 1)->getAtivo()){
                $numero_fase++;
            }
            $this->adicionarTramites($processo, $processo->getAssunto(), $numero_fase, $processo->getSetorOrigem(), false);
            foreach ($processo->getAnexos() as $anexo) {
                $anexo->setDataCadastro(new DateTime());
                if ($anexo->getTipo() != null && $anexo->getTipo()->getId() != null) {
                    $anexo->setTipo((new TipoAnexo())->buscar($anexo->getTipo()->getId()));
                }
                if ($anexo->getClassificacao()->getId() != null) {
                    $anexo->setClassificacao((new Classificacao())->buscar($anexo->getClassificacao()->getId()));
                }
                $anexo->setUsuario($usuario);
            }
            foreach ($processo->getApensos() as $apenso) {
                $apenso->setSetorOrigem((new Setor())->buscar($apenso->getSetorOrigem()->getId()));
                $apenso->setAssunto((new Assunto())->buscar($apenso->getAssunto()->getId()));
                $apenso->setInteressado((new Interessado())->buscar($apenso->getInteressado()->getId()));
                $apenso->setUsuarioAbertura($usuario);
            }
            foreach ($processo->getDocumentos() as $documento) {
                $documento->setCategoria((new CategoriaDocumento())->buscar($documento->getCategoria()->getId()));
            }
            $this->verificarArquivamentoAutomatico($processo);
            $processo_id = $processo->inserir();
            $processo = $processo->buscar($processo_id);
            if(isset($_POST['vincula']) && $_POST['vincula'] = 1){
                $processoPrincipal = new Processo();
                $processoPrincipal = $processoPrincipal->buscar($_POST['campo_' . $_POST['campo_id']]);
                $processo->setApensado($processoPrincipal);
                $processo->atualizar();
            }
            $processo->getComponentes();
            HistoricoProcesso::registrar(TipoHistoricoProcesso::CRIADO, $processo);
            foreach ($processo->getTramites() as $tramite) {
                HistoricoProcesso::registrar(TipoHistoricoProcesso::ENVIADO, $processo, $tramite);
            }
            foreach ($processo->getAnexos() as $anexo) {
				$usuario = UsuarioController::getUsuarioLogadoDoctrine();
                HistoricoAnexo::registrar(TipoHistoricoAnexo::INSERT, "Anexo registrado.",  null, $anexo, $anexo, $usuario);
            }
            Log::registrarLog(TipoLog::ACTION_INSERT, $processo->getTableName(), "Registro criado", null, null, $processo->imprimir());
            unset($_SESSION['processo']);
            if($processo->getIsExterno()){
                $emailEnviado = (new Email())->notificarCriacaoProcesso($processo);
                HistoricoProcesso::registrar(($emailEnviado) ? TipoHistoricoProcesso::EMAIL_ENVIADO : TipoHistoricoProcesso::EMAIL_ERRO, $processo);
            }
            self::setMessage(TipoMensagem::SUCCESS, 'Processo cadastrado com sucesso!', $processo_id, true);
        } catch (UniqueConstraintViolationException $ex) {
            parent::registerLogError($ex);
            self::setMessage(TipoMensagem::ERROR, "Processo já cadastrado.", null, true);
        } catch (\Doctrine\DBAL\Exception $ex) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao cadastrar processo.", null, true);
            parent::registerLogError($ex);
        } catch (AppException $ex) {
            self::setMessage(TipoMensagem::ERROR, $ex->getMessage(), null, true);
        } catch (Exception $ex) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao cadastrar processo. Erro: {$ex->getMessage()}.", null, true);
            parent::registerLogError($ex);
        }
    }

    /**
     * @throws Exception
     */
    private function setCI(Processo $processo)
    {
        if (isset($_FILES['arquivo_comunicacao_interna']) && !empty($_FILES['arquivo_comunicacao_interna']['name'])) {
            $arquivoCI = (new Upload('arquivo_comunicacao_interna', $processo->getAnexosPath(), array('pdf', 'doc', 'docx')))->upload();
            $anexo = new Anexo();
            $anexo->setDataCadastro(new DateTime());
            $anexo->setData(new DateTime(Functions::converteDataParaMysql($_POST['data_comunicacao_interna'])));
            if (!empty($_POST['numero_comunicacao_interna']) && !empty($_POST['ano_comunicacao_interna'])) {
                $anexo->setNumero($_POST['numero_comunicacao_interna'] . "/" . $_POST['ano_comunicacao_interna']);
            }
            $anexo->setTipo((new TipoAnexo())->buscar(29));
            $anexo->setDescricao("Comunicação Interna");
            $anexo->setProcesso($processo);
            $anexo->setArquivo($arquivoCI);
            $anexo->setIsDigitalizado(false);
            $anexo->setUsuario(UsuarioController::getUsuarioLogadoDoctrine());
            $processo->adicionaAnexo($anexo);
        }
    }


    function gerarArquivosParaVisualizacaoDigital(){
        $processo_id = $_POST['processo_id'] ?? $_GET['processo_id'];
        $processo = new Processo();
        $processo = $processo->buscar($processo_id);
        $qtdePaginasPdf = 1;
        foreach($processo->getComponentes() as $componente){
            $anexo = $componente->getAnexo();
            $tramiteAux = $componente->getTramite();
            if($componente->getAnexo() && file_exists($anexo->getPath() . $anexo->getArquivo(true))){
                $anexo->getArquivoParaCarimbar();
                $assinatura = Assinatura::buscarPorAnexo($anexo);
                if($assinatura){
                    $PDF_CONTENTS = file_get_contents($anexo->getArquivoUrl());
                    file_put_contents($anexo->getArquivoParaCarimbar(true), $PDF_CONTENTS);
                    Functions::adicionarPaginacaoECarimbo(
                        $anexo->getArquivoParaCarimbar(true),
                        IndexController::getClienteConfig(),
                        $qtdePaginasPdf,
                        true
                    );
                }else{
                    if(
                        $anexo->getArquivoParaCarimbar() && 
                        file_exists($anexo->getArquivoParaCarimbar())
                    ){
                        Functions::adicionarPaginacaoECarimbo(
                            $anexo->getArquivoParaCarimbar(),
                            IndexController::getClienteConfig(),
                            $qtdePaginasPdf
                        );
                    }else {
                        new BusinessException("Arquivo não encontrado para carimbar. Anexo id ". $anexo->getId() );
                    }                    
                }
                if(file_exists($anexo->getArquivo(false, true, true))){
                    $qtdePaginasPdf += Functions::getQntdePaginasPDF($anexo->getArquivo(false, true, true));
                }else{
                    new BusinessException("Arquivo não encontrado para carimbar. Anexo id ". $anexo->getId() );
                }
            }elseif($tramiteAux){
                $tramiteAux->gerarFormularioEletronico();
                $arquivoComCaminhoCompleto = $processo->getAnexosPath() . $tramiteAux->getNomeFormularioEletronico();
                Functions::adicionarPaginacaoECarimbo($arquivoComCaminhoCompleto, IndexController::getClienteConfig(), $qtdePaginasPdf);
                $qtdePaginasPdf += Functions::getQntdePaginasPDF($arquivoComCaminhoCompleto);
            }
        }
    }

    function visualizarDigital(){
        $_POST['carregadoByController'] = true;
        $args = func_get_args();
        if (empty($args[0])) {
            $this->error404();
            return;
        }
        $_POST['processo_id'] = $args[0];
        $this->loadSemTemplate("Processo", "visualizar_digital");
    }

    /**
     * Gera o Processo
     */
    function gerarProcesso()
    {
        try{
            if(isset($_POST['id']) && !empty($_POST['id'])){
                $processo = (new Processo())->buscar($_POST['id']);
                if($processo->getNumero() == null && $processo->getIsExterno()){
                    /*
                     * o número já é gerado automaticamente pelo Pré Persist (gerarNumero()),
                     * porém quando é uma solicitação de criação pelo contribuinte, o número não é gerado
                     * somente após ser consolidada a criação do processo
                    */
                    $processo->gerarNumero(false);
                    $processo->atualizar();
                    if($processo->getNumero() !== null){
                        HistoricoProcesso::registrar(TipoHistoricoProcesso::ATUALIZADO, $processo, null, null, UsuarioController::getUsuarioLogadoDoctrine());
                        self::setMessage(TipoMensagem::SUCCESS, 'Processo gerado com sucesso!', $processo->getId(), true);
                    }else{
                        self::setMessage(TipoMensagem::WARNING, 'Erro ao gerar processo, numeração não foi gerada!', $processo->getId(), true);
                    }
                }else{
                    self::setMessage(TipoMensagem::WARNING, 'Processo já foi gerado anteriormente!', $processo->getId(), true);
                }
            }else{
                self::setMessage(TipoMensagem::ERROR, 'Solicitação de abertura não foi encontrada!', null, true);
            }
        }catch (Exception $ex) {
            parent::registerLogError($ex);
            self::setMessage(TipoMensagem::ERROR, "Falha ao gerar processo. Erro: {$ex->getMessage()}.", null, true);
        }
    }

    /**
     * Atualiza um registro de processo no banco de dados
     */
    function atualizar()
    {
        $processo_id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
        try {
            $_POST['ultimaAlteracao'] = new DateTime();
            $processo = new Processo();
            $processo = $processo->buscar($processo_id);
            $old = clone $processo;
            $this->setProcesso();
            $this->setLocalizacaoFisica($processo);
            $this->setApensos($processo);
            $this->getValues($processo);
            if (!isset($_POST['usuariosPermitidos'])) {
                $processo->setUsuariosPermitidos(new ArrayCollection());
            }
            $new = $processo;
            $processo->atualizar();
            $comparar = $this->compararObjetos($old, $new);
            if ($comparar['alterou']) {
                //não está salvando novos anexos na atualização de processos
                HistoricoProcesso::registrar(TipoHistoricoProcesso::ATUALIZADO, $processo);
                Log::registrarLog(TipoLog::ACTION_UPDATE, $processo->getTableName(), "Registro atualizado", null, $old->imprimir(), $new->imprimir());
            }
            self::setMessage(TipoMensagem::SUCCESS, 'Processo atualizado com sucesso!', $processo_id, true);
        }  catch (DBALException $ex) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao atualizar processo.", null, true);
            parent::registerLogError($ex);
        }  catch (AppException $ex) {
            self::setMessage(TipoMensagem::ERROR, $ex->getMessage(), null, true);
        } catch (Exception $ex) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao atualizar processo. Erro: {$ex->getMessage()}.", null, true);
            parent::registerLogError($ex);
        }
    }

    public function excluir()
    {
        $isAjax = isset($_REQUEST['ajax']);
        try {
            $permissao = $this->getPermissao();
            $prosseguir = !$permissao instanceof PermissaoEntidade || $permissao->getExcluir();
            if (!$prosseguir) {
                throw new SecurityException("Você não têm permissão para realizar essa ação.");
            }
            $processo = new Processo();
            $processo_id = func_get_args()[1];
            $processo = $processo->buscar($processo_id);
            foreach ($processo->getAnexos() as $anexo) {
                $file = $anexo->getPath() . $anexo->getArquivo();
                if (is_file($file)) {
                    unlink($file);
                }
            }
            $processo->remover($processo_id);
            Log::registrarLog(TipoLog::ACTION_DELETE, $processo->getTableName(), "Registro deletado", null, $processo->imprimir());
            self::setMessage(TipoMensagem::SUCCESS, 'Processo removido com sucesso!', null, $isAjax);
        } catch (ForeignKeyConstraintViolationException $e) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao remover processo. Mensagem: este processo está relacionado a outro registro no sistema. Você não pode excluí-lo.", null, $isAjax);
        }  catch (DBALException $e) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao remover processo. ", null, $isAjax);
            parent::registerLogError($e);
        }  catch (AppException $e) {
            self::setMessage(TipoMensagem::ERROR, $e->getMessage(), null, $isAjax);
        } catch (Exception $e) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao remover processo. Erro: {$e->getMessage()}.", null, $isAjax);
            parent::registerLogError($e);
        }
        if (!$isAjax) {
            $this->route($this->class_path);
        }
    }

    /**
     * Seta informações do processo para posterior atribuição do framework
     * @throws Exception
     */
    private function setProcesso()
    {
        if ((UsuarioController::getUsuarioLogadoDoctrine())->getTipo() == TipoUsuario::INTERESSADO) {
            if ($_POST['acao'] == 'inserir') {
                /**
                 * @var Processo $processo
                 */
                $processo = unserialize($_SESSION['processo']);
                $interessados = UsuarioController::getUsuarioLogadoDoctrine()->getPessoa()->getInteressados();
                $_POST['setorOrigem'] = (new Setor())->buscar($processo->getSetorOrigem()->getId());
                $_POST['setor_origem_id'] = $_POST['setorOrigem']->getId();
                $_POST['interessado'] = (UsuarioController::getUsuarioLogadoDoctrine())->getPessoa()->getInteressados()[0];
                $_POST['interessado_id'] = $_POST['interessado']->getId();
                $_POST['assunto'] = (new Assunto())->buscar($_POST['assunto_id']);
                $_POST['dataAbertura'] = $processo->getDataAbertura();
                $_POST['isExterno'] = $processo->getIsExterno();
                $_POST['dataVencimento'] = new DateTime(Functions::converteDataParaMysql($_POST['assunto']->getVencimento(true)));
            }
        } else {
            if (isset($_POST['assunto_id'])) {
                $_POST['assunto'] = (new Assunto())->buscar($_POST['assunto_id']);
            }
            if (isset($_POST['setor_origem_id'])) {
                $_POST['setorOrigem'] = (new Setor())->buscar($_POST['setor_origem_id']);
            }
            if (isset($_POST['interessado_id'])) {
                $_POST['interessado'] = (new Interessado())->buscar($_POST['interessado_id']);
            }
            if (isset($_POST['dataAbertura']) && isset($_POST['dataVencimento'])) {
                $_POST['dataAbertura'] = new DateTime(Functions::converteDataParaMysql($_POST['dataAbertura']) . " " . Date("H:i:s"));
                $_POST['dataVencimento'] = new DateTime(Functions::converteDataParaMysql($_POST['dataVencimento']));
            }
            if (isset($_POST['usuariosPermitidos'])) {
                foreach ($_POST['usuariosPermitidos'] as $value) {
                    $usuarios[] = (new Usuario())->buscar($value);
                }
                if (isset($usuarios)) {
                    $_POST['usuariosPermitidos'] = $usuarios;
                }
            }
        }
    }

    private function setLocalizacaoFisica(Processo $processo)
    {
        $localizacaoFisica = !empty($_POST['localizacao_fisica_id']) ? (new LocalizacaoFisica())->buscar($_POST['localizacao_fisica_id']) : new LocalizacaoFisica();
        $localizacaoFisica->setLocal((new Local())->buscar($_POST['local_id']));
        $localizacaoFisica->setRefLocal($_POST['referencia_local']);
        $localizacaoFisica->setTipoLocal((new TipoLocal())->buscar($_POST['tipolocal_id']));
        $localizacaoFisica->setRefTipoLocal($_POST['referencia_tipo_local']);
        $localizacaoFisica->setSubTipoLocal((new SubTipoLocal())->buscar($_POST['subtipo_local_id']));
        $localizacaoFisica->setRefSubTipoLocal($_POST['referencia_subtipo_local']);
        $localizacaoFisica->setObservacao($_POST['observacoes_local_fisico']);
        $localizacaoFisica->setNumeroDocumento($processo->getNumero());
        $localizacaoFisica->setExercicioDocumento($processo->getExercicio());
        $localizacaoFisica->setDataDocumento($processo->getDataAbertura());
        $usuario = UsuarioController::getUsuarioLogadoDoctrine();
        if (empty($_POST['localizacao_fisica_id'])) {
            $localizacaoFisica->setDataCadastro(new DateTime());
            $localizacaoFisica->setUsuario($usuario);
        } else {
            $localizacaoFisica->setUsuarioAlteracao(new DateTime());
            $localizacaoFisica->setUsuarioAlteracao($usuario);
        }
        $processo->setLocalizacaoFisica($localizacaoFisica);
    }

    private function setApensos(Processo $processo)
    {
        $apensos = $processo->getApensos();
        if(!empty($apensos)){
            foreach($apensos as $apenso){
                if(!isset($_POST['apensos_id']) || !in_array($apenso->getId(), $_POST['apensos_id'])){
                    $apenso->setApensado(null);
                    $processo->removerApenso($apenso);
                }
            }
        }
        if (isset($_POST['apensos_id'])) {
            foreach ($_POST['apensos_id'] as $apenso_id) {
                $apenso = new Processo();
                $apenso = $apenso->buscar($apenso_id);
                $apenso->setApensado($processo);
                $processo->adicionaApenso($apenso);
            }
        }
    }

    /**
     * Função que executa o trâmite do processo se for pertinente naquele momento
     * Condição: todos os trâmites da fase atual tem que estar despachados
     */
    function tramitar()
    {
        if (!isset($_POST['tramite_id']) && $this->ehTramitacaoRedundante()) {
            http_response_code(205);
            return;
        }
        try {
            if( isset($_POST['tramite_id']) &&  (empty($_POST['cancelar']))){
                $tramite = new Tramite();
                $tramitesDocumentoRequerido = array();
                if(is_array($_POST['tramite_id'])){
                    foreach($_POST['tramite_id'] as $tramite_id){
                        $tramitesDocumentoRequerido[] = $tramite->buscar($tramite_id);
                    }
                }else{
                    $tramitesDocumentoRequerido[] = $tramite->buscar($_POST['tramite_id']);

                }
                foreach($tramitesDocumentoRequerido as $tramite){
                    if(!empty($tramite->getRequirimentosObrigaroriosNaoCumpridos())){
                        throw new BusinessException( "Adicione os documentos obrigatórios para o tramite, eles estão localizados no top da página de tramite.  ");
                    }
                }
            }
            //Se chegou no fim do fluxo, segue para arquivamento
            if ($_POST['arquivar'] == 1) {
                $this->arquivar();
                return;
            }
            if (!empty($_POST['setor_destino_id'])) {
                //Se for cancelamento
                if (isset($_POST['cancelar']) && $_POST['cancelar'] == 1) {
                    (new TramiteController())->cancelar($_POST['tramite_id']);
                }
                if (isset($_POST['tramite_id'])) {
                    $remessa = isset($_POST['gerar_guia_tramitacao']) ? new Remessa() : null;
                    //Tramitação em Massa
                    if (is_array($_POST['tramite_id'])) {
                        foreach ($_POST['tramite_id'] as $tramite_id) {
                            $this->tramitarProcesso($tramite_id, $remessa);
                        }
                        self::setMessage(TipoMensagem::SUCCESS, "Trâmite em massa realizado com sucesso!", $remessa != null ? $remessa->getId() : null, true);
                    } else {
                        //Trâmite Comum
                        $prosseguir_fase = $this->tramitarProcesso($_POST['tramite_id'], $remessa);
                        if ($prosseguir_fase) {
                            self::setMessage(TipoMensagem::SUCCESS, "Trâmite realizado com sucesso!", $remessa != null ? $remessa->getId() : null, true);
                        } else {
                            self::setMessage(TipoMensagem::SUCCESS, "Processo despachado com sucesso! Aguardando despacho dos outros setores para finalizar trâmite.", null, true);
                        }
                    }
                } else {
                    throw new BusinessException("Selecione ao menos um processo para tramitar.");
                }
            } else {
                throw new BusinessException("Selecione ao menos um setor de destino para continuar.");
            }
        }  catch (DBALException $ex) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao realizar operação.", null, true);
            parent::registerLogError($ex);
        }  catch (AppException $ex) {
            self::setMessage(TipoMensagem::ERROR, $ex->getMessage(), null, true);
        } catch (Exception $ex) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao realizar operação. Erro: {$ex->getMessage()}.", null, true);
            parent::registerLogError($ex);
        }
    }

    function devolver()
    {
        try {
            $tramiteAtual = (new Tramite())->buscar($_POST['tramite_id']);
            $processo = $tramiteAtual->getProcesso();
            // Se proceso teve tomada de decisão e está na primeira etapa do trâmite após tomar a decisão
            if ($processo->getAssuntos()->count() > 0 && $processo->getNumeroFase() == 1 && $processo->getTramiteAtual() instanceof Tramite) {
                $tramiteAnterior = $processo->getTramiteAnterior();
                // cancelar tomada de decisão e voltar ao setor anterior
                $assuntoProcesso = (new AssuntoProcesso())->buscarPorAssunto($tramiteAnterior->getAssunto()->getId(), $processo->getId());
                //remover o assunto
                if($assuntoProcesso){
                    $processo->removerAssunto($assuntoProcesso);
                }
                // setar processo ao assunto anterior
                $processo->setAssunto($tramiteAnterior->getAssunto());
                $processo->atualizar();
                // setar número fase para igual do trâmite anterior
                $processo->setNumeroFase($tramiteAnterior->getNumeroFase());
                $_POST['cancelouDecisao'] = true;
            }
            $remessa = isset($_POST['gerar_guia_tramitacao']) ? new Remessa() : null;
            $this->tramitarProcesso($tramiteAtual, $remessa);
            self::setMessage(TipoMensagem::SUCCESS, "Protocolo devolvido com sucesso!", $remessa != null ? $remessa->getId() : null, true);
        }  catch (DBALException $ex) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao devolver protolo.", null, true);
            parent::registerLogError($ex);
        }  catch (AppException $ex) {
            self::setMessage(TipoMensagem::ERROR, $ex->getMessage(), null, true);
        } catch (Exception $ex) {
            self::setMessage(TipoMensagem::ERROR, "Erro ao devolver protolo. Erro: {$ex->getMessage()}", null, true);
            parent::registerLogError($ex);
        }
    }

    /**
     * @throws BusinessException
     */
    private function apensar(Processo $processo){
        if(isset($_POST['processo_apensado']) && !empty($_POST['processo_apensado'])){
            $processoPrincipal = new Processo();
            $processoPrincipal = $processoPrincipal->buscar($_POST['processo_apensado']);
            $processo->setApensado($processoPrincipal);
            $processo->atualizar();
        }
    }

    /**
     * @throws BusinessException
     * @throws Exception
     */
    private function tramitarProcesso($tramite_id, $remessa = null): bool
    {
        $tramite = (new Tramite())->buscar($tramite_id);
        $processo = $tramite->getProcesso();
        $this->apensar($processo);
        $tramite->setIsDespachado(true);
        $tramite->setDataDespacho(new DateTime());
        $tramite->atualizar();

        $prosseguir_fase = true;
        $tramites_atuais = $processo->getTramiteAtual();
        $setorAnterior = $tramite->getSetorAtual();
        foreach ($tramites_atuais as $tramite_atual) {
            if (!$tramite_atual->getIsDespachado()) {
                $prosseguir_fase = false;
                break;
            }
        }
        if ($prosseguir_fase) {
            if (isset($_POST['assuntoProsseguir']) && !empty($_POST['assuntoProsseguir'])) {
                $assuntoProcesso = new AssuntoProcesso();
                $assuntoProcesso->setProcesso($processo);
                $assuntoProcesso->setAssunto($processo->getAssunto());
                $processo->adicionarAssunto($assuntoProcesso);
                $processo->setAssunto((new Assunto())->buscar($_POST['assuntoProsseguir']));
                $processo->setNumeroFase(1);
                $this->adicionarTramites($processo, $processo->getAssunto(),  $processo->getNumeroFase(), $setorAnterior, true, $remessa);
            }
            else{
                $this->adicionarTramites($processo, $processo->getAssunto(), !isset($_POST['devolver']) || $_POST['devolver'] == 0 ? $processo->getNumeroFase() + 1 : $processo->getNumeroFase(), $setorAnterior, true, $remessa);
            }
            $this->verificarArquivamentoAutomatico($processo);
            $processo->atualizar();
            $this->vincularDocumentosRequeridos($processo->getTramites()->last(), $tramite);
            ComponenteController::inserirComponente($processo, null, $processo->getTramites()->last());
        }
        return $prosseguir_fase;
    }

    private function vincularDocumentosRequeridos( $tramite,  $tramiteAnterior){
        foreach($tramiteAnterior->getDocumentosRequerimentosCadastrados() as $documentoRequerido){
            $documentoRequerido->setTramiteValidar($tramite);
            $documentoRequerido->atualizar();
        }
    }

    private function verificarArquivamentoAutomatico($processo)
    {
        if (!empty($_POST['setor_destino_id'])) {
            $setorDestino = (new Setor())->buscar($_POST['setor_destino_id'][0]);
            $statusDestino = (new StatusProcesso())->buscar($_POST['status_processo_id'][0]);
            if ($statusDestino->getIsArquivamento() || (!is_null($setorDestino) && $setorDestino->getArquivar())) {
                $processo->setDataArquivamento(new DateTime());
                $processo->setJustificativaEncerramento($_POST['descricao_tramite'][0]);
                $processo->setIsArquivado(true);
                $tramiteAtual = $processo->getTramiteAtual();
                if (is_array($tramiteAtual) || $tramiteAtual instanceof Collection) {
                    foreach ($processo->getTramiteAtual() as $tramite) {
                        $tramite->setStatus((new StatusProcesso())->buscar(StatusProcesso::ARQUIVADO));
                    }
                } else {
                    $tramiteAtual->setStatus((new StatusProcesso())->buscar(StatusProcesso::ARQUIVADO));
                }
            }
        }
    }

    /**
     * @throws Exception
     */
    private function adicionarTramites(Processo $processo, Assunto $assunto, $numero_fase, Setor $setorAnterior = null, $registrar = true, $remessa = null)
    {
        $setores_destino_id = is_array($_POST['setor_destino_id']) ? $_POST['setor_destino_id'] : explode(",", $_POST['setor_destino_id']);
        $hora_envio = new DateTime();
        $usuario_envio = UsuarioController::getUsuarioLogadoDoctrine();
        if ($remessa != null) {
            $remessa->setSetorOrigem($setorAnterior);
            $remessa->setHorario($hora_envio);
            $remessa->setResponsavelOrigem($usuario_envio);
        }
        foreach ($setores_destino_id as $i => $setor_destino_id) {
            $setor_destino = (new Setor())->buscar($setor_destino_id);
            $status = (new StatusProcesso())->buscar($_POST['status_processo_id'][$i]);
            $usuarioDestino = !empty($_POST['usuario_destino_id'][$i]) ? (new Usuario())->buscar($_POST['usuario_destino_id'][$i]) : null;
            $parecer = !empty($_POST['descricao_tramite']) ? $_POST['descricao_tramite'][$i] : null;
            $tramite = new Tramite();
            if (isset($_POST['setor_destino_fluxograma_id'])) {
                if (is_array($_POST['setor_destino_fluxograma_id']) && isset($_POST['setor_destino_fluxograma_id'][$i])) {
                    $setor_destino_fluxograma = $_POST['setor_destino_fluxograma_id'][$i];
                } else if ($i = 0 && $_POST['setor_destino_fluxograma_id']){
                    $setor_destino_fluxograma = $_POST['setor_destino_fluxograma_id'];
                }
                if (isset($setor_destino_fluxograma) && $setor_destino_fluxograma != $setor_destino_id) {
                    $numero_fase--;
                    $tramite->setForaFluxograma(true);
                }
            }else if(isset($_POST['assuntoProsseguir']) && empty($_POST['assuntoProsseguir'])){
                $tramite->setForaFluxograma(true);
            }else if (isset($_POST['devolver'])) {
                $tramite->setForaFluxograma(true);
            }
            if ($remessa != null && $i == 0) {
                $remessa->setSetorDestino($setor_destino);
                $remessa->setResponsavelDestino($usuarioDestino);
                $remessa->setStatus($status);
                $remessa->setParecer($parecer);
                $tramite->setRemessa($remessa);
            }
            if (!empty($_POST['prazo_destino'][$i])) {
                $tramite->setDataVencimento(new DateTime(Functions::converteDataParaMysql($_POST['prazo_destino'][$i])));
            }
            $tramite->setDataEnvio($hora_envio);
            $tramite->setIsCancelado(false);
            $tramite->setNumeroFase($numero_fase);
            $tramite->setCancelouDecisao(isset($_POST['cancelouDecisao']));
            $tramite->setIsRecebido(false);
            $tramite->setParecer($parecer);
            $tramite->setProcesso($processo);
            $tramite->setIsDespachado(false);
            if ($setorAnterior != null) {
                $tramite->setSetorAnterior($setorAnterior);
            }
            $tramite->setSetorAtual($setor_destino);
            $tramite->setUsuarioEnvio($usuario_envio);
            $tramite->setStatus($status);
            $tramite->setUsuarioDestino($usuarioDestino);
            if (!$tramite->getForaFluxograma()) {
                $tramiteController = new TramiteController();
                $tramiteController->setRespostasCampo($tramite, $assunto, $processo, $numero_fase);
                $tramiteController->setRespostasPergunta($tramite, $assunto, $numero_fase);
            }
            $processo->adicionaTramite($tramite);
            $processo->setNumeroFase($numero_fase);
            if(!is_null($tramite->getSetorAtual()) && $tramite->getSetorAtual()->getId() == AppController::getParametosConfig()['processo_setor_contribuinte_id']){
                $emailEnviado = (new Email())->notificarModificacaoProcesso($processo);
                if ($registrar) {
                    HistoricoProcesso::registrar(($emailEnviado) ? TipoHistoricoProcesso::EMAIL_ENVIADO : TipoHistoricoProcesso::EMAIL_ERRO, $processo);
                }
            }
            if ($registrar) {
                HistoricoProcesso::registrar(TipoHistoricoProcesso::ENVIADO, $processo, $tramite);
            }
        }
    }

    /**
     * @throws exception
     */
    function download()
    {
        $_POST['download'] = 1;
        $processo_id = func_get_args()[1];
        $processo = (new Processo())->buscar($processo_id);

        include APP_PATH . 'lib/pdf-merger/PDFMerger.php';
        $pdf = new PDFMerger();

        if (isset($_POST["anexos"]) && !empty($_POST["anexos"])){
            
            foreach($_POST["anexos"] as $anexoSelecionado){
                $pdf->addPDF($anexoSelecionado);
            }
            
        } else {
            if(AppController::getClienteConfig("adicionar_paginacao")){
                $pdf->addPDF($processo->getAnexosPath() . "capa_{$processo->getNumero()}_{$processo->getExercicio()}_carimbado.pdf");
            } else {
                $pdf->addPDF($processo->getAnexosPath() . "capa_{$processo->getNumero()}_{$processo->getExercicio()}.pdf");
            }

            foreach ($processo->getComponentes(true, true) as $componente) {
                if ($componente->getAnexo() ) {
                    $anexo = $componente->getAnexo();
                    $fileCarimbado = $anexo->getArquivoCarimbado();
                } else if ($componente->getTramite()) {
                    $file = $processo->getAnexosPath() . $componente->getTramite()->getNomeFormularioEletronico();
                    $fileCarimbado = substr_replace($file, "_carimbado", -4, 0);
                }
                if((isset($fileCarimbado) && is_file($fileCarimbado)) ){
                    $pdf->addPDF($fileCarimbado);
                }else if (isset($file) && is_file($file)) {
                    $pdf->addPDF($file);
                }
            }

            if ($processo->getApensos()) {
                foreach ($processo->getApensos() as $apenso) {
                    if(AppController::getClienteConfig("adicionar_paginacao")){
                        $pdf->addPDF($apenso->getAnexosPath() . "capa_{$apenso->getNumero()}_{$apenso->getExercicio()}_carimbado.pdf");
                    } else {
                        $pdf->addPDF($apenso->getAnexosPath() . "capa_{$apenso->getNumero()}_{$apenso->getExercicio()}.pdf");
                    }
                    foreach ($apenso->getComponentes(true, true) as $componente) {
                        if ($componente->getAnexo() ) {
                            $anexo = $componente->getAnexo();
                            $fileCarimbado = $anexo->getArquivoCarimbado();
                        } else if ($componente->getTramite()) {
                            $file = $apenso->getAnexosPath() . $componente->getTramite()->getNomeFormularioEletronico();
                            $fileCarimbado = substr_replace($file, "_carimbado", -4, 0);
                        }
                        if((isset($fileCarimbado) && is_file($fileCarimbado)) ){
                            $pdf->addPDF($fileCarimbado);
                        }else if (isset($file) && is_file($file)) {
                            $pdf->addPDF($file);
                        }
                    }
                }
            }
        }
        
        $pdf->merge('browser', "Processo_{$processo->getNumero()}_{$processo->getExercicio()}.pdf");
    }

    function downloadZip()
    {
        $processo_id = func_get_args()[1];
        $processo = (new Processo())->buscar($processo_id);
        include APP_PATH . 'lib/pdf-merger/PDFMerger.php';
        $zip = new ZipArchive();
        $zipPath = $processo->getAnexosPath() . "processo_{$processo->getNumero()}_{$processo->getExercicio()}.zip";
        $zip->open($zipPath, ZipArchive::CREATE);
        $capa = "capa_{$processo->getNumero()}_{$processo->getExercicio()}.pdf";
        $zip->addFile($processo->getAnexosPath() . $capa, $capa);

        foreach ($processo->getAnexos() as $anexo) {
            $assinatura = new Assinatura();
            $assinatura = $assinatura->buscarPorAnexo($anexo);
            if(!empty($assinatura)){
                $app = AppController::getConfig();
                $url = $app["lxsign_url"]."documento/versaoImpressa/".$assinatura->getLxsign_id();
                $opts = array(
                    "ssl"=>array(
                        "verify_peer"=>false,
                        "verify_peer_name"=>false,
                    )
                );
                $context = stream_context_create($opts);
                $filePdf = file_get_contents($url, false, $context);

                if($filePdf){
                    $copyPath = "/tmp/anexo_{$anexo->getId()}.pdf";
                    file_put_contents($copyPath, $filePdf);
                    $file = $copyPath;
                }else{
                    $file = $anexo->getPath() . $anexo->getArquivo(true);
                }
            }else{
                $file = $anexo->getPath() . $anexo->getArquivo(true);
            }
            if (is_file($file)) {
                $tipoDocumento = preg_replace("/[^0-9a-zA-Z]+/","_",trim($anexo->getTipo()));
                $novoNome = str_replace("/","_","{$tipoDocumento}_{$anexo->getNumero()}_{$anexo->getArquivo()}");
                $zip->addFile($file, $novoNome);
            }
        }
        foreach ($processo->getComponentes() as $componente) {
            if ($componente->getTramite()) {
                $file = $processo->getAnexosPath() . $componente->getTramite()->getNomeFormularioEletronico();
                if (is_file($file)) {
                    $zip->addFile($file, $componente->getTramite()->getNomeFormularioEletronico());
                }
            }
        }
        $zip->close();
        if(file_exists($zipPath)){
            header("Location: ".APP_URL.str_replace(APP_PATH,"",$zipPath));
        }
    }

    /**
     * Gera etiqueta para impressoa matricial para o processo
     */
    function gerarEtiqueta()
    {
        require_once APP_PATH . 'lib/fpdf/fpdf.php';
        $processo_id = func_get_args()[1];
        $processo = new Processo();
        $processo = $processo->buscar($processo_id);
        // Variaveis de Tamanho
        $mesq = "8"; // Margem Esquerda (mm)
        $mdir = "4"; // Margem Direita (mm)
        $msup = "6"; // Margem Superior (mm)
        $leti = "107"; // Largura da Etiqueta (mm)
        $aeti = "36"; // Altura da Etiqueta (mm)
        $pdf = new FPDF('L', 'mm', array($leti, $aeti));
        $pdf->SetTitle("Etiqueta Processo " . $processo);
        $pdf->AliasNbPages();
        $pdf->AddPage(); // adiciona a primeira pagina
        $pdf->SetMargins($mesq, $msup, $mdir);
        $pdf->SetAuthor("LXtec Informática"); // Define o autor
        $pdf->SetFont('Arial', '', 9.5); // Define a fonte
        $words_count = 47;
        $numero_processo = $processo->getNumero(true) . "/" . $processo->getExercicio();
        $nomenclatura = strtoupper(IndexController::getParametosConfig()['nomenclatura']);
        $pdf->Text($mesq, $msup + 4, "$nomenclatura:  " . $numero_processo . "  - " . $processo->getDataAbertura()->format('d/m/Y')); // Imprime o numero do processo com as coordenadas
        $pdf->Text($mesq, $msup + 8, substr(strtoupper(utf8_decode($processo->getInteressado())), 0, $words_count)); // Imprime o interessado de acordo com as
        $pdf->Text($mesq, $msup + 12, substr(strtoupper(utf8_decode($processo->getAssunto())), 0, $words_count)); // Imprime o assunto de acordo com as coordenadas
        $objeto = explode("\n", trim(mb_strtoupper(utf8_decode($processo->getObjeto()), 'ISO-8859-1')));
        if (is_array($objeto)) {
            $y_objeto = $msup + 16;
            foreach ($objeto as $i => $texto) {
                if ($i <= 2) {
                    $pdf->Text($mesq, $y_objeto, substr($texto, 0, $words_count));
                    $y_objeto += 4;
                }
            }
        }
        $pdf->Text($mesq, $msup + 28, strtoupper(utf8_decode("RESPONSÁVEL: " . substr($processo->getUsuarioAbertura(), 0, $words_count)))); // Imprime a descricao de acordo com as coordenadas
        $pdf->Output();
    }

    /**
     * Gera recibo para interessado
     */
    function gerarRecibo()
    {
        $processo_id = func_get_args()[1];
        $processo = new Processo();
        $processo = $processo->buscar($processo_id);
        $label_offset = 30;
        $line_heigth = 7;
        $font_size = 10;
        $pdf = new CapaProcesso($processo);
        $pdf->SetTitle("Recibo de Processo - " . $processo);
//        $pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->SetFont('times', 'B', 12);
        $pdf->SetTextColor(136, 136, 136);
        $pdf->SetY($pdf->GetY() + 25);
        $pdf->Cell(0, 10, 'Recibo Requerimento - Via Interessado', 1, 1, 'C');
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('times', '', $font_size);
        $pdf->Ln();
        $pdf->Cell($label_offset, $line_heigth, 'Processo :', 0, 0);
        $pdf->SetFont('times', 'B', $font_size);
        $pdf->Cell(0, $line_heigth, $processo->getNumero(true) . "/" . $processo->getExercicio(), 0, 1);
        $pdf->SetFont('times', '', $font_size);
        $pdf->Cell($label_offset, 5, 'Data abertura:', 0, 0);
        $pdf->SetFont('times', 'B', $font_size);
        $pdf->Cell(0, $line_heigth, $processo->getDataAbertura()->format('d/m/Y'), 0, 1);
        $pdf->SetFont('times', '', $font_size);
        $pdf->Cell($label_offset, $line_heigth, 'Nome:', 0, 0);
        $pdf->SetFont('times', 'B', $font_size);
        $pdf->Cell(0, $line_heigth, $processo->getInteressado(), 0, 1);
        $pdf->SetFont('times', '', $font_size);
        $pdf->Cell($label_offset, $line_heigth, 'Assunto:', 0, 0);
        $pdf->SetFont('times', 'B', $font_size);
        $pdf->Cell(0, $line_heigth, $processo->getAssunto(), 0, 1);
        $pdf->SetFont('times', '', $font_size);
        $pdf->Cell($label_offset, $line_heigth, 'Objeto:', 0, 0);
        $pdf->SetFont('times', 'B', $font_size);
        $pdf->MultiCell(0, $line_heigth, mb_strtoupper($processo->getObjeto()));
        $pdf->Ln(10);
        $Y_descricao = $pdf->GetY();
        $pdf->Image(APP_PATH . 'assets/img/recorte.jpg', 8, $Y_descricao, 192);
        $pdf->Output();
    }

    public function statusAssinatura() {
        if (empty($_GET['ids'])) {
            echo "[]"; die;
        }
        $data = array_map(function ($id) {
            $anexosIds = (new Processo())->buscarLxSignIdDosAnexos($id);
            if (!empty($anexosIds)) {
                $anexosStatusTemp = self::buscarStatusAssinaturas($anexosIds);
                $semAssinatura = 0;
                foreach ($anexosStatusTemp as $assinatura) {
                    if(($assinatura->status == 'Em Processo')){
                        $semAssinatura =  1;
                        break;
                    }
                }
                $statusAssinatura = ($semAssinatura > 0)
                    ? '<i data-toggle="tooltip" data-placement="top" title="Pendente de assinatura(s)." class="fa fa-exclamation-circle text-warning" style="font-size: 1.2rem;"></i>'
                    : '<i data-toggle="tooltip" data-placement="top" title="Totalmente assinado." class="fa fa-edit text-success" style="font-size: 1.2rem;"></i>';
            } else {
                $statusAssinatura = '<i class="fa fa-check-circle-o text-info" data-toggle="tooltip" data-placement="top" title="Sem requisição de assinatura(s)." style="font-size: 1.2rem;"></i>';//não necessita de assinatura
            }
            return $statusAssinatura;
        }, $_GET['ids']);
        echo json_encode($data);
    }

    public static function buscarStatusAssinaturas($idsRemotos) {
        $opts = array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-type: application/x-www-form-urlencoded',
                'content' => http_build_query(['id' => $idsRemotos])
            ),
            "ssl" => array(
                "verify_peer"=>false,
                "verify_peer_name"=>false,
            )
        );
        $context = stream_context_create($opts);
        $url = AppController::getConfig()['lxsign_url'] . "documento/restAPI/consultar?access_token=" . self::LX_SIGN_TOKEN;
        return json_decode(file_get_contents($url, false, $context));
    }

    private function ehTramitacaoRedundante(): bool
    {
        $criterios = [
            'processo_id' => $_POST['processo_id'],
            'setor_origem_id' => $_POST['setor_origem_id'],
            'setor_destino_id' => $_POST['setor_destino_id'][0],
            'status_processo_id' => $_POST['status_processo_id'][0],
            'usuario_destino_id' => $_POST['usuario_destino_id'][0],
            'prazo_destino' => $_POST['prazo_destino'][0],
            'descricao_tramite' => $_POST['descricao_tramite'][0]
        ];
        return (new Tramite())->ehDuplicado($criterios);
    }
}