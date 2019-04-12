<?php
    $db = new SQLite3('../sqlite/apontamentos.db');
    session_start();
    if((isset($_POST['old_usrpsw'])) && (isset($_POST['new_usrpsw']))){

        $_usr_id = $_SESSION['usr_id'];
       
        $_old_usrpsw = md5($_POST['old_usrpsw']);
        $_new_usrpsw = md5($_POST['new_usrpsw']);

        if($_old_usrpsw == $_new_usrpsw){
            print 
            '<br>
            <div class="alert alert-warning" role="alert">
                As senhas precisam ser diferentes.
            </div>';
            
        }
        else{
            $s_tblusr = "
            SELECT * FROM usrsys 
                WHERE usr_id = :usr_id
                AND usrpsw = :usrpsw;
            ";

            $cmd_db = $db->prepare($s_tblusr);
            $cmd_db->bindValue('usr_id', $_usr_id);
            $cmd_db->bindValue('usrpsw', $_old_usrpsw);
           
            $resultado = $cmd_db->execute();
            
            if($resultado->numColumns() == 0){
                
                print '
                <br>
                <div class="alert alert-warning" role="alert">
                    A senha informada não confere com a senha do usuário logado.
                </div>
                ';
            }else{
                $u_tblusr = "
                UPDATE usrsys 
                    SET usrpsw = :usrpsw
                    WHERE usr_id = :usr_id
                ";
                $cmd_db = $db->prepare($u_tblusr);
                $cmd_db->bindValue('usr_id', $_usr_id);
                $cmd_db->bindValue('usrpsw', $_new_usrpsw);
                $resultado = $cmd_db->execute();

                print '
                <br>
                <div class="alert alert-success" role="alert">
                    Senha alterada com sucesso.
                </div>
                ';
            }
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