<?php

namespace Core\Util;

use Core\Controller\AppController;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;

/**
 * Classe EntityManager
 * Faz a conexão com o banco de dados
 * @author Anderson
 */
class EntityManagerConn
{
    private static $entityManager;

    /**
     * @throws Exception|ORMException
     * @throws \Doctrine\ORM\ORMException
     */
    public static function getEntityManager(): EntityManager
    {
        if (self::$entityManager == null) {
            $config = new Configuration();
            $config->addCustomStringFunction('remove_accents', 'App\Util\DoctrineExtensions\RemoveAccent');
            $config->addCustomStringFunction('group_concat', 'Oro\ORM\Query\AST\Functions\String\GroupConcat');
            $config->addCustomNumericFunction('hour', 'Oro\ORM\Query\AST\Functions\SimpleFunction');
            $config->addCustomDatetimeFunction('date', 'Oro\ORM\Query\AST\Functions\SimpleFunction');
            $driverImpl = $config->newDefaultAnnotationDriver(APP_PATH . "src/App/Model/");
            $config->setMetadataDriverImpl($driverImpl);
            $config->setProxyDir(CACHE_PATH . 'proxies/');
            $config->setProxyNamespace('App\Proxies');
            $config->setAutoGenerateProxyClasses(true);
            $em = EntityManager::create(self::getDataBaseParams(), $config);
            $em->getConnection()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
            self::$entityManager = $em;
        }
        if (!self::$entityManager->isOpen()) {
            self::$entityManager = self::$entityManager->create(
                self::$entityManager->getConnection(),
                self::$entityManager->getConfiguration()
            );
        }
        return self::$entityManager;
    }

    private static function getDataBaseParams(): array
    {
        // Arquivo de configuração do banco de dados
        $database = AppController::getDatabaseConfig();
        //Parâmetros para conexão
        return array(
            'host' => $database['db_host'],
            'driver' => $database['db_driver'],
            'port' => $database['db_port'],
            'user' => $database['db_user'],
            'password' => $database['db_password'],
            'dbname' => $database['db_name'],
        );
    }

}
