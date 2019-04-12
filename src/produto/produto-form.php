<?php
    include('../template/template-barra.php');
?>


<main role="main" class="class="container"">
    
    <div class="card" style="margin: 1em; padding: 1em; margin-top:3em;">
        <div class="card-body">
            <h1>Cadastro de Produto</h1>
            <form id="produto">
                <div class="form-group">
                    <label for="prdnme">Nome do Produto</label>
                    <input type="text" class="form-control" id="prdnme" placeholder="Seu Produto">
                </div>
                <div class="form-group">
                    <label for="rspare">Responsável</label>
                    <input type="text" class="form-control" id="rspare" placeholder="Responsável do produto">
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
<script src="./produto-form.js"></script>