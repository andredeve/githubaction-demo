<?php

namespace App\Model\Dao;

use App\Model\AssuntoProcesso;
use Core\Model\AppDao;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\Exception\ORMException;

class AssuntoProcessoDao extends AppDao
{
    /**
     * @deprecated Utilizar construtor vazio.
     */
    function __construct($entidade = AssuntoProcesso::class)
    {
        parent::__construct($entidade);
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws ORMException
     * @throws Exception
     */
    function buscarPorAssunto($assunto_id, $processo_id)
    {
        $sql = "SELECT ap FROM \App\Model\AssuntoProcesso ap JOIN ap.processo p JOIN ap.assunto a WHERE a.id=:assunto_id AND p.id=:processo_id";
        $query = parent::getEntityManager()->createQuery($sql);
        $query->setParameter('assunto_id', $assunto_id);
        $query->setParameter('processo_id', $processo_id);
        return $query->getResult();
    }
}