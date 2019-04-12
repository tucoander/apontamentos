<?php
    include('../template/template-barra.php');
?>
<link href="mdtimepicker.css" rel="stylesheet">

<main role="main" class="class="container"">
    <div class="card" style="margin: 1em; padding: 1em; margin-top:3em;">
        <div class="card-body">
            <h1>Lançamento de Horas</h1>
            <hr>
            <form id="apontamento">
                <div class="row">
                    <!-- Começa: Data -->
                    <div class="col-md-4 mb-3">
                        <label for="adddte">Data</label>
                        <div class="input-group">
                            <input type="date" class="form-control" id="adddte">
                        </div>
                    </div>
                    <!-- Termina: Data -->

                    <!-- Começa: Hora Inicio -->
                    <div class="col-md-4 mb-3">
                        <label for="fr_tim">Hora Início</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="fr_tim"  >
                        </div>
                    </div>
                    <!-- Termina: Hora Inicio -->

                    <!-- Começa: Hora Fim -->
                    <div class="col-md-4 mb-3">
                        <label for="to_tim">Hora Fim</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="to_tim" >
                        </div>
                    </div>
                    <!-- Termina: Hora Fim -->

                    <!-- Começa: Produto -->
                    <div class="col-md-6 mb-3">
                        <label for="prd_id">Produto</label>
                            <select class="custom-select d-block w-100" id="prd_id" >
                                <option value="">Selecione...</option>
                                <?php
                                    $db = new SQLite3('../sqlite/apontamentos.db');

                                    $s_tblprd = "
                                            SELECT prd_id, prdnme FROM usrprd GROUP BY prd_id, prdnme
                                        ";
                                        $resultado = $db->query($s_tblprd);
                                        while($row = $resultado->fetchArray(SQLITE3_ASSOC)){
                                            print '
                                            <option value="'.$row["prd_id"].'">'.$row["prdnme"].'</option>
                                            ';
                                        }
                                ?>
                            </select>
                    </div>
                    <!-- Termina: Produto -->

                    <!-- Começa: Operação -->
                    <div class="col-md-6 mb-3">
                        <label for="opr_id">Operações</label>
                        <select class="custom-select d-block w-100" id="opr_id" >
                            <option value="">Selecione...</option>
                            <?php
                                $db = new SQLite3('../sqlite/apontamentos.db');

                                $s_tblprd = "
                                    SELECT 
                                    uo.opr_id, 
                                    uo.oprnme,
                                    uc.ctysgl
                                        FROM usropr uo
                                            inner join usrcty uc 
                                            on(uo.cty_id = uc.cty_id)
                                        GROUP BY 
                                            uo.opr_id, 
                                            uo.oprnme,
                                            uc.ctysgl
                                    ";
                                    $resultado = $db->query($s_tblprd);
                                    while($row = $resultado->fetchArray(SQLITE3_ASSOC)){
                                        print '
                                        <option value="'.$row["opr_id"].'">'.$row["oprnme"].' - '.$row["ctysgl"].'</option>
                                        ';
                                    }
                            ?>
                        </select>
                    </div>
                    <!-- Termina: Operação -->
                    <!-- Começa: Solicitante -->
                    <div class="col-md-5 mb-3">
                        <label for="usrask">Solicitante</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="usrask" placeholder="Nome" >
                        </div>
                    </div>
                    <!-- Termina: Solicitante -->

                    <!-- Começa: Observação -->
                    <div class="col-md-7 mb-3">
                        <label for="usrobs">Observação</label>
                        <textarea  rows="3" class="form-control" id="usrobs" placeholder="Observações."></textarea>
                    </div>
                    <!-- Termina: Observação -->

                     <!-- Começa: Usuário -->
                    <input type="text" class="form-control" id="usr_id" placeholder=""  value="<?php print $_SESSION["usr_id"];?>" hidden>
                    <!-- Termina: Usuário -->
                </div>
                
                <button type="submit" class="btn btn-primary" id="salvar" style="width: 33%; float: right;">Salvar</button>
            </form>
            
        </div>

        <div role="alert" id="res"> 
            <!-- class="alert alert-primary" -->
            <?php
            
            ?>
        </div>
    </div>
    <br>
    <br>
    <br>
</main>
<?php
    include('../template/template-rodape.php');
?>
<script src="./apontamento-form.js"></script>
<script src="./apontamento-timepicker-from.js"></script> 
<script src="./apontamento-timepicker-to.js"></script> 



