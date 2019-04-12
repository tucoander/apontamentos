<?php
    $db = new SQLite3('../sqlite/apontamentos.db');

    if((isset($_POST['prdnme'])) && (isset($_POST['rspare']))){
        $i_tblprd = "
            INSERT INTO usrprd (prdnme, rspare ) 
                VALUES (:prdnme, :rspare);
        ";
        $_prdnme = $_POST['prdnme'];
        $_rspare = $_POST['rspare'];
        
        $cmd_db = $db->prepare($i_tblprd);
        $cmd_db->bindValue('prdnme', $_prdnme);
        $cmd_db->bindValue('rspare', $_rspare);

        $resultado = $cmd_db->execute();

     
        if($resultado->numColumns() != 0){
            var_dump($resultado->numColumns());
            print '
            <br>
            <div class="alert alert-warning" role="alert">
                Erro no envio dos parametros. Procure o Administrador do sistema.
            </div>
            ';
        }else{
            
            print '
            <br>
            <div class="alert alert-success" role="alert">
                Produto Cadastrado.
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