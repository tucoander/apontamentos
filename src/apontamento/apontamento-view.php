<?php
    include('../template/template-barra.php');
    $db = new SQLite3('../sqlite/apontamentos.db');
    $s_tbllog = "";
?>

<main role="main" class="container-fluid">
    <div class="card">
        <div class="card-body">
        <?php
       if(isset($_POST['filtro'])){
            
            if(isset($_POST['adddte'])){
                $s_tbllog = "
                SELECT * 
                    FROM usrlog ul
                        inner join  usrprd up 
                        on (ul.prd_id = up.prd_id)
                        inner join usrcty uc
                        on (ul.cty_id = uc.cty_id)
                        inner join usropr uo
                        on (ul.opr_id = uo.opr_id)
                    WHERE ul.usr_id = :usr_id
                        AND ul.logdte = :logdte
                    order by ul.logdte asc
                ";
                $_adddte = $_POST['adddte'];
                $_usr_id = $_SESSION['usr_id'];
        
                $cmd_db = $db->prepare($s_tbllog);
                $cmd_db->bindValue('usr_id', $_usr_id);
                $cmd_db->bindValue('logdte', $_adddte);
            }
       }
       else if(isset($_POST['todos'])){
            
            $s_tbllog = "
                SELECT * 
                    FROM usrlog ul
                        inner join  usrprd up 
                        on (ul.prd_id = up.prd_id)
                        inner join usrcty uc
                        on (ul.cty_id = uc.cty_id)
                        inner join usropr uo
                        on (ul.opr_id = uo.opr_id)
                    WHERE ul.usr_id = :usr_id
                    order by ul.logdte asc
            ";
            $_usr_id = $_SESSION['usr_id'];
            $cmd_db = $db->prepare($s_tbllog);
            $cmd_db->bindValue('usr_id', $_usr_id);
       }
       else{
            
            
            $s_tbllog = "
            SELECT * 
                FROM usrlog ul
                    inner join  usrprd up 
                    on (ul.prd_id = up.prd_id)
                    inner join usrcty uc
                    on (ul.cty_id = uc.cty_id)
                    inner join usropr uo
                    on (ul.opr_id = uo.opr_id)
                WHERE ul.usr_id = :usr_id
                    AND ul.logdte = :logdte
                order by ul.logdte asc
            ";
            $_adddte = date("Y-m-d");
            $_usr_id = $_SESSION['usr_id'];

            $cmd_db = $db->prepare($s_tbllog);
            $cmd_db->bindValue('usr_id', $_usr_id);
            $cmd_db->bindValue('logdte', $_adddte);
       }

        
        ?>
        
        <form method="POST" action="apontamento-view.php">
			  <div class="row">
				<div class="col">
				  <input type="text" class="form-control-plaintext" value="Data:">
				</div>
				<div class="col">
				  <input type="date" class="form-control" placeholder="Data" id="adddte" name="adddte">
				</div>
				<div class="col">
				  <input type="submit" class="btn btn-primary col-12" name="filtro" value="Filtrar">
				</div>
				<div class="col">
				  <input type="submit" class="btn btn-primary col-12" name="todos" value="Limpar Filtro" >
				</div>
			  </div>
		</form>
        <br>
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
            <th colspan="2" scope="col">Observações</th>
            </tr>
        </thead>
            <tbody>
       
        <?php
        $contador = 0;
        $resultado = $cmd_db->execute();
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
                        <td>
                            <!-- Botão cada form -->
                            <form id="update'.$contador.'" action="apontamento-atualiza.php" method="post">
                                <input type="text" class="form-control" name="log_id" id="log_id" placeholder=""  value="'.$row["log_id"].'" hidden>
                                <input type="text" class="form-control" name="usr_id" id="usr_id" placeholder=""  value="'.$row["usr_id"].'" hidden>
                                <input type="text" class="form-control" name="prdnme" id="prdnme" placeholder=""  value="'.$row["prdnme"].'" hidden>
                                <input type="text" class="form-control" name="oprnme" id="oprnme" placeholder=""  value="'.$row["oprnme"].'" hidden>
                                <input type="text" class="form-control" name="ctynme" id="ctynme" placeholder=""  value="'.$row["ctynme"].'" hidden>
                                <input type="text" class="form-control" name="to_usr_id" id="to_usr_id" placeholder=""  value="'.$row["to_usr_id"].'" hidden>
                                <input type="text" class="form-control" name="logdte" id="logdte" placeholder=""  value="'.$row["logdte"].'" hidden>
                                <input type="text" class="form-control" name="fr_logtim" id="fr_logtim" placeholder=""  value="'.$row["fr_logtim"].'" hidden>
                                <input type="text" class="form-control" name="to_logtim" id="to_logtim" placeholder=""  value="'.$row["to_logtim"].'" hidden>
                                <input type="text" class="form-control" name="usrobs" id="usrobs" placeholder=""  value="'.$row["usrobs"].'" hidden>
                                <button type="submit" class="btn btn-primary" data-toggle="modal" >
                                    Editar
                                </button>
                            </form>
                        </td>
                    </tr>';
               
                $contador++;
            }
       ?>
            </tbody>
        </table>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <div>
    </div>
    
</main>
<?php
    include('../template/template-rodape.php');
?>

<script src="apontamento-edit-row.js"></script>
