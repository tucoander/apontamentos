$('#apontamento').SetEditable({ 
    columnsEd:"1,2,3,5,6,7,8,9",
    onEdit: function() {
        var th = document.querySelectorAll('table tbody tr th'); 
        var td = document.querySelectorAll('table tbody tr td'); 
        var id = th['0'].innerHTML;
        var usr_id = td['0'].innerHTML;
        var prdnme = td['1'].innerHTML;
        var oprnme = td['2'].innerHTML;
        var ctynme = td['3'].innerHTML;
        var usrask = td['4'].innerHTML;
        var adddte = td['5'].innerHTML;
        var fr_tim = td['6'].innerHTML;
        var to_tim = td['7'].innerHTML;
        var usrobs = td['8'].innerHTML;

        console.log(td);
        jQuery("#res").append(id);
        jQuery.ajax({
            type: "POST",
            url: "apontamento-edit-table-exe.php",
            dataType: "html",
            data: {
                id: id, 
                usr_id: usr_id,
                prdnme: prdnme, 
                oprnme: oprnme,
                ctynme: ctynme , 
                usrask: usrask,
                adddte: adddte,
                fr_tim: fr_tim,
                to_tim: to_tim,
                usrobs: usrobs
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
        
    },   
    onDelete: function() {
        alert("Opção não permitida! Procure o Administrador do Sistema.");
    }, 
    onAdd: function() {} 
});