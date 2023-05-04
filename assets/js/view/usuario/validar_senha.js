$(document).ready( function (){
    $("#novaSenha").blur(function ()  {validatePasswordLevel($(this))});
    $("#novaSenha").keyup(function () {validatePasswordLevel($(this))});

    function validatePasswordLevel($elemento){
        let texto = $elemento.val() ;

        if (temLetraMinuscula(texto)) {
            $('#letter').removeClass('text-danger').addClass('text-success');
            $('#iLetter').removeClass('fa-times').addClass('fa-check');
        } else {
            $('#letter').removeClass('text-success').addClass('text-danger');
            $('#iLetter').removeClass('fa-check').addClass('fa-times' );
        }
        
        //validate capital letter
        if ( temLetraMaiuscula(texto) ) {
            $('#capital').removeClass('text-danger').addClass('text-success');
            $('#iCapital').removeClass('fa-times').addClass('fa-check');
        } else {
            $('#capital').removeClass('text-success').addClass('text-danger');
            $('#iCapital').removeClass('fa-check').addClass('fa-times');
        }
        
        //validate number
        if (temNumero(texto)) {
            $('#number').removeClass('text-danger').addClass('text-success');
            $('#iNumber').removeClass('fa-times').addClass('fa-check');
        } else {
            $('#number').removeClass('text-success').addClass('text-danger');
            $('#iNumber').removeClass('fa-check').addClass('fa-times');
        }
        //validate length
        if (temMinimoCaracteres(texto)) {
            $('#length').removeClass('text-danger').addClass('text-success');
            $('#iLength').removeClass('fa-times').addClass('fa-check');
        } else {
            $('#length').removeClass('text-success').addClass('text-danger');
            $('#iLength').removeClass('fa-check').addClass('fa-times');
        }        
    }
    function temLetraMinuscula(texto){
        return (/[a-z]/.test(texto));
    }
    function temLetraMaiuscula(texto){
        return  (/[A-Z]/.test(texto)) ;
    }
    function temNumero(texto){
        return /\d/.test(texto) ;
    }
    function temMinimoCaracteres(texto){
        return texto.length  > 7? true:false;
    }

    function isNewPasswordIsValid(){
        let texto = $("#novaSenha").val();
        return temLetraMaiuscula(texto) && temLetraMinuscula(texto) && temMinimoCaracteres(texto);
    }

    /**
     * =================================================================================
     *  Jquery Validate para alterar senha de usuário
     * =================================================================================
     */
    /**
     * Validação de Formulário de alterar senha
     */
    $("#alteraSenhaForm").validate({
        rules: {
            senha: {minlength: 8},
            confirmaSenha: {equalTo: "#novaSenha"}
        },
        messages: {
            senha: {minlength: "Senha deve ter no mínimo 5 caracteres"},
            confirmaSenha: {equalTo: " As senhas informada são diferentes."}
        },
        submitHandler: function (form) {
            if(!isNewPasswordIsValid()){
                showGrowMessage('error', "A senha não segue o formato requerido");
                return false;
            }
            var l = Ladda.create(form.querySelector('.ladda-button'));
            l.start();
            form.submit();
        }
    });
});