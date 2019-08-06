<?php
include('../template/template-barra.php');
$db = new SQLite3('../sqlite/apontamentos.db');
/* Variáveis  */
$retorno = ''; // testes da tela
$tamanho = 0; // tamanho do card principal
$msg = ''; // mensagem de retorno do usuário

if (isset($_POST['oprnme']) && isset($_POST['fr_adddte']) && isset($_POST['to_adddte'])) {
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
        '; // detalhamentos em forma de tabela

    $msg = 'Intervalo Pesquisado: ';
    $msg .= date("d/m/Y", strtotime($_POST['fr_adddte']));
    $msg .= ' até ';
    $msg .= date("d/m/Y", strtotime($_POST['to_adddte']));
    $msg .= ' para a operação ';
    $msg .= $_POST['oprnme'];

    $tamanho = 'style="height: 40em;"';

    $s_qtdusr = "
        SELECT 
            ul.usr_id as usuarios,
            us.usrnme as nome
        FROM usrlog ul left join 
            usrsys us on (ul.usr_id = us.usr_id)
        where ul.logdte between :fr_adddte and :to_adddte and ul.usr_id is not null
        GROUP BY ul.usr_id,
        us.usrnme
            ";

    $cmd_db_usr = $db->prepare($s_qtdusr);
    $_fr_adddte = $_POST['fr_adddte'];
    $_to_adddte = $_POST['to_adddte'];
    $cmd_db_usr->bindValue('fr_adddte', $_fr_adddte);
    $cmd_db_usr->bindValue('to_adddte', $_to_adddte);
    $resultado_usr = $cmd_db_usr->execute();

    while ($row = $resultado_usr->fetchArray(SQLITE3_ASSOC)) {
        $s_hrsapp = "
            SELECT 
            uo.opr_id,
            uo.oprnme,
            sum((julianday(to_logtim) - julianday(fr_logtim))*24) as diff_jd
            FROM usrlog ul
                inner join  usrprd up 
                on (ul.prd_id = up.prd_id)
                inner join usrcty uc
                on (ul.cty_id = uc.cty_id)
                inner join usropr uo
                on (ul.opr_id = uo.opr_id)
            where ul.logdte between :fr_adddte and :to_adddte
            and ul.usr_id = :usr_id
            and uo.oprnme = :oprnme
            group by
            uo.opr_id,
                uo.oprnme
            order by 
                ul.opr_id asc
            ";
        $cmd_db_app = $db->prepare($s_hrsapp);
        $cmd_db_app->bindValue('usr_id', $row['usuarios']);
        $cmd_db_app->bindValue('fr_adddte', $_fr_adddte);
        $cmd_db_app->bindValue('to_adddte', $_to_adddte);
        $cmd_db_app->bindValue('oprnme', $_POST['oprnme']);
        $resultado_app = $cmd_db_app->execute();
        $apontamento = 0;
        while ($rows = $resultado_app->fetchArray(SQLITE3_ASSOC)) {
            $apontamento = $rows['diff_jd'];
        }

        $dados_usr[] = array(
            "usuario" => $row['usuarios'],
            "nome" => $row['nome'],
            "apontamento" => $apontamento
        );
    }
    foreach ($dados_usr as $rows) {
        $labels[] = $rows['usuario'] . ' - ' . $rows['nome'];
        $data[] = round($rows['apontamento'], 2);
    }

    $labels_script = implode("', '", $labels);
    $data_script = implode(", ", $data);

    $soma_disponibilidade = 0;
    foreach ($dados_usr as $rows) {
        $soma_disponibilidade = $soma_disponibilidade + $rows['apontamento'];
    }
    foreach ($dados_usr as $rows) {
        $table .= '
                        <tr>
                            <td>' . $rows['usuario'] . ' - ' . $rows['nome'] . '</td>
                            <td>' . round((($rows['apontamento'] / $soma_disponibilidade) * 100), 2) . ' %</td>
                            <td>' . floor($rows['apontamento']) . 'h' . floor(($rows['apontamento'] - floor($rows['apontamento'])) * 60) . 'm</td>
                        </tr>';
    }

    $table .= '
            </tbody>
        </table>
        ';
} else {
    $msg = 'Erro no envio de parâmetros.';
}


?>

<div role="main" class="container-fluid">
    <div class="card">
        <div class="card-body" <?php print $tamanho; ?>>
            <form method="POST" action="dashboard-detalhamento.php">
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
                        <input type="hidden" id="oprnme" name="oprnme" value="<?php echo $_POST['oprnme']; ?>">
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
                    <?php echo $retorno; ?>
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
        // The type of chart we want to create
        type: 'bar',
        // The data for our dataset
        data: {
            labels: ['<?php echo $labels_script; ?>'],
            datasets: [{
                label: ['<?php echo $_POST['
                        oprnme ']; ?>'],
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
                borderColor: [
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
                data: [ < ? php echo $data_script; ? > ]
            }]
        },

        // Configuration options go here
        options: {
            responsive: true,
            maintainAspectRatio: true,
            aspectRatio: 3.3

        }
    });
    </script>
</div>