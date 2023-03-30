$('#btn_add_signatario').on('click', (event) => {
    event.preventDefault();
    $("#form_add_signatario").show();
    $("#assinaturaForm").hide();
    $("#btn_add_signatario").hide();
    hideLoading();
})


$('#btn_add_signatario_cancelar').on('click', (event) => {
    event.preventDefault();
    $("#form_add_signatario").hide();
    $("#assinaturaForm").show();
    $("#btn_add_signatario").show();
})
$("#form_add_signatario").on('submit', (event) => {
    event.preventDefault();
});

$("#form_add_signatario").validate({
    submitHandler: function (form) {
        showLoading();
        $.post($(form).attr("action"), $(form).serialize(), function (response){
            showGrowMessage(response.tipo, response.msg);
            $(form).closest(".modal").modal("hide");
            atualizarAnexos(processo_id);
        }, "json")
            .done(function () {
                hideLoading();
            });
    }
});