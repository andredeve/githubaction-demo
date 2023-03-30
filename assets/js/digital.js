$(document).ready(function () {
    Pace.stop();
    $(".btn-reordenar").click(function (e){
        e.preventDefault();
        e.stopPropagation();
        let componente_id = $(this).attr('componente');
        let app_path  = $("#app_path").val();
        $.post(app_path + 'Componente/reordenar', {
            componente_id: componente_id
        }, function (response) {
            createModal("reordenarModal", "Mudar Ordem", response, 'modal-lg');
            $('[data-toggle="tooltip"]').tooltip();
                initSelect2();
        }).done(function () {
//            hideLoading();
        });
    });
    
    calcularTamanhoFrame();
    $(window).resize(function () {
        calcularTamanhoFrame();
    });
    $('.arquivo-processo').click(function (e) {
        e.preventDefault();
        $("#listaDocumentos").find('.list-group-item').each(function () {
            $(this).removeClass('active');
        });
        $(this).addClass('active');
        var pdf = $(this).attr('href');
        $("#object_pdf").attr('src', pdf);
    });
    
    $(".btn-download-processo").click(function (){
        showLoading();
        var processo = document.getElementById("listaDocumentos").querySelectorAll("input:checked");
        params = new FormData();
        var href = $(this).attr('href');
        var numero = $(this).attr('processo-numero');
        var exercicio = $(this).attr('processo-exercicio');
        if(processo.length > 0)
            processo.forEach(p => params.append('anexos[]', p.name));

        var request = new XMLHttpRequest();
        request.open("POST", href, true); 
        request.responseType = "blob";
        request.onload = function (e) {
            if (this.status === 200) {
                // `blob` response
                // console.log(this.response);
                // create `objectURL` of `this.response` : `.pdf` as `Blob`
                var file = window.URL.createObjectURL(this.response);
                var a = document.createElement("a");
                a.href = file;
                a.download = "Processo_" + numero + "_" + exercicio;
                document.body.appendChild(a);
                a.click();
                // remove `a` following `Save As` dialog, 
                // `window` regains `focus`
                window.onfocus = function () {                     
                document.body.removeChild(a)
                }
            };
            hideLoading();
        };
        request.send(params);
        //load();
    });

    $('.btn-proximo-arquivo').click(function (){
        proximoArquivo();
    });

    $('.btn-arquivo-anterior').click(function (){
        arquivoAnterior();
    });

});

function proximoArquivo(){
    $('#listaDocumentos').find('a').each(function  (key, value){        
        if($(this).hasClass('active')){
            let proximoItem = key+1;
            if($('#listaDocumentos').find('a')[proximoItem]){
                $('#listaDocumentos').find('a')[proximoItem].click();
            }
            return false ;
        }
    });
}

function arquivoAnterior(){
    $('#listaDocumentos').find('a').each(function  (key, value){        
        if($(this).hasClass('active')){
            let proximoItem = key-1;
            if($('#listaDocumentos').find('a')[proximoItem]){
                $('#listaDocumentos').find('a')[proximoItem].click();
            }
            return false ;
        }
    });
}

function calcularTamanhoFrame() {
    var height = $(window).height() - 150;
    $("#object_pdf").height(height);
    $("#listaDocumentos").height(height-10);
}

var loadedCount = 0;

function load() {
    let src = $("#app_path").val()+'lib/pdfjs/build/pdf.js';
    var pdfjsLib = window[src];
    
    // Load PDFs one after another
    pdfjsLib.getDocument(urls[loadedCount])
            .promise.then(function (pdfDoc_) {
                console.log("loaded PDF " + loadedCount);
                pdfDocs.push(pdfDoc_);
                loadedCount++;
                if (loadedCount !== urls.length) {
                    return load();
                }

                console.log("Finished loading");
                totalPageCount = getTotalPageCount();
                document.getElementById("page_count").textContent = totalPageCount;

                // Initial/first page rendering
                renderPage(pageNum);
            });
}