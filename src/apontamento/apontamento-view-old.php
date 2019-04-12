<?php
    include('../template/template-barra.php');
    $logado = base64_decode($_COOKIE['usr_id']);
    $db = new SQLite3('../sqlite/apontamentos.db');
?>

<div class="alert alert-success" role="alert" id="res">
    
</div>
<main role="main" class="class="container"">
    
    <div class="card" style="margin: 1em; padding: 1em; margin-top:3em;">
        <div class="card-body">
        <?php
        $s_tbllog = "
            SELECT * 
                FROM usrlog ul
                    inner join  usrprd up 
                    on (ul.prd_id = up.prd_id)
                    inner join usrcty uc
                    on (ul.cty_id = uc.cty_id)
                    inner join usropr uo
                    on (ul.opr_id = uo.opr_id)
                order by ul.logdte asc
        ";

        print '
        <button type="submit" class="btn btn-primary" id="editar" style="float: right">Editar</button>
        <br><br>
        <table class="table" id="apontamento">
        <thead>
            <tr>
            <th scope="col">ID</th>
            <th scope="col">Usuário</th>
            <th scope="col">Produto</th>
            <th scope="col">Operação</th>
            <th scope="col">País</th>
            <th scope="col">Solicitante</th>
            <th scope="col">Data</th>
            <th scope="col">Hora Início</th>
            <th scope="col">Hora Fim</th>
            <th scope="col">Observações</th>
            </tr>
        </thead>

        ';
        $resultado = $db->query($s_tbllog);
            while($row = $resultado->fetchArray(SQLITE3_ASSOC)){
                print '
                <tr>
                    <td scope="row">'.$row["log_id"].'</td>
                    <td>'.$row["usr_id"].'</td>
                    <td>'.$row["prdnme"].'</td>
                    <td>'.$row["oprnme"].'</td>
                    <td>'.$row["ctynme"].'</td>
                    <td>'.$row["to_usr_id"].'</td>
                    <td>'.$row["logdte"].'</td>
                    <td>'.$row["fr_logtim"].'</td>
                    <td>'.$row["to_logtim"].'</td>
                    <td>'.$row["usrobs"].'</td>
               
                </tr>
                ';
            }
        print '
            </tbody>
        </table>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        ';
        print '
        ';
        ?>
        <div>
    </div>
    
</main>

<?php
    include('../template/template-rodape.php');
?>
<script src="apontamento-edit-row.js"></script>
