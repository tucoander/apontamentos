<?php
include('../template/template-barra.php');
$db = new SQLite3('../sqlite/apontamentos.db');

include('./dashboard-ponderada-vars.php');

// checar se as variaveis foram definidas, ou seja, o que veio pelo post
if (isset($_POST['filtro']) && isset($_POST['fr_adddte']) && isset($_POST['to_adddte'])) {

    if (!empty($_POST['fr_adddte']) && !empty($_POST['to_adddte'])) {

        if (!empty($_POST['usr_id'])) {
            
            $final = ' para o usuário: ' . $_POST['usr_id'];
            
            $cmd_dsp = $db->prepare($sel_dsp_1);
            $cmd_dsp->bindValue('usr_id', $_POST['usr_id']);
            $cmd_dsp->bindValue('fr_adddte', $_POST['fr_adddte']);
            $cmd_dsp->bindValue('to_adddte', $_POST['to_adddte']);
            $res_dsp = $cmd_dsp->execute();

            $cmd_app = $db->prepare($sel_app_1);
            $cmd_app->bindValue('usr_id', $_POST['usr_id']);
            $cmd_app->bindValue('fr_adddte', $_POST['fr_adddte']);
            $cmd_app->bindValue('to_adddte', $_POST['to_adddte']);
            $res_app = $cmd_app->execute();

            
            $cmd_ind = $db->prepare($sel_ind_1);
            $cmd_ind->bindValue('usr_id', $_POST['usr_id']);
            $cmd_ind->bindValue('fr_adddte', $_POST['fr_adddte']);
            $cmd_ind->bindValue('to_adddte', $_POST['to_adddte']);
            $res_ind = $cmd_ind->execute();

        } else {
            $cmd_dsp = $db->prepare($sel_dsp_2);
            $cmd_dsp->bindValue('fr_adddte', $_POST['fr_adddte']);
            $cmd_dsp->bindValue('to_adddte', $_POST['to_adddte']);
            $res_dsp = $cmd_dsp->execute();

            $cmd_app = $db->prepare($sel_app_2);
            $cmd_app->bindValue('fr_adddte', $_POST['fr_adddte']);
            $cmd_app->bindValue('to_adddte', $_POST['to_adddte']);
            $res_app = $cmd_app->execute();

            $cmd_ind = $db->prepare($sel_ind_2);
            $cmd_ind->bindValue('fr_adddte', $_POST['fr_adddte']);
            $cmd_ind->bindValue('to_adddte', $_POST['to_adddte']);
            $res_ind = $cmd_ind->execute();
        }

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
        
        $disponibilidade_total = (($disponibilidade['dispon_index']) * (1 - $perc_fadiga) - $indisponibilidade['hr_inds'] * (1 - $perc_fadiga));

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
    
    $cmd_dsp = $db->prepare($sel_dsp_3);
    $res_dsp = $cmd_dsp->execute();

    $cmd_app = $db->prepare($sel_app_3);
    $res_app = $cmd_app->execute();

    $cmd_ind = $db->prepare($sel_ind_3);
    $res_ind = $cmd_ind->execute();

    while ($row = $res_app->fetchArray(SQLITE3_ASSOC)) {
        $labels[] = $row['oprnme'];
        $data[] = $row['diff_jd'];
    }

    while ($row = $res_dsp->fetchArray(SQLITE3_ASSOC)) {
        $disponibilidade = $row;
    }

    $indisponibilidade;
    while ($row = $res_ind->fetchArray(SQLITE3_ASSOC)) {
        $indisponibilidade = $row;
    }

    $disponibilidade_total = (($disponibilidade['dispon_index']) * (1 - $perc_fadiga) - $indisponibilidade['hr_inds'] * (1 - $perc_fadiga));

    $tamanho = 'style="height: 50em;"';

} else {
    
    $cmd_dsp = $db->prepare($sel_dsp_4);
    $res_dsp = $cmd_dsp->execute();

    $cmd_app = $db->prepare($sel_app_4);
    $res_app = $cmd_app->execute();

    $cmd_ind = $db->prepare($sel_ind_4);
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

    $disponibilidade_total = (($disponibilidade['dispon_index']) * (1 - $perc_fadiga) - $indisponibilidade['hr_inds'] * (1 - $perc_fadiga));

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