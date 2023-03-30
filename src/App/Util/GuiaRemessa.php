<?php

namespace App\Util;

use App\Controller\IndexController;
use App\Enum\OrigemProcesso;
use App\Model\Estado;
use App\Model\Remessa;
use Core\Util\Report;
use const APP_PATH;

/**
 * Classe CapaProcesso
 * @version 1.0
 * @author Anderson Brandão Batistoti <anderson@lxtec.com.br>
 * @date   24/01/2018
 * @copyright (c) 2018, Lxtec Informática
 */
class GuiaRemessa extends Report
{

    private $guiaRemessa;
    private $isFinished;

    function __construct(Remessa $guiaRemessa, $orientation = 'P')
    {
        $this->guiaRemessa = $guiaRemessa;
        parent::__construct("Guia de Envio - Remessa nº. {$guiaRemessa->getNumero()}", $orientation);
    }

    function Header()
    {
        $this->SetY($this->GetY() + 10, false);
        $this->SetLineWidth(0.1);
        $cliente = IndexController::getClienteConfig();
        $this->SetFont('times', '', 12);
        $imagePath = APP_PATH . 'assets/img/brasao.png';
        $imageInfo = getimagesize($imagePath);
        $ratio = $imageInfo[0] / $imageInfo[1];
        $width = $ratio * 20;
        $this->Image($imagePath, 20, $this->GetY() + 2.5, $width, 20);
        $this->drawTextBox('', 190, 25);
        $this->Cell(35);
        $this->Cell(0, 7, 'ESTADO DE ' . strtoupper((new Estado())->buscarPorUF($cliente['estado'])), 0, 2, 'L');
        $this->SetFont('times', 'B', 12);
        $this->Cell(0, 5, mb_strtoupper($cliente['nome'], 'UTF-8'), 0, 2, 'L');
        $this->Cell(0, 5, "REMESSA NÚMERO {$this->guiaRemessa->getNumero(true)}", 0, 0, 'L');
        $this->Ln(15);
    }

    function Footer()
    {
        if ($this->isFinished) {
            $this->SetY(-100, false);
            $this->Rect($this->GetX(), $this->GetY(), 85, 20);
            $this->Rect(113, $this->GetY(), 85, 20);
            $this->Cell(85, 5, ' Emissor:', 0, 0);
            $this->Cell(20, 5, "", 0, 0);
            $this->Cell(85, 5, 'Receptor:', 0, 1);
            $this->Ln(5);
            $this->SetFont('times', 'B', 9);
            $this->Cell(2, 5, '', 0, 0);
            $this->Cell(80, 10, $this->guiaRemessa->getResponsavelOrigem(), 'T', 0);
            $this->Cell(20, 5, "", 0, 0);
            $this->Cell(2, 5, '', 0, 0);
            $this->Cell(80, 10, $this->guiaRemessa->getResponsavelDestino(), 'T', 1);
            $this->Ln(5);
            $this->Image(APP_PATH . 'assets/img/recorte.jpg', 8, $this->GetY(), 192);
            $this->Ln(7);
            $this->SetFont('times', 'B', 12);
            $this->Cell(0, 10, "CANHOTO REMESSA: {$this->guiaRemessa->getNumero(true)}", 0, 1);
            $this->SetFont('times', '', 9);
            $offset = 20;
            $this->mostrarLinha('Origem:', $this->guiaRemessa->getSetorOrigem(), false, true, false, $offset);
            $this->Ln(5);
            $this->mostrarLinha('Emissor:', $this->guiaRemessa->getResponsavelOrigem(), false, false, false, $offset);
            $this->Ln(5);
            $this->mostrarLinha('Destino:', $this->guiaRemessa->getSetorDestino(), false, false, false, $offset);
            $this->Ln(5);
            $this->mostrarLinha('Receptor:', $this->guiaRemessa->getResponsavelDestino(), false, false, false, $offset);
            $this->Ln(5);
            $this->mostrarLinha('Data/Hora:', $this->guiaRemessa->getData() . " - " . $this->guiaRemessa->getHora(), false, false, false, $offset);
            $this->Ln(10);
            $this->Cell(85, 5, 'Emissor:', 0, 0);
            $this->Cell(15, 5, "", 0, 0);
            $this->Cell(85, 5, 'Receptor:', 0, 1);
            $this->Ln(5);
            $this->SetFont('times', 'B', 9);
            $this->Cell(85, 10, $this->guiaRemessa->getResponsavelOrigem(), 'T', 0);
            $this->Cell(15, 5, "", 0, 0);
            $this->Cell(85, 10, $this->guiaRemessa->getResponsavelDestino(), 'T', 1);
        }
    }

    function gerar()
    {
        $this->isFinished = false;
        $font_size = 9;
        $height_bold = 7;
//        $this->AliasNbPages();
        $this->AddPage('P', 'A4');
        $y1 = $this->GetY() + 31;
        $x1 = $this->GetX();
        $this->SetAutoPageBreak(true, 100);
        $x2 = 92;
        $x_data = 174;
        //Retângulo Origem
        $this->Rect($x1, $y1, 80, 20);
        //Retângulo Emissor
        $this->Rect($x2, $y1, 80, 20);
        //Retângulo Data/hora
        $this->Rect($x_data, $y1, 26, 41);
        $y2 = 62;
        //Retângulo Destino
        $this->Rect($x1, $y2, 80, 20);
        //Retângulo Receptor
        $this->Rect($x2, $y2, 80, 20);
        $this->SetXY($x1, $y1);
        //Origem
        $this->SetFont('times', 'B', $font_size);
        $this->Cell(15, 5, 'Origem:', 0, 0);
        $this->SetFont('times', '', $font_size);
        $this->MultiCell(65, 5, filter_var($this->guiaRemessa->getSetorOrigem(), FILTER_SANITIZE_STRING), 0, 'L');
        //Emissor
        $this->SetXY($x2, $y1);
        $this->SetFont('times', 'B', $font_size);
        $this->Cell(15, 5, 'Emissor:', 0, 0);
        $this->SetFont('times', '', $font_size);
        $this->MultiCell(65, 5, filter_var($this->guiaRemessa->getResponsavelOrigem(), FILTER_SANITIZE_STRING), 0, 'L');
        //Destino
        $this->SetXY($x1, $y2);
        $this->SetFont('times', 'B', $font_size);
        $this->Cell(15, 5, 'Destino:', 0, 0);
        $this->SetFont('times', '', $font_size);
        $this->MultiCell(65, 5, filter_var($this->guiaRemessa->getSetorDestino(), FILTER_SANITIZE_STRING), 0, 'L');
        //Receptor
        $this->SetXY($x2, $y2);
        $this->SetFont('times', 'B', $font_size);
        $this->Cell(15, 5, 'Receptor:', 0, 0);
        $this->SetFont('times', '', $font_size);
        $this->MultiCell(65, 5, filter_var($this->guiaRemessa->getResponsavelDestino(), FILTER_SANITIZE_STRING), 0, 'L');
        //Data Hora
        $this->SetXY($x_data, $y1);
        $this->SetFont('times', 'B', $font_size);
        $this->Cell(26, 5, 'Data/Hora:', 0, 2, 'C');
        $this->SetFont('times', '', $font_size);
        $this->Cell(26, 10, $this->guiaRemessa->getData(), 0, 2, 'C');
        $this->Cell(26, 10, $this->guiaRemessa->getHora(), 0, 2, 'C');
        $this->Ln(20);
        $this->SetFont('times', 'B', $font_size);
        $this->Cell(20, 5, "Status:", 0, 0);
        $this->SetFont('times', '', $font_size);
        $this->Cell(0, 5, $this->guiaRemessa->getStatus(), 0, 1);
        $this->SetFont('times', 'B', $font_size);
        $this->Cell(20, 5, "Parecer:", 0, 0);
        $this->SetFont('times', '', $font_size);
        $this->MultiCell(0, 5, $this->guiaRemessa->getParecer(), 0);
        $this->Ln(5);
        $this->listarProcessos();
        $this->isFinished = true;
        $this->Output();
    }


    /**
     * Imprime o histórico
     */
    function listarProcessos()
    {
        $this->SetFont('times', 'B', 8);
        $header = array('Processo', 'Interessado', 'Assunto');
        $tam = count($header); //verifica quantas colunas tem a tabela
        //Soma 190
        $w = array(25, 80, 85);
        $a = array('L', 'L', 'L');
        $this->Cell(0, 1, "", "T", 1);
        for ($i = 0; $i < $tam; $i++) {
            $this->Cell($w[$i], 7, $header[$i], 1, 0, $a[$i]);
        }
        $this->Ln();
        $this->SetFont('times', '', 8);
        $this->SetWidths($w);
        $this->aligns = $a;
        $i = 0;
        $this->SetLineWidth(0.1);
        foreach ($this->guiaRemessa->getTramites() as $tramite) {
            $this->SetY($this->GetY() + 1, false);
            $processo = $tramite->getProcesso();
            $this->Row(array("{$processo->getNumero(true)}/{$processo->getExercicio()}", $processo->getInteressado(), $processo->getAssunto()));
            $i++;
        }
        // Fechando a  linha
        $this->Cell(array_sum($w), 0, '', 'T');
    }
}
