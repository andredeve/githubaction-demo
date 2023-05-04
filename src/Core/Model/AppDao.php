<?php

namespace Core\Model;

use App\Controller\UsuarioController;
use App\Enum\TipoUsuario;
use Core\Controller\AppController;
use Core\Exception\BusinessException;
use Core\Util\EntityManagerConn;
use Core\Util\Functions;
use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\TransactionRequiredException;
use Exception;
use ReflectionException;
use ReflectionMethod;

/**
 * Classe Dao
 * Representa classe genérica DAO para classe modelo
 * @version 1.0
 * @author Anderson Brandão <batistoti@gmail.com>
 */
abstract class AppDao
{

    /**
     * Variável que guarda o objeto da classe model
     * @var String
     */
    private $entidade;

    /**
     * Variável que guarda o campo que será ordenado em todas as listagens do objeto
     * @var String
     */
    private $order_by;

    /**
     * Construtor da classe
     * @param string|object $entidade = nome da entidade da classe
     * @param string $order_by = string com campo que deve ser ordenado (opcional)
     */
    function __construct($entidade, $order_by = '')
    {
        if (!is_object($entidade)) {
            $this->entidade = new $entidade;
        } else {
            $this->entidade = $entidade;
        }
        $this->order_by = $order_by;
    }

    private function __clone()
    {

    }

    public function __destruct()
    {
        foreach ($this as $key => $value) {
            unset($this->$key);
        }
        foreach (array_keys(get_defined_vars()) as $var) {
            unset(${"$var"});
        }
        unset($var);

    }

    /**
     * @return EntityManager
     * @throws ORMException
     * @throws Exception
     */
    protected static function getEntityManager(): EntityManager
    {
        return EntityManagerConn::getEntityManager();
    }

    /**
     * Método que devolve o nome da tabela de uma entidade
     * @return string
     * @throws ORMException
     */
    function getTableName()
    {
        return $this->getEntityManager()->getClassMetadata(get_class($this->entidade))->getTableName();
    }

    protected function getConnection()
    {
        return self::getEntityManager()->getConnection();
    }

    /**
     * Método que realizar o commit das requisições em aberto
     * @throws BusinessException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    private function flush($validarSomenteLeitura = true)
    {
        if(AppController::sistemaSomenteLeitura()) {
            throw new BusinessException("Sistema em modo somente leitura.");
        }
        if(UsuarioController::getUsuarioLogadoDoctrine() && TipoUsuario::VISITANTE == UsuarioController::getUsuarioLogadoDoctrine()->getTipo() && $validarSomenteLeitura) {
            throw new BusinessException("Usuário sem permissão.");
        }
        $this->getEntityManager()->flush();
    }

    /**
     * @throws ORMException
     */
    public function clear()
    {
        $this->getEntityManager()->clear();
    }

    /**
     * @throws ORMException
     */
    public function merge()
    {
        $this->getEntityManager()->persist($this->entidade);
    }

    /**
     * @param $object
     * @param bool $validarSomenteLeitura
     * @return int|null
     * @throws BusinessException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function inserir($object = null, bool $validarSomenteLeitura = true): ?int
    {
        
        if(AppController::sistemaSomenteLeitura() && $validarSomenteLeitura) {
            throw new BusinessException("Sistema em modo somente leitura.");
        }

        if(UsuarioController::getUsuarioLogadoDoctrine() && TipoUsuario::VISITANTE == UsuarioController::getUsuarioLogadoDoctrine()->getTipo() && $validarSomenteLeitura) {
            throw new BusinessException("Usuário sem permissão.");
        }
        if (!is_null($object)) {
            $entidade = $object;
        } else {
            $entidade = $this->entidade;
        }
        
        $this->getEntityManager()->persist($entidade);
        $this->flush($validarSomenteLeitura);
        return $entidade->getId();
    }

    /**
     * Método que contabiliza todos os registros de uma tabela
     * @return int|null
     * @throws NoResultException
     * @throws NonUniqueResultException|ORMException
     */
    public function contar()
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $result = $qb->select('count(object.id)')
            ->from(get_class($this->entidade), 'object')
            ->getQuery()
            ->getSingleScalarResult();
        if (is_numeric($result)) {
            return intval($result);
        } else {
            return null;
        }
    }

    /**
     * Método que lista a entidade pelo periodo e campo fornecidos
     * @param $campo
     * @param $dataInicio
     * @param $dataFim
     * @return array|float|int|string
     * @noinspection PhpUnused
     * @noinspection SqlResolve
     * @throws ORMException
     */
    function listarPorPeriodo($campo, $dataInicio, $dataFim)
    {
        $classe = get_class($this->entidade);
        $sql = "SELECT o FROM $classe o WHERE o.id IS NOT NULL ";
        $sql .= $this->getFilterDateQuery($dataInicio, $dataInicio, 'dataInicio', 'dataFim', "o.$campo");
        $query = $this->getEntityManager()->createQuery($sql);
        if (!empty($dataInicio) && !empty($dataFim)) {
            $query->setParameter('dataInicio', $dataInicio);
            $query->setParameter('dataFim', $dataFim);
        } else if (!empty($dataInicio) && empty($dataFim)) {
            $query->setParameter('dataInicio', $dataInicio);
        } else if (!empty($dataFim) && empty($dataInicio)) {
            $query->setParameter('dataFim', $dataFim);
        }
        return $query->getResult();

    }

    /**
     * Método que lista todos os registros de uma tabela
     * @return $this[]
     * @throws ORMException
     */
    public function listar()
    {
        if (!empty($this->order_by)) {
            return $this->getEntityManager()->getRepository(get_class($this->entidade))->findBy(array(), array($this->order_by => 'ASC'));
        }
        return $this->getEntityManager()->getRepository(get_class($this->entidade))->findAll();
    }

    /**
     * @return array|object[]
     * @throws ORMException
     */
    public function listarAtivos() {
        if (!empty($this->order_by)) {
            return $this->getEntityManager()->getRepository(get_class($this->entidade))->findBy(['ativo' => 1], array($this->order_by => 'ASC'));
        }
        return $this->getEntityManager()->getRepository(get_class($this->entidade))->findBy(['ativo' => 1]);
    }

    /**
     * Método que lista todos os registros tendo como parametros um array de campos
     * @param array $campos
     * @param $ordem
     * @return array|object[]
     * @throws Exception
     * @throws ORMException
     */
    public function listarPorCampos(array $campos, $ordem = null): array
    {
        return $this->getEntityManager()->getRepository(get_class($this->entidade))->findBy($campos, $ordem);
    }

    /**
     * Método que atualiza um registro banco de dados
     * @throws ORMException|BusinessException|Exception
     */
    public function atualizar($validarSomenteLeitura = true)
    {
        if(!AppController::sistemaSomenteLeitura() && $validarSomenteLeitura) {
            throw new BusinessException("Sistema em modo somente leitura.");
        }
        if(UsuarioController::getUsuarioLogadoDoctrine() && TipoUsuario::VISITANTE == UsuarioController::getUsuarioLogadoDoctrine()->getTipo() && $validarSomenteLeitura) {
            throw new BusinessException("Usuário sem permissão.");
        }
        $this->getEntityManager()->persist($this->entidade);
        $this->flush();
    }


    /**
     * Método que remove um registro no banco de dados
     * @param int|null $id
     * @throws BusinessException
     * @throws ORMException
     */
    public function remover(?int $id = null, $entity = null)
    {
        if (is_null($id) && is_null($entity)) {
            throw new BusinessException("Id e entidade indefinidos. Informe um dos dois parametrôs para remover uma tupla.");
        }
        if(AppController::sistemaSomenteLeitura()) {
            throw new BusinessException("Sistema em modo somente leitura.");
        }
        if(UsuarioController::getUsuarioLogadoDoctrine() && TipoUsuario::VISITANTE == UsuarioController::getUsuarioLogadoDoctrine()->getTipo()) {
            throw new BusinessException("Usuário sem permissão.");
        }
        if (is_null($entity)) {
            $entity = $this->buscar($id);
        }
        $this->getEntityManager()->remove($entity);
        $this->flush();
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     * @throws TransactionRequiredException
     * @throws BusinessException
     */
    public function desativar($id) {
        if(AppController::sistemaSomenteLeitura()) {
            throw new BusinessException("Sistema em modo somente leitura.");
        }
        if(UsuarioController::getUsuarioLogadoDoctrine() && TipoUsuario::VISITANTE == UsuarioController::getUsuarioLogadoDoctrine()->getTipo()) {
            throw new BusinessException("Usuário sem permissão.");
        }
        $em = $this->getEntityManager();
        $entity = $em->find(get_class($this->entidade), $id);
        if (method_exists($entity, 'setAtivo')) {
            $entity->setAtivo(0);
            $this->flush();
        } else if (method_exists($entity, 'setIsAtivo')) {
            $entity->setIsAtivo(0);
            $this->flush();
        } else if (method_exists($entity, 'isAtivo')) {
            $entity->setAtivo(0);
            $this->flush();
        } else {
            throw new BusinessException("Falha ao desativar: Operação inexistente.");
        }

    }

    /**
     * Método que remove um registro no banco de dados
     * @param string $classe
     * @param array $campo
     * @param array|null $orderBy
     * @return array|object[]
     * @throws Exception
     * @throws ORMException
     */
    public static function listarPor(string $classe, array $campo, array $orderBy = null): array
    {
        return self::getEntityManager()->getRepository($classe)->findBy($campo, $orderBy);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     * @throws TransactionRequiredException
     * @throws BusinessException
     */
    public function reativar($id) {
        if(AppController::sistemaSomenteLeitura()) {
            throw new BusinessException("Sistema em modo somente leitura.");
        }
        if(UsuarioController::getUsuarioLogadoDoctrine() && TipoUsuario::VISITANTE == UsuarioController::getUsuarioLogadoDoctrine()->getTipo()) {
            throw new BusinessException("Usuário sem permissão.");
        }
        $em = $this->getEntityManager();
        $entity = $em->find(get_class($this->entidade), $id);
        if (method_exists($entity, 'setAtivo')) {
            $entity->setAtivo(1);
            $this->flush();
        } else if (method_exists($entity, 'setIsAtivo')) {
            $entity->setIsAtivo(1);
            $this->flush();
        } else if (method_exists($entity, 'isAtivo')) {
            $entity->setAtivo(1);
            $this->flush();
        } else {
            throw new BusinessException("Falha ao reativar: Operação inexistente.");
        }

    }

    /**
     * Metodo que busca um registro no banco de dados
     * @param int $id
     * @return object|null
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     * @return mixed
     */
    public function buscar($id)
    {
        return $this->getEntityManager()->find(get_class($this->entidade), $id);
    }

    /**
     * Metodo que busca um registro no banco de dados
     * @param string[] $campos
     * @param string[]|null $orderBy
     * @return object|mixed|null
     * @throws ORMException
     */
    public function buscarPorCampos($campos, $orderBy = null)
    {
        return $this->getEntityManager()->getRepository(get_class($this->entidade))->findOneBy($campos, $orderBy);
    }

    /**
     * Metodo que busca um registro no banco de dados
     * @param string[] $campos
     * @param string[]|null $orderBy
     * @return $this[]
     * @throws ORMException
     */
	public function filtrarPorCampos($campos, $orderBy = null)
	{
		return $this->getEntityManager()->getRepository(get_class($this->entidade))->findBy($campos, $orderBy);
	}

    /**
     * Busca a quantidade de registros no banco de dados.
     * @param string[] $campos
     * @return int
     * @throws ORMException
     */
    public function qtdPorCampos(array $campos): int
    {
        return $this->getEntityManager()->getRepository(get_class($this->entidade))->count($campos);
    }

    /**
     * @throws ReflectionException
     */
    protected function getFunctionArgNames($funcName)
    {
        $f = new ReflectionMethod($this->entidade, $funcName);
        $result = array();
        foreach ($f->getParameters() as $param) {
            $result[] = $param->name;
        }
        return $result;
    }


    /**
     * Monta string padrão para buscar em campos tipo data
     * @param $data_ini
     * @param $data_fim
     * @param $campo_ini
     * @param $campo_fim
     * @param $campo
     * @return string
     */
    protected function getFilterDateQuery($data_ini, $data_fim, $campo_ini, $campo_fim, $campo)
    {
        $sql = "";
        if (!empty($data_ini) && !empty($data_fim)) {
            $sql .= " AND DATE($campo) BETWEEN :$campo_ini AND :$campo_fim";
        } else if (!empty($data_ini) && empty($data_fim)) {
            $sql .= " AND DATE($campo)>=:$campo_ini";
        } else if (!empty($data_fim) && empty($data_ini)) {
            $sql .= " AND DATE($campo)<=:$campo_fim";
        }
        return $sql;
    }

    /**
     * @throws ReflectionException
     */
    protected function setParameteres($query, $function_name, $ignore, $args)
    {
        foreach ($this->getFunctionArgNames($function_name) as $i => $arg) {
            $variavel = $args[$i];
            if (!empty($variavel) && !in_array($arg, $ignore)) {
                if (Functions::testDate($variavel)) {
                    $variavel = Functions::converteDataParaMysql($variavel);
                }
                $query->setParameter($arg, $variavel);
            }
        }
        return $query;
    }
    
    protected function getRangeSql($valor_ini, $valor_fim, $campo_ini, $campo_fim, $campo)
    {
        $sql = "";
        if (!empty($valor_ini) && !empty($valor_fim)) {
            $sql .= " AND $campo >= :$campo_ini AND $campo<=:$campo_fim";
        } else if (!empty($valor_ini) && empty($valor_fim)) {
            $sql .= " AND $campo>=:$campo_ini";
        } else if (!empty($valor_fim) && empty($valor_ini)) {
            $sql .= " AND $campo<=:$campo_fim";
        }
        return $sql;
    }


    public function beginTransaction(): void
    {
        $this->getConnection()->beginTransaction();
    }

    /**
     * @throws \Doctrine\DBAL\ConnectionException
     */
    function commit(): void
    {
        $this->getConnection()->commit();
    }

    /**
     * @throws \Doctrine\DBAL\ConnectionException
     */
    function rollback(): void
    {
        $this->getConnection()->rollback();
    }
}
