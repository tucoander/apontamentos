<?php
    include('../template/template-barra.php');
?>

<main role="main" class="container-fluid">
    <div class="card">
        <div class="card-body" style="height: 550px;">
            <h2>Atividades por usuário</h1>
            <hr>
            <form id="apontamento">
                <div class="row" style="width: 30%; float: right; padding: 10px;">
                <!-- Começa: Data -->
                <div class="col-md-12 mb-12" >
                    <label for="adddte">Data</label>
                    <div class="input-group">
                        <input type="date" class="form-control" id="adddte">
                    </div>
                </div>
                    <!-- Termina: Data -->
                    <!-- Começa: Usuario -->
                <div class="col-md-12 mb-12" style="margin-top: 20px;">
                    <label for="usr_id">Usuário</label>
                    <select class="custom-select d-block w-100" id="usr_id" onchange="">
                        <option value="">Selecione...</option>
                        <?php
                            $db = new SQLite3('../sqlite/apontamentos.db');

                            $s_tblprd = "
                                SELECT 
                                us.usr_id
                                    FROM usrsys us
                                    GROUP BY 
                                    us.usr_id
                                ";
                            $resultado = $db->query($s_tblprd);
                            while($row = $resultado->fetchArray(SQLITE3_ASSOC)){
                                print '
                                <option value="'.$row["usr_id"].'">'.$row["usr_id"].'</option>
                                ';
                            }
                        ?>
                    </select>
                </div>
                    <!-- Termina: Data -->
                </div>
                <div role="alert" id="res"> 
                    <!-- class="alert alert-primary" -->
                </div>          
            </form>
        </div>
    </div>

</main>
<?php
    include('../template/template-rodape.php');
?>
<script src="./apontamento-produtos.js"></script>
<script src="./apontamento-timepicker-from.js"></script> 
<script src="./apontamento-timepicker-to.js"></script> 