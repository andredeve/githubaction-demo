<?php

namespace App\Model\Dao;

use App\Model\Pessoa;
use Core\Model\AppDao;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Exception\ORMException;

class PessoaDao extends AppDao
{

    function __construct()
    {
        parent::__construct(Pessoa::class);
    }

    /**
     * @param $nome
     * @return float|int|mixed|string
     * @throws Exception
     * @throws ORMException
     * @throws \Doctrine\ORM\ORMException
     */
    function buscarPorNome($nome)
    {
        $query = self::getEntityManager()->createQueryBuilder();
        $query->select('pessoa');
        $query->from(Pessoa::class,'pessoa');
        $query->where('pessoa.nome=:nome');
        $query->setParameter('nome', $nome);
        $query->orderBy('pessoa.nome', 'ASC');
        return $query->getQuery()->getResult();
    }

    /**
     * Método para buscar usuário com o e-mail informado.
     * @param $email
     * @throws \Exception
     */
    public function buscaPorEmail($email)
    {
        return parent::getEntityManager()->getRepository(Pessoa::class)->findOneBy(array('email' => $email));
    }

}