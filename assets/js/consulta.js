var app_url = $("#app_url").val();
var check_login = false;
//var app_path = $("#app_url").val();
//debugger;
$(function () {
    initSelect2Interessado();
    $("body").on('click', '.btn-pesquisar-interessado', function (e) {
        var $select = $(this).closest('.form-group').find('.select_interessado');
        e.preventDefault();
        showLoading();
        $.post(app_url + 'src/App/View/Interessado/pesquisar.php', function (response) {
            createModal('pesquisaInteressadoModal', 'Pesquisar Interessado', response, 'modal-lg');
            initTabelaPesquisaInteressados($select);
            $("#cpfcnpj").blur(function (e) {
                var tamanho = $(this).val().length;
                if (tamanho == 0) {
                    return true;
                }
                if (tamanho <= 14) {
                    return validarCPF($(this));
                }
                return validaCNPJ($(this), e);
            });
            var cpfMascara = function (val) {
                    return val.replace(/\D/g, '').length > 11 ? '00.000.000/0000-00' : '000.000.000-009';
                },
                cpfOptions = {
                    onKeyPress: function (val, e, field, options) {
                        field.mask(cpfMascara.apply({}, arguments), options);
                    }
                };
            $('#cpfcnpj').mask(cpfMascara, cpfOptions);
        }).done(function () {
            hideLoading();
        });
    }).on('click', '.btn-detalhar', function (e) {
        e.preventDefault();
        var entidade = $(this).attr('entidade');
        var entidade_id = $(this).closest('tr').attr(entidade + '_id');
        $(this).toggleClass('text-success text-danger');
        $(this).find('i').toggleClass('fa-plus-circle fa-minus-circle');
        $("#detalhes-" + entidade + "-" + entidade_id).toggleClass('show hidden');
    });
    initValidateDefault();
    $('body').on('click', '#tabelaProcessosPublicos tbody tr td:not(.col-actions)', function (e) {
        e.preventDefault();
        var processo_id = $(this).closest('tr').attr('processo_id');
        if (processo_id != null) {
            showLoading();
            window.location.href = app_url + 'consulta/processo/id/' + processo_id;
        } else {
            alert('#ID de processo não encontrado.');
        }
    });
    $("body").on('click', '.tabelaProcessos tbody tr td:not(.col-actions),.tabelaListaProcessos tbody tr td:not(.col-actions),#tabelaControleVencimentos tbody tr td:not(.col-actions),.tabelaVencimentosProximo tbody tr', function (e) {
        var processo_id = $(this).closest('tr').attr('id').split(":")[1];
        window.open(app_url + processo_id, "_blank");
    });
    initDataTable();
    var cpfMascara = function (val) {
            return val.replace(/\D/g, '').length > 11 ? '00.000.000/0000-00' : '000.000.000-009';
        },
        cpfOptions = {
            onKeyPress: function (val, e, field, options) {
                field.mask(cpfMascara.apply({}, arguments), options);
            }
        };
    $('#cpfcnpj').mask(cpfMascara, cpfOptions);
    $("#consultaProcessoForm").validate({
        rules: {
            numeroProcesso: {
                require_from_group: [1, ".buscar-processo-group"]
            },
            interessadoProcesso: {
                require_from_group: [1, ".buscar-processo-group"]
            },
            cpfCnpjProcesso: {
                require_from_group: [1, ".buscar-processo-group"]
            },
            anoProcesso: {
                require_from_group: [1, ".buscar-processo-group"]
            },
            objetoProcesso: {
                require_from_group: [1, ".buscar-processo-group"]
            }
        },
        submitHandler: function (form) {
            check_login = false;
            if (grecaptcha.getResponse()) {
                var l = Ladda.create(form.querySelector('.ladda-button'));
                l.start();
                $.post(app_url + 'src/App/View/Processo/listar_publico.php', $(form).serialize(), function (response) {
                    createModal("resultaBuscaModal", "Resultado da Consulta", response, 'modal-lg');
                    initTabelaProcessosPublicos();
                }).done(function () {
                    l.stop();
                });
            } else {
                bootbox.alert('Por favor, confirme que você não é um robô antes de prosseguir.');
            }

        }
    });
});

function initValidateDefault() {
    $.validator.setDefaults({
        highlight: function (element) {
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
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());
            } else if (element.hasClass('select2')) {
                error.insertAfter(element.parent().find('.select2-container'));
            } else {
                error.insertAfter(element);
            }
        }
    });

}

function initTabelaPesquisaInteressados($select) {
    var url = $("#tabelaPesquisaInteressados").attr('url');
    $("#tabelaPesquisaInteressados").dataTable({
        "sDom": "<'row'<'col hidden'l><'col hidden'f>r>t<'row'<'col'i><'col'p>>",
        orderCellsTop: true,
        "processing": true,
        "serverSide": true,
        "bStateSave": false,
        "aaSorting": [[1, "asc"]],
        "ajax": url,
        "createdRow": function (tr, tdsContent) {
            $(tr).attr('title', 'Clique para selecionar').attr('id', tdsContent[0]);
        },
        "initComplete": function (settings, json) {
            $(this).fadeIn();
            var $tabela = this;
            var api = $tabela.api();
            var $codigo = $('#formPesquisaInteressado').find('.codigo_filter');
            var $nome = $('#formPesquisaInteressado').find('.nome_filter');
            var $cpf_cnpj = $('#formPesquisaInteressado').find('.cpf_cnpj_filter');
            $codigo.keyup(function () {
                api.column(0).search($(this).val(), false, false, false).draw();
            });
            $nome.keyup(function () {
                api.ajax.url(url + '?nome=' + $(this).val()).load();
                //api.column(1).search($(this).val()).draw();
            });
            $cpf_cnpj.keyup(function () {
                api.ajax.url(url + '?cpf_cnpj=' + $(this).val()).load();
            });
        },
        "aoColumnDefs": [
            {
                targets: 1,
                type: 'locale-compare'
            },
            {
                "bSortable": false,
                "aTargets": [2]
            },
            {
                "bVisible": false,
                "aTargets": [3]
            },
            {
                "bVisible": false,
                "aTargets": [4]
            },
            {
                "bVisible": false,
                "aTargets": [5]
            }
        ]
    });
    $('#tabelaPesquisaInteressados tbody').on('click', 'tr', function () {
        var id = this.id;
        var $tds = $(this).find('td');
        $('#tabelaPesquisaInteressados tbody').find('tr').each(function () {
            $(this).removeClass('table-primary');
        });
        $(this).toggleClass('table-primary');
        $(this).closest('form').find("#interessado_id").val(id);
        $(this).closest('form').find("#nome_interessado").val($($tds[1]).text());
    });
    $("#formPesquisaInteressado").validate({
        submitHandler: function (form) {
            var interessado_id = $(form).find('#interessado_id').val();
            var nome_interessado = $(form).find('#nome_interessado').val();
            if (interessado_id == "") {
                showAlert('Selecione um interessado da lista.');
                return false;
            }
            $select.html("<option value='" + interessado_id + "' selected>" + nome_interessado + "</option>").trigger('change');
            $(form).closest('.modal').modal('hide');
        }
    });
}

function initTabelaProcessosPublicos() {
    var url = $("#tabelaProcessosPublicos").attr('url');
    $("#tabelaProcessosPublicos").dataTable({
        "sDom": "<'row'<'col d-block d-sm-none'l><'col d-block d-sm-none'f>r>t<'row'<'col'i><'col'p>>",
        "stateSave": true,
        "aaSorting": [[3, "desc"]],
        "deferRender": true,
        "autoWidth": false,
        "serverSide": true,
        "processing": true,
        "ajax": url,
        "language": {
            "loadingRecords": "Carregando..."
        },
        "fnRowCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
            $(nRow).attr('processo_id', aData[0]).attr('title', aData[8]);
        },
        "initComplete": function (settings, json) {

        },
        "aoColumnDefs": [
            {
                bVisible: false,
                "aTargets": [0]
            },
            {
                "sClass": "text-center",
                "aTargets": [1]
            },
            {
                "sClass": "text-center",
                "aTargets": [2]
            },
            {
                "sClass": "text-center",
                type: 'date-uk',
                "aTargets": [3]
            },
            {
                bVisible: false,
                "aTargets": [8]
            },
        ]
    });
}
function initDataTable() {
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
            "sProcessing": "Carregando...",
            "sEmptyTable": "Nenhum registro encontrado.",
            "sZeroRecords": "Nenhum registro encontrado.",
            "sInfo": "Mostrando _START_ de _END_ de _TOTAL_ registros",
            "sInfoEmpty": "Mostrando 0 de 0 de 0 registros",
            "sInfoFiltered": "(filtrados de um total de  _MAX_ registros)",
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
    });
}

function initSelect2Interessado() {
    $(".select_interessado").select2({
        placeholder: "Busque por um  interessado",
        minimumInputLength: 3,
        language: "pt-BR",
        allowClear: true,
        theme: 'bootstrap4',
        multiple: false,
        ajax: {
            url: app_url + "src/Core/Ajax/select2_ajax_response.php?entidade=Interessado",
            dataType: 'json',
            delay: 250,
            data: function (params) { // page is the one-based page number tracked by Select2
                return {
                    search: params.term, //search term
                    page: params.page || 1 // page number
                };
            },
            cache: true
        }
    });
}

function initSelect2() {
    $('select.select2').each(function () {
        if ($(this).select2_initialized != true) {
            $(this).select2({
                language: "pt-BR",
                width: '100%',
                placeholder: "Selecione",
                allowClear: true
            });
        }
    });
    //$('.select2').fadeIn('slow');
}
function initApp(){
    
}
function createModal(modal_id, modal_title, modal_content, modal_size) {
    var head = '<div id="' + modal_id + '" class="modal" data-backdrop="static" data-keyboard="false">';
    var body = '<div class="modal-dialog ' + modal_size + '">\n\
                    <div class = "modal-content">\n';
    if (modal_title != null) {
        body += '<div class="modal-header">'
            + '<h5 class="modal-title">' + modal_title + '</h5>'
            + '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>'
            + '</div>';
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

}

function showLoading() {
    let $element = $('#divLoading');
    if (!$element.is(':visible')) {
        $element.fadeIn();
    }
}

function hideLoading() {
    $('#divLoading').hide();
}

function visualizarProcesso(processo_id) {
    window.open(app_url + processo_id, "_blank");
}