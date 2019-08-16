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
        }
        else{
            $s_valida = "
            SELECT 
                sum((julianday(:to_logtim) - julianday(:fr_logtim))*24) as total,
                (julianday(date('now')) - julianday(:logdte)) as dias
                ";
                $cmd_db_val = $db->prepare($s_valida);
                $cmd_db_val->bindValue('fr_logtim', $_POST['fr_tim']);
                $cmd_db_val->bindValue('to_logtim', $_POST['to_tim']);
                $cmd_db_val->bindValue('logdte', $_POST['adddte']);
                $resultado_val = $cmd_db_val->execute();
                $acumulado = 0;
                $amanha = 0;

                while($rows = $resultado_val->fetchArray(SQLITE3_ASSOC)){
                   $acumulado = $rows['total'];
                   $amanha = $rows['dias'];
                }

            if(($acumulado < 6.4)and ($amanha >= 0)){
                $s_tblcty = "
                SELECT * FROM usropr WHERE opr_id = ".$_POST['opr_id']."
                ";
                $rescty = $db->query($s_tblcty);
                $rescty = $rescty->fetchArray(SQLITE3_ASSOC);
                
                $i_tblulg = "
                    INSERT INTO usrlog ( usr_id , prd_id , opr_id, cty_id , to_usr_id , logdte , fr_logtim, to_logtim, usrobs, insdte, insusr  ) 
                        VALUES ( :usr_id , :prd_id , :opr_id, :cty_id , :to_usr_id , :logdte , :fr_logtim, :to_logtim, :usrobs, :insdte, :insusr);
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

                date_default_timezone_set('America/Sao_Paulo');
                $agora = new DateTime('now');
                $_insdte = $agora->format('Y-m-d H:i');
                $_insusr = $_POST['logged_usr_id'];

                
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

                

                $cmd_db->bindValue('insdte', $_insdte);
                $cmd_db->bindValue('insusr', $_insusr);

                $resultado = $cmd_db->execute();

                if($resultado->numColumns() != 0){
                    print '
                    <br>
                    <div class="alert alert-warning" role="alert">
                        Erro ao salvar Lançamento. Procure o Administrador do sistema.
                    </div>
                    ';
                }
                else{

                    if($_POST['page'] == 'apontamento-form-gestor'){
                        print '
                        
                        <script language= "JavaScript">
                            var delay=1000;
                            setTimeout(function(){
                                window.location.replace("./apontamento-form-gestor.php");
                            },delay);
                        </script>
                        <br>
                        
                        <div class="alert alert-success" role="alert">
                            Apontamento lançado.
                        </div>
                        ';
                    }
                    else{
                        print '
                        
                        <script language= "JavaScript">
                            var delay=1000;
                            setTimeout(function(){
                                window.location.replace("./apontamento-form.php");
                            },delay);
                        </script>
                        <br>
                      
                        <div class="alert alert-success" role="alert">
                            Apontamento lançado.
                        </div>
                        ';
                    }
                    
                }
            }
            else{
                if(($acumulado > 6.4) ){
                    echo '
                    <br>
                    <div class="alert alert-warning" role="alert">
                        Parece que você esqueceu de considerar o horário de almoço, favor corrigir.
                    </div>
                    ';
                }
                else if($amanha < 0){
                    echo '
                    <br>
                    <div class="alert alert-warning" role="alert">
                        Parece que você colocou o dia de manhã sem querer, favor corrigir.
                    </div>
                    ';
                }
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