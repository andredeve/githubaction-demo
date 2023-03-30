/* Caminho da raiz da aplicação */
// noinspection JSUnresolvedVariable

if (app_path === undefined) {
    var app_path = $("#app_path").val(); // TODO: Refatorar para definir app_path como constante de nível mais alto.
}

$(document).ready(function () {
    $(".btn-mesclar-anexos").click(function () {
        let processo_id = $(this).attr('processo_id');
        showLoading();
        $.post(app_path + 'src/App/View/Anexo/mesclar.php', {processo_id: processo_id}, function (response) {
            createModal("anexoModal", "Mesclar Arquivos", response, 'modal-lg');
            initDatePicker();
            initAutoNumeric();
            initSelect2();
            
            $(".btn-converter-pdf").click(function (e){
                e.preventDefault();
                $.post(app_path + "Anexo/converter", {anexo: $(this).attr("anexo")}, function (response){
                    showGrowMessage(response.tipo, response.msg);
                }, 'json');
            });
            
            $(".btn-listar-anexos-converter").click(function (e){
                e.preventDefault();
                $.post(app_path + 'src/App/View/Anexo/listar_conversoes.php', {}, function (response){
                    showGrowMessage(response.tipo, response.msg);
                }, 'json');
            });
            
            $("#mesclarAnexosForm").validate({
                submitHandler: function (form) {
                    var l = Ladda.create(form.querySelector('.ladda-button'));
                    l.start();
                    $.post($(form).attr('action'), $(form).serialize(), function (response) {
                        showGrowMessage(response.tipo, response.msg);
                        if (response.tipo == 'success') {
                            atualizarAnexos(processo_id);
                            $(form).closest('.modal').modal('hide');
                        }
                    }, 'json').done(function () {
                        l.stop();
                    });
                }
            });
            
            $("#sortable1, #sortable2").sortable({
                connectWith: ".connectedSortable",
                items: "li:not(.unsortable)",
                update(event, ui) {
                    var data = $(this).sortable('toArray');
                    if (event.target.id == 'sortable2') {
                        
                        if ($("#anexo_manter_id").val() != data[0]) {
                            showLoading();
                            $.post(app_path + 'src/App/Ajax/Anexo/buscar.php', {anexo_id: data[0]}, function (anexo) {
                                var $form = $("#mesclarAnexosForm");
                                $form.find("select[name='tipo_documento_id']").val(anexo.tipo).trigger('change');
                                $form.find("#numero_doc").val(anexo.numero);
                                $form.find("#select_classificacao_documento").val(anexo.classificacao).trigger('change');
                                $form.find('#descricao_doc').val(anexo.descricao);
                                $form.find('#data_doc').datepicker('setDate', anexo.data);
                                $form.find('#valor_doc').autoNumeric('set', anexo.valor);
                                $form.find('#ementa').val(anexo.objeto);
                            }, 'json').done(function () {
                                hideLoading();
                            });
                        }
                        $("#anexo_manter_id").val(data[0]);
                        $("#anexos_mesclar").val(JSON.stringify(data));
                    }
                },
                receive: function (event, ui) {
                    var target_receive_id = event.target.id;
                    var $qtde_paginas = $('#mesclarAnexosForm').find("#paginas_doc");
                    var qtde_paginas = Number($(ui.item).attr('paginas'));
                    if (target_receive_id == 'sortable2') {
                        $qtde_paginas.val(Number($qtde_paginas.val()) + qtde_paginas);
                    } else {
                        $qtde_paginas.val(Number($qtde_paginas.val()) - qtde_paginas);
                    }
                }
            }).disableSelection();
        }).done(function () {
            hideLoading();
        });
    });
    
    $("body").on('change', "input[name='arquivo_processo']", function (){
       getQtdeDePaginasAnexo();
    }).on('click', '.btn-importar-anexo', function (e){
        e.preventDefault();
        showLoading();
        $.post(app_path + 'src/App/View/Anexo/importar.php', {processo_id: $("#processo_id").val()}, 
            function (response) {
                createModal("importarAnexoModal", "Importar Anexo", response, 'modal-lg');
                initSelect2FornecedorImportacao();
                    initInputMasks();
                var tabelaAnexoImportacao = $("#tabelaAnexoImportacao").DataTable();

                $("#formAnexoImportarPesquisa").validate({
                    submitHandler: function (form) {
                        showLoading();
                            
                        $.post($(form).attr("action"), $(form).serialize(), function (response){
                            tabelaAnexoImportacao.rows().remove().draw();
                            var codigoInseridos = [] ;
                            $("input[name='codigoImportacao[]']").each(function (){
                                codigoInseridos.push($(this).val());
                            });
                            
                            $.each(response, function  (key, item){
                                numero = `${item.numero}/${item.exercicio}`;
                                fornecedor = `${item.fornecedor.cpfCnpj} - ${item.fornecedor.nome}`;
                                secretaria = `${item.unidadeOrcamentaria.nome}`;
                                if(item.unidadeGestora.nome) {
                                    secretaria += `/${item.unidadeGestora.nome}`;
                                }
                                valor = item.valor?item.valor:0;
                                valor = decimalToReal(valor);
                                if($.inArray( "fiorilli"+item.id+"-"+$(form).find("select[name='documento']").val(), codigoInseridos) != -1){
                                    numero = `<del>${numero}</del>`;
                                    fornecedor = `<del>${fornecedor}</del>`; 
                                    secretaria = `<del>${secretaria}</del>`;
                                    valor = `<del>${valor}</del>`;
                                    checkbox = `<input type="hidden"  name="tipo_documento_${item.id}" value="${$(form).find("select[name='documento']").val()}">  <input type="checkbox" disabled="true" name="id_documento[]" value="${item.id}">`;
                                }else{
                                    checkbox = `<input type="hidden" name="tipo_documento_${item.id}" value="${$(form).find("select[name='documento']").val()}">  <input type="checkbox" name="id_documento[]" value="${item.id}">`;
                                }
                                tabelaAnexoImportacao.row.add( [
                                    checkbox,
                                    numero,
                                    secretaria,
                                    fornecedor,
                                    valor
                                ] ).draw();
                            });
                        }, "json").done(function (){
                            hideLoading();
                        });
                        
                    }
                });
                
                $("#formAnexoImportar").validate({
                    submitHandler: function (form) {
                        showLoading();
                        $.post($(form).attr("action"), $(form).serialize(), function (response){
                            showGrowMessage(response.tipo, response.msg);
                            if(response.tipo == "success"){
                                atualizarAnexos($(form).find("input[name='processo_id']").val());
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
    }).on('click', '.btn-cadastrar-anexo', function (e) {
        e.preventDefault();
        var tipo = $(this).attr('tipo');
        var processo_id = $("#processo_id").val();
        showLoading();
        $.post(app_path + 'src/App/View/Anexo/cadastrar.php', {
            processo_id: processo_id,
            tipo: tipo
        }, function (response) {
            createModal("anexoModal", "Novo Anexo", response, 'modal-lg');
            initDatePicker();
            initAutoNumeric();
            initSelect2();
            initValidateFormAnexo();
            initUploadAnexoProcesso();
            initChangeActionTipo();
        }).done(function () {
            hideLoading();
        });
    }).on('click', '.convertendo', function (e){
        e.preventDefault();
        e.stopPropagation();
        showGrowMessage('error', $(this).attr('title') );
        return;
    }).on('click', '.btn-assinatura', function (e) {
        
        e.preventDefault();
    
        var anexo_id =$(this).attr('anexo_id');
        var indice = $(this).attr('indice');
        configurarEnvioAssinatura(anexo_id, indice);
    }).on('click', '.btn-editar-anexo', function (e) {
        e.preventDefault();
        var anexo_id = $(this).attr('anexo_id');
        var indice = $(this).attr('indice');
        var is_digitalizado = $(this).attr('is_digitalizado');
        var tipo = is_digitalizado ? 'digitalizar' : 'upload';
        showLoading();
        $.post(app_path + 'src/App/View/Anexo/editar.php', {
            anexo_id: anexo_id,
            indice: indice,
            tipo: tipo
        }, function (response) {
            createModal("anexoModal", "Editar Anexo", response, 'modal-lg');
            initDatePicker();
            initAutoNumeric();
            initSelect2();
            initValidateFormAnexo();
            initUploadAnexoProcesso();
            initChangeActionTipo();
            /**********************************/
            /***Última Alteração: 03/02/2023***/
            /*************André****************/
            /**********************************/
            let option = $("#selectModelo option:selected").text();
            let value = $("#selectModelo option:selected").val();
            if (option == 'Modelo Atual' && value == "AtualizarModeloTemp"){
                let texto = $("#texto_temp").data("texto");
                tinyMCE.activeEditor.setContent('');
                tinymce.activeEditor.execCommand('mceInsertContent', false, ` ${texto} `);       
            } 
        }).done(function () {
            hideLoading();
        });
    }).on('click', '.btn-notificar-usuarios', function (e) {
        e.preventDefault();
        var anexo_id = $(this).attr('anexo-id');
        var processo_id = $(this).attr('processo-id');
        var processo_num = $(this).attr('processo-num');
        alertar(anexo_id, processo_id, processo_num, false);
    });

    configurarAcaoExcluirAnexo();
});

function alertar(anexo_id, processo_id, processo_num, cancelar = false, show_btn_close= true) {
    showLoading();
    $.post(app_path + "src/App/View/Notificacao/cadastrar.php", {
        anexo_id: anexo_id,
        processo_id: processo_id,
        cancelar: cancelar? 1 : 0
    }, function (response) {
        let modal_id = "alertarUsuarioModal";
        let modal_title = (nomenclatura + " nº " + (processo_num ?? 'S/N'));
        let modal_content = response;
        let modal_size = 'modal-elg';
        createModal(modal_id, modal_title, modal_content, modal_size, show_btn_close);
        showLoading();
    }).done(function () {
        hideLoading();
    });
}

function configurarAcaoExcluirAnexo() {
    let btnDelete = $("#btn-delete-attach");
    let btn = $(".btn-excluir-anexo");
    btn.off('click');
    btn.on('click', function (e) {
        e.preventDefault();
        let anexo_id = $(this).data('anexo-id');
        let processo_id = $(this).data('processo-id');
        let indice = $(this).data('indice');
        let arquivo = $(this).data('nome-arquivo');
        let urlBase = $("#modal-delete-attach").data("app-url");
        let data = {indice: indice, arquivo: arquivo};
        let urlRemocao = urlBase + "anexo/excluir/id/" + anexo_id;
        btnDelete.data("anexo-id", anexo_id);
        requisitarRemocaoAnexo(urlRemocao, processo_id, data, function (statusCode, result, xhr) {
            if (statusCode === 424) {
                solicitarMotivo(processo_id, anexo_id, statusCode, urlBase, data);
            } else if (statusCode < 400) {
                showGrowMessage(result.tipo, result.msg);
                atualizarAnexos(processo_id);
                $("#motivoText").val(null);
            } else {
                mostrarMensagemFalhaAoRemoverAnexo(xhr, processo_id, anexo_id, statusCode, urlBase, data);
            }
        });
    });
}

function requisitarRemocaoAnexo(url, processo_id, data, onResult) {
    showLoading();
    let handleResult = function (data, statusMsg, xhr) {
        onResult(xhr.status, data, xhr);
    };
    $.post(url, data, handleResult, 'json')
        .fail(function (xhr) {
            handleResult(xhr.responseJSON, null, xhr);
        }).done(function () {
            hideLoading();
        });
}

function solicitarMotivo(processo_id, anexo_id, status, urlBase, data) {
    if (status === 424) {
        enviarRequisicaoRemocao(processo_id, anexo_id, urlBase, data);
    } else if (status === 403) {
        solicitarRemocao();
    } else {
        console.error("Caso não definido: " + status + ".");
    }

}

function enviarRequisicaoRemocao(processo_id, anexo_id, url_base, corpo) {
    let destino = url_base + "anexo/excluir/id/" + anexo_id;
    $("#modal-delete-attach").modal('show');
    $("#btn-delete-attach").off('click');
    $("#btn-delete-attach").on('click', function (e) {
        $("#modal-delete-attach").modal('hide');
        corpo.motivo = $("#motivoText").val();
        requisitarRemocaoAnexo(destino, processo_id, corpo, function (statusCode, result, xhr) {
            if (result.tipo === 'success') {
                atualizarAnexos(processo_id);
                $("#motivoText").val(null);
            }
            if (statusCode === 423) {
                mostrarMensagemFalhaAoRemoverAnexo(xhr, result.objeto, anexo_id, statusCode, url_base, result)
            } else {
                showGrowMessage(result.tipo, result.msg);
            }
        });
    });
}

function solicitarRemocao() {
    let btnDelete = $("#btn-delete-attach");
    let modal = $("#modal-delete-attach");
    btnDelete.val("Solicitar");
    modal.find(".modal-title").text("Solicitação de Remoção de Anexo");
    modal.modal('show');
    btnDelete.off('click');
    btnDelete.on('click', function (e) {
        showLoading();
        let modal = $("#modal-delete-attach");
        let anexo_id = $(this).data('anexo-id');
        let url_base = modal.data("app-url");
        modal.modal('hide');
        let motivo = $("#motivoText").val();
        let destino = url_base + "solicitacao/anexo/excluir/" + anexo_id;
        $.post(
            {
                url: destino,
                data: {motivo: motivo}
            },
            'json'
        ).done(function (result) {
            showGrowMessage("success", "Foi aberto uma solicitação para a remoção do anexo.");
        }).fail(function (xhr, textStatus, errorThrown) {
            showGrowMessage("error", "Ocorreu uma falha. Cód.: " + xhr.status);
        }).always(function (){
            hideLoading();
        });
    });
}

function mostrarMensagemFalhaAoRemoverAnexo(xhr, processo_id, anexo_id, status, urlBase, data) {
    console.error(xhr.responseJSON.msg ?? xhr.status);
    if (xhr.status === 403) { // Permissão recusada.
        showGrowMessage("error", xhr.responseJSON.msg, 600, 5000, "Abrir solicitação", function() { solicitarMotivo(processo_id, anexo_id, status, urlBase, data); });
    } else if (xhr.status === 409) { // Bloqueado por relacionamento interno.
        showGrowMessage("error", xhr.responseJSON.msg);
    } else if (xhr.status === 423) {
        console.error(xhr.responseJSON.msg);
        showGrowMessage("error", xhr.responseJSON.msg, 600, 15000, "Visualizar as tramitações", function() { $('.nav-tabs a[href="#historicoTramitesTab"]').tab('show'); });
    } else { // Outro caso ou inesperado.
        // noinspection JSUnresolvedVariable
        if (xhr.responseJSON !== undefined && xhr.responseJSON.msg !== undefined) {
            showGrowMessage("error", xhr.responseJSON.msg + " Código: " + xhr.status);
            console.error(msg);
        } else {
            showGrowMessage("error", "Não foi possível remover o anexo. Código: " + xhr.status);
        }
    }
}

function initChangeActionTipo(){
    $("#anexoModal").find('select[name="tipo_documento_id"]').change(function (){
        $.post(app_path + 'src/App/Ajax/TipoAnexo/get_altera_vencimento.php', {tipo: $(this).val()}, function (response){
            if(!response.altera_vencimento){
                $("#divNovoVencimentoProcesso").addClass("d-none");
            }else{
                $("#divNovoVencimentoProcesso").removeClass("d-none");
            }
        },"json");
    });
}

function configurarEnvioAssinatura(anexo_id, indice, esperarAssinatura ){
        showLoading();
        $.post(app_path + 'src/App/View/Assinatura/anexo.php', {
            anexo_id: anexo_id,
            anexo_indice: indice
        }, function (response) {
            createModal("assinaturaModal", "Assinatura(s)", response, 'modal-lg');
            initDatePicker();
            initSelect2();
            initAppEvents();
            var assinaturaForm = $("#assinaturaForm");
            assinaturaForm.validate({
                ignore: ":hidden, .select2-search__field, .ignore-validate",
                submitHandler: function (form) {
                    enviarParaAssinatura($(form, anexo_id, esperarAssinatura));
                }
            });
            assinaturaForm.on('click', '.btn-reenviar-assinatura', function (e){
                e.preventDefault();
                assinaturaForm.attr("action", $(this).attr("url"));
                assinaturaForm.attr("reenviar",true);
                assinaturaForm.submit();
            });
        }).fail(function (response){
            if (response.status === 400) {
                showGrowMessage('error', "Permitido envio para assinatura apenas de arquivos  PDF.");
            }
        }).done(function () {
            hideLoading();
        });
}

function enviarParaAssinatura(form, anexo_id, esperarAssinatura) {
    showLoading();
    let data = {
        anexo_id: form.find("input[name='anexo_id']").val(),
        processo_id: form.find("input[name='processo_id']").val(),
        anexo_indice: form.find("input[name='anexo_indice']").val(),
        ajax: true,
        assinatura_id: form.find("input[name='assinatura_id']").val(),
        grupo: form.find("select[name='grupo']").val(),
        signatarios: form.find("select[name='signatario']").val(),
        tipo_documento: form.find("select[name='tipo_documento']").val(),
        empresa: form.find("select[name='empresa']").val(),
        auto_numero_doc: form.find("input[name='auto_numero_doc']").is(':checked') ? 1 : 0,
        numero: form.find("input[name='numero']").val(),
        exercicio: form.find("input[name='exercicio']").val(),
        data_limite_assinatura: form.find("input[name='data_limite_assinatura']").val()
    }

    $.ajax({
        type: 'POST',
        url: form.attr("action"),
        data: data,
        success: function (response){
            showGrowMessage(response.tipo, response.msg);
            if(response.tipo === "success"){
                form.closest(".modal").modal("hide");
                if(esperarAssinatura){
                    window.open(app_path+"Assinatura/visualizarByAnexo/"+anexo_id, '_blank');
                    bootbox.dialog({
                        size: 'large',
                        animate: false,
                        message: 'Após assinar o documento clique em "Concluir" ou cancele caso não seja possível assinar agora. ',
                        title: "",
                        buttons: {
                            success: {
                                label: "Concluir",
                                className: "btn-primary",
                                callback: function () {
                                    let isConcluido = false;
                                    $.ajax({
                                        type: "POST",
                                        url: app_path+"src/App/Ajax/Anexo/status_assinatura.php",
                                        data: {anexo_id: anexo_id },
                                        success: function (resp){
                                            if(resp[0].status == "Finalizado"){
                                                isConcluido = true;
                                                atualizarAlertaDocumentosRequeridos();
                                            }else{
                                                showGrowMessage("warning", "O documento ainda não foi assinado. Caso não possa assinar agora clique em cancelar");
                                                isConcluido =false;
                                            }
                                        },
                                        async: false,
                                        dataType: 'json'
                                    });
                                    return isConcluido;
                                }
                            },
                            danger: {
                                label: "Cancelar",
                                className: "btn-default",
                                callback: function () {

                                }
                            }
                        }
                    });
                }else{
                    atualizarAnexos(data.processo_id);
                }
            }
            if (form.attr("reenviar")) {
                showGrowMessage('success', 'Documento reenviado para assinatura.');
                location.reload();
            }
        },
        error: function (xhr) {
            showGrowMessage('error', xhr.responseJSON.msg);
        },
        complete: function () {
            hideLoading();
        },
        dataType: 'json'
    });
}

function initUploadAnexoProcesso() {
    if ($("#arquivo_processo").length) {
        var preview = $("#arquivo_processo").attr('preview') != "" ? $("#arquivo_processo").attr('preview') : null;
        var $form = $("#arquivo_processo").closest('form');
        var processo_id = $form.find('input[type=hidden][name=processo_id]').val();
        var anexo_id = $form.find('input[type=hidden][name=id]').val();
        var indice = $form.find('input[type=hidden][name=indice]').val();
        if (preview != null) {
            $.post(app_path + 'src/App/Ajax/Anexo/get_preview_config.php', {
                processo_id: processo_id,
                anexo_id: anexo_id,
                indice: indice
            }, function (response) {
                var previews = $("#arquivo_processo").attr('preview') != "" ? $("#arquivo_processo").attr('preview').split(";") : null;
                var previewContent = previews != null ? previews : [];
                var previewConfig = response.preview_config;
                $("#arquivo_processo").fileinput({
                    showUpload: false,
                    showRemove: false,
                    showCancel: false,
                    theme: "fa",
                    overwriteInitial: true,
                    initialPreviewShowDelete: false,
                    language: 'pt-BR',
                    allowedFileExtensions: ["jpg", "png", "gif", "jpeg", "doc", "docx", "pdf"],
                    initialPreviewAsData: true, // identify if you are sending preview data only and not the raw markup
                    initialPreviewFileType: 'image', // image is the default and can be overridden in config below
                    initialPreview: previewContent,
                    initialPreviewConfig: previewConfig,
                    layoutTemplates: {
                        main1: "{preview}\n" +
                            "<div class=\'input-group {class}\'>\n" +
                            "   {caption}\n" +
                            "   <div class=\'input-group-btn\ input-group-prepend'>\n" +
                            "       {browse}\n" +
                            " <button type=\"button\" "+ ( $("#arquivo_processo").attr("disabled") == "disabled"? " disabled=\"disabled\" ":"") +" onclick=\"startScanner(" + (anexo_id == "" ? null : anexo_id) + "," + processo_id + ");\" class=\"btn btn-warning\"><i\n" +
                            "                    class=\"fa fa-picture-o\"></i> Digitalizar\n" +
                            "        </button>" +
                            "       {upload}\n" +
                            "       {remove}\n" +
                            "   </div>\n" +
                            "</div>",
                        actions: '<div class="file-actions">\n' +
                            '    <div class="file-footer-buttons">\n' +
                            '        {upload} {download}  {zoom} {other}' +
                            '    </div>\n' +
                            '    {drag}\n' +
                            '    <div class="clearfix"></div>\n' +
                            '</div>',        
                    }
                });
            }, 'json').done(function () {
                $("#arquivo_processo").fadeIn();
                hideLoading();
            });
        } else {
            
            $("#arquivo_processo").fileinput({
                showUpload: false,
                showRemove: false,
                showCancel: false,
                initialPreviewShowDelete: false,
                language: 'pt-BR',
                reversePreviewOrder: true,
                theme: "fa",
                'allowedFileExtensions': ['jpg', 'jpeg', 'pdf', 'doc', 'docx', 'png', 'gif'],
                layoutTemplates: {
                    main1: "{preview}\n" +
                        "<div class=\'input-group {class}\'>\n" +
                        "   {caption}\n" +
                        "   <div class=\'input-group-btn\ input-group-prepend'>\n" +
                        "       {browse}\n" +
                        " <button type=\"button\" "+ ( $("#arquivo_processo").attr("disabled") == "disabled"? " disabled=\"disabled\" ":"") +" onclick=\"startScanner(" + (anexo_id == "" ? null : anexo_id) + "," + processo_id + ");\" class=\"btn btn-warning\"><i\n" +
                        "                    class=\"fa fa-picture-o\"></i> Digitalizar\n" +
                        "        </button>" +
                        "       {upload}\n" +
                        "       {remove}\n" +
                        "   </div>\n" +
                        "</div>",
                    actions: '<div class="file-actions">\n' +
                        '    <div class="file-footer-buttons">\n' +
                        '        {upload} {download}  {zoom} {other}' +
                        '    </div>\n' +
                        '    {drag}\n' +
                        '    <div class="clearfix"></div>\n' +
                        '</div>',    
                }
            });
        }
    }
}

function getQtdeDePaginasAnexo(){
    var input = document.getElementsByName('arquivo_processo');
    var reader = new FileReader();
    if(input[0].files.length > 0){
        reader.readAsBinaryString(input[0].files[0]);
    }else{
        var xhr = new XMLHttpRequest();
        xhr.open('GET', input[0].getAttribute('preview'), true);
        xhr.responseType = 'blob';

        xhr.onload = function(e) {
          if (this['status'] == 200) {          
            var blob = new Blob([this['response']], {type: 'application/pdf'});
            reader.readAsBinaryString(blob);
          }
        };

        xhr.send();
              
    }
    reader.onloadend = function(){
        var matches=reader.result.match(/\/Type[\s]*\/Page[^s]/g);
        var count = matches!=null?matches.length:0;
        $("#paginas_doc").val(count);
    };
}

function initValidateFormFromDocumentoRequerido($select, entidade){
    $("#formAnexo").validate({
        ignore: ":hidden, .select2-search__field, .ignore-validate",
        submitHandler: function (form) {
            var tipo = $(form).find("#tipo_upload").val();
            var l = Ladda.create(form.querySelector('.ladda-button'));
            var processo_id = $(form).find('input[type=hidden][name=processo_id]').val();
            var anexo_id = $(form).find('input[type=hidden][name=id]').val();
            var indice = $(form).find('input[type=hidden][name=indice]').val();
            $(form).ajaxSubmit({
                beforeSubmit: function () {
                    if ($("#is_anexo_selecionado").val() == 1)
                        $('.progress').removeClass('hidden');
                    l.start();
                },
                uploadProgress: function (event, position, total, percentComplete) {
                    updateProgressBar($(form).find('.progress'), percentComplete);
                },
                dataType: 'json',
                success: function (response) {
                    showGrowMessage(response.tipo, response.msg);
                    if (response.tipo == 'success') {
                        $.post(app_path + 'src/App/Ajax/Anexo/atualizar_caixa_selecao.php', {
                            entidade: entidade,
                            objeto_id: response.anexo_id,
                            valores_selecionados: $select.val()
                        }, function (response) {
                            $select.html(response);
                            $select.change();
                        });
                        $(form).closest('.modal').modal('hide');
                    } else {
                        $('.progress').addClass('hidden');
                        updateProgressBar($(form).find('.progress'), 0);
                    }
                    l.stop();
                }
            });
        }

    });
}

function initValidateFormAnexoCompletarRequisitos() {
    jQuery.validator.addMethod("docNumberPattern", function(value, element) {
        if(this.optional( element ) || /^[0-9]+(\/\d{4})?$/g.test(value)){
            var arrayNumeroAno = value.split("/");
            return arrayNumeroAno[0] <= 2147483647;
        }
        return false;
    }, 'Informe o número ou número/ano.');
    
    $("#formAnexo").validate({
        rules: {
            numero_doc: {
                docNumberPattern: true
            }
        },
        ignore: ":hidden, .select2-search__field, .ignore-validate",
        submitHandler: function (form) {
            var tipo = $(form).find("#tipo_upload").val();
            var l = Ladda.create(form.querySelector('.ladda-button'));
            var processo_id = $(form).find('input[type=hidden][name=processo_id]').val();
            var anexo_id = $(form).find('input[type=hidden][name=id]').val();
            var indice = $(form).find('input[type=hidden][name=indice]').val();
            $(form).ajaxSubmit({
                beforeSubmit: function () {
                    if ($("#is_anexo_selecionado").val() == 1)
                        $('.progress').removeClass('hidden');
                    l.start();
                },
                uploadProgress: function (event, position, total, percentComplete) {
                    updateProgressBar($(form).find('.progress'), percentComplete);
                },
                dataType: 'json',
                success: function (response) {
                    showGrowMessage(response.tipo, response.msg);
                    if (response.tipo === 'success') {
                        let usuarioEhInteressado = $('#usuarioEhInteressado').val() == 1;
                        if(usuarioEhInteressado) {
                            atualizarAlertaDocumentosRequeridos();
                        }else{

                            bootbox.dialog({
                                size: 'small',
                                animate: false,
                                message: "Deseja enviar para assinatura?",
                                title: "",
                                buttons: {
                                    success: {
                                        label: "Sim",
                                        className: "btn-primary",
                                        callback: function () {
                                            atualizarAlertaDocumentosRequeridos();
                                            configurarEnvioAssinatura(anexo_id,indice, true);
                                        }
                                    },
                                    danger: {
                                        label: "Não",
                                        className: "btn-default",
                                        callback: function () {
                                            atualizarAlertaDocumentosRequeridos();
                                        }
                                    }
                                }
                            });
                        }
                        $(form).closest('.modal').modal('hide');
                        atualizarAnexos(processo_id);
                    } else {
                        $('.progress').addClass('hidden');
                        updateProgressBar($(form).find('.progress'), 0);
                    }
                    l.stop();
                }
            });
        }

    });
}

function initValidateFormAnexo() {
    jQuery.validator.addMethod("docNumberPattern", function(value, element) {
        if(this.optional( element ) || /^[0-9]+(\/\d{4})?$/g.test(value)){
            var arrayNumeroAno = value.split("/");
            return arrayNumeroAno[0] <= 2147483647;
        }
        return false;
    }, 'Informe o número ou número/ano.');
    
    $("#formAnexo").validate({
        rules: {
            numero_doc: {
                docNumberPattern: true
            }
        },
        ignore: ":hidden, .select2-search__field, .ignore-validate",
        submitHandler: function (form) {
            isCreatedByBlackList(ajaxSalvarAnexo, form);
        }
    });
    function isCreatedByBlackList(funcAjaxSalvarAnexo, form){
        let viltualUrl = getAnexoVirtualUrl();
        if(!viltualUrl){
            ajaxSalvarAnexo(form);
            return;
        }
        if ($("input[name='arquivo_processo']").val().split('.').pop().toLowerCase() !== 'pdf') {
            funcAjaxSalvarAnexo(form);
            return;
        }
        readPdfMetadata(viltualUrl).then( (pdfMetaData) => {
            if( pdfMetaData.getProducer() == 'iLovePDF'){
                bootbox.dialog({
                    size: 'large',
                    animate: false,
                    message: 'Esse PDF foi criado pelo '+pdfMetaData.getProducer()+' e anexos criados por esse aplicativo pode gerar erros no sistema. Recomendamos abrir o arquivo pelo Chrome e imprimir-lo em PDF. ',
                    title: "",
                    buttons: {
                        success: {
                            label: "Continuar mesmo assim",
                            className: "btn-primary",
                            callback: function () {
                                funcAjaxSalvarAnexo(form);
                            }
                        },
                        danger: {
                            label: "Cancelar",
                            className: "btn-default",
                            callback: function () {

                            }
                        }
                    }
                });
            }else{
                ajaxSalvarAnexo(form);
            }
        });
    }
    function getAnexoVirtualUrl(){
        var input = document.getElementsByName('arquivo_processo');
        if(!input[0].files[0]){
            return false;
        }
        return window.URL.createObjectURL(input[0].files[0]); 
    }
    function ajaxSalvarAnexo(form){
        var l = Ladda.create(form.querySelector('.ladda-button'));
        var tipo = $(form).find("#tipo_upload").val();
        var processo_id = $(form).find('input[type=hidden][name=processo_id]').val();
        var anexo_id = $(form).find('input[type=hidden][name=id]').val();
        var indice = $(form).find('input[type=hidden][name=indice]').val();
        $(form).ajaxSubmit({
            beforeSubmit: function () {
                if ($("#is_anexo_selecionado").val() == 1)
                    $('.progress').removeClass('hidden');
                l.start();
            },
            uploadProgress: function (event, position, total, percentComplete) {
                updateProgressBar($(form).find('.progress'), percentComplete);
            },
            dataType: 'json',
            success: function (response) {
                showGrowMessage(response.tipo, response.msg);
                if (response.tipo == 'success') {
                    $(form).closest('.modal').modal('hide');
                    atualizarAnexos(processo_id);
                } else {
                    $('.progress').addClass('hidden');
                    updateProgressBar($(form).find('.progress'), 0);
                }
                l.stop();
            }
        });
    }

    $("#formAnexoEdicao").validate({
        ignore: ":hidden, .select2-search__field, .ignore-validate",
        submitHandler: function (form) {
            let ladda = Ladda.create(form.querySelector('.ladda-button'));
            $(form).ajaxSubmit({
                beforeSubmit: function () {
                    if ($("#is_anexo_selecionado").val() === 1)
                        $('.progress').removeClass('hidden');
                    ladda.start();
                },
                uploadProgress: function (event, position, total, percentComplete) {
                    updateProgressBar($(form).find('.progress'), percentComplete);
                },
                dataType: 'json',
                success: function (response) {
                    showGrowMessage(response.tipo, response.msg);
                    if (response.tipo === 'success') {
                        $(form).closest('.modal').modal('hide');
                    } else {
                        $('.progress').addClass('hidden');
                        updateProgressBar($(form).find('.progress'), 0);
                    }
                    ladda.stop();
                }
            });
        }
    });
}

function atualizarAnexos(processo_id) {
    $.post(app_path + 'src/App/View/Anexo/listar.php', {processo_id: processo_id}, function (response) {
        $("#divAnexos").html(response);
        initDataTable();
    }).done(function () {
        setQtdeAnexosProcesso();
        configurarAcaoExcluirAnexo();
    });
}

function setQtdeAnexosProcesso() {
    var qtde_anexos = $('.linha-anexo').length;
    $("#qtde_anexos_processo").text(qtde_anexos);
    if (qtde_anexos == 0) {
        $("#linha_empty_anexo").show();
    } else {
        $("#linha_empty_anexo").hide();
    }
}

function startScanner(anexo_id, processo_id) {
    if ($("#permitir_digitalizacao").val() == 0) {
        showGrowMessage('error', 'Usuário sem configuração para realizar digitalização.');
        return false;
    }
    var wsImpl = window.WebSocket || window.MozWebSocket;
    window.ws = new wsImpl('ws://localhost:8181/');
    showLoading();
    var result = true;
    ws.onmessage = function (e) {
        console.log("Response: " + e);
    };
    ws.onopen = function () {
        console.log("Conexão Twain realizada com sucesso!");
        hideLoading();
        ws.send('1100');
        $.post(app_path + 'src/App/Ajax/Anexo/limpar_diretorio_digitalizacao.php', function () {
            var interval = 10000;  // 1000 = 1 second, 3000 = 3 seconds
            function verificarDigitalizacao(anexo_id, processo_id, interval) {
                $.post(app_path + 'src/App/Ajax/Anexo/get_arquivo_digitalizado.php', {
                    anexo_id: anexo_id,
                    processo_id: processo_id
                }, function (response) {
                    if (response.tipo != 'success' && $("#anexoModal").is(':visible')) {
                        setTimeout(verificarDigitalizacao(anexo_id, processo_id, interval), interval);
                    } else {
                        $.post(app_path + 'src/App/View/Anexo/fileinput.php', {
                            anexo_id: anexo_id,
                            processo_id: processo_id
                        }, function (response) {
                            $("#divArquivoProcesso").html(response);
                            initUploadAnexoProcesso();
                            getQtdeDePaginasAnexo();
                        });
                    }
                }, 'json');
            }

            setTimeout(verificarDigitalizacao(anexo_id, processo_id, interval), interval);
        });
    };
    ws.onerror = function () {
        if ($("#processoForm").length) {
            showGrowMessage('error', 'Não foi possível se conectar com o Scanner. Certifique-se que o aplicativo <a target="_blank" title="Clique para baixar" href="' + app_path + 'lib/LxScanWeb.rar">LxScan App</a> esteja instalado e iniciado, e que seu scanner esteja conectado a sua máquina.');
        }
        result = false;
        hideLoading();
    };
    ws.onclose = function () {
        console.log("Conexão com Scanner encerrada.");
    };
    return result;
}