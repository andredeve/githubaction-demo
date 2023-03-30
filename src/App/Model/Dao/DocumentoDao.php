<?php /** @noinspection PhpUnused */

namespace App\Model\Dao;

use App\Controller\ProcessoController;
use App\Controller\UsuarioController;
use App\Enum\TipoUsuario;
use App\Model\Documento;
use Core\Model\AppDao;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\Exception\ORMException;

class DocumentoDao extends AppDao
{
    /**
     * @deprecated Utilizar construtor vazio.
     */
    function __construct($entidade = Documento::class)
    {
        parent::__construct($entidade);
    }

    /**
     * @return string
     */
    private function getCommonFilter(): ?string
    {
        $exercicio = ProcessoController::getExercicioAtual();
        $common_sql = $exercicio != null ? " AND p.exercicio='$exercicio'" : "";
        $usuario = UsuarioController::getUsuarioLogadoDoctrine();
        if ($usuario != null && $usuario->getTipo() != TipoUsuario::MASTER && count($usuario->getSetores()) > 0) {
            $common_sql .= " AND sa.id IN({$usuario->getSetoresIds(true)})";
        }
        return $common_sql;
    }

    /**
     * @param $dataIni
     * @param $dataFim
     * @return float|int|mixed|string
     * @throws Exception
     * @throws ORMException
     * @throws \Doctrine\ORM\ORMException
     */
    function listarVencimentoProximos($dataIni, $dataFim){
        $sql = "SELECT d FROM \App\Model\Documento d "
                . " INNER JOIN d.processo p WITH p.isArquivado=false "
                . " JOIN p.tramites t WITH p.numeroFase=t.numeroFase AND t.assunto=p.assunto"
                . " JOIN t.setorAtual sa"
                . " WHERE d.vencimento BETWEEN :dataIni AND :dataFim ";
        $sql .= $this->getCommonFilter();
        $query = parent::getEntityManager()->createQuery($sql);
        $query->setParameter('dataIni', $dataIni);
        $query->setParameter('dataFim', $dataFim);
        return $query->getResult();
    }
}