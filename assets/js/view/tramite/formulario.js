$(function() {
    $('.grupo_assinatura').on('change', function () {
        let id = this.getAttribute('name').split('_')[3];
        let options = $('#grupo-id-' + this.value).children('option').clone();
        let tipoDocumentoSelect = $('#tipo_documento_campo_' + id);
        tipoDocumentoSelect.children('option').remove();
        tipoDocumentoSelect.append(options);
        tipoDocumentoSelect.trigger('change');
    });
});