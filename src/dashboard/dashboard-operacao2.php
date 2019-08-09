<?php
include('../template/template-barra.php');
$db = new SQLite3('../sqlite/apontamentos.db');

$labels = array();
$data = array();
$data_perc = array();

$labels_script;
$data_script;

$soma_hora = 0;
$tamanho;

$_fr_adddte = '';
$_to_adddte = '';

$table = '';
$msg = '';
$final = '';
$users = 0;
$perc_fadiga = 0.146;
$disponibilidade = '';
$disponibilidade_total = 0;

// checar se as variaveis foram definidas, ou seja, o que veio pelo post
if (isset($_POST['filtro']) && isset($_POST['fr_adddte']) && isset($_POST['to_adddte'])) {

    if (!empty($_POST['fr_adddte']) && !empty($_POST['to_adddte'])) {

        if (!empty($_POST['usr_id'])) {
            $final = ' para o usuário: ' . $_POST['usr_id'];
            // select qtd usuarios
            $sel_dsp = "
					SELECT
						sum(shftim) as disponibilidade
					FROM
						(
						SELECT
							(yr_dte || '-' || mn_dte || '-' || dy_dte) as data,
							ul.usr_id,
							yi.shftim,
							uf.funidx
						FROM
							yr_idx yi
							inner join usrlog ul 
							on ((yr_dte || '-' || mn_dte || '-' || dy_dte) = ul.logdte)
							inner join funusr fu 
							on (ul.usr_id = fu.usr_id) 
							inner join usrfun uf 
							on (fu.fun_id = uf.fun_id)
						WHERE
							wk_day not in ('sábado', 'domingo')
							and ul.logdte between :fr_adddte and :to_adddte
							and ul.usr_id = :usr_id 
						GROUP BY
							(yr_dte || '-' || mn_dte || '-' || dy_dte),
							ul.usr_id,
							yi.shftim,
							uf.funidx
						UNION
						SELECT
							(yr_dte || '-' || mn_dte || '-' || dy_dte) as data,
							ui.usr_id,
							yi.shftim,
							uf.funidx
						FROM
							yr_idx yi
							inner join usrind ui
							on ((yr_dte || '-' || mn_dte || '-' || dy_dte) = ui.inddte)
							inner join funusr fu 
							on (ui.usr_id = fu.usr_id) 
							inner join usrfun uf 
							on (fu.fun_id = uf.fun_id)
						WHERE
							wk_day not in ('sábado', 'domingo')
							and ui.inddte between :fr_adddte and :to_adddte
							and ui.usr_id = :usr_id 
						GROUP BY
							(yr_dte || '-' || mn_dte || '-' || dy_dte),
							ui.usr_id,
							yi.shftim,
							uf.funidx
						)
					";
            $cmd_dsp = $db->prepare($sel_dsp);
            $cmd_dsp->bindValue('usr_id', $_POST['usr_id']);
            $res_dsp = $cmd_dsp->execute();


            $sel_app = "
				select 
					op.oprnme,
					count(distinct ul.usr_id),
					sum((julianday(to_logtim) - julianday(fr_logtim))*24 * 1) as diff_jd
				from 
					usrlog ul 
					inner join usrprd pr 
					on (ul.prd_id = pr.prd_id)
					inner join usropr op 
					on (ul.opr_id = op.opr_id) 
					inner join funusr fu 
					on (ul.usr_id = fu.usr_id) 
					inner join usrfun uf 
					on (fu.fun_id = uf.fun_id) 
				where 
					logdte between :fr_adddte and :to_adddte
					and ul.usr_id = :usr_id 
				group by
					op.oprnme
				order by 
					op.opr_id
				";
            $cmd_app = $db->prepare($sel_app);
            $cmd_app->bindValue('usr_id', $_POST['usr_id']);
            $res_app = $cmd_app->execute();

            $sel_ind = "
				SELECT IFNULL(sum((julianday(to_logtim) - julianday(fr_logtim))*24),0) * 1 as hr_inds
					from usrind ul
					 inner join funusr fu 
					on (ul.usr_id = fu.usr_id) 
					inner join usrfun uf 
					on (fu.fun_id = uf.fun_id) 
				where inddte between :fr_adddte and :to_adddte
				and ul.usr_id = :usr_id 
				";
            $cmd_ind = $db->prepare($sel_ind);
            $cmd_ind->bindValue('usr_id', $_POST['usr_id']);
            $res_ind = $cmd_ind->execute();
        } else {
            // select padrão do grafico do apontamento qtd usuarios
            $sel_dsp = "
                SELECT
                    sum(shftim) as disponibilidade
                FROM
                    (
                    SELECT
                        (yr_dte || '-' || mn_dte || '-' || dy_dte) as data,
                        ul.usr_id,
                        yi.shftim,
                        uf.funidx
                    FROM
                        yr_idx yi
                        inner join usrlog ul 
                        on ((yr_dte || '-' || mn_dte || '-' || dy_dte) = ul.logdte)
                        inner join funusr fu 
                        on (ul.usr_id = fu.usr_id) 
                        inner join usrfun uf 
                        on (fu.fun_id = uf.fun_id)
                    WHERE
                        wk_day not in ('sábado', 'domingo')
                        and ul.logdte between :fr_adddte and :to_adddte
                    GROUP BY
                        (yr_dte || '-' || mn_dte || '-' || dy_dte),
                        ul.usr_id,
                        yi.shftim,
                        uf.funidx
                    UNION
                    SELECT
                        (yr_dte || '-' || mn_dte || '-' || dy_dte) as data,
                        ui.usr_id,
                        yi.shftim,
                        uf.funidx
                    FROM
                        yr_idx yi
                        inner join usrind ui
                        on ((yr_dte || '-' || mn_dte || '-' || dy_dte) = ui.inddte)
                        inner join funusr fu 
                        on (ui.usr_id = fu.usr_id) 
                        inner join usrfun uf 
                        on (fu.fun_id = uf.fun_id)
                    WHERE
                        wk_day not in ('sábado', 'domingo')
                        and ui.inddte between :fr_adddte and :to_adddte
                    GROUP BY
                        (yr_dte || '-' || mn_dte || '-' || dy_dte),
                        ui.usr_id,
                        yi.shftim,
                        uf.funidx
                    )
					";
            $cmd_dsp = $db->prepare($sel_dsp);

            $sel_app = "
				select 
					op.oprnme,
					count(distinct ul.usr_id),
					sum((julianday(to_logtim) - julianday(fr_logtim))*24 * 1) as diff_jd
				from 
					usrlog ul 
					inner join usrprd pr 
					on (ul.prd_id = pr.prd_id)
					inner join usropr op 
					on (ul.opr_id = op.opr_id) 
					inner join funusr fu 
					on (ul.usr_id = fu.usr_id) 
					inner join usrfun uf 
					on (fu.fun_id = uf.fun_id) 
				where 
					logdte between :fr_adddte and :to_adddte
				group by
					op.oprnme
				order by 
					op.opr_id
				";
            $cmd_app = $db->prepare($sel_app);


            $sel_ind = "
				SELECT IFNULL(sum((julianday(to_logtim) - julianday(fr_logtim))*24),0) * 1 as hr_inds
					from usrind ul
					 inner join funusr fu 
					on (ul.usr_id = fu.usr_id) 
					inner join usrfun uf 
					on (fu.fun_id = uf.fun_id) 
				where inddte between :fr_adddte and :to_adddte
				";
            $cmd_ind = $db->prepare($sel_ind);
        }


        $cmd_dsp->bindValue('fr_adddte', $_POST['fr_adddte']);
        $cmd_dsp->bindValue('to_adddte', $_POST['to_adddte']);
        $res_dsp = $cmd_dsp->execute();

        $cmd_app->bindValue('fr_adddte', $_POST['fr_adddte']);
        $cmd_app->bindValue('to_adddte', $_POST['to_adddte']);
        $res_app = $cmd_app->execute();

        $cmd_ind->bindValue('fr_adddte', $_POST['fr_adddte']);
        $cmd_ind->bindValue('to_adddte', $_POST['to_adddte']);
        $res_ind = $cmd_ind->execute();


        while ($row = $res_dsp->fetchArray(SQLITE3_ASSOC)) {
            $disponibilidade = $row;
        }

        while ($row = $res_app->fetchArray(SQLITE3_ASSOC)) {
            $labels[] = $row['oprnme'];
            $data[] = $row['diff_jd'];
        }

        $indisponibilidade;
        while ($row = $res_ind->fetchArray(SQLITE3_ASSOC)) {
            $indisponibilidade = $row;
        }
        
        $disponibilidade_total = (($disponibilidade['disponibilidade']) * (1 - $perc_fadiga) - $indisponibilidade['hr_inds'] * (1 - $perc_fadiga));

        $msg = 'Intervalo Pesquisado: ';
        $msg .= date("d/m/Y", strtotime($_POST['fr_adddte']));
        $msg .= ' até ';
        $msg .= date("d/m/Y", strtotime($_POST['to_adddte']));
        $msg .= $final;
        $tamanho = 'style="height: 55em;"';

        $_fr_adddte = $_POST['fr_adddte'];
        $_to_adddte = $_POST['to_adddte'];
    } else {
        $msg = 'É necessário inserir data inicial e data final';
        $tamanho = 'style="height: 55em;"';
    }
} else if (isset($_POST['todos'])) {
    $sel_dsp = "
        SELECT
            sum(shftim) as disponibilidade
        FROM
            (
            SELECT
                (yr_dte || '-' || mn_dte || '-' || dy_dte) as data,
                ul.usr_id,
                yi.shftim,
                uf.funidx
            FROM
                yr_idx yi
                inner join usrlog ul 
                on ((yr_dte || '-' || mn_dte || '-' || dy_dte) = ul.logdte)
                inner join funusr fu 
                on (ul.usr_id = fu.usr_id) 
                inner join usrfun uf 
                on (fu.fun_id = uf.fun_id)
            WHERE
                wk_day not in ('sábado', 'domingo')
                
            GROUP BY
                (yr_dte || '-' || mn_dte || '-' || dy_dte),
                ul.usr_id,
                yi.shftim,
                uf.funidx
            UNION
            SELECT
                (yr_dte || '-' || mn_dte || '-' || dy_dte) as data,
                ui.usr_id,
                yi.shftim,
                uf.funidx
            FROM
                yr_idx yi
                inner join usrind ui
                on ((yr_dte || '-' || mn_dte || '-' || dy_dte) = ui.inddte)
                inner join funusr fu 
                on (ui.usr_id = fu.usr_id) 
                inner join usrfun uf 
                on (fu.fun_id = uf.fun_id)
            WHERE
                wk_day not in ('sábado', 'domingo')
            GROUP BY
                (yr_dte || '-' || mn_dte || '-' || dy_dte),
                ui.usr_id,
                yi.shftim,
                uf.funidx,
                (yi.shftim * uf.funidx)
            )
        ";
    $cmd_dsp = $db->prepare($sel_dsp);
    $res_dsp = $cmd_dsp->execute();

    while ($row = $res_dsp->fetchArray(SQLITE3_ASSOC)) {
        $disponibilidade = $row;
    }

    $sel_app = "
        select 
            op.oprnme,
            count(distinct ul.usr_id),
            sum((julianday(to_logtim) - julianday(fr_logtim))*24 * 1) as diff_jd
        from 
            usrlog ul 
            inner join usrprd pr 
            on (ul.prd_id = pr.prd_id)
            inner join usropr op 
            on (ul.opr_id = op.opr_id) 
            inner join funusr fu 
            on (ul.usr_id = fu.usr_id) 
            inner join usrfun uf 
            on (fu.fun_id = uf.fun_id)
        group by
            op.oprnme
        order by 
            op.opr_id
        ";

    $cmd_app = $db->prepare($sel_app);
    $res_app = $cmd_app->execute();

    while ($row = $res_app->fetchArray(SQLITE3_ASSOC)) {
        $labels[] = $row['oprnme'];
        $data[] = $row['diff_jd'];
    }

    $sel_ind = "
        SELECT IFNULL(sum((julianday(to_logtim) - julianday(fr_logtim))*24),0) * 1 as hr_inds
            from usrind ul
             inner join funusr fu 
            on (ul.usr_id = fu.usr_id) 
            inner join usrfun uf 
            on (fu.fun_id = uf.fun_id) 
        ";

    $cmd_ind = $db->prepare($sel_ind);
    $res_ind = $cmd_ind->execute();

    $indisponibilidade;
    while ($row = $res_ind->fetchArray(SQLITE3_ASSOC)) {
        $indisponibilidade = $row;
    }

    $disponibilidade_total = (($disponibilidade['disponibilidade']) * (1 - $perc_fadiga) - $indisponibilidade['hr_inds'] * (1 - $perc_fadiga));

    $tamanho = 'style="height: 50em;"';
} else {
    $sel_dsp = "
        SELECT
            sum(shftim) as disponibilidade
        FROM
            (
            SELECT
                (yr_dte || '-' || mn_dte || '-' || dy_dte) as data,
                ul.usr_id,
                yi.shftim,
                uf.funidx
            FROM
                yr_idx yi
                inner join usrlog ul 
                on ((yr_dte || '-' || mn_dte || '-' || dy_dte) = ul.logdte)
                inner join funusr fu 
                on (ul.usr_id = fu.usr_id) 
                inner join usrfun uf 
                on (fu.fun_id = uf.fun_id)
            WHERE
                wk_day not in ('sábado', 'domingo')
                and substr(ul.logdte, 0,8) = substr(date(),0,8)
            GROUP BY
                (yr_dte || '-' || mn_dte || '-' || dy_dte),
                ul.usr_id,
                yi.shftim,
                uf.funidx
            UNION
            SELECT
                (yr_dte || '-' || mn_dte || '-' || dy_dte) as data,
                ui.usr_id,
                yi.shftim,
                uf.funidx
            FROM
                yr_idx yi
                inner join usrind ui
                on ((yr_dte || '-' || mn_dte || '-' || dy_dte) = ui.inddte)
                inner join funusr fu 
                on (ui.usr_id = fu.usr_id) 
                inner join usrfun uf 
                on (fu.fun_id = uf.fun_id)
            WHERE
                wk_day not in ('sábado', 'domingo')
                and substr(ui.inddte, 0,8) = substr(date(),0,8)
            GROUP BY
                (yr_dte || '-' || mn_dte || '-' || dy_dte),
                ui.usr_id,
                yi.shftim,
                uf.funidx
            )
        ";
    $cmd_dsp = $db->prepare($sel_dsp);
    $res_dsp = $cmd_dsp->execute();

    while ($row = $res_dsp->fetchArray(SQLITE3_ASSOC)) {
        $disponibilidade = $row;
    }

    $sel_app = "
        select 
            op.oprnme,
            count(distinct ul.usr_id),
            sum((julianday(to_logtim) - julianday(fr_logtim))*24 * 1) as diff_jd
        from 
            usrlog ul 
            inner join usrprd pr 
            on (ul.prd_id = pr.prd_id)
            inner join usropr op 
            on (ul.opr_id = op.opr_id) 
            inner join funusr fu 
            on (ul.usr_id = fu.usr_id) 
            inner join usrfun uf 
            on (fu.fun_id = uf.fun_id) 
        where 
            logdte between (select 
                                min(logdte) 
                                from 
                                    usrlog 
                                where 
                                    substr(logdte, 0,8) = substr(date(),0,8)) 
            and (select 
                    max(logdte) 
                    from 
                        usrlog 
                    where 
                        substr(logdte, 0,8) = substr(date(),0,8))
        group by
            op.oprnme
        order by 
            op.opr_id
        ";

    $cmd_app = $db->prepare($sel_app);
    $res_app = $cmd_app->execute();

    while ($row = $res_app->fetchArray(SQLITE3_ASSOC)) {
        $labels[] = $row['oprnme'];
        $data[] = $row['diff_jd'];
    }

    $sel_ind = "
        SELECT IFNULL(sum((julianday(to_logtim) - julianday(fr_logtim))*24),0) * 1 as hr_inds
            from usrind ul
             inner join funusr fu 
            on (ul.usr_id = fu.usr_id) 
            inner join usrfun uf 
            on (fu.fun_id = uf.fun_id) 
        where inddte between (select min(logdte)
                                from usrlog where substr(logdte, 0,8) = substr(date(),0,8))
                        and (select max(logdte)
                        from usrlog where substr(logdte, 0,8) = substr(date(),0,8));
        ";

    $cmd_ind = $db->prepare($sel_ind);
    $res_ind = $cmd_ind->execute();

    $indisponibilidade;
    while ($row = $res_ind->fetchArray(SQLITE3_ASSOC)) {
        $indisponibilidade = $row;
    }

    $disponibilidade_total = (($disponibilidade['disponibilidade']) * (1 - $perc_fadiga) - $indisponibilidade['hr_inds'] * (1 - $perc_fadiga));

    $msg = 'Intervalo de dados: ';
    $msg .= date("m/Y");
    $s_datas = "
        SELECT min(yr_dte || '-' || mn_dte || '-' || dy_dte) as fr_dte, 
        max(yr_dte || '-' || mn_dte || '-' || dy_dte) as to_dte
        FROM yr_idx
        WHERE yr_dte || '-' || mn_dte || '-' || dy_dte between (select min(logdte)
                                                                from usrlog where substr(logdte, 0,8) = substr(date(),0,8))
        and (select max(logdte)
                from usrlog where substr(logdte, 0,8) = substr(date(),0,8))
        and wk_day not in ('sábado', 'domingo')";

    $cmd_db_datas = $db->prepare($s_datas);
    $resultado_data = $cmd_db_datas->execute();
    while ($row = $resultado_data->fetchArray(SQLITE3_ASSOC)) {
        $_fr_adddte = $row['fr_dte'];
        $_to_adddte = $row['to_dte'];
    }


    $tamanho = 'style="height: 55em;"';
}

$soma_hora = $disponibilidade_total;
foreach ($data as $rowsoma) {
    $soma_hora = $soma_hora - $rowsoma;
}

foreach ($data as $row) {
    $data_perc[] = round($row / $disponibilidade_total, 4) * 100;
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

foreach ($data as $key => $value) {
    $table .= '
                    <tr>
                        <td scope="row">
                        <form method="POST" action="dashboard-detalhamento.php">
                            <div class="col">
                                <input type="submit" class="btn btn-light" name="filtro" value="' . $labels[$key] . '">
                                <input type="hidden" id="fr_adddte" name="fr_adddte" value="' . $_fr_adddte . '">
                                <input type="hidden" id="to_adddte" name="to_adddte" value="' . $_to_adddte . '">
                                <input type="hidden" id="oprnme" name="oprnme" value="' . $labels[$key] . '">
                            </div>
                        </form>
                        </td>
                        <td>' . $data_perc[$key] . ' %</td>
                        <td>' . floor($value) . 'h' . floor(($value - floor($value)) * 60) . 'm</td>
                    </tr>
        ';
}

if ($disponibilidade_total <> 0) {
    $table .= '
            <tr>
                <td scope="row">
                <form method="POST" action="dashboard-disponibilidade.php">
                    <div class="col">
                        <input type="submit" class="btn btn-light" name="filtro" value="Disponível">
                        <input type="hidden" id="fr_adddte" name="fr_adddte" value="' . $_fr_adddte . '">
                        <input type="hidden" id="to_adddte" name="to_adddte" value="' . $_to_adddte . '">
                    </div>
                </form>
                </td>
                <td>' . (round($soma_hora / $disponibilidade_total, 4) * 100) . ' %</td>
                <td>' . floor($soma_hora) . 'h' . floor(($soma_hora - floor($soma_hora)) * 60) . 'm</td>
            </tr>
        ';
}

$teste = '
    <form method="POST" action="dashboard-operacoes.php">
        <div class="col">
            <input type="submit" class="btn btn-primary col-12" name="filtro" value="Disponível">
            <input type="hidden" id="custId" name="fr_adddte" value="' . $_fr_adddte . '">
            <input type="hidden" id="custId" name="to_adddte" value="' . $_to_adddte . '">
        </div>
    </form>';


$table .= '
                </tbody>
            </table>
        </div>
    </div>
    ';

$data_perc_script = implode("', '", $data_perc);
$labels_script = implode("', '", $labels);
$data_script = implode("', '", $data);

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
            <div>
                <div class="chart-container">
                    <canvas id="myChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <br>
    <?php print '' . $table . ''; ?>
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
                labels: ['Disponível', '<?php print $labels_script; ?> '],
                datasets: [{
                    label: 'Operações',
                    backgroundColor: [
                        'rgb( 50, 50, 50)',
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
                    data: ['<?php print(round($soma_hora / $disponibilidade_total, 4) * 100); ?> ',
                        ' <?php print $data_perc_script; ?> '
                    ]
                }]
            },

            // Configuration options go here
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 2.5
            }
        });
    </script>
</div>