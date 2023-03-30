// noinspection DuplicatedCode

$(function() {
    let grupoSelect = $('.grupo_assinatura');
    let lxsignUrl = $('#lxsign_url').val();
    let accessToken = $('#access_token').val();

    function carregarTiposDocumentos(gruposIds, campoId) {
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
                let tipoDocumentoSelect = $("#tipo_documento_campo_" + campoId);
                tipoDocumentoSelect.find('option').remove();
                data.tipos_documentos.forEach(function (item) {
                    let option = $("<option value='" + item.id + "'>" + item.nome + "</option>");
                    tipoDocumentoSelect.append(option);
                });
                // Empresas
                let empresaSelect = $("#empresa_campo_" + campoId);
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

    function selecionarSignatarios(grupos, campo_id) {
        let signatarios = [];
        for (let grupo of grupos) {
            let $grupo = $(grupo);
            signatarios = signatarios.concat($grupo.data('signatarios'));
        }
        let $signatariosSelect = $(`select[name='signatario_assinatura_campo_${campo_id}[]']`);
        $signatariosSelect.val(signatarios);
        $signatariosSelect.trigger('change');
    }

    grupoSelect.on('change', function () {
        let $select = $(this);
        let ids = $select.val();
        let campo_id = $select.data("campo-id");
        if (ids !== undefined && ids.length > 0) {
            carregarTiposDocumentos(ids, campo_id);
            selecionarSignatarios($select.find(':selected'), campo_id);
        }
    });

    $('body').off('click', '.btn-completar-requerimento').on('click', '.btn-completar-requerimento', function (e) {
        e.preventDefault();
        var processo_id = $(this).attr('processo-id');
        var anexo_id = $(this).attr('anexo-id');
        var entidade = "Anexo";
//        alert(anexo_id);
        
        showLoading();
        $.post(app_path + 'src/Core/Ajax/verificar_permissao.php', {
            entidade: entidade,
            acao: 'atualizar'
        }, 
        function (permissao) {
            if (permissao) {
                var modal_id = entidade + 'Modal';
                $.post(app_path + 'src/App/View/' + entidade+ '/editar.php', 
                    {processo_id:processo_id, anexo_id: anexo_id}, 
                    function (response) {
                        var form_id = $(response).closest('form').attr('id');
                        createModal(modal_id, null, response);
//                            initFormRules();
                        initDatePicker();
                        initAutoNumeric();
                        initSelect2();
                        initValidateFormAnexoCompletarRequisitos();
                        initUploadAnexoProcesso();
                        initChangeActionTipo();
                }).done(function () {
                    hideLoading();
                 });
            } else {
                hideLoading();
                showGrowMessage('warning', "Desculpe, mas você não permissão para executar essa ação.");
            }

        });
    });
    
    
    $('body').off('click', '.btn-editar-documento-requerido').on('click', '.btn-editar-documento-requerido', function () {
        abrirModalDocumentoRequerido($(this));
    });

    $('body').off('click', '.btn-documento-requerido').on('click', '.btn-documento-requerido', function (){
        abrirModalDocumentoRequerido($(this));
    });
});

function abrirModalDocumentoRequerido($element){
    if ($element.attr('tramite')){
        showLoading();
        let documento_requerido_id = $element.attr('documento-requerido');
        let pode_assinar = $element.data("pode-assinar");
        $.post(app_path + 'src/App/View/DocumentoRequerido/cadastrar.php', {
            tramite_id: $element.attr('tramite'),
            documento_requerido_id: documento_requerido_id
        }, function (response) {
            createModal("documentoRequeridoModal", "Requerer Documento", response, 'modal-lg');
            initSelect2();
            initAppEvents();
            if (pode_assinar === false) {
                $("#checkbox_assign").hide();
            }
            $("#documentoRequeridoModal").off('click', '.btn-cadastrar-anexo-requerido').on('click', '.btn-cadastrar-anexo-requerido', function (e){
                e.preventDefault();
                var tipo = $(this).attr('tipo');
                var processo_id = $(this).attr('processo-id');
                var aux = $(this).attr('id').split(':');
                var entidade = aux[1];
                var href_select = $(this).attr('href_select');
                var $div = $(this).closest('.form-group');
                var $select = href_select ? $div.find("#" + href_select) : $div.find('select');
                //Verifica se usuário tem permissão para realizar cadastro na entidade
                showLoading();
                $.post(app_path + 'src/Core/Ajax/verificar_permissao.php', {
                    entidade: entidade,
                    acao: 'inserir'
                }, function (permissao) {
                    if (permissao) {
                        var modal_id = aux[1] + 'Modal';
                        var modal_size = aux[2] ? aux[2] : '';
                        var $select = href_select ? $("#" + href_select) : $select_form;
                        $.post(app_path + 'src/App/View/' + aux[1] + '/cadastrar.php', {processo_id:processo_id}, function (response) {
                            var form_id = $(response).closest('form').attr('id');
                            createModal(modal_id, null, response, modal_size);
                            initDatePicker();
                            initAutoNumeric();
                            initSelect2();
                            initValidateFormFromDocumentoRequerido($select, entidade);
                            initUploadAnexoProcesso();
                            initChangeActionTipo();
                            $('#numero_doc').removeAttr('required');
                            $('#formAnexo'/* + form_id*/).validate({
                                submitHandler: function (form) {
                                    //showLoading();
                                    $.post($(form).attr('action'), $(form).serialize() + '&ajax=true', function (response) {
                                        if (response.tipo === 'success') {
                                            if ($select) {
                                                $.post(app_path + 'src/Core/Ajax/atualizar_caixa_selecao.php', {
                                                    entidade: entidade,
                                                    objeto_id: response.objeto_id,
                                                    valores_selecionados: $select.val()
                                                }, function (response) {
                                                    $select.html(response);
                                                    $select.change();
                                                });
                                            }
                                            $('#' + modal_id).modal('hide');
                                        }
                                        showGrowMessage(response.tipo, response.msg);
                                    }, 'json');
                                    return false;
                                }
                            });
                        }).done(function () {
                            hideLoading();
                        });
                    } else {
                        hideLoading();
                        showGrowMessage('warning', "Desculpe, mas você não permissão para executar essa ação.");
                    }
                });
            });
            $("#formDocumentoRequerido").validate({
                ignore: ":hidden, .select2-search__field, .ignore-validate",
                submitHandler: function (form) {
                    showLoading();
                    $.post($(form).attr("action"), $(form).serialize(), function (response){
                        showGrowMessage(response.tipo, response.msg);
                        if (response.tipo == "success"){
                            $.post(app_path + 'src/App/View/DocumentoRequerido/listar.php', {tramite_cadastro_id: $element.attr('tramite')}, function (response){
                                $('#tbodyDocumentosRequeridos').html(response);
                            });
                            $(form).closest(".modal").modal("hide");
                        }
                    }, "json").done(function (){
                        hideLoading();
                    });
                }
            });
        }).done(function () {
            hideLoading();
        });
    }
}

function atualizarAlertaDocumentosRequeridos(){
    showLoading();
    $.post(app_path+"src/App/View/Tramite/alert_documentos_requeridos.php", {tramite_id: $("#tramitarProcessoModal").find('input[name="tramite_id"]').val() }, function(response){
        $("#divDocumentosRequisitados").html(response);
        hideLoading();
    }).done(function (){
        hideLoading();
    });
}