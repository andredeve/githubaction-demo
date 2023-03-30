<?php
set_time_limit(0);
ini_set("memory_limit", -1);



require 'bootstrap.php';

$assinaturaAtualizada = new \App\Model\Assinatura();
$assinaturaAtualizada = $assinaturaAtualizada->buscar(247);

ob_start();
echo __FILE__ . ' LINHA: ' . __LINE__;
echo '<pre>';
var_dump($assinaturaAtualizada->getLxsign_id());
echo '</pre>';
$print_log = ob_get_contents();
ob_clean();
echo $print_log;
die();
//$parser = new \Smalot\PdfParser\Parser();
//$pdf    = $parser->parseFile(APP_PATH.'documento_teste_A1.pdf'); //com keywords
//$details = $pdf->getDetails();
for($i = 0; $i < 100 ; $i++ ){
    sleep(5);
    $pid = pcntl_fork();
    if ($pid == -1) {
    //            die('Fork failed');
    }else if ($pid ==0 ){
        echo "\n i={$i} antes do sleep";
        sleep(100);
        echo "\n i={$i} depois do sleep";
    //    require 'bootstrap.php';
        $anexo =  new App\Model\Anexo();
        $anexo = $anexo->buscar(1118);
        
        echo "\n anexo_id = {$anexo->getId()}";
        exit(0);
    }
}



ob_start();
echo __FILE__ . ' LINHA: ' . __LINE__;
echo '<pre>';
var_dump("aqui");
echo '</pre>';
$print_log = ob_get_contents();
ob_clean();   
echo $print_log;
die();

//ob_start();
//echo __FILE__ . ' LINHA: ' . __LINE__;
//echo '<pre>';
//var_dump($anexo->getArquivo(false,false,true));
//echo '</pre>';
//$print_log = ob_get_contents();
//ob_clean();
//die($print_log);


//$texto = (new \Smalot\PdfParser\Parser())->parseFile($anexo->getArquivo(false,false,true))->getText();
$texto = (new \Core\Util\PdfParser\Parser())->lxParseFile(APP_PATH. "/documento_grande_precisa_converter.pdf");
ob_start();
echo __FILE__ . ' LINHA: ' . __LINE__;
echo '<pre>';
var_dump($texto);
echo '</pre>';
$print_log = ob_get_contents();
ob_clean();
echo $print_log;
die("terminou");

/**
 * CronJob que realiza o OCR de anexos pendentes
 * Executada a cada todo minuto.
 * Cron:  * * * * * php {APP_ROOT}/realizar_ocr.php
 */

//$anexos_pendentes = (new \App\Model\Anexo())->listarPorCampos(array('isOCRIniciado' => false, 'isDigitalizado' => true, 'isOCRFinalizado' => false), array('dataCadastro' => 'ASC'));
$anexos_pendentes = array((new App\Model\Anexo())->buscar(395781));
foreach ($anexos_pendentes as $anexo) {
    try {
        
        $anexo->convertToPdf();
        
        ob_start();
        echo __FILE__ . ' LINHA: ' . __LINE__;
        echo '<pre>';
        var_dump("Teste");
        echo '</pre>';
        $print_log = ob_get_contents();
        ob_clean();
        die();
        
        $anexo->realizarOCR();
        escreverLogOCR("OCR realizada com sucesso para #{$anexo->getId()} - $anexo");
    } catch (Exception $ex) {
        escreverLogOCR("Erro #{$anexo->getId()} : " . $ex->getMessage());
    }
}
/**
 * Escreve log no arquivo de que registra log de realização de OCR
 * @param $registro
 */
function escreverLogOCR($registro)
{
    $log_file = APP_PATH . '_log/ocr.log';
    if (is_file($log_file)) {
        $content = file_get_contents($log_file);
    } else {
        $content = "";
    }
    $horario = Date('d/m/y - H:i:s');
    file_put_contents($log_file, $content . "\n" . "Horário: $horario |  Registro: " . $registro);

}