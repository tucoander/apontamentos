<?php
    $db = new SQLite3('../sqlite/apontamentos.db');
    /**
     * select para pegar o fechamento da semana
     * verifica a semana que está lançando e a semana fechada
     */
    $selfec = "
        select 
            (yy.yr_dte||'-'||mn_dte||'-'||dy_dte||' 12:00') as fechamento,
            strftime('%W',(yy.yr_dte||'-'||mn_dte||'-'||dy_dte||' 12:00')) as semana_indisponivel,
            strftime('%W',:apontamento) as semana_lancamento,
            yy.*
        from
            yr_idx yy
        WHERE 
            strftime('%W', (yy.yr_dte||'-'||mn_dte||'-'||dy_dte)) = strftime('%W', 'now')
            and wk_day = 'terça-feira'
    ";

    $datapp = $_POST['adddte'].' '.$_POST['fr_tim'];

    $cmdfec = $db->prepare($selfec);
    $cmdfec->bindValue('apontamento', $datapp);
    $resfec = $cmdfec->execute();

    $fechamento = '';
    $limite = '';
    $semana = '';

    while($rows = $resfec->fetchArray(SQLITE3_ASSOC)){
        $fechamento = $rows['semana_indisponivel'];
        $limite = $rows['fechamento'];
        $semana = $rows['semana_lancamento'];
    }

    if($_POST['page'] == 'apontamento-form' && $datapp < $limite){ // usuario
        if($semana >= $fechamento){ // lança
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
                /**
                 * verifica se o inicio do lançamento é maior que o fim
                 * se for retorna erro
                 */
                if( $_POST['fr_tim'] > $_POST['to_tim'] ){
                    print '
                        <br>
                        <div class="alert alert-warning" role="alert">
                            Favor inserir o horário de início inferior ao horário de término.
                        </div>
                        ';
                }
                else{
                    /**
                     * verifica o horário acumulado do lançamento acumulado
                     * e a diferença entre agora e o lançamento
                     */
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
                    /**
                     * se o acumulado não ultrapassar o horário do almoço 
                     * e a diferença do lançamento com agora for maior igual a zero
                     * (ou seja não é lançamento de amanhã ou superior)
                     * permite o lançamento
                     */
                    if(($acumulado < 6.4)and ($amanha >= 0)){
                        /**
                         * de acordo com a operação selecionada é feito a procura
                         * no banco pra saber qual pais pertence aquela operação
                         */
                        $s_tblcty = "
                        SELECT * FROM usropr WHERE opr_id = ".$_POST['opr_id']."
                        ";
                        $rescty = $db->query($s_tblcty);
                        $rescty = $rescty->fetchArray(SQLITE3_ASSOC);
                        
                        /**
                         * inserindo dados de log do apontamento
                         */
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

                        /**
                         * caso não consiga inserir no banco retorna um erro
                         * para o usuário
                         */
                        if($resultado->numColumns() != 0){
                            print '                        
                            <br>
                            <div class="alert alert-warning" role="alert">
                                Erro ao salvar Lançamento. Procure o Administrador do sistema.
                            </div>
                            ';
                        }
                        /**
                         * caso inserir retorna um sucesso e atualiza a página
                         */
                        else{
                            print '
                            <br>
                            <div class="alert alert-success" role="alert">
                                Apontamento lançado!
                            </div>
                            <script>
                                jQuery("#apontamento input").val("");
                                jQuery("#apontamento select").val("");
                                jQuery("#apontamento textarea").val("");
                                jQuery("#apontamento input[type = submit]").val("");
                            </script>
                            ';
                        }
                    }
                    /**
                     * se o acumulado ultrapassar o horário do almoço 
                     * ou a diferença do lançamento com agora for menor que zero
                     * (ou seja é lançamento de amanhã ou antes)
                     * realiza a checagem e retorna pro usuário
                     */
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
            /**
             * problema no envio de parametros
             */
            else{
                print '
                <br>
                <div class="alert alert-warning" role="alert">
                    Erro no envio dos parametros. Procure o Administrador do sistema.
                </div>
                ';
            }
        }
        else{ //não lança
            echo '
            <br>
            <div class="alert alert-danger" role="alert">
                Falha. Lançamento realizado após o período de fechamento. Alinhe com seu gestor.
            </div>
            ';
        }
    }
    else if($_POST['page'] != 'apontamento-form-gestor'){ // lança

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
            /**
             * verifica se o inicio do lançamento é maior que o fim
             * se for retorna erro
             */
            if( $_POST['fr_tim'] > $_POST['to_tim'] ){
                print '
                    <br>
                    <div class="alert alert-warning" role="alert">
                        Favor inserir o horário de início inferior ao horário de término.
                    </div>
                    ';
            }
            else{
                /**
                 * verifica o horário acumulado do lançamento acumulado
                 * e a diferença entre agora e o lançamento
                 */
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
                /**
                 * se o acumulado não ultrapassar o horário do almoço 
                 * e a diferença do lançamento com agora for maior igual a zero
                 * (ou seja não é lançamento de amanhã ou superior)
                 * permite o lançamento
                 */
                if(($acumulado < 6.4)and ($amanha >= 0)){
                    /**
                     * de acordo com a operação selecionada é feito a procura
                     * no banco pra saber qual pais pertence aquela operação
                     */
                    $s_tblcty = "
                    SELECT * FROM usropr WHERE opr_id = ".$_POST['opr_id']."
                    ";
                    $rescty = $db->query($s_tblcty);
                    $rescty = $rescty->fetchArray(SQLITE3_ASSOC);
                    
                    /**
                     * inserindo dados de log do apontamento
                     */
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

                    /**
                     * caso não consiga inserir no banco retorna um erro
                     * para o usuário
                     */
                    if($resultado->numColumns() != 0){
                        print '                        
                        <br>
                        <div class="alert alert-warning" role="alert">
                            Erro ao salvar Lançamento. Procure o Administrador do sistema.
                        </div>
                        ';
                    }
                    /**
                     * caso inserir retorna um sucesso e atualiza a página
                     */
                    else{
                        print '
                        <br>
                        <div class="alert alert-success" role="alert">
                            Apontamento lançado!
                        </div>
                        <script>
                            jQuery("#apontamento input").val("");
                            jQuery("#apontamento select").val("");
                            jQuery("#apontamento textarea").val("");
                            jQuery("#apontamento input[type = submit]").val("");
                        </script>
                        ';
                    }
                }
                /**
                 * se o acumulado ultrapassar o horário do almoço 
                 * ou a diferença do lançamento com agora for menor que zero
                 * (ou seja é lançamento de amanhã ou antes)
                 * realiza a checagem e retorna pro usuário
                 */
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
        /**
         * problema no envio de parametros
         */
        else{
            print '
            <br>
            <div class="alert alert-warning" role="alert">
                Erro no envio dos parametros. Procure o Administrador do sistema.
            </div>
            ';
        }

    }

    if($_POST['page'] == 'apontamento-form-gestor' && $datapp > $limite){ // gestor //não lança
        echo '
        <br>
        <div class="alert alert-danger" role="alert">
            Gestores estão autorizadas a lançamentos apenas após o fechamento semanal.
        </div>
        ';
    }
    else if($_POST['page'] != 'apontamento-form') { // lança
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
                /**
                 * verifica se o inicio do lançamento é maior que o fim
                 * se for retorna erro
                 */
                if( $_POST['fr_tim'] > $_POST['to_tim'] ){
                    print '
                        <br>
                        <div class="alert alert-warning" role="alert">
                            Favor inserir o horário de início inferior ao horário de término.
                        </div>
                        ';
                }
                else{
                    /**
                     * verifica o horário acumulado do lançamento acumulado
                     * e a diferença entre agora e o lançamento
                     */
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
                /**
                 * se o acumulado ultrapassar o horário do almoço 
                 * ou a diferença do lançamento com agora for menor que zero
                 * (ou seja é lançamento de amanhã ou antes)
                 * realiza a checagem e retorna pro usuário
                 */
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
                    /**
                     * caso não consiga inserir no banco retorna um erro
                     * para o usuário
                     */
                    if($resultado->numColumns() != 0){
                        print '
                        <br>
                        <div class="alert alert-warning" role="alert">
                            Erro ao salvar Lançamento. Procure o Administrador do sistema.
                        </div>
                        ';
                    }
                    /**
                     * caso inserir retorna um sucesso e atualiza a página
                     */
                    else{
                        print '
                        <br>
                        <div class="alert alert-success" role="alert">
                            Apontamento lançado.
                        </div>
                        <script>
                            jQuery("#apontamento input").val("");
                            jQuery("#apontamento select").val("");
                            jQuery("#apontamento textarea").val("");
                            jQuery("#apontamento input[type = submit]").val("");
                        </script>
                        ';
                    }
                }
                /**
                 * se o acumulado ultrapassar o horário do almoço 
                 * ou a diferença do lançamento com agora for menor que zero
                 * (ou seja é lançamento de amanhã ou antes)
                 * realiza a checagem e retorna pro usuário
                 */
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
    }
?>