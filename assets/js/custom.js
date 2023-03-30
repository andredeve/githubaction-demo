/* Caminho da raiz da aplicação */
// noinspection JSUnresolvedVariable

var app_path = $("#app_path").val();
var nomenclatura = $("#nomenclatura").val();
$(document).ready(function () {
    initApp();
    initSelect2Tree();
    
    $('body').on('click', '.btn-processo-digital', function (){
        var $link = $(this);
        
        showLoading();
        $.post(app_path + 'processo/gerarArquivosParaVisualizacaoDigital', {processo_id: $(this).attr('processo_id')}, function(response){
            window.open($link.attr('href-relatorio'), "_blank");
        } ).done(function (){
            hideLoading();
            
            $(this).attr('href-relatorio');
        });
    });
    
    $('body').on('click', '.btn-cadastrar-interessado', function () {
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
                $.post(app_path + 'src/App/View/' + aux[1] + '/cadastrar.php', function (response) {
                    var form_id = $(response).closest('form').attr('id');
                    createModal(modal_id, null, response, modal_size);
                    initFormRules();
                    $('#' + form_id).validate({
                        submitHandler: function (form) {
                            showLoading();
                            $.post($(form).attr('action'), $(form).serialize() + '&ajax=true', function (response) {
                                if (response.tipo === 'success') {
                                    var interessado_id = response.objeto_id;
                                    if ($select) {
                                        $.post(app_path + 'src/App/Ajax/' + entidade + '/buscar.php', {interessado_id: interessado_id}, function (interessado) {
                                            var option = "<option value='" + interessado.id + "' selected>" + interessado.nome + "</option>";
                                            $select.find('option').each(function () {
                                                $(this).removeAttr('selected');
                                            })
                                            $select.append(option).trigger('change');
                                        }, 'json');
                                    }
                                    $('#' + modal_id).modal('hide');
                                }
                                hideLoading();
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
    /**************************************************************************************************************/
    /*================================        Remessa Envio    ===============================================*/
    /*************************************************************************************************************/
    $("#buscarRemessaForm").validate({
        submitHandler: function (form) {
            var l = Ladda.create(form.querySelector('.ladda-button'));
            l.start();
            $.post(app_path + 'src/App/View/Remessa/gerar.php', $(form).serialize(), function (response) {
                createModal("gerarRemessaModal", nomenclatura + "s disponíveis para gerar remessa", response, 'modal-lg')
                $("#gerarRemessaForm").validate({
                    submitHandler: function (form) {
                        var l = Ladda.create(form.querySelector('.ladda-button'));
                        l.start();
                        $.post($(form).attr('action'), $(form).serialize(), function (response) {
                            showGrowMessage(response.tipo, response.msg);
                            if (response.tipo == 'success') {
                                $(form).closest('.modal').modal('hide');
                                window.open(app_path + 'remessa/imprimir/' + response.objeto_id)
                            }
                        }, 'json').done(function () {
                            l.stop();
                        })
                        return false;
                    }
                });
            }).done(function () {
                l.stop();
            })
            return false;
        }
    });
    $('body').on('click', '.btn-visualizar-remessa', function () {
        var remessa_id = $(this).attr('remessa_id');
        showLoading();
        $.post(app_path + 'src/App/View/Remessa/visualizar.php', {remessa_id: remessa_id}, function (response) {
            createModal("visualizarRemessaModal", "Visualizar detalhes de Remessa", response);
        }).done(function () {
            hideLoading();
        });
    });
    if ($("#tabelaRemessas").length) {
        $("#data_remessa_ini").val($.cookie("data_remessa_ini"));
        $("#data_remessa_fim").val($.cookie("data_remessa_fim"));
        defineDateRange(1, 1, "data_remessa_ini", "data_remessa_fim");
        $('#tabelaRemessas').dataTable({
            "serverSide": true,
            "ajax": app_path + "src/App/Ajax/Remessa/listar_server_side.php",
            "aaSorting": [[1, "desc"]],
            "bStateSave": true,
            "deferRender": true,
            "autoWidth": false,
            "createdRow": function (tr, tdsContent) {
                $(tr).attr('title', "Clique para visualizar detalhes da Remessa ").attr("remessa_id", tdsContent[0]).addClass("btn-visualizar-remessa");
            },
            "initComplete": function (settings, json) {
                $(this).fadeIn('slow');
                var $tabela = $(this).dataTable();
                var $numero = $(this).closest('.remessa-table').find('.numero_filter');
                var $setor_origem = $(this).closest('.remessa-table').find('.setor_origem_filter');
                var $responsavel_origem = $(this).closest('.remessa-table').find('.responsavel_origem_filter');
                var $setor_destino = $(this).closest('.remessa-table').find('.setor_destino_filter');
                var $responsavel_destino = $(this).closest('.remessa-table').find('.responsavel_destino_filter');

                $numero.keyup(function () {
                    $tabela.fnFilter($.trim($(this).val()), 0);
                });
                $setor_origem.keyup(function () {
                    $tabela.fnFilter($.trim($(this).val()), 3);
                });
                $responsavel_origem.keyup(function () {
                    $tabela.fnFilter($.trim($(this).val()), 4);
                });
                $setor_destino.keyup(function () {
                    $tabela.fnFilter($.trim($(this).val()), 5);
                });
                $responsavel_destino.keyup(function () {
                    $tabela.fnFilter($.trim($(this).val()), 6);
                });
                $('#data_remessa_ini,#data_remessa_fim').change(function () {
                    $.cookie("data_remessa_ini", $("#data_remessa_ini").val());
                    $.cookie("data_remessa_fim", $("#data_remessa_fim").val());
                    $tabela.fnDraw();
                });
                var search;
                for (var iCol = 0; iCol < settings.aoPreSearchCols.length; iCol++) {
                    if (settings.aoPreSearchCols[iCol].sSearch) {
                        search = settings.aoPreSearchCols[iCol].sSearch;
                        switch (iCol) {
                            case 0:
                                $numero.val(search);
                                break;
                            case 3:
                                $setor_origem.val(search);
                                break;
                            case 4:
                                $responsavel_origem.val(search);
                                break;
                            case 5:
                                $setor_destino.val(search);
                                break;
                            case 6:
                                $responsavel_destino.val(search);
                                break;
                        }
                    }
                }
            },
            "aoColumnDefs": [
                {
                    type: 'date-uk',
                    "aTargets": [1]
                },
            ]
        });
    }
    /**************************************************************************************************************/
    /*================================        Arquivo Físico    ===============================================*/
    /*************************************************************************************************************/
    $("#pesquisaLocalizacaoFisicaForm").validate({
        submitHandler: function (form) {
            var l = Ladda.create(form.querySelector('.ladda-button'));
            l.start();
            var tabela = $('#tabelaLocalizacaoFisica').dataTable();
            tabela.api().ajax.url(app_path + 'src/App/Ajax/LocalizacaoFisica/listar_server_side.php?' + $(form).serialize()).load();
            $("#pesquisarLocalizacaoFisica").modal('hide');
            l.stop();
            return false;
        }
    });
    /**************************************************************************************************************/
    /*================================        Painel de Controle    ===============================================*/
    /*************************************************************************************************************/
    $('.btn-ver-vencidos').click(function (e) {
        e.preventDefault();
        showLoading();
        $.post(app_path + "src/App/View/Processo/vencidos.php", function (response) {
            createModal("processosVencidosModal", nomenclatura + " vencidos", response, 'modal-lg');
            initTabelaResumoProcessos();
        }).done(function () {
            hideLoading();
        });
    });
    
    $('.btn-ver-processos-vencidos').click(function (e) {
        e.preventDefault();
        showLoading();
        $.post(app_path + "src/App/View/Processo/processos_vencidos.php", function (response) {
            createModal("processosVencidosModal", nomenclatura + " vencidos", response, 'modal-lg');
            initTabelaResumoProcessos();
        }).done(function () {
            hideLoading();
        });
    });
    
    if (jQuery().Highcharts) {
        Highcharts.setOptions({
            lang: {
                downloadJPEG: "Baixar Imagem em JPEG",
                downloadPDF: "Baixar Documento em PDF",
                downloadPNG: "Baixar Imagem em PNG",
                downloadSVG: "Baixar Imagem em SVG",
                loading: "Carregando...",
                months: ["Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "JUlho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"],
                noData: "Sem dados para mostrar.",
                printChart: "Imprimir gráfico",
                resetZoom: "Resetar zoom",
                shortMonths: ["Jan", "Fev", "Mar", "Abr", "Mai", "Jun", "Jul", "Ago", "Set", "Out", "Nov", "Dez"],
                weekdays: ["Domingo", "Segunda", "Terça", "Quarta", "Quinta", "Sexta", "Sábado"]
            }
        });
    }
    $(".filter-mensal-processos").change(function () {
        var assunto_id = $("#select_asssunto_mensal_filter").val();
        var ano = $("#select_ano_mensal_filter").val();
        showLoading();
        graficoProcessosMensal(ano, assunto_id);
    });
    $(".filter-pizza-processos").change(function () {
        var assunto_id = $("#select_asssunto_filter").val();
        var interessado_id = $(".select_interessado").val();
        var responsavel_id = $("#select_responsavel_filter").val();
        showLoading();
        graficoPizzaProcessos('pieReceber', 'receber', responsavel_id, assunto_id, interessado_id);
        graficoPizzaProcessos('pieAberto', 'aberto', responsavel_id, assunto_id, interessado_id);
        graficoPizzaProcessos('pieVencidos', 'vencidos', responsavel_id, assunto_id, interessado_id);

    });
    graficoProcessosMensal(null, null);
    graficoPizzaProcessos('pieReceber', 'receber', null, null, null);
    graficoPizzaProcessos('pieAberto', 'aberto', null, null, null);
    graficoPizzaProcessos('pieVencidos', 'vencidos', null, null, null);
    //initSelect2Custom();
    /**************************************************************************************************************/
    /*================================        Localização Física    =====================================================*/
    /*************************************************************************************************************/

    $('#tabelaLocalizacaoFisica').DataTable({
        "serverSide": true,
        "ajax": app_path + "src/App/Ajax/LocalizacaoFisica/listar_server_side.php",
        "aaSorting": [[3, "asc"]],
        "bStateSave": true,
        "deferRender": true,
        "autoWidth": false,
        "initComplete": function (settings, json) {
            $(this).fadeIn('slow');
        },
        "createdRow": function (tr, tdsContent) {
            $(tr).attr('title', "Clique para visualizar detalhes da Localização ").attr("localizacao_fisica_id", tdsContent[0]).addClass("btn-visualizar-localizacao-fisica");
        },
        "aoColumnDefs": [
            {
                "sClass": "hidden",
                "aTargets": [0]
            },
            {
                "sClass": "text-center",
                type: 'date-uk',
                "aTargets": [3]
            },
            {
                "sClass": "text-center",
                bSortable: false,
                "aTargets": [9]
            },
            {
                "sClass": "hidden",
                "aTargets": [10]
            },
            {
                "sClass": "hidden",
                "aTargets": [11]
            },
            {
                "sClass": "hidden",
                "aTargets": [12]
            },
            {
                "sClass": "hidden",
                "aTargets": [13]
            },
            {
                "sClass": "hidden",
                "aTargets": [14]
            },
            {
                "sClass": "hidden",
                "aTargets": [15]
            },
            {
                "sClass": "hidden",
                "aTargets": [16]
            },
            {
                "sClass": "hidden",
                "aTargets": [17]
            }

        ]
    });
    /**************************************************************************************************************/
    /*================================        Interessados    =====================================================*/
    /*************************************************************************************************************/
    $("body").on('click', '.btn-pesquisar-interessado', function (e) {
        var $select = $(this).closest('.form-group').find('.select_interessado');
        e.preventDefault();
        showLoading();
        $.post(app_path + 'src/App/View/Interessado/pesquisar.php', function (response) {
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
    });

    $(".cpf").blur(function (e) {
        var tamanho = $(this).val().length;
        if (tamanho == 0) {
            return true;
        }
        if (tamanho <= 14) {
            return validarCPF($(this));
        }
        return true;
    });

    $("body").on("click", ".btn-visualizar-interessado", function (e) {
        e.preventDefault();
        var interessado_id = $(this).attr('interessado_id');
        showLoading();
        $.post(app_path + 'src/App/View/Interessado/visualizar.php', {interessado_id: interessado_id}, function (response) {
            createModal("interessadoModal", "Visualizar Interessado", response);
        }).done(function () {
            hideLoading();
        });
    });

    $("body").on("click", ".btn-converter", function (e) {
        e.stopPropagation();
        var href = $(this).attr('href');
        showLoading();
        window.location.href = href;
    });

    $('#tabelaInteressados').DataTable({
        "serverSide": true,
        "ajax": app_path + "src/App/Ajax/Interessado/listar_server_side.php",
        "aaSorting": [[1, "asc"]],
        "bStateSave": true,
        "deferRender": true,
        "autoWidth": false,
        "initComplete": function (settings, json) {
            $(this).fadeIn('slow');
        },
        "createdRow": function (tr, tdsContent) {
            $(tr).attr('title', "Clique para visualizar detalhes de " + tdsContent[1]).attr("interessado_id", tdsContent[0]).addClass("btn-visualizar-interessado");
        },
        "aoColumnDefs": [
            {
                "sClass": "text-center",
                "aTargets": [0]
            },
            {
                "sClass": "text-center",
                bSortable: false,
                "aTargets": [3]
            },
            {
                "bVisible": false,
                "aTargets": [4]
            },
            {
                "bVisible": false,
                "aTargets": [5]
            },
        ]
    });

    $('.dataTables_filter input[aria-controls="tabelaInteressados"]').unbind().keyup(function() {
        var value = $(this).val().replace(/\./g, '').replace(/\-/g, '');
        console.log(value);
        $('#tabelaInteressados').DataTable().search(value).draw()
    });
    /**************************************************************************************************************/
    /*================================        Notificações    =====================================================*/
    /*************************************************************************************************************/
    $('body').on('click', '.btn-excluir-notificacao', function (e) {
        e.preventDefault();
        var qtde_selecionadas = $('.sel-notificacao:checked').length;
        if (qtde_selecionadas > 0) {
            bootbox.confirm({
                title: "",
                message: "Deseja realmente excluir as notificações selecionadas?",
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
                        $.post(app_path + 'notificacao/excluir', $("#notificacoesTableForm").serialize(), function (response) {
                            showGrowMessage(response.tipo, response.msg);
                            if (response.tipo == 'success') {
                                window.location.reload();
                            }
                        }, 'json').done(function () {
                            hideLoading();
                        });

                    }
                }
            });
        } else {
            showGrowMessage('warning', "Selecione ao menos uma notificação para excluir.");
        }
    }).on('click', '.btn-arquivar-notificacao', function (e) {
        e.preventDefault();
        var qtde_selecionadas = $('.sel-notificacao:checked').length;
        if (!$(this).attr('notificacao_id') && qtde_selecionadas == 0) {
            showGrowMessage('warning', "Selecione ao menos uma notificação para arquivar.");
            return false;
        }
        var dados = $(this).attr('notificacao_id') ? {notificacao_id: $(this).attr('notificacao_id')} : $("#notificacoesTableForm").serialize();
        var mensagem = $(this).attr('notificacao_id') ? "Deseja realmente arquivar esta notificação?" : "Deseja realmente arquivar as notificações selecionadas?";
        bootbox.confirm({
            title: "",
            message: mensagem,
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
                    $.post(app_path + 'notificacao/arquivar', dados, function (response) {
                        showGrowMessage(response.tipo, response.msg);
                        if (response.tipo == 'success') {
                            window.location.reload();
                        }
                    }, 'json').done(function () {
                        hideLoading();
                    });

                }
            }
        });
    }).on('click', '#tabelaNotificacoes tbody tr td:not(.col-actions)', function (e) {
        e.preventDefault();
        var notificacao_id = $(this).closest('tr').attr('notificacao_id');
        if (notificacao_id != null) {
            showLoading();
            window.location.href = app_path + 'notificacao/visualizar/id/' + notificacao_id;
        } else {
            showGrowMessage('error', '#ID de notificação não encontrado.');
        }
    }).on('change', '#select_processo', function (e) {
        e.preventDefault();
        var tramite_id = $(this).val();
        showLoading();
        $.post(app_path + 'src/App/Ajax/Processo/buscar_responsavel.php', {tramite_id: tramite_id}, function (response) {
            $("#select_destinatario").val(response.id);
        }, 'json').done(function () {
            hideLoading();
        });
    });
    $("#tabelaNotificacoes").dataTable({
        "sDom": "<'row'<'col'l><'col'f>r>t<'row'<'col'i><'col'p>>",
        "aaSorting": [],
        "autoWidth": false,
        "bStateSave": true,
        "aoColumnDefs": [
            {
                bSortable: false,
                "aTargets": [0]
            },
            {
                type: 'date-uk',
                "aTargets": [5]
            },
            {
                type: 'date-uk',
                "aTargets": [6]
            },
            {
                bSortable: false,
                "aTargets": [9]
            }
        ]
    });
    responderForm();

    function responderForm() {
        $("#responderNotificacaoForm").validate({
            submitHandler: function (form) {
                var $modal = $(form).closest('.modal');
                var l = Ladda.create(form.querySelector('.ladda-button'));
                l.start();
                $.post(app_path + 'notificacao/responder', $(form).serialize(), function (response) {
                    if (response.tipo == 'success') {
                        $modal.modal('hide');
                    }
                    showGrowMessage(response.tipo, response.msg);
                }, 'json').done(function () {
                    l.stop();
                });
                return false;
            }
        });
    }

    /**************************************************************************************************************/
    /*================================        Processo    =====================================================*/
    /*************************************************************************************************************/
    initValidateFormAutenticarProcesso();

    function setUsuariosSetor($select, setor_id) {
        showLoading();
        $.post(app_path + 'src/App/Ajax/Setor/buscar_usuarios.php', {setor_id: setor_id}, function (response) {
            if (response.tipo == 'success') {
                var options = "<option value=''>Todos</option>";
                $.each(response.usuarios, function (index, usuario) {
                    options += "<option value='" + usuario.id + "'>" + usuario.nome + "</option>";
                });
                $select.html(options);
            } else {
                showGrowMessage(response.tipo, response.msg);
            }
        }, 'json').done(function () {
            hideLoading();
        });
    }

    $('body').on('change', '#select_setor_destino', function (e) {
        var setor_id = $(this).val();
        var $select_destino = $(this);
        var setor_id_before = $(this).attr('setor_id');
        var $sel_usuario = $(this).closest('tr').find('.usuario_destino_processo');
        var setor_destino_fluxograma = [];
        if (setor_id != "" && $("#divTomadaDecisao").length) {
            $("#divTomadaDecisao").hide();
        } else if (setor_id == "" && $("#divTomadaDecisao").length) {
            $("#divTomadaDecisao").show();
        }
        if ($(".setor_destino_fluxograma_id").length || $("select[name='assuntoProsseguir']").length ) {
            if (setor_id != setor_id_before || ($select_destino.val() && $("select[name='assuntoProsseguir']").length > 0 && !$("select[name='assuntoProsseguir']").val()))  {
                bootbox.confirm("Você está alterando o setor pré-definido no fluxograma. Deseja confirmar?", function (result) {
                    if (result) {
                        $(".setor_destino_fluxograma_id").each(function () {
                            setor_destino_fluxograma.push($(this).val());
                        });
                        
                        if ( $.inArray(setor_id, setor_destino_fluxograma) == -1) {
                            $("#divRequisitos").hide();
                            $(".divRequisitosProximoTramite").removeClass('hidden');
                        } else {
                            $("#divRequisitos").show();
                        }
                        setUsuariosSetor($sel_usuario, setor_id);
                    } else {
                        $select_destino.val(setor_id_before).trigger('change');
                        $("#devolverSetorOrigemCheck").prop('checked', false);
                    }
                });
            } else {
                $("#divRequisitos").show();
                $(".divRequisitosProximoTramite").addClass('hidden');
            }
        } else {
            setUsuariosSetor($sel_usuario, setor_id);
        }
    }).on('change', '#devolverSetorOrigemCheck', function (e) {
        var tramite_id = $(this).closest('form').find('input[type=hidden][name=tramite_id]').val();
        if ($(this).is(':checked')) {
            showLoading();
            $.post(app_path + 'tramite/buscarSetorAnterior', {tramite_id: tramite_id}, function (response) {
                if (response.tipo == 'success') {
                    $("#select_setor_destino").val(response.setor_anterior_id).trigger('change');
                    //prop('disabled', true).
                }
            }, 'json').done(function () {
                hideLoading();
            });
        } else {
            if ($('.setor_destino_fluxograma_id').length) {
                $("#select_setor_destino").val($('.setor_destino_fluxograma_id').val()).trigger('change');
                //prop('disabled', true)
            } else {
                //$("#select_setor_destino").prop("disabled", false);
            }
        }
    }).on('click', '.btn-cadastrar-apenso', function (e) {
        e.preventDefault();
        var processo_id = $(this).attr('processo_id');
        $.post(app_path + 'processo/setarInformacoes', $("#processoForm").serialize(), function (response) {
            showLoading();
            $.post(app_path + 'src/App/View/Processo/cadastrar_apenso.php', {processo_id: processo_id}, function (response) {
                createModal("cadastrarApensoModal", "Cadastrar Apenso", response, 'modal-lg');
                initSelect2Interessado();
                initSelect2Assunto();
                initSelect2();
                $("#processoApensoForm").validate({
                    submitHandler: function (form) {
                        var l = Ladda.create(form.querySelector('.ladda-button'));
                        l.start();
                        $.post($(form).attr('action'), $(form).serialize(), function (response) {
                            showGrowMessage(response.tipo, response.msg);
                            if (response.tipo == 'success') {
                                atualizarApensos(processo_id, $("#select_apensos").val());
                                $(form).closest('.modal').modal('hide');
                            }
                            l.stop();
                        }, 'json');
                        return false;
                    }
                });
            }).done(function () {
                hideLoading();
            });
        }, 'json');
    }).on('change', '#select_status_processo', function (e) {
        e.preventDefault();
        var is_arquivamento = $(this).find("option:selected").attr('is_arquivamento');
        if (is_arquivamento == 1) {
            $("#divLocalizacaoFisica").fadeIn();
        } else {
            $("#divLocalizacaoFisica").fadeOut();
        }
    });

    $('body').on('click', '#btn_gerar_processo', function (e) {
        e.preventDefault();
        let id = $(this).attr("processo_id");
        let closeLoader = true;
        showLoading();
        $.post(app_path + 'Processo/gerarProcesso',{id: id}, function (response) {
            showGrowMessage(response.tipo, response.msg);
            if (response.tipo == "success") {
                closeLoader = false;
                window.location.reload();
            }

        },"json"
        ).done(function () {
            if(closeLoader){
                hideLoading();
            }
        });
    })

    function checkInputRemessa() {
        if ($("#customCheckGTE").length) {
            if ($(".linha-destino").length > 1) {
                $("#customCheckGTE").attr('disabled', true);
            } else if ($(".linha-destino").length == 1) {
                $("#customCheckGTE").removeAttr('disabled');
            }
        }
    }

    $("body").on("click", ".select_entidade_pesquisa", function () {
        var $select = $(this);
        var entidade = $(this).attr('entidade');
        e.preventDefault();
        showLoading();
        $.post(app_path + 'src/App/View/' + entidade + '/pesquisa.php', function (response) {
            createModal(entidade + 'PesquisaModal', 'Pesquisar', response, 'modal-lg');
            initPesquisaEntidade(entidade, $select);
        }).done(function () {
            hideLoading();
        });
    });
    $("#select_processo").select2({
        placeholder: "Selecione um " + nomenclatura + " disponível",
        minimumInputLength: 1,
        language: "pt-BR",
        width: '100%',
        allowClear: true,
        multiple: false,
        beforeSend: function () {
            if (currentRequest != null) {
                currentRequest.abort();
            }
        },
        ajax: {
            url: app_path + "src/App/Ajax/Processo/listar_disponiveis_select2.php",
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
    initSelect2Assunto();
    initSelect2Interessado();
    $("#select_processo_arquivo").select2({
        placeholder: "Selecione",
        minimumInputLength: 1,
        language: "pt-BR",
        width: '100%',
        allowClear: true,
        beforeSend: function () {
            if (currentRequest != null) {
                currentRequest.abort();
            }
        },
        ajax: {
            url: app_path + "src/Core/Ajax/select2_ajax_response.php?entidade=Processo",
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
    $("#select_apensos").select2({
        placeholder: "Selecione",
        minimumInputLength: 1,
        language: "pt-BR",
        width: '100%',
        allowClear: true,
        multiple: true,
        beforeSend: function () {
            if (currentRequest != null) {
                currentRequest.abort();
            }
        },
        ajax: {
            url: app_path + "src/Core/Ajax/select2_ajax_response.php?entidade=Processo",
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
    $("body").on('change', '#select_apensos', function (e) {
        e.preventDefault();
        var processo_id = $("#processo").val();
//        processo_id = processo_id?processo_id: $("#processo_id").val();
        var apensos = $(this).val();
        
        atualizarApensos(processo_id, apensos);
    }).on('change', '#select_processo_arquivo', function (e) {
        var processo_id = $(this).val();
        var $form = $("#localizacaoFisicaForm");
        $.post(app_path + 'src/App/Ajax/Processo/buscar.php', {processo_id: processo_id}, function (processo) {
            $form.find('#numeroDocumento').val(processo.numero);
            $form.find('#exercicioDocumento').val(processo.exercicio);
            $form.find('#dataDocumento').val(processo.data);
            $form.find('#ementa').val(processo.objeto);
        }, 'json');
    });
    $("#select_exercicio").change(function () {
        var exercicio = $(this).val();
        $.post(app_path + 'src/App/Ajax/Processo/mudar_exercicio.php', {exercicio: exercicio}, function (response) {
            showGrowMessage('success', "Exercício de referência alterado.");
            window.location.reload();
        });
    });
    $('body').on('click', '.check-tarefa', function (e) {
        var $label = $(this).closest('label');
        if ($(this).is(":checked")) {
            $label.css('text-decoration', 'line-through');
        } else {
            $label.css('text-decoration', 'none');
        }
    });

    $(".btn-atualizar-tabela").click(function () {
        var $tabela = $(this).closest('.processo-table').find('.tabelaProcessos');
        //showLoading();
        //$tabela().dataTable().api().ajax.reload(null, false);
        $tabela.dataTable().api().ajax.reload();
        atualizarContadoresProcesso();
    });
    $('.tabelaProcessos').each(function () {
        var col = $(this).children('tr').children('td').length;
        var url = $(this).attr('url');
        var verificar_vencimento = $(this).attr('verificar_vencimento');
        var hide_checkbox = $(this).attr('hide_checkbox');
        var hide_to_contribuintes = $(this).attr('tipo_listagem') == 'contribuintes' ? 'hidden' : '';

        $(this).dataTable({
            "sDom": "<'row'<'col'l><'col invisible'f>r>t<'row'<'col'i><'col'p>>",
            "stateSave": true,
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Tudo"]],
            "pageLength": 10,
            "aaSorting": [[7, "desc"]],
            "deferRender": true,
            "autoWidth": false,
            "serverSide": true,
            "processing": true,
            "ajax": url,
            "fnRowCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                $(nRow).attr('id', "visualizar:" + aData[0]).attr('title', aData[Object.keys(aData).length-1]);
            },
            "initComplete": function (settings, json) {

                var $tabela = $(this).dataTable();
                //$("#qtde_" + status_id).text(settings.fnRecordsTotal());
                var $remessa = $(this).closest('.processo-table').find('.numero_remessa');
                var $numero = $(this).closest('.processo-table').find('.numero_processo');
                var $assunto = $(this).closest('.processo-table').find('.assunto_processo');
                var $interessado = $(this).closest('.processo-table').find('.interessado_processo');
                var $setor = $(this).closest('.processo-table').find('.setor_processo');
                var $data = $(this).closest('.processo-table').find('.data_tramite_processo');
                var $status = $(this).closest('.processo-table').find('.status_processo');
                $numero.keyup(function () {
                    $tabela.fnFilter($.trim($(this).val()), 2);
                });
                $assunto.keyup(function () {
                    $tabela.fnFilter($.trim($(this).val()), 3);
                });
                $interessado.keyup(function () {
                    $tabela.fnFilter($.trim($(this).val()), 4);
                });
                $setor.keyup(function () {
                    $tabela.fnFilter($.trim($(this).val()), 6);
                });
                $data.change(function () {
                    $tabela.fnFilter($(this).val(), 7);
                });
                $status.change(function () {
                    $tabela.fnFilter($(this).val(), 10);
                });
                $remessa.keyup(function () {
                    $tabela.fnFilter($.trim($(this).val()), 15);
                });
                var search;
                for (var iCol = 0; iCol < settings.aoPreSearchCols.length; iCol++) {
                    if (settings.aoPreSearchCols[iCol].sSearch) {
                        search = settings.aoPreSearchCols[iCol].sSearch;
                        switch (iCol) {
                            case 15:
                                $remessa.val(search);
                                break;
                            case 2:
                                $numero.val(search);
                                break;
                            case 3:
                                $assunto.val(search);
                                break;
                            case 4:
                                $interessado.val(search);
                                break;
                            case 6:
                                $setor.val(search);
                                break;
                            case 7:
                                $data.val(search);
                                break;
                            case 9:
                                $status.val(search);
                                break;

                        }
                    }
                }
            },
            "aoColumnDefs": [
                {
                    bVisible: false,
                    "aTargets": [0]
                },
                {
                    "sClass": "col-actions vertical-middle " + hide_checkbox,
                    bSortable: false,
                    "aTargets": [1]
                },
                {
                    "sClass": "text-center "+hide_to_contribuintes,
                    "iDataSort": 0,
                    "aTargets": [2],
                },
                {
                    "sClass": "text-left",
                    "aTargets": [3]
                },
                {
                    "sClass": "text-left",
                    "aTargets": [4]
                },
                {
                    "sClass": "text-left",
                    "aTargets": [5]
                },
                {
                    "sClass": "text-left",
                    "aTargets": [6]
                },
                {
                    type: 'date-uk',
                    "aTargets": [7]
                },
                {
                    type: 'date-uk',
                    "aTargets": [8],
                    "sClass": "text-center "+hide_to_contribuintes,
                    "mRender": function (data, type, full) {
                        if (verificar_vencimento == 'true' && data) {
                            var vencimento = new Date();
                            var vencimento_temp = data.split('/');
                            vencimento.setFullYear(vencimento_temp[2]);
                            vencimento.setMonth(vencimento_temp[1] - 1);
                            vencimento.setDate(vencimento_temp[0]);
                            //<div style="width: 15px;height: 15px;float: left" class="{if $notificacao->getPrazoResposta() gt $data_atual}bg-success{else}bg-danger{/if}"></div>
                            if (new Date().getTime() > vencimento.getTime())
                                return '<div style="width: 15px;height: 15px;float: left" class="bg-danger"></div>' + data;
                            else if (new Date().getTime() == vencimento.getTime())
                                return '<div style="width: 15px;height: 15px;float: left" class="bg-info"></div>' + data;
                            else
                                return '<div style="width: 15px;height: 15px;float: left" class="bg-success"></div>' + data;
                        } else {
                            return data;
                        }

                    }
                },
                {
                    type: 'date-uk',
                    "aTargets": [9],
                    "sClass": "text-center "+hide_to_contribuintes,
                    "mRender": function (data, type, full) {
                        if (verificar_vencimento == 'true') {
                            var vencimento = new Date();
                            var vencimento_temp = data.split('/');
                            vencimento.setFullYear(vencimento_temp[2]);
                            vencimento.setMonth(vencimento_temp[1] - 1);
                            vencimento.setDate(vencimento_temp[0]);
                            //<div style="width: 15px;height: 15px;float: left" class="{if $notificacao->getPrazoResposta() gt $data_atual}bg-success{else}bg-danger{/if}"></div>
                            if (new Date().getTime() > vencimento.getTime())
                                return '<div style="width: 15px;height: 15px;float: left" class="bg-danger"></div>' + data;
                            else if (new Date().getTime() == vencimento.getTime())
                                return '<div style="width: 15px;height: 15px;float: left" class="bg-info"></div>' + data;
                            else
                                return '<div style="width: 15px;height: 15px;float: left" class="bg-success"></div>' + data;
                        } else {
                            return data;
                        }

                    }
                },
                {
                    "sClass": 'col-actions vertical-middle ',
                    "aTargets": [10]
                },
                {
                    "sClass": 'text-center '+hide_to_contribuintes,
                    "aTargets": [11]
                },
                {
                    "sClass": "col-actions vertical-middle ",
                    bSortable: false,
                    "aTargets": [col - 1]
                }
            ]
        }).on('draw.dt', () => buscarAssinaturaStatus());
    });
    $('.tabelaProcessosContribuintes').each(function () {
        let url = $(this).attr('url');
        $(this).dataTable({
            "sDom": "<'row'<'col'l><'col invisible'f>r>t<'row'<'col'i><'col'p>>",
            "stateSave": true,
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Tudo"]],
            "pageLength": 100,
            "aaSorting": [[3, "desc"]],
            "deferRender": true,
            "autoWidth": false,
            "serverSide": true,
            "processing": true,
            "ajax": url,
            "fnRowCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                $(nRow).attr('id', "visualizar:" + aData[0]).attr('title', aData[aData[Object.keys(aData).length-1]]);
            },
            "initComplete": function (settings, json) {

                let $tabela = $(this).dataTable();
                let $numero = $(this).closest('.processo-table').find('.numero_processo');
                let $assunto = $(this).closest('.processo-table').find('.assunto_processo');
                let $data = $(this).closest('.processo-table').find('.data_abertura_processo');
                let $status = $(this).closest('.processo-table').find('.status_processo');
                $numero.keyup(function () {
                    $tabela.fnFilter($.trim($(this).val()), 1);
                });
                $assunto.keyup(function () {
                    $tabela.fnFilter($.trim($(this).val()), 2);
                });
                $data.change(function () {
                    $tabela.fnFilter($(this).val(), 3);
                });
                $status.change(function () {
                    $tabela.fnFilter($(this).val(), 4);
                });
                let search;
                for (var iCol = 0; iCol < settings.aoPreSearchCols.length; iCol++) {
                    if (settings.aoPreSearchCols[iCol].sSearch) {
                        search = settings.aoPreSearchCols[iCol].sSearch;
                        switch (iCol) {
                            case 1:
                                $numero.val(search);
                                break;
                            case 2:
                                $assunto.val(search);
                                break;
                            case 3:
                                $data.val(search);
                                break;
                            case 4:
                                $status.val(search);
                                break;

                        }
                    }
                }
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
                    "sClass": "text-left",
                    "aTargets": [2]
                },
                {
                    type: 'date-uk',
                    "aTargets": [3]
                },
                {
                    "sClass": "text-center",
                    "aTargets": [4]
                },
                {
                    "sClass": "text-center",
                    "aTargets": [5]
                },
            ]
        });
    });

    $("#processosEmAbertoForm").validate({
        submitHandler: function (form) {
            var modal_title = $(this.submitButton).attr("title");
            var modal_id = $(this.submitButton).attr("modal_id");
            var $tabela = $('.tabelaProcessos');
            var qtde_selecionados = $('.check-arquivar-processo:checked').length;
            if (qtde_selecionados > 0) {
                showLoading();
                $.post($(form).attr('action'), $(form).serialize(), function (response) {
                        createModal(modal_id, modal_title, response, 'modal-elg');
                        if ($("#tramitarProcessoForm").length) {
                            initValidateTramitarProcessoForm($tabela);
                        } else if ($("#arquivarProcessoMassaForm").length) {
                            $("#arquivarProcessoMassaForm").validate({
                                submitHandler: function (form) {
                                    var l = Ladda.create(form.querySelector('.ladda-button'));
                                    l.start();
                                    $.post($(form).attr('action'), $(form).serialize(), function (response) {
                                        if (response.tipo == 'success') {
                                            $(form).closest('.modal').modal('hide');
                                            $("#tabelaProcessosTramitar").dataTable().api().ajax.reload(null, false);
                                            $(".marcaTodosTabela").prop('checked', false);
                                            atualizarContadoresProcesso();
                                        }
                                        showGrowMessage(response.tipo, response.msg);
                                        l.stop();
                                    }, 'json');
                                    return false;
                                }
                            });
                        }
                    }
                ).done(function () {
                    hideLoading();
                });
            } else {
                showAlert("Selecione ao menos um " + nomenclatura + " para continuar.");
            }
            return false;
        }
    });
    $("#receberProcessosForm").validate({
        submitHandler: function (form) {
            var l = Ladda.create(form.querySelector('.ladda-button'));
            var qtde_selecionados = $('.check-receber-processo:checked').length;
            if (qtde_selecionados > 0) {
                bootbox.confirm({
                    title: "",
                    message: "Deseja realmente receber o(s) " + nomenclatura + "(s) selecionado(s)?",
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
                            l.start();
                            $.post($(form).attr('action'), $(form).serialize(), function (response) {
                                if (response.tipo == 'success') {
                                    $("#tabelaProcessosReceber").dataTable().api().ajax.reload(null, false);
                                    $(".marcaTodosTabela").prop('checked', false);
                                    atualizarContadoresProcesso();
                                }
                                l.stop();
                                showGrowMessage(response.tipo, response.msg);
                            }, 'json');
                        }
                    }
                });
            } else {
                showAlert("Selecione ao menos um " + nomenclatura + " para continuar.");
            }
            return false;
        }
    });
    $("#formPesquisaProcesso").validate({
        submitHandler: function (form) {
            var l = Ladda.create(form.querySelector('.ladda-button'));
            l.start();
            $.post($(form).attr('action'), $(form).serialize(), function (response) {
                l.stop();
                createModal("pesquisaModal", "Resultados da pesquisa:", response, 'modal-lg');
                initTabelaResumoProcessos();
            });
            return false;
        }
    });
    
    $("#formPesquisaProcesso").find("button[type='submit']").removeAttr("disabled");
    
    $("#formPesquisaAnexoProcesso").validate({
        submitHandler: function (form) {
            var l = Ladda.create(form.querySelector('.ladda-button'));
            l.start();
            $.post($(form).attr('action'), $(form).serialize(), function (response) {
                l.stop();
                createModal("pesquisaAnexoModal", "Resultados da pesquisa:", response, 'modal-lg');
                initTabelaPesquisaAnexos();
            });
            return false;
        }
    });
    
    $("#formPesquisaAnexoProcesso").find("button[type='submit']").removeAttr("disabled");
    $("body").on('change', '#status_processo_todos', function () {
        $(".status-processo").val($(this).val());
    });
    $('#processoForm').validate({
        ignore: ":hidden, .select2-search__field, .ignore-validate",
        messages: {
            setor_origem_id: {required: "*Selecione o setor de origem do " + nomenclatura},
            assunto_id: {required: "*Selecione o assunto do " + nomenclatura},
            interessado_id: {required: "*Selecione o interessado do " + nomenclatura},
            dataVencimento: {required: "*Informe a data de vencimento do " + nomenclatura},
            objeto: {required: "* Descreva do que se trata o " + nomenclatura}

        },
        submitHandler: function (form) {
            let acao = $(form).find("#acao_processo").val();
            let processo_externo = $(form).find("#processo_externo").val();
            $(form).ajaxSubmit({
                dataType: 'json',
                beforeSubmit: function () {
                    showLoading();
                },
                success: function (response) {
                    if (response.tipo === 'success') {
                        if (acao === 'inserir') {
                            window.location.href = app_path + (processo_externo ? 'Contribuinte':'Processo') +'/finalizado/id/' + response.objeto_id;
                        } else {
                            hideLoading();
                        }
                    } else {
                        hideLoading();
                    }
                    showGrowMessage(response.tipo, response.msg);
                }
            });
            return false;
        }
    });

    var es;

    function startOCRProcesso(processo_id, acao) {
        $("#divProgressBox").addClass("progress-box-on");
        es = new EventSource(app_path + 'Processo/realizarOCR/processo/' + processo_id);
        //a message is received
        es.addEventListener('message', function (e) {
            hideLoading();
            var result = JSON.parse(e.data);
            addLog(result.message);
            $("#divProgress").show();
            if (e.lastEventId == 'CLOSE') {
                $("#divProgressBox").removeClass("progress-box-on");
                es.close();
                var $pBar = $('#progressor_arquivos');
                $pBar.attr('aria-valuenow', 100);
                $pBar.css('width', '100%');
                var $pBar_c = $('#progressor_clientes');
                $pBar_c.attr('aria-valuenow', 100);
                $pBar_c.css('width', '100%');
                if (acao == 'inserir') {
                    window.location.href = app_path + 'Processo/finalizado/id/' + processo_id;
                } else {
                    window.location.reload();
                }
            } else {
                $("#divProgressBox").addClass("progress-box-on");
                var aux = e.lastEventId.split('.');
                if (aux[1]) {
                    var $pBar = $('#progressor_arquivos');
                    $pBar.attr('aria-valuenow', result.progress);
                    $pBar.css('width', result.progress + '%');
                    $('#percentage_arquivos').text(result.progress + "%");
                } else {
                    $("#spanArquivos").html('<i class="fa fa-spin fa-circle-o-notch fa-fw"></i> <strong>Analisando imagens</strong><br/> ' + result.message);
                    var $pBar_c = $('#progressor_clientes');
                    if (result.progress > 0) {
                        $pBar_c.attr('aria-valuenow', result.progress);
                        $pBar_c.css('width', result.progress + '%');
                        $('#percentage_clientes').text(result.progress + "%");
                    }
                }
            }
        });
        es.addEventListener('error', function (e) {
            $("#divProgressBox").removeClass("progress-box-on");
            stopTask();
        });
    }

    function stopTask() {
        addLog('Ocorreu um erro: ' + es.readystate);
        es.close();
    }

    function addLog(message) {
        var r = document.getElementById('results');
        r.innerHTML = message;
        r.scrollTop = r.scrollHeight;
    }

    if (jQuery().bootstrapWizard) {
        $('#rootwizard').bootstrapWizard({
            onNext: function (tab, navigation, index) {
                var assunto_id = $('.select_assunto').select2('val');
                if ($('#rootwizard').attr("processo_externo") != 1){
                    var setor_origem_id = $('#select_setor_origem').select2('val');
                    if (index == 1) {
                        if (!$("#processoForm").valid()) {
                            return false;
                        }
                        setarInformacoesProcesso();
                    } else if (index == 2) {
                        if (!$("#processoForm").valid())
                            return false;
                        //Processa requisitos para abertura
                        showLoading();
                        $.post(app_path + 'src/App/View/Tramite/requisitos.php', {
                            assunto_id: assunto_id,
                            numero_fase: 1,
                            setor_id: null
                        }, function (response) {
                            $("#tabRequisitos").html(response).fadeIn();
                            initFormRules();
                            initSelect2Processos();
                        }).done(function () {
                            hideLoading();
                        });
                        $('html, body').animate({scrollTop: 0}, 'slow');
                        $("#btn_submit").hide();
                    } else if (index == 3) {
                        if (!$("#processoForm").valid())
                            return false;
                        //Processar primeiro trâmite
                        showLoading();
                        $.post(app_path + 'src/App/View/Tramite/tramitar.php', {
                            assunto_id: assunto_id,
                            setor_origem_id: setor_origem_id,
                            numero_fase: 1
                        }, function (response) {
                            $("#tabTramitar").html(response).fadeIn();
                            initSelect2Tree();
                            initDatePicker();
                        }).done(function () {
                            hideLoading();
                        });
                        $("#btn_submit").show();
                    } else {
                        $("#btn_submit").hide();
                    }
                }else{
                    let setor_origem_id = $('#rootwizard').attr("setor_origem_id");
                    if (index == 1) {
                        if (!$("#processoForm").valid()) {
                            return false;
                        }
                        setarInformacoesProcesso(false);
                        //Processa requisitos para abertura
                        showLoading();
                        $.post(app_path + 'src/App/View/Tramite/requisitos.php', {
                            assunto_id: assunto_id,
                            numero_fase: 1,
                            setor_id: null
                        }, function (response) {
                            $("#tabRequisitos").html(response).fadeIn();
                            initFormRules();
                            initSelect2Processos();
                        }).done(function () {
                            hideLoading();
                        });
                        $('html, body').animate({scrollTop: 0}, 'slow');
                        $("#btn_submit").hide();
                    }else if (index == 2) {
                        if (!$("#processoForm").valid())
                            return false;
                        //Processar primeiro trâmite
                        showLoading();
                        $.post(app_path + 'src/App/View/Tramite/tramitar.php', {
                            assunto_id: assunto_id,
                            setor_origem_id: setor_origem_id,
                            numero_fase: 1,
                            tramite_inicial_contribuinte: true
                        }, function (response) {
                            $("#tabTramitar").html(response).fadeIn();
                            initSelect2Tree();
                            initDatePicker();
                        }).done(function () {
                            hideLoading();
                        });
                        $("#btn_submit").show();
                    }
                }
            },
            onTabClick: function (tab, navigation, index) {
                return false;
            }
        });
    }
    $("#processoForm,#processoApensoForm").on('change', '.select_assunto,.data_abertura', function () {
        var data_abertura = $(this).closest('form').find('.data_abertura').val();
        var assunto_id = $(this).closest('form').find(".select_assunto").val();
        buscarPrazoAssunto(assunto_id, data_abertura);
    });
    $("body").on('click', '.tabelaProcessos tbody tr td:not(.col-actions),.tabelaListaProcessos tbody tr td:not(.col-actions),#tabelaControleVencimentos tbody tr td:not(.col-actions),.tabelaVencimentosProximo tbody tr', function (e) {
        var processo_id = $(this).closest('tr').attr('id').split(":")[1];
        visualizarProcesso(processo_id);
    }).on('click', '.btn-receber-processo', function (e) {
        e.preventDefault();
        var processo = $(this).attr('processo');
        var setor_atual = $(this).attr('setor_atual');
        var href = $(this).attr('href');
        let reload_page = $(this).attr("reload_page") !== undefined;
        var $tabela = $(this).closest('.tabelaProcessos');
        bootbox.confirm({
            title: "",
            message: "Deseja realmente receber o " + nomenclatura + " <strong>" + processo + "</strong> no setor <strong>" + setor_atual + "</strong>?",
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
                        if (response.tipo == 'success' || response.tipo == 'warning' ) {
                            if(reload_page){
                                window.location.reload()
                            }else{
                                $tabela.dataTable().api().ajax.reload(null, false);
                                atualizarContadoresProcesso();
                            }
                        }
                        showGrowMessage(response.tipo, response.msg);
                    }, 'json').done(function () {
                        if(!reload_page){
                            hideLoading();
                        }
                    });
                }
            }
        });
    }).on('click', '.btn-cancelar-envio', function (e) {
        e.preventDefault();
        var tramite_id = $(this).attr('tramite_id');
        var $tabela = $(this).closest('.tabelaProcessos');
        bootbox.confirm({
            message: "Deseja realmente cancelar esse envio?",
            buttons: {
                confirm: {
                    label: 'Sim',
                    className: 'btn-success'
                },
                cancel: {
                    label: 'Não',
                    className: 'btn-danger'
                }
            },
            callback: function (result) {
                if (result) {
                    tramitar(tramite_id, '', '', $tabela, true);
                }
            }
        });

    }).on('click', '.btn-tramitar-processo', function (e) {
        e.preventDefault();
        var tramite_id = $(this).attr('tramite_id');
        var processo = $(this).attr('processo');
        var tramites = $(this).attr('tramites');
        var $tabela = $(this).closest('.tabelaProcessos');
        tramitar(tramite_id, tramites, processo, $tabela, false);
    }).on('click', '.btn-recusar-processo', function (e) {
        e.preventDefault();
        var tramite_id = $(this).attr('tramite_id');
        var processo = $(this).attr('processo');
        var $tabela = $(this).closest('.tabelaProcessos');
        let reload_page = $(this).attr("reload_page") !== undefined;
        showLoading();
        $.post(app_path + "src/App/View/Tramite/devolver.php", {tramite_id: tramite_id}, function (response) {
            createModal("devolverProcessoModal", "Recusar " + nomenclatura + " nº " + processo, response, 'modal-lg');
            initSelect2Tree();
            $("#devolverProcessoForm").validate({
                ignore: ":hidden",
                submitHandler: function (form) {
                    var l = Ladda.create(form.querySelector('.ladda-button'));
                    l.start();
                    $.post($(form).attr('action'), $(form).serialize(), function (response) {
                        if (response.tipo == 'success') {
                            $("#devolverProcessoModal").modal('hide');
                            if(reload_page){
                                window.location.reload();
                            }else{
                                $tabela.dataTable().api().ajax.reload(null, false);
                                atualizarContadoresProcesso();
                            }
                        }
                        showGrowMessage(response.tipo, response.msg);
                    }, 'json').done(function () {
                        l.stop();
                        if(reload_page){
                            showLoading();
                        }
                    });
                    return false;
                }
            });
        }).done(function () {
            hideLoading();
        });
    }).on('click', '.btn-devolver-processo', function (e) {
        e.preventDefault();
        var tramite_id = $(this).attr('tramite_id');
        var processo = $(this).attr('processo');
        var $tabela = $(this).closest('.tabelaProcessos');
        let reload_page = $(this).attr('reload_page') !== undefined;
        showLoading();
        $.post(app_path + "src/App/View/Tramite/devolver_origem.php", {tramite_id: tramite_id}, function (response) {
            createModal("devolverProcessoModal", "Devolver à Origem " + nomenclatura + " nº " + processo, response, 'modal-lg');
            initSelect2Tree();
            
            $("#devolverProcessoForm").validate({
                ignore: ":hidden",
                submitHandler: function (form) {
                    var l = Ladda.create(form.querySelector('.ladda-button'));
                    var gerar_guia = $(form).find('input[type=checkbox][name=gerar_guia_tramitacao]').is(':checked');
                    l.start();
                    $.post($(form).attr('action'), $(form).serialize(), function (response) {
                        showGrowMessage(response.tipo, response.msg);
                        if (response.tipo == 'success') {
                            if(reload_page){
                                window.location.reload();
                            }else{
                                $("#devolverProcessoModal").modal('hide');
                                $tabela.dataTable().api().ajax.reload(null, false);
                                atualizarContadoresProcesso();
                            }
                            if (gerar_guia) {
                                window.open(app_path + 'remessa/imprimir/' + response.objeto_id);
                            }
                        }
                    }, 'json').done(function () {
                        l.stop();
                        if(reload_page){
                            showLoading();
                        }
                    });
                    return false;
                }
            });
        }).done(function () {
            hideLoading();
        });
    }).on('click', '.btn-arquivar-processo', function (e) {
        e.preventDefault();
        var processo_id = $(this).attr('processo_id');
        var processo = $(this).attr('processo');
        var $tabela = $(this).closest('.tabelaProcessos');
        let reload_page = $(this).attr("reload_page") !== undefined;
        showLoading();
        $.post(app_path + "src/App/View/Processo/arquivar.php", {processo_id: processo_id}, function (response) {
            createModal("arquivarProcessoModal", "Arquivar " + nomenclatura + " nº " + processo, response, 'modal-lg');
            initSelect2();
            $("#arquivarProcessoForm").validate({
                submitHandler: function (form) {
                    var l = Ladda.create(form.querySelector('.ladda-button'));
                    l.start();
                    $.post($(form).attr('action'), $(form).serialize(), function (response) {
                        if (response.tipo == 'success') {
                            $("#arquivarProcessoModal").modal('hide');
                            if ($tabela.length) {
                                $tabela.dataTable().api().ajax.reload(null, false);
                                atualizarContadoresProcesso();
                            } else {
                                window.location.reload();
                            }
                        }
                        showGrowMessage(response.tipo, response.msg);
                        l.stop();
                    }, 'json');
                    if(reload_page){
                        showLoading();
                    }
                    return false;
                }
            });
        }).done(function () {
            hideLoading();
        });
    }).on('click', '.btn-excluir-processo', function (e) {
        e.preventDefault();
        var processo = $(this).attr('processo');
        var href = $(this).attr('href');
        var $tabela = $(this).closest('.tabelaProcessos');
        bootbox.confirm({
            title: "",
            message: "Deseja realmente excluir o " + nomenclatura + " <strong>" + processo + "</strong>?",
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
                        if (response.tipo == 'success') {
                            if ($tabela.length) {
                                $tabela.dataTable().api().ajax.reload(null, false);
                            }
                            atualizarContadoresProcesso();
                        }
                        showGrowMessage(response.tipo, response.msg);
                    }, 'json').done(function () {
                        hideLoading();
                    });
                }
            }
        });
    }).on('click', '.btn-alterar-status', function (e) {
        e.preventDefault();
        var processo = $(this).attr('processo');
        var tramite_id = $(this).attr('tramite_id');
        var $tabela = $(this).closest('.tabelaProcessos');
        showLoading();
        $.post(app_path + 'src/Core/Ajax/verificar_permissao.php', {
            entidade: 'Processo',
            acao: 'editar'
        }, function (permissao) {
            if (permissao) {
                $.post(app_path + 'src/App/View/Tramite/alterar_status.php', {tramite_id: tramite_id}, function (response) {
                    createModal("statusTramiteModal", "Status " + nomenclatura + " nº " + processo, response, 'modal-lg');
                    $("#alterarStatusForm").validate({
                        submitHandler: function (form) {
                            var l = Ladda.create(form.querySelector('.ladda-button'));
                            l.start();
                            $.post($(form).attr('action'), $(form).serialize(), function (response) {
                                if (response.tipo == 'success') {
                                    $tabela.dataTable().api().ajax.reload(null, false);
                                    atualizarContadoresProcesso();
                                    $("#statusTramiteModal").modal('hide');
                                }
                                showGrowMessage(response.tipo, response.msg);
                                l.stop();
                            }, 'json');
                            return false;
                        }
                    });
                    initSelect2();
                }).done(function () {
                    hideLoading();
                });
            } else {
                showGrowMessage('warning', "Desculpe, mas você não permissão para executar essa ação.");
                hideLoading();
            }
        });
    });
    /**************************************************************************************************************/
    /*================================        Fluxograma    =====================================================*/
    /*************************************************************************************************************/
    $("#tabelaSetores").on('click', '.btn-ver-usuarios-setor', function (e) {
        e.preventDefault();
        var setor = $(this).attr('setor');
        var setor_id = $(this).attr('setor_id');
        showLoading();
        $.post(app_path + 'src/App/View/Setor/usuarios.php', {setor_id: setor_id}, function (response) {
            createModal("usuariosSetorModal", "Usuários do Setor: " + setor, response);
        }).done(function () {
            hideLoading();
        });
    });
    /**************************************************************************************************************/
    /*================================        Fluxograma    =====================================================*/
    /*************************************************************************************************************/
    initTabelaRequisitosSortable();
    $("body").on('click', '.btn-cadastrar-entidade', function () {
        var objeto_ref_id = $(this).attr('objeto_ref_id');
        var entidade = $(this).attr('entidade');
        var modal_id = entidade + "Modal";
        var $button = $(this);
        showLoading();
        $.post(app_path + 'src/App/View/' + entidade + '/cadastrar.php', {objeto_ref_id: objeto_ref_id}, function (response) {
            createModal(modal_id, "Cadastrar " + entidade, response);
            initValidateFormRequisito(entidade, modal_id, objeto_ref_id, $button);
            initFormRules();
        }).done(function () {
            hideLoading();
        });
    }).on('click', '.btn-editar-entidade', function (e) {
        e.preventDefault();
        var aux = $(this).attr('entidade_id').split(':');
        var entidade_id = aux[0];
        var indice = aux[1] ? aux[1] : null;
        var entidade = $(this).attr('entidade');
        var modal_id = entidade + "Modal";
        var modal_size = $(this).attr('modal-size');
        var objeto_ref_id = $(this).attr('objeto_ref_id');
        showLoading();
        $.post(app_path + 'src/App/View/' + entidade + '/editar.php', {
            entidade_id: entidade_id,
            indice: indice
        }, function (response) {
            createModal(modal_id, "Editar " + entidade, response, modal_size);
            initValidateFormRequisito(entidade, modal_id, objeto_ref_id);
            initFormRules();
        }).done(function () {
            hideLoading();
        });
    }).on('click', '.btn-excluir-entidade', function (e) {
        e.preventDefault();
        var href = $(this).attr('href');
        var entidade = $(this).attr('entidade');
        var objeto_ref_id = $(this).attr('objeto_ref_id');
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
                    $.post(href, {ajax: true}, function (response) {
                        if (response.tipo == 'success') {
                            atualizarListagemRequisitos(entidade, objeto_ref_id);
                        }
                        showGrowMessage(response.tipo, response.msg);
                    }, 'json').done(function () {
                        hideLoading();
                    });

                }
            }
        });
        e.stopPropagation();
    }).on('click', '.btn-reativar-entidade', function (e) {
        e.preventDefault();
        var href = $(this).attr('href');
        var entidade = $(this).attr('entidade');
        var objeto_ref_id = $(this).attr('objeto_ref_id');
        bootbox.confirm({
            title: "",
            message: "Deseja realmente reativar este registro?",
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
                        if (response.tipo == 'success') {
                            atualizarListagemRequisitos(entidade, objeto_ref_id);
                        }
                        showGrowMessage(response.tipo, response.msg);
                    }, 'json').done(function () {
                        hideLoading();
                    });
                }
            }
        });
        e.stopPropagation();
    }).on("change", "#select_tipo_campo", function () {
        var classe = $(this).val();
        var $options_filter = $('#select_mascara_campo option.' + classe);
        $('#select_mascara_campo option:not(.nenhuma)').hide();
        if (classe == 'caixa-selecao') {
            $("#divValoresSelecao").fadeIn();
            $("#divTemplate").fadeOut();
            $("#divArquivosMultiplos").fadeOut();
        } else if (classe == 'arquivo') {
            $("#divTemplate").fadeIn();
            $("#divValoresSelecao").fadeOut();
            $("#divArquivosMultiplos").fadeOut();
        } else if (classe == 'arquivo-multiplo'){
            $("#divArquivosMultiplos").fadeIn();
            $("#divValoresSelecao").fadeOut();
            $("#divTemplate").fadeOut();
        }else {
            $("#divValoresSelecao").fadeOut();
            $("#divTemplate").fadeOut();
            $("#divArquivosMultiplos").fadeOut();
            $options_filter.show();
            $('#select_mascara_campo').val("");
            if ($options_filter.length == 1) {
                $options_filter.attr('selected', true);
            }
        }
    });
    $("#tabelaFases").find("tbody").first().sortable({
        placeholder: "ui-state-highlight",
        update: function (event, ui) {
            var data = $(this).sortable('serialize');
            reaorganizarFases();
        }
    });

    function reaorganizarFases() {
        var i = 1;
        $('.linha-fase').each(function () {
            $(this).find('.fase-numero').val(i);
            $(this).find('.fase-numero-text').text(i);
            $(this).find('.tabela-fase').find('input').each(function () {
                var name = $(this).attr('name');
                var aux = name.split('[');
                $(this).attr('name', aux[0] + "[" + (i - 1) + "][]");
            });
            i++;
        });
    }

    $(".btn-adicionar-fase").click(function () {
        var setores_id = $("#setor_id").val().split(',');
        var setores_sel = $("#jstree").jstree('get_selected', true);
        var fase_atual = $('.linha-fase').length + 1;
        var indice_fase = fase_atual - 1;
        var $linha = "<tr class='linha-fase'>"
            + "<th class='text-center bg-light' style='vertical-align: middle'>";
        $linha += "<input type='hidden' class='fase-id' name='fase_id[]' value=''/>";
        $linha += "<input type='hidden' class='fase-numero' name='fase[]' value='" + fase_atual + "'/>";
        $linha += "<span class='fase-numero-text'>" + fase_atual + "</span>ª";
        $linha += "</th>";
        $linha += "<td style='padding: 0px;'>";
        $linha += "<table class='tabela-fase table table-sm' style='margin-bottom: 0px'><tbody>";
        for (var i = 0; i < setores_sel.length; i++) {
            $linha += "<tr style='cursor: move'>";
            $linha += "<td>";
            $linha += '<input type="hidden" name="setor_fase_id[' + indice_fase + '][]" value=""/>';
            $linha += '<input type="hidden" name="setor_id[' + indice_fase + '][]" value="' + setores_id[i] + '"/>';
            $linha += $.trim(setores_sel[i].text);
            $linha += "</td>";
            $linha += "<td style='width: 80px'>";
            $linha += '<input placeholder="Prazo" type="number" name="prazo[' + indice_fase + '][]" value="" class="form-control form-control-sm"/>';
            $linha += "</td>";
            $linha += "<td class='text-center' style='width: 85px'>";
            $linha += '<input title="Marque para que o prazo seja contado em dias úteis" type="checkbox" name="is_dia_util[' + indice_fase + '][]"/> Dia útil?';
            $linha += "</td>";
            $linha += "</tr>";
        }
        $linha += "</tbody></table>";
        $linha += "</td>"
            + "<td class='text-center' style='vertical-align: middle'>"
            + "<a id='a{$fase->getId()}'' title='{if $fase->getAtivo() eq true}Des{else}Re{/if}ativar fase' href='javascript:' class='btn-{if $fase->getAtivo() eq true}des{else}re{/if}ativar-fase'>"
            + "<label class='switch' style='margin-bottom: 0; margin-right: .3rem; vertical-align:middle'>"
            + "<input type='checkbox' name='ativo[]' value='1' checked>"
            + "<span class='slider' onclick='return false;'></span>"
            + "</label></a>"
            + "<a title='Remover fase' href='javascript:' class='btn btn-danger btn-xs btn-remover-fase'><i class='fa fa-times'></i></a>"
            + "</td>"
            + "</tr>";
        $("#tabelaFases tbody").first().append($linha);
        $("#setor_id").val(null);
        $('#jstree').jstree(true).refresh(false, true);
        $("#fase_atual_text").text(fase_atual + 1);
        showGrowMessage('success', "Fase adicionada com sucesso");
    });
    $("#tabelaFases").on('click', '.btn-remover-fase', function (e) {
        var $linha = $(this).closest('tr');
        var fase_id = $linha.find('.fase-id').val();
        e.preventDefault();
        bootbox.confirm({
            title: "",
            message: "Deseja realmente excluir esta fase?",
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
                    if (fase_id != "") {
                        showLoading();
                        $.post(app_path + 'fase/excluir/id/' + fase_id, {ajax: true}, function (response) {
                            if (response.tipo == 'success') {
                                $linha.remove();
                                reaorganizarFases();
                            }
                            showGrowMessage(response.tipo, response.msg);
                        }, 'json').done(function () {
                            hideLoading();
                        });
                    } else {
                        $linha.remove();
                        showGrowMessage('success', 'Fase removida com sucesso.');
                    }
                    $("#fase_atual_text").text($('.linha-fase').length + 1);
                }
            }
        });
    });

    $('body').on("click", ".btn-limpar-filtros-relatorio", function () {
        $('.tabela-processos-relatorio').dataTable().fnResetAllFilters();
    });
    


    $("#tabelaFases").on('click', '.btn-reativar-fase', function (e) {
        var $linha = $(this).closest('tr');
        var fase_id = $linha.find('.fase-id').val();
        e.preventDefault();
        bootbox.confirm({
            title: "",
            message: "Deseja realmente reativar esta fase?",
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
                    if (fase_id != "") {
                        showLoading();
                        $.post(app_path + 'fase/reativar/id/' + fase_id, {ajax: true}, function (response) {
                            if (response.tipo == 'success') {
                                let alink = document.getElementById('a' + fase_id);
                                alink.title = 'Desativar fase';
                                alink.className = 'btn-desativar-fase';
                                $('#check' + fase_id).prop('checked', true);
                                reaorganizarFases();
                            }
                            showGrowMessage(response.tipo, response.msg);
                        }, 'json').done(function () {
                            hideLoading();
                        });
                    } else {
                        $linha.remove();
                        showGrowMessage('success', 'Fase reativada com sucesso.');
                    }
                    $("#fase_atual_text").text($('.linha-fase').length + 1);
                }
            }
        });
    });

    $("#tabelaFases").on('click', '.btn-desativar-fase', function (e) {
        var $linha = $(this).closest('tr');
        var fase_id = $linha.find('.fase-id').val();
        e.preventDefault();
        bootbox.confirm({
            title: "",
            message: "Deseja realmente desativar esta fase?",
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
                    if (fase_id != "") {
                        showLoading();
                        $.post(app_path + 'fase/desativar/id/' + fase_id, {ajax: true}, function (response) {
                            if (response.tipo == 'success') {

                                let alink = document.getElementById('a' + fase_id);
                                alink.title = 'Reativar fase';
                                alink.className = 'btn-reativar-fase';
                                $('#check' + fase_id).prop('checked', false);
                                reaorganizarFases();
                            }
                            showGrowMessage(response.tipo, response.msg);
                        }, 'json').done(function () {
                            hideLoading();
                        });
                    } else {

                        showGrowMessage('success', 'Fase desativada com sucesso.');
                    }
                    $("#fase_atual_text").text($('.linha-fase').length + 1);
                }
            }
        });
    });
    
    if($('#tabelaProcessoForaFluxogramaPorInteressado').length){
        $('#tabelaProcessoForaFluxogramaPorInteressado').dataTable({
            "sDom": "<'row'<'col-md-6 col-lg-6'i><'col-md-6 col-lg-6 col-xs-12 text-right'B>r>t<'row'<'col-md-6 hidden-xs'i><'col-md-6 col-xs-12'p>>",
            "paginate": true,
            responsive: true,
            "deferRender": true,
            "autoWidth": false,
            "aaSorting": [],
            buttons: [
                {
                    extend: 'excel',
                    text: '<i class="fa fa-file-excel-o"></i> Excel',
                    className: 'btn-success btn-sm',
                    exportOptions: {
                        page: 'all'
                    }
                },
                {
                    extend: 'pdfHtml5',
                    text: '<i class="fa fa-file-pdf-o"></i> PDF',
                    orientation: 'landscape',
                    messageBottom: null,
                    footer: true,
                    className: 'btn-info btn-sm',
                    exportOptions: {
                        page: 'all'
                    },
                   // download: 'open',
                    pageSize: 'A4', //A3 , A5 , A6 , legal , letter
                    customize: function (doc) {
                        let widths = [45,140,110,110,110,220];
                        let titulo = `Relatório de Processos (${$('#data_periodo_ini').val()} - ${$('#data_periodo_fim').val()})`;
                        customizeExportPDF(doc, titulo, widths);
                        let rowCount = doc.content[0].table.body.length;
                        for (let i = 1; i < rowCount; i++) {
                            doc.content[0].table.body[i][0].alignment = 'center';
                            doc.content[0].table.body[i][5].alignment = 'center';
                        }
                    }
                }
            ],
            "aoColumnDefs": [
                {
                    "sClass": "text-center",
                    "aTargets": [0]
                },
                {
                    "aTargets": [8],
                    "mRender": function (data, type, full) {
                        return  parseFloat(realToDecimal(data)).toFixed(2)+'%';
                    }                    
                }
            ]
        });
    }
    if($('#tabelaProcessoForaFluxograma').length){
        $('#tabelaProcessoForaFluxograma').dataTable({
            "sDom": "<'row'<'col-md-6 col-lg-6'i><'col-md-6 col-lg-6 col-xs-12 text-right'B>r>t<'row'<'col-md-6 hidden-xs'i><'col-md-6 col-xs-12'p>>",
            "paginate": true,
            responsive: true,
            "deferRender": true,
            "autoWidth": false,
            "aaSorting": [],
            buttons: [
                {
                    extend: 'excel',
                    text: '<i class="fa fa-file-excel-o"></i> Excel',
                    className: 'btn-success btn-sm',
                    exportOptions: {
                        page: 'all'
                    }
                },
                {
                    extend: 'pdfHtml5',
                    text: '<i class="fa fa-file-pdf-o"></i> PDF',
                    orientation: 'landscape',
                    messageBottom: null,
                    footer: true,
                    className: 'btn-info btn-sm',
                    exportOptions: {
                        page: 'all'
                    },
                   // download: 'open',
                    pageSize: 'A4', //A3 , A5 , A6 , legal , letter
                    customize: function (doc) {
                        let widths = [45,140,110,110,110,220];
                        let titulo = `Relatório de Processos (${$('#data_periodo_ini').val()} - ${$('#data_periodo_fim').val()})`;
                        customizeExportPDF(doc, titulo, widths);
                        let rowCount = doc.content[0].table.body.length;
                        for (let i = 1; i < rowCount; i++) {
                            doc.content[0].table.body[i][0].alignment = 'center';
                            doc.content[0].table.body[i][5].alignment = 'center';
                        }
                    }
                }
            ],
            "aoColumnDefs": [
                {
                    "sClass": "text-center",
                    "aTargets": [0]
                },
                {
                    "sClass": "text-center",
                    type: 'date-uk',
                    "aTargets": [3]
                }
            ]
        });
    }
    if($('#tabelaProcessoPorPeriodo').length){
        $('#tabelaProcessoPorPeriodo').dataTable({
            "sDom": "<'row'<'col-md-6 col-lg-6'i><'col-md-6 col-lg-6 col-xs-12 text-right'B>r>t<'row'<'col-md-6 hidden-xs'i><'col-md-6 col-xs-12'p>>",
            "paginate": true,
            responsive: true,
            "deferRender": true,
            "autoWidth": false,
            "aaSorting": [],
            buttons: [
                {
                    extend: 'excel',
                    text: '<i class="fa fa-file-excel-o"></i> Excel',
                    className: 'btn-success btn-sm',
                    exportOptions: {
                        page: 'all'
                    }
                },
                {
                    extend: 'pdfHtml5',
                    text: '<i class="fa fa-file-pdf-o"></i> PDF',
                    orientation: 'landscape',
                    messageBottom: null,
                    footer: true,
                    className: 'btn-info btn-sm',
                    exportOptions: {
                        page: 'all'
                    },
                   // download: 'open',
                    pageSize: 'A4', //A3 , A5 , A6 , legal , letter
                    customize: function (doc) {
                        let widths = [45,110,110,110,110,50,200];
                        let titulo = `Relatório de Processos por Período (${$('#data_periodo_ini').val()} - ${$('#data_periodo_fim').val()})`;
                        customizeExportPDF(doc, titulo, widths);
                        let rowCount = doc.content[0].table.body.length;
                        for (let i = 1; i < rowCount; i++) {
                            doc.content[0].table.body[i][0].alignment = 'center';
                            doc.content[0].table.body[i][5].alignment = 'center';
                        }
                    }
                }
            ],
            "aoColumnDefs": [
                {
                    "sClass": "text-center",
                    "aTargets": [0]
                },
                {
                    "sClass": "text-center",
                    type: 'date-uk',
                    "aTargets": [5]
                }
            ]
        });
    }
    if ($(".tabela-processos-relatorio").length) {
        $.fn.dataTableExt.afnFiltering.push(
            function (oSettings, aData, iDataIndex) {
                var retorno_processo = true;
                if ($("#data_processo_ini").length && $("#data_processo_fim").length) {
                    var iFini = $("#data_processo_ini").val();
                    var iFfin = $("#data_processo_fim").val();
                    var iStartDateCol = 3;
                    var iEndDateCol = 3;

                    iFini = iFini.substring(6, 10) + iFini.substring(3, 5) + iFini.substring(0, 2);
                    iFfin = iFfin.substring(6, 10) + iFfin.substring(3, 5) + iFfin.substring(0, 2);

                    var datofini = aData[iStartDateCol].substring(6, 10) + aData[iStartDateCol].substring(3, 5) + aData[iStartDateCol].substring(0, 2);
                    var datoffin = aData[iEndDateCol].substring(6, 10) + aData[iEndDateCol].substring(3, 5) + aData[iEndDateCol].substring(0, 2);

                    retorno_processo = false;
                    if (iFini === "" && iFfin === "") {
                        retorno_processo = true;
                    } else if (iFini <= datofini && iFfin === "") {
                        retorno_processo = true;
                    } else if (iFfin >= datoffin && iFini === "") {
                        retorno_processo = true;
                    } else if (iFini <= datofini && iFfin >= datoffin) {
                        retorno_processo = true;
                    }
                }
                var retorno_tramite = true;
                if ($("#data_tramite_ini").length && $("#data_tramite_fim").length) {
                    var iFini = $("#data_tramite_ini").val();
                    var iFfin = $("#data_tramite_fim").val();
                    var iStartDateCol = 8;
                    var iEndDateCol = 8;

                    iFini = iFini.substring(6, 10) + iFini.substring(3, 5) + iFini.substring(0, 2);
                    iFfin = iFfin.substring(6, 10) + iFfin.substring(3, 5) + iFfin.substring(0, 2);

                    var datofini = aData[iStartDateCol].substring(6, 10) + aData[iStartDateCol].substring(3, 5) + aData[iStartDateCol].substring(0, 2);
                    var datoffin = aData[iEndDateCol].substring(6, 10) + aData[iEndDateCol].substring(3, 5) + aData[iEndDateCol].substring(0, 2);

                    retorno_tramite = false;
                    if (iFini === "" && iFfin === "") {
                        retorno_tramite = true;
                    } else if (iFini <= datofini && iFfin === "") {
                        retorno_tramite = true;
                    } else if (iFfin >= datoffin && iFini === "") {
                        retorno_tramite = true;
                    } else if (iFini <= datofini && iFfin >= datoffin) {
                        retorno_tramite = true;
                    }
                }
                return retorno_processo && retorno_tramite;
            }
        );
        $(".tabela-processos-relatorio").dataTable({
            "sDom": "<'row'<'col-md-6 col-lg-6'i><'col-md-6 col-lg-6 col-xs-12 text-right'B>r>t<'row'<'col-md-6 hidden-xs'i><'col-md-6 col-xs-12'p>>",
            "bStateSave": false,
            "paginate": true,
            responsive: true,
            "deferRender": true,
            "autoWidth": false,
            "aaSorting": [[3, "asc"]],
            buttons: [
                {
                    extend: 'excel',
                    text: '<i class="fa fa-file-excel-o"></i> Excel',
                    className: 'btn-success btn-sm',
                    exportOptions: {
                        page: 'all'
                    }
                },
                {
                    extend: 'pdfHtml5',
                    text: '<i class="fa fa-file-pdf-o"></i> PDF',
                    orientation: 'landscape',
                    messageBottom: null,
                    footer: true,
                    className: 'btn-info btn-sm',
                    exportOptions: {
                        page: 'all'
                    },
                    download: 'open',
                    pageSize: 'A2', //A3 , A5 , A6 , legal , letter
                    customize: function (doc) {
                        customizeExportPDF(doc, 'Relatório de ' + nomenclatura + 's em Aberto');
                    }
                },
                {
                    extend: 'colvis',
                    text: '<i class="fa fa-filter"></i> Visibilidade de coluna',
                    className: 'btn-warning btn-sm'
                }
            ],
            "drawCallback": function (settings) {
                var api = this.api();
                var groupColumn = $(this).attr('groupColumn');
                if (groupColumn != "") {
                    api.order([groupColumn, 'asc']);
                    var rows = api.rows({page: 'current'}).nodes();
                    var last = null;
                    api.column(groupColumn, {page: 'current'}).visible(false).data().each(function (group, i) {
                        if (last !== group) {
                            $(rows).eq(i).before(
                                '<tr class="bg-light"><td colspan="10">' + group + '</td></tr>'
                            );
                            last = group;
                        }
                    });
                }

            },
            "createdRow": function (tr, tdsContent) {
                $(tr).attr('title', tdsContent[11]);
            },
            "initComplete": function (settings, json) {
                var $tabela = $(this);
                $tabela.fadeIn();
                var datatable = $tabela.dataTable();
                var $exercicio = $('#relatorioAbertosForm').find('.exercicio_filter');
                var $setor_atual = $('#relatorioAbertosForm').find('.setor_atual_filter');
                var $responsavel = $('#relatorioAbertosForm').find('.responsavel_filter');
                var $assunto = $('#relatorioAbertosForm').find('.assunto_filter');
                var $interessado = $('#relatorioAbertosForm').find('.interessado_filter');
                var $agrupar = $('#relatorioAbertosForm').find('.agrupar_filter');
                $exercicio.change(function () {
                    datatable.fnFilter($(this).find('option:selected').text(), 2);
                });
                $assunto.change(function () {
                    datatable.fnFilter($(this).find('option:selected').text(), 4);
                });
                $interessado.change(function () {
                    datatable.fnFilter($(this).find('option:selected').text(), 5);
                });
                $setor_atual.change(function () {
                    datatable.fnFilter($(this).find('option:selected').text(), 6);
                });
                $responsavel.change(function () {
                    datatable.fnFilter($(this).find('option:selected').text(), 7);
                });
                $agrupar.change(function () {
                    if ($(this).val() == 'setor') {
                        $tabela.attr('groupColumn', 6);
                        $setor_atual.prop("disabled", true);
                        $responsavel.prop("disabled", false);
                    } else if ($(this).val() == 'responsavel') {
                        $tabela.attr('groupColumn', 7);
                        $responsavel.prop("disabled", true);
                        $setor_atual.prop("disabled", false);
                    } else {
                        $setor_atual.prop("disabled", false);
                        $responsavel.prop("disabled", false);
                    }
                    datatable.fnDraw();
                });
                $('#data_processo_ini,#data_processo_fim,#data_tramite_ini,#data_tramite_fim').change(function () {
                    datatable.fnDraw();
                });
            },
            "aoColumnDefs": [
                {
                    "sClass": "text-left",
                    "aTargets": [0]
                },
                {
                    "sClass": "text-center",
                    type: 'date-uk',
                    "aTargets": [3]
                },
                {
                    "sClass": "text-center",
                    type: 'date-uk',
                    "aTargets": [8]
                },
                {
                    "sClass": "text-center",
                    type: 'date-uk',
                    "aTargets": [9]
                }
            ]
        });
    }
    if ($("#tabelaListagemUsuarios").length) {
        usuarioListaInativo($("#usuario_status_filter").val());
    }
    if ($("#tabelaControleVencimentos").length) {
        $.fn.dataTableExt.afnFiltering.push(
            function (oSettings, aData, iDataIndex) {
                var retorno_processo = true;
                if ($("#data_vencimento_ini").length && $("#data_vencimento_fim").length) {
                    var iFini = $("#data_vencimento_ini").val();
                    var iFfin = $("#data_vencimento_fim").val();
                    var iStartDateCol = 4;
                    var iEndDateCol = 4;

                    iFini = iFini.substring(6, 10) + iFini.substring(3, 5) + iFini.substring(0, 2);
                    iFfin = iFfin.substring(6, 10) + iFfin.substring(3, 5) + iFfin.substring(0, 2);

                    var datofini = aData[iStartDateCol].substring(6, 10) + aData[iStartDateCol].substring(3, 5) + aData[iStartDateCol].substring(0, 2);
                    var datoffin = aData[iEndDateCol].substring(6, 10) + aData[iEndDateCol].substring(3, 5) + aData[iEndDateCol].substring(0, 2);

                    retorno_processo = false;
                    if (iFini === "" && iFfin === "") {
                        retorno_processo = true;
                    } else if (iFini <= datofini && iFfin === "") {
                        retorno_processo = true;
                    } else if (iFfin >= datoffin && iFini === "") {
                        retorno_processo = true;
                    } else if (iFini <= datofini && iFfin >= datoffin) {
                        retorno_processo = true;
                    }
                }
                return retorno_processo;
            }
        );
        //Controle de Vencimentos
        $("#tabelaControleVencimentos").dataTable({
            "sDom": "<'row'<'col'l><'col invisible'f>r>t<'row'<'col'i><'col'p>>",
            "aaSorting": [],
            "autoWidth": false,
            "bStateSave": false,
            "initComplete": function (settings, json) {
                var $tabela = $(this);
                $tabela.fadeIn();
                var datatable = $tabela.dataTable();
                var $setor_atual = $('#relatorioVencimentosForm').find('.setor_atual_filter');
                var $responsavel = $('#relatorioVencimentosForm').find('.responsavel_filter');
                var $assunto = $('#relatorioVencimentosForm').find('.assunto_filter');
                var $interessado = $('#relatorioVencimentosForm').find('.interessado_filter');
                $assunto.change(function () {
                    initGraficosVencimentos($("#relatorioVencimentosForm").serialize());
                    datatable.fnFilter($(this).find('option:selected').text(), 2);
                });
                $interessado.change(function () {
                    initGraficosVencimentos($("#relatorioVencimentosForm").serialize());
                    datatable.fnFilter($(this).find('option:selected').text(), 1);
                });
                $setor_atual.change(function () {
                    initGraficosVencimentos($("#relatorioVencimentosForm").serialize());
                    datatable.fnFilter($(this).find('option:selected').text(), 3);
                });
                $responsavel.change(function () {
                    initGraficosVencimentos($("#relatorioVencimentosForm").serialize());
                    datatable.fnFilter($(this).find('option:selected').text(), 5);
                });
                $('#data_vencimento_ini,#data_vencimento_fim').change(function () {
                    initGraficosVencimentos($("#relatorioVencimentosForm").serialize());
                    datatable.fnDraw();
                });
            },
            "aoColumnDefs": [
                {
                    type: 'date-uk',
                    "aTargets": [4]
                },
                {
                    "sClass": "col-actions vertical-middle",
                    bSortable: false,
                    "aTargets": [7]
                }

            ]
        });
        initGraficosVencimentos();

        function initGraficosVencimentos(param) {
            //Inicia gráficos pizza da tela de Controle de Vencimentos
            graficoPizzaTramitesVencidos('pieVencidosSetor', 'setorAtual', param);
            graficoPizzaTramitesVencidos('pieVencidosAssunto', 'assunto', param);
            graficoPizzaTramitesVencidos('pieVencidosInteressado', 'interessado', param);
        }
    }
//Anexos Por Período
//graficoPizzaAnexos('pieFormaAnexo', "isDigitalizado", "");
    
    graficoPizzaAnexos('piePorTipoAnexo', "tipo", "Anexos por Tipo", false);
    $("#formAnexosPeriodo").on('change', '.form-filter', function () {
        graficoPizzaAnexos('piePorTipoAnexo', "tipo", "Anexos por Tipo", true);
    });

    /**
     * Processos Por período
     */
    graficoPizzaProcessosPeriodo('piePorOrigemProcesso', 'origem');
    graficoPizzaProcessosPeriodo('piePorAssuntoProcesso', 'assunto');
    graficoPizzaProcessosPeriodo('piePorResponsavelProcesso', 'usuarioAbertura');
    /**************************************************************************************************************/
    /*================================        Permissões Grupo    =====================================================*/
    /*************************************************************************************************************/
    $("#tabelaGrupos").on('click', '.btn-ver-usuarios-grupo', function (e) {
        e.preventDefault();
        var grupo = $(this).attr('grupo');
        var grupo_id = $(this).attr('grupo_id');
        showLoading();
        $.post(app_path + 'src/App/View/Grupo/usuarios.php', {grupo_id: grupo_id}, function (response) {
            createModal("usuariosGrupoModal", "Usuários do Grupo: " + grupo, response);
        }).done(function () {
            hideLoading();
        });
    });
    $("body").on("click", '.marcaTodosTabela', function () {
        if ($(this).is(':checked')) {
            $(this).closest('table').find('input[type=checkbox]:enabled').each(function () {
                $(this).attr('checked', 'true');
            });
        } else {
            $(this).closest('table').find('input[type=checkbox]').each(function () {
                $(this).removeAttr('checked');
            });
        }
    });
    $("#tabelaPermissoes").on("click", 'tr .marcaLinhaMenu', function () {
        if ($(this).is(':checked')) {
            $(this).closest('tr').find('input[type=checkbox]').each(function () {
                $(this).attr('checked', 'true');
            });
        } else {
            $(this).closest('tr').find('input[type=checkbox]').each(function () {
                $(this).removeAttr('checked');
            });
        }
    });
    /*************************************************************************************************************/
    /**************************************************************************************************************/
    /*================================        Eventos App    =====================================================*/
    /*************************************************************************************************************/
    $("body").on('change', '.estado', function () {
        var cidade = $(this).attr('cidade');
        var uf = $(this).val();
        var $select = $(this).closest('.form-group').find('.cidade');
        showLoading();
        $.post(app_path + 'src/App/Ajax/Estado/buscar_cidades.php', {uf: uf}, function (response) {
            $select.html(response);
        }).done(function () {
            if (cidade != "") {
                $select.closest("form").find("#cidade").find('option:contains("' + cidade + '")').attr('selected', true);
            }
            hideLoading();
        });
    }).on('change', '#select_tipo_pessoa', function () {
        var tipo = $(this).val();
        if (tipo == 'fisica') {
            $("#divPessoaJuridica").hide();
            $("#cpf").attr("required",true);
            $("#cnpj").removeAttr("required");
            $("#divPessoaFisica").fadeIn();
        } else {
            $("#divPessoaFisica").hide();
            $("#cnpj").attr("required",true);
            $("#cpf").removeAttr("required");
            $("#divPessoaJuridica").fadeIn();
        }
    }).on('click', '.btn-link-email', function (e) {
        e.stopPropagation();
        var href = $(this).attr('href');
        window.open(href);
    });

    $("#interessadoForm").validate({
        submitHandler: function(form) {
            showLoading();
            if($("#senha").val() != $("#senha_confirma").val()){
                showGrowMessage('error', "As senhas informadas não podem ser diferente.");
                hideLoading();
                return false
            }

            let href = $(form).attr('action');
            let data = $(form).serialize()+'&ajax=true';
            let is_externo = $("#isExterno").val() == 1;

            $.post(href, data, function (response) {
                hideLoading();
                showGrowMessage(response.tipo, response.msg);
                if (response.tipo === 'success') {

                    if(is_externo){
                        bootbox.dialog({
                            size: 'large',
                            animate: false,
                            message: "<h5>Confirme seu email para ativação do login</h5>",
                            title: "",
                            buttons: {
                                success: {
                                    label: "Ok",
                                    className: "btn-primary",
                                    callback: function () {
                                        location.replace(app_path+'contribuinte');
                                    }
                                },
                            }
                        });
                    }else{
                        location.replace(href + "/../index");
                    }
                }
            }, 'json');
            return false;
        }
    });
});

function visualizarSolicitacao(solicitacao_id) {
    showLoading();
    let url = app_url + "solicitacao/anexo/visualizar/" + solicitacao_id;
    $.get(url, null, (response) => {
        createModal("modal-solicitacao-visualizacao", "Solicitação", response);
        hideLoading()
    });
    return false;

}

/**
 * Mostrar histórico do anexo.
 *
 */
function mostrarHistoricoAnexo(element) {
    let container = $("#wall-history");
    container.children().remove();
    let anexoId = $(element).attr("data-id");
    carregarHistoricoAnexo(anexoId, container);
}

function carregarHistoricoAnexo(anexoId, container) {
    showLoading();
    let app_url = $("#app_url").text();
    $.get(app_url + "Anexo/historico/" + anexoId, function(data) {
        if (container.children().length === 0) {
            container.html(data);
        }
        $("#historic-modal").modal("show");
        hideLoading();
    }).fail(function () {
        showGrowMessage('error', 'Ocorreu uma falha na solicitação.')
    });
}

function dismiss(target) {
    $(target).modal('hide');
}

function initSelect2Assunto() {
    $('.select_assunto').each(function () {
        if (!$(this).hasClass("select2-hidden-accessible")) {
            $(this).select2({
                placeholder: "Busque por um assunto",
                minimumInputLength: 3,
                language: "pt-BR",
                width: '100%',
                allowClear: true,
                multiple: false,
                beforeSend: function () {
                    if (currentRequest != null) {
                        currentRequest.abort();
                    }
                },
                ajax: {
                    url: app_path + "src/Core/Ajax/select2_ajax_response.php?entidade=Assunto",
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
    });
}

function initSelect2Processos() {
    
    $('.select_processo').each(function () {
        if (!$(this).hasClass("select2-hidden-accessible")) {
            $(this).select2({
                placeholder: "Busque por um processo(s)/protocolo(s)",
                minimumInputLength: 1,
                language: "pt-BR",
                width: '100%',
                allowClear: true,
                multiple: false,
                beforeSend: function () {
                    if (currentRequest != null) {
                        currentRequest.abort();
                    }
                },
                ajax: {
                    url: app_path + "src/Core/Ajax/select2_ajax_response.php?entidade=Processo",
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
    });
}

function initSelect2FornecedorImportacao() {
    $('.select-fornecedor-importacao').each(function () {
        if (!$(this).hasClass("select2-hidden-accessible")) {
            $(this).select2({
                placeholder: "Busque por um  fornecedor",
                minimumInputLength: 3,
                language: "pt-BR",
                width: '100%',
                allowClear: true,
                multiple: false,
                ajax: {
                    url: $(this).attr("url_pesquisa")+"index.php?entidade=fornecedor&method=listaSelect2&ano="+$("#formAnexoImportarPesquisa").find("select[name='bancoAno']").val(),
                    dataType: 'json',
                    delay: 250,
                    data: function (params) { // page is the one-based page number tracked by Select2
                        return {
                            banco: $("#formAnexoImportarPesquisa").find("select[name='bancoAno']").val(),
                            search: params.term, //search term
                            page: params.page || 1 // page number
                        };
                    },
                    cache: true
                }
            });
        }
    });

}

function initSelect2Interessado() {
    $('.select_interessado').each(function () {
        if (!$(this).hasClass("select2-hidden-accessible")) {
            $(this).select2({
                placeholder: "Busque por um  interessado",
                minimumInputLength: 3,
                language: "pt-BR",
                width: '100%',
                allowClear: true,
                multiple: false,
                ajax: {
                    url: app_path + "src/Core/Ajax/select2_ajax_response.php?entidade=Interessado",
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
    });

}

function initSelect2Tree() {
    if ($(".select2Tree").length) {
        $(".select2Tree").select2ToTree({
            language: "pt-BR",
            width: '100%',
            allowClear: true,
            placeholder :'Selecione'
        });
    }
}

function initValidateTramitarProcessoForm($tabela = null) {
    initSelect2Tree();
    initDatePicker();
    initFileInput();
    initSelect2Processos();
    initTooltip();
    
    $("#tramitarProcessoForm").validate({
        ignore: ":hidden, .select2-search__field, .ignore-validate",
        submitHandler: function (form) {
            var l = Ladda.create(form.querySelector('.ladda-button'));
            var gerar_guia = $(form).find('input[type=checkbox][name=gerar_guia_tramitacao]').is(':checked');
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
                    if (response.tipo == 'success') {
                        let reload_page = $("#btn-tramitar-processo").attr('reload_page') !== undefined;
                        if(reload_page){
                            showLoading();
                            window.location.reload();
                        }else if($tabela !== null){

                            /** ########### INICIO CORREÇÕES DEVIDO A INTERNET LENTA ########## */
                            //Remove todos os checkeds
                            $tabela.find('input').each(function (){
                                $(this).prop("checked", false);
                            });

                            //Desabilita os checkbox do body da tabela
                            $tabela.find('tbody input').each(function (){
                                $(this).attr('disabled', 'disabled');
                            });

                            $tabela.find('button').each(function (){
                                $(this).attr('disabled', 'disabled');
                            });
                            /** ########### FIM CORREÇÕES DEVIDO A INTERNET LENTA ########## */


                            $tabela.dataTable().api().ajax.reload(null, false);
                        }

                        atualizarContadoresProcesso();
                        $(form).closest('.modal').modal('hide');
                        if (gerar_guia) {
                            window.open(app_path + 'remessa/imprimir/' + response.objeto_id);
                        }
                    }
                    showGrowMessage(response.tipo, response.msg);
                    l.stop();
                }
            });
            return false;
        }
    });
    $(".select-opcao-assunto").change(function () {
        var assunto_sel = $(this).val();
        var processo_id = $(this).attr('processo_id');
        var numero_fase = $(this).attr('numero_fase');
        if (assunto_sel != "") {
            showLoading();
            $.post(app_path + 'src/App/View/Tramite/fluxograma.php', {
                processo_id: processo_id,
                assunto_id: assunto_sel,
                numero_fase: numero_fase
            }, function (response) {
                $("#divFluxograma").html(response);
                $.post(app_path + 'src/App/View/Tramite/destino_fluxograma.php', {
                    processo_id: processo_id,
                    assunto_id: assunto_sel
                }, function (response) {
                    $("#divDestino").html(response);
                    initFileInput();
                    initDatePicker();
                    initSelect2Processos();
                }).done(function () {
                    hideLoading();
                });
            });
        } else {
            $("#divDestino").html("");
            showLoading();
            $.post(app_path + 'src/App/View/Tramite/fluxograma.php', {
                processo_id: processo_id,
            }, function (response) {
                $("#divFluxograma").html(response);
                $.post(app_path + 'src/App/View/Tramite/destino_fluxograma.php', {
                    processo_id: processo_id,
                }, function (response) {
                    $("#divDestino").html(response);
                    initSelect2Tree();
                }).done(function () {
                    hideLoading();
                });
            });
        }
    });
}

function initValidateFormAutenticarProcesso() {
    $("#loginProcessoForm").validate({
        highlight: function (element) {
            $(element).addClass('is-invalid');
            $(element).removeClass('is-valid');
        },
        unhighlight: function (element) {
            $(element).removeClass('is-invalid');
            $(element).addClass('is-valid');
        },
        messages: {
            login: {required: "*Digite seu login de acesso."},
            senha: {required: "*Digite sua senha de acesso."}
        },
        errorClass: 'form-text text-danger text-left',
        submitHandler: function (form) {
            var l = Ladda.create(form.querySelector('.ladda-button'));
            l.start();
            $.post($(form).attr('action'), $(form).serialize(), function (response) {
                if (response.tipo == 'success') {
                    if ($("#processoForm").length) {
                        window.location.href = app_path + response.objeto_id;
                    } else {
                        visualizarProcesso(response.objeto_id);
                    }
                }
                showGrowMessage(response.tipo, response.msg);
            }, 'json').done(function () {
                l.stop();
            });
            return false;
        }
    });
}

function visualizarProcesso(processo_id) {
    if (processo_id != "") {
        showLoading();
        $.post(app_path + 'src/App/View/Processo/visualizar.php', {processo_id: processo_id}, function (response) {
            createModal('visualizarProcessoModal' + processo_id, "Visualizar " + nomenclatura, response, 'modal-lg');
            initValidateFormAutenticarProcesso();
            initDataTable();
        }).done(function () {
            hideLoading();
        });
    }
}

function tramitar(tramite_id, tramites, processo, $tabela = null, cancelar = false, show_btn_close= true) {
    showLoading();
    $.post(app_path + "src/App/View/Tramite/tramitar.php", {
        tramite_id: tramite_id,
        tramites: tramites,
        cancelar: cancelar ? 1 : 0
    }, function (response) {
        let modal_id = "tramitarProcessoModal";
        let modal_title = (cancelar ? 'Cancelar Envio de ' + nomenclatura : "Tramitar " + nomenclatura + " nº " + (processo ?? 'S/N'));
        if(show_btn_close){
            modal_title = (cancelar ? 'Cancelar Envio de ' + nomenclatura : "Encaminhar " + nomenclatura + " nº " + processo);
        }
        let modal_content = response;
        let modal_size = 'modal-elg';
        createModal(modal_id, modal_title, modal_content, modal_size, show_btn_close);
        initValidateTramitarProcessoForm($tabela);
        showLoading();
    }).done(function () {
        hideLoading();
    });
}

function initValidateFormRequisito(entidade, modal_id, objeto_ref_id, $button) {
    $('#form' + entidade).validate({
        ignore: ":hidden",
        submitHandler: function (form) {
            var acao = $(form).find('input[name=acao]').val();
            $(form).ajaxSubmit({
                beforeSubmit: function () {
                    if ($("#is_anexo_selecionado").val() == 1) {
                        $('.progress').removeClass('hidden');
                    }
                    showLoading();
                },
                uploadProgress: function (event, position, total, percentComplete) {
                    $('.progress-bar').attr('aria-valuenow', percentComplete).css('width', percentComplete + '%');
                    $('#porcentagem').html(percentComplete + '%');
                },
                dataType: 'json',
                success: function (response) {
                    if (response.tipo === 'success') {
                        atualizarListagemRequisitos(entidade, objeto_ref_id);
                        if (acao == 'atualizar') {
                            $('#' + modal_id).modal('hide');
                        } else {
                            $(form).trigger('reset');
                        }
                    }
                    hideLoading();
                    showGrowMessage(response.tipo, response.msg);
                }
            });
            return false;
        }
    });
}

function atualizarApensos(processo_id, apensos) {
    if ($("#divApensos").length) {
        showLoading();
        $.post(app_path + 'src/App/View/Processo/apensos.php', {
            processo_id: processo_id,
            apensos: apensos
        }, function (response) {
            $("#divApensos").html(response);
            $("#qtde_apensos").text($('.linha-processo').length);
        }).done(function () {
            hideLoading();
        });
    }
}

function initTabelaRequisitosSortable() {
    $(".lista-perguntas").sortable({
        items: 'li:not(.no-sortable)',
        cursor: 'move',
        placeholder: "ui-state-highlight",
        update: function (event, ui) {
            var data = $(this).sortable('serialize');
            showLoading();
            $.post(app_path + 'src/App/Ajax/Pergunta/ordenar.php', data, function (response) {
                showGrowMessage(response.tipo, response.msg);
                atualizarListagemRequisitos('Pergunta', response.objeto_id);
            }, 'json').done(function () {
                hideLoading();
            });
        }
    }).disableSelection();
    $(".lista-tarefas").sortable({
        items: 'li:not(.no-sortable)',
        cursor: 'move',
        placeholder: "ui-state-highlight",
        update: function (event, ui) {
            var data = $(this).sortable('serialize');
            showLoading();
            $.post(app_path + 'src/App/Ajax/Tarefa/ordenar.php', data, function (response) {
                showGrowMessage(response.tipo, response.msg);
                atualizarListagemRequisitos('Tarefa', response.objeto_id);
            }, 'json').done(function () {
                hideLoading();
            });
        }
    }).disableSelection();
    $(".lista-campos").sortable({
        items: 'li:not(.no-sortable)',
        cursor: 'move',
        placeholder: "ui-state-highlight",
        update: function (event, ui) {
            var data = $(this).sortable('serialize');
            showLoading();
            $.post(app_path + 'src/App/Ajax/Campo/ordenar.php', data, function (response) {
                showGrowMessage(response.tipo, response.msg);
                atualizarListagemRequisitos('Campo', response.objeto_id);
            }, 'json').done(function () {
                hideLoading();
            });
        }
    }).disableSelection();
}

function atualizarListagemRequisitos(entidade, objeto_ref_id) {
    $.post(app_path + 'src/App/View/' + entidade + '/listar.php', {objeto_ref_id: objeto_ref_id}, function (response) {
        $("#lista" + entidade + "_" + objeto_ref_id).html(response).fadeIn();
        initTabelaRequisitosSortable();
        $("#qtde_perguntas_" + objeto_ref_id).text($(".linha_pergunta_" + objeto_ref_id).length);
        $("#qtde_tarefas_" + objeto_ref_id).text($(".linha_tarefa_" + objeto_ref_id).length);
        $("#qtde_campos_" + objeto_ref_id).text($(".linha_campo_" + objeto_ref_id).length);
    }).done(function () {
        hideLoading();
    });
}

function buscarPrazoAssunto(assunto_id, data_processo) {
    showLoading();
    $.post(app_path + 'src/App/Ajax/Assunto/buscar_vencimento.php', {
        assunto_id: assunto_id,
        data_processo: data_processo
    }, function (response) {
        $("#data_vencimento").datepicker('setDate', response);
    }).done(function () {
        hideLoading();
    });
}

function initPesquisaEntidade(entidade, $select) {
    var $tabela = $(".tabelaPesquisaEntidade");
    var url = $tabela.attr('url');
    $('.tabelaPesquisaEntidade thead th').each(function () {
        var title = $(this).text();
        $(this).html('<input type="text" class="form-control form-control-sm" placeholder="Buscar ' + title + '" />');
    });
    var tabela = $tabela.dataTable({
        "sDom": "<'row'<'col hidden'l><'col hidden'f>r>t<'row'<'col'i><'col'p>>",
        orderCellsTop: true,
        "processing": true,
        "serverSide": true,
        "bStateSave": false,
        "aaSorting": [],
        "ajax": url,
        "createdRow": function (tr, tdsContent) {
            $(tr).attr('title', 'Clique para selecionar').attr('id', tdsContent[0]);
        },
        "initComplete": function (settings, json) {
            $(this).fadeIn();
        }
    });
    tabela.columns().every(function () {
        var that = this;
        $('input', this.header()).on('keyup change', function () {
            if (that.search() !== this.value) {
                that
                    .search(this.value)
                    .draw();
            }
        });
    });
    $('.tabelaPesquisaEntidade tbody').on('click', 'tr', function () {
        var id = this.id;
        var $tds = $(this).find('td');
        $('.tabelaPesquisaEntidade tbody').find('tr').each(function () {
            $(this).removeClass('table-primary');
        });
        $(this).toggleClass('table-primary');
        $(this).closest('form').find("#entidade_id").val(id);
        $(this).closest('form').find("#entidade_descricao").val($($tds[1]).text());
    });
    $("#formPesquisaEntidade").validate({
        submitHandler: function (form) {
            var entidade_id = $(form).find('#entidade_id').val();
            var descricao_entidade = $(form).find('#entidade_descricao').val();
            if (entidade_id == "") {
                showAlert('Selecione um item  da lista.');
                return false;
            }
            $select.html("<option value='" + entidade_id + "' selected>" + descricao_entidade + "</option>").trigger('change');
            $(form).closest('.modal').modal('hide');
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

function initTabelaPesquisaProcessos($select) {
    
    var id_tabela = "#tabelaPesquisaSelecionarProcesso";
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
                $tabela.api().ajax.url(url + '&' + col_name + '=' + $(this).val()).load();
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
        $(this).closest('form').find("#entidade_descricao").val($($tds[0]).text()+"/"+$($tds[1]).text());
    });
    $("#formPesquisaProcesso").validate({
        submitHandler: function (form) { 
            var entidade_id = $(form).find('#entidade_id').val();
            var descricao_entidade = $(form).find('#entidade_descricao').val();
            if (entidade_id == "") {
                showAlert('Selecione um item  da lista.');
                return false;
            }
            $select.html("<option value='" + entidade_id + "' selected>" + descricao_entidade + "</option>").trigger('change');
            $(form).closest('.modal').modal('hide');
        }
    });
    
    
    
    
    
    
    
    
    
    var url = $("#tabelaPesquisaProcessos").attr('url');
    $("#tabelaPesquisaProcessos").dataTable({
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

function initTabelaPesquisaAnexos() {
    var url = $("#tabelaResultaPesquisaAnexos").attr('url');
    $("#tabelaResultaPesquisaAnexos").dataTable({
        "serverSide": true,
        "processing": true,
        "bStateSave": false,
        "ajax": url,
        "order": [[5, "desc"]],
        "autoWidth": false,
        "initComplete": function (settings, json) {
            $(this).fadeIn('slow');
        },
        "createdRow": function (tr, tdsContent) {
            $(tr).attr('title', tdsContent[9]);
        },
        "aoColumnDefs": [
            {
                "bVisible": false,
                "aTargets": [0]
            },
            {
                "sClass": 'text-center',
                type: 'date-uk',
                "aTargets": [6]
            },
            {
                "sClass": 'text-center',
                bSortable: false,
                "aTargets": [8]
            },
            {
                "bVisible": false,
                "aTargets": [10]
            }
        ]
    });
}

function initTabelaResumoProcessos() {
    $('.tabelaListaProcessos').each(function () {
        var url = $(this).attr('url');
        if (url) {
            var $tabela = $(this);
            if ($tabela.hasClass('dataTable')) {
                $tabela.dataTable().api().ajax.url(url).load();
            } else {
                $tabela.dataTable({
                    "sDom": 'lBfrtip',
                    "lengthMenu": [
                        [ 10, 25, 50, -1 ],
                        [ '10', '25', '50', 'Todos' ]
                    ],
                    "serverSide": true,
                    "processing": true,
                    "bStateSave": false,
                    "ajax": url,
                    "aaSorting": [[6, "desc"]],

                    "autoWidth": false,
                    "initComplete": function (settings, json) {
                        $(this).fadeIn('slow');
                    },
                    "createdRow": function (tr, tdsContent) {
                        $(tr).attr('title', tdsContent[9]).attr("id", "visualizar:" + tdsContent[0]);
                    },
                    buttons: [
                        {
                            extend: 'pdfHtml5',
                            text: '<i class="fa fa-file-pdf-o"></i> PDF',
                            orientation: 'portrait',
                            messageBottom: null,
                            footer: true,
                            className: 'btn-info btn-sm',
                            exportOptions: {
                                page: 'all',
                                columns: ':visible'
                            },
                           // download: 'open',
                            pageSize: 'A4', //A3 , A5 , A6 , legal , letter
                            customize: function (doc) {
                                let widths = [145,140,110,110,110,220,100];
                                let titulo = `Resultados da Pesquisa`;
                                customizeExportPDF(doc, titulo, widths);
                                let table = null;
                                
                                $.each(doc.content, function (key, content){
                                    if(content.table){
                                        table = content.table;
                                        return false;
                                    }
                                });
                                
                                let rowCount = table.body.length;
                                for (let i = 1; i < rowCount; i++) {
                                    table.body[i][0].alignment = 'center';
                                    table.body[i][1].alignment = 'center';
                                    table.body[i][5].alignment = 'center';
                                    table.body[i][6].alignment = 'center';
                                }
                                doc.defaultStyle.fontSize = 7;
                                doc.styles.tableHeader.fontSize = 8;
                                table.autoFill = true;
                                table.widths = [30,35,100,100,100,40,45,50];
                            }
                        }
                    ],
                    "aoColumnDefs": [
                        {
                            "sClass": "hidden",
                            "aTargets": [0]
                        },
                        {
                            "sClass": 'text-center',
                            type: 'date-uk',
                            "aTargets": [6]
                        },
                        {
                            "sClass": 'text-center',
                            type: 'date-uk',
                            "aTargets": [7]
                        },
                        {
                            "sClass": "hidden",
                            "aTargets": [8]
                        },
                        {
                            "sClass": "hidden",
                            "aTargets": [9]
                        }
                    ]
                });
            }
        }
    });
}

function graficoProcessosMensal(ano, assunto_id) {
    if ($("#graficoMensal").length) {
        $.post(app_path + 'src/App/Ajax/Public/processos_por_mes.php', {
            ano: ano,
            assunto_id: assunto_id
        }, function (data) {
            drawLineChartByMonth("graficoMensal", data, nomenclatura + "s por mês" + (ano != null ? " " + ano : ""), "Quantidade de " + nomenclatura + "s");
        }, 'json').done(function () {
            hideLoading();
        });
    }
}

function graficoPizzaAnexos(target, referencia, titulo, update_table) {
    if ($("#" + target).length) {
        var tipo_documento_id = $("#formAnexosPeriodo").find("#select_tipo_documento").val();
        var usuario_id = $("#formAnexosPeriodo").find("#select_usuario").val();
        var data_periodo_ini = $("#formAnexosPeriodo").find("#data_periodo_ini").val();
        var data_periodo_fim = $("#formAnexosPeriodo").find("#data_periodo_fim").val();
        if (update_table) {
            showLoading();
            $.post(app_path + "src/App/View/Anexo/listar_relatorio.php", {
                tipo_documento_id: tipo_documento_id,
                usuario_id: usuario_id,
                data_periodo_ini: data_periodo_ini,
                data_periodo_fim: data_periodo_fim
            }, function (response) {
                $("#divListaAnexos").html(response);
                $('a[href="#detalhadoTab"]').click();
            }).done(function () {
                hideLoading();
            });
        }
        $.post(app_path + 'src/App/Ajax/Relatorio/grafico_pizza_anexos.php', {
            referencia: referencia,
            tipo_documento_id: tipo_documento_id,
            usuario_id: usuario_id,
            data_periodo_ini: data_periodo_ini,
            data_periodo_fim: data_periodo_fim
        }, function (data) {
            drawPie(target, data, 'Anexos', titulo, '<b>{point.y:.f}</b>', false, true);
        }, 'json');
    }
}

function graficoPizzaTramitesVencidos(target, referencia, parametros) {
    var referencia_txt = referencia == 'setorAtual' ? 'setor' : referencia;
    var indice_filter = referencia == 'setorAtual' ? 3 : (referencia == 'interessado' ? 1 : 2);
    if ($("#" + target).length) {
        var $tabela = $("#tabelaControleVencimentos").dataTable();
        $tabela.fnResetAllFilters();
        $.post(app_path + 'src/App/Ajax/Relatorio/grafico_pizza_vencimentos.php', parametros + "&referencia=" + referencia, function (data) {
            Highcharts.chart(target, {
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    backgroundColor: '#fff',
                    plotShadow: false,
                    type: 'pie'
                },
                title: {
                    text: "Vencimentos por " + referencia_txt
                },
                tooltip: {
                    pointFormat: '{series.name}:<b>{point.y:.f}</b>'
                },
                //colors: (cores != null ? cores : '[]'),
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: false,
                            format: '{point.name}: <b>{point.y:.f}</b>',
                            style: {
                                color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                            }
                        },
                        showInLegend: true
                    }
                },
                credits: {
                    enabled: false
                },
                series: [
                    {
                        name: 'Trâmites',
                        colorByPoint: true,
                        data: data,
                        point: {
                            events: {
                                click: function (event) {
                                    if (!event.point.selected) {
                                        console.log("Filtrou " + this.name + " | Indice:" + indice_filter);
                                        $tabela.fnFilter(this.name, indice_filter);
                                    } else {
                                        console.log("Resetou filtros | Indice:" + indice_filter);
                                        $tabela.fnFilter("", indice_filter);
                                    }
                                }
                            }
                        }
                    }
                ]
            });
            //drawPie(target, data, 'Processos', "Vencimentos por " + referencia_txt, '<b>{point.y:.f}</b>', false, true);
        }, 'json').done(function () {
            hideLoading();
        });
    }
}

function graficoPizzaProcessosPeriodo(target, agrupado) {
    if ($("#" + target).length) {
        var qtde_registros = $("#formProcessosPeriodo").find("#qtde_registros").val();
        var data_ini = $("#formProcessosPeriodo").find("#data_periodo_ini").val();
        var data_fim = $("#formProcessosPeriodo").find("#data_periodo_fim").val();
        $.post(app_path + 'src/App/Ajax/Relatorio/grafico_pizza_processos_periodo.php', {
            data_ini: data_ini,
            data_fim: data_fim,
            agrupado: agrupado,
            qtde_registros: qtde_registros
        }, function (data) {
            drawPie(target, data, nomenclatura + 's', nomenclatura + "s  por " + agrupado, '{point.percentage:.1f} %', false, true);
        }, 'json').done(function () {
            hideLoading();
        });
    }
}

function graficoPizzaProcessos(target, referencia, responsavel_id, assunto_id, interessado_id) {
    if ($("#" + target).length) {
        var referencia_txt = referencia == 'receber' ? "a receber" : (referencia == 'aberto' ? "em aberto" : "vencidos");
        $.post(app_path + 'src/App/Ajax/Public/grafico_pizza.php', {
            referencia: referencia,
            responsavel_id: responsavel_id,
            assunto_id: assunto_id,
            interessado_id: interessado_id
        }, function (data) {
            drawPie(target, data, nomenclatura + 's', nomenclatura + "s  " + referencia_txt, '<b>{point.y:.f}</b>', false, true);
        }, 'json').done(function () {
            hideLoading();
        });
    }
}

function drawLineChartByMonth(id, series, title, y_title) {
    if ($("#" + id).length) {
        Highcharts.chart(id, {
            chart: {
                type: 'line'
            },
            title: {
                text: title
            },
            xAxis: {
                categories: ['Jan', 'Fev', 'Mar', 'Apr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez']
            },
            yAxis: {
                title: {
                    text: y_title
                }
            },
            plotOptions: {
                line: {
                    dataLabels: {
                        enabled: true
                    },
                    enableMouseTracking: false
                }
            },
            series: series,
            credits: {
                enabled: false
            }
        });
    }
}

function drawPieChartHtmlTable(id, table_id, title) {
    Highcharts.chart(id, {
        data: {
            table: table_id
        },
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            backgroundColor: '#fff',
            plotShadow: false,
            type: 'pie'
        },
        plotOptions: {
            column: {
                colorByPoint: true
            }
        },
        colors: [
            '#00c0ef',
            '#f39c12',
            '#00a65a',
        ],
        title: {
            text: title
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: false
                },
                showInLegend: true
            }
        },
        credits: {
            enabled: false
        }
    });
}

function drawColumnChartHtmlTable(id, table_id, title, y_title) {
    Highcharts.chart(id, {
        data: {
            table: table_id
        },
        /*plotOptions: {
         column: {
         colorByPoint: true
         }
         },
         colors: [
         '#ff0000',
         '#00ff00',
         ],*/
        chart: {
            type: 'column'
        },
        title: {
            text: title
        },
        yAxis: {
            allowDecimals: false,
            title: {
                text: y_title
            }
        },
        tooltip: {
            formatter: function () {
                return '<b>' + this.series.name + '</b><br/>' +
                    this.point.y + ' ' + this.point.name.toLowerCase();
            }
        },
        credits: {
            enabled: false
        }
    });
}

function drawPie(id, data, series_name, title, point_format, dataLabels, showlegend) {
    if ($("#" + id).length) {
        Highcharts.chart(id, {
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                backgroundColor: '#fff',
                plotShadow: false,
                type: 'pie'
            },
            title: {
                text: title
            },
            tooltip: {
                pointFormat: '{series.name}:' + (point_format != null ? point_format : '<b>{point.percentage:.1f}%</b>')
            },
            //colors: (cores != null ? cores : '[]'),
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: dataLabels != null ? dataLabels : false,
                        format: '{point.name}: ' + point_format,
                        style: {
                            color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                        }
                    },
                    showInLegend: showlegend != null ? showlegend : (dataLabels != null ? !dataLabels : true)
                }
            },
            credits: {
                enabled: false
            },
            series: [{
                name: series_name,
                colorByPoint: true,
                data: data
            }]
        });
    }
}

function setarInformacoesProcesso(hide = true) {
    showLoading();
    $.post(app_path + 'processo/setarInformacoes', $("#processoForm").serialize(), function (response) {

    }, 'json').done(function (){
        if(hide)
            hideLoading();
    });
}

function atualizarContadoresProcesso() {
    $.ajax({
        type: "POST",
        async: true,
        url: app_path + 'src/App/Ajax/Processo/buscar_quantidades.php',
        success: function (response) {
            $('.qtde_processos_enviados').text(response.qtde_processos_enviados);
            $('.qtde_processos_receber').text(response.qtde_processos_receber);
            $('.qtde_processos_abertos').text(response.qtde_processos_abertos);
            $('.qtde_processos_arquivados').text(response.qtde_processos_arquivados);
            $('.qtde_processos_vencidos').text(response.qtde_processos_vencidos);
            $('.qtde_processos_contribuintes').text(response.qtde_processos_contribuintes);
        },
        dataType: 'json'
    });
}

/**
 * Inicia plugin de Ã¡rvore para listar setores
 * @param {type} jstree_id
 * @param {type} element_id
 * @param {type} search_tree_id
 * @param {type} user_filter
 * @param {type} params
 * @param {type} is_checkbox
 * @returns {undefined}
 */
function initJSTree(jstree_id, element_id, search_tree_id, user_filter, params, is_checkbox, disable_node_with_children =false) {
    var source = user_filter ? 'nos_usuario_ajax.php' : 'nos_ajax.php';
    source += (params != null ? params : '');
    $('#' + jstree_id).jstree({
        "checkbox": {
            "deselect_all": true,
            "keep_selected_style": false,
            "three_state": false
        },
        'core': {
            'strings': {'Loading ...': 'Carregando...'},
            'multiple': false,
            'data': {
                'url': app_path + 'src/App/Ajax/Setor/' + source,
                'data': function (node) {
                    return {'id': node.id};
                }
            }
        },
        "search": {
            "show_only_matches": true,
            "case_insensitive": true,
            'ajax': {
                'url': app_path + 'src/App/Ajax/Setor/buscar_nos_ajax.php',
                'dataType': 'json',
                'type': 'GET',
                success: function (response) {
                    console.log('consulta realizada na árvore.');
                }
            }
        },
        "plugins": ["search", is_checkbox ? "checkbox" : ""]
    });
    $('#' + jstree_id).on('changed.jstree', function (e, data) {
        var i, j, r = [];
        for (i = 0, j = data.selected.length; i < j; i++) {
            r.push(data.instance.get_node(data.selected[i]).id);
        }
        $('#' + element_id).val(r.join(', '));
        $('#' + element_id).trigger('change');
        var qtde_setores_sel = data.selected.length;
        if ($("#tabelaDetalheTramite").length) {
            if ($(".linha-destino").length < qtde_setores_sel) {
                var indice = data.selected.length - 1;
                var setor_destino_id = data.selected[indice];
                var status_inicial = $("#status_processo_todos").val();
                showLoading();
                $.post(app_path + 'src/App/View/Tramite/montar_detalhes_envio.php', {
                    setor_destino_id: setor_destino_id,
                    indice: indice,
                    status_inicial: status_inicial,
                    qtde_linhas: qtde_setores_sel,
                    setor_origem_id: $("#select_setor_origem").val()
                }, function (response) {
                    $("#tabelaDetalheTramite tbody").append(response);
                    $("#linha_setor_origem").attr('rowspan', qtde_setores_sel * 2);
                }).done(function () {
                    hideLoading();
                });
            } else {
                $(".linha-destino").each(function () {
                    var setor_id = $(this).attr('id').split('_')[1];
                    if ($.inArray(setor_id, data.selected) == -1) {
                        $("#linha_" + setor_id).remove();
                        $("#sublinha_" + setor_id).remove();
                        $("#linha_setor_origem").attr('rowspan', qtde_setores_sel * 2);
                    }
                });
            }
        }        
    });
    if (search_tree_id != null) {
        var to = false;
        $('#' + search_tree_id).keyup(function () {
            if (to) {
                clearTimeout(to);
            }
            to = setTimeout(function () {
                var search_text = $('#' + search_tree_id).val();
                // $('#' + jstree_id).jstree(true).show_all();
                $('#' + jstree_id).jstree(true).search(search_text);
            }, 250);
        });
    }
    if(disable_node_with_children){
        
        initDisabledNodeWithChildren(jstree_id);
    }
}
function initDisabledNodeWithChildren(elementId){
    $('#' + elementId).bind('loaded.jstree', function(e, data) {
        disabledNodeWithChildren(elementId);
    });
    $('#' + elementId).bind('open_node.jstree', function (){       
        disabledNodeWithChildren(elementId);        
    });
}
function disabledNodeWithChildren(elementId){
    $('#'+elementId+' li').each( function() { 
        if(this.classList[1] != 'jstree-leaf'){
            $("#"+elementId).jstree().disable_node(this.id);
        }            
    });
}
function validaAno($input) {
    var year = $input.val();
    var currYear = (new Date()).getFullYear();
    if (year >= 1500 && year <= currYear) {
        return true;
    } else {
        bootbox.alert('Ano "' + year + '" inválido!');
        $input.val('');
        return false;
    }
}

function alternarEntradaNumeroAnexo(thisObject, id){
    let $checkbox = $(thisObject);
    let element = $("#"+id);
    let isChecked = $checkbox.is(":checked");
    if (isChecked) {
        element.removeAttr("required");
        $checkbox.val(1);
        element.addClass("ignore-validate")
    } else {
        element.attr("required", true);
        $checkbox.val(0);
        element.removeClass("ignore-validate");
    }
    element.attr("disabled",thisObject.checked).val(null);
}

function validarCPF($input) {
    var cpf = $input.val().replace(/[^0-9]/g, '').toString();
    if (cpf == "") {
        return true;
    }
    if (cpf.length == 11) {
        var v = [];
        //Calcula o primeiro dígito de verificação.
        v[0] = 1 * cpf[0] + 2 * cpf[1] + 3 * cpf[2];
        v[0] += 4 * cpf[3] + 5 * cpf[4] + 6 * cpf[5];
        v[0] += 7 * cpf[6] + 8 * cpf[7] + 9 * cpf[8];
        v[0] = v[0] % 11;
        v[0] = v[0] % 10;

        //Calcula o segundo dígito de verificação.
        v[1] = 1 * cpf[1] + 2 * cpf[2] + 3 * cpf[3];
        v[1] += 4 * cpf[4] + 5 * cpf[5] + 6 * cpf[6];
        v[1] += 7 * cpf[7] + 8 * cpf[8] + 9 * v[0];
        v[1] = v[1] % 11;
        v[1] = v[1] % 10;

        //Retorna Verdadeiro se os dígitos de verificação são os esperados.
        if ((v[0] != cpf[9]) || (v[1] != cpf[10])) {
            bootbox.alert('CPF inválido!');
            $input.val('');
            return false;
        }
        return true;
    } else {
        bootbox.alert('CPF inválido!');
        $input.val('');
        return false;
    }
}

/**
 * Função para verificar se o CNPJ é valido
 * @returns {Boolean}
 * @param element elemento do html
 * @param event evento do elemento
 */
function validaCNPJ(element, event) {
    event.preventDefault();
    var CNPJ = $(element).val();
    if (CNPJ != "__.___.___/____-__") {
        var erro = new String;
        if (CNPJ.length < 18) {
            erro += "É necessário preencher corretamente o número do CNPJ. <br />";
        }
        if ((CNPJ.charAt(2) != ".") || (CNPJ.charAt(6) != ".") || (CNPJ.charAt(10) != "/") || (CNPJ.charAt(15) != "-")) {
            if (erro.length == 0) {
                erro += "É necessário preencher corretamente o número do CNPJ. <br />";
            }
        }
        //substituir os caracteres que não são números
        if (document.layers && parseInt(navigator.appVersion) == 4) {
            x = CNPJ.substring(0, 2);
            x += CNPJ.substring(3, 6);
            x += CNPJ.substring(7, 10);
            x += CNPJ.substring(11, 15);
            x += CNPJ.substring(16, 18);
            CNPJ = x;
        } else {
            CNPJ = CNPJ.replace(".", "");
            CNPJ = CNPJ.replace(".", "");
            CNPJ = CNPJ.replace("-", "");
            CNPJ = CNPJ.replace("/", "");
        }

        var nonNumbers = /\D/;
        if (nonNumbers.test(CNPJ)) {
            erro += "A verificação de CNPJ suporta apenas números! \n\n";
        }
        var a = [];
        var b = new Number;
        var c = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        for (i = 0; i < 12; i++) {
            a[i] = CNPJ.charAt(i);
            b += a[i] * c[i + 1];
        }
        if ((x = b % 11) < 2) {
            a[12] = 0
        } else {
            a[12] = 11 - x
        }

        b = 0;
        for (y = 0; y < 13; y++) {
            b += (a[y] * c[y]);
        }

        if ((x = b % 11) < 2) {
            a[13] = 0;
        } else {
            a[13] = 11 - x;
        }
        if ((CNPJ.charAt(12) != a[12]) || (CNPJ.charAt(13) != a[13])) {
            erro += "O CNPJ informado é inválido!";
        }
        if (erro.length > 0) {
            $(element).val('');
            bootbox.alert({
                animate: false,
                message: erro
            });
            return false;
        }
        return true;
    }
}

function usuarioListaInativo(value = "1"){
    let busca = (value == "Todos") ? "" : value;
    //valor da busca é contrário a pergunta por conta do valor do DataTable
    switch (value) {
        case '1':
            busca = "NÃO";
          break;
        case '0':
            busca = "SIM";
          break;
        case '-1':
            busca = "";
          break;
      }
    let table = $('#tabelaListagemUsuarios').DataTable();
    table.columns(4).search(busca).draw();
}
// ###############################
// ##        Solicitações       ##
// ###############################
$(function () {
    $("#tabelaSolicitacao").DataTable({
        language: {
            url: app_url + '/assets/js/locale/DataTables/pt_br.json'
        },
        "order": [[ 6, 'asc' ], [ 1, 'asc' ]],
        "processing": true,
        "serverSide": true,
        "bStateSave": false,
        "ajax": app_url + "/src/App/Ajax/Solicitacao/listar.php",
        "initComplete": function (settings, json) {
            $(this).fadeIn();
            let tabela = this;
            let api = tabela.api();
            $('#solicitacao-anexo-tipo').keyup(function () {
                api.column(2).search($(this).find("input").val(), false, false, false).draw();
            });
            $('#solicitacao-anexo-descricao').keyup(function () {
                api.column(3).search($(this).find("input").val(), false, false, false).draw();
            });
            $('#solicitacao-anexo-numero').keyup(function () {
                api.column(1).search($(this).find("input").val(), false, false, false).draw();
            });
            $('#solicitacao-anexo-data').keyup(function () {
                api.column(5).search($(this).find("input").val(), false, false, false).draw();
            });
        },
    });
})

function aprovarSolicitacaoAnexo(e) {
    showLoading();
    let button = (e.originalEvent instanceof Event) ? $(this) : $(e);
    let solicitacaoId = button.data("solicitacao-id");
    $.post({
            url: app_url + "/Solicitacao/aprovar/" + solicitacaoId
        }, 'json'
    ).done(function (data) {
        if (data.tipo !== undefined) {
            showGrowMessage(data.tipo, data.msg);
        } else {
            showGrowMessage("success", "Solicitação aprovada.");
        }
        atualizarLinhaComoAprovado($("[data-solicitacao-id='" + solicitacaoId + "']").parent().parent().children());
        $("#modal-solicitacao-visualizacao").modal("toggle");
    }).fail(function (xhr, textStatus, errorThrown) {
        let response = xhr.responseJSON;
        console.log(xhr);
        if (xhr.status === 423) {
            mostrarMensagemBloqueioPorTramitacao(xhr);
        } else {
            showGrowMessage(response.tipo, response.msg);
        }
    }).always(function (){
        hideLoading();
    });
}

function reprovarSolicitacaoAnexo(e) {
    showLoading();
    let button = (e.originalEvent instanceof Event) ? $(this) : $(e); //
    let solicitacaoId = button.data("solicitacao-id");
    $.post({
            url: app_url + "/Solicitacao/recusar/" + solicitacaoId
        }, 'json'
    ).done(function (data) {
        if (data.tipo !== undefined) {
            showGrowMessage(data.tipo, data.msg);
        } else {
            showGrowMessage("success", "Solicitação recusada.");
        }
        atualizarLinhaComoRecusado($("[data-solicitacao-id='" + solicitacaoId + "']").parent().parent().children());
        $("#modal-solicitacao-visualizacao").modal("toggle");
    }).fail(function (xhr, textStatus, errorThrown) {
        let response = xhr.responseJSON;
        showGrowMessage(response.tipo, response.msg);
    }).always(function (){
        hideLoading();
    });
}

function mostrarMensagemBloqueioPorTramitacao(xhr) {
    let data = xhr.responseJSON;
    let acao;
    let tituloAcao;
    if (data.objeto_id !== undefined) {
        tituloAcao = "Ver tramitações"
        acao = function () {
            window.location.href = app_url + "processo/editar/id/" + data.objeto_id + "#historicoTramitesTab";
        }
    }
    showGrowMessage(data.tipo, "Não é possível remover o anexo porque ele foi definido como obrigatório por uma tramitação. Remova a obrigatoriedade do anexo para que possa ser removido.", 500, 15000, tituloAcao, acao);
}

function atualizarLinhaComoRecusado(row) {
    $(row[6]).text("Recusado");
    $(row[7]).find(".btn-solicitacao-aprovar").remove();
    $(row[7]).find(".btn-solicitacao-reprovar").prop('disabled', true);
    $(row[7]).find(".btn-solicitacao-reprovar").removeClass('mr-1');
}

function atualizarLinhaComoAprovado(row) {
    $(row[6]).text("Aprovado");
    $(row[7]).find(".btn-solicitacao-reprovar").remove();
    $(row[7]).find(".btn-solicitacao-aprovar").prop('disabled', true);
    $(row[7]).find(".btn-solicitacao-aprovar").removeClass('ml-1');
}

function habilitarSolicitacaoEdicaoAnexo() {
    $("#formAnexo > div:nth-child(14) > button").text("Solicitar Edição");
    $("#anexoModal > div > div > div.modal-body > p")
        .removeClass("alert-warning")
        .addClass("alert-info")
        .text("Habilitado edição para abertura de solicitação de alteração.");
    $("input[name='motivo']").attr("required", true);
    $("[disabled]").removeAttr("disabled");
    $("[class*='disabled']").removeClass("disabled");
    $(".form-anexo").removeClass("hidden");
}

function habilitarTramitacao(tramite_id, qtde_tramites, processo) {
    tramitar(tramite_id, qtde_tramites, processo);
}