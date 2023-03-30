<?php

namespace App\Model\Dao;

use Core\Util\Functions;
use App\Enum\TipoLog;
use App\Enum\TipoUsuario;
use App\Model\Pessoa;
use App\Model\Usuario;
use Core\Model\AppDao;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\QueryBuilder;

class UsuarioDao extends AppDao
{

    function __construct()
    {
        parent::__construct(Usuario::class);
    }

    /**
     * @return float|int|mixed|string
     * @throws Exception
     * @throws ORMException
     * @throws \Doctrine\ORM\ORMException
     */
    function listarUsuariosDigitalizacao()
    {
        $query = parent::getEntityManager()->createQuery('SELECT u FROM App\Model\Usuario u WHERE u.nomePastaDigitalizacao IS NOT NULL');
        return $query->getResult();
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
        $query = parent::getEntityManager()->createQuery('SELECT u FROM App\Model\Usuario u WHERE u.nome=:nome');
        $query->setParameter('nome', $nome);
        return $query->getResult();
    }

    /**
     * Método para buscar usuário com o e-mail informado.
     * @param $email
     * @return float|int|mixed|string
     * @throws Exception
     * @throws ORMException
     * @throws \Doctrine\ORM\ORMException
     */
    function buscaPorEmail($email)
    {
        $query = parent::getEntityManager()->createQuery('SELECT u 
        FROM App\Model\Usuario u
        JOIN u.pessoa p WHERE p.email=:email');
        $query->setParameter('email', $email);
        return $query->getResult();
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     */
    function buscaPorLogin($login)
    {
        return parent::getEntityManager()->getRepository('App\Model\Usuario')->findOneBy(array('login' => $login));
    }

    /**
     * @param string $tokenAtivacao
     * @return Usuario|null
     * @throws \Doctrine\ORM\ORMException
     */
    public function buscarPorTokenAtivacao(string $tokenAtivacao): ?Usuario
    {
        return parent::getEntityManager()->getRepository(Usuario::class)->findOneBy(array('tokenAtivacao' => $tokenAtivacao));
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @return QueryBuilder
     */
    protected function getQueryBuilder(): QueryBuilder
    {
        $query = self::getEntityManager()->createQueryBuilder();
        $query->select('usuario');
        $query->from(Usuario::class,'usuario');
        return $query;
    }



    /**
     * @throws Exception
     * @throws \Doctrine\ORM\ORMException
     * @throws ORMException
     */
    function listarUsuarios()
    {
        $query = parent::getEntityManager()->createQuery('SELECT u FROM App\Model\Usuario u WHERE  u.tipo<>:tipo_master AND u.tipo<>:tipo_interessado');
        $query->setParameter('tipo_master', TipoUsuario::MASTER);
        $query->setParameter('tipo_interessado', TipoUsuario::INTERESSADO);
        return $query->getResult();
    }

    /**
     * Método para buscar usuário credenciado no banco.
     * @param $login
     * @param $senha
     * @return mixed|object|null
     * @throws Exception
     * @throws ORMException
     * @throws \Doctrine\ORM\ORMException
     */
    function autenticar($login, $senha)
    {
        return parent::getEntityManager()->getRepository('App\Model\Usuario')->findOneBy(array('login' => $login, 'senha' => $senha));
    }

    /**
     * Lista as tentativas de login de um usuário dentro de um intervalo.
     * @param Usuario $usuario
     * @param $time
     * @return array|float|int|string
     * @throws Exception
     * @throws \Doctrine\ORM\ORMException
     */
    function listarTentativas(Usuario $usuario, $time)
    {
        $query = parent::getEntityManager()->createQuery('SELECT l FROM App\Model\Log l WHERE l.tipo=:tipo AND l.usuario=:usuario AND l.horario>:time');
        $query->setParameter('tipo', TipoLog::LOGIN_ATTEMPT);
        $query->setParameter('usuario', $usuario);
        $query->setParameter('time', $time);
        return $query->getResult();
    }

    /**
     * TODO: Migrar para uma função genérica.
     * @param $nome
     * @param $campos
     * @param int $hydratationMode
     * @return float|int|mixed|string
     * @throws Exception
     * @throws ORMException
     * @throws \Doctrine\ORM\ORMException
     */
    function pesquisarPorNome($nome, $campos = null, int $hydratationMode = AbstractQuery::HYDRATE_OBJECT) {
        if (!is_null($campos)) {
            foreach ($campos as $key => $campo) {
                if (!is_int($key)) {
                    $camposTemp[] = "u." . $campo . " AS " . $key;
                } else {
                    $camposTemp[] = "u." . $campo;
                }
            }
            if (isset($camposTemp) && count($camposTemp) > 0) {
                $fields = implode(",", $camposTemp);
            } else {
                $fields = "u";
            }
        } else {
            $fields = "u";
        }
        $query = parent::getEntityManager()->createQuery('SELECT ' . $fields . ' FROM App\Model\Usuario u WHERE u.nome LIKE :nome ORDER BY u.nome');
        $query->setParameter('nome', $nome);
        return $query->getResult($hydratationMode);
    }

    function listarSelect2($busca, $pagina)
    {
        $total = $this->contar();
        $inicio = ($total * $pagina) - $total;
        $sql = " SELECT u.id,p.nome AS text"
            . " FROM App\Model\Usuario u"
            . " JOIN App\Model\Pessoa p WITH p=u.pessoa"
            . " WHERE p.nome LIKE :busca"
            . " AND u.ativo = 1";
        $query = parent::getEntityManager()->createQuery($sql);
        $query->setParameter('busca', "%" . Functions::sanitizeString($busca) . "%");
        $query->setMaxResults(50);
        $query->setFirstResult($inicio);
        return $query->getResult();

    }
}