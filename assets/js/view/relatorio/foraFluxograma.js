$(function () {
    $("#tabelaProcessoForaFluxograma").on('click', 'tbody tr td', function (e) {
        var processo_id = $(this).closest('tr').attr('id').split(":")[1];
        visualizarProcesso(processo_id);
    });
    
});
