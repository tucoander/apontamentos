<?php
    // incluindo a barra de navegação padrão
    include('../template/template-barra.php');
    // incluindo a base de dados 
    $db = new SQLite3('../sqlite/apontamentos.db');

    //variaveis globais
    $por_fadiga = 0.146;

    // criando uma view
    $sel_view = "
    CREATE TEMP VIEW IF NOT EXISTS apontamento_calculado 
    AS 
    select base.form_data,
            base.shftim,
            base.shftim *(1- '.$por_fadiga.') as shftim_fadiga,
            base.usr_id,
            base.funidx,
            IFNULL(sum(julianday(ul.to_logtim) - julianday(ul.fr_logtim)) *24, 0) as apont,
            IFNULL(sum(julianday(ui.to_logtim) - julianday(ui.fr_logtim)) *24, 0) as indis,
            base.shftim *(1- '.$por_fadiga.') * base.funidx as shftim_fadiga_idx,
            IFNULL(sum(julianday(ul.to_logtim) - julianday(ul.fr_logtim)) *24, 0) * base.funidx as apont_idx,
            IFNULL(sum(julianday(ui.to_logtim) - julianday(ui.fr_logtim)) *24, 0) * base.funidx as indis_idx
    from ((select (yr_dte || '-' || mn_dte || '-' || dy_dte) as form_data,
                    shftim,
                    wk_day
                from yr_idx) dias join(select us.usr_id,
                                            us.usrnme,
                                            uf.funidx
                                        from usrsys us
                                        left
                                        join funusr fu
                                        on (us.usr_id = fu.usr_id)
                                        left
                                        join usrfun uf
                                        on (fu.fun_id = uf.fun_id)) usuarios) as base
    left
    join usrlog ul
        on (base.form_data = ul.logdte and base.usr_id = ul.usr_id)
    left
    join usrind ui
        on (base.form_data = ui.inddte and base.usr_id = ui.usr_id)
    group by base.form_data,
            base.shftim,
            base.usr_id,
            base.funidx
    order by base.usr_id
    ";
    try {
        $db->enableExceptions(true);
        $db->exec($sel_view);
    } catch (Exception $e) {
        echo 'Caught exception: ' . $e->getMessage().'<br>';
    }

	// checar se as variaveis foram definidas, ou seja, o que veio pelo post
    if(isset($_POST['fr_adddte']) && isset($_POST['to_adddte']) ){
        // checar se as datas foram preenchidas
        if(!empty($_POST['fr_adddte']) && !empty($_POST['to_adddte'])){

			if(!empty($_POST['usr_id'])){
            
            }
            else{

            }
        }
        // sem datas assume o mes atual
        else{

        }
    }

    $sel_nvw = "
    SELECT * FROM apontamento_calculado
    ";
    try {
        $db->enableExceptions(true);
        $cmd_nvew = $db->prepare($sel_nvw);
        $res_nvew = $cmd_nvew->execute();

        while($row = $res_nvew->fetchArray(SQLITE3_ASSOC)){
            var_dump($row);
        }
    } catch (Exception $e) {
        echo 'Caught exception: ' . $e->getMessage().'<br>';
    }

    
?>
<!-- Conteúdo exibido -->

<?php
    // incluindo a barra de rodapé padrão
    include('../template/template-rodape.php');
?>
    