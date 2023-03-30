<?php

include '../../../../bootstrap.php';
include './init.php';
while ($arquivo = $diretorio->read()) {
    if (is_file($dir . 'Model/' . $arquivo)) {
        $aux = explode('.', $arquivo);
        createDaoFile($aux[0]);
    }
}
echo "<br/>Arquivos Dao criados com sucesso!";
$diretorio->close();

function createDaoFile($class_name) {
    global $dir;
    $dir_dao = $dir . 'Model/Dao/';
    $class_dao = $class_name . "Dao";
    if (!is_file($dir_dao . $class_dao . '.php')) {
        $dao_file = fopen($dir_dao . $class_dao . '.php', 'w');
        $conteudo = '<?php 
            namespace App\Model\Dao;
    use Core\Model\AppDao;

    /**
     * Classe ' . $class_dao . '
     * @version 1.0
     * @author Anderson Brandão <batistoti@gmail.com>
     * 
     * @copyright 2016 Lxtec Informática LTDA
     */
    class ' . $class_dao . ' extends AppDao {

        function __construct($entidade) {
            parent::__construct($entidade);
        }

    }';

        fwrite($dao_file, $conteudo);
        fclose($dao_file);
    }
}
