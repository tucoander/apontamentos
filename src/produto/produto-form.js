jQuery(document).ready(function(){
    jQuery("#produto").submit(function(){
        return false;
    });   
    // carregando a função para o envio
    jQuery("#salvar").click(function(){
        login_usr();
    });
    // limpando a div antes de um novo envio
    function login_usr() {
        jQuery("#res").empty();
           
        // pegando os campos do formulário
        var prdnme = jQuery("#prdnme").val();
        var rspare = jQuery("#rspare").val();
        
        // tipo dos dados, url do documento, tipo de dados, campos enviados    
        // para GET mude o type para GET  
        jQuery.ajax({
            type: "POST",
            url: "produto-exe.php",
            dataType: "html",
            data: {prdnme: prdnme, rspare: rspare},
        // enviado com sucesso
            success: function(response){
                jQuery("#res").append(response);
            },
            // quando houver erro
            error: function(){
                alert("Erro no Ajax");
            }
        });
    }
});