<?php
    include('./Semana.php');
    include('../template/template-barra.php');

    $semana = new Semana();
    if(isset($_POST['semana'])){
        $semana_tabela = $_POST['semana'];
    }
    else{
        $semana_tabela = $semana->semana_fechada; 
    }  
    echo '

    ';

    echo '
    <main role="main" class="container-fluid">

    

    <div class="card">
        <div class="card-body">

        <div class="row justify-content-center">
            <div class="col-2">
                <form method="POST" action="">
                    <div class="form-group" style="text-align: center;">
                        <button type="submit" class="btn btn-primary my-1">Anterior</button>
                        <input type="text" name="semana" value="'.($semana_tabela-1).'" hidden>
                    </div>
                </form>
            </div>
            <div class="col-8">
                
            </div>
            <div class="col-2" >
                <form method="POST" action="">
                    <div class="form-group" style="text-align: center;">
                        <button type="submit" class="btn btn-primary my-1">Próximo</button>
                        <input type="text" name="semana" value="'.($semana_tabela+1).'" hidden>
                    </div>
                </form>
            </div>
        </div>

    <table class="table text-center">
        <thead>
            <tr>
                <th scope="col">Usuário</th>';
                foreach($semana->datas($semana_tabela) as $key_d=>$value_d){

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
        foreach($semana->datas($semana_tabela) as $key_d=>$value_d){
            echo '<td>';
                if( $semana->checarDia($value_u['usr_id'], $value_d) == 1){
                    echo ' <img src="../../img/Ok.svg" class="figure-img" alt="Ok" style="height: 25px;">';
                }else if($semana->checarDia($value_u['usr_id'], $value_d) == 2){
                    echo ' <img src="../../img/iOk.png" class="figure-img" alt="iOk" style="height: 25px;">';
                } else{
                    echo ' <img src="../../img/nOk.svg" class="figure-img" alt="nOk" style="height: 25px;">';
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