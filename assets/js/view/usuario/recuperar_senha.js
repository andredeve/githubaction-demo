$(document).ready(function () {
    $('.btn-enviar-senha').click(function (){
        let usuario_id = $(this).attr('usuario');
        bootbox.confirm({
            title: "",
            message: "Deseja realmente criar e enviar uma nova senha para o usuário selecionado?",
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
                    $.post(app_path + 'usuario/gerarSenha', 
                        {usuario_id: usuario_id}, 
                        function (response) {
                            showGrowMessage(response.tipo, response.msg);                
                    }, 'json').done(() => {hideLoading()});

                }
            }
        });
    });
});