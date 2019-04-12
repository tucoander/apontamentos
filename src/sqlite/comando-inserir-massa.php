<?php
    $db = new SQLite3('apontamentos.db');
    /*
    $agora = date("Y-m-d H:i:s");
    $senha = '123456';
    $senha_md5 = md5($senha);

    $c_tbllog = "
    INSERT INTO usrsys (usr_id , usrpsw, adddte) values ('FAA5LOV','$senha_md5','$agora')
    ";
    $db->exec($c_tbllog);

    */
    $comando = array (
       "insert into usrlog (usr_id,prd_id,opr_id,cty_id, to_usr_id, logdte, fr_logtim, to_logtim, usrobs) values ('DOM9ITV', '6', '1', '1', '', '2019-04-01','07:55','08:00','Atualização andon');",
		"insert into usrlog (usr_id,prd_id,opr_id,cty_id, to_usr_id, logdte, fr_logtim, to_logtim, usrobs) values ('DOM9ITV', '19', '1', '1', '', '2019-04-01','08:00','08:05','Manutenção preventiva atividades diarias');",
		"insert into usrlog (usr_id,prd_id,opr_id,cty_id, to_usr_id, logdte, fr_logtim, to_logtim, usrobs) values ('DOM9ITV', '6', '1', '1', '', '2019-04-01','08:07','08:20','Atualização opl ');",
		"insert into usrlog (usr_id,prd_id,opr_id,cty_id, to_usr_id, logdte, fr_logtim, to_logtim, usrobs) values ('DOM9ITV', '19', '1', '1', '', '2019-04-01','08:25','11:26','chamado: 1000006665');",
		"insert into usrlog (usr_id,prd_id,opr_id,cty_id, to_usr_id, logdte, fr_logtim, to_logtim, usrobs) values ('DOM9ITV', '6', '1', '1', '', '2019-04-01','12:55','14:30','orçamento (orion)');",
		"insert into usrlog (usr_id,prd_id,opr_id,cty_id, to_usr_id, logdte, fr_logtim, to_logtim, usrobs) values ('DOM9ITV', '23', '1', '1', '', '2019-04-01','14:30','16:00','treinamento compliance');",
		"insert into usrlog (usr_id,prd_id,opr_id,cty_id, to_usr_id, logdte, fr_logtim, to_logtim, usrobs) values ('DOM9ITV', '19', '1', '1', '', '2019-04-02','07:55','08:00','Manutenção preventiva atividades diarias');",
		"insert into usrlog (usr_id,prd_id,opr_id,cty_id, to_usr_id, logdte, fr_logtim, to_logtim, usrobs) values ('DOM9ITV', '6', '1', '1', '', '2019-04-02','08:00','08:05','Atualização opl ');",
		"insert into usrlog (usr_id,prd_id,opr_id,cty_id, to_usr_id, logdte, fr_logtim, to_logtim, usrobs) values ('DOM9ITV', '6', '1', '1', '', '2019-04-02','09:02','10:00','instalação aplicativos maquina nova ');",
		"insert into usrlog (usr_id,prd_id,opr_id,cty_id, to_usr_id, logdte, fr_logtim, to_logtim, usrobs) values ('DOM9ITV', '6', '1', '1', '', '2019-04-02','10:05','11:30','  kpi sentinela');",
		"insert into usrlog (usr_id,prd_id,opr_id,cty_id, to_usr_id, logdte, fr_logtim, to_logtim, usrobs) values ('DOM9ITV', '6', '1', '1', '', '2019-04-02','12:40','14:50','Testes sentinela dashboard ');",
		"insert into usrlog (usr_id,prd_id,opr_id,cty_id, to_usr_id, logdte, fr_logtim, to_logtim, usrobs) values ('DOM9ITV', '6', '1', '1', '', '2019-04-02','07:55','08:00','Atualização andon');",
		"insert into usrlog (usr_id,prd_id,opr_id,cty_id, to_usr_id, logdte, fr_logtim, to_logtim, usrobs) values ('DOM9ITV', '19', '1', '1', '', '2019-04-03','08:00','08:05','Manutenção preventiva atividades diarias');",
		"insert into usrlog (usr_id,prd_id,opr_id,cty_id, to_usr_id, logdte, fr_logtim, to_logtim, usrobs) values ('DOM9ITV', '32', '1', '1', '', '2019-04-03','09:00','09:48','chamado Alarme gabinete de chaves ');",
		"insert into usrlog (usr_id,prd_id,opr_id,cty_id, to_usr_id, logdte, fr_logtim, to_logtim, usrobs) values ('DOM9ITV', '19', '1', '1', '', '2019-04-03','09:50','11:30','instalação hd dip 6000 retorno de garantia ');",
		"insert into usrlog (usr_id,prd_id,opr_id,cty_id, to_usr_id, logdte, fr_logtim, to_logtim, usrobs) values ('DOM9ITV', '6', '1', '1', '', '2019-04-03','12:40','13:40','Workon RBGA-2963969');",
		"insert into usrlog (usr_id,prd_id,opr_id,cty_id, to_usr_id, logdte, fr_logtim, to_logtim, usrobs) values ('DOM9ITV', '6', '1', '1', '', '2019-04-04','07:55','08:00','Atualização andon');",
		"insert into usrlog (usr_id,prd_id,opr_id,cty_id, to_usr_id, logdte, fr_logtim, to_logtim, usrobs) values ('DOM9ITV', '19', '1', '1', '', '2019-04-04','08:00','08:05','Manutenção preventiva atividades diarias');",
		"insert into usrlog (usr_id,prd_id,opr_id,cty_id, to_usr_id, logdte, fr_logtim, to_logtim, usrobs) values ('DOM9ITV', '6', '1', '1', '', '2019-04-04','08:20','09:00','ativação sentinela rua 35 ');",
		"insert into usrlog (usr_id,prd_id,opr_id,cty_id, to_usr_id, logdte, fr_logtim, to_logtim, usrobs) values ('DOM9ITV', '32', '1', '1', '', '2019-04-04','09:05','12:00','acompanhamento formatação PC's sib ');",
		"insert into usrlog (usr_id,prd_id,opr_id,cty_id, to_usr_id, logdte, fr_logtim, to_logtim, usrobs) values ('DOM9ITV', '6', '1', '1', '', '2019-04-04','13:10','14:30','chamado: 1000006697');",
		"insert into usrlog (usr_id,prd_id,opr_id,cty_id, to_usr_id, logdte, fr_logtim, to_logtim, usrobs) values ('DOM9ITV', '32', '1', '1', '', '2019-04-04','14:31','17:00','acompanhamento formatação PC's sib ');",
		"insert into usrlog (usr_id,prd_id,opr_id,cty_id, to_usr_id, logdte, fr_logtim, to_logtim, usrobs) values ('DOM9ITV', '6', '1', '1', '', '2019-04-05','07:55','08:00','Atualização andon');",
		"insert into usrlog (usr_id,prd_id,opr_id,cty_id, to_usr_id, logdte, fr_logtim, to_logtim, usrobs) values ('DOM9ITV', '19', '1', '1', '', '2019-04-05','08:00','08:05','Manutenção preventiva atividades diarias');",
		"insert into usrlog (usr_id,prd_id,opr_id,cty_id, to_usr_id, logdte, fr_logtim, to_logtim, usrobs) values ('DOM9ITV', '6', '1', '1', '', '2019-04-05','08:10','10:00','chamado: 1000006697');",
		"insert into usrlog (usr_id,prd_id,opr_id,cty_id, to_usr_id, logdte, fr_logtim, to_logtim, usrobs) values ('DOM9ITV', '32', '1', '1', '', '2019-04-05','10:11','11:00','infraestrutura portais de det.');",
		"insert into usrlog (usr_id,prd_id,opr_id,cty_id, to_usr_id, logdte, fr_logtim, to_logtim, usrobs) values ('DOM9ITV', '32', '1', '1', '', '2019-04-05','11:10','11:54','reunião mireia (claviculario)');",
		"insert into usrlog (usr_id,prd_id,opr_id,cty_id, to_usr_id, logdte, fr_logtim, to_logtim, usrobs) values ('DOM9ITV', '32', '1', '1', '', '2019-04-05','12:10','13:10','infraestrutura portais de det.');",
		"insert into usrlog (usr_id,prd_id,opr_id,cty_id, to_usr_id, logdte, fr_logtim, to_logtim, usrobs) values ('DOM9ITV', '32', '1', '1', '', '2019-04-05','13:12','13:40','auxilio relatorio BIS mireia ');",
		"insert into usrlog (usr_id,prd_id,opr_id,cty_id, to_usr_id, logdte, fr_logtim, to_logtim, usrobs) values ('DOM9ITV', '32', '1', '1', '', '2019-04-05','13:50','16:05','infraestrutura portais de det.');",
		"insert into usrlog (usr_id,prd_id,opr_id,cty_id, to_usr_id, logdte, fr_logtim, to_logtim, usrobs) values ('DOM9ITV', '6', '1', '1', '', '2019-04-05','16:30','17:00','Reunião coc ');",
		"insert into usrlog (usr_id,prd_id,opr_id,cty_id, to_usr_id, logdte, fr_logtim, to_logtim, usrobs) values ('DOM9ITV', '6', '1', '1', '', '2019-04-08','07:55','08:00','Atualização andon');",
		"insert into usrlog (usr_id,prd_id,opr_id,cty_id, to_usr_id, logdte, fr_logtim, to_logtim, usrobs) values ('DOM9ITV', '19', '1', '1', '', '2019-04-08','08:00','08:05','Manutenção preventiva atividades diarias');",
		"insert into usrlog (usr_id,prd_id,opr_id,cty_id, to_usr_id, logdte, fr_logtim, to_logtim, usrobs) values ('DOM9ITV', '32', '1', '1', '', '2019-04-08','08:10','09:00','Fontes catracas (aterramento)');",
		"insert into usrlog (usr_id,prd_id,opr_id,cty_id, to_usr_id, logdte, fr_logtim, to_logtim, usrobs) values ('DOM9ITV', '19', '1', '1', '', '2019-04-08','09:10','09:30','chamado: 1000006717');",
		"insert into usrlog (usr_id,prd_id,opr_id,cty_id, to_usr_id, logdte, fr_logtim, to_logtim, usrobs) values ('DOM9ITV', '6', '1', '1', '', '2019-04-08','10:16','11:30','kpi sentinela ');",
		"insert into usrlog (usr_id,prd_id,opr_id,cty_id, to_usr_id, logdte, fr_logtim, to_logtim, usrobs) values ('DOM9ITV', '19', '1', '1', '', '2019-04-08','12:40','14:48','Manutenção preventiva ');",
		"insert into usrlog (usr_id,prd_id,opr_id,cty_id, to_usr_id, logdte, fr_logtim, to_logtim, usrobs) values ('DOM9ITV', '19', '1', '1', '', '2019-04-08','15:00','16:20','Manutenção preventiva ');",
		"insert into usrlog (usr_id,prd_id,opr_id,cty_id, to_usr_id, logdte, fr_logtim, to_logtim, usrobs) values ('DOM9ITV', '6', '1', '1', '', '2019-04-08','16:22','16:33','conf leptop sala prs ');",
		"insert into usrlog (usr_id,prd_id,opr_id,cty_id, to_usr_id, logdte, fr_logtim, to_logtim, usrobs) values ('DOM9ITV', '6', '1', '1', '', '2019-04-09','07:55','08:00','Atualização andon');",
		"insert into usrlog (usr_id,prd_id,opr_id,cty_id, to_usr_id, logdte, fr_logtim, to_logtim, usrobs) values ('DOM9ITV', '19', '1', '1', '', '2019-04-09','08:00','08:05','Manutenção preventiva atividades diarias');",
		"insert into usrlog (usr_id,prd_id,opr_id,cty_id, to_usr_id, logdte, fr_logtim, to_logtim, usrobs) values ('DOM9ITV', '6', '1', '1', '', '2019-04-09','08:10','09:37','Problema de gravação vrm ');",
		"insert into usrlog (usr_id,prd_id,opr_id,cty_id, to_usr_id, logdte, fr_logtim, to_logtim, usrobs) values ('DOM9ITV', '19', '1', '1', '', '2019-04-09','09:40','10:00','chamado 1000006730');",
		"insert into usrlog (usr_id,prd_id,opr_id,cty_id, to_usr_id, logdte, fr_logtim, to_logtim, usrobs) values ('DOM9ITV', '6', '1', '1', '', '2019-04-09','10:19','11:10','kpi sentinela ');",
		"insert into usrlog (usr_id,prd_id,opr_id,cty_id, to_usr_id, logdte, fr_logtim, to_logtim, usrobs) values ('DOM9ITV', '6', '1', '1', '', '2019-04-09','12:30','16:30','Problema de gravação vrm ');",
		"insert into usrlog (usr_id,prd_id,opr_id,cty_id, to_usr_id, logdte, fr_logtim, to_logtim, usrobs) values ('DOM9ITV', '19', '1', '1', '', '2019-04-09','16:30','17:00','rack sala sib ');",
		"insert into usrlog (usr_id,prd_id,opr_id,cty_id, to_usr_id, logdte, fr_logtim, to_logtim, usrobs) values ('DOM9ITV', '6', '1', '1', '', '2019-04-10','07:55','08:00','Atualização andon');",
		"insert into usrlog (usr_id,prd_id,opr_id,cty_id, to_usr_id, logdte, fr_logtim, to_logtim, usrobs) values ('DOM9ITV', '19', '1', '1', '', '2019-04-10','08:00','08:05','Manutenção preventiva atividades diarias');",
		"insert into usrlog (usr_id,prd_id,opr_id,cty_id, to_usr_id, logdte, fr_logtim, to_logtim, usrobs) values ('DOM9ITV', '6', '', '', '', '2019-04-10','08:10','09:00','orçamentos hugo dias ');",
		"insert into usrlog (usr_id,prd_id,opr_id,cty_id, to_usr_id, logdte, fr_logtim, to_logtim, usrobs) values ('DOM9ITV', '23', '1', '1', '', '2019-04-10','09:00','09:30','indice uv ');",
		"insert into usrlog (usr_id,prd_id,opr_id,cty_id, to_usr_id, logdte, fr_logtim, to_logtim, usrobs) values ('DOM9ITV', '6', '1', '1', '', '2019-04-10','09:57','10:27','kpi sentinela ');",
		"insert into usrlog (usr_id,prd_id,opr_id,cty_id, to_usr_id, logdte, fr_logtim, to_logtim, usrobs) values ('DOM9ITV', '6', '1', '1', '', '2019-04-10','10:30','11:50','orçamentos hugo dias ');",
		"insert into usrlog (usr_id,prd_id,opr_id,cty_id, to_usr_id, logdte, fr_logtim, to_logtim, usrobs) values ('DOM9ITV', '6', '1', '1', '', '2019-04-10','13:15','14:20','treinamento BVMS COC');",
		"insert into usrlog (usr_id,prd_id,opr_id,cty_id, to_usr_id, logdte, fr_logtim, to_logtim, usrobs) values ('DOM9ITV', '6', '', '', '', '2019-04-10','14:30','17:05','orçamentos hugo dias ');",
		"insert into usrlog (usr_id,prd_id,opr_id,cty_id, to_usr_id, logdte, fr_logtim, to_logtim, usrobs) values ('DOM9ITV', '6', '1', '1', '', '2019-04-11','07:55','08:00','Atualização andon');",
		"insert into usrlog (usr_id,prd_id,opr_id,cty_id, to_usr_id, logdte, fr_logtim, to_logtim, usrobs) values ('DOM9ITV', '19', '1', '1', '', '2019-04-11','08:00','08:05','Manutenção preventiva atividades diarias');",
		"insert into usrlog (usr_id,prd_id,opr_id,cty_id, to_usr_id, logdte, fr_logtim, to_logtim, usrobs) values ('DOM9ITV', '6', '1', '1', '', '2019-04-11','08:06','08:23','Atualização opl ');",
		"insert into usrlog (usr_id,prd_id,opr_id,cty_id, to_usr_id, logdte, fr_logtim, to_logtim, usrobs) values ('DOM9ITV', '6', '1', '1', '', '2019-04-11','08:24','09:30','Maquina desenvolvimento / ajustes');",
		"insert into usrlog (usr_id,prd_id,opr_id,cty_id, to_usr_id, logdte, fr_logtim, to_logtim, usrobs) values ('DOM9ITV', '6', '', '', '', '2019-04-11','09:40','11:30','orçamentos hugo dias ');",
		"insert into usrlog (usr_id,prd_id,opr_id,cty_id, to_usr_id, logdte, fr_logtim, to_logtim, usrobs) values ('DOM9ITV', '32', '1', '1', '', '2019-04-11','12:40','14:13','chamado Alarme gabinete de chaves ');",
		"insert into usrlog (usr_id,prd_id,opr_id,cty_id, to_usr_id, logdte, fr_logtim, to_logtim, usrobs) values ('DOM9ITV', '6', '1', '1', '', '2019-04-11','14:15','14:42','kpi sentinela ');"
	);


   // var_dump($comando);

    foreach($comando as $cmd){
        $resultado = $db->query($cmd);
    }

    $resultado = $db->query("SELECT * FROM usrlog WHERE usr_id = 'DOM9ITV' and log_id > 227");

    while($row = $resultado->fetchArray(SQLITE3_ASSOC)){
        var_dump($row) ;
        print '<hr>';
        
    }
?>
