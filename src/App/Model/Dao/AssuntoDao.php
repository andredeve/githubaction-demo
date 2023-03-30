<?php /** @noinspection PhpUnused */

namespace App\Model\Dao;

use App\Controller\UsuarioController;
use App\Enum\TipoUsuario;
use App\Model\Assunto;
use App\Model\Usuario;
use Core\Model\AppDao;
use Core\Util\Functions;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class AssuntoDao extends AppDao
{

    /**
     * @deprecated Utilizar construtor vazio.
     */
    function __construct($entidade = Assunto::class)
    {
        parent::__construct($entidade);
    }

    /**
     * @param $busca
     * @param $pagina
     * @return float|int|mixed|string
     * @throws Exception
     * @throws ORMException
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws \Doctrine\ORM\ORMException
     */
    function listarSelect2($busca, $pagina)
    {
        $total = $this->contar();
        $inicio = ($total * $pagina) - $total;
        $sql = " SELECT a.id,a.descricao AS text"
            . " FROM App\Model\Assunto a"
            . " WHERE a.isAtivo=1 AND a.shadowNome LIKE :busca";
        $usuario = UsuarioController::getUsuarioLogadoDoctrine();
        if($usuario->getTipo() == TipoUsuario::INTERESSADO){
            $sql .= " AND a.isExterno = 1 ";
        }else{
            $sql .= " AND  a.assuntoPai IS NULL ";
        }   
        $query = parent::getEntityManager()->createQuery($sql);
        $query->setParameter('busca', "%" . Functions::sanitizeString($busca) . "%");
        $query->setMaxResults(50);
        $query->setFirstResult($inicio);
        return $query->getResult();
    }

    /**
     * @return float|int|mixed|string
     * @throws Exception
     * @throws ORMException
     * @throws \Doctrine\ORM\ORMException
     */
    function listarDisponiveis()
    {
        $sql = 'SELECT a,(SELECT COUNT(f) FROM App\Model\Fluxograma f WHERE f.assunto=a) as qtde'
            . ' FROM App\Model\Assunto a'
            . ' HAVING qtde=0';
        $query = parent::getEntityManager()->createQuery($sql);
        return $query->getResult();
    }

    /**
     * @param $descricao
     * @return float|int|mixed|string
     * @throws Exception
     * @throws ORMException
     * @throws \Doctrine\ORM\ORMException
     */
    function listarPorDescricao($descricao)
    {
        $sql = "SELECT a FROM App\Model\Assunto a WHERE a.descricao LIKE '%$descricao%'";
        $query = parent::getEntityManager()->createQuery($sql);
        return $query->getResult();
    }

}
