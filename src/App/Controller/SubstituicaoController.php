<?php

namespace App\Controller;

use App\Enum\StatusSolicitacao;
use App\Enum\TipoHistoricoAnexo;
use App\Enum\TipoSolicitacao;
use App\Enum\TipoUsuario;
use App\Log\HistoricoAnexo;
use App\Model\Anexo;
use App\Model\AnexoAlteracao;
use App\Model\AnexoSubstituicao;
use App\Model\Classificacao;
use App\Model\Dao\AnexoDao;
use App\Model\Dao\SubstituicaoDao;
use App\Model\Solicitacao;
use App\Model\Substituicao;
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

class SubstituicaoController extends AppController
{
    /**
     * @type Usuario
     */
    private $usuarioLogado;
    /**
     * @type SubstituicaoDao
     */
    private $substituicaoDao;

    public function __construct()
    {
        parent::__construct(get_class());
        $this->breadcrumb = self::getParametosConfig('nomenclatura') . 's';
        $this->substituicaoDao = new SubstituicaoDao();
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

    public function montarSubstituicao(Anexo $anexoNovo, Anexo $anexoAnterior)
    {

        $substituicao = new Substituicao();
        $anexoSubstituicao = new AnexoSubstituicao();

        $anexoSubstituicao->setIsAutoNumeric($anexoAnterior->getIsAutoNumeric());
        $anexoSubstituicao->setTipo($anexoAnterior->getTipo());
        $anexoSubstituicao->setClassificacao($anexoAnterior->getClassificacao());
        $anexoSubstituicao->setDescricao($anexoAnterior->getDescricao());
        $anexoSubstituicao->setData($anexoAnterior->getData(false));
        $anexoSubstituicao->setNumero($anexoAnterior->getNumero(false));
        $anexoSubstituicao->setExercicio($anexoAnterior->getExercicio());
        $anexoSubstituicao->setValor($anexoAnterior->getValor());
        $anexoSubstituicao->setQtdePaginas($anexoAnterior->getQtdePaginas());
        $anexoSubstituicao->setUsuario(UsuarioController::getUsuarioLogadoDoctrine());
        $anexoSubstituicao->setProcesso($anexoAnterior->getProcesso());
        $anexoSubstituicao->setArquivo($anexoAnterior->getArquivo(true));

        $substituicao->setAnexo($anexoNovo);
        $substituicao->setAnexoAnterior($anexoSubstituicao);
        $substituicao->setMotivo($_POST['motivo']);
        $substituicao->setResponsavel(UsuarioController::getUsuarioLogadoDoctrine());

        $this->substituicaoDao->inserir($substituicao);
    }

    public function buscarSubstituicoesAnexo(int $anexo_id){
        return (new Substituicao())->buscarSubstituicoesAnexo($anexo_id);
    }

    private function validarAutenticacao()
    {
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
}
