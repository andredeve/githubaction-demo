<?php

namespace App\Controller;

use App\Model\Setor;
use Core\Controller\AppController;
use Core\Util\Report;

/**
 * Classe SetorController
 * @version 1.0
 * @author Anderson Brandão Batistoti <anderson@lxtec.com.br>
 * @date   04/01/2018
 * @copyright (c) 2018, Lxtec Informática
 */
class SetorController extends AppController
{

    function __construct()
    {
        parent::__construct(get_class());
    }

    /**
     * Método para imprimir lista de usuário cadastrados
     */
    public function imprimir()
    {
//        require_once APP_PATH . 'lib/fpdf/fpdf.php';
        $report = new Report("Relatório de Setores cadastrados");
//        $report->AliasNbPages();
        $report->AddPage();
        $report->SetY($report->GetY() + 25, false);
        $report->SetFont("times", "B", "12");
        $report->Cell(0, 10, 'Relaçao de setores cadastrados no sistema:', 0, 1);
        $report->SetFont("times", "B", "9");
        $report->SetAligns(array('C', 'L', 'L', 'C', 'C', 'C'));
        $report->SetWidths(array(15, 75, 40, 10, 20, 30)); //Total 190 para Retrato
        $report->setTableHeader(array('Cód.', 'Nome', 'Sigla', 'Ativo', 'Dt.Cadastro', 'Última alteração'));
        $setor = new Setor();
        $fill = false;
        $report->SetFillColor(224, 235, 255);
        $report->SetFont("times", "", "8");
        foreach ($setor->listarSetoresPai() as $setor) {
            $this->imprimirSetor($report, $setor);
            foreach ($setor->getSetoresFilhos() as $setorFilho) {
                $this->imprimirSetor($report, $setorFilho, false);
            }
            $fill = !$fill;
        }
        $report->Ln(5);
        $report->Output();
    }

    private function imprimirSetor($report, $setor, $fill = false)
    {
        $ultimaAlteracao = $setor->getUltimaAlteracao();
        $report->Row(
            array(
                $setor->getId(),
                $setor->getNome(),
                $setor->getSigla(),
                $setor->getIsAtivo() ? 'Sim' : 'Não',
                $setor->getDataCadastro()->format('d/m/Y'),
                !empty($ultimaAlteracao) ? $ultimaAlteracao->format('d/m/Y H:i:s') : 'Não registrada'
            ), $fill, true
        );
        //$report->Rect($report->getX(), $yIni, 190, $report->getY() - $yIni);
    }

    function inserir()
    {
        $this->setSetor();
        return parent::inserir();
    }

    function atualizar()
    {
        $this->setSetor();
        return parent::atualizar();
    }

    function setSetor()
    {
        $setor_pai = filter_input(INPUT_POST, 'setor_pai_id', FILTER_SANITIZE_NUMBER_INT);
        
        $_POST['setorPai'] = !empty($setor_pai) ? (new Setor())->buscar($setor_pai) : '';
        $_POST['arquivar'] = isset($_POST['arquivar']) ? true : false;
        $_POST['disponivelTramite'] = isset($_POST['disponivel_tramite']) ? true : false;
    }

}
