jQuery(document).ready(function(){
    jQuery("#apontamento-linha").submit(function(){
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
        var adddte = jQuery("#adddte").val();
        var fr_tim = jQuery("#fr_tim").val();
        var to_tim = jQuery("#to_tim").val();
        var prd_id = jQuery("#prd_id").val();
        var opr_id = jQuery("#opr_id").val();
        var cty_id = jQuery("#cty_id").val();
        var usrask = jQuery("#usrask").val();
        var usrobs = jQuery("#usrobs").val();
        var usr_id = jQuery("#usr_id").val();
        
        // tipo dos dados, url do documento, tipo de dados, campos enviados    
        // para GET mude o type para GET  
        jQuery.ajax({
            type: "POST",
            url: "apontamento-edit-table-exe.php",
            dataType: "html",
            data: {
                adddte: adddte, 
                fr_tim: fr_tim,
                to_tim: to_tim, 
                prd_id: prd_id , 
                opr_id: opr_id,
                cty_id: cty_id,
                usrask: usrask,
                usrobs: usrobs,
                usr_id: usr_id
            },
        // enviado com sucesso
            success: function(response){
                jQuery("#res").append(response);
                jQuery("#adddte").empty();
                jQuery("#fr_tim").empty();
                jQuery("#to_tim").empty();
                jQuery("#prd_id").empty();
                jQuery("#opr_id").empty();
                jQuery("#cty_id").empty();
                jQuery("#usrask").empty();
                jQuery("#usrobs").empty();
            },
            // quando houver erro
            error: function(){
                alert("Erro no Ajax");
            }
        });
    }
});