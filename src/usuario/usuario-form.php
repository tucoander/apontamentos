<?php
    include('../template/template-barra.php');
?>


<main role="main" class="class="container"">
    
    <div class="card" style="margin: 1em; padding: 1em; margin-top:3em;">
        <div class="card-body">
            <h1>Cadastro de Usuários</h1>
            <form id="usuario">
                <div class="form-group">
                    <label for="usr_id">Usuário</label>
                    <input type="text" class="form-control" id="usr_id" placeholder="Digite o usuário">
                </div>
                <div class="form-group">
                    <label for="usrpsw">Sigla</label>
                    <input type="password" class="form-control" id="usrpsw" placeholder="Digite a senha">
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
<script src="./usuario-form.js"></script>