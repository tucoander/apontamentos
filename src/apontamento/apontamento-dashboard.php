<?php
    include('../template/template-barra.php');
    $db = new SQLite3('../sqlite/apontamentos.db');

    $labels = array();
    $data = array();

    $labels_script;
    $data_script;

    $msg = '';

    if(isset($_POST['filtro']) && isset($_POST['fr_adddte']) && isset($_POST['to_adddte'])){
        if(!empty($_POST['fr_adddte']) && !empty($_POST['to_adddte'])){

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
                where ul.logdte between :fr_adddte and :to_adddte
                group by
                uo.opr_id,
                    uo.oprnme
                order by 
                    ul.logdte asc
                ";

            $_usr_id = $_SESSION['usr_id'];
            $_fr_adddte = $_POST['fr_adddte'];
            $_to_adddte = $_POST['to_adddte'];

            $cmd_db = $db->prepare($s_tbllog);
            $cmd_db->bindValue('fr_adddte', $_fr_adddte);
            $cmd_db->bindValue('to_adddte', $_to_adddte);
        
            $resultado = $cmd_db->execute();
           
            while($row = $resultado->fetchArray(SQLITE3_ASSOC)){
                $labels[] = $row['oprnme'];
                $data[] = $row['diff_jd'];
            }

            $labels_script = implode("', '",$labels);
            $data_script = implode("', '",$data);

            $msg = 'Intervalo Pesquisado: ';
            $msg .= date("d/m/Y",strtotime($_POST['fr_adddte']));
            $msg .= ' até ';
            $msg .= date("d/m/Y",strtotime($_POST['to_adddte']));
        }
        else {
           $msg = 'É necessário inserir data inicial e data final';
        }
    }
    else if(isset($_POST['todos'])){
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
            ul.logdte asc
        ";
        $_usr_id = $_SESSION['usr_id'];
        
        $cmd_db = $db->prepare($s_tbllog);
        
        $resultado = $cmd_db->execute();

        while($row = $resultado->fetchArray(SQLITE3_ASSOC)){
            $labels[] = $row['oprnme'];
            $data[] = $row['diff_jd'];
        }
        $labels_script = implode("', '",$labels);
        $data_script = implode("', '",$data);
    }
    else{
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
            ul.logdte asc
        ";
        $_usr_id = $_SESSION['usr_id'];
        
        $cmd_db = $db->prepare($s_tbllog);

        $resultado = $cmd_db->execute();

        while($row = $resultado->fetchArray(SQLITE3_ASSOC)){
            $labels[] = $row['oprnme'];
            $data[] = $row['diff_jd'];
        }
        $labels_script = implode("', '",$labels);
        $data_script = implode("', '",$data);
    }
    
	
?>

<main role="main" class="container-fluid">
    <div class="card" >
        <div class="card-body" style="height: 35em;">
            <form method="POST" action="apontamento-dashboard.php">
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
                    <input type="submit" class="btn btn-primary col-12" name="todos" value="Limpar Filtro" >
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
            <div style="
                position: initial;
                margin: auto;
                height: 60vh;
                width: 60vw;">
                <div class="chart-container">
                    <canvas id="myChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <br>
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
        labels: ['<?php print $labels_script;?> '],
        datasets: [{
            label: 'Operações',
            backgroundColor: [
                'rgb(0, 98, 73)',
                'rgb(120, 190, 32)',
                'rgb(0, 168, 176)',
                'rgb(0, 142, 207)',
                'rgb(0, 86, 145)',
                'rgb(80, 36, 127)',
                'rgb(185, 2, 118)'
            ],
            borderColor: 'rgb(255, 255, 255)',
            data: [' <?php print $data_script;?> ']
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

