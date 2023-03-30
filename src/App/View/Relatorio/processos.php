<?php

$smarty->assign('page_title', 'Processos por período');
$smarty->assign('page_icon', 'fa fa-files-o');
$smarty->assign('page_description', 'Confira uma visão quantitativa de processos por período');
$processo = new App\Model\Processo();
$dataInicio = isset($_POST['periodo_ini']) ? $_POST['periodo_ini'] : '01/'.Date('m/Y');
$dataFim = isset($_POST['periodo_fim']) ? $_POST['periodo_fim'] : Date('d/m/Y');
$limite = isset($_POST['qtde_registros']) ? $_POST['qtde_registros'] : 10;
$smarty->assign('dataInicio', $dataInicio);
$smarty->assign('dataFim', $dataFim);
$smarty->assign('limite', $limite);
$smarty->assign('processosPorOrigem', $processo->listarQtdeAgrupada('origem', $dataInicio, $dataFim, $limite));
$smarty->assign('processosPorAssunto', $processo->listarQtdeAgrupada('assunto', $dataInicio, $dataFim, $limite));
$smarty->assign('processosPorResponsavel', $processo->listarQtdeAgrupada('usuarioAbertura', $dataInicio, $dataFim, $limite));
$smarty->assign('processos' ,$processo->listarProcessosPorDataAbertura($dataInicio, $dataFim));
