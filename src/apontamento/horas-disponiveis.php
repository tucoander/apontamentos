<?php
    /* barra navegação */
    include('../template/template-barra.php');
    $db = new SQLite3('../sqlite/apontamentos.db');
    /* variaveis */
    $labels = array();
    $data = array();
    $data_perc = array();

    $labels_script;
    $data_script;

    $soma_hora = 0;
    $tamanho;

    $table = '';
    $msg = '';
    $final = '';

    $perc_fadiga = 0.146;

    /* area de testes */
    echo '
    <div class="container">
    ';
    // select padrão do grafico do apontamento qtd usuarios
    $s_tbllogusr = "
    SELECT 
    count( distinct(ul.usr_id)) as usuarios
    FROM usrlog ul
        ";
    // execucao da query padrao
    $cmd_db = $db->prepare($s_tbllogusr);

    $resultado_usr = $cmd_db->execute();
    $users = 0;
    // armazena os labels separados dos resultados para jogar no grafico
    while($row = $resultado_usr->fetchArray(SQLITE3_ASSOC)){
        $users = $row;
    }
    echo '
        <div class="alert alert-secondary" role="alert">
        Quantidade de usuários: '.$users["usuarios"].'
        </div>';

    // select padrão do grafico do apontamento
    $s_tbllog = "
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
        group by
        uo.opr_id,
            uo.oprnme
        order by 
            ul.opr_id asc
        ";
    // execucao da query padrao
    $cmd_db = $db->prepare($s_tbllog);

    $resultado = $cmd_db->execute();
    // armazena os labels separados dos resultados para jogar no grafico
    while($row = $resultado->fetchArray(SQLITE3_ASSOC)){
        $labels[] = $row['oprnme'];
        $data[] = $row['diff_jd'];
    }

    // select da disponibilidade 
    $s_tblpar = "
    SELECT count(distinct ap_dte) as dias,
            sum(shftim) as soma_horas,
            comeco,
            fim
    FROM yr_idx
    inner
    join (SELECT count(distinct ul.usr_id),
                    min(ul.logdte) as comeco,
                    max(ul.logdte) as fim
            FROM usrlog ul
            inner
            join usrprd up
                on (ul.prd_id = up.prd_id)
            inner
            join usrcty uc
                on (ul.cty_id = uc.cty_id)
            inner
            join usropr uo
                on (ul.opr_id = uo.opr_id)) extremos
        on (yr_dte || '-' || mn_dte || '-' || dy_dte >= extremos.comeco and yr_dte || '-' || mn_dte || '-' || dy_dte <= extremos.fim)
    WHERE wk_day not in ('sábado', 'domingo')
    ";

   
   // execucao da query de disponibilidade
    $cmd_db = $db->prepare($s_tblpar);
    
    $resultado_disponibilidade = $cmd_db->execute();
    echo '
        <div class="alert alert-secondary" role="alert">';
    while($row = $resultado_disponibilidade->fetchArray(SQLITE3_ASSOC)){
        $disponibilidade = $row;
    }
    print_r($disponibilidade);
    echo'
        </div>
    ';
    $soma = 0;
    $disponibilidade_total = ($disponibilidade['soma_horas'] * $users["usuarios"]) * (1 - $perc_fadiga);
    echo '
    <div class="alert alert-secondary" role="alert">';
    foreach($data as $key=>$value){
        $soma = $soma + $value;
    }
    echo 'Soma : ';
    echo $soma;
    echo '<br>';
    echo 'Disponível: ';
    echo $disponibilidade_total ;
    echo'
    </div>
    ';
   

    $tamanho = 'style="height: 50em;"';

    $table = '
    <div class="card" >
        <div class="card-body">
            <table class="table" id="apontamento">
                <thead>
                    <tr>
                    <th scope="col">Operação</th>
                    <th scope="col">Percentual</th>
                    <th scope="col">Horas</th>
                </thead>
                <tbody>
    ';
    $soma_hora = $disponibilidade_total;
    foreach($data as $rowsoma){
        $soma_hora = $soma_hora - $rowsoma;
    }

    foreach($data as $row){
        $data_perc[] = round($row / $disponibilidade_total,4) * 100;
    }

    foreach($data as $key=>$value){
        $table .= '
                    <tr>
                        <td scope="row">'.$labels[$key].'</td>
                        <td>'.$data_perc[$key].' %</td>
                        <td>'.floor($value).'h'.floor(($value-floor($value))*60).'m</td>
                    <tr>
        ';

    }
    $table .= '
                    <tr>
                        <td scope="row">Disponível</td>
                        <td>'.(round($soma_hora / $disponibilidade_total,4) * 100).' %</td>
                        <td>'.floor($soma_hora).'h'.floor(($soma_hora-floor($soma_hora))*60).'m</td>
                    <tr>
        ';
    $table .= '
                </tbody>
        </div>
    </div>
    ';
    echo $table;
    $data_perc_script = implode("', '",$data_perc);
	$labels_script = implode("', '",$labels);
    $data_script = implode("', '",$data);

    echo '
    <div class="chart-container">
        <canvas id="myChart"></canvas>
    </div>
    ';
    

    echo '
    </div> <!-- fim do container -->
    ';
    include('../template/template-rodape.php');
?>
<script>
    var ctx = document.getElementById('myChart').getContext('2d');
    var chart = new Chart(ctx, {
        type: 'doughnut',
		responsive: true,
        data: {
        labels: ['Disponível','<?php print $labels_script;?> '],
        datasets: [{
            label: 'Operações',
            backgroundColor: [
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
            data: ['<?php print (round($soma_hora / $disponibilidade_total,4) * 100);?> ',' <?php print $data_perc_script;?> ']
        }]
    },

    // Configuration options go here
    options: {
        layout: {
            padding: {
                responsive: true
            }
        }
    }
    });
</script>