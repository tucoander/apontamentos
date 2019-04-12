<<<<<<< HEAD
<?php
	
	$db = new SQLite3('./src/sqlite/apontamentos.db');

	if((isset($_POST['usr_id'])) && (isset($_POST['usrpsw']))){
		$s_tblusr = "
			SELECT usr_id, usrpsw FROM usrsys WHERE usr_id = :usr_id and usrpsw = :usrpsw
		";
		$_usr_id = $_POST['usr_id'];
		$_usrpsw = md5($_POST['usrpsw']);
		
        $cmd_db = $db->prepare($s_tblusr);
        $cmd_db->bindValue('usr_id', $_usr_id);
        $cmd_db->bindValue('usrpsw', $_usrpsw);

		$resultado = $cmd_db->execute();

		if($resultado->fetchArray(SQLITE3_ASSOC) == false){
			header("Location: ./src/erro/senha-incorreta.php");
		}
		else{
			// Get the private context
			session_name(($_SEESION["usr_id"]).($_SEESION["usrpsw"]) );
			session_start();
		
			$_SESSION['usr_id'] = $_POST['usr_id'];
			$_SESSION['usrpsw'] = $_POST['usrpsw'];
			$_SESSION['usrlog'] = true;
			session_write_close();

			/**
			
			 * implementar função de ultimo login
			 */
			print '
			<br>
			<div class="d-flex align-items-center">
				<strong>Logando...</strong>
				<div class="spinner-border ml-auto" role="status" aria-hidden="true"></div>
			</div>
			<script language= "JavaScript">
				var delay=10000;
				setTimeout(console.log("Logando..."),delay);
			</script>
			';
			header("Location: ./src/apontamento/apontamento-form.php");
		}
	}
	else{
		header("Location: ./src/erro/senha-incorreta.php");
	}
=======
<?php
	
	$db = new SQLite3('./src/sqlite/apontamentos.db');

	if((isset($_POST['usr_id'])) && (isset($_POST['usrpsw']))){
		$s_tblusr = "
			SELECT usr_id, usrpsw FROM usrsys WHERE usr_id = :usr_id and usrpsw = :usrpsw
		";
		$_usr_id = $_POST['usr_id'];
		$_usrpsw = md5($_POST['usrpsw']);
		
        $cmd_db = $db->prepare($s_tblusr);
        $cmd_db->bindValue('usr_id', $_usr_id);
        $cmd_db->bindValue('usrpsw', $_usrpsw);

		$resultado = $cmd_db->execute();

		if($resultado->fetchArray(SQLITE3_ASSOC) == false){
			header("Location: ./src/erro/senha-incorreta.php");
		}
		else{
			// Get the private context
			session_name(($_SEESION["usr_id"]).($_SEESION["usrpsw"]) );
			session_start();
		
			$_SESSION['usr_id'] = $_POST['usr_id'];
			$_SESSION['usrpsw'] = $_POST['usrpsw'];
			$_SESSION['usrlog'] = true;
			session_write_close();

			/**
			
			 * implementar função de ultimo login
			 */
			print '
			<br>
			<div class="d-flex align-items-center">
				<strong>Logando...</strong>
				<div class="spinner-border ml-auto" role="status" aria-hidden="true"></div>
			</div>
			<script language= "JavaScript">
				var delay=10000;
				setTimeout(console.log("Logando..."),delay);
			</script>
			';
			header("Location: ./src/apontamento/apontamento-form.php");
		}
	}
	else{
		header("Location: ./src/erro/senha-incorreta.php");
	}
>>>>>>> 01e0c4f96880feb867b210397a6f4a8c7c65b090
?>