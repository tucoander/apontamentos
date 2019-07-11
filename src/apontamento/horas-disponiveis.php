<?php
    include('../template/template-barra.php');
    $db = new SQLite3('../sqlite/apontamentos.db');
    $array_insert =  [
        "04" =>  [ "mes" => "04","qtd_horas" => 184.8],
"05" =>  [ "mes" => "05","qtd_horas" => 193.2],
"06" =>  [ "mes" => "06","qtd_horas" => 168],
"07" =>  [ "mes" => "07","qtd_horas" => 193.2],
"08" =>  [ "mes" => "08","qtd_horas" => 184.8],
"09" =>  [ "mes" => "09","qtd_horas" => 176.4],
"10" =>  [ "mes" => "10","qtd_horas" => 193.2],
"11" =>  [ "mes" => "11","qtd_horas" => 176.4],
"12" =>  [ "mes" => "12","qtd_horas" => 184.8]       
    ];
    
    $s_tbllog = "
        SELECT 
        usr_id,
        substr(replace(logdte,'2019/',''),0,3),
        sum((julianday(to_logtim) - julianday(fr_logtim))*24) as diff_jd
        from usrlog 
        group by usr_id,
        substr(replace(logdte,'2019/',''),0,3)
        ";
        
    $cmd_db = $db->prepare($s_tbllog);

    $resultado = $cmd_db->execute();

    while($row = $resultado->fetchArray(SQLITE3_ASSOC)){
        print_r($row);
        echo  '<hr>';
    }
    

     
?>