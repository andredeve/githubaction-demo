var app_url = $("#app_url").val();
$(function () {
    $("#loginForm").validate({
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
                    window.location.href = response.objeto_id != null ? response.objeto_id : app_url;
                } else {
                    l.stop();
                    if(recaptcha && typeof recaptcha.reset == "function"){
                        recaptcha.reset();
                    }
                }
                console.log(response)
                showGrowMessage(response.tipo, response.msg);
            }, 'json');
            return false;
        }
    });
    $("#recuperaSenhaForm").validate({
        submitHandler: function (form) {
            var l = Ladda.create(form.querySelector('.ladda-button'));
            l.start();
            $.post($(form).attr('action'), $(form).serialize(), function (response) {
                if (response.tipo == 'success') {
                    $("#alteraSenhaModal").modal('hide');
                }
                bootbox.alert(response.msg);
            }, 'json').done(function () {
                l.stop();
            });
            return false;
        }
    });
    $("body").fadeIn('slow');
});

/**
 * Função que exibe uma mensagem na tela ao usuário
 * @param {type} tipo : error,success ou warning
 * @param {type} msg
 * @returns {undefined}
 */
function showGrowMessage(tipo, msg) {
    var icone = "";
    var type = "";
    if (tipo == 'error') {
        icone = 'fa fa-ban';
        type = 'danger';
    } else if (tipo == 'success') {
        icone = 'fa fa-check';
        type = 'success';
    } else if (tipo == 'warning') {
        icone = 'fa fa-info-circle';
        type = 'info';
    }
    $.bootstrapGrowl("<i class='" + icone + "'></i> " + msg, {type: type, width: 300, delay: 5000});
}