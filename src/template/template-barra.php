<?php

include(  $_SERVER['DOCUMENT_ROOT']."/001_apontar/src/rotas/rotas.php");

session_start();
if ($_SESSION["usrlog"]) {
    if (isset($_SESSION["usr_id"]) || !empty($_SESSION["usr_id"])) {
        $usr_id =  base64_decode($_SESSION["usr_id"]);
        $usr_psw =  base64_decode($_SESSION["usrpsw"]);

        $db = new SQLite3($_SERVER['DOCUMENT_ROOT'].'/001_apontar/src/sqlite/apontamentos.db');

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

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../../bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="../../css/custom.css">
    <link rel="stylesheet" href="../../mdt/css/mdtimepicker.css">
    <link rel="stylesheet" href="../../dashboard/css/Chart.css">
    
    <title>Apontamento</title>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <a class="navbar-brand" href="#"> <img src="../../img/logo1.png" height="25" class="d-inline-block align-top" alt=""></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Alterna navegação">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item active" >
                    <a class="nav-link" href="<?php print 'http://'.$site['Home'];?>">Home <span class="sr-only" ></span></a>
                </li>

                <li class="nav-item" >
                    <a class="nav-link" href="<?php print 'http://'.$site['Inserir'];?>" >Inserir</a>
                </li>
                <li class="nav-item" >
                    <a class="nav-link" href="<?php print 'http://'.$site['Visualizar'];?>" >Visualizar</a>
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
						  <a class="dropdown-item" href="http://'.$site['Produtos'].'">Produtos</a>
						  <a class="dropdown-item" href="#">Working here</a>
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
  