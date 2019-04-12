<?php
    include('../template/template-barra.php');
?>


<main role="main" class="class="container"">
    
    <div class="card" style="margin: 1em; padding: 1em; margin-top:3em;">
        <div class="card-body">
            <h1>Cadastro de Países</h1>
            <form id="pais">
                <div class="form-group">
                    <label for="ctynme">Nome dp País</label>
                    <input type="text" class="form-control" id="ctynme" placeholder="Digite o nome do país">
                </div>
                <div class="form-group">
                    <label for="ctysgl">Sigla</label>
                    <input type="text" class="form-control" id="ctysgl" placeholder="Digite a sigla do país">
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
<script src="./pais-form.js"></script>