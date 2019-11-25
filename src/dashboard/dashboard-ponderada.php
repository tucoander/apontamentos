<?php
include('../template/template-barra.php');
$db = new SQLite3('../sqlite/apontamentos.db');
$resposta = '';
/*
    Cenario POST
    sem parametros
        - com data
        - com data e usuario
 */
$lblgrp = array();
$numgrp = array();
$pergrp = array();



$fadiga = 0.146;
$tamanho = 55;

if(isset($_POST['fr_adddte']) && isset($_POST['to_adddte'])){
    if(isset($_POST['usr_id']) && !empty($_POST['usr_id'])){
        /*
            Mostrar um retorno do filtro para o usuário
        */
        $final = ' para o usuário: ' . $_POST['usr_id'];
        $msg = 'Intervalo Pesquisado: ';
        $msg .= date("d/m/Y", strtotime($_POST['fr_adddte']));
        $msg .= ' até ';
        $msg .= date("d/m/Y", strtotime($_POST['to_adddte']));
        $msg .= $final;
        /*
            Parametros para envio do form de detalhamento
        */
        $fr_dte = $_POST['fr_adddte'];
        $to_dte = $_POST['to_adddte'];
        $usr_id = $_POST['usr_id'];

        /*
        Inicio do apontamento entre duas datas com usuário - Com indice
        */
        $selapp = "
            SELECT 
                op.oprnme as oprnme,
                round(ifnull(
                    sum(
                    (julianday(strftime('%Y-%m-%d %H:%M', (julianday(strftime('%Y-%m-%d %H:%M', ul.logdte || ' ' || ul.to_logtim)) - julianday(((24.0 - 23.0) / 24.0) *3.0)))) 
                    - julianday(strftime('%Y-%m-%d %H:%M', (julianday(strftime('%Y-%m-%d %H:%M', ul.logdte || ' ' || ul.fr_logtim)) - julianday(((24.0 - 23.0) / 24.0) *3.0))))) 
                    *24 * uf.funidx), 0), 2) as aponta
            FROM (SELECT
                        (dt.yr_dte||'-'||dt.mn_dte||'-'||dt.dy_dte) days,
                        (dt.shftim * uf.funidx)*(1-".$fadiga.") as shftim,
                        us.usr_id as user
                    FROM 
                        yr_idx dt 
                        join 
                        usrsys us  
                        left join 
                        funusr fu on (us.usr_id = fu.usr_id)
                        left join 
                        usrfun uf on (fu.fun_id = uf.fun_id)
                        WHERE dt.wk_day not in ('sábado','domingo')
                        and us.usr_id in (select usr_id from usrlog 
                                            where logdte between :fr_adddte and :to_adddte 
                                            and usr_id = :usr_id)
                        and  (dt.yr_dte || '-' || dt.mn_dte || '-' || dt.dy_dte) in 
                        (select logdte from usrlog where logdte between :fr_adddte and :to_adddte )
                                            ) bd
                    left join usrlog ul
                    on(bd.days = (strftime('%Y-%m-%d', (julianday(strftime('%Y-%m-%d %H:%M', ul.logdte || ' ' || ul.fr_logtim)) - julianday(((24.0 - 23.0) / 24.0) *3.0)))) 
                    and bd.user = ul.usr_id)
                    left join 
                    usrind ui on (bd.days = ui.inddte and bd.user = ui.usr_id)
                    left join
                    usropr op on (ul.opr_id = op.opr_id) 
                    left join 
                    usrprd pd on (ul.prd_id = pd.prd_id)
                    left join 
                    funusr fu on (bd.user = fu.usr_id)
                    left join 
                    usrfun uf on (fu.fun_id = uf.fun_id)
            WHERE 
                bd.days between :fr_adddte and :to_adddte 
                and bd.user = :usr_id
            GROUP BY
                op.oprnme
            HAVING 
                op.opr_id is not null
            ORDER BY 
                op.opr_id asc
        ";

        try {
            $cmdapp = $db->prepare($selapp);
            $cmdapp->bindValue('fr_adddte', $_POST['fr_adddte']);
            $cmdapp->bindValue('to_adddte', $_POST['to_adddte']);
            $cmdapp->bindValue('usr_id', $_POST['usr_id']);
            $resapp = $cmdapp->execute();
        } catch (Exception $e) {
            $sqlerr .= '<hr>Caught exception: ' . $e->getMessage();
        }

        while ($rowapp = $resapp->fetchArray(SQLITE3_ASSOC)) {
            $lblgrp[] = $rowapp['oprnme'];
            $numgrp[] = $rowapp['aponta'];
        }
        /*
        Fim do apontamento entre duas datas com usuário - Com indice
        */

        /*
        Inicio da disponibilidade entre duas datas com usuário - Com indice
        */
        $seldsp = "
            select sum(dispon) as indisp from (
                SELECT bd.days,
                        bd.user,
                        bd.shftim,
                        uf.funidx,
                        ifnull(sum((julianday(ul.to_logtim) - julianday(ul.fr_logtim)) *24 * uf.funidx), 0) as aponta,
                        ifnull(sum((julianday(ui.to_logtim) - julianday(ui.fr_logtim)) *24 * uf.funidx), 0) as indisp,
                        round((bd.shftim - 
                        ifnull(
                                sum(
                                (julianday(strftime('%Y-%m-%d %H:%M', (julianday(strftime('%Y-%m-%d %H:%M', ul.logdte || ' ' || ul.to_logtim)) - julianday(((24.0 - 23.0) / 24.0) *3.0)))) 
                                - julianday(strftime('%Y-%m-%d %H:%M', (julianday(strftime('%Y-%m-%d %H:%M', ul.logdte || ' ' || ul.fr_logtim)) - julianday(((24.0 - 23.0) / 24.0) *3.0))))) 
                                *24 * uf.funidx), 0)
                         - ifnull(sum((julianday(ui.to_logtim) - julianday(ui.fr_logtim)) *24 * uf.funidx), 0)), 2) as dispon
                    FROM (SELECT (dt.yr_dte || '-' || dt.mn_dte || '-' || dt.dy_dte) days,
                                    (dt.shftim * uf.funidx)*(1-".$fadiga.") as shftim,
                                    us.usr_id as user
                            FROM yr_idx dt
                            join usrsys us
                            left join 
                            funusr fu on (us.usr_id = fu.usr_id)
                            left join 
                            usrfun uf on (fu.fun_id = uf.fun_id)
                            WHERE dt.wk_day not in ('sábado', 'domingo') 
                            and  us.usr_id in (select usr_id from usrlog 
                                                where logdte between :fr_adddte and :to_adddte 
                                                and usr_id = :usr_id)
                            and  (dt.yr_dte || '-' || dt.mn_dte || '-' || dt.dy_dte) in 
                            (select logdte from usrlog where logdte between :fr_adddte and :to_adddte )
                                                ) bd
                    left join usrlog ul
                    on(bd.days = (strftime('%Y-%m-%d', (julianday(strftime('%Y-%m-%d %H:%M', ul.logdte || ' ' || ul.fr_logtim)) - julianday(((24.0 - 23.0) / 24.0) *3.0)))) 
                    and bd.user = ul.usr_id)
                    left
                    join usrind ui
                        on (bd.days = ui.inddte and bd.user = ui.usr_id)
                    left
                    join usropr op
                        on (ul.opr_id = op.opr_id)
                    left
                    join usrprd pd
                        on (ul.prd_id = pd.prd_id)
                    left
                    join funusr fu
                        on (bd.user = fu.usr_id)
                    left
                    join usrfun uf
                        on (fu.fun_id = uf.fun_id)
                    WHERE bd.days between :fr_adddte
                        and :to_adddte
                        and bd.user = :usr_id
                    GROUP BY bd.days,
                            bd.user,
                            bd.shftim,
                            uf.funidx
            )
        ";

        try {
            $cmddsp = $db->prepare($seldsp);
            $cmddsp->bindValue('fr_adddte', $_POST['fr_adddte']);
            $cmddsp->bindValue('to_adddte', $_POST['to_adddte']);
            $cmddsp->bindValue('usr_id', $_POST['usr_id']);
            $resdsp = $cmddsp->execute();
        } catch (Exception $e) {
            $sqlerr .= '<hr>Caught exception: ' . $e->getMessage();
        }

        while ($rowdsp = $resdsp->fetchArray(SQLITE3_ASSOC)) {
            array_unshift($lblgrp, 'Disponível');
            array_unshift($numgrp, $rowdsp['indisp']);
        }

        /*
        Fim da disponibilidade entre duas datas com usuário - Com indice
        */
    }
    else{
        /*
            Mostrar um retorno do filtro para o usuário
        */
        $final = ' para todos os usuários';
        $msg = 'Intervalo Pesquisado: ';
        $msg .= date("d/m/Y", strtotime($_POST['fr_adddte']));
        $msg .= ' até ';
        $msg .= date("d/m/Y", strtotime($_POST['to_adddte']));
        $msg .= $final;
        /*
        Inicio do apontamento entre duas datas - Com indice
        */
        /*
            Parametros para envio do form de detalhamento
        */
        $fr_dte = $_POST['fr_adddte'];
        $to_dte = $_POST['to_adddte'];
        $usr_id = '';
        
        $selapp = "
            SELECT 
                op.oprnme as oprnme,
                round(ifnull(
                    sum(
                    (julianday(strftime('%Y-%m-%d %H:%M', (julianday(strftime('%Y-%m-%d %H:%M', ul.logdte || ' ' || ul.to_logtim)) - julianday(((24.0 - 23.0) / 24.0) *3.0)))) 
                    - julianday(strftime('%Y-%m-%d %H:%M', (julianday(strftime('%Y-%m-%d %H:%M', ul.logdte || ' ' || ul.fr_logtim)) - julianday(((24.0 - 23.0) / 24.0) *3.0))))) 
                    *24 * uf.funidx), 0), 2) as aponta
            FROM (SELECT
                        (dt.yr_dte||'-'||dt.mn_dte||'-'||dt.dy_dte) days,
                        (dt.shftim * uf.funidx)*(1-".$fadiga.") as shftim,
                        us.usr_id as user
                    FROM 
                        yr_idx dt 
                        join 
                        usrsys us  
                        left join 
                        funusr fu on (us.usr_id = fu.usr_id)
                        left join 
                        usrfun uf on (fu.fun_id = uf.fun_id)
                        WHERE dt.wk_day not in ('sábado','domingo')
                        and us.usr_id in (select usr_id from usrlog where logdte between :fr_adddte and :to_adddte )
                        and  (dt.yr_dte || '-' || dt.mn_dte || '-' || dt.dy_dte) in 
                                    (select logdte from usrlog where logdte between :fr_adddte and :to_adddte )
                        ) bd
                    left join usrlog ul
                    on(bd.days = (strftime('%Y-%m-%d', (julianday(strftime('%Y-%m-%d %H:%M', ul.logdte || ' ' || ul.fr_logtim)) - julianday(((24.0 - 23.0) / 24.0) *3.0)))) 
                    and bd.user = ul.usr_id)
                    left join 
                    usrind ui on (bd.days = ui.inddte and bd.user = ui.usr_id)
                    left join
                    usropr op on (ul.opr_id = op.opr_id) 
                    left join 
                    usrprd pd on (ul.prd_id = pd.prd_id)
                    left join 
                    funusr fu on (bd.user = fu.usr_id)
                    left join 
                    usrfun uf on (fu.fun_id = uf.fun_id)
            WHERE 
                bd.days between :fr_adddte and :to_adddte 
            GROUP BY
                op.oprnme
            HAVING 
                op.opr_id is not null
            ORDER BY 
                op.opr_id asc
        ";
        

        try {
            $cmdapp = $db->prepare($selapp);
            $cmdapp->bindValue('fr_adddte', $_POST['fr_adddte']);
            $cmdapp->bindValue('to_adddte', $_POST['to_adddte']);
            $resapp = $cmdapp->execute();
        } catch (Exception $e) {
            $sqlerr .= '<hr>Caught exception: ' . $e->getMessage();
        }

        while ($rowapp = $resapp->fetchArray(SQLITE3_ASSOC)) {
            $lblgrp[] = $rowapp['oprnme'];
            $numgrp[] = $rowapp['aponta'];
        }
        /*
        Fim do apontamento entre duas datas - Com indice
        */

        /*
        Inicio da disponibilidade entre duas datas - Com indice
        */
        $seldsp = "
            select sum(dispon) as indisp from (
                SELECT bd.days,
                        bd.user,
                        bd.shftim,
                        uf.funidx,
                        ifnull(sum((julianday(ul.to_logtim) - julianday(ul.fr_logtim)) *24 * uf.funidx), 0) as aponta,
                        ifnull(sum((julianday(ui.to_logtim) - julianday(ui.fr_logtim)) *24 * uf.funidx), 0) as indisp,
                        round((bd.shftim - 
                        ifnull(
                                sum(
                                (julianday(strftime('%Y-%m-%d %H:%M', (julianday(strftime('%Y-%m-%d %H:%M', ul.logdte || ' ' || ul.to_logtim)) - julianday(((24.0 - 23.0) / 24.0) *3.0)))) 
                                - julianday(strftime('%Y-%m-%d %H:%M', (julianday(strftime('%Y-%m-%d %H:%M', ul.logdte || ' ' || ul.fr_logtim)) - julianday(((24.0 - 23.0) / 24.0) *3.0))))) 
                                *24 * uf.funidx), 0)
                         - ifnull(sum((julianday(ui.to_logtim) - julianday(ui.fr_logtim)) *24 * uf.funidx), 0)), 2) as dispon
                    FROM (SELECT (dt.yr_dte || '-' || dt.mn_dte || '-' || dt.dy_dte) days,
                                    (dt.shftim * uf.funidx)*(1-".$fadiga.") as shftim,
                                    us.usr_id as user
                            FROM yr_idx dt
                            join usrsys us
                            left join 
                            funusr fu on (us.usr_id = fu.usr_id)
                            left join 
                            usrfun uf on (fu.fun_id = uf.fun_id)
                            WHERE dt.wk_day not in ('sábado', 'domingo') 
                            and  us.usr_id in (select usr_id from usrlog where logdte between :fr_adddte and :to_adddte )
                            and  (dt.yr_dte || '-' || dt.mn_dte || '-' || dt.dy_dte) in 
                                    (select logdte from usrlog where logdte between :fr_adddte and :to_adddte )
                            ) bd
                    left join usrlog ul
                    on(bd.days = (strftime('%Y-%m-%d', (julianday(strftime('%Y-%m-%d %H:%M', ul.logdte || ' ' || ul.fr_logtim)) - julianday(((24.0 - 23.0) / 24.0) *3.0)))) 
                    and bd.user = ul.usr_id)
                    left
                    join usrind ui
                        on (bd.days = ui.inddte and bd.user = ui.usr_id)
                    left
                    join usropr op
                        on (ul.opr_id = op.opr_id)
                    left
                    join usrprd pd
                        on (ul.prd_id = pd.prd_id)
                    left
                    join funusr fu
                        on (bd.user = fu.usr_id)
                    left
                    join usrfun uf
                        on (fu.fun_id = uf.fun_id)
                    WHERE bd.days between :fr_adddte
                        and :to_adddte
                    GROUP BY bd.days,
                            bd.user,
                            bd.shftim,
                            uf.funidx
            )
        ";

        

        try {
            $cmddsp = $db->prepare($seldsp);
            $cmddsp->bindValue('fr_adddte', $_POST['fr_adddte']);
            $cmddsp->bindValue('to_adddte', $_POST['to_adddte']);
            $resdsp = $cmddsp->execute();
        } catch (Exception $e) {
            $sqlerr .= '<hr>Caught exception: ' . $e->getMessage();
        }

        while ($rowdsp = $resdsp->fetchArray(SQLITE3_ASSOC)) {
            array_unshift($lblgrp, 'Disponível');
            array_unshift($numgrp, $rowdsp['indisp']);
        }

        /*
        Fim da disponibilidade entre duas datas - Com indice
        */
    }
}
else {

        /*
            Mostrar um retorno do filtro para o usuário
        */
        $final = ' para todos os usuários';
        $msg = 'Intervalo Pesquisado é o mês atual';
        $msg .= $final;
        /*
            Parametros para envio do form de detalhamento
        */
        $fr_dte = 'mes atual';
        $to_dte = '';
        $usr_id = '';
        /*
        Inicio do apontamento entre duas datas - Com indice
        */

        
        
        $selapp = "
            SELECT 
                op.oprnme as oprnme,
                round(ifnull(
                    sum(
                    (julianday(strftime('%Y-%m-%d %H:%M', (julianday(strftime('%Y-%m-%d %H:%M', ul.logdte || ' ' || ul.to_logtim)) - julianday(((24.0 - 23.0) / 24.0) *3.0)))) 
                    - julianday(strftime('%Y-%m-%d %H:%M', (julianday(strftime('%Y-%m-%d %H:%M', ul.logdte || ' ' || ul.fr_logtim)) - julianday(((24.0 - 23.0) / 24.0) *3.0))))) 
                    *24 * uf.funidx), 0), 2) as aponta
            FROM (SELECT
                        (dt.yr_dte||'-'||dt.mn_dte||'-'||dt.dy_dte) days,
                        (dt.shftim * uf.funidx)*(1-".$fadiga.") as shftim,
                        us.usr_id as user
                    FROM 
                        yr_idx dt 
                        join 
                        usrsys us  
                        left join 
                        funusr fu on (us.usr_id = fu.usr_id)
                        left join 
                        usrfun uf on (fu.fun_id = uf.fun_id)
                        WHERE dt.wk_day not in ('sábado','domingo')
                        and us.usr_id in (select usr_id from usrlog where substr(logdte,0,8) = :fr_adddte)
                        and  (dt.yr_dte || '-' || dt.mn_dte || '-' || dt.dy_dte) 
                            in (select logdte from usrlog where substr(logdte,0,8) = :fr_adddte  )
                        ) bd
                    left join usrlog ul
                    on(bd.days = (strftime('%Y-%m-%d', (julianday(strftime('%Y-%m-%d %H:%M', ul.logdte || ' ' || ul.fr_logtim)) - julianday(((24.0 - 23.0) / 24.0) *3.0)))) 
                    and bd.user = ul.usr_id)
                    left join 
                    usrind ui on (bd.days = ui.inddte and bd.user = ui.usr_id)
                    left join
                    usropr op on (ul.opr_id = op.opr_id) 
                    left join 
                    usrprd pd on (ul.prd_id = pd.prd_id)
                    left join 
                    funusr fu on (bd.user = fu.usr_id)
                    left join 
                    usrfun uf on (fu.fun_id = uf.fun_id)
            WHERE 
                substr(bd.days,0,8) = :fr_adddte 
            GROUP BY
                op.oprnme
            HAVING 
                op.opr_id is not null
            ORDER BY 
                op.opr_id asc
        ";

        try {
            $cmdapp = $db->prepare($selapp);
            $cmdapp->bindValue('fr_adddte', date("Y") . '-' . date("m"));
            $resapp = $cmdapp->execute();
        } catch (Exception $e) {
            $sqlerr .= '<hr>Caught exception: ' . $e->getMessage();
        }

        while ($rowapp = $resapp->fetchArray(SQLITE3_ASSOC)) {
            $lblgrp[] = $rowapp['oprnme'];
            $numgrp[] = $rowapp['aponta'];
        }
        /*
        Fim do apontamento entre duas datas - Com indice
        */

        /*
        Inicio da disponibilidade entre duas datas - Com indice
        */
        $seldsp = "
            select sum(dispon) as indisp from (
                SELECT bd.days,
                        bd.user,
                        bd.shftim,
                        uf.funidx,
                        ifnull(sum((julianday(ul.to_logtim) - julianday(ul.fr_logtim)) *24 * uf.funidx), 0) as aponta,
                        ifnull(sum((julianday(ui.to_logtim) - julianday(ui.fr_logtim)) *24 * uf.funidx), 0) as indisp,
                        round((bd.shftim - 
                        ifnull(
                                sum(
                                (julianday(strftime('%Y-%m-%d %H:%M', (julianday(strftime('%Y-%m-%d %H:%M', ul.logdte || ' ' || ul.to_logtim)) - julianday(((24.0 - 23.0) / 24.0) *3.0)))) 
                                - julianday(strftime('%Y-%m-%d %H:%M', (julianday(strftime('%Y-%m-%d %H:%M', ul.logdte || ' ' || ul.fr_logtim)) - julianday(((24.0 - 23.0) / 24.0) *3.0))))) 
                                *24 * uf.funidx), 0)
                         - ifnull(sum((julianday(ui.to_logtim) - julianday(ui.fr_logtim)) *24 * uf.funidx), 0)), 2) as dispon
                    FROM (SELECT (dt.yr_dte || '-' || dt.mn_dte || '-' || dt.dy_dte) days,
                                    (dt.shftim * uf.funidx)*(1-".$fadiga.") as shftim,
                                    us.usr_id as user
                            FROM yr_idx dt
                            join usrsys us
                            left join 
                            funusr fu on (us.usr_id = fu.usr_id)
                            left join 
                            usrfun uf on (fu.fun_id = uf.fun_id)
                            WHERE dt.wk_day not in ('sábado', 'domingo') 
                            and  us.usr_id in (select usr_id from usrlog where substr(logdte,0,8) = :fr_adddte )
                            and  (dt.yr_dte || '-' || dt.mn_dte || '-' || dt.dy_dte) 
                            in (select logdte from usrlog where substr(logdte,0,8) = :fr_adddte  )
                            ) bd
                    left join usrlog ul
                    on(bd.days = (strftime('%Y-%m-%d', (julianday(strftime('%Y-%m-%d %H:%M', ul.logdte || ' ' || ul.fr_logtim)) - julianday(((24.0 - 23.0) / 24.0) *3.0)))) 
                    and bd.user = ul.usr_id)
                    left
                    join usrind ui
                        on (bd.days = ui.inddte and bd.user = ui.usr_id)
                    left
                    join usropr op
                        on (ul.opr_id = op.opr_id)
                    left
                    join usrprd pd
                        on (ul.prd_id = pd.prd_id)
                    left
                    join funusr fu
                        on (bd.user = fu.usr_id)
                    left
                    join usrfun uf
                        on (fu.fun_id = uf.fun_id)
                    WHERE 
                        substr(bd.days,0,8) = :fr_adddte 
                    GROUP BY bd.days,
                            bd.user,
                            bd.shftim,
                            uf.funidx
            )
        ";

        try {
            $cmddsp = $db->prepare($seldsp);
            $cmddsp->bindValue('fr_adddte', date("Y") . '-' . date("m"));
            $resdsp = $cmddsp->execute();
        } catch (Exception $e) {
            $sqlerr .= '<hr>Caught exception: ' . $e->getMessage();
        }

        while ($rowdsp = $resdsp->fetchArray(SQLITE3_ASSOC)) {
            array_unshift($lblgrp, 'Disponível');
            array_unshift($numgrp, $rowdsp['indisp']);
        }

        /*
        Fim da disponibilidade entre duas datas - Com indice
        */
}


$soma_hora = 0;
foreach ($numgrp as $rowsoma) {
    $soma_hora = $soma_hora + $rowsoma;
}

foreach ($numgrp as $row) {
    $pergrp[] = round($row / $soma_hora, 4) * 100;
}


$table = '
    <div class="card" >
        <div class="card-body">
            <table class="table" id="apontamento">
                <thead>
                    <tr>
                        <th scope="col">Operação</th>
                        <th scope="col">Percentual</th>
                        <th scope="col">Horas</th>
                    </tr>
                </thead>
                <tbody>
    ';

foreach ($numgrp as $key => $value) {
    if($lblgrp[$key] != 'Disponível'){
        $table .= '
                    <tr>
                        <td scope="row">
                        <form method="POST" action="dashboard-detalhe.php">
                            <div class="col">
                                <input type="submit" class="btn btn-light" name="oprnme" value="' . $lblgrp[$key] . '">
                                <input type="hidden" id="fr_dte" name="fr_dte" value="' . $fr_dte . '">
                                <input type="hidden" id="to_dte" name="to_dte" value="' . $to_dte . '">
                                <input type="hidden" id="usr_id" name="usr_id" value="' . $usr_id . '">
                            </div>
                        </form>
                        </td>
                        <td>' . $pergrp[$key] . ' %</td>
                        <td>' . floor($value) . 'h' . floor(($value - floor($value)) * 60) . 'm</td>
                    </tr>
        ';
    }
}

foreach ($numgrp as $key => $value) {
    if($lblgrp[$key] == 'Disponível'){
        $table .= '
                    <tr>
                        <td scope="row">
                        <form method="POST" action="dashboard-detalhe.php">
                            <div class="col">
                                <input type="submit" class="btn btn-light" name="filtro" value="' . $lblgrp[$key] . '">
                                <input type="hidden" id="oprnme" name="oprnme" value="' . $lblgrp[$key] . '">
                                <input type="hidden" id="fr_dte" name="fr_dte" value="' . $fr_dte . '">
                                <input type="hidden" id="to_dte" name="to_dte" value="' . $to_dte . '">
                                <input type="hidden" id="usr_id" name="usr_id" value="' . $usr_id . '">
                            </div>
                        </form>
                        </td>
                        <td>' . $pergrp[$key] . ' %</td>
                        <td>' . floor($value) . 'h' . floor(($value - floor($value)) * 60) . 'm</td>
                    </tr>
        ';
    }
}


$table .= '
                </tbody>
            </table>
        </div>
    </div>
    ';

$data_perc_script = implode("', '", $pergrp);
$labels_script = implode("', '", $lblgrp);
$data_script = implode("', '", $numgrp);



?>

<?php
    $tabela_ponderacao = '
    <div class="card" >
            <div class="card-body">
                <table class="table" id="apontamento">
                    <thead>
                        <tr>
                            <th scope="col">Função</th>
                            <th scope="col">Fator</th>
                        </tr>
                    </thead>
                    <tbody>
                   
                        <tr>	<td>	Estagiário	</td><td>	0,15	</td>	</tr>
                        <tr>	<td>	Assistente Jr	</td><td>	0,30	</td>	</tr>
                        <tr>	<td>	Assistente Pl	</td><td>	0,45	</td>	</tr>
                        <tr>	<td>	Assisitente Sr	</td><td>	0,60	</td>	</tr>
                        <tr>	<td>	Analista Jr	</td><td>	0,75	</td>	</tr>
                        <tr>	<td>	Analista Pl	</td><td>	1,00	</td>	</tr>
                        <tr>	<td>	Analista Sr	</td><td>	1,15	</td>	</tr>
                        <tr>	<td>	Supervisor	</td><td>	1,30	</td>	</tr>
                        <tr>	<td>	Chefe	</td><td>	1,45	</td>	</tr>
                        <tr>	<td>	Gerente	</td><td>	1,60	</td>	</tr>


                </tbody>
            </table>
        </div>
    ';
?>

<div role="main" class="container-fluid">
    <div class="card">
        <div class="card-body" <?php print $tamanho; ?>>
            <form method="POST" action="dashboard-ponderada.php">
                <div class="row">
                    <div class="col">
                        <input type="text" class="form-control-plaintext" value="Data Início:">
                    </div>
                    <div class="col">
                        <input type="date" class="form-control" placeholder="Data" id="fr_adddte" name="fr_adddte">
                    </div>
                    <div class="col">
                        <input type="text" class="form-control-plaintext" value="Data Final:">
                    </div>
                    <div class="col">
                        <input type="date" class="form-control" placeholder="Data" id="to_adddte" name="to_adddte">
                    </div>
                    <div class="col">
                        <input type="submit" class="btn btn-primary col-12" name="filtro" value="Filtrar">
                    </div>
                    <div class="col">
                        <input type="submit" class="btn btn-primary col-12" name="todos" value="Limpar Filtro">
                    </div>
                </div>
                <hr>
                <div class="row">

                    <div class="col-2">
                        <input type="text" class="form-control-plaintext" value="Usuário:">
                    </div>
                    <div class="col-6">
                        <select class="custom-select d-block w-100" id="usr_id" name="usr_id" onchange="">
                            <option value="">Selecione...</option>
                            <?php
                            $db = new SQLite3('../sqlite/apontamentos.db');
                            $s_tblprd = "
                                    SELECT 
                                    us.usr_id,
                                    us.usrnme
                                        FROM usrsys us
                                        GROUP BY 
                                        us.usr_id,
                                        us.usrnme
                                    ";
                            $resultado = $db->query($s_tblprd);
                            while ($row = $resultado->fetchArray(SQLITE3_ASSOC)) {
                                print '
                                    <option value="' . $row["usr_id"] . '">' . $row["usr_id"] . ' - ' . $row["usrnme"] . '</option>
                                    ';
                            }
                            ?>
                        </select>

                    </div>

                </div>
            </form>
            <div>
                <?php
                if (!empty($msg)) {
                    print '<hr>' . $msg;
                }
                ?>
            </div>
            <hr>
            <div class="row">
                <div class="col-sm-9">
                    <div sytle="">
                        <div class="chart-container">
                            <canvas id="myChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <?php echo $tabela_ponderacao; ?>
                </div>
            </div>
            
        </div>
    </div>
    <br>

   

    <div class=""> <?php print '' . $table . '';  ?></div>

</div>

   

</div>

<div>
    <?php
    include('../template/template-rodape.php');
    ?>
    <script>
        var ctx = document.getElementById('myChart').getContext('2d');
        var chart = new Chart(ctx, {
            type: 'doughnut',
            responsive: true,
            data: {
                labels: ['<?php print $labels_script; ?> '],
                datasets: [{
                    label: 'Operações',
                    backgroundColor: [
                        'rgb(50, 50, 50)',
                        'rgb(0, 86, 145)',
                        'rgb(0, 142, 207)',
                        'rgb(0, 168, 176)',
                        'rgb(120, 190, 32)',
                        'rgb(0, 98, 73)',
                        'rgb(185, 2, 118)',
                        'rgb(80, 35, 127)',
                        'rgb(82, 95, 107)',
                        'rgb(191, 192, 194)',
                        'rgb(0, 86, 145)',
                        'rgb(0, 142, 207)',
                        'rgb(0, 168, 176)',
                        'rgb(120, 190, 32)',
                        'rgb(0, 98, 73)',
                        'rgb(185, 2, 118)',
                        'rgb(80, 35, 127)',
                        'rgb(82, 95, 107)',
                        'rgb(191, 192, 194)'
                    ],
                    borderColor: 'rgb(255, 255, 255)',
                    data: [' <?php print $data_perc_script; ?> '
                    ]
                }]
            },

            // Configuration options go here
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 2.5,
                legend: {
                    position: 'left'
                },
                layout: {
                    padding: {
                        left: 0,
                        right: 0,
                        top: 0,
                        bottom: 0
                    }
                }
            }
        });
    </script>
</div>