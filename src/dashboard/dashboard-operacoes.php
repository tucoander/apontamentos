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
    if(isset($_POST['filtro']) && isset($_POST['fr_adddte']) && isset($_POST['to_adddte']) ){
	
        if(!empty($_POST['fr_adddte']) && !empty($_POST['to_adddte'])){

			if(!empty($_POST['usr_id'])){
				
				// select qtd usuarios
				$s_qtdusr = "
				SELECT 
					count( distinct(ul.usr_id)) as usuarios
				FROM usrlog ul
				where ul.logdte between :fr_adddte and :to_adddte
					and ul.usr_id = :usr_id 
					";
				
				$cmd_db_usr = $db->prepare($s_qtdusr);
                $cmd_db_usr->bindValue('usr_id', $_POST['usr_id']);
				
				$s_hrsdis = "
				SELECT count(distinct ap_dte) as dias,
					sum(shftim) as soma_horas
				FROM yr_idx
                WHERE yr_dte || '-' || mn_dte || '-' || dy_dte between :fr_adddte and :to_adddte
                and wk_day not in ('sábado','domingo')
                ";
                
                $cmd_db_dsp = $db->prepare($s_hrsdis);
                $cmd_db_dsp->bindValue('usr_id', $_POST['usr_id']);  
				
				
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
                $final = ' para o usuário: '.$_POST['usr_id'];
				$cmd_db = $db->prepare($s_hrsapp);
				$cmd_db->bindValue('usr_id', $_POST['usr_id']);
				
				
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
                $cmd_db_ind->bindValue('usr_id', $_POST['usr_id']);
				
			}else{
				// select padrão do grafico do apontamento qtd usuarios
				$s_qtdusr = "
				SELECT 
					count( distinct(ul.usr_id)) as usuarios
				FROM usrlog ul
				where ul.logdte between :fr_adddte and :to_adddte
					";
				$cmd_db_usr = $db->prepare($s_qtdusr);
                
				$s_hrsdis = "
				SELECT count(distinct ap_dte) as dias,
					sum(shftim) as soma_horas
				FROM yr_idx
                WHERE yr_dte || '-' || mn_dte || '-' || dy_dte between :fr_adddte and :to_adddte
                and wk_day not in ('sábado','domingo')
                ";
				$cmd_db_dsp = $db->prepare($s_hrsdis);
				
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
                group by
                uo.opr_id,
                    uo.oprnme
                order by 
                    ul.opr_id asc
                ";
                $final = '';
                $cmd_db = $db->prepare($s_hrsapp);
				
				$s_hr_indis = "
				SELECT sum((julianday(to_logtim) - julianday(fr_logtim))*24) as hr_inds
					FROM yr_idx 
					left join usrind 
					on ((yr_dte||'-'||mn_dte||'-'||dy_dte) = inddte) 
					WHERE (yr_dte||'-'||mn_dte||'-'||dy_dte) between :fr_adddte and :to_adddte
					ORDER BY dy_dte asc,
							mn_dte asc
				";
				$cmd_db_ind = $db->prepare($s_hr_indis);
			}
            
            $_fr_adddte = $_POST['fr_adddte'];
            $_to_adddte = $_POST['to_adddte'];

            
            $cmd_db_usr->bindValue('fr_adddte', $_fr_adddte);
            $cmd_db_usr->bindValue('to_adddte', $_to_adddte);
			
			$cmd_db_dsp->bindValue('fr_adddte', $_fr_adddte);
            $cmd_db_dsp->bindValue('to_adddte', $_to_adddte);
			
			$cmd_db->bindValue('fr_adddte', $_fr_adddte);
            $cmd_db->bindValue('to_adddte', $_to_adddte);
			
			$cmd_db_ind->bindValue('fr_adddte', $_fr_adddte);
            $cmd_db_ind->bindValue('to_adddte', $_to_adddte);
			
			$resultado_usr = $cmd_db_usr->execute();
			$resultado_disponibilidade = $cmd_db_dsp->execute();
            $resultado = $cmd_db->execute();
			$resultado_ind = $cmd_db_ind->execute();
           
            while($row = $resultado->fetchArray(SQLITE3_ASSOC)){
                $labels[] = $row['oprnme'];
                $data[] = $row['diff_jd'];
                
            }
            
			while($row = $resultado_disponibilidade->fetchArray(SQLITE3_ASSOC)){
                $disponibilidade = $row;
                
			}
			
			// armazena os labels separados dos resultados para jogar no grafico
			while($row = $resultado_usr->fetchArray(SQLITE3_ASSOC)){
                $users = $row;
                
            }
            
			$indisponibilidade;
			while($row = $resultado_ind->fetchArray(SQLITE3_ASSOC)){
                $indisponibilidade = $row;
                
			}

            $msg = 'Intervalo Pesquisado: ';
            $msg .= date("d/m/Y",strtotime($_POST['fr_adddte']));
            $msg .= ' até ';
            $msg .= date("d/m/Y",strtotime($_POST['to_adddte']));
            $msg .= $final;
            $tamanho = 'style="height: 55em;"';
			
            $disponibilidade_total = (($disponibilidade['soma_horas'] * $users["usuarios"]) * (1 - $perc_fadiga) - $indisponibilidade['hr_inds']* (1 - $perc_fadiga));
            
        }
        else {
           $msg = 'É necessário inserir data inicial e data final';
           $tamanho = 'style="height: 55em;"';
        }
    }
	//Mexi até aqui
    else if(isset($_POST['todos'])){
        $s_qtdusr = "
        SELECT 
            count( distinct(ul.usr_id)) as usuarios
        FROM usrlog ul
            ";
        $cmd_db_usr = $db->prepare($s_qtdusr);
        
        $s_hrsdis = "
        SELECT count(distinct ap_dte) as dias,
                sum(shftim) as soma_horas
        FROM yr_idx
        WHERE yr_dte || '-' || mn_dte || '-' || dy_dte between (select min(logdte)
                                                                from usrlog)
            and (select max(logdte)
                from usrlog)
            and wk_day not in ('sábado', 'domingo')";

        $cmd_db_dsp = $db->prepare($s_hrsdis);

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
        group by
        uo.opr_id,
            uo.oprnme
        order by 
            ul.opr_id asc
        ";

        $s_hr_indis = "
        SELECT sum((julianday(to_logtim) - julianday(fr_logtim))*24) as hr_inds
            FROM yr_idx 
            left join usrind 
            on ((yr_dte||'-'||mn_dte||'-'||dy_dte) = inddte) 
            ORDER BY dy_dte asc,
                    mn_dte asc
        ";
       
                
        $_usr_id = $_SESSION['usr_id'];
        
        $cmd_db = $db->prepare($s_hrsapp);
        $resultado = $cmd_db->execute();

        $cmd_db_usr = $db->prepare($s_qtdusr);
        $resultado_usr = $cmd_db_usr->execute();

        $cmd_db_dsp = $db->prepare($s_hrsdis);
        $resultado_disponibilidade = $cmd_db_dsp->execute();

        $cmd_db_ind = $db->prepare($s_hr_indis);
        $resultado_ind = $cmd_db_ind->execute();



        while($row = $resultado->fetchArray(SQLITE3_ASSOC)){
            $labels[] = $row['oprnme'];
            $data[] = $row['diff_jd'];
            
        }
        
        while($row = $resultado_disponibilidade->fetchArray(SQLITE3_ASSOC)){
            $disponibilidade = $row;
            
        }
        
        while($row = $resultado_usr->fetchArray(SQLITE3_ASSOC)){
            $users = $row;
            
        }

         
        $indisponibilidade;
        while($row = $resultado_ind->fetchArray(SQLITE3_ASSOC)){
            $indisponibilidade = $row;
            
        }

        $disponibilidade_total = (($disponibilidade['soma_horas'] * $users["usuarios"]) * (1 - $perc_fadiga) - $indisponibilidade['hr_inds']* (1 - $perc_fadiga));
            
        $tamanho = 'style="height: 50em;"';
    }
    else{
        $s_qtdusr = "
        SELECT 
            count( distinct(ul.usr_id)) as usuarios
            FROM 
                    usrlog ul
            WHERE 
                    substr(logdte, 0,8) = substr(date(),0,8)
            ";

        $cmd_db_usr = $db->prepare($s_qtdusr);
        
        $s_hrsdis = "
        SELECT count(distinct ap_dte) as dias,
                sum(shftim) as soma_horas
        FROM yr_idx
        WHERE yr_dte || '-' || mn_dte || '-' || dy_dte between (select min(logdte)
                                                                from usrlog where substr(logdte, 0,8) = substr(date(),0,8))
            and (select max(logdte)
                from usrlog where substr(logdte, 0,8) = substr(date(),0,8))
            and wk_day not in ('sábado', 'domingo')";
        $cmd_db_dsp = $db->prepare($s_hrsdis);

 

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
        where substr(ul.logdte, 0,8) = substr(date(),0,8)
        group by
            uo.opr_id,
            uo.oprnme
        order by 
            ul.opr_id asc
        ";

        $s_hr_indis = "
        SELECT IFNULL(sum((julianday(to_logtim) - julianday(fr_logtim))*24),0) as hr_inds
            from usrind 
        where inddte between (select min(logdte)
                                from usrlog where substr(logdte, 0,8) = substr(date(),0,8))
                        and (select max(logdte)
                        from usrlog where substr(logdte, 0,8) = substr(date(),0,8));
        ";

        $_usr_id = $_SESSION['usr_id'];
        
        $cmd_db = $db->prepare($s_hrsapp);
        $resultado = $cmd_db->execute();

        $cmd_db_usr = $db->prepare($s_qtdusr);
        $resultado_usr = $cmd_db_usr->execute();

        $cmd_db_dsp = $db->prepare($s_hrsdis);
        $resultado_disponibilidade = $cmd_db_dsp->execute();

        $cmd_db_ind = $db->prepare($s_hr_indis);
        $resultado_ind = $cmd_db_ind->execute();


        while($row = $resultado->fetchArray(SQLITE3_ASSOC)){
            $labels[] = $row['oprnme'];
            $data[] = $row['diff_jd'];
            
        }
        
        while($row = $resultado_disponibilidade->fetchArray(SQLITE3_ASSOC)){
            $disponibilidade = $row;
            
        }
        
        while($row = $resultado_usr->fetchArray(SQLITE3_ASSOC)){
            $users = $row;
            
        }

        $indisponibilidade;
        while($row = $resultado_ind->fetchArray(SQLITE3_ASSOC)){
            $indisponibilidade = $row;
            
        }

        $disponibilidade_total = (($disponibilidade['soma_horas'] * $users["usuarios"]) * (1 - $perc_fadiga) - $indisponibilidade['hr_inds']* (1 - $perc_fadiga));
        
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
        while($row = $resultado_data->fetchArray(SQLITE3_ASSOC)){
            $_fr_adddte = $row['fr_dte'];
            $_to_adddte = $row['to_dte'];
            
        }

        
        $tamanho = 'style="height: 55em;"';
    }
    
    $soma_hora = $disponibilidade_total;
    foreach($data as $rowsoma){
        $soma_hora = $soma_hora - $rowsoma;
    }

    foreach($data as $row){
        $data_perc[] = round($row / $disponibilidade_total,4) * 100;
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

    foreach($data as $key=>$value){
        $table .= '
                    <tr>
                        <td scope="row">
                        <form method="POST" action="dashboard-detalhamento.php">
                            <div class="col">
                                <input type="submit" class="btn btn-light" name="filtro" value="'.$labels[$key].'">
                                <input type="hidden" id="fr_adddte" name="fr_adddte" value="'.$_fr_adddte.'">
                                <input type="hidden" id="to_adddte" name="to_adddte" value="'.$_to_adddte.'">
                                <input type="hidden" id="oprnme" name="oprnme" value="'.$labels[$key].'">
                            </div>
                        </form>
                        </td>
                        <td>'.$data_perc[$key].' %</td>
                        <td>'.floor($value).'h'.floor(($value-floor($value))*60).'m</td>
                    </tr>
        ';

    }

    if($disponibilidade_total <> 0){
        $table .= '
            <tr>
                <td scope="row">
                <form method="POST" action="dashboard-disponibilidade.php">
                    <div class="col">
                        <input type="submit" class="btn btn-light" name="filtro" value="Disponível">
                        <input type="hidden" id="fr_adddte" name="fr_adddte" value="'.$_fr_adddte.'">
                        <input type="hidden" id="to_adddte" name="to_adddte" value="'.$_to_adddte.'">
                    </div>
                </form>
                </td>
                <td>'.(round($soma_hora / $disponibilidade_total,4) * 100).' %</td>
                <td>'.floor($soma_hora).'h'.floor(($soma_hora-floor($soma_hora))*60).'m</td>
            </tr>
        ';
    }
    
    $teste = '
    <form method="POST" action="dashboard-operacoes.php">
        <div class="col">
            <input type="submit" class="btn btn-primary col-12" name="filtro" value="Disponível">
            <input type="hidden" id="custId" name="fr_adddte" value="'.$_fr_adddte.'">
            <input type="hidden" id="custId" name="to_adddte" value="'.$_to_adddte.'">
        </div>
    </form>';
    
    
    $table .= '
                </tbody>
            </table>
        </div>
    </div>
    ';

    $data_perc_script = implode("', '",$data_perc);
	$labels_script = implode("', '",$labels);
    $data_script = implode("', '",$data);
	
?>

<div role="main" class="container-fluid">
    <div class="card">
        <div class="card-body" <?php print $tamanho;?>>
            <form method="POST" action="dashboard-operacoes.php">
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
                                while($row = $resultado->fetchArray(SQLITE3_ASSOC)){
                                    print '
                                    <option value="'.$row["usr_id"].'">'.$row["usr_id"].' - '.$row["usrnme"].'</option>
                                    ';
                                }
                            ?>
                        </select>

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
            <div>
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
        type: 'doughnut',
        responsive: true,
        data: {
            labels: ['Disponível', '<?php print $labels_script;?> '],
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
                data: ['<?php print (round($soma_hora / $disponibilidade_total,4) * 100);?> ',
                    ' <?php print $data_perc_script;?> '
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