<?php
    session_start();
    $db = new SQLite3('../sqlite/apontamentos.db');
    $usuario_selecionado = isset($_POST['select_usr_id'])? $_POST['select_usr_id'] : $_SESSION["usr_id"];
    if(isset($_POST['dia']) && isset($_POST['acao'])) {

        if($_POST['acao'] == 'true'){
            echo '
            <div class="alert alert-primary" role="alert">
                Dia: '.$_POST["dia"].' alterado para indisponível.
            </div>
            ';
            /* Fazer insert */

            $i_manha = "
            insert into usrind (usr_id, inddte, fr_logtim, to_logtim, indobs)
            values (:usr_id, :inddte, '07:40', '12:00', 'Indisponibilidade: Férias, médico, falta, abono;')
            ";
            $cmd_db_m = $db->prepare($i_manha);
            $cmd_db_m->bindValue('usr_id', $usuario_selecionado);
            $cmd_db_m->bindValue('inddte', $_POST['dia']);
            $resultado = $cmd_db_m->execute();

            $i_tarde = "
            insert into usrind (usr_id, inddte, fr_logtim, to_logtim, indobs)
            values (:usr_id, :inddte, '13:00', '17:04', 'Indisponibilidade: Férias, médico, falta, abono;')
            ";
            $cmd_db_t = $db->prepare($i_tarde);
            $cmd_db_t->bindValue('usr_id',$usuario_selecionado);
            $cmd_db_t->bindValue('inddte', $_POST['dia']);
            $resultado = $cmd_db_t->execute();

        }
        else{
            echo '
            <div class="alert alert-success" role="alert">
                Dia: '.$_POST["dia"].' alterado para disponível.
            </div>
            ';
            /* Fazer delete */
            $s_indisp = "
            SELECT * from usrind where inddte = :inddte and usr_id = :usr_id
            ";

            $cmd_db_s = $db->prepare($s_indisp);
            $cmd_db_s->bindValue('usr_id', $usuario_selecionado);
            $cmd_db_s->bindValue('inddte', $_POST['dia']);
            $resultado_s = $cmd_db_s->execute();
            
            while($row = $resultado_s->fetchArray(SQLITE3_ASSOC)){
           
                $d_indisp = "
                DELETE from usrind where ind_id = :ind_id
                ";

                $cmd_db_d = $db->prepare($d_indisp);
                $cmd_db_d->bindValue('ind_id', $row["ind_id"]);
                $resultado = $cmd_db_d->execute();
             

            }
        }
    }
    else{
        echo '
            <div class="alert alert-success" role="alert">
                Verificar com o Administrador
            </div>
            ';
    }

?>