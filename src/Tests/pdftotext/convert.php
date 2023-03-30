<?php
/**
 * Created by PhpStorm.
 * User: ander
 * Date: 20/12/2018
 * Time: 09:06
 */

error_reporting(E_ERROR);
ignore_user_abort(true);
set_time_limit(0);

require '../../../bootstrap.php';
echo (new \App\Util\Tesseract\TesseractOCR())->pdfToText(APP_PATH . 'src/Tests/pdftotext/teste.PDF');
//exec('sudo pdftotext teste.PDF teste.txt 2>&1', $output, $return);
//var_dump($output, $return);