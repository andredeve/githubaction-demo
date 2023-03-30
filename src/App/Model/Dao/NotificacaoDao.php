<?php

namespace App\Model\Dao;

use App\Controller\UsuarioController;
use App\Model\Notificacao;
use Core\Model\AppDao;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class NotificacaoDao extends AppDao {

    /**
     * @deprecated Utilizar construtor vazio.
     */
    function __construct($entidade = Notificacao::class) {
        parent::__construct($entidade);
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws ORMException
     * @throws Exception
     */
    function listarArquivadas() {
        $sql = "SELECT n "
                . " FROM App\Model\Notificacao n "
                . " WHERE n.isArquivada=true"
                . " AND n.usuarioAbertura=:usuario"
                . " OR n.usuarioDestino=:usuario"
                . " ORDER BY n.dataArquivamento DESC";
        $query = parent::getEntityManager()->createQuery($sql);
        $query->setParameter('usuario', UsuarioController::getUsuarioLogadoDoctrine());
        return $query->getResult();
    }

    /**
     * Busca maior número de processo do ano atual
     *
     * @return float|int|mixed|string
     * @throws Exception
     * @throws ORMException
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws \Doctrine\ORM\ORMException
     */
    function getMaiorNumero() {
        $sql = "SELECT MAX(n.numero) numero "
                . " FROM App\Model\Notificacao n "
                . " WHERE SUBSTRING(n.dataCriacao, 1, 4)=:ano";
        $query = parent::getEntityManager()->createQuery($sql);
        $query->setParameter('ano', Date('Y'));
        return $query->getSingleScalarResult();
    }
}
