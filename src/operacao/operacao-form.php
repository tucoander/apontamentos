<?php
    include('../template/template-barra.php');
?>


<main role="main" class="class="container"">
    
    <div class="card" style="margin: 1em; padding: 1em; margin-top:3em;">
        <div class="card-body">
            <h1>Cadastro de Operações</h1>
            <form id="operacao">
                <div class="form-group">
                    <label for="oprnme">Nome da Operação</label>
                    <input type="text" class="form-control" id="oprnme" placeholder="Digite o nome da operação">
                </div>
                <div class="form-group">
                    <label for="rspare">País</label>
                    <select class="custom-select d-block w-100" id="cty_id" required>
                        <option value="">Escolha...</option>
                        <?php
                            $db = new SQLite3('../sqlite/apontamentos.db');

                            $s_tblprd = "
                                    SELECT cty_id, ctynme FROM usrcty GROUP BY cty_id, ctynme
                                ";
                                $resultado = $db->query($s_tblprd);
                                while($row = $resultado->fetchArray(SQLITE3_ASSOC)){
                                    print '
                                    <option value="'.$row["cty_id"].'">'.$row["ctynme"].'</option>
                                    ';
                                }
                        ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary verde" id="salvar">Salvar</button>
            </form>
            
        </div>

        <div role="alert" id="res"> 
            <!-- class="alert alert-primary" -->
        </div>
    </div>
    
</main>

<?php
    include('../template/template-rodape.php');
?>
<script src="./operacao-form.js"></script>