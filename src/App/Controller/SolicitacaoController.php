<?php

namespace App\Controller;

use App\Enum\StatusSolicitacao;
use App\Enum\TipoHistoricoAnexo;
use App\Enum\TipoSolicitacao;
use App\Enum\TipoUsuario;
use App\Log\HistoricoAnexo;
use App\Model\Anexo;
use App\Model\AnexoAlteracao;
use App\Model\Classificacao;
use App\Model\Dao\AnexoDao;
use App\Model\Dao\SolicitacaoDao;
use App\Model\Solicitacao;
use App\Model\TipoAnexo;
use App\Model\Usuario;
use Core\Controller\AppController;
use Core\Enum\TipoMensagem;
use Core\Exception\TechnicalException;
use Core\Util\Functions;
use Core\Util\NumericUtil;
use Core\Util\Upload;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\ORM\TransactionRequiredException;
use Exception;
use SmartyException;
use Throwable;

class SolicitacaoController extends AppController
{
    /**
     * @type Usuario
     */
    private $usuarioLogado;
    /**
     * @type SolicitacaoDao
     */
    private $solicitacaoDao;

    public function __construct()
    {
        parent::__construct(get_class());
        $this->breadcrumb = self::getParametosConfig('nomenclatura') . 's';
        $this->solicitacaoDao = new SolicitacaoDao();
        $this->usuarioLogado = UsuarioController::getUsuarioLogadoDoctrine();
        $this->validarAutenticacao();
    }

    /**
     * @throws SmartyException
     */
    public function index()
    {
        $perfil = $this->usuarioLogado->getTipo();
        if ($perfil === TipoUsuario::MASTER || $perfil === TipoUsuario::ADMINISTRADOR) {
            $this->load($this->class_path, "index_master");
        } else {
            $this->load($this->class_path, "index_comum");
        }
    }

    /**
     * @throws OptimisticLockException
     * @throws TechnicalException
     * @throws ORMException
     * @throws SmartyException
     * @throws TransactionRequiredException
     */
    public function anexo($acao, $param) {
//        TODO: Registrar log.
        switch ($acao) {
            case "excluir":
                $this->solicitarRemocaoAnexo(intval($param));
                break;
            case "editar":
                $this->solicitarEdicaoAnexo(intval($param));
                break;
            case "visualizar":
                $this->visualizarSolicitacaoAnexo(intval($param));
                break;
            default:
                throw new TechnicalException("Ação indefinida.");
        }
    }

    public function aprovar($id) {
//        TODO: Em caso de falha, retornar o arquivo ao estado original.
//        TODO: Analisar caso tenha sido efetuado múltiplas solicitações.
        $this->prosseguirOuBloquearAcesso();
        /**
         * @var Solicitacao $solicitacao
         */
        $solicitacao = $this->solicitacaoDao->buscar($id);
        if ($solicitacao->getTipo() === TipoSolicitacao::Edicao) {
            $this->atualizarAnexo($solicitacao);
        } else if ($solicitacao->getTipo() === TipoSolicitacao::Exclusao) {
            $this->removerAnexo($solicitacao);
        }
    }

    public function recusar($id) {
        $this->prosseguirOuBloquearAcesso();
        if ($this->solicitacaoDao->reprovar($id)) {
            self::setMessage(TipoMensagem::SUCCESS, "Remoção de anexo recusada.", null, true);
        } else {
            self::setMessage(TipoMensagem::ERROR, "A solicitação falhou.", null, true);
        }
    }

    public function removerSolicitacaoAnexo(int $anexo_id) {
        try {
            (new SolicitacaoDao())->removerPendenciasPorAnexo($anexo_id);
        } catch (Exception $e) {
            Functions::escreverLogErro($e);
        }
    }

    private function removerAnexo(Solicitacao $solicitacao) {
        try {
            $solicitacao->aprovar();
            $anexoAnterior = $solicitacao->getAnexoAnterior();
            $anexoAnterior->remover();
            $aprovador = UsuarioController::getUsuarioLogadoDoctrine();
            $observacao = "Aprovado por {$aprovador->getPessoa()->getNome()} (código {$aprovador->getId()}).";
            HistoricoAnexo::registrarLogAnexoRemovido($anexoAnterior, $solicitacao->getMotivo(), $observacao, $solicitacao->getSolicitante());
        } catch (Throwable $e) {
            http_response_code(409);
            error_log($e->getMessage());
            error_log($e->getTraceAsString());
            self::setMessage(TipoMensagem::ERROR, "A solicitação falhou.", null, true);
        }
    }

    private function atualizarAnexo(Solicitacao $solicitacao) {
        $anexoAnterior = $solicitacao->getAnexoAnterior();
        try {
            $solicitacao->aprovar();
            $anexoLog = $anexoAnterior;
            $anexoAnterior->merge($solicitacao->getAnexoNovo());
            $anexoAnterior->atualizar();
            $aprovador = UsuarioController::getUsuarioLogadoDoctrine();
            HistoricoAnexo::registrar(TipoHistoricoAnexo::UPDATE, $solicitacao->getMotivo(), "Autorizado por {$aprovador->getPessoa()->getNome()} (código {$aprovador->getId()}).", $anexoLog, $anexoAnterior, $solicitacao->getSolicitante());
        } catch (Throwable $e) {
            http_response_code(409);
            error_log($e->getMessage());
            error_log($e->getTraceAsString());
            self::setMessage(TipoMensagem::ERROR, "A solicitação falhou.", null, true);
        }
    }

    private function solicitarRemocaoAnexo($anexoId) {
        try {
            if ($this->solicitacaoDao->possuiPendencia($anexoId, TipoSolicitacao::Exclusao)) {
                http_response_code(201);
                exit;
            }
        } catch (NonUniqueResultException | NoResultException $e) {
            Functions::escreverLogErro($e);
        }
        try {
            $usuario = UsuarioController::getUsuarioLogadoDoctrine();
            if ($usuario->isAdm()) {
                (new AnexoController())->excluir(null, $anexoId);
            } else {
                $solicitacao = $this->montarSolicitacao($anexoId);
                $this->solicitacaoDao->inserir($solicitacao);
                http_response_code(201);
            }
        } catch (Exception $e) {
            Functions::escreverLogErro($e);
            http_response_code(500);
        }
    }

    private function solicitarEdicaoAnexo($anexoId) {
//        try {
//            if ($this->solicitacaoDao->possuiPendencia($anexoId, TipoSolicitacao::Edicao)) {
//                http_response_code(201);
//                exit;
//            }
//        } catch (NonUniqueResultException | NoResultException $e) {
//            Functions::escreverLogErro($e);
//        }
        try {
            $solicitacao = $this->montarSolicitacao($anexoId);
            $this->montarSolicitacaoEdicaoAnexo($solicitacao);
            $this->solicitacaoDao->inserir($solicitacao);
            self::setMessage(TipoMensagem::SUCCESS, "Solicitação aberta.", null, true);
        } catch (Exception $e) {
            self::registerLogError($e);
            self::setMessage(TipoMensagem::ERROR, "Ocorreu uma falha ao tentar abrir a solicitação: " . get_class($e) . ".", null, true);
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     * @throws Exception
     */
    private function montarSolicitacaoEdicaoAnexo(Solicitacao &$solicitacao) {
        $anexo = $this->solicitacaoDao->procurarAnexoSolicitacaoEdicaoPendente($solicitacao) ?? new AnexoAlteracao();
        $anexo->setProcesso($solicitacao->getAnexoAnterior()->getProcesso());
        $anexo->setIsAutoNumeric(isset($_POST['auto_numero_doc']) && $_POST['auto_numero_doc']);
//        if ($_POST['tipo_upload'] == 'model') {
//            TODO
//        }
        $anexo->setTipo((new TipoAnexo())->buscar($_POST['tipo_documento_id']));
        $anexo->setClassificacao((new Classificacao())->buscar($_POST['classificacao_documento_id']));
        $anexo->setDescricao($_POST['descricao_doc']);
        $anexo->setData(Functions::parseDate($_POST["data_doc"]));
        $anexo->setNumero($_POST["numero_doc"]);
        $anexo->setExercicio($anexo->getData()->format('Y'));
        $anexo->setValor(NumericUtil::parseFloat($_POST["valor_doc"]));
        $anexo->setQtdePaginas(intval($_POST["paginas_doc"]));
        $anexo->setUsuario(UsuarioController::getUsuarioLogadoDoctrine());
        $anexo->setProcesso($solicitacao->getAnexoAnterior()->getProcesso());
//        TODO: Mesclar arquivos if(isset($_POST['auto_numero_doc']) && $_POST["mesclar_arquivos"]);
        if (isset($_FILES['arquivo_processo'])) {
            $nome_arquivo = (new Upload('arquivo_processo', $anexo->getPath(), ['pdf', 'png', 'gif', 'doc', 'docx', 'jpg', 'jpeg']))->upload();
            $anexo->setArquivo($nome_arquivo);
        }
        $solicitacao->setTipo(TipoSolicitacao::Edicao);
        $solicitacao->setAnexoNovo($anexo);
    }

    /**
     * @param int $anexoId
     * @return Solicitacao
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     */
    private function montarSolicitacao(int $anexoId): Solicitacao
    {
        /**
         * @var Anexo $anexo
         */
        $anexoDao = new AnexoDao();
        $anexo = $anexoDao->buscar($anexoId);
        $solicitacao = $this->solicitacaoDao->procurarSolicitacaoEdicaoPendente($anexoId) ?? new Solicitacao();
        $solicitacao->setAnexoAnterior($anexo);
        $solicitacao->setMotivo($_POST['motivo']);
        $solicitacao->setSolicitante(UsuarioController::getUsuarioLogadoDoctrine());
        return $solicitacao;
    }

    private function prosseguirOuBloquearAcesso()
    {
        if (!$this->getUsuario()->isAdm()) {
            http_response_code(403);
            try {
                $this->load('Public', '403');
            } catch (SmartyException $e) {
                Functions::escreverLogErro($e);
            }
            die;
        }
    }

    private function validarAutenticacao() {
        if (is_null($this->usuarioLogado)) {
            try {
                parent::error401();
            } catch (SmartyException $e) {
                Functions::escreverLogErro($e);
                echo "Ocorreu um erro ao carregar o layout. Por favor, contate o suporte.";
                exit;
            }
        }
    }

    /**
     * @param int $id
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws SmartyException
     * @throws TransactionRequiredException
     */
    private function visualizarSolicitacaoAnexo(int $id) {
        /**
         * TODO: Criar migration da tabela de solicitação.
         */
        /**
         * @var Solicitacao $solicitacao
         */
        $solicitacao = SolicitacaoDao::get($id);
        $vars = ["solicitacao" => $solicitacao];
        $vars["anexo_anterior"] = $solicitacao->getAnexoAnterior();
        $vars["anexo_novo"] = $solicitacao->getAnexoNovo();
        $this->load("Solicitacao", "visualizar", true, false, $vars, false, false);
    }
}