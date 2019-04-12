<<<<<<< HEAD
<?php
	session_start();
	$_SESSION['usr_id'] = "";
	$_SESSION['usrpsw'] = "";
	$_SESSION['usrlog'] = false;
	session_write_close();
	session_cache_expire();
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Jekyll v3.8.5">
    <title>Login</title>
    <!-- Bootstrap core CSS -->
	<link rel="stylesheet" href="./bootstrap/css/bootstrap.css">
	<script src="./jquery/jquery-3.3.1.min.js"></script>
	<!-- Custom styles for this template -->
    <style>
		html,
		body {
			height: 100%;
		}

		body {
			display: -ms-flexbox;
			display: flex;
			-ms-flex-align: center;
			align-items: center;
			padding-top: 40px;
			padding-bottom: 40px;
			background-color: #f5f5f5;
		}

		.form-signin {
			width: 100%;
			max-width: 330px;
			padding: 15px;
			margin: auto;
		}
		.form-signin .checkbox {
			font-weight: 400;
		}
		.form-signin .form-control {
			position: relative;
			box-sizing: border-box;
			height: auto;
			padding: 10px;
			font-size: 16px;
		}
		.form-signin .form-control:focus {
			z-index: 2;
		}
		.form-signin input[type="email"] {
			margin-bottom: -1px;
			border-bottom-right-radius: 0;
			border-bottom-left-radius: 0;
		}
		.form-signin input[type="password"] {
			margin-bottom: 10px;
			border-top-left-radius: 0;
			border-top-right-radius: 0;
		}

		.bd-placeholder-img {
			font-size: 1.125rem;
			text-anchor: middle;
			-webkit-user-select: none;
			-moz-user-select: none;
			-ms-user-select: none;
			user-select: none;
		}

		@media (min-width: 768px) {
			.bd-placeholder-img-lg {
				font-size: 3.5rem;
			}
		}
    </style>
  </head>
	<body class="text-center">
		<form class="form-signin" id="login" method="post" action="index-exe.php">
			<img class="mb-4" src="./img/bosch-17.png" alt=""  height="72">
			<h1 class="h3 mb-3 font-weight-normal">Por favor entre</h1>
			<label for="usr_id" class="sr-only">Usuário</label>
			<input type="text" id="usr_id" name="usr_id" class="form-control" placeholder="Usuário" required autofocus value="">
			<label for="usrpsw" class="sr-only">Senha</label>
			<input type="password" id="usrpsw" name="usrpsw" class="form-control" placeholder="Senha" required value="">
			<button id="logar" class="btn btn-lg btn-primary btn-block" type="submit">Logar</button>
			<div class="h3 mb-3 font-weight-normal" id="res">
			<!-- Resposta -->
			<?php
				
			?>
			</div>
		</form>
	</body>
</html>
=======
<?php
	session_start();
	$_SESSION['usr_id'] = "";
	$_SESSION['usrpsw'] = "";
	$_SESSION['usrlog'] = false;
	session_write_close();
	session_cache_expire();
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Jekyll v3.8.5">
    <title>Login</title>
    <!-- Bootstrap core CSS -->
	<link rel="stylesheet" href="./bootstrap/css/bootstrap.css">
	<script src="./jquery/jquery-3.3.1.min.js"></script>
	<!-- Custom styles for this template -->
    <style>
		html,
		body {
			height: 100%;
		}

		body {
			display: -ms-flexbox;
			display: flex;
			-ms-flex-align: center;
			align-items: center;
			padding-top: 40px;
			padding-bottom: 40px;
			background-color: #f5f5f5;
		}

		.form-signin {
			width: 100%;
			max-width: 330px;
			padding: 15px;
			margin: auto;
		}
		.form-signin .checkbox {
			font-weight: 400;
		}
		.form-signin .form-control {
			position: relative;
			box-sizing: border-box;
			height: auto;
			padding: 10px;
			font-size: 16px;
		}
		.form-signin .form-control:focus {
			z-index: 2;
		}
		.form-signin input[type="email"] {
			margin-bottom: -1px;
			border-bottom-right-radius: 0;
			border-bottom-left-radius: 0;
		}
		.form-signin input[type="password"] {
			margin-bottom: 10px;
			border-top-left-radius: 0;
			border-top-right-radius: 0;
		}

		.bd-placeholder-img {
			font-size: 1.125rem;
			text-anchor: middle;
			-webkit-user-select: none;
			-moz-user-select: none;
			-ms-user-select: none;
			user-select: none;
		}

		@media (min-width: 768px) {
			.bd-placeholder-img-lg {
				font-size: 3.5rem;
			}
		}
    </style>
  </head>
	<body class="text-center">
		<form class="form-signin" id="login" method="post" action="index-exe.php">
			<img class="mb-4" src="./img/bosch-17.png" alt=""  height="72">
			<h1 class="h3 mb-3 font-weight-normal">Por favor entre</h1>
			<label for="usr_id" class="sr-only">Usuário</label>
			<input type="text" id="usr_id" name="usr_id" class="form-control" placeholder="Usuário" required autofocus value="">
			<label for="usrpsw" class="sr-only">Senha</label>
			<input type="password" id="usrpsw" name="usrpsw" class="form-control" placeholder="Senha" required value="">
			<button id="logar" class="btn btn-lg btn-primary btn-block" type="submit">Logar</button>
			<div class="h3 mb-3 font-weight-normal" id="res">
			<!-- Resposta -->
			<?php
				
			?>
			</div>
		</form>
	</body>
</html>
>>>>>>> 01e0c4f96880feb867b210397a6f4a8c7c65b090
