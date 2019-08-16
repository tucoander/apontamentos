<?php
date_default_timezone_set('America/Sao_Paulo');
    $db = new SQLite3('apontamentos.db');
	
	$agora = new DateTime('now');
	         

	var_dump($agora->format('Y-m-d H:i'));

?>
