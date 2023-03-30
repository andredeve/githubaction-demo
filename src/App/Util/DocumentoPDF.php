<?php
/**
 * Created by PhpStorm.
 * User: ander
 * Date: 13/12/2018
 * Time: 09:33
 */

namespace App\Util;


use App\Controller\IndexController;
use App\Model\Estado;
use FPDF;

if(!class_exists("FPDF")){
    require_once APP_PATH . 'lib/fpdf/fpdf.php';
}

class DocumentoPDF extends FPDF
{
    function Header()
    {
        $this->SetLineWidth(0.1);
        $cliente = IndexController::getClienteConfig();
        $this->SetFont('times', '', 12);
        $this->drawTextBox('', 190, 25);
        $this->Image(APP_PATH . 'assets/img/brasao.png', 20, 13, 20);
        $this->Cell(20);
        $this->Cell(0, 7, 'ESTADO DE ' . strtoupper((new Estado())->buscarPorUF($cliente['estado'])), 0, 2, 'C');
        $this->SetFont('times', 'B', 12);
        $this->Cell(0, 5, mb_strtoupper($cliente['nome']), 0, 0, 'C');
        $this->Ln(17);
    }
}