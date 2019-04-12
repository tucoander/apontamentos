<?php
    include('../template/template-barra.php');
    $db = new SQLite3('../sqlite/apontamentos.db');

    $s_tbllog = "
    SELECT 
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
        uo.oprnme
    order by 
        ul.logdte asc
    ";
    $_usr_id = $_SESSION['usr_id'];
    
    $cmd_db = $db->prepare($s_tbllog);
    $cmd_db->bindValue('usr_id', $_usr_id);

    $resultado = $cmd_db->execute();
    $labels;
    $data;

    while($row = $resultado->fetchArray(SQLITE3_ASSOC)){
        $labels[] = $row['oprnme'];
        $data[] = $row['diff_jd'];
    }
  
	
	$labels = implode("', '",$labels);
	
	$data = implode("', '",$data);
	
?>

<main role="main" class="container">
	<div style="margin: 1em; padding: 1em;">
		<div class="chart-container" style="position: relative; height:30vh; width:60vw" >
			<canvas id="myChart"></canvas>
		</div>
	</div>
</main>

<?php
    include('../template/template-rodape.php');
?>
<script>
    var ctx = document.getElementById('myChart').getContext('2d');
    var chart = new Chart(ctx, {
        type: 'doughnut',
		responsive: true,
        data: {
        labels: ['<?php print $labels;?> '],
        datasets: [{
            label: 'Operações',
            backgroundColor: ['rgb(255, 255, 0)','rgb(0, 255, 255)','rgb(0, 255, 0)'],
            borderColor: 'rgb(255, 255, 255)',
            data: [' <?php print $data;?> ']
        }]
    },

    // Configuration options go here
    options: {}
    });
</script>

