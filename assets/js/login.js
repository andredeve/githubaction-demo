var app_url = $("#app_url").val();
$(function () {
    $.ajaxSetup({
        timeout: 1500000, //60 segundos
        error: function (xhr, textStatus, errorThrown) {
            if ($('.ladda-spinner').length) {
                Ladda.stopAll();
            }
            if (textStatus === 'timeout') {
                showGrowMessage('warning', 'Tempo limite excedido: sua conexão parece estar lenta, tente novamente.');
            } else {
                showGrowMessage('error', errorThrown);
            }
        }
    });
    /**
     * Botão com classe .closeMessage remove o elemento da tela
     */
    $('.closeMessage').click(function () {
        $(this).parent().fadeOut();
    });
    $('.login_mask').on('keyup change',function () {
        let login = $(this).val();
        if(login.length == 0){
            $(this).unmask();
        }else if(!isNaN(login)){
            var SPMaskBehavior_cpf_cnpj = function (val) {
                return val.replace(/\D/g, '').length <= 11 ? '000.000.000-000000' : '00.000.000/0000-00';
            };
            var spOptions_cpf_cnpj = {
                onKeyPress: function (val, e, field, options) {
                    field.mask(SPMaskBehavior_cpf_cnpj.apply({}, arguments), options);
                }
            };
            $(this).mask(SPMaskBehavior_cpf_cnpj, spOptions_cpf_cnpj);
        }
    })
    // override jquery validate plugin defaults
    $.validator.setDefaults({
        highlight: function (element) {
            $(element).closest('.form-group').addClass('has-error');
            $(element).closest('.form-group').removeClass('has-success');
        },
        unhighlight: function (element) {
            $(element).closest('.form-group').removeClass('has-error');
            $(element).closest('.form-group').addClass('has-success');
        },
        errorElement: 'small',
        errorClass: 'form-text text-danger',
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }
        }
    });
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