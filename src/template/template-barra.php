<?php



$projeto = preg_split('/(\/)/', $_SERVER['PHP_SELF']) ;
 
    
    /* Rotas */
    $site =  array (
        "Index"=> $_SERVER['HTTP_HOST']."/".$projeto[1]."/src/",
        "Home"=> $_SERVER['HTTP_HOST']."/".$projeto[1]."/src/template/home.php",
        "Inserir" => $_SERVER['HTTP_HOST']."/".$projeto[1]."/src/apontamento/apontamento-form.php",
        "Tabela" => $_SERVER['HTTP_HOST']."/".$projeto[1]."/src/apontamento/apontamento-view.php",
        "Operações" => $_SERVER['HTTP_HOST']."/".$projeto[1]."/src/apontamento/apontamento-dashboard.php",
        "Produtos" => $_SERVER['HTTP_HOST']."/".$projeto[1]."/src/apontamento/apontamento-produtos.php",
        "Alterar_senha" => $_SERVER['HTTP_HOST']."/".$projeto[1]."/src/usuario/usuario-senha.php",
        "Database"=> $_SERVER['HTTP_HOST']."/".$projeto[1]."/src/sqlite/apontamentos.db",
        "Gráfico"=> $_SERVER['HTTP_HOST']."/".$projeto[1]."/src/dashboard/dashboard-user.php",
        "ProdutoGeral"=> $_SERVER['HTTP_HOST']."/".$projeto[1]."/src/dashboard/dashboard-produtos.php"
    );


session_start();
if ($_SESSION["usrlog"]) {
    if (isset($_SESSION["usr_id"]) || !empty($_SESSION["usr_id"])) {
        $usr_id =  base64_decode($_SESSION["usr_id"]);
        $usr_psw =  base64_decode($_SESSION["usrpsw"]);

        $db = new SQLite3('../sqlite/apontamentos.db');

        $s_tblaut = "
            SELECT ua.usr_id, ua.usrrol  
                FROM usraut ua
                WHERE ua.usr_id = '".$_SESSION['usr_id']."'
        ";
        $auth = false;
        $resultado = $db->query($s_tblaut);
        while($row = $resultado->fetchArray(SQLITE3_ASSOC)){
            if($row['usrrol'] == 'ADM'){
                $auth = true;
            }
            else{
                $auth = false;
            }
        }

    } else {
        header("Location: ../../");
    }
} else {
    header("Location: ../../");
}


?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <!-- Meta tags Obrigatórias -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="apple-touch-icon" sizes="57x57" href="../../img/favico/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="../../img/favico/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="../../img/favico/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="../../img/favico/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="../../img/favico/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="../../img/favico/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="../../img/favico/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="../../img/favico/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="../../img/favico/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192"  href="../../img/favico/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../../img/favico/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="../../img/favico/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../../img/favico/favicon-16x16.png">
    <link rel="manifest" href="../../img/favico/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="../../img/favico/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../../bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="../../css/custom.css">
    <link rel="stylesheet" href="../../mdt/css/mdtimepicker.css">
    <link rel="stylesheet" href="../../dashboard/css/Chart.css">
    
    <title>Apontamento</title>
</head>

<body>
  
    <nav class="navbar navbar-expand-lg navbar-bosch bg-bosch fixed-top float-left"  >
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Alterna navegação">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item active" >
                    <a class="nav-link" href="<?php print 'http://'.$site['Home'];?>">Home <span class="sr-only" ></span></a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Lançamentos
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                       
                        <a class="dropdown-item" href="<?php print 'http://'.$site['Gráfico'];?>">Gráfico</a>
                        <a class="dropdown-item" href="<?php print 'http://'.$site['Inserir'];?>" >Inserir</a>
                        <a class="dropdown-item" href="<?php print 'http://'.$site['Tabela'];?>">Tabela</a>
                    </div>
                </li>
                <?php 
                if($auth){
                    print '
					<li class="nav-item dropdown">
						<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						  Dashboard
						</a>
						<div class="dropdown-menu" aria-labelledby="navbarDropdown">
						  <a class="dropdown-item" href="http://'.$site['Operações'].'">Operações</a>
                          <a class="dropdown-item" href="http://'.$site['Produtos'].'">Produtos por Usuário</a>
                          <a class="dropdown-item" href="http://'.$site['ProdutoGeral'].'">Produtos</a>
						</div>
					</li>
                    
                    ';
                }
                ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarusuario" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Usuário
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarusuario">
                        <a class="dropdown-item" href="<?php print 'http://'.$site['Alterar_senha'];?>">Alterar Senha</a>
                    </div>
                </li>
                
                <!--
            <li class="nav-item">
                <a class="nav-link disabled" href="#">Desativado</a>
            </li>
            -->
            </ul>
        </div>
        <a href="../../">
            <span class="badge badge-secondary"> <?php print $_SESSION["usr_id"]; ?></span>
        </a>
    </nav>
    <br>
    
   
    <?php 
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
 
    function getBrowser($user_agent){
     
        if(strpos($user_agent, 'MSIE') !== FALSE)
            return 'Internet explorer';
        elseif(strpos($user_agent, 'Edge') !== FALSE) //Microsoft Edge
            return 'Microsoft Edge';
        elseif(strpos($user_agent, 'Trident') !== FALSE) //IE 11
            return 'Internet explorer';
        elseif(strpos($user_agent, 'Opera Mini') !== FALSE)
            return "Opera Mini";
        elseif(strpos($user_agent, 'Opera') || strpos($user_agent, 'OPR') !== FALSE)
            return "Opera";
        elseif(strpos($user_agent, 'Firefox') !== FALSE)
            return 'Mozilla Firefox';
        elseif(strpos($user_agent, 'Chrome') !== FALSE)
            return 'Google Chrome';
        elseif(strpos($user_agent, 'Safari') !== FALSE)
            return "Safari";
        else
            return 'Não Identificado';
     
    }
    $retorno = '<p></p>';
    $navegador = getBrowser($user_agent);
    if($navegador != 'Mozilla Firefox'){
        $retorno = '<p>Esse programa é otimizado para uso no Firefox. Clique <a href="https://www.mozilla.org/pt-BR/firefox/new/" >aqui</a>.</p>';
    }
    
    ?>
    <br>
    <div class="container-fluid">
        <div class="row">
            <div class="col-10 float-right" ><?php print $retorno;?></div>
            <div class="col float-left"><p><a href="#"><img src="../../img/Bosch-logo.png" height="35px" alt=""></a></p></div>
        </div>
    </div>