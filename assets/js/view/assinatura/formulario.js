initSelect2();
var tipoDocumentoSelect = $('select[name="tipo_documento"]');
var grupoSelect = $('select[name="grupo"]');
var $signatariosSelect = $('select[name="signatario"]');
var empresaSelect = $('select[name="empresa"]');
var lxsignUrl = $('#lxsign_url').val();
var accessToken = $('#access_token').val();

function carregarTiposDocumentos(gruposIds) {
    $.post({
        type: 'POST',
        url: lxsignUrl + "GrupoSignatario/api/buscar-tipo-documentos?access_token=" + accessToken,
        data: {grupos_id: gruposIds},
        dataType: 'json',
        crossDomain: true,
        beforeSend: function(xhr){
            xhr.withCredentials = true;
        },
        success: function (data) {
            // Tipos de Documentos
            tipoDocumentoSelect.find('option').remove();
            data.tipos_documentos.forEach(function (item) {
                let option = $("<option value='" + item.id + "'>" + item.nome + "</option>");
                tipoDocumentoSelect.append(option);
            });
            // Empresas
            empresaSelect.find('option').remove();
            data.empresas.forEach(function (item) {
                let option = $("<option value='" + item.id + "'>" + item.nome + "</option>");
                empresaSelect.append(option);
            });
        }
    }).fail(function (data, msg, xhr) {
        console.log(data);
        console.error(xhr);
        showGrowMessage('error', 'Ocorreu uma falha ao carregar a relação de tipos de documentos.');
    });
}

grupoSelect.on('change', function () {
    let $select = $(this);
    let ids = $select.val();
    if (ids !== undefined && ids.length > 0) {
        carregarTiposDocumentos(ids);
        selecionarSignatarios($select.find(':selected'));
    }
});

function selecionarSignatarios(grupos) {
    let signatarios = [];
    for (let grupo of grupos) {
        let $grupo = $(grupo);
        signatarios = signatarios.concat($grupo.data('signatarios'));
    }
    $signatariosSelect.val(signatarios);
    $signatariosSelect.trigger('change');
}

$('.tipo_documento_assinatura').on('change', function (){
    let autoNumeric =  $('.tipo_documento_assinatura option:selected').attr("autonumeric");
    if(autoNumeric === 1){
        $('#numero_documento_assinatura').prop('disabled', true);
        $('#documento_auto_numeric').val(1);
        $('#assinatura-alerta').hide();
    }else{
        $('#numero_documento_assinatura').prop('disabled', false);
        $('#documento_auto_numeric').val(0);
        $('#assinatura-alerta').show();
    }
});