var lxsignUrl = $('#lxsign_url').val();
var accessToken = $('#access_token').val();

$(function() {
    $('.grupo_assinatura').on('change', function () {
        carregarCampos($(this));
    });
    
    $('.fileinput-remove-button').click(function (){
        
        if($(this).closest('fieldset').attr('is_obrigatorio')){
            return;
        }
        $(this).closest('fieldset').find('input').each(function () {
            $(this).attr('required', false);
        });
        $(this).closest('fieldset').find('select').each(function () {
            $(this).prop('required', false);
        });
        $(this).closest('fieldset').find('label').each(function ()  {
            $(this).removeClass('required');
        });
    });
    
    $('.fileinput').change(function (){
        $(this).closest('fieldset').find('input').each(function () {
            $(this).attr('required', true);
        });
        $(this).closest('fieldset').find('select').each(function () {
            $(this).prop('required', true);
        });
        $(this).closest('fieldset').find('label').each(function ()  {
            $(this).addClass('required');
        });
    });
});

function carregarCampos($gruposSelect) {
    let id = $gruposSelect.attr('name').split('_')[3];
    id = id.replace("[]", "");
    let $grupos = $gruposSelect.find(':selected');
    selecionarSignatarios(id, $grupos);
    let count = $grupos.length;
    if (count > 0) {
        selecionarEmpresa(id, $($grupos[count - 1]));
    }
}

function selecionarSignatarios(campo_id, grupos) {
    let selector = "select[name='signatario_assinatura_campo_" + campo_id + "[]']";
    let $selectSignatarios = $(selector);
    let signatarios = [];
    for (let grupo of grupos) {
        let $grupo = $(grupo);
        signatarios = signatarios.concat($grupo.data('signatarios'));
    }
    $selectSignatarios.val(signatarios);
    $selectSignatarios.trigger('change');
}

function selecionarEmpresa(campo_id, $grupo) {
    let $selectEmpresa = $("select[name='empresa_campo_" + campo_id + "']");
    if ($selectEmpresa.find(":selected").length === 0) {
        let data = $grupo.data('empresa');
        if (data !== undefined) {
            $selectEmpresa.val(data);
            $selectEmpresa.trigger('change');
        }
    }
}