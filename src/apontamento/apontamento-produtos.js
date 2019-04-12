jQuery(document).ready(function(){
    jQuery("#apontamento").submit(function(){
        return false;
    });   
    // carregando a função para o envio
    jQuery("#usr_id").change(function(){
        pro_usr();
    });
    // limpando a div antes de um novo envio
    function pro_usr() {
        jQuery("#res").empty();
           
        // pegando os campos do formulário
        var usr_id = jQuery("#usr_id").val();
        var adddte = jQuery("#adddte").val();
        
        
        // tipo dos dados, url do documento, tipo de dados, campos enviados    
        // para GET mude o type para GET  
        jQuery.ajax({
            type: "POST",
            url: "apontamento-produtos-exe.php",
            dataType: "html",
            data: {
                usr_id: usr_id,
                adddte: adddte
            },
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