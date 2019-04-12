<?php
    /**
     * Comando para atualização parcial dos apontamentos
     * É possível atualizar:
     * produto
     * operacao
     * solicitante
     * data
     * hora de inicio
     * hora de termino
     * obs
     * 
     * Não é possível atualizar
     * usuário
     * país (atualizado pela operação)
     * 
     */
    $db = new SQLite3('../sqlite/apontamentos.db');
    // campo a ser utilizado
    $tb_usr = '';
    $tb_prd = '';
    $tb_opr = '';
    $tb_cty = '';
    $tb_ask = '';
    $tb_dte = '';
    $tb_frt = '';
    $tb_trt = '';
    $tb_obs = '';

    // retorno de cada campo
    $res_usr = 'Não alterado';
    $res_prd = 'Não alterado';
    $res_opr = 'Não alterado';
    $res_cty = 'Não alterado';
    $res_ask = 'Não alterado';
    $res_dte = 'Não alterado';
    $res_frt = 'Não alterado';
    $res_trt = 'Não alterado';
    $res_obs = 'Não alterado';

    
    /* verificar dados diferentes do registrado */
    if(isset($_POST['log_id'])){
        $s_tbllog = "
        SELECT * 
        FROM usrlog ul
            inner join  usrprd up 
            on (ul.prd_id = up.prd_id)
            inner join usrcty uc
            on (ul.cty_id = uc.cty_id)
            inner join usropr uo
            on (ul.opr_id = uo.opr_id)
        WHERE log_id = :log_id
        order by ul.logdte asc
        ";
    
        $_log_id = $_POST['log_id'];
       
        $cmd_db = $db->prepare($s_tbllog);
        $cmd_db->bindValue('log_id', $_log_id);
        $resultado = $cmd_db->execute();

        while($row = $resultado->fetchArray(SQLITE3_ASSOC)){
            $tb_usr = $row["usr_id"];
            $tb_prd = $row["prd_id"];
            $tb_opr = $row["opr_id"];
            $tb_ask = $row["to_usr_id"];
            $tb_dte = $row["logdte"];
            $tb_frt = $row["fr_logtim"];
            $tb_trt = $row["to_logtim"];
            $tb_obs = $row["usrobs"];
        }
    }
    

    if(isset($_POST['prd_id']) && isset($_POST['log_id']) && ($_POST['prd_id'] != $tb_prd)) {
        $_prd_id = $_POST['prd_id'];
        $_log_id = $_POST['log_id'];
        if(!empty($_prd_id)){
            $u_tbllog = "
                UPDATE usrlog 
                    SET prd_id = :prd_id 
                    WHERE log_id = :log_id
            ";
            $cmd_db = $db->prepare($u_tbllog);
            $cmd_db->bindValue('prd_id', $_prd_id);
            $cmd_db->bindValue('log_id', $_log_id);
            $resultado = $cmd_db->execute();
            $res_prd = "Alterado";
        }
        else{
            $res_prd = 'Produto não alterado.';
        }
    }

    if(isset($_POST['opr_id']) && isset($_POST['log_id']) && ($_POST['opr_id'] != $tb_opr)) {
        $_opr_id = $_POST['opr_id'];
        $_cty_id = "";
        $_log_id = $_POST['log_id'];

        $s_tblcty = "
            SELECT * FROM usropr WHERE opr_id = '$_opr_id'
        ";
        $rescty = $db->query($s_tblcty);
        while($row = $rescty->fetchArray(SQLITE3_ASSOC)){
            $_cty_id = $row['cty_id'];
        }
        if(!empty($_opr_id) && !empty($_cty_id)){
            $u_tbllog = "
                UPDATE usrlog 
                    SET opr_id = :opr_id ,
                        cty_id = :cty_id 
                    WHERE log_id = :log_id
            ";

            $cmd_db = $db->prepare($u_tbllog);
            $cmd_db->bindValue('opr_id', $_opr_id);
            $cmd_db->bindValue('cty_id', $_cty_id);
            $cmd_db->bindValue('log_id', $_log_id);
            $resultado = $cmd_db->execute();
            $res_opr = "Alterado";
        }
        else{
            
        }
    }

    if(isset($_POST['usrask']) && isset($_POST['log_id']) && ($_POST['usrask'] != $tb_ask)) {
        $_to_usr_id = $_POST['usrask'];
        $_log_id = $_POST['log_id'];
        if(!empty($_to_usr_id)){
            $u_tbllog = "
                UPDATE usrlog 
                    SET to_usr_id = :to_usr_id
                    WHERE log_id = :log_id
            ";
            $cmd_db = $db->prepare($u_tbllog);
            $cmd_db->bindValue('to_usr_id', $_to_usr_id);
            $cmd_db->bindValue('log_id', $_log_id);
            $resultado = $cmd_db->execute();
            $res_ask = "Alterado";
        }
        else{
            $res_opr = 'Operação não alterada.';
            
        }
    }

    if(isset($_POST['adddte']) && isset($_POST['log_id']) && ($_POST['adddte'] != $tb_dte)) {
        $_logdte = $_POST['adddte'];
        $_log_id = $_POST['log_id'];
        if(!empty($_logdte)){
            $u_tbllog = "
                UPDATE usrlog 
                    SET logdte = :logdte
                    WHERE log_id = :log_id
            ";
            $cmd_db = $db->prepare($u_tbllog);
            $cmd_db->bindValue('logdte', $_logdte);
            $cmd_db->bindValue('log_id', $_log_id);
            $resultado = $cmd_db->execute();
            $res_dte = "Alterado";
        }
        else{
            $res_opr = 'Operação não alterada.';
        }
    }

    if(isset($_POST['fr_tim']) && isset($_POST['log_id']) && ($_POST['fr_tim'] != $tb_frt)) {
        $_fr_logtim = $_POST['fr_tim'];
        $_log_id = $_POST['log_id'];
        if(!empty($_fr_logtim)){
            $u_tbllog = "
                UPDATE usrlog 
                    SET fr_logtim = :fr_logtim
                    WHERE log_id = :log_id
            ";
            $cmd_db = $db->prepare($u_tbllog);
            $cmd_db->bindValue('fr_logtim', $_fr_logtim);
            $cmd_db->bindValue('log_id', $_log_id);
            $resultado = $cmd_db->execute();
            $res_frt = "Alterado";
        }
        else{
            $res_opr = 'Operação não alterada.';
            
        }
    }

    if(isset($_POST['to_tim']) && isset($_POST['log_id']) && ($_POST['to_tim'] != $tb_trt)) {
        $_to_logtim = $_POST['to_tim'];
        $_log_id = $_POST['log_id'];
        if(!empty($_to_logtim)){
            $u_tbllog = "
                UPDATE usrlog 
                    SET to_logtim = :to_logtim
                    WHERE log_id = :log_id
            ";
            $cmd_db = $db->prepare($u_tbllog);
            $cmd_db->bindValue('to_logtim', $_to_logtim);
            $cmd_db->bindValue('log_id', $_log_id);
            $resultado = $cmd_db->execute();
            $res_trt = "Alterado";
        }
        else{
            $res_opr = 'Operação não alterada.';
        }
    } 

    if(isset($_POST['usrobs']) && isset($_POST['log_id']) && ($_POST['usrobs'] != $tb_obs)) {
        $_usrobs = $_POST['usrobs'];
        $_log_id = $_POST['log_id'];

        if(!empty($_usrobs)){
            $u_tbllog = "
                UPDATE usrlog 
                    SET usrobs = :usrobs
                    WHERE log_id = :log_id
            ";
            $cmd_db = $db->prepare($u_tbllog);
            $cmd_db->bindValue('usrobs', $_usrobs);
            $cmd_db->bindValue('log_id', $_log_id);
            $resultado = $cmd_db->execute();
            $res_obs = "alterado";
        }
        else{
            $res_opr = 'Operação não alterada.';
        }
    }
    print '
            <script language= "JavaScript">
                var delay=250;
				setTimeout(function(){
					window.location.replace("./apontamento-view.php");
				},delay);
			</script>
            <br>
            <div class="alert alert-success" role="alert">
                Apontamento Alterado.
            </div>';
?>
