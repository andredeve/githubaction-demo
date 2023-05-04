<?php

namespace Core\Model;

use App\Controller\InteressadoController;
use App\Controller\UsuarioController;
use App\Enum\TipoLog;
use App\Model\Anexo;
use App\Enum\TipoUsuario;
use App\Model\Log;
use App\Model\PermissaoEntidade;
use Core\Controller\AppController;
use Core\Exception\BusinessException;
use Core\Exception\SecurityException;
use Core\Util\Functions;
use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\ConnectionException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\PersistentCollection;
use Doctrine\ORM\TransactionRequiredException;
use Exception;
use JsonSerializable;
use ReflectionClass;

abstract class AppModel implements JsonSerializable
{

    /**
     * Variável que guarda o objeto DAO da classe
     * @var mixed|AppDao Object
     */
    private $dao;

    /**
     * @return int|null
     */
    abstract function getId(): ?int;

    /**
     * @param int|null $id
     * @return void
     */
    abstract function setId(?int $id): void;

    protected function getDAO()
    {
        if ($this->dao == null) {
            $classe = str_replace("App\Proxies\__CG__\\", "", get_class($this));
            $classDAO = str_replace('\\Model\\', '\\Model\\Dao\\', $classe . 'Dao');
            $this->dao = new $classDAO($this);
        }
        return $this->dao;
    }

    public function beginTransaction(): void
    {
        $this->getDAO()->beginTransaction();
    }

    /**
     * @throws ConnectionException
     */
    public function commit(): void
    {
        $this->getDAO()->commit();
    }

    /**
     * @throws ConnectionException
     */
    public function rollback(): void
    {
        $this->getDAO()->rollback();
    }

    /**
     * Método que insere um novo registro em uma tabela
     * @throws BusinessException
     * @throws Exception
     */
    public function inserir($validarSomenteLeitura = true, bool $considerarPermissoes = true): ?int
    {
        $permissao =  $this->getPermissao();
        if($considerarPermissoes && (!$permissao || !$permissao->getInserir())){
            throw new Exception("Usuário não tem permissão para cadastrar o registro.");
        }
        if (AppController::sistemaSomenteLeitura() && $validarSomenteLeitura) {
            throw new BusinessException("Sistema em modo somente leitura.");
        }
        if ($considerarPermissoes && UsuarioController::getUsuarioLogadoDoctrine() && TipoUsuario::VISITANTE == UsuarioController::getUsuarioLogadoDoctrine()->getTipo() && $validarSomenteLeitura) {
            throw new BusinessException("Usuário sem permissão.");
        }
        $id = $this->getDAO()->inserir($this, $validarSomenteLeitura);
        if(empty($_SESSION["desabilita_log"])){
            Log::registrarLog(TipoLog::ACTION_INSERT, $this->getTableName(), "Registro criado", null, null, $this->imprimir());
        }
        return $id;
    }

    /**
     * Método que lista todos os registros de uma tabela
     * @return $this[]
     * @throws ORMException
     */
    public function listar()
    {
        return $this->getDAO()->listar();
    }

    public function listarAtivos()
    {
        return $this->getDAO()->listarAtivos();
    }

    /**
     * Retorna a quantidade de registros
     * @return int
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function contar()
    {
        return $this->getDAO()->contar();
    }

    /**
     * Método que lista todos os registros de uma tabela
     * @param $campos
     * @param null $ordem
     * @return array
     * @throws ORMException
     * @throws \Doctrine\DBAL\Exception
     * @throws \Doctrine\ORM\Exception\ORMException
     */
    public function listarPorCampos($campos, $ordem = null): ?array
    {
        return $this->getDAO()->listarPorCampos($campos, $ordem);
    }

    /**
     * Método que atualiza um registro banco de dados
     * @param bool $validarSomenteLeitura
     * @throws BusinessException
     * @throws SecurityException
     * @throws Exception
     */
    public function atualizar(bool $validarSomenteLeitura = true, bool $considerarPermissoes = true)
    {
        if($considerarPermissoes && AppController::sistemaSomenteLeitura() && $validarSomenteLeitura) {
            throw new BusinessException("Sistema em modo somente leitura.");
        }
        if(UsuarioController::getUsuarioLogadoDoctrine() && TipoUsuario::VISITANTE == UsuarioController::getUsuarioLogadoDoctrine()->getTipo() && $validarSomenteLeitura) {
            throw new BusinessException("Usuário sem permissão.");
        }
        if($considerarPermissoes){
            $permissao =  $this->getPermissao();
            if(!$permissao || !$permissao->getEditar()){
                throw new SecurityException("Usuário não tem permissão para editar o registro de {$this->getTableName()}.");
            }
        }
        
        $antigo = $this->buscar($this->getId());
        // Log::registrarLog(TipoLog::ACTION_UPDATE, $this->getTableName(), "Registro atualizado", null, $antigo->imprimir(), $this->imprimir());
        $this->getDAO()->merge();
    }

    /**
     * @throws BusinessException
     * @throws Exception
     */
    public function remover(?int $id = null)
    {
        if(AppController::sistemaSomenteLeitura()) {
            throw new BusinessException("Sistema em modo somente leitura.");
        }
        if(UsuarioController::getUsuarioLogadoDoctrine() && TipoUsuario::VISITANTE == UsuarioController::getUsuarioLogadoDoctrine()->getTipo()) {
            throw new BusinessException("Usuário sem permissão.");
        }
        $permissao =  $this->getPermissao();
        if(!$permissao || !$permissao->getExcluir()){
            throw new Exception("Usuário não tem permissão para remover o registro.");
        }
        Log::registrarLog(TipoLog::ACTION_DELETE, $this->getTableName(), "Registro deletado", null, null, $this->imprimir());
        if (is_null($id)) {
            return $this->getDAO()->remover(null, $this);
        } else {
            return $this->getDAO()->remover($id);
        }
    }

    /**
     * Metodo que busca um registro no banco de dados
     * @param int $id
     * @return $this
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     */
    public function buscar($id)
    {
        return $this->getDAO()->buscar($id);
    }

	/**
	 * @param string[]|mixed $campos
	 * @param string[]|null $orderBy
	 */
    public function filtrarPorCampos($campos, $orderBy = null)
    {
        return $this->getDAO()->filtrarPorCampos($campos, $orderBy);
    }

    function getTableName()
    {
        return $this->getDAO()->getTableName();
    }


    // TODO: Revisar método.
    /** @noinspection PhpUnreachableStatementInspection
     * @noinspection PhpUnusedParameterInspection
     */
    private function getCollection($arrayCollection)
    {
        return "collection";
        $output = "<ul>";
        foreach ($arrayCollection as $objeto) {
            $output .= "<li>$objeto</li>";
        }
        $output .= "</ul>";
        return $output;
    }

    /**
     * @param $atributo
     * @return string|null
     */
    private function imprimirAtributo($atributo)
    {
        $getMethod = 'get' . ucfirst($atributo);
        if(!method_exists($this, $getMethod)){
            return null;
        }
        $valor = $this->$getMethod();
        if ($atributo != 'dao') {
            $valor = $valor instanceof DateTime ? $valor->format('d/m/Y') : ($valor instanceof PersistentCollection || is_array($valor) ? $this->getCollection($valor) : $valor);
            return "$atributo = $valor \n";
        }        
        return null;
    }

    function imprimir(): string
    {
        $output = "";
        $api = new ReflectionClass($this);
        foreach ($api->getDefaultProperties() as $atributo => $valor) {
            $output .= $this->imprimirAtributo($atributo);
        }
        return $output;
    }

    public function clear()
    {
        $this->getDAO()->clear();
    }

    function jsonSerialize()
    {
        return get_object_vars($this);
    }

    public function __toString()
    {
        return get_class($this);
    }

    /**
     * @return PermissaoEntidade|false
     */
    protected function getPermissao()
    {
        $usuario_logado = UsuarioController::getUsuarioLogadoDoctrine();
        $nameSpace = get_class($this);
        $arrayClass = explode("\\", $nameSpace);
        $nomeClass = $arrayClass[count($arrayClass)-1];
        $classesVerificar = array("Anexo");
        if($usuario_logado != null){
            if($usuario_logado->getTipo() == TipoUsuario::MASTER || !in_array($nomeClass, $classesVerificar)) {
                $permissao = new PermissaoEntidade();
                $permissao->setEditar(true);
                $permissao->setExcluir(true);
                $permissao->setInserir(true);
                return $permissao;
            }else if ($usuario_logado->getTipo() == TipoUsuario::INTERESSADO && in_array($nomeClass, array('Processo','Anexo'))){
                $permissao = new PermissaoEntidade();
                $permissao->setCodigoEntidade(PermissaoEntidade::getCodigo($nomeClass));
                $permissao->setEditar(true);
                $permissao->setExcluir(false);
                $permissao->setInserir(true);
                return $permissao;
            }else if (!in_array($usuario_logado->getTipo(),array(TipoUsuario::MASTER, TipoUsuario::INTERESSADO))) {
                return $usuario_logado->getPermissoesEntidade(PermissaoEntidade::getCodigo($nomeClass));
            }
        }else if($nomeClass == "Log" || $nomeClass == "Usuario"){
            $permissao = new PermissaoEntidade();
            $permissao->setEditar(true);
            $permissao->setInserir(true);
            return $permissao;
        }else if(isset($_SESSION["execucao_script"]) && $_SESSION["execucao_script"] == true){
            $permissao = new PermissaoEntidade();
            $permissao->setEditar(true);
            $permissao->setExcluir(true);
            $permissao->setInserir(true);
            return $permissao;
        }
        return false;
    }

    /**
     * @return int|mixed
     * @throws ORMException
     */
	public function buscarPorCampos($campos, $order = null) {
		return $this->getDAO()->buscarPorCampos($campos, $order);
	}

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     * @throws TransactionRequiredException
     * @throws BusinessException
     * @throws Exception
     */
    public function desativar($id)
    {
        if(AppController::sistemaSomenteLeitura()) {
            throw new BusinessException("Sistema em modo somente leitura.");
        }
        if(UsuarioController::getUsuarioLogadoDoctrine() && TipoUsuario::VISITANTE == UsuarioController::getUsuarioLogadoDoctrine()->getTipo()) {
            throw new BusinessException("Usuário sem permissão.");
        }
        $permissao =  $this->getPermissao();
        if(!$permissao || !$permissao->getExcluir()){
            throw new Exception("Usuário não tem permissão para remover o registro.");
        }
        Log::registrarLog(TipoLog::ACTION_DELETE, $this->getTableName(), "Registro desativado.", null, null, $this->imprimir());
        $this->getDAO()->desativar($id);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     * @throws TransactionRequiredException
     * @throws BusinessException
     * @throws Exception
     */
    public function reativar($id)
    {
        if(AppController::sistemaSomenteLeitura()) {
            throw new BusinessException("Sistema em modo somente leitura.");
        }
        if(UsuarioController::getUsuarioLogadoDoctrine() && TipoUsuario::VISITANTE == UsuarioController::getUsuarioLogadoDoctrine()->getTipo()) {
            throw new BusinessException("Usuário sem permissão.");
        }
        $permissao =  $this->getPermissao();
        if(!$permissao || !$permissao->getExcluir()){
            throw new Exception("Usuário não tem permissão para reativar o registro.");
        }
        Log::registrarLog(TipoLog::ACTION_UPDATE, $this->getTableName(), "Registro reativado.", null, null, $this->imprimir());
        $this->getDAO()->reativar($id);
    }
}