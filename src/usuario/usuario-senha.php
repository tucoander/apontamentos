<?php
    include('../template/template-barra.php');
?>
<main role="main" class="class="container"">
    <div class="card" style="margin: 1em; padding: 1em; margin-top:3em;">
        <div class="card-body">
            <h1>Aletração de senha</h1>
            <form id="usuario">
                <div class="form-group">
                    <label for="old_usrpsw">Senha Antiga</label>
                    <input type="password" class="form-control" id="old_usrpsw" placeholder="Digite a senha antiga">
                </div>
                <div class="form-group">
                    <label for="new_usrpsw">Nova Senha</label>
                    <input type="password" class="form-control" id="new_usrpsw" placeholder="Digite a nova senha">
                </div>
                <button type="submit" class="btn btn-primary verde" id="salvar">Alterar Senha</button>
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
<script src="./usuario-senha.js"></script>