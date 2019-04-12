<?php
    $db = new SQLite3('../sqlite/apontamentos.db');

    if((isset($_POST['oprnme'])) && (isset($_POST['cty_id']))){
        $i_tblopr = "
            INSERT INTO usropr (oprnme, cty_id ) 
                VALUES (:oprnme, :cty_id);
        ";
        $_oprnme = $_POST['oprnme'];
        $_cty_id = $_POST['cty_id'];
        
        $cmd_db = $db->prepare($i_tblopr);
        $cmd_db->bindValue('oprnme', $_oprnme);
        $cmd_db->bindValue('cty_id', $_cty_id);

        $resultado = $cmd_db->execute();


        if($resultado->numColumns() != 0){
            
            print '
            <br>
            <div class="alert alert-warning" role="alert">
                Erro ao salvar Operação. Procure o Administrador do sistema.
            </div>
            ';
        }else{
            
            print '
            <br>
            <div class="alert alert-success" role="alert">
                Operação Cadastrada.
            </div>
            ';
        }
    }
    else{
        print '
        <br>
        <div class="alert alert-warning" role="alert">
            Erro no envio dos parametros. Procure o Administrador do sistema.
        </div>
        ';
    }
?>