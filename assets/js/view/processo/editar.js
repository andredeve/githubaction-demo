$(function () {
    if($("#abrirTramitacao").val()) {
        let tramite_id = $('#tramite_id').val();
        let processo = $('#processo').val();
        tramitar(tramite_id, processo, null, false, false);
    }

    let hash = window.location.hash;
    if (hash !== undefined && hash !== '') {
        console.log("hash: " + hash);
        $('.nav-tabs a[href=' + hash + ']').tab('show');
    }
});