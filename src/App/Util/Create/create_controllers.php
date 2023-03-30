<?php

include '../../../../bootstrap.php';
include './init.php';
while ($arquivo = $diretorio->read()) {
    if (is_file($dir . 'Model/' . $arquivo)) {
        $aux = explode('.', $arquivo);
        createControllerFile($aux[0]);
    }
}
echo "<br/>Arquivos Controllers criados com sucesso!";
$diretorio->close();

function createControllerFile($class_name) {
    global $dir;
    $dir_controller = $dir . 'Controller/';
    $class_controller = $class_name . "Controller";
    if (!is_file($dir_controller . $class_controller . '.php')) {
        $dao_file = fopen($dir_controller . $class_controller . '.php', 'w');
        $conteudo = '<?php 
            namespace App\Controller;
    use Core\Controller\AppController;

    /**
     * Classe ' . $class_controller . '
     * @version 1.0
     * @author Anderson Brandão <batistoti@gmail.com>
     * 
     * @copyright 2016 Lxtec Informática LTDA
     */
    class ' . $class_controller . ' extends AppController {

        function __construct() {
            parent::__construct(get_class());
        }

    }';

        fwrite($dao_file, $conteudo);
        fclose($dao_file);
    }
}
