<?php
    $db = new SQLite3('../sqlite/apontamentos.db');



    if((isset($_POST['adddte'])) 
        && (isset($_POST['fr_tim']))
        && (isset($_POST['to_tim']))
        && (isset($_POST['prd_id']))
        && (isset($_POST['opr_id']))
        && (isset($_POST['usrask']))
        && (isset($_POST['usrobs']))
        && (isset($_POST['usr_id']))
        )
        {

        if( $_POST['fr_tim'] > $_POST['to_tim']){
            print '
                <br>
                <div class="alert alert-warning" role="alert">
                    Favor inserir o horário de início inferior ao horário de término.
                </div>
                ';
        }else{
            $s_tblcty = "
            SELECT * FROM usropr WHERE opr_id = ".$_POST['opr_id']."
            ";
            $rescty = $db->query($s_tblcty);
            $rescty = $rescty->fetchArray(SQLITE3_ASSOC);
            
            $i_tblulg = "
                INSERT INTO usrlog ( usr_id , prd_id , opr_id, cty_id , to_usr_id , logdte , fr_logtim, to_logtim, usrobs ) 
                    VALUES ( :usr_id , :prd_id , :opr_id, :cty_id , :to_usr_id , :logdte , :fr_logtim, :to_logtim, :usrobs);
            ";

            $_usr_id = $_POST['usr_id'];
            $_prd_id = $_POST['prd_id'];
            $_opr_id = $_POST['opr_id'];
            $_cty_id = $rescty['cty_id'];
            $_to_usr_id = $_POST['usrask'];
            $_logdte = $_POST['adddte'];
            $_fr_logtim = $_POST['fr_tim'];
            $_to_logtim = $_POST['to_tim'];
            $_usrobs = $_POST['usrobs'];
            
            $cmd_db = $db->prepare($i_tblulg);
            $cmd_db->bindValue('usr_id', $_usr_id);
            $cmd_db->bindValue('prd_id', $_prd_id);
            $cmd_db->bindValue('opr_id', $_opr_id);
            $cmd_db->bindValue('cty_id', $_cty_id);
            $cmd_db->bindValue('to_usr_id', $_to_usr_id);
            $cmd_db->bindValue('logdte', $_logdte);
            $cmd_db->bindValue('fr_logtim', $_fr_logtim);
            $cmd_db->bindValue('to_logtim', $_to_logtim);
            $cmd_db->bindValue('usrobs', $_usrobs);

            $resultado = $cmd_db->execute();

            if($resultado->numColumns() != 0){
                
                print '
                <br>
                <div class="alert alert-warning" role="alert">
                    Erro ao salvar Lançamento. Procure o Administrador do sistema.
                </div>
                ';
            }else{
            
            print '
            <script language= "JavaScript">
                var delay=150;
				setTimeout(function(){
					window.location.replace("./apontamento-form.php");
				},delay);
			</script>
            <br>
            <div class="alert alert-success" role="alert">
                Apontamento lançado.
            </div>';
           
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