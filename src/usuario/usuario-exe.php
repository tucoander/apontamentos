<?php
    $db = new SQLite3('../sqlite/apontamentos.db');

    if((isset($_POST['usr_id'])) && (isset($_POST['usrpsw']))){
        $i_tblusr = "
            INSERT INTO usrsys (usr_id, usrpsw ) 
                VALUES (:usr_id, :usrpsw);
        ";
        $_usr_id = $_POST['usr_id'];
        $_usrpsw = md5($_POST['usrpsw']);
        
        $cmd_db = $db->prepare($i_tblusr);
        $cmd_db->bindValue('usr_id', $_usr_id);
        $cmd_db->bindValue('usrpsw', $_usrpsw);

        $resultado = $cmd_db->execute();


        if($resultado->numColumns() != 0){
            var_dump($resultado->numColumns());
            print '
            <br>
            <div class="alert alert-warning" role="alert">
                Erro ao salvar usuário. Procure o Administrador do sistema.
            </div>
            ';
        }else{
            
            print '
            <br>
            <div class="alert alert-success" role="alert">
                Usuário Cadastrado.
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