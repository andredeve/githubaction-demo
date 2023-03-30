<?php

namespace App\Model\Dao;

use App\Enum\TipoLog;
use App\Model\Interessado;
use Core\Model\AppDao;
use Core\Util\Functions;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class InteressadoDao extends AppDao
{

    /**
     * @deprecated Utilizar construtor vazio.
     */
    function __construct($entidade = Interessado::class)
    {
        parent::__construct($entidade);
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws ORMException
     * @throws NonUniqueResultException
     * @throws Exception
     * @throws NoResultException
     */
    function listarSelect2($busca, $pagina)
    {
        $total = $this->contar();
        $inicio = ($total * $pagina) - $total;
        $sql = " SELECT i.id,p.nome AS text"
            . " FROM App\Model\Interessado i"
            . " JOIN App\Model\Pessoa p WITH p=i.pessoa"
            . " WHERE p.shadowNome LIKE :busca"
            . " AND i.isAtivo = 1";
        $query = parent::getEntityManager()->createQuery($sql);
        $query->setParameter('busca', "%" . Functions::sanitizeString($busca) . "%");
        $query->setMaxResults(50);
        $query->setFirstResult($inicio);
        return $query->getResult();

    }

    /**
     * Busca setores pelo início do seu nome.
     * @param $nome
     * @return float|int|mixed|string
     * @throws Exception
     * @throws ORMException
     * @throws \Doctrine\ORM\ORMException
     */
    public function buscarPorNome($nome)
    {
        $sql = "SELECT i, p FROM App\Model\Interessado i "
            . " JOIN App\Model\Pessoa p"
            . " WHERE p.nome LIKE :nome";
        $query = parent::getEntityManager()->createQuery($sql);
        $query->setParameter('nome', $nome . '%');
        $query->setMaxResults(50);
        return $query->getResult();
    }
}
