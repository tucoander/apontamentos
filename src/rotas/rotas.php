<?php

    $projeto = preg_split('/(\/)/', $_SERVER['PHP_SELF']) ;
 
    
    /* Rotas */
    $site =  array (
        "Index"=> $_SERVER['HTTP_HOST']."/".$projeto[1]."/src/",
        "Home"=> $_SERVER['HTTP_HOST']."/".$projeto[1]."/src/home.php",
        "Inserir" => $_SERVER['HTTP_HOST']."/".$projeto[1]."/src/apontamento/apontamento-form.php",
        "Visualizar" => $_SERVER['HTTP_HOST']."/".$projeto[1]."/src/apontamento/apontamento-view.php",
        "Operações" => $_SERVER['HTTP_HOST']."/".$projeto[1]."/src/apontamento/apontamento-dashboard.php",
        "Produtos" => $_SERVER['HTTP_HOST']."/".$projeto[1]."/src/apontamento/apontamento-produtos.php",
        "Alterar_senha" => $_SERVER['HTTP_HOST']."/".$projeto[1]."/src/usuario/usuario-senha.php",
        "Database"=> $_SERVER['HTTP_HOST']."/".$projeto[1]."/src/sqlite/apontamentos.db"
    ); 
  
/*
    foreach($site as $rowid => $rowvalue){
        print '<a href="http://'.$rowvalue.'">'.$rowid.'</a><br>';
        print '<p>'.$rowvalue.'</p>';
    }

    */
?>