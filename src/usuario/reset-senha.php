<?php
$db = new SQLite3('../sqlite/apontamentos.db');
$user = 'HDI1SP';
$_new_usrpsw_c = md5('Hugo321');

$u_tblusr = "
    UPDATE usrsys 
        SET usrpsw = :usrpsw
        WHERE usr_id = :usr_id
    ";
$cmd_db = $db->prepare($u_tblusr);
$cmd_db->bindValue('usr_id', $user);
$cmd_db->bindValue('usrpsw', $_new_usrpsw_c);
$resultado = $cmd_db->execute();

?>