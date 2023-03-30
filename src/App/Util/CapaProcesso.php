<?php

namespace App\Util;

use App\Controller\IndexController;
use App\Enum\OrigemProcesso;
use App\Model\Estado;
use App\Model\Processo;
use Core\Controller\AppController;
use Core\Util\Functions;
use Core\Util\Report;
use const APP_PATH;

class CapaProcesso extends Report
{

    private $processo;
    private $nomenclatura;

    function __construct(Processo $processo, $orientation = 'P')
    {
        $this->processo = $processo;
        $this->nomenclatura = AppController::getParametosConfig()['nomenclatura'];
        parent::__construct("Capa " . $this->nomenclatura . $processo, $orientation);
    }

    function Header()
    {
        $this->SetLineWidth(0.1);
        $app = IndexController::getConfig();
        $cliente = IndexController::getClienteConfig();
        if(!isset($app['ocultar_header_capa_processo']) || $app['ocultar_header_capa_processo']!=1){
            $this->SetY($this->GetY() + 5, false);
            $this->SetFont('times', '', 12);
            $imagePath = APP_PATH . 'assets/img/brasao.png';
            $imageInfo = getimagesize($imagePath);
            $ratio = $imageInfo[0] / $imageInfo[1];
            $width = $ratio * 20;
            $this->Image($imagePath, 20, $this->GetY() + 2.5, $width, 20, '', '', '', false, 300);
            $this->drawTextBox('', 190, 25);
            $this->Cell(20);
            $estado = ((new Estado())->buscarPorUF($cliente['estado']));
            $estado = mb_convert_encoding($estado, 'Windows-1252', 'UTF-8');
            $this->Cell(0, 7, mb_strtoupper('ESTADO DE ' . $estado, 'UTF-8'), 0, 2, 'C');
            $this->SetFont('times', 'B', 12);
            $this->Cell(0, 5, mb_strtoupper($cliente['nome'], 'UTF-8'), 0, 0, 'C');
        }
        $this->Ln(17);
    }

    function gerar($dest = '', $name = '')
    {
        $this->SetTitle("Capa do {$this->nomenclatura}");
        $this->listarInfo();
        if (empty($name)) {
            $this->Output();
        } else {
            $this->Output($name, $dest);
        }
    }

    /**
     * Imprime informações básicas do Processo
     */
    private function listarInfo()
    {
        $this->AddPage();
        $this->SetFont('times', 'B', 12);
        $y_inicial = $this->GetY() + 25;
        $width_ini = 120;
        $this->SetY($y_inicial, false);
        $this->Cell($width_ini, 7, 'INTERESSADO', 'LTR', 2);
        $this->SetFont('times', '', 10);
        $this->MultiCell($width_ini, 5, Functions::tratarSaidaAjax($this->processo->getInteressado()), 'LBR', 'L');
        $y_interessado = $this->GetY();
        $this->SetXY($width_ini + 12, $y_inicial);
        $this->SetFont('times', 'B', 12);
        //$this->drawTextBox('', 65, 16);
        $this->Cell(0, 7, 'ORIGEM', 'LTR', 2, 'C');
        $this->SetFont('times', '', 10);
        $this->MultiCell(0, 5, mb_strtoupper(OrigemProcesso::getDescricao($this->processo->getOrigem()), 'UTF-8'), 'LBR', 'C');
        $this->SetXY($width_ini + 12, $this->GetY());
        $this->SetFont('times', 'B', 12);
        $this->Cell(0, 7, 'ANO', 'LR', 2, 'C');
        $this->SetFont('times', '', 12);
        $this->Cell(0, 5, Functions::tratarSaidaAjax($this->processo->getExercicio()), 'LRB', 1, 'C');
        $this->SetXY(10, $y_interessado);
        $this->SetFont('times', 'B', 12);
        $this->Cell($width_ini / 2, 7, 'Nº. ' . $this->nomenclatura, 'LR', 2);
        $this->SetFont('times', '', 12);
        $this->Cell($width_ini / 2, 5, Functions::tratarSaidaAjax($this->processo->getNumero(true)), 'LRB', 0, 'L');
        $y_ano = $this->GetY();
        $this->SetXY(70, $y_interessado);
        $this->SetFont('times', 'B', 12);
        $this->Cell($width_ini / 2, 7, 'DATA', 'LR', 2);
        $this->SetFont('times', '', 12);
        $this->Cell($width_ini / 2, 5, $this->processo->getDataAbertura()->format('d/m/Y'), 'LRB', 0, 'L');
        $this->SetY($y_ano, false);
        $this->Ln(10);
        $this->SetFont('times', 'B', 12);
        $this->Cell(0, 7, 'SETOR ORIGEM', 'LTR', 2);
        $this->SetFont('times', '', 10);
        $this->MultiCell(0, 5, $this->processo->getSetorOrigem(), 'LBR', 'L');
        $this->Ln(5);
        $this->SetFont('times', 'B', 12);
        $this->Cell(0, 7, 'ASSUNTO', 'LTR', 2);
        $this->SetFont('times', '', 10);
        $this->MultiCell(0, 5, $this->processo->getAssunto(), 'LBR', 'L');
        $this->Ln(5);
        $this->SetFont('times', 'B', 12);
        $this->Cell(0, 7, 'OBJETO', 'LTR', 2);
        $this->SetFont('times', '', 10);
        $this->MultiCell(0, 5, mb_strtoupper($this->processo->getObjeto(), 'UTF-8'), 'LBR', 'L');
        $this->Ln(5);
    }

    /**
     * Imprime os anexos do processo
     */
    function listarAnexos()
    {
        $this->SetFont('times', '', 12);
        $this->Cell(0, 7, 'ANEXOS DO PROCESSO', 'LTR', 2);
        $this->SetFont('times', '', 10);
        $anexos = $this->processo->getAnexos();
        $qtde_anexos = count($anexos);
        if ($qtde_anexos > 0) {
            $cont = 1;
            foreach ($anexos as $anexo) {
                $botom = $cont == $qtde_anexos ? 'B' : '';
                $this->MultiCell(0, 5, "{$anexo->getData()->format('d/m/Y')} - [{$anexo->getTipo()}] {$anexo->getDescricao()}", 'LR' . $botom, 'L');
                $cont++;
            }
        } else {
            $this->MultiCell(0, 5, "Nenhum anexo encontrado", 'LBR', 'L');
        }
        $this->Ln(5);
    }

    /**
     * Imprime o histórico de trâmites do Processo
     */
    function listarTramites()
    {
        $this->SetFont('times', 'B', 9);
        $this->Cell(0, 7, "MOVIMENTAÇÕES ASSOCIADAS", 1, 1, "C");
        $header = array('FASE', 'DATA', 'HORA', 'DESTINO');
        $tam = count($header); //verifica quantas colunas tem a tabela
        //Soma 190
        $w = array(15, 20, 15, 140);
        $a = array('C', 'C', 'C', 'L');
        for ($i = 0; $i < $tam; $i++) {
            $this->Cell($w[$i], 7, $header[$i], 1, 0, $a[$i]);
        }
        $this->Ln();
        $this->SetFont('times', '', 9);
        $this->SetWidths($w);
        $this->aligns = $a;
        $i = 0;
        $this->SetLineWidth(0.1);
        foreach ($this->processo->getTramites() as $tramite) {
            $this->Row(array($tramite->getNumeroFase(), $tramite->getDataEnvio(true), $tramite->getHoraEnvio(), $tramite->getSetorAtual()));
            $i++;
        }
        // Fechando a  linha
        $this->Cell(array_sum($w), 0, '', 'T');
    }
}
