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

    $table = '';
    $msg = '';
    $final = '';
    $users = array();
    $dados_usr = array();
    $perc_fadiga = 0.146;
    $disponibilidade = '';
	$disponibilidade_total = 0;
	
	// checar se as variaveis foram definidas, ou seja, o que veio pelo post
    if(isset($_POST['filtro']) && isset($_POST['fr_adddte']) && isset($_POST['to_adddte']) ){
	
        if(!empty($_POST['fr_adddte']) && !empty($_POST['to_adddte'])){

			if(!empty($_POST['usr_id'])){
				// select qtd usuarios
				
            }
            else{
                $msg = 'Intervalo Pesquisado: ';
                $msg .= date("d/m/Y",strtotime($_POST['fr_adddte']));
                $msg .= ' até ';
                $msg .= date("d/m/Y",strtotime($_POST['to_adddte']));
                $msg .= $final;
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

                while($row = $resultado_usr->fetchArray(SQLITE3_ASSOC)){
                    $users[] = $row;
                    
                    $s_hrsdis = "
                    SELECT count(distinct ap_dte) as dias,
                        sum(shftim) as soma_horas
                    FROM yr_idx
                    WHERE yr_dte || '-' || mn_dte || '-' || dy_dte between :fr_adddte and :to_adddte
                    and wk_day not in ('sábado','domingo')
                    ";

                    $cmd_db_disp = $db->prepare($s_hrsdis);
                    $cmd_db_disp->bindValue('fr_adddte', $_fr_adddte);
                    $cmd_db_disp->bindValue('to_adddte', $_to_adddte);
                    $resultado_disp = $cmd_db_disp->execute();

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
                        $resultado_app = $cmd_db_app->execute();

                        $s_hr_indis = "
                        SELECT sum((julianday(to_logtim) - julianday(fr_logtim))*24) as hr_inds
                            FROM yr_idx 
                            left join usrind 
                            on ((yr_dte||'-'||mn_dte||'-'||dy_dte) = inddte) 
                            WHERE (yr_dte||'-'||mn_dte||'-'||dy_dte) between :fr_adddte and :to_adddte
                                and usr_id = :usr_id 
                            ORDER BY dy_dte asc,
                                    mn_dte asc
                        ";
                        $cmd_db_ind = $db->prepare($s_hr_indis);
                        $cmd_db_ind->bindValue('usr_id', $row['usuarios']);
                        $cmd_db_ind->bindValue('fr_adddte', $_fr_adddte);
                        $cmd_db_ind->bindValue('to_adddte', $_to_adddte);
                        $resultado_ind = $cmd_db_ind->execute();

                        while($rows = $resultado_disp->fetchArray(SQLITE3_ASSOC)){
                            $disponibilidade = $rows;
                        }

                        $indisponibilidade;
                        while($rows = $resultado_ind->fetchArray(SQLITE3_ASSOC)){
                            $indisponibilidade = $rows;
                        }

                        $apontamentos = 0;
                        while($rows = $resultado_app->fetchArray(SQLITE3_ASSOC)){
                           $apontamentos = $apontamentos + $rows['diff_jd'];
                        }
                        
                        $disponibilidade_total = (($disponibilidade['soma_horas']) * (1 - $perc_fadiga) - $indisponibilidade['hr_inds']* (1 - $perc_fadiga)) - $apontamentos;
                        /*
                        echo 'Disponibilidade: ';
                        echo $disponibilidade_total;
                        echo '<hr>';
                        */
                        $dados_usr[] = array(
                            "usuario" => $row['usuarios'],
                            "nome" => $row['nome'],
                            "disponibilidade" => $disponibilidade_total,
                        );
                }
                
            }
        }
        else {
           $msg = 'É necessário inserir data inicial e data final';
           $tamanho = 'style="height: 35em;"';
        }
    }
	//Mexi até aqui
    else if(isset($_POST['todos'])){
        
    }
    else{
       
    }
    $soma = 0;
    foreach($dados_usr as $rows){
        $labels[] = $rows['usuario'].' - '.$rows['nome'];
        $data[] = $rows['disponibilidade'];
    }

    //$data_perc_script = implode("', '",$data_perc);
	$labels_script = implode("', '",$labels);
    $data_script = implode(", ",$data);

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
    $soma_disponibilidade = 0;
    foreach($dados_usr as $rows){
        $soma_disponibilidade = $soma_disponibilidade + $rows['disponibilidade'];
    }
    foreach($dados_usr as $rows){
        $table .= '
                    <tr>
                        <td>'.$rows['usuario'].' - '.$rows['nome'].'</td>
                        <td>'.round( (($rows['disponibilidade']/$soma_disponibilidade)*100),2).' %</td>
                        <td>'.floor($rows['disponibilidade']).'h'.floor(($rows['disponibilidade'] -floor($rows['disponibilidade']))*60).'m</td>
                    </tr>';

    }
	
?>

<div role="main" class="container-fluid">
    <div class="card" >
        <div class="card-body" <?php print $tamanho;?> >
            <form method="POST" action="dashboard-disponibilidade.php">
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
                </div>
              
            </form>
            <div>     
                <?php
                    if(!empty($msg)){
                        print '<hr>'.$msg;
                    }
                ?>
            </div>
            <hr>
            <div >
                <div class="chart-container">
                    <canvas id="myChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <br>
    <?php print ''.$table.'';?>
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
        labels: ['<?php echo $labels_script;?>'],
        datasets: [{
            label: ['Disponibilidade'],
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
                'rgb(191, 192, 194)',
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
                'rgb(191, 192, 194)'],
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
                'rgb(191, 192, 194)',
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
            data: [<?php echo $data_script;?>]
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
