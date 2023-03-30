/**********************************/
/***Última Alteração: 03/02/2023***/
/*************André****************/
/**********************************/
$(function () {
    $('.form-validate-ajax-file').submit(function (e) {
        e.preventDefault();
        $('.progress').removeClass('hidden');
    });
    $('.form-validate-ajax-file').validate({
        submitHandler: function (form) {
            var entidade = $(form).find('input[type=hidden][name=entidade]').val();
            var $progress = $(form).find('.progress');
            $(form).ajaxSubmit({
                beforeSubmit: function () {
                    if ($("#is_anexo_selecionado").val() == 1)
                        $progress.show();
                    showLoading();
                },
                dataType: 'json',
                success: function (response) {
                    showGrowMessage(response.tipo, response.msg);
                    if (response.tipo == 'success'  && typeof (entidade) !== "undefined") {
                        window.location.href = app_path + entidade;
                    } else {
                        $('.progress').hide();
                        hideLoading();
                    }
                }
            });
        }
    });
});