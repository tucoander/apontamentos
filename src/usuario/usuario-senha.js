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
        var old_usrpsw = jQuery("#old_usrpsw").val();
        var new_usrpsw = jQuery("#new_usrpsw").val();
        var new_usrpsw_c = jQuery("#new_usrpsw_c").val();
       
        
        // tipo dos dados, url do documento, tipo de dados, campos enviados    
        // para GET mude o type para GET  
        jQuery.ajax({
            type: "POST",
            url: "usuario-senha-exe.php",
            dataType: "html",
            data: {old_usrpsw: old_usrpsw, new_usrpsw: new_usrpsw, new_usrpsw_c: new_usrpsw_c},
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