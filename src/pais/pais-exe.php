<?php
    $db = new SQLite3('../sqlite/apontamentos.db');

    if((isset($_POST['ctynme'])) && (isset($_POST['ctysgl']))){
        $i_tblcty = "
            INSERT INTO usrcty (ctynme, ctysgl ) 
                VALUES (:ctynme, :ctysgl);
        ";
        $_ctynme = $_POST['ctynme'];
        $_ctysgl = $_POST['ctysgl'];
        
        $cmd_db = $db->prepare($i_tblcty);
        $cmd_db->bindValue('ctynme', $_ctynme);
        $cmd_db->bindValue('ctysgl', $_ctysgl);

        $resultado = $cmd_db->execute();


        if($resultado->numColumns() != 0){
            var_dump($resultado->numColumns());
            print '
            <br>
            <div class="alert alert-warning" role="alert">
                Erro ao salvar país. Procure o Administrador do sistema.
            </div>
            ';
        }else{
            
            print '
            <br>
            <div class="alert alert-success" role="alert">
                País Cadastrado.
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