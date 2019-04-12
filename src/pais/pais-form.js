jQuery(document).ready(function(){
    jQuery("#pais").submit(function(){
        return false;
    });   
    // carregando a função para o envio
    jQuery("#salvar").click(function(){
        cad_cty();
    });
    // limpando a div antes de um novo envio
    function cad_cty() {
        jQuery("#res").empty();
           
        // pegando os campos do formulário
        var ctynme = jQuery("#ctynme").val();
        var ctysgl = jQuery("#ctysgl").val();
        
        // tipo dos dados, url do documento, tipo de dados, campos enviados    
        // para GET mude o type para GET  
        jQuery.ajax({
            type: "POST",
            url: "pais-exe.php",
            dataType: "html",
            data: {ctynme: ctynme, ctysgl: ctysgl},
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