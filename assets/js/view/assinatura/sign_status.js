var index = 0;
$('.btn-info-sign').on('click', function (e){
    // debugger;
    e.preventDefault();
    $('.app-overlay').show();
    let href = $(this).attr('href');

    let selector = '#lxsign-modal-' + $(this).data('anexo-id');
    if ($(selector).length < 1) {
        showLoading();
        loadSignInfo(href, function (result) {
            // debugger;
            let modal = createModalSignStatus(result);
            hideLoading();
            modal.modal('show');
        });
    } else {
        $(selector).modal('show');
    }
});

function loadSignInfo(href, response) {
    // debugger;
    $.post(href, function(data) {
        data = JSON.parse(data);
        response(data.document);
    });
}

function createModalSignStatus(data) {
    let selector = '#lxsign-modal-' + data.id;
    console.log(selector);
    let modal = $('#lxsign-modal').clone();
    modal.attr('id', 'lxsign-modal-' + data.id);
    modal.attr('z-index', --index);
    $('#modal-box').append(modal);

    /* Conteúdo */
    let content = '<table class="table"> ' +
        '   <tr>' +
        '       <td><label><strong>Número:</strong></label></td>' +
        '       <td><p>' + data.number + '/' + data.year + '</p></td>' +
        '   </tr>' +
        '   <tr> ' +
        '       <td><label><strong>Descrição:</strong></label></td>' +
        '       <td><p>' + data.description + '</p></td>' +
        '   </tr>' +
        '   <tr> ' +
        '       <td><label><strong>Anexado em:</strong></label></td>' +
        '       <td><p>' + data.uploaded_at + '</p></td>' +
        '   </tr>' +
        '   <tr> ' +
        '       <td><label><strong>Tipo:</strong></label></td>' +
        '       <td><p>' + data.type + '</p></td>' +
        '   </tr>' +
        '   <tr> ' +
        '       <td><label><strong>Prazo:</strong></label></td>' +
        '       <td><p>' + data.deadline_at + '</p></td>' +
        '   </tr>' +
        '   <tr> ' +
        '       <td><label><strong>Status:</strong></label></td>' +
        '       <td><p>' + data.status + '</p></td>' +
        '   </tr>' +
        '   <tr> ' +
        '       <td><label><strong>Signatários:</strong></label></td>' +
        '       <td><ul>' +
        '           <SIGNATARIOS>' +
        '       </ul></td>' +
        '   </tr>' +
        '</table>';

    /* Status por signatário. */
    let signersContent = '';
    data.signatures.forEach(function (item){
        signersContent+= '<li><strong>' + item.signer + ':</strong> ' + item.status + '</li>';
    });
    content = content.replace('<SIGNATARIOS>', signersContent);
    modal.find('.modal-body').append(content);
    $('.lxsign-modal-close').on('click', function(){
        modal.modal('hide');
    });
    modal.on('hidden.bs.modal', function () {
         $('.app-overlay').hide();
    });
    return modal;
}