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
                uploadProgress: function (event, position, total, percentComplete) {
                    updateProgressBar($progress, percentComplete);
                },
                dataType: 'json',
                success: function (response) {
                    showGrowMessage(response.tipo, response.msg);
                    if (response.tipo == 'success' && typeof (entidade) !== "undefined") {
                        window.location.href = app_path + entidade;
                    } else {
                        $('.progress').hide();
                        hideLoading();
                        updateProgressBar($progress, 0);
                    }
                }
            });
        }
    });
    
    if ($("#arquivo_modelo").length) {
        var preview = $("#arquivo_modelo").attr('preview') != "" ? $("#arquivo_modelo").attr('preview') : null;
        var $form = $("#arquivo_modelo").closest('form');
        var modelo_id = $form.find('input[type=hidden][name=id]').val();
        if (preview != null) {
            showLoading();
            $.post(app_path + 'src/App/Ajax/ModeloDocumento/get_preview_config.php', {
                modelo_id: modelo_id,
            }, function (response) {
                var previewContent = preview != null ? preview : [];
                var previewConfig = response.preview_config;
                $("#arquivo_modelo").fileinput({
                    showUpload: false,
                    showRemove: false,
                    showCancel: false,
                    theme: "fa",
                    overwriteInitial: true,
                    initialPreviewShowDelete: false,
                    language: 'pt-BR',
                    previewFileIcon: '<i class="fa fa-file-word-o"></i>',
                    allowedFileExtensions: ["docx"],
                    initialPreviewAsData: true, // identify if you are sending preview data only and not the raw markup
                    initialPreviewFileType: 'office', // image is the default and can be overridden in config below
                    initialPreview: previewContent,
                    initialPreviewConfig: previewConfig,
                    previewFileIconSettings: { // configure your icon file extensions
                        'doc': '<i class="fa fa-file-word-o text-primary"></i>',
                    },
                    previewFileExtSettings: { // configure the logic for determining icon file extensions
                        'doc': function (ext) {
                            return ext.match(/(docx)$/i);
                        }
                    }
                });
            }, 'json').done(function () {
                $("#arquivo_modelo").fadeIn();
                hideLoading();
            });
        } else {
            $("#arquivo_modelo").fileinput({
                showUpload: false,
                showRemove: false,
                showCancel: false,
                language: 'pt-BR',
                previewFileIcon: '<i class="fa fa-file-word-o"></i>',
                theme: "fa",
                initialPreviewFileType: 'office',
                'allowedFileExtensions': ['docx'],
                initialPreviewAsData: true,
                previewFileIconSettings: { // configure your icon file extensions
                    'doc': '<i class="fa fa-file-word-o text-primary"></i>',
                },
                previewFileExtSettings: { // configure the logic for determining icon file extensions
                    'doc': function (ext) {
                        return ext.match(/(docx)$/i);
                    }
                }
            });
        }
    }
});