/**
 * Id dos usuários adicionais que terão permissão para visualizar o processo.
 * @type {*[]}
 */
var usuariosSelecionados = [];

$(function (){
    /**
     * Mostrar/ocultar campo de identificação de usuários adicionais que terão permissão de acesso.
     */
    $('select[name="sigilo"]').on('change', function () {
        if ($('select[name="sigilo"] option:selected').val() === 'sigiloso') {
            $("#users-permission").show();
        } else {
            $("#users-permission").hide();
        }
    })

    /**
     * Busca assíncrona de usuários.
     */
    let usuariosSelect = $('#select_usuarios_permitidos');
    usuariosSelect.select2({
        ajax: {
            url: usuariosSelect.data("app-action"),
            dataType: "json",
            data: function (params) {
                return {
                    search: params.term,
                    page: params.page || 1
                }
            }
        },
        language: "pt-BR"
    });

    /**
     * Modal a ser mostrado ao clicar no botão de pesquisa de usuários.
     */
    $(".btn-pesquisar-usuario").on('click', function (e) {
        e.preventDefault();
        showLoading();
        $.post(app_path + 'src/App/View/Usuario/pesquisar.php', function (response) {
            createModal('pesquisaUsuarioModal', 'Pesquisar Usuário', response, 'modal-lg');
            initTabelaPesquisaSigilo();
            usuariosSelecionados = [];
        }).done(function () {
            hideLoading();
        });
    });
});

/**
 * Setup da tabela.
 */
function initTabelaPesquisaSigilo() {
    let tabela = $("#tabelaPesquisaUsuarios");
    let form = $('#formPesquisaUsuario');
    let url = tabela.attr('url');
    tabela.dataTable({
        searching: false,
        select: {
            style: 'multi',
        },
        language: {
            select: {
                rows: {
                    _: "%d linhas selecionadas.",
                    0: "", //"Clique em uma linha para selecioná-la.",
                    1: "1 linha selecionada."
                }
            }
        },
        serverSide: true,
        dataType: 'json',
        columns: [
            {"data": "id"},
            {"data": "nome"},
        ],
        ajax: {
            url: url,
            dataFilter: function(data){
                return JSON.stringify(JSON.parse(data));
            }
        },
        stateSave: true,
        "initComplete": function (settings, json) {
            let api = this.api();
            let codigo = form.find('.codigo_filter');
            let nome = form.find('.nome_filter');
            // Busca por id.
            codigo.keyup(function () {
                api.ajax.url(url + '?id=' + $(this).val()).load();
            });
            // Busca por nome.
            nome.keyup(function () {
                api.ajax.url(url + '?nome=' + $(this).val()).load();
            });
        }
    });
    let dataTable = tabela.DataTable();
    // Armezenar em memória o id dos usuários selecionados.
    dataTable.on( 'select', function ( e, dt, type, indexes ) {
        if ( type === 'row' ) {
            let data = dataTable.rows(indexes).data();
            let id = data.pluck( 'id' )[0];
            let nome = data.pluck( 'nome' )[0];
            usuariosSelecionados.push({id: id, nome: nome});
        }
    // Remover da memória do id dos usuários selecionados.
    }).on( 'deselect', function ( e, dt, type, indexes ) {
        if ( type === 'row' ) {
            let id = dataTable.rows(indexes).data().pluck( 'id' )[0];
            usuariosSelecionados = usuariosSelecionados.filter(function (item) {
                return item.id !== id;
            });
        }
    });
    /**
     * Atualizar campo do tipo select com os usuários selecionados.
     */
    form.on("submit", function (e){
        e.preventDefault();
        $(this).closest('.modal').modal('hide');
        let select = $("#select_usuarios_permitidos");
        select.empty();
        usuariosSelecionados.forEach(function (item) {
            let element = "<option value='" + item.id + "' selected>" + item.nome + "</option>";
            select.append(element);
        });
    });
}