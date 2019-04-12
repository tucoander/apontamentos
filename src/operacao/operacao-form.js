jQuery(document).ready(function(){
    jQuery("#operacao").submit(function(){
        return false;
    });   
    // carregando a função para o envio
    jQuery("#salvar").click(function(){
        cad_opr();
    });
    // limpando a div antes de um novo envio
    function cad_opr() {
        jQuery("#res").empty();
           
        // pegando os campos do formulário
        var oprnme = jQuery("#oprnme").val();
        var cty_id = jQuery("#cty_id").val();
        
        // tipo dos dados, url do documento, tipo de dados, campos enviados    
        // para GET mude o type para GET  
        jQuery.ajax({
            type: "POST",
            url: "operacao-exe.php",
            dataType: "html",
            data: {oprnme: oprnme, cty_id: cty_id},
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