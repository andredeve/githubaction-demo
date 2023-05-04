var app_path = $("#app_path").val();
/* Variável que guardar se vou verificado a sessão do usuário logado */
var check_login = true;
var app_url = $("#app_path").val();
/**
 * Função que inicia JS necessários para LxOne
 * @returns {undefined}
 */
function initApp() {
    initAjaxSetup();
    initAppEvents();
    initDataTable();
    initFormRules();
    initValidates();
    initSuporteForm();
    initTooltip();
    initPlugins();
    $('#conteudo').fadeIn('slow');
}

async function readPdfMetadata(urlPdf) {
    const { PDFDocument } = PDFLib
    let pdfBytes = await fetch(urlPdf).then((res) => res.arrayBuffer());

    // Load the PDF document without updating its existing metadata
    let pdfDoc = await PDFDocument.load(pdfBytes, { 
        updateMetadata: false,
        ignoreEncryption: true 
    })
    return pdfDoc;
     
}

function initTooltip(){
    $('.tooltip-icon').hover(function () {
        $(this).popover('show');
    }, function () {
        $(this).popover('hide');
    });
}
function decimalToReal(valor) {
    //dava erro quando o valor tinha muitas casas decimais ex: 31177638.49000001
    valor = parseFloat(valor);
    valor = valor.toFixed(2);
    let tmp = valor + '';
    let neg = false;
    if (tmp.indexOf(".")) {
        tmp = tmp.replace(".", "");
    }

    if (tmp.indexOf("-") == 0) {
        neg = true;
        tmp = tmp.replace("-", "");
    }

    if (tmp.length == 1)
        tmp = "0" + tmp

    tmp = tmp.replace(/([0-9]{2})$/g, ",$1");

    if (tmp.length > 6)
        tmp = tmp.replace(/([0-9]{3}),([0-9]{2}$)/g, ".$1,$2");

    if (tmp.length > 9)
        tmp = tmp.replace(/([0-9]{3}).([0-9]{3}),([0-9]{2}$)/g, ".$1.$2,$3");

    if (tmp.length = 12)
        tmp = tmp.replace(/([0-9]{3}).([0-9]{3}).([0-9]{3}),([0-9]{2}$)/g, ".$1.$2.$3,$4");

    if (tmp.length > 12)
        tmp = tmp.replace(/([0-9]{3}).([0-9]{3}).([0-9]{3}).([0-9]{3}),([0-9]{2}$)/g, ".$1.$2.$3.$4,$5");

    if (tmp.indexOf(".") == 0)
        tmp = tmp.replace(".", "");
    if (tmp.indexOf(",") == 0)
        tmp = tmp.replace(",", "0,");

    return (neg ? '-' + tmp : tmp);
}
function customizeExportPDF(doc, title, widths) {
    if(doc.content[1].table.widths){
        if(widths){
            doc.content[1].table.widths = widths;
        }else{
            doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
        }
    }

    doc.content.splice(0, 1);
    var now = new Date();
    var jsDate = now.getDate() + '/' + (now.getMonth() + 1) + '/' + now.getFullYear();
    var logo = $("#base64_logo").val();
    var nome_cliente = $("#nome_cliente").val();
    var endereco_cliente = $("#endereco_cliente").val();
    var cnpj_cliente = $("#cnpj_cliente").val();
    var telefone_cliente = $("#telefone_cliente").val();
    doc.pageMargins = [15, 80, 20, 15];
    doc.defaultStyle.fontSize = 8;
    doc.styles.tableHeader.fontSize = 10;
    doc['header'] = (function () {
        return {
            columns: [
                {
                    image: logo,
                    width: 55,
                    height: 45
                }
                ,
                {
                    text: nome_cliente + '\n' + endereco_cliente + '\nCNPJ: ' + cnpj_cliente + ' / Telefone: ' + telefone_cliente,
                    fontSize: 15,
                    margin: [10, 0]
                },
                {
                    alignment: 'right',
                    text: title,
                    fontSize: 13,
                    margin: [10, 0]
                }
            ],
            margin: 30
        };
    });
    doc['footer'] = (function (page, pages) {
        return {
            columns: [
                {
                    alignment: 'left',
                    text: ['Gerado em: ', {text: jsDate.toString()}]
                },
                {
                    alignment: 'right',
                    text: ['página ', {text: page.toString()}, ' de ', {text: pages.toString()}]
                }
            ],
            margin: 20
        };
    });

    var objLayout = {};
    objLayout['hLineWidth'] = function (i) {
        return .5;
    };
    objLayout['vLineWidth'] = function (i) {
        return .5;
    };
    objLayout['hLineColor'] = function (i) {
        return '#aaa';
    };
    objLayout['vLineColor'] = function (i) {
        return '#aaa';
    };
    objLayout['paddingLeft'] = function (i) {
        return 4;
    };
    objLayout['paddingRight'] = function (i) {
        return 4;
    };
    doc.content[0].layout = objLayout;
}
function initAjaxSetup() {
    /* 
     * Define o tempo de espera para que seja verificado se o usuário está logado 
     * Padrão: 30 segundos
     * */
    setInterval(function () {
        check_login = true;
    }, 30000);
    $.ajaxSetup({
        beforeSend: function (xhr, settings) {
            let login_check_url = app_path + 'src/Core/Ajax/verificar_status_login.php';
            if (settings.url != login_check_url && check_login && !deveIgnorarLogin(settings.url)) {
                check_login = false;
                $.post(login_check_url, {location: (window.location.href).toLowerCase(), url_request: settings.url}, function (response) {
                    if (response == "0") {
                        hideLoading();
                        xhr.abort();
                        bootbox.alert({
                            message: "Ops! Sua sessão expirou. Por favor, realize o login novamente.",
                            animate: false,
                            callback: function () {
                                window.location.href = app_path + "login";
                            }
                        });
                    }
                });
            }
        },
        error: function (xhr, textStatus, errorThrown) {
            if ($('.ladda-spinner').length) {
                Ladda.stopAll();
            }
            hideLoading();
            if (textStatus === 'timeout') {
                showGrowMessage('warning', 'Tempo limite excedido: sua conexão parece estar lenta, tente novamente.');
            }
        }
    });
}

function deveIgnorarLogin(url) {
    let ignore = ["buscar_endereco.php", "buscar_cidades.php", "interessado/inserir", "escrever_log_javascript.php"];
    for (let i = 0; i < ignore.length; i++) {
        let result = url.includes(ignore[i]);
        if (result === true) {
            return true;
        }
    }
    return false;
}

function initSuporteForm() {
    $('#tipo_suporte').change(function () {
        if ($('#tipo_suporte').val() == 'erro-bug') {
            $('.alert-erro').css('display', 'block');
        } else {
            $('.alert-erro').css('display', 'none');
        }
    });
    $('#enviar_suporte').on('click', function () {
        var descricao = $.trim($('#descricao_suporte').val());
        if (descricao == '') {
            bootbox.alert("Informe o campo descrição");
            return false;
        }
        $("#formSuporte").submit();
    });
    $("#formSuporte").validate({
        rules: {
            tipo: {required: true},
            descricao: {required: true}
        },
        submitHandler: function (form) {
            var l = Ladda.create(form.querySelector('.ladda-button'));
            l.start();
            $('#formSuporte').ajaxSubmit({
                dataType: 'json',
                beforeSubmit: function () {
                    if ($("#is_anexo_selecionado").val() == 1) {
                        $("#formSuporte").find('.progress').removeClass('invisible');
                    }
                },
                uploadProgress: function (event, position, total, percentComplete) {
                    $("#formSuporte").find('.progress-bar').attr('aria-valuenow', percentComplete).css('width', percentComplete + '%');
                    $("#formSuporte").find('#porcentagem').html(percentComplete + '%');
                },
                success: function (response) {
                    showGrowMessage(response.tipo, response.msg);
                    $("#formSuporte").find('.progress-bar').attr('aria-valuenow', '0').css('width', '0%');
                    $("#formSuporte").find('#porcentagem').html('0%');
                    if (response.tipo == 'success') {
                        $(form).trigger('reset');
                        $("#formSuporte").find('.progress').addClass('invisible');
                        l.stop();
                        $('#suporteModal').modal('hide');
                    } else {
                        l.stop();
                    }
                }
            });
            return false;
        }
    });
}

/**
 * Função que inicia validações padrão
 * @returns {undefined}
 */
function initValidates() {
    /**
     * =================================================================================
     *  Sobrepondo padrões do Jquery Validate
     * =================================================================================
     */
    $.validator.setDefaults({
        ignore: ":hidden, .select2-search__field, .ignore-validate",
        highlight: function (element) {
            if ($(".tab-content").find("div.tab-pane.active:has(input.is-invalid)").length == 0) {
                $(".tab-content").find("div.tab-pane:hidden:has(input.is-invalid)").each(function (index, tab) {
                    var id = $(tab).attr("id");
                    $('a[href="#' + id + '"]').tab('show');
                });
            }
            $(element).addClass('is-invalid');
            $(element).removeClass('is-valid');
        },
        unhighlight: function (element) {
            $(element).removeClass('is-invalid');
            $(element).addClass('is-valid');
        },
        errorElement: 'small',
        errorClass: 'form-text text-danger',
        errorPlacement: function (error, element) {
            var input_name = $(element).attr('name');
            input_name = input_name.replace("[]","");
            if ($("#" + input_name + "-error").length) {
                $("#" + input_name + "-error").html(error);
            } else if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());
            } else if (element.parent().find('.select2-container').length) {
                error.insertAfter(element.parent().find('.select2-container'));
            } else {
                error.insertAfter(element);
            }
        }
    });
    $.validator.addMethod("dateBR", function (value, element) {
        value = value.replace('__/__/____', '');
        if (value == "")
            return true;
        if (value.length != 10)
            return false;
        // verificando data
        var data = value;
        var dia = data.substr(0, 2);
        var barra1 = data.substr(2, 1);
        var mes = data.substr(3, 2);
        var barra2 = data.substr(5, 1);
        var ano = data.substr(6, 4);
        if (data.length != 10 || barra1 != "/" || barra2 != "/" || isNaN(dia) || isNaN(mes) || isNaN(ano) || dia > 31 || mes > 12)
            return false;
        if ((mes == 4 || mes == 6 || mes == 9 || mes == 11) && dia == 31)
            return false;
        if (mes == 2 && (dia > 29 || (dia == 29 && ano % 4 != 0)))
            return false;
        if (ano < 1900)
            return false;
        return true;
    }, "*Informe uma data válida");  // Mensagem padrão
    $.each($.validator.methods, function (key, value) {
        $.validator.methods[key] = function () {
            if (arguments.length > 0) {
                arguments[0] = $.trim(arguments[0]);
            }

            return value.apply(this, arguments);
        };
    });
    $.validator.addClassRules('datepicker', {
        dateBR: true
    });
    /**
     * =================================================================================
     *  Jquery Validate para form padrão
     * =================================================================================
     */
    $('.form-validate').validate({
        ignore: ":hidden, .select2-search__field, .ignore-validate",
        submitHandler: function (form) {
            var l = Ladda.create(form.querySelector('.ladda-button'));
            l.start();
            form.submit();
        }
    });
    $('.form-validate-no-ignore-hidden').validate({
        ignore: ".select2-search__field, .ignore-validate",
        invalidHandler: function(e, validator){
            if(validator.errorList.length) {
                $('a[href="#' + jQuery(validator.errorList[0].element).closest(".tab-pane").attr('id') + '"]').tab('show');
            }
        },
        submitHandler: function (form) {
            var l = Ladda.create(form.querySelector('.ladda-button'));
            l.start();
            form.submit();
        }
    });
    $('.form-validate-rel').validate();
    
}

function initFileInput() {
    if ($(".fileinput").length) {
        /**
         * =================================================================================
         *  Bootstrap file input
         * =================================================================================
         */
        $(".fileinput").fileinput({
            language: 'pt-BR',
            showUploadedThumbs: false,
            showPreview: false,
            showUpload: false,
            theme: "fa"
        });
    }
}

/**
 * Função que inicia tudo que é necessário para formulários
 * @returns {undefined}
 */
function initFormRules() {
    /**
     * =================================================================================
     *  Plugin para campos data
     * =================================================================================
     * */
    initDatePicker();
    /**
     * =================================================================================
     *  Editor de Texto
     * =================================================================================
     * */
    initEditor();
    /**
     * =================================================================================
     *  Eventos genéricos para inputs
     * =================================================================================
     * */
    initInputs();
    /**
     * =================================================================================
     *  Máscara de entrada para inputs
     * =================================================================================
     * */
    initInputMasks();
    /**
     * =================================================================================
     *  FileInput
     * =================================================================================
     * */
    initFileInput();
    /**
     * =================================================================================
     *  Complexify -medidor de senha
     * =================================================================================
     * */
    //initComplexify();
    /**
     * =================================================================================
     *  AutoNumeric
     * =================================================================================
     * */
    initAutoNumeric();
    /**
     * =================================================================================
     *  AutoComplete
     * =================================================================================
     * */
    initAutoComplete();
    /**
     * =================================================================================
     *  Select2Tree
     * =================================================================================
     * */
    initSelect2Tree();
}

function initAutoComplete() {
    $('.autocomplete').each(function () {
        $(this).autocomplete({
            minLength: 2,
            source: function (request, response) {
                $.ajax({
                    url: app_path + "src/Core/Ajax/autocomplete.php",
                    type: 'post',
                    dataType: "json",
                    data: {
                        search: request.term
                    },
                    success: function (data) {
                        response(data);
                    }
                });
            },
            select: function (event, ui) {
                // Set selection
                /*$('#autocomplete').val(ui.item.label); // display the selected text
                $('#selectuser_id').val(ui.item.value); // save selected id to input*/
                return false;
            }
        });
    });
}

function escreverLogErro(mensagem) {
    $.post(app_path + 'src/Core/Ajax/escrever_log_javascript.php', {log: mensagem});
}

function initTabelaPesquisaSelecionarEntidade(entidade, $select) {
    var id_tabela = "#tabelaPesquisaSelecionar" + entidade;
    var $tabela = $(id_tabela);
    var sorter = $tabela.attr('sorter');
    var url = $tabela.attr('url');
    var cols_select = $tabela.attr('cols_select') ? $tabela.attr('cols_select').split(',') : [];
    var cols_hidden = $tabela.attr('cols_hidden') ? $tabela.attr('cols_hidden').split(',') : [];
    var linha_filtro = "<tr>";
    $(id_tabela + ' thead th').each(function () {
        var title = $(this).text();
        linha_filtro += '<th><input type="text" class="form-control form-control-sm select-filter" placeholder="Buscar ' + title + '" /></th>';
    });
    linha_filtro += "</tr>";
    $(id_tabela + ' thead').append(linha_filtro);
    $tabela.dataTable({
        "sDom": "<'row'<'col hidden'l><'col hidden'f>r>t<'row'<'col'i><'col'p>>",
        orderCellsTop: true,
        "processing": true,
        "serverSide": true,
        "bStateSave": false,
        "aaSorting": [[sorter, "asc"]],
        "ajax": url,
        "createdRow": function (tr, tdsContent) {
            $(tr).attr('title', 'Clique para selecionar').attr('id', tdsContent[0]);
        },
        "initComplete": function (settings, json) {
            $(this).fadeIn();
        }
    });
    cols_hidden.forEach(function (col) {
        $tabela.api().column(col).visible(false);
    });
    $(id_tabela + ' thead').on('keyup change', '.select-filter', function () {
        var index = $(this).parent().index();
        var $tr_rel = $(this).closest('thead').find('tr').eq(0).find('th').eq(index);
        var server_side = $tr_rel.attr('server_side');
        if (server_side !== typeof undefined) {
            var col_name = $tr_rel.attr('col_name');
            if (col_name !== typeof undefined) {
                $tabela.fnFilter($.trim($(this).val()), (cols_select[index]));
            } else {
                console.log("Atributo 'col_name' não foi encontrado para a coluna: " + $tr_rel.text());
            }
        } else {
            $tabela.api().column(index).search(this.value).draw();
        }
    });
    $(id_tabela + ' tbody').on('click', 'tr', function () {
        var id = this.id;
        var $tds = $(this).find('td');
        $(id_tabela + ' tbody').find('tr').each(function () {
            $(this).removeClass('table-primary');
        });
        $(this).toggleClass('table-primary');
        $(this).closest('form').find("#entidade_id").val(id);
        if($(id_tabela).attr("cols_descricao")){
            var descricao = '';
            var cols_descricao =  $tabela.attr('cols_descricao').split(',');
            cols_descricao.forEach(function (col) {
                descricao += descricao != ''? $tabela.attr('string_implode').split(','):"";
                descricao += $($tds[col]).text();
            });
            $(this).closest('form').find("#entidade_descricao").val(descricao);
        }else{
            $(this).closest('form').find("#entidade_descricao").val($($tds[1]).text());
        }
    });
    $("#formPesquisa" + entidade).validate({
        submitHandler: function (form) {
            var entidade_id = $(form).find('#entidade_id').val();
            var descricao_entidade = $(form).find('#entidade_descricao').val();
            if (entidade_id == "") {
                showAlert('Selecione um item  da lista.');
                return false;
            }
            var appends = ["Processo"]
            if($.inArray(entidade, appends) != -1){
                $select.append("<option value='" + entidade_id + "' selected>" + descricao_entidade + "</option>").trigger('change');
            }else{
                $select.html("<option value='" + entidade_id + "' selected>" + descricao_entidade + "</option>").trigger('change');
            }
            $(form).closest('.modal').modal('hide');
        }
    });
}
/**********************************/
/***Última Alteração: 07/02/2023***/
/*************André****************/
/**
 * Função que inicia eventos da aplicação
 * @returns {undefined}
 */
function initAppEvents() {
    //Select avançado
    $("body").on('click', '.btn-selectionar-entidade', function (e) {
        var $select = $(this).parent().parent().find('select');
        var entidade = $(this).attr('entidade');
        e.preventDefault();
        showLoading();
        $.post(app_path + 'src/App/View/' + entidade + '/selecionar.php', function (response) {
            createModal('pesquisaSelecionar' + entidade + 'Modal', 'Pesquisar', response, 'modal-lg');
            let apensadoId = $(".btn-selectionar-entidade").data("apensadoid");
            let processoId = $(".btn-selectionar-entidade").data("processoid");
            if (processoId === undefined) {
                processoId = "";
            }
            apensadoIdparametro = apensadoId === undefined ? '' : '&apensadoId=' + apensadoId
            var id_tabela = "#tabelaPesquisaSelecionar" + entidade;       
            let tableUrl = $(id_tabela).attr('url');
            $(id_tabela).attr('url', tableUrl + '&' + 'processoId=' + processoId + apensadoIdparametro);  
            initTabelaPesquisaSelecionarEntidade(entidade, $select);
        }).done(function () {
            hideLoading();
        });
    }).on("click", ".btn-limpar-filtros", function () {
        var $tabela = $(this).closest('.card-body').find('table.dataTable');
        $(this).closest('tr').find('input,select').each(function () {
            $(this).val("").trigger('change');
        });
        $tabela.dataTable().fnResetAllFilters();
    }).on('click', '.btn-detalhar', function (e) {
        e.preventDefault();
        var entidade = $(this).attr('entidade');
        var entidade_id = $(this).closest('tr').attr(entidade + '_id');
        $(this).toggleClass('text-success text-danger');
        $(this).find('i').toggleClass('fa-plus-circle fa-minus-circle');
        $("#detalhes-" + entidade + "-" + entidade_id).toggleClass('show hidden');
    });
    $("#cor_faixa").change(function () {
        $("#cabecalho").css('border-bottom-color', $(this).val());
    });
    $("#select_tema_navbar").change(function () {
        $("#menu").toggleClass('navbar-light navbar-dark');
    });
    var sel = $("#select_tema");
    sel.data("prev", sel.val());
    sel.change(function (data) {
        $('body').hide();
        var tema = $(this);
        if (tema.data("prev") === 'default') {
            $('link[href="' + app_path + 'vendor/twbs/bootstrap/dist/css/bootstrap.min.css"]').attr('href', app_path + 'lib/themes/' + tema.val() + '/bootstrap.min.css');
        } else {
            if ($(this).val() === 'default') {
                $('link[href="' + app_path + 'lib/themes/' + tema.data("prev") + '/bootstrap.min.css"]').attr('href', app_path + 'vendor/twbs/bootstrap/dist/css/bootstrap.min.css');
            } else {
                $('link[href="' + app_path + 'lib/themes/' + tema.data("prev") + '/bootstrap.min.css"]').attr('href', app_path + 'lib/themes/' + tema.val() + '/bootstrap.min.css');
            }
        }
        tema.data("prev", tema.val());
        $('body').fadeIn();
    });
    $("#select_empresa_logada").change(function () {
        var empresa_id = $(this).val();
        $.post(app_path + 'src/App/Ajax/Usuario/alterar_empresa.php', {empresa_id: empresa_id}, function () {
            window.location.href = app_path;
        });
    });
    
    $("#formUsuario").on('select2:unselecting', '#select_setores_usuario', function (e){
        setor_id = e.params.args.data.id;
        let usuario_id = $("#usuario_id").val();
        $.post(app_path + 'src/App/Ajax/Usuario/verificar_remocao_setor.php', {setor_id: setor_id, usuario_id:usuario_id }, function (response){
            if(response.tipo == 'info'){
                bootbox.confirm({
                    title: "",
                    message: response.msg,
                    buttons: {
                        cancel: {
                            label: '<i class="fa fa-times"></i> Não'
                        },
                        confirm: {
                            label: '<i class="fa fa-check"></i> Sim'
                        }
                    },
                    callback: function (result) {
                        if (!result) {
                            let values = $('#select_setores_usuario').val();
                            values.push(setor_id);
                            $('#select_setores_usuario').select2('val', values);
                        }
                    }
                });
                return true;
            }
        }, 'json');
    });
    
    // Prevent Bootstrap dialog from blocking focusin
    $(document).on('focusin', function (e) {
        if ($(e.target).closest(".mce-window").length) {
            e.stopImmediatePropagation();
        }
    }).delegate('*[data-toggle="lightbox"]', 'click', function (event) {
        event.preventDefault();
        $(this).ekkoLightbox();
    });
    window.onerror = function (error, url, line) {
        var error_string = 'Erro: ' + error + ' URL: ' + url + ' Linha: ' + line;
        if (typeof l !== 'undefined') {
            l.stop();
        }
        hideLoading();
        escreverLogErro(error_string);
        console.log({acc: 'error', data: error_string});
    };
    $('form').on('reset', function (e) {
        $(this).find('select').each(function () {
            $(this).val('').trigger('change');
        });
    });
    $('.btn-go-back').click(function () {
        showLoading();
        if (window.history.back() === undefined) {
            window.location.href = app_path;
        }
    });
    $('.btn-go-forward').click(function () {
        showLoading();
        if (window.history.forward() === undefined) {
            window.location.href = app_path;
        }
    });
    $("body").on('change', '.cep', function (e) {
        var $cep = $(this);
        if ($.trim($(this).val()) !== "") {
            //showLoading();
            $.getScript(app_url + "src/App/Ajax/Cep/buscar_endereco.php?cep=" + $(this).val(), function () {
                if (resultadoCEP["resultado"] != 0) {
                    $cep.closest("form").find("input#rua").val((unescape(resultadoCEP["tipo_logradouro"]) + " " + unescape(resultadoCEP["logradouro"])).toUpperCase());
                    $cep.closest("form").find("input#bairro").val((unescape(resultadoCEP["bairro"])).toUpperCase());
                    var cidade = unescape(resultadoCEP["cidade"]);
                    $cep.closest("form").find("#estado").attr('cidade', cidade);
                    $cep.closest("form").find("#estado").val(unescape(resultadoCEP["uf"]).toUpperCase()).trigger('change');
                    $cep.closest("form").find("#cidade").val(cidade.toUpperCase());
                    $cep.closest("form").find("input#numero").focus();
                } else {
                    bootbox.alert("Não foi possível encontrar o endereço a partir deste CEP.");
                }
                hideLoading();
            });
        }
    }).on('click', '.selecionar_todos', function () {
        var target = $(this).attr('target');
        if ($(this).is(':checked')) {
            $('.' + target).each(function () {
                this.checked = true;
            });
        } else {
            $('.' + target).each(function () {
                this.checked = false;
            });
        }
    }).on('keyup', '.search', function () {
        var target = $(this).attr('target');
        var current_query = $(this).val().toLowerCase();
        var $list = $("." + target + " li");
        if (current_query !== "") {
            $list.hide();
            $list.each(function () {
                var current_keyword = $(this).text().toLowerCase();
                if (current_keyword.indexOf(current_query) >= 0) {
                    $(this).show();
                }
            });
        } else {
            $list.show();
        }
    });
    /************************ Eventos do Formulário de Configuração ********************************************************/
    $("#btn_restore_default").click(function (e) {
        e.preventDefault();
        $.post(app_path + 'configuracao/buscarPadroes', function (response) {
            $("#select_tema").val(response.tema).trigger('change');
            $("#select_tema_navbar").val(response.tema_navbar).trigger('change');
            $("#cor_faixa").val(response.cor_faixa).trigger('change');
        }, 'json');
    });
    $("#cor_faixa").change(function () {
        $("#cabecalho").css('border-bottom-color', $(this).val());
    });
    $("#select_tema_navbar").change(function () {
        $("#navegador").toggleClass('navbar-default navbar-inverse');
    });
    var sel = $("#select_tema");
    sel.data("prev", sel.val());
    sel.change(function (data) {
        $('body').hide();
        var tema = $(this);
        if (tema.data("prev") === 'default') {
            $('link[href="' + app_path + 'vendor/twbs/bootstrap/dist/css/bootstrap.min.css"]').attr('href', app_path + 'lib/themes/' + tema.val() + '/bootstrap.min.css');
        } else {
            if ($(this).val() === 'default') {
                $('link[href="' + app_path + 'lib/themes/' + tema.data("prev") + '/bootstrap.min.css"]').attr('href', app_path + 'vendor/twbs/bootstrap/dist/css/bootstrap.min.css');
            } else {
                $('link[href="' + app_path + 'lib/themes/' + tema.data("prev") + '/bootstrap.min.css"]').attr('href', app_path + 'lib/themes/' + tema.val() + '/bootstrap.min.css');
            }
        }
        tema.data("prev", tema.val());
        $('body').fadeIn('slow');
    });
    /************************ Eventos do Input File ********************************************************/
    $(document).on('change', 'input[type=file]', function (e) {
        if ($(this).val() != "") {
            $("#is_anexo_selecionado").val(1);
        } else {
            $("#is_anexo_selecionado").val(0);
        }
    });
    /************************ Eventos Bootstrap Modal *************************************************************/
    $(document).on('show.bs.modal', '.modal', function () {
        var zIndex = 1050 + (20 * $('.modal:visible').length);
        $(this).css('z-index', zIndex);
        /*setTimeout(function () {
            $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
        }, 0);*/
    });
    $(document).on('hidden.bs.modal', '.modal', function () {
        $('.modal:visible').length && $(document.body).addClass('modal-open');
    });
    /**
     * Verifica se senha atual informada está correta
     */
    $("#senhaAtual").change(function () {
        var $senha_atual = $(this);
        if ($senha_atual.val() != "") {
            $.post(app_path + 'src/App/Ajax/Usuario/verificar_senha.php', {senha: $senha_atual.val()}, function (response) {
                if (response.tipo === 'success') {
                    $($senha_atual).closest('.form-group').addClass('has-success');
                    $($senha_atual).closest('.form-group').removeClass('has-error');
                    $("#msgSenhaAtual").addClass('hidden').fadeOut();
                } else {
                    $($senha_atual).closest('.form-group').addClass('has-error');
                    $("#msgSenhaAtual").text(response.msg).removeClass('hidden').fadeIn();
                    $senha_atual.focus();
                }
            }, 'json');
        } else {
            $($senha_atual).closest('.form-group').removeClass('has-error');
            $($senha_atual).closest('.form-group').removeClass('has-success');
        }
    });
    /**
     * Instancia todo botão com a classe 'btn-loading' a mostrar loader
     */
    $('body').on('click', '.btn-loading', function () {
        showLoading();
    });
    bootbox.setDefaults({
        "bootstrap-version": "4"
    });

    $('body').on('click', '.btn-desativar', function (e) {
        e.preventDefault();
//        debugger;
        var href = $(this).attr('href');
        bootbox.confirm({
            title: "",
            message: "Deseja realmente desativar este registro?",
            buttons: {
                cancel: {
                    label: '<i class="fa fa-times"></i> Não'
                },
                confirm: {
                    label: '<i class="fa fa-check"></i> Sim'
                }
            },
            callback: function (result) {
                if (result) {
                    showLoading();
                    $.post(href, {ajax: true}, function (response) {
                        showGrowMessage(response.tipo, response.msg);
                        location.reload();
                    }, 'json').done(function () {
                        hideLoading();
                    });
                }
            }
        });
        e.stopPropagation();
    })
    $('.btn-ajax').on('click', function (e) {
        e.preventDefault();
        let href = $(this).attr('href');
        showLoading();
        $.post(href, {ajax: true}, function (response) {
            showGrowMessage(response.tipo, response.msg);
            location.reload();
        }, 'json').done(function () {
            hideLoading();
        });
    })

    /**
     * Instancia todo botão com classe btn-excluir para abrir um modal de confirmação
     */
    $('body').on('click', '.btn-excluir', function (e) {
        e.preventDefault();
        var href = $(this).attr('href');
        var $linha = $(this).closest('tr');
        initBootboxRemove(href, $linha);
        e.stopPropagation();
    }).on('click', '.btn-editar', function (e) {
        e.preventDefault();
        var href = $(this).attr('href');
        var entidade = $(this).attr('entidade');
        var modal_id = "editar" + entidade + "Modal";
        var modal_size = $(this).attr('modal-size');
        showLoading();
        $.post(href, function (response) {
            createModal(modal_id, "Editar", response, modal_size);
            initFormRules();
            var form_id = $(response).closest('form').attr('id');
            $('#' + form_id).validate({
                submitHandler: function (form) {
                    showLoading();
                    $.post($(form).attr('action'), $(form).serialize() + '&ajax=true', function (response) {
                        if (response.tipo === 'success') {
                            $('#' + modal_id).modal('hide');
                            atualizarListagem(entidade);
                        }
                        hideLoading();
                        showGrowMessage(response.tipo, response.msg);
                    }, 'json');
                    return false;
                }
            });
        }).done(function () {
            hideLoading();
        });
    }).on('click', '.btn-editar-no-propagate', function (e) {
        e.stopPropagation();
        var href = $(this).attr('href');
        showLoading();
        window.location.href = href;
    }).on('click', '.visualizar-entidade', function (e) {
        e.preventDefault();
        var entidade = $(this).attr('entidade');
        var entidade_id = $(this).attr('entidade_id');
        var modal_size = $(this).attr('modal_size');
        showLoading();
        $.post(app_path + 'src/App/View/' + entidade + '/visualizar.php', {entidade_id: entidade_id}, function (response) {
            createModal("visualizar_" + entidade + "Modal", "Visualizar", response, modal_size);
        }).done(function () {
            hideLoading();
        });
    }).on("input keyup", ".input-with-limit", function () {
        var limite = $(this).attr('maxlength');
        var caracteresDigitados = $(this).val().length;
        var caracteresRestantes = limite - caracteresDigitados;
        $(this).closest('.form-group').find(".caracteres-restantes").text(caracteresRestantes + ' caracteres restantes.');
    });
    /**
     * Botão com classe .closeMessage remove o elemento da tela
     */
    $('.closeMessage').click(function () {
        $(this).parent().fadeOut();
    });
    /**
     * instancia evento para disparar cadastro dinâmico de entidades via modal
     */
    $('body').off('click', '.btn-cadastrar-modal').on('click', '.btn-cadastrar-modal', function () {
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
                $.post(app_path + 'src/App/View/' + aux[1] + '/cadastrar.php', function (response) {
                    var form_id = $(response).closest('form').attr('id');
                    createModal(modal_id, null, response, modal_size);
                    initFormRules();
                    $('#' + form_id).validate({
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
                                //hideLoading();
                                showGrowMessage(response.tipo, response.msg);
                            }, 'json');
                            return false;
                        }
                    });
                    //initFormRules();
                }).done(function () {
                    hideLoading();
                });
            } else {
                hideLoading();
                showGrowMessage('warning', "Desculpe, mas você não permissão para executar essa ação.");
            }
        });

    });
}

function NeutralizeAccent(data) {
    return !data
        ? ''
        : typeof data === 'string'
            ? data
                .replace(/\n/g, ' ')
                .replace(/[éÉěĚèêëÈÊË]/g, 'e')
                .replace(/[šŠ]/g, 's')
                .replace(/[čČçÇ]/g, 'c')
                .replace(/[řŘ]/g, 'r')
                .replace(/[žŽ]/g, 'z')
                .replace(/[ýÝ]/g, 'y')
                .replace(/[áÁâàÂÀ]/g, 'a')
                .replace(/[íÍîïÎÏ]/g, 'i')
                .replace(/[ťŤ]/g, 't')
                .replace(/[ďĎ]/g, 'd')
                .replace(/[ňŇ]/g, 'n')
                .replace(/[óÓ]/g, 'o')
                .replace(/[úÚůŮ]/g, 'u')
            : data
}

/**
 * Função que instancia datatable genérica
 * @returns {undefined}
 */
function initDataTable() {
    jQuery.extend(jQuery.fn.dataTableExt.oSort, {
        'locale-compare-asc': function (a, b) {
            return a.localeCompare(b, 'cs', {sensitivity: 'case'})
        },
        'locale-compare-desc': function (a, b) {
            return b.localeCompare(a, 'cs', {sensitivity: 'case'})
        }
    });
    jQuery.fn.dataTable.ext.type.search['locale-compare'] = function (data) {
        return NeutralizeAccent(data);
    }
    jQuery.extend(jQuery.fn.dataTableExt.oSort, {
        "date-uk-pre": function (a) {
            if (a == null || a == "") {
                return 0;
            }
            var ukDatea = a.split('/');
            return (ukDatea[2] + ukDatea[1] + ukDatea[0]) * 1;
        },
        "date-uk-asc": function (a, b) {
            return ((a < b) ? -1 : ((a > b) ? 1 : 0));
        },
        "date-uk-desc": function (a, b) {
            return ((a < b) ? 1 : ((a > b) ? -1 : 0));
        }
    });
    /**
     * =================================================================================
     *  Tradução Datatable
     * =================================================================================
     */
    $.extend(true, $.fn.dataTable.defaults, {
        "oLanguage": {
            "sLengthMenu": "_MENU_ registros por página",
            "sProcessing": "<i class='fa fa-circle-o-notch fa-fw fa-spin'></i> Carregando...",
            "sEmptyTable": "Nenhum registro encontrado.",
            "sZeroRecords": "Nenhum registro encontrado.",
            "sInfo": "Mostrando de _START_ a _END_ de _TOTAL_ registros",
            "sInfoEmpty": "Mostrando 0 registros",
            "sInfoFiltered": "(filtrados de um total de _MAX_ registros)",
            "sSearch": "Pesquisar:",
            "oPaginate": {
                "sPrevious": "<i class='fa fa-angle-double-left'></i>", // This is the link to the previous page
                "sNext": "<i class='fa fa-angle-double-right'></i>" // This is the link to the next page
            }
        },
        "bStateSave": true

    });
    /* Default class modification */
    $.extend($.fn.dataTable.ext.classes, {
        sWrapper: "dataTables_wrapper dt-bootstrap4"
    });
    $.fn.dataTableExt.oApi.fnResetAllFilters = function (oSettings, bDraw/*default true*/) {
        for (iCol = 0; iCol < oSettings.aoPreSearchCols.length; iCol++) {
            oSettings.aoPreSearchCols[iCol].sSearch = '';
        }
        oSettings.oPreviousSearch.sSearch = '';
        if (typeof bDraw === 'undefined')
            bDraw = true;
        if (bDraw)
            this.fnDraw();
    };
    (function ($) {
        /*
         * Function: fnGetColumnData
         * Purpose:  Return an array of table values from a particular column.
         * Returns:  array string: 1d data array
         * Inputs:   object:oSettings - dataTable settings object. This is always the last argument past to the function
         *           int:iColumn - the id of the column to extract the data from
         *           bool:bUnique - optional - if set to false duplicated values are not filtered out
         *           bool:bFiltered - optional - if set to false all the table data is used (not only the filtered)
         *           bool:bIgnoreEmpty - optional - if set to false empty values are not filtered from the result array
         * Author:   Benedikt Forchhammer <b.forchhammer /AT\ mind2.de>
         */
        $.fn.dataTableExt.oApi.fnGetColumnData = function (oSettings, iColumn, bUnique, bFiltered, bIgnoreEmpty) {
            // check that we have a column id
            if (typeof iColumn == "undefined")
                return new Array();
            // by default we only want unique data
            if (typeof bUnique == "undefined")
                bUnique = true;
            // by default we do want to only look at filtered data
            if (typeof bFiltered == "undefined")
                bFiltered = true;
            // by default we do not want to include empty values
            if (typeof bIgnoreEmpty == "undefined")
                bIgnoreEmpty = true;
            // list of rows which we're going to loop through
            var aiRows;
            // use only filtered rows
            if (bFiltered == true)
                aiRows = oSettings.aiDisplay;
            // use all rows
            else
                aiRows = oSettings.aiDisplayMaster; // all row numbers

            // set up data array   
            var asResultData = new Array();
            for (var i = 0, c = aiRows.length; i < c; i++) {
                iRow = aiRows[i];
                var aData = this.fnGetData(iRow);
                var sValue = aData[iColumn];
                // ignore empty values?
                if (bIgnoreEmpty == true && sValue.length == 0)
                    continue;
                // ignore unique values?
                else if (bUnique == true && jQuery.inArray(sValue, asResultData) > -1)
                    continue;
                // else push the value onto the result data array
                else
                    asResultData.push(sValue);
            }

            return asResultData;
        };
    }(jQuery));
    /**
     * =================================================================================
     *  Datatable Genérica
     * =================================================================================
     */
    $('.datatable').each(function () {
        var col = $(this).children('tr').children('td').length;
        if (!$(this).hasClass('dataTable')) {
            $(this).dataTable({
                "sDom": "<'row'<'col'l><'col'f>r>t<'row'<'col'i><'col'p>>",
                "aaSorting": [],
                "bStateSave": true,
                responsive: true,
                "deferRender": true,
                "autoWidth": false,
                "initComplete": function (settings, json) {
                    $(this).fadeIn('slow');
                },
                "aoColumnDefs": [
                    {
                        "sClass": "text-center",
                        "aTargets": [0]
                    },
                    {
                        "sClass": "text-center",
                        bSortable: false,
                        "aTargets": [col - 1]
                    }
                ]
            });
        }
    });
}

/**
 * Instancia a função bootbox alert
 * @param {type} message
 * @param {type} animate
 * @returns {undefined}
 */
function showAlert(message, animate) {
    bootbox.alert({
        animate: animate,
        message: message
    });
}
/**********************************/
/***Última Alteração: 03/02/2023***/
/*************André****************/
/**********************************/
function initEditor() {
    if ($(".editor").length) {
        tinyMCE.baseURL = app_path + 'lib/tinymce';
        tinyMCE.suffix = ".min";
        tinymce.remove('.editor');
        tinymce.init({
            language: 'pt_BR',
            selector: '.editor',
            skin: "lightgray",
            height: 200,
            menubar: false,
            branding: false,
            plugins: 'print preview code searchreplace autolink directionality visualblocks visualchars fullscreen link table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists textcolor contextmenu colorpicker textpattern',
            toolbar1: 'formatselect fontselect fontsizeselect | bold italic strikethrough forecolor backcolor | link | table | alignleft aligncenter alignright alignjustify  | numlist bullist outdent indent  | removeformat fullscreen', 
            protect: [
                /\<\/?(if|endif)\>/g, // Protect <if> & </endif>
                /\<xsl\:[^>]+\>/g, // Protect <xsl:...>
                /<\?php.*?\?>/g // Protect php code
            ],
            setup: function (editor) {
                editor.on('change', function () {
                    $(".editor").text(tinyMCE.activeEditor.getContent());
                    editor.save();
                });
            }
        });
    }
}

/**
 * Instancia todo botão de submit de formulário com spining a direita
 * @returns {undefined}
 */
function initLadda() {
    Ladda.bind('button[type=submit]');
}

/**
 * Função que inicia plugin para medir força de senha
 * @returns {undefined}
 */
function initComplexify() {
    $("#novaSenha").complexify({}, function (valid, complexity) {
        //progress-bar-success
        var complexidade = complexity.toFixed(0);
        var classe_complexidade = complexidade <= 40 ? 'progress-bar-danger' : (complexidade <= 70 ? 'progress-bar-warning' : 'progress-bar-success');
        $('.progress').show();
        $("#passwordMeter").attr('aria-valuenow', complexity)
            .css('width', complexity + '%')
            .text(complexidade + ' % força senha')
            .removeClass('progress-bar-danger')
            .removeClass('progress-bar-success')
            .removeClass('progress-bar-warning')
            .addClass(classe_complexidade);
    });
}

/**
 * Função que cria um bootstrap V3 modal
 * @param {string} modal_id = id html do modal
 * @param {string} modal_title = título do modal
 * @param {string} modal_content = conteúdo do modal
 * @param {string} modal_size = modal-lg | modal-sm
 * @returns {string}
 */
function createModal(modal_id, modal_title, modal_content, modal_size = 'modal-lg', show_btn_close = true) {
    var head = '<div id="' + modal_id + '" class="modal" data-backdrop="static" data-keyboard="false">';
    var body = '<div class="modal-dialog ' + modal_size + '">\n\
                    <div class = "modal-content">\n';
    if (modal_title != null) {
        body += '<div class="modal-header">'
            + '<h5 class="modal-title">' + modal_title + '</h5>';
        if(show_btn_close) {
            body += '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
        }
        body += '</div>';
    }
    body += '<div class="modal-body"> ' + modal_content + ' </div>\n\
                    </div>\n\
                </div>';
    var foot = '</div>';
    if ($('#' + modal_id).length) {
        $('#' + modal_id).html(body).modal('show');
    } else {
        $(head + body + foot).modal('show');
    }
//    Hack para forçar a vinculação do script com os elementos da resposta. 
//    Sem isso, o script não tem efeito.
    if (modal_content.includes("script")) {
        $(head + body + foot).find("script").each(function(i) {
            $("body").append($(this));
        });
    }

    //-----------------------------------------------------------------------Alterei aqui
    initEditor();
    //--
}

/**
 * Função que exibe uma mensagem na tela ao usuário
 * @param {string} tipo Tipo de mensagem. Opções: error, success ou warning.
 * @param {string} msg Mensagem a ser exibida.
 * @param {int} width Comprimento da caixa de texto.
 * @param {int} delay Tempo em que a caixa de texto é exibida.
 * @param {string} acaoTitulo Texto a ser exibido no botão de ação.
 * @param {function} acao Ação a ser executada após o gatilho de clique.
 */
function showGrowMessage(tipo, msg, width, delay, acaoTitulo, acao) {
    let icone = "";
    let type = "";
    if (tipo === 'error') {
        icone = 'fa fa-ban';
        type = 'danger';
    } else if (tipo === 'success') {
        icone = 'fa fa-check';
        type = 'success';
    } else if (tipo === 'warning') {
        icone = 'fa fa-info-circle';
        type = 'info';
    }
    let content = $("<did class='row'><div class='col'><i class='" + icone + "'></i> " + msg +"</div></did>");
    if (acao != null) {
        let btnAcao = $("<button class='btn btn-primary'>" + acaoTitulo + "</button>");
        $(btnAcao).on("click", acao);
        content.append(btnAcao);
    }
    $.bootstrapGrowl(content, {type: type, width: width ?? 300, delay: delay ?? 5000});
}

/**
 * Inicia Loading na tela
 * @returns {undefined}
 */
function showLoading() {
    let $element = $('#divLoading');
    if (!$element.is(':visible')) {
        $element.fadeIn();
    }
}

/**
 * Encerra loading na tela
 * @returns {undefined}
 */
function hideLoading() {
    $('#divLoading').hide();
}

/**
 * Função que marca/desmarca checkboxes
 * @param {type} classe
 * @returns {undefined}
 */
function marcaCheckBox(classe) {
    $('.' + classe).each(
        function () {
            if ($(this).prop("checked"))
                $(this).prop("checked", false);
            else
                $(this).prop("checked", true);
        }
    );
}

/**
 * Função que cria um select com os dados da coluna de uma tabela DataTable
 * @param {type} aData
 * @returns {String}
 */
function fnCreateOptionsSelect(aData) {
    var options = '<option value="">Todos</option>', i, iLen = aData.length;
    for (i = 0; i < iLen; i++) {
        options += '<option value="' + aData[i].replace(/(<([^>]+)>)/ig, "") + '">' + aData[i] + '</option>';
    }
    return options;
}

/**
 * Função que cria um select com os dados da coluna de uma tabela DataTable
 * @param {type} aData
 * @returns {String}
 */
function fnCreateSelect(aData, column_name) {
    if (!column_name) {
        column_name = "Selecione";
    }
    var r = '<select name="select"><option value="">Todos</option>', i, iLen = aData.length;
    for (i = 0; i < iLen; i++) {
        r += '<option value="' + aData[i].replace(/(<([^>]+)>)/ig, "") + '">' + aData[i] + '</option>';
    }
    return r + '</select>';
}

/**
 * Função que inicia plugin select2 v4
 * @returns {undefined}
 */
function initSelect2() {
//    debugger;
    $('select.select2').each(function () {
        if (!$(this).hasClass("select2-hidden-accessible")) {
            $(this).select2({
                language: "pt-BR",
                width: '100%',
                placeholder: "Selecione",
                allowClear: true,
                dropdownParent: $(this).parent()
            });
        }
    });
    //$('.select2').fadeIn('slow');
}

function initSelect2Tag() {
    $(".select2_tag").select2({
        tags: true,
        language: "pt-BR",
        tokenSeparators: [","]
    });
}

/**
 * Função que transforma todas as inputs tipo date para text e instancia o plugin datepicker do jquery ui,
 * além disso, seta a máscara de entrada para o formato dd/mm/aaaa e cria um clone no formato aaaa-mm-dd para
 * ser enviado via $_POST
 * @returns {undefined}
 */
function initDatePicker() {
    /* Date picker plugin */
    $('.datepicker').datepicker({
        dateFormat: "dd/mm/yy"
    }).on('change', function (ev) {
        var $form = $(this).closest('form');
        if ($form.length)
            $(this).valid();
    });
    /* Date picker mask */
    $('.datepicker').mask('00/00/0000');
    /** Date range picker */
    var dateFormat = "dd/mm/yy";
    $('.date-range').each(function () {
        var data_ini_id = $(this).attr('id');
        var data_fim_id = $(this).attr('data_fim_id');
        var minDateFrom = $(this).attr('data-mindate');
        var from = $("#" + data_ini_id).datepicker({
            defaultDate: new Date(),
            changeMonth: true,
            minDate: minDateFrom
        }).on("change", function () {
            to.datepicker("option", "minDate", getDate(this));
        });
        var to = $("#" + data_fim_id).datepicker({
            defaultDate: "+1w",
            changeMonth: true
        }).on("change", function () {
            from.datepicker("option", "maxDate", getDate(this));
        });
        if ($(this).val() != "") {
            to.datepicker("option", "minDate", getDate(this));
        }
        $("#" + data_ini_id).mask('00/00/0000');
        $("#" + data_fim_id).mask('00/00/0000');
    });

    function getDate(element) {
        var date;
        try {
            date = $.datepicker.parseDate(dateFormat, element.value);
        } catch (error) {
            date = null;
        }

        return date;
    }
}

/**
 * Eventos e plugins para inputs
 * @returns {undefined}
 */
function initInputs() {
    //Faz com que toda label vinculada a input obrigatória seja da classe required
    $('input[required],textarea[required],select[required]').each(function () {
        $(this).closest('.form-group').find('.col-form-label').each(function (){
            if(!$(this).hasClass("not-required")){
                $(this).addClass('required');
            }
        });
    });
    var maisculas = $('.maiscula');
    // Para tratar o colar
    $(maisculas).bind('paste', function () {
        $(this).val($(this).val().toUpperCase());
    });
    // Para tratar quando é digitado
    $(maisculas).keyup(function (e) {
        var start = this.selectionStart,
            end = this.selectionEnd;
        switch (e.keyCode) {
            case 8:  // Backspace
            case 9:  // Tab
            case 13: // Enter
            case 37: // Left
            case 38: // Up
            case 39: // Right
            case 40: // Down
                break;
            default:
                $(this).val($(this).val().toUpperCase());
        }
        this.setSelectionRange(start, end);
    });
    var minusculas = $('.minuscula');
    $(minusculas).keyup(function (e) {
        var start = this.selectionStart,
            end = this.selectionEnd;
        switch (e.keyCode) {
            case 8:  // Backspace
            case 9:  // Tab
            case 13: // Enter
            case 37: // Left
            case 38: // Up
            case 39: // Right
            case 40: // Down
                break;
            default:
                $(this).val($(this).val().toLowerCase());
        }
        this.setSelectionRange(start, end);
    });
    initSelect2();
    initSelect2Tag();
    initPopover();
    initToolTip();
}

/**
 * Função que inicia as máscaras de entrada da aplicação
 * @returns {undefined}
 */
function initInputMasks() {
    $(".ano").mask('0000');
    $('.date').mask('00/00/0000');
    $('.time').mask('00:00:00');
    $('.date_time').mask('00/00/0000 00:00:00');
    $('.cep').mask('00000-000');
    $('.phone').mask('0000-0000');
    var SPMaskBehavior = function (val) {
        return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
    };
    var spOptions = {
        onKeyPress: function (val, e, field, options) {
            field.mask(SPMaskBehavior.apply({}, arguments), options);
        }
    };
    $('.telefone').mask(SPMaskBehavior, spOptions);
    $('.phone_with_ddd').mask('(00) 0000-0000');
    $('.phone_us').mask('(000) 000-0000');
    $('.mixed').mask('AAA 000-S0S');
    $('.codigo-orgao').mask('00');
    $('.codigo-unidade').mask('000');
    $('.cpf').mask('000.000.000-00', {reverse: true});
    $('.cnpj').mask('00.000.000/0000-00', {reverse: true});

    var SPMaskBehavior_cpf_cnpj = function (val) {
        return val.replace(/\D/g, '').length <= 11 ? '000.000.000-000000' : '00.000.000/0000-00';
    };
    var spOptions_cpf_cnpj = {
        onKeyPress: function (val, e, field, options) {
            field.mask(SPMaskBehavior_cpf_cnpj.apply({}, arguments), options);
        }
    };
    $('.cpf_cnpj').mask(SPMaskBehavior_cpf_cnpj, spOptions_cpf_cnpj);
    $('.money').mask('000.000.000.000.000,00', {reverse: true});
    $('.money2').mask("#.##0,00", {reverse: true});
    $('.ip_address').mask('0ZZ.0ZZ.0ZZ.0ZZ', {
        translation: {
            'Z': {
                pattern: /[0-9]/, optional: true
            }
        }
    });
    $('.ip_address').mask('099.099.099.099');
    $('.percent').mask('##0,00%', {reverse: true});
    $('.clear-if-not-match').mask("00/00/0000", {clearIfNotMatch: true});
    $('.placeholder').mask("00/00/0000", {placeholder: "__/__/____"});
    $('.fallback').mask("00r00r0000", {
        translation: {
            'r': {
                pattern: /[\/]/,
                fallback: '/'
            },
            placeholder: "__/__/____"
        }
    });
    $('.selectonfocus').mask("00/00/0000", {selectOnFocus: true});
}

/**
 * Função que inicia o plugin autoNumeric
 * @returns {undefined}
 */
function initAutoNumeric() {
    if ($('.autonumeric').length) {
        $(".autonumeric").autoNumeric('init',
            {
                aSep: '.',
                aDec: ',',
                vMin: '-999999999.99',
                vMax: '999999999.99'
            }
        );
    }
}

/**
 * Função que instancia um novo modal de confirmação
 * @param {type} href caminho ao responder positivo
 * @returns {undefined}
 */
function initBootboxRemove(href, $linha) {
    bootbox.confirm({
        title: "",
        message: "Deseja realmente excluir este registro?",
        buttons: {
            cancel: {
                label: '<i class="fa fa-times"></i> Não'
            },
            confirm: {
                label: '<i class="fa fa-check"></i> Sim'
            }
        },
        callback: function (result) {
            if (result) {
                showLoading();
                if ($linha.length) {
                    $.post(href, {ajax: true}, function (response) {
                        if (response.tipo == 'success') {
                            $linha.remove();
                        }
                        showGrowMessage(response.tipo, response.msg);
                    }, 'json').done(function () {
                        hideLoading();
                    });
                } else {
                    window.location.href = href;
                }
            }
        }
    });
}

function initToolTip() {
    $('[data-toggle="tooltip"]').tooltip();
    $('[data-toggle="tooltip-html"]').tooltip({html: true});
}

/**
 * Função para iniciar  elementos popover
 * @returns {undefined}
 */
function initPopover() {
    $('[data-toggle="popover"]').popover({
        animation: true,
        trigger: 'hover',
        placement: 'top',
        container: 'body',
        delay: {show: 500, hide: 100}
    });
}

/**
 * Função que limpa um formulario passando sua id
 * @param {element} formulario = id do formulario
 * @returns {undefined}
 */
function limpaFormulario(formulario) {
    $(formulario).each(function () {
        this.reset();
    });
}

/**
 * Função que cria filtros para uma datatable
 * @param {type} row_id = id tabela html
 * @param {type} dataTable= objeto datatable
 * @param {type} filterIndexesSelect = array com indices de colunas que terão filtro select
 * @param {type} filterIndexesText = array com indices de colunas que terão filtro por input text
 * @returns {undefined}
 */
function filterColumns(row_id, dataTable, filterIndexesSelect, filterIndexesText) {
    $('td', '#' + row_id).each(function (i) {
        if ($.inArray(i, filterIndexesSelect) !== -1) {
            this.innerHTML = fnCreateSelect(dataTable.fnGetColumnData(i).sort());
            $('select', this).change(function () {
                if ($(this).val() != "") {
                    dataTable.fnFilter("^" + $(this).val() + "$", i, true);
                } else {
                    dataTable.fnFilter($(this).val(), i);
                }
                //updateSelectFilter(row_id, dataTable, filterIndexesSelect);
            });
        }
        if ($.inArray(i, filterIndexesText) !== -1) {
            $('input', this).keyup(function () {
                /*if ($(this).val() != "") {
                 //dataTable.fnFilter("^" + $(this).val() + "$", i, true);
                 } else {*/
                dataTable.fnFilter($(this).val(), i);
                //}
            });
        }
    });
    $('.select2-filter').select2();
}

/**
 * Função para formatar o valor de real para decimal
 *  @param {string} valor
 */
function realToDecimal(valor) {
    if (valor != "") {
        valor = valor.replace(/\./g, "");
        return valor.replace(",", ".", function toFloat(retorno) {
            return parseFloat(retorno);
        });
    }
    return 0;
}

function updateProgressBar($progress, percentComplete) {
    $progress.find('.progress-bar').attr('aria-valuenow', percentComplete).css('width', percentComplete + '%');
    $progress.find('#porcentagem').text(percentComplete + '%');
}

function defineDateRange(col_ini, col_fim, data_ini_id, data_fim_id) {
    $.fn.dataTableExt.afnFiltering.push(
        function (oSettings, aData, iDataIndex) {
            let iFini = $("#" + data_ini_id).val();
            let iFfin = $("#" + data_fim_id).val();
            let iStartDateCol = col_ini;
            let iEndDateCol = col_fim;
            iFini = iFini.substring(6, 10) + iFini.substring(3, 5) + iFini.substring(0, 2);
            iFfin = iFfin.substring(6, 10) + iFfin.substring(3, 5) + iFfin.substring(0, 2);
            let datofini = aData[iStartDateCol].substring(6, 10) + aData[iStartDateCol].substring(3, 5) + aData[iStartDateCol].substring(0, 2);
            let datoffin = aData[iEndDateCol].substring(6, 10) + aData[iEndDateCol].substring(3, 5) + aData[iEndDateCol].substring(0, 2);
            if (iFini === "" && iFfin === "") {
                return true;
            } else if (iFini <= datofini && iFfin === "") {
                return true;
            } else if (iFfin >= datoffin && iFini === "") {
                return true;
            } else if (iFini <= datofini && iFfin >= datoffin) {
                return true;
            }
            return false;
        }
    );
}

function buscarAssinaturaStatus() {
    let url = app_url + "processo/statusAssinatura";
    let $rows = $("div[data-processo-id]");
    let ids = $rows.map((i, data) => $(data).data('processo-id'));
    let data = {ids: $.makeArray(ids)};
    $.ajax({
        url: url,
        type: "GET",
        data: data,
        dataType: "json",
        success: (result) => {
            $rows.each((i, element) => {
                $(element).html(result[i]);
            });
        }, error: () => {
            showGrowMessage("error", "Não foi possível carregar o status das assinaturas.");
        }
    });
}

function initPlugins() {
    $.fn.serializeJson = function() {
        let data = {};
        $(this).serializeArray().forEach((element)=> {
            let position = undefined;
            let name = element.name;
            let start = name.indexOf('[');
            if (start >= 0) {
                let end = name.indexOf(']');
                position = parseInt(name.slice(start + 1, end));
                name = name.slice(0, start);
            }
            if (data[name] === undefined) {
                if (position !== undefined) {
                    if (data[name] !== undefined) {
                        let value = data[name];
                        data[name] = [];
                        data[name].push(value);
                    } else {
                        data[name] = [];
                    }
                    data[name][position] = element.value;
                } else {
                    data[name] = element.value;
                }
            } else {
                let value = data[name];
                data[name] = [];
                data[name].push(value);
                if (position !== undefined) {
                    if (data[name] !== undefined) {
                        let value = data[name];
                        data[name] = [];
                        data[name].push(value);
                    }
                    data[name][position] = element.value;
                } else {
                    data[name].push(element.value);
                }
            }
        });
        return JSON.parse(JSON.stringify(data));
    };
}