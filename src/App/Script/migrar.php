<?php /** @noinspection PhpUnhandledExceptionInspection */
require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'bootstrap.php';

use App\Log\Migration\MigracaoLogS1;
use Core\Controller\AppController;
use Core\Exception\TechnicalException;
use Core\Util\Migration\MigrationHelper;
use Core\Util\Migration\Step\MigracaoS1;

// MySQL
$conectarBD = function () {
    $database_config = AppController::getDatabaseConfig();
    echo "Conectando ao banco {$database_config['db_name']}... ";
    $con = new PDO(
        "mysql:host={$database_config['db_host']};port={$database_config['db_port']};dbname={$database_config['db_name']}",
        $database_config['db_user'], $database_config['db_password'],
        [PDO::ATTR_AUTOCOMMIT => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    echo "Concluído." . PHP_EOL;
    return $con;
};
$rollback = function (MigrationHelper $migracao) {
    try {
        $migracao->rollback();
    } catch (TechnicalException $e) {
        echo "Erro. Migração interrompida.";
        echo $e->getMessage() . PHP_EOL;
        echo $e->getTraceAsString() . PHP_EOL;
    }
};

try {
    $bd = $conectarBD();
    $migracoes = [new MigracaoS1($bd)];
} catch (Exception $e) {
    throw new TechnicalException("Não foi possível estabelecer uma conexão com o banco de dados: " . $e->getMessage() . PHP_EOL . $e->getTraceAsString());
}

echo PHP_EOL . "=================================================================================================" . PHP_EOL . PHP_EOL;
foreach ($migracoes as $key => $migracao) {
    echo "Iniciando migração " . ($key + 1) . "/" . count($migracoes) . "... " . PHP_EOL;
    try {
        $migracao->beginTransaction();
        $migracao->run();
        $migracao->commit();
        echo "Migração " . ($key + 1) . "/" . count($migracoes) . " concluída." . PHP_EOL. PHP_EOL;
        echo "=================================================================================================" . PHP_EOL . PHP_EOL;
    } catch (TechnicalException $e) {
        $rollback($migracao);
        echo "Erro fatal: " . $e->getMessage() . PHP_EOL;;
        break;
    } catch (Exception $e) {
        $rollback($migracao);
        echo "Falha na migração do banco de dados: " . $e->getMessage() . PHP_EOL;
        echo $e->getTraceAsString() . PHP_EOL;
        break;
    }
    if ($key === count($migracoes) - 1) {
        echo "Migração finalizada com sucesso." . PHP_EOL;
    }
}