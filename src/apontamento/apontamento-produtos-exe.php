<?php
    session_start();
    if(isset($_POST['usr_id']) && isset($_POST['adddte'])) {
        $db = new SQLite3('../sqlite/apontamentos.db');
        
        $s_tbllog = "
        SELECT 
        up.prdnme,
        sum((julianday(to_logtim) - julianday(fr_logtim))*24) as diff_jd
        FROM usrlog ul
            inner join  usrprd up 
            on (ul.prd_id = up.prd_id)
            inner join usrcty uc
            on (ul.cty_id = uc.cty_id)
            inner join usropr uo
            on (ul.opr_id = uo.opr_id)
        WHERE ul.usr_id = :usr_id
            AND ul.logdte = :adddte
        group by
            up.prdnme
        order by 
            ul.logdte asc
        ";
        $_usr_id = $_POST['usr_id'];
        $_adddte = $_POST['adddte'];

        $cmd_db = $db->prepare($s_tbllog);
        $cmd_db->bindValue('usr_id', $_usr_id);
        $cmd_db->bindValue('adddte', $_adddte);
    
        $resultado = $cmd_db->execute();
        $labels;
        $data;
        $cont= 0;
        while($row = $resultado->fetchArray(SQLITE3_ASSOC)){
            $labels[] = $row['prdnme'];
            $data[] = $row['diff_jd'];
            $cont++;
        }
      
        
        
        if($cont > 0){
            $labels = implode("', '",$labels);
            $data = implode("', '",$data);
            print '
        
                <div style="margin: 1em; padding: 1em;">
                    <div class="chart-container" style="position: relative; height:30vh; width:60vw" >
                        <canvas id="myChart"></canvas>
                    </div>
                </div>
            
            ';
            include('../template/template-rodape.php');
            print "
            <script>
                var ctx = document.getElementById('myChart').getContext('2d');
                var chart = new Chart(ctx, {
                    type: 'doughnut',
                    responsive: true,
                    data: {
                    labels: ['".$labels."'],
                    datasets: [{
                        label: 'Produtos',
                        backgroundColor: ['rgb(255, 255, 0)','rgb(0, 255, 255)','rgb(0, 255, 0)'],
                        borderColor: 'rgb(255, 255, 255)',
                        data: ['".$data."']
                    }]
                },
            
                // Configuration options go here
                options: {}
                });
            </script>
            ";
        }else{
            print 'Sem lançamentos';
        }
    }else{
        print 'Erro';
    }
    

