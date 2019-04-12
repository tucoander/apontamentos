jQuery(document).ready(function(){
    jQuery("#usuario").submit(function(){
        return false;
    });   
    // carregando a função para o envio
    jQuery("#salvar").click(function(){
        cad_usr();
    });
    // limpando a div antes de um novo envio
    function cad_usr() {
        jQuery("#res").empty();
           
        // pegando os campos do formulário
        var usr_id = jQuery("#usr_id").val();
        var usrpsw = jQuery("#usrpsw").val();
        
        // tipo dos dados, url do documento, tipo de dados, campos enviados    
        // para GET mude o type para GET  
        jQuery.ajax({
            type: "POST",
            url: "usuario-exe.php",
            dataType: "html",
            data: {usr_id: usr_id, usrpsw: usrpsw},
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