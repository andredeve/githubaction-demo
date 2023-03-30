$('#processoForm,#tramitarProcessoForm').on('click', '.btn-adicionar-campo', function (e) {
    e.preventDefault();
    div_id = this.getAttribute("add-id");
    div_add = document.getElementById("mais-arquivos-"+div_id);
    campo_id = div_add.getAttribute("data-campo-id");
    qtde = parseInt(div_add.getAttribute("qtde"));
    new_qtde = qtde+1;
    var form = document.getElementById("div-"+div_id+"-campos-arquivo-"+qtde);

    resetaLibs(form);

    var clone = form.cloneNode(true);
    div_add.setAttribute("qtde", new_qtde);
    

    renomeiaCampos(clone, div_id, campo_id, qtde, new_qtde);

    document.getElementById("mais-arquivos-"+div_id).appendChild(clone);

    initSelect2();
    initFileInput();
    initDatePicker();

    requisitosjs = document.getElementById("requisitosjs");
    src = requisitosjs.getAttribute("src");
    requisitosjs.remove();
    $('<script/>').attr({'src':src,'id':"requisitosjs"}).appendTo('head');

}).on('click', '.btn-remover-campo', function (e) {
    div_id = this.getAttribute("remove-id");
    div_add = document.getElementById("mais-arquivos-"+div_id);
    qtde = parseInt(div_add.getAttribute("qtde"));
    if(qtde > 0){
        new_qtde = qtde-1;
        div_add.setAttribute("qtde", new_qtde);
        document.getElementById("mais-arquivos-"+div_id).lastChild.remove();
    }
});

function resetaLibs(form){

    spanremoval = form.querySelectorAll("span.select2");
    spanremoval.forEach(p => p.remove());
    selectreset = form.querySelectorAll("select.select2");
    selectreset.forEach(p => p.classList.remove("select2-hidden-accessible"));
    dataidcheck = form.querySelectorAll("[data-select2-id]");
    dataidcheck.forEach(p => p.removeAttribute("data-select2-id"));
    datePickerReset = form.querySelectorAll("input.hasDatepicker");
    datePickerReset.forEach(p => p.classList.remove("hasDatepicker"));

};

function renomeiaCampos(clone, div_id, campo_id ,qtde, new_qtde){

    clone.id = "div-"+div_id+"-campos-arquivo-" + new_qtde;
    clone.children[0].children[0].children["data_assinatura_campo_"+campo_id+"["+qtde+"]"].name =  "data_assinatura_campo_"+campo_id+"["+new_qtde+"]";
    clone.children[0].children[0].children["data_assinatura_campo_"+campo_id+"["+qtde+"]"].id =  "data_assinatura_campo_"+campo_id+"["+new_qtde+"]";
    clone.children[0].children[1].children["numero_campo_"+campo_id+"_"+qtde].name =  "numero_campo_"+campo_id+"["+new_qtde+"]";
    clone.children[0].children[1].children["numero_campo_"+campo_id+"_"+qtde].id =  "numero_campo_"+campo_id+"_"+new_qtde;
    clone.children[0].children[1].children[0].children[1].children["documento_auto_numeric_"+campo_id+"["+qtde+"]"].name =  "documento_auto_numeric_"+campo_id+"["+new_qtde+"]";
    clone.children[0].children[1].children[0].children[1].children["documento_auto_numeric_"+campo_id+"["+qtde+"]"].attributes[5].nodeValue = "alternarEntradaNumeroAnexo(this,'numero_campo_"+campo_id+"_"+new_qtde+"')"
    clone.children[0].children[1].children[0].children[1].children["documento_auto_numeric_"+campo_id+"["+qtde+"]"].id =  "documento_auto_numeric_"+campo_id+"["+new_qtde+"]";
    clone.children[0].children[2].children["ano_campo_"+campo_id+"["+qtde+"]"].name =  "ano_campo_"+campo_id+"["+new_qtde+"]";
    clone.children[0].children[2].children["ano_campo_"+campo_id+"["+qtde+"]"].id =  "ano_campo_"+campo_id+"["+new_qtde+"]";
    clone.children[0].children[3].children["data_campo_"+campo_id+"["+qtde+"]"].name =  "data_campo_"+campo_id+"["+new_qtde+"]";
    clone.children[0].children[3].children["data_campo_"+campo_id+"["+qtde+"]"].id =  "data_campo_"+campo_id+"["+new_qtde+"]";

    clone.children[1].children[0].children["grupo_assinatura_campo_"+campo_id+"["+qtde+"]"].name =  "grupo_assinatura_campo_"+campo_id+"["+new_qtde+"][]";
    clone.children[1].children[0].children["grupo_assinatura_campo_"+campo_id+"["+qtde+"]"].id =  "grupo_assinatura_campo_"+campo_id+"["+new_qtde+"]";
    clone.children[1].children[1].children["signatario_assinatura_campo_"+campo_id+"["+qtde+"]"].name =  "signatario_assinatura_campo_"+campo_id+"["+new_qtde+"][]";
    clone.children[1].children[1].children["signatario_assinatura_campo_"+campo_id+"["+qtde+"]"].id =  "signatario_assinatura_campo_"+campo_id+"["+new_qtde+"]";
    clone.children[1].children[2].children["empresa_campo_"+campo_id+"["+qtde+"]"].name =  "empresa_campo_"+campo_id+"["+new_qtde+"]";
    clone.children[1].children[2].children["empresa_campo_"+campo_id+"["+qtde+"]"].id =  "empresa_campo_"+campo_id+"["+new_qtde+"]";
    clone.children[1].children[3].children["tipo_documento_campo_"+campo_id+"["+qtde+"]"].name =  "tipo_documento_campo_"+campo_id+"["+new_qtde+"]";
    clone.children[1].children[3].children["tipo_documento_campo_"+campo_id+"["+qtde+"]"].id =  "tipo_documento_campo_"+campo_id+"["+new_qtde+"]";
    
    var insereArquivo = document.createElement('input');
    insereArquivo.setAttribute('type', "file");
    insereArquivo.setAttribute('name', "campo_"+campo_id+"["+new_qtde+"]");
    insereArquivo.setAttribute('accept', "application/pdf");
    insereArquivo.setAttribute('id', "campo_"+campo_id+"["+new_qtde+"]");
    insereArquivo.setAttribute('class', "fileinput");

    if(clone.querySelector('input[type="file"]').hasAttribute('required')){
        insereArquivo.setAttribute('required', "true");
    }

    clone.children[1].children["div-"+div_id+"-insere-arquivo-"+qtde].lastElementChild.remove();
    clone.children[1].children["div-"+div_id+"-insere-arquivo-"+qtde].lastElementChild.remove();

    var insereArquivoErro = document.createElement('div');
    insereArquivoErro.setAttribute('id', "campo_"+campo_id+"["+new_qtde+"]-error");

    clone.children[1].children["div-"+div_id+"-insere-arquivo-"+qtde].appendChild(insereArquivo);
    clone.children[1].children["div-"+div_id+"-insere-arquivo-"+qtde].appendChild(insereArquivoErro);
    clone.children[1].children["div-"+div_id+"-insere-arquivo-"+qtde].id = "div-"+div_id+"-insere-arquivo-"+new_qtde;

};