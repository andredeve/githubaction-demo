<?php
/**
 * Created by PhpStorm.
 * User: ander
 * Date: 20/02/2019
 * Time: 16:16
 */
include "../../../bootstrap.php";
$dir = APP_PATH . "src/Tests/pdfMerger/pdfs/";
$arquivo1 = 'C:\wamp64\www\LxProcessos/_files/processos/2019/teste_2/290/nota_fiscal/201902201631230000005c6db91b6c6d800000023311620022019.pdf';
$arquivo2 = 'C:\wamp64\www\LxProcessos/_files/processos/2019/teste_2/290/nota_fiscal/201902201644490000005c6dbc41c0a0500000049441620022019.pdf';
$arquivos = array($arquivo1, $arquivo2);
(new \App\Controller\AnexoController())->merge($arquivos, $arquivo1);