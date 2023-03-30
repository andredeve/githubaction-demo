function validarObrigatorio(element) {
    if($(element).is(":checked")) {
        $('#isObrigatorioSim').attr('checked', true).trigger("click");
    }
}

function radioIsObrigatorioNaoAlterado(element) {
    if($('#customCheckObrigatorioNumero').is(':checked')) {
        if ($(element).is(':checked')) {
            showGrowMessage('error', 'Desmarque a opção número de documento obrigatório.');
            $('#isObrigatorioSim').attr('checked', true).trigger('click');
        }
    } else if ($('#customCheckAssinaturaObrigatoria').is(':checked')) {
        if ($(element).is(':checked')) {
            showGrowMessage('error', 'Desmarque a opção assinatura digital obrigatória.');
            $('#isObrigatorioSim').attr('checked', true).trigger('click');
        }
    }
}