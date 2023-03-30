<?php

error_reporting(E_ERROR);
ignore_user_abort(true);
set_time_limit(0);

require '../../../bootstrap.php';

use App\Util\Tesseract\TesseractOCR;

try {
    $source = APP_PATH . 'src/Tests/ocr/images/300DPI/image1.jpg';
    $destiny = APP_PATH . 'src/Tests/ocr/output';
    if (is_file($destiny . '.pdf')) {
        unlink($destiny . '.pdf');
    }
    $time_start = microtime(true);
    echo "<pre>";
    echo (new TesseractOCR($source))->lang('por')->outPutFile($destiny)->configFile('pdf')->run();
    $time_end = microtime(true);
    echo "<hr>";
    echo '<b>Tempo de execução:</b> ' . ($time_end - $time_start) . ' segundos';
    echo "</pre>";
} catch (Exception $ex) {
    echo $ex->getMessage();
}
