<?php
include('../template/template-barra.php');
$db = new SQLite3('../sqlite/apontamentos.db');
/**
 * Variaveis
 */
$lblgrp = array();
$numgrp = array();
$pergrp = array();

$fadiga = 0.146;
$tamanho = 55;
$select = '';
$color = array (
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
    'rgb(191, 192, 194)',
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
);

if(isset($_POST['oprnme']) && !empty($_POST['oprnme']) && $_POST['oprnme'] != 'Disponível'){
    if(isset($_POST['fr_dte']) && $_POST['fr_dte'] == 'mes atual'){
        $msg = 'Mês atual da operação '.$_POST['oprnme'];

        $selapp = "
            SELECT bd.user as user,
                round(ifnull(sum((julianday(strftime('%Y-%m-%d %H:%M', (julianday(strftime('%Y-%m-%d %H:%M', ul.logdte || ' ' || ul.to_logtim)) - julianday(((24.0 - 23.0) / 24.0) *3.0)))) - julianday(strftime('%Y-%m-%d %H:%M', (julianday(strftime('%Y-%m-%d %H:%M', ul.logdte || ' ' || ul.fr_logtim)) - julianday(((24.0 - 23.0) / 24.0) *3.0))))) *24 * uf.funidx), 0), 2) as aponta
            FROM (SELECT (dt.yr_dte || '-' || dt.mn_dte || '-' || dt.dy_dte) days,
                            (dt.shftim * uf.funidx) *(1 - ".$fadiga.") as shftim,
                            us.usr_id as user
                    FROM yr_idx dt
                    join usrsys us
                    left
                    join funusr fu
                        on (us.usr_id = fu.usr_id)
                    left
                    join usrfun uf
                        on (fu.fun_id = uf.fun_id)
                    WHERE dt.wk_day not in ('sábado', 'domingo')
                        and us.usr_id in (select usr_id
                                            from usrlog
                                        where substr(logdte, 0, 8) = :fr_adddte)
                        and (dt.yr_dte || '-' || dt.mn_dte || '-' || dt.dy_dte) in (select logdte
                                                                                    from usrlog
                                                                                    where substr(logdte, 0, 8) = :fr_adddte)) bd
            left
            join usrlog ul
                on (bd.days = (strftime('%Y-%m-%d', (julianday(strftime('%Y-%m-%d %H:%M', ul.logdte || ' ' || ul.fr_logtim)) - julianday(((24.0 - 23.0) / 24.0) *3.0)))) and bd.user = ul.usr_id)
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
            WHERE substr(bd.days, 0, 8) = :fr_adddte
                and op.oprnme = :oprnme
            GROUP BY bd.user
            ORDER BY bd.user asc
        ";

        try {
            $cmdapp = $db->prepare($selapp);
            $cmdapp->bindValue('fr_adddte', date("Y") . '-' . date("m"));
            $cmdapp->bindValue('oprnme', $_POST['oprnme']);
            $resapp = $cmdapp->execute();
        } catch (Exception $e) {
            $sqlerr .= '<hr>Caught exception: ' . $e->getMessage();
        }

        while ($rowapp = $resapp->fetchArray(SQLITE3_ASSOC)) {
            $lblgrp[] = $rowapp;
           
        }
        /*
        Fim do apontamento entre duas datas - Com indice
        */

    }
    else if(isset($_POST['fr_dte']) && $_POST['fr_dte'] != 'mes atual'){
        $fr_dte = new DateTime($_POST['fr_dte']);
        $to_dte = new DateTime($_POST['to_dte']);
        $msg = 'Intervalo de pesquisa: '.$fr_dte->format('d/m/Y').' até '.$to_dte->format('d/m/Y');
        $msg .= ' para a operação '.$_POST['oprnme'];

        $selapp = "
            SELECT bd.user as user,
                    nome as nome,
                round(ifnull(sum((julianday(strftime('%Y-%m-%d %H:%M', (julianday(strftime('%Y-%m-%d %H:%M', ul.logdte || ' ' || ul.to_logtim)) - julianday(((24.0 - 23.0) / 24.0) *3.0)))) - julianday(strftime('%Y-%m-%d %H:%M', (julianday(strftime('%Y-%m-%d %H:%M', ul.logdte || ' ' || ul.fr_logtim)) - julianday(((24.0 - 23.0) / 24.0) *3.0))))) *24 * uf.funidx), 0), 2) as aponta
            FROM (SELECT (dt.yr_dte || '-' || dt.mn_dte || '-' || dt.dy_dte) days,
                            (dt.shftim * uf.funidx) *(1 - ".$fadiga.") as shftim,
                            us.usr_id as user,
                            us.usrnme as nome
                    FROM yr_idx dt
                    join usrsys us
                    left
                    join funusr fu
                        on (us.usr_id = fu.usr_id)
                    left
                    join usrfun uf
                        on (fu.fun_id = uf.fun_id)
                    WHERE dt.wk_day not in ('sábado', 'domingo')
                        and us.usr_id in (select usr_id
                                            from usrlog
                                        where logdte between :fr_adddte and :to_adddte)
                        and (dt.yr_dte || '-' || dt.mn_dte || '-' || dt.dy_dte) in (select logdte
                                                                                    from usrlog
                                                                                    where logdte between :fr_adddte and :to_adddte)) bd
            left
            join usrlog ul
                on (bd.days = (strftime('%Y-%m-%d', (julianday(strftime('%Y-%m-%d %H:%M', ul.logdte || ' ' || ul.fr_logtim)) - julianday(((24.0 - 23.0) / 24.0) *3.0)))) and bd.user = ul.usr_id)
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
            WHERE bd.days between :fr_adddte and :to_adddte
                and op.oprnme = :oprnme
            GROUP BY bd.user
            ORDER BY bd.user asc
        ";

        try {
            $cmdapp = $db->prepare($selapp);
            $cmdapp->bindValue('fr_adddte', $_POST['fr_dte']);
            $cmdapp->bindValue('to_adddte', $_POST['to_dte']);
            $cmdapp->bindValue('oprnme', $_POST['oprnme']);
            $resapp = $cmdapp->execute();
        } catch (Exception $e) {
            $sqlerr .= '<hr>Caught exception: ' . $e->getMessage();
        }

        while ($rowapp = $resapp->fetchArray(SQLITE3_ASSOC)) {
            $lblgrp[] = $rowapp;
        }

    }
}
else if(isset($_POST['oprnme']) && !empty($_POST['oprnme']) && $_POST['oprnme'] == 'Disponível'){
    if(isset($_POST['fr_dte']) && $_POST['fr_dte'] == 'mes atual'){
        $msg = 'Dispnibilidade dos operadores no mês atual';
        

        $selapp = "
            select
                user as user,
                nome as nome,
                sum(dispon) as aponta 
            from (
                    SELECT bd.days,
                            bd.user,
                            bd.nome,
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
                                        us.usr_id as user,
                                        us.usrnme as nome
                                FROM yr_idx dt
                                join usrsys us
                                left join 
                                funusr fu on (us.usr_id = fu.usr_id)
                                left join 
                                usrfun uf on (fu.fun_id = uf.fun_id)
                                WHERE dt.wk_day not in ('sábado', 'domingo') 
                                and  us.usr_id in (select usr_id from usrlog where substr(logdte,0,8) = :fr_adddte)
                                and  (dt.yr_dte || '-' || dt.mn_dte || '-' || dt.dy_dte) in 
                                        (select logdte from usrlog where substr(logdte,0,8) = :fr_adddte)
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
                        WHERE substr( bd.days ,0,8) = :fr_adddte
                        GROUP BY bd.days,
                                bd.user,
                                bd.shftim,
                                uf.funidx
                )
            GROUP BY
                user
        ";

        try {
            $cmdapp = $db->prepare($selapp);
            $cmdapp->bindValue('fr_adddte', date("Y") . '-' . date("m"));
            $resapp = $cmdapp->execute();
        } catch (Exception $e) {
            $sqlerr .= '<hr>Caught exception: ' . $e->getMessage();
        }

        while ($rowapp = $resapp->fetchArray(SQLITE3_ASSOC)) {
            $lblgrp[] = $rowapp;
        
        }

        $_POST['oprnme'] = 'Disponível';
    }
    else if(isset($_POST['fr_dte']) && $_POST['fr_dte'] != 'mes atual'){
        $fr_dte = new DateTime($_POST['fr_dte']);
        $to_dte = new DateTime($_POST['to_dte']);
        $msg = 'Dispnibilidade dos operadores';
        $msg .= ' no intervalo '.$fr_dte->format('d/m/Y').' até '.$to_dte->format('d/m/Y');

        $selapp = "
            select
                user as user,
                nome as nome,
                sum(dispon) as aponta 
            from (
                    SELECT bd.days,
                            bd.user,
                            bd.nome,
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
                                        us.usr_id as user,
                                        us.usrnme as nome
                                FROM yr_idx dt
                                join usrsys us
                                left join 
                                funusr fu on (us.usr_id = fu.usr_id)
                                left join 
                                usrfun uf on (fu.fun_id = uf.fun_id)
                                WHERE dt.wk_day not in ('sábado', 'domingo') 
                                and  us.usr_id in (select usr_id from usrlog where logdte between :fr_adddte and :to_adddte)
                                and  (dt.yr_dte || '-' || dt.mn_dte || '-' || dt.dy_dte) in 
                                        (select logdte from usrlog where logdte between :fr_adddte and :to_adddte)
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
            GROUP BY
                user
        ";

        try {
            $cmdapp = $db->prepare($selapp);
            $cmdapp->bindValue('fr_adddte', $_POST['fr_dte']);
            $cmdapp->bindValue('to_adddte', $_POST['to_dte']);
            $resapp = $cmdapp->execute();
        } catch (Exception $e) {
            $sqlerr .= '<hr>Caught exception: ' . $e->getMessage();
        }

        while ($rowapp = $resapp->fetchArray(SQLITE3_ASSOC)) {
            $lblgrp[] = $rowapp;
           
        }

        $_POST['oprnme'] = 'Disponível';
    }
}
else{
    $msg = 'Erro inesperado, contate o administrador :(';
}


$soma_hora = 0;
foreach ($lblgrp as $rowsoma) {
    $soma_hora = $soma_hora + $rowsoma['aponta'];
}

foreach ($lblgrp as $row) {
    $pergrp[] = round($row['aponta'] / $soma_hora, 4) * 100;
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

foreach ($lblgrp as $key => $value) {
    $table .= '
                <tr>
                    <td scope="row">
                    
                        <div class="col">
                           ' . $value['user'] . '
                        </div>
                    </form>
                    </td>
                    <td>' . $pergrp[$key] . ' %</td>
                    <td>' . floor($value['aponta']) . 'h' . floor(($value['aponta'] - floor($value['aponta'])) * 60) . 'm</td>
                </tr>
    ';

}



$table .= '
                </tbody>
            </table>
        </div>
    </div>
    ';

?>

<div role="main" class="container-fluid">
    <div class="card">
        <div class="card-body">
            <form method="POST" action="dashboard-disponibilidade2.php">
                <div class="row">
                    <div class="col">
                        <input type="text" class="form-control-plaintext" value="Data Início:">
                    </div>
                    <div class="col">
                        <input type="date" class="form-control" placeholder="Data" id="fr_dte" name="fr_dte">
                    </div>
                    <div class="col">
                        <input type="text" class="form-control-plaintext" value="Data Final:">
                    </div>
                    <div class="col">
                        <input type="date" class="form-control" placeholder="Data" id="to_dte" name="to_dte">
                    </div>
                    <div class="col">
                        <input type="submit" class="btn btn-primary col-12" name="filtro" value="Filtrar">
                    </div>
                    <input type="hidden" name="oprnme" id="oprnme" value="<?php echo $_POST['oprnme'];?>">
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
            <div>
                <div class="chart-container">
                    <canvas id="myChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <br>
    <?php
        echo $table;
    ?>
</div>

<div>
    <?php
    include('../template/template-rodape.php');
    ?>
    <script>
        var ctx = document.getElementById('myChart').getContext('2d');
        var barChartData = {
            labels: ['<?php echo $_POST['oprnme'];?>'],
            datasets: [
            <?php
                foreach ($lblgrp as $key => $row){
                    echo "
                    {
                        label: '".$row['user']."',
                        backgroundColor: '".$color[$key]."',
                        borderWidth: 1,
                        data: [".$row['aponta']."],
                    }, 
                    ";
            }
            ?>
			]

		};
        var chart = new Chart(ctx, {
            // The type of chart we want to create
            type: 'bar',
            labels: '<?php echo $_POST['oprnme'];?>',
            // The data for our dataset
            data: barChartData,
            // Configuration options go here
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 3.3

            }
        });
    </script>
</div>