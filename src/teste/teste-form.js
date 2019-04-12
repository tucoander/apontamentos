jQuery(document).ready(function(){
    jQuery("#email").submit(function(){
        return false;
    });   
    // carregando a função para o envio
    jQuery("#enviar").click(function(){
        login_usr();
    });
    // limpando a div antes de um novo envio
    function login_usr() {
        jQuery("#res").empty();
           
        // pegando os campos do formulário
        var exampleInputEmail1 = jQuery("#exampleInputEmail1").val();
        var exampleInputPassword1 = jQuery("#exampleInputPassword1").val();
        
        // tipo dos dados, url do documento, tipo de dados, campos enviados    
        // para GET mude o type para GET  
        jQuery.ajax({
            type: "POST",
            url: "teste-exe.php",
            dataType: "html",
            data: {exampleInputEmail1: exampleInputEmail1, exampleInputPassword1: exampleInputPassword1},
        // enviado com sucesso
            success: function(response){
                jQuery("#res").append(response);
                jQuery("#res").addClass('alert alert-primary');
            },
            // quando houver erro
            error: function(){
                alert("Erro no Ajax");
            }
        });
    }
});