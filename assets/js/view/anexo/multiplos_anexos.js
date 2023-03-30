
$("#multi-arquivos").fileinput({
    maxFileCount: 20,
    validateInitialCount: true,
    showUpload: false,
    showRemove: false,
    showCancel: false,
    theme: "fa",
    overwriteInitial: true,
    initialPreviewShowDelete: false,
    language: 'pt-BR',
    allowedFileExtensions: ["jpg", "png", "gif", "jpeg", "doc", "docx", "pdf"],
    initialPreviewAsData: true, // identify if you are sending preview data only and not the raw markup
    initialPreviewFileType: 'image', // image is the default and can be overridden in config below
    layoutTemplates: {
        main1: "{preview}\n" +
            "<div class=\'input-group {class}\'>\n" +
            "   {caption}\n" +
            "   <div class=\'input-group-btn\ input-group-prepend'>\n" +
            "       {browse}\n" +
            "       {upload}\n" +
            "       {remove}\n" +
            "   </div>\n" +
            "</div>",
        actions: '<div class="file-actions">\n' +
            '    <div class="file-footer-buttons">\n' +
            '        {upload} {download}  {zoom} {other}' +
            '    </div>\n' +
            '    {drag}\n' +
            '    <div class="clearfix"></div>\n' +
            '</div>',
    }
});

$("#formAnexoMulti").validate({
    ignore: ":hidden, .select2-search__field, .ignore-validate",
    submitHandler: function (form) {
        let l = Ladda.create(form.querySelector('.ladda-button'));
        $(form).ajaxSubmit({
            beforeSubmit: function () {
                l.start();
            },
            uploadProgress: function (event, position, total, percentComplete) {
                updateProgressBar($(form).find('.progress'), percentComplete);
            },
            dataType: 'json',
            success: function (response) {
                if (response.tipo === 'success') {
                    atualizarAnexos($("#processo_id").val());
                    $(form).trigger("reset")
                    $(form).closest('.modal').modal('hide');
                    showGrowMessage(response.tipo, "Os anexos foram adicionados ao processo.");
                } else if (response.tipo === 'partial_success') {
                    atualizarAnexos($("#processo_id").val());
                    $(form).trigger("reset")
                    let modal_id = "upload-attachments-report";
                    let modal_title = "Status do envio dos documentos";
                    let modal_content = makeTable(makeHead() + makeBody(response.data));
                    createModal(modal_id, modal_title, modal_content)
                    $(form).closest('.modal').modal('hide');
                } else {
                    showGrowMessage(response.tipo, response.msg);
                }

            },
            complete: function () {
                updateProgressBar($(form).find('.progress'), 0);
                l.stop();
                $('.progress').addClass('hidden');
            }
        });
    }
});


function makeTable(child) {
    let content = `<table class="table">`;
    content += child;
    content += `</table>`;
    return content;
}

function makeHead() {
    return `<thead>
                <th>Arquivo</th>
                <th class="text-center">Status</th>
                <th>Mensagem</th>
        </thead>`;
}

function makeBody(data) {
    let content= "";
    for (let i = 0; i < data.length; i++) {
        let val = data[i];
        let type;
        let status;
        let icon;
        if (val['tipo'] === "success") {
            type = "success";
            status = "Sucesso";
            icon = "check";
        } else {
            type = "danger";
            status = "Erro";
            icon = "close";
        }
        content += `<tr>
            <td class="col-4">` + val['nome_arquivo'] + `</td>
            <td class="text-center col-2 text-` + type + `"><span class="fa fa-` + icon + `"></span> ` + status + `</td>
            <td class="col-6">` + val['msg'] + `</td>
        </tr>`;
    }
    return "<tbody>" + content + "</tbody>";
}