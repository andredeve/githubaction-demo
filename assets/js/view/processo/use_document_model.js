$("#useModelCheckbox").change(function () {
    if (this.checked) {
        $("#file-input").addClass("hidden");
        $("#model-input").removeClass("hidden");
        $(".documentModelInput").attr("required", true);
        $("#arquivo_processo").attr("required", false);
        $("#tipo_upload").val("model");
    } else {
        $("#file-input").removeClass("hidden");
        $("#model-input").addClass("hidden");
        $(".documentModelInput").attr("required", false);
        $("#tipo_upload").val("upload");
    }
});
/**********************************/
/***Última Alteração: 03/02/2023***/
/*************André****************/
/**********************************/
$("#selectModelo").change(function () {
    let option = $("#selectModelo option:selected").text();
    let value = $("#selectModelo option:selected").val();

    if (option == 'Modelo Atual' && value != "AtualizarModeloTemp"){
        let anexo_id =  $(this).val();
        showLoading();
        $.post(app_path + 'src/App/Ajax/ModeloDocumento/get_preview_config.php', {anexo_id: anexo_id}, function (response) {       
            tinyMCE.activeEditor.setContent('');
            tinymce.activeEditor.execCommand('mceInsertContent', false, ` ${response.preview_config.texto_ocr} `);        
        }).done(function () {
            hideLoading();           
        });
    }else if (option != "Modelo Atual"){
        let modelo_id = $(this).val();
        showLoading();
        $.post(app_path + 'src/App/Ajax/ModeloDocumento/get_preview_config.php', {modelo_id: modelo_id}, function (response) {       
            tinyMCE.activeEditor.setContent('');
            tinymce.activeEditor.execCommand('mceInsertContent', false, ` ${response.preview_config.texto} `);        
        }).done(function () {
            hideLoading();           
        });
    }else if (option == 'Modelo Atual' && value == "AtualizarModeloTemp"){
        let texto = $("#texto_temp").data("texto");
        tinyMCE.activeEditor.setContent('');
        tinymce.activeEditor.execCommand('mceInsertContent', false, ` ${texto} `);       
    } 
});


