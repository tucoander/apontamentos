<?php
    /* barra navegação */
    include('../template/template-barra.php');
    $db = new SQLite3('../sqlite/apontamentos.db');
    $dias = array();
    $indisp = '';
    $s_calendario = "
    select strftime('%W', yr_dte || '-' || mn_dte || '-' || dy_dte) as semana,
    ( yr_dte || '-' || mn_dte || '-' || dy_dte) as dia,
    strftime('%w', yr_dte || '-' || mn_dte || '-' || dy_dte) as d_semana,
    wk_day,
    count(ind_id) as indisp,
    count(log_id) as apont
    from yr_idx
    inner
    join (SELECT min(strftime('%W', yr_dte || '-' || mn_dte || '-' || dy_dte)) as sem_com,
                    max(strftime('%W', yr_dte || '-' || mn_dte || '-' || dy_dte)) as sem_fim
            FROM yr_idx
            WHERE yr_dte = :yr_dte
                and mn_dte = :mn_dte
            ORDER BY dy_dte asc,
                    mn_dte asc) limites
        on (strftime('%W', yr_dte || '-' || mn_dte || '-' || dy_dte) between limites.sem_com and limites.sem_fim) 
        left join usrind 
        on ((yr_dte || '-' || mn_dte || '-' || dy_dte)= usrind.inddte and usrind.usr_id = :usr_id)
        left join usrlog 
        on ((yr_dte || '-' || mn_dte || '-' || dy_dte)= usrlog.logdte and usrlog.usr_id = :usr_id)
        group by strftime('%W', yr_dte || '-' || mn_dte || '-' || dy_dte),
        ( yr_dte || '-' || mn_dte || '-' || dy_dte) ,
        strftime('%w', yr_dte || '-' || mn_dte || '-' || dy_dte) ,
        wk_day
        Order by ( yr_dte || '-' || mn_dte || '-' || dy_dte)  asc
    ";

    if(isset($_POST['mes'])){
      
        $mes = str_pad($_POST['mes'], 2, "0", STR_PAD_LEFT);
    }
    else{
        $mes = str_pad(date('m'), 2, "0", STR_PAD_LEFT);
    }
    $ano = str_pad(date('Y'), 2, "0", STR_PAD_LEFT);
    
    

    $cmd_db_cal = $db->prepare($s_calendario);
    $cmd_db_cal->bindValue('usr_id', $_SESSION["usr_id"]);
    $cmd_db_cal->bindValue('yr_dte', $ano);
    $cmd_db_cal->bindValue('mn_dte', $mes);
    $res_cal = $cmd_db_cal->execute();

    while($row = $res_cal->fetchArray(SQLITE3_ASSOC)){
        $dias[]= $row;
    }

    $semana = array(
        "segunda-feira",
        "terça-feira",
        "quarta-feira",
        "quinta-feira",
        "sexta-feira"
    );
?>
<div class="container">

<?php
    $i = 0;
    // começo do calendario
    echo '

    <div class="row justify-content-center">
        <div class="col-1">
            <form method="POST" action="calendario-indisponivel.php">
                <div class="form-group">
                    <button type="submit" class="btn btn-primary my-1 float-left">Anterior</button>
                    <input type="text" name="mes" value="'.($mes-1).'" hidden>
                </div>
            </form>
        </div>
        <div class="col-10 ">
            <form >
                <div class="form-group">
                    <button type="button" class="btn btn-light" style="margin-left: 25em; margin-right: 25em; margin-top: 1em;">'.$mes.'/'.$ano.'</button>
                </div>
            </form>
        </div>
        <div class="col-1">
            <form method="POST" action="calendario-indisponivel.php">
                <div class="form-group">
                    <button type="submit" class="btn btn-primary my-1 float-right">Próximo</button>
                    <input type="text" name="mes" value="'.($mes+1).'" hidden>
                </div>
            </form>
        </div>
    </div>

    <table class="table">
    <thead>
        <tr>';
    foreach($semana as $key=>$value){
        echo '
        <th>'.$value.'</th>
        ';
    }
    echo '</tr>
    </thead>
    <tbody>';
    
    foreach($dias as $key=>$value_r){
        if($value_r['d_semana'] == 0 or $value_r['d_semana'] == 6){
            echo '';
        }else if($value_r['d_semana'] == 5){
            echo '
            <td>
                <div class="custom-control custom-checkbox">';
                if($value_r['indisp']> 0){
                    echo '
                    <input type="checkbox" class="custom-control-input" id="dia-'.$i.'" onchange=pegadia("'.$value_r["dia"].'","dia-'.$i.'") checked>';
                }
                else{
					if($value_r['apont']> 0){
						echo '
						<input type="checkbox" class="custom-control-input" id="dia-'.$i.'" onchange=pegadia("'.$value_r["dia"].'","dia-'.$i.'") disabled >';
					}
					else{
						echo '
						<input type="checkbox" class="custom-control-input" id="dia-'.$i.'" onchange=pegadia("'.$value_r["dia"].'","dia-'.$i.'") >';
					}
                    
                }
                
                echo '
                    <label class="custom-control-label" for="dia-'.$i.'">'.$value_r["dia"].'</label>
                </div>
            </td>
            </tr>';
        }else {
            echo '
            <td>
                <div class="custom-control custom-checkbox">';
                    if($value_r['indisp']> 0){
                        echo '
                        <input type="checkbox" class="custom-control-input" id="dia-'.$i.'" onchange=pegadia("'.$value_r["dia"].'","dia-'.$i.'") checked>';
                    }
                    else{
						if($value_r['apont']> 0){
							echo '
							<input type="checkbox" class="custom-control-input" id="dia-'.$i.'" onchange=pegadia("'.$value_r["dia"].'","dia-'.$i.'") disabled >';
						}
						else{
							echo '
							<input type="checkbox" class="custom-control-input" id="dia-'.$i.'" onchange=pegadia("'.$value_r["dia"].'","dia-'.$i.'") >';
						}
					}
                    
                    echo '
                    <label class="custom-control-label" for="dia-'.$i.'">'.$value_r["dia"].'</label>
                </div>
            </td>';
        }
        $i++;
    }
    echo '</tbody>
    </table>
    <div id="res"></div>';

    
   //fim do calendario
?>
</div>
<?php
    include('../template/template-rodape.php');
?>
<script>
jQuery(document).ready(function(){
});
function pegadia(dia, id) {
    var dia = dia;
    var acao = $("#"+id).prop("checked");
    if( acao == true){
        console.log($("#"+id).prop("checked"));
        console.log(acao);
        
        jQuery.ajax({
            type: "POST",
            url: "indisponibilidade-exe.php",
            dataType: "html",
            data: {
                dia: dia,
                acao: acao
            },
        // enviado com sucesso
            success: function(response){
                jQuery("#res").empty();
                jQuery("#res").append(response);
                
            },
            // quando houver erro
            error: function(){
                alert("Erro no Ajax");
            }
        });
    }
    else{
        console.log($("#"+id).prop("checked"));
        console.log(acao);
        jQuery.ajax({
            type: "POST",
            url: "indisponibilidade-exe.php",
            dataType: "html",
            data: {
                dia: dia,
                acao: acao
            },
        // enviado com sucesso
            success: function(response){
                jQuery("#res").empty();
                jQuery("#res").append(response);
                
            },
            // quando houver erro
            error: function(){
                alert("Erro no Ajax");
            }
        });
    }
        
}

</script>