<?php
    include('./Semana.php');
    include('../template/template-barra.php');

    $semana = new Semana();
    echo '
    <main role="main" class="container-fluid">
    <div class="card">
        <div class="card-body">
    <table class="table text-center">
        <thead>
            <tr>
                <th scope="col">Usuário</th>';
                foreach($semana->datas($semana->semana_fechada) as $key_d=>$value_d){

                    $data_texto = date('d/m/Y',strtotime($value_d));
                    echo '<th scope="col">'.$data_texto.'</th>';
                }
    echo '
            </tr>
        </thead>
        <tbody>';
    foreach($semana->usuarios() as $key_u=>$value_u){
        echo '<tr>';
        echo '<td scope="row">';
                echo $value_u['usrnme'];
                echo ' (';
                echo $value_u['usr_id'];
                echo ')';
            echo '</td>';
        foreach($semana->datas($semana->semana_fechada) as $key_d=>$value_d){
            echo '<td>';
            if($semana->formatarHora($semana->checarDiaHoras($value_u['usr_id'], $value_d)) >0){
                echo $semana->formatarHora($semana->checarDiaHoras($value_u['usr_id'], $value_d));
            }
            
            echo '</td>';
        }
        echo '</tr>';
    }
    echo '
        </tbody>
    </table>
    <br>
    </div>
    </div>
    </main>';
    include('../template/template-rodape.php');
?>