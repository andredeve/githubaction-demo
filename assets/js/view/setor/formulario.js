$(document).ready(function (){
   gerenciarCampoUnidadeOrgao($("#setorForm").find("select[name='setor_pai_id']"));
   
    $("#setorForm").on("change", "select[name='setor_pai_id']", function (){
        gerenciarCampoUnidadeOrgao($(this));
        
    });
});

function gerenciarCampoUnidadeOrgao($selectSetorPai){
    if($selectSetorPai.val() == ""){
        $('.divUnidade').addClass('d-none');
        $('.divUnidade').find("input").val('');
        $('.divOrgao').removeClass('d-none');
    }else{
        $('.divOrgao').addClass('d-none');
        $('.divOrgao').find("input").val('');
        $('.divUnidade').removeClass('d-none');
    }
}