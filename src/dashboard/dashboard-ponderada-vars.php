<?php
    $labels = array();
    $data = array();
    $data_perc = array();
    
    $labels_script;
    $data_script;
    
    $soma_hora = 0;
    $tamanho;
    
    $_fr_adddte = '';
    $_to_adddte = '';
    
    $table = '';
    $msg = '';
    $final = '';
    $users = 0;
    $perc_fadiga = 0.146;
    $disponibilidade = '';
    $disponibilidade_total = 0;

    // disponibilidade x disponibilidade com index filtro de data e usuário
    $sel_dsp_1 = "
        SELECT
            sum(shftim) as disponibilidade,
            sum(indice) as dispon_index
        FROM
            (
            SELECT
                (yr_dte || '-' || mn_dte || '-' || dy_dte) as data,
                ul.usr_id,
                yi.shftim,
                uf.funidx,
                (yi.shftim * uf.funidx) as indice
            FROM
                yr_idx yi
                inner join usrlog ul 
                on ((yr_dte || '-' || mn_dte || '-' || dy_dte) = ul.logdte)
                inner join funusr fu 
                on (ul.usr_id = fu.usr_id) 
                inner join usrfun uf 
                on (fu.fun_id = uf.fun_id)
            WHERE
                wk_day not in ('sábado', 'domingo')
                and ul.logdte between :fr_adddte and :to_adddte
                and ul.usr_id = :usr_id 
            GROUP BY
                (yr_dte || '-' || mn_dte || '-' || dy_dte),
                ul.usr_id,
                yi.shftim,
                uf.funidx,
                (yi.shftim * uf.funidx)
            UNION
            SELECT
                (yr_dte || '-' || mn_dte || '-' || dy_dte) as data,
                ui.usr_id,
                yi.shftim,
                uf.funidx,
                (yi.shftim * uf.funidx) as indice
            FROM
                yr_idx yi
                inner join usrind ui
                on ((yr_dte || '-' || mn_dte || '-' || dy_dte) = ui.inddte)
                inner join funusr fu 
                on (ui.usr_id = fu.usr_id) 
                inner join usrfun uf 
                on (fu.fun_id = uf.fun_id)
            WHERE
                wk_day not in ('sábado', 'domingo')
                and ui.inddte between :fr_adddte and :to_adddte
                and ui.usr_id = :usr_id 
            GROUP BY
                (yr_dte || '-' || mn_dte || '-' || dy_dte),
                ui.usr_id,
                yi.shftim,
                uf.funidx,
                (yi.shftim * uf.funidx)
            )
        ";
    // operações agrupada com index filtro de data e usuário
    
    $sel_app_1 = "
    select 
        op.oprnme,
        count(distinct ul.usr_id),
        sum((julianday(to_logtim) - julianday(fr_logtim))*24 * uf.funidx) as diff_jd
    from 
        usrlog ul 
        inner join usrprd pr 
        on (ul.prd_id = pr.prd_id)
        inner join usropr op 
        on (ul.opr_id = op.opr_id) 
        inner join funusr fu 
        on (ul.usr_id = fu.usr_id) 
        inner join usrfun uf 
        on (fu.fun_id = uf.fun_id) 
    where 
        logdte between :fr_adddte and :to_adddte
        and ul.usr_id = :usr_id 
    group by
        op.oprnme
    order by 
        op.opr_id
    ";
    
    // indisponibilidade com index filtro de data e usuário
    $sel_ind_1 = "
        SELECT IFNULL(sum((julianday(to_logtim) - julianday(fr_logtim))*24),0) * uf.funidx as hr_inds
            from usrind ul
                inner join funusr fu 
            on (ul.usr_id = fu.usr_id) 
            inner join usrfun uf 
            on (fu.fun_id = uf.fun_id) 
        where inddte between :fr_adddte and :to_adddte
        and ul.usr_id = :usr_id 
        ";
    // disponibilidade x disponibilidade com index filtro de data
    $sel_dsp_2 = "
        SELECT
            sum(shftim) as disponibilidade,
            sum(indice) as dispon_index
        FROM
            (
            SELECT
                (yr_dte || '-' || mn_dte || '-' || dy_dte) as data,
                ul.usr_id,
                yi.shftim,
                uf.funidx,
                (yi.shftim * uf.funidx) as indice
            FROM
                yr_idx yi
                inner join usrlog ul 
                on ((yr_dte || '-' || mn_dte || '-' || dy_dte) = ul.logdte)
                inner join funusr fu 
                on (ul.usr_id = fu.usr_id) 
                inner join usrfun uf 
                on (fu.fun_id = uf.fun_id)
            WHERE
                wk_day not in ('sábado', 'domingo')
                and ul.logdte between :fr_adddte and :to_adddte
            GROUP BY
                (yr_dte || '-' || mn_dte || '-' || dy_dte),
                ul.usr_id,
                yi.shftim,
                uf.funidx,
                (yi.shftim * uf.funidx)
            UNION
            SELECT
                (yr_dte || '-' || mn_dte || '-' || dy_dte) as data,
                ui.usr_id,
                yi.shftim,
                uf.funidx,
                (yi.shftim * uf.funidx) as indice
            FROM
                yr_idx yi
                inner join usrind ui
                on ((yr_dte || '-' || mn_dte || '-' || dy_dte) = ui.inddte)
                inner join funusr fu 
                on (ui.usr_id = fu.usr_id) 
                inner join usrfun uf 
                on (fu.fun_id = uf.fun_id)
            WHERE
                wk_day not in ('sábado', 'domingo')
                and ui.inddte between :fr_adddte and :to_adddte
            GROUP BY
                (yr_dte || '-' || mn_dte || '-' || dy_dte),
                ui.usr_id,
                yi.shftim,
                uf.funidx,
                (yi.shftim * uf.funidx)
            )
            ";

    $sel_app_2 = "
        select 
            op.oprnme,
            count(distinct ul.usr_id),
            sum((julianday(to_logtim) - julianday(fr_logtim))*24 * uf.funidx) as diff_jd
        from 
            usrlog ul 
            inner join usrprd pr 
            on (ul.prd_id = pr.prd_id)
            inner join usropr op 
            on (ul.opr_id = op.opr_id) 
            inner join funusr fu 
            on (ul.usr_id = fu.usr_id) 
            inner join usrfun uf 
            on (fu.fun_id = uf.fun_id) 
        where 
            logdte between :fr_adddte and :to_adddte
        group by
            op.oprnme
        order by 
            op.opr_id
        ";

    $sel_ind_2 = "
        SELECT IFNULL(sum((julianday(to_logtim) - julianday(fr_logtim))*24),0) * uf.funidx as hr_inds
            from usrind ul
             inner join funusr fu 
            on (ul.usr_id = fu.usr_id) 
            inner join usrfun uf 
            on (fu.fun_id = uf.fun_id) 
        where inddte between :fr_adddte and :to_adddte
        ";

    $sel_dsp_3 = "
        SELECT
            sum(shftim) as disponibilidade,
            sum(indice) as dispon_index
        FROM
            (
            SELECT
                (yr_dte || '-' || mn_dte || '-' || dy_dte) as data,
                ul.usr_id,
                yi.shftim,
                uf.funidx,
                (yi.shftim * uf.funidx) as indice
            FROM
                yr_idx yi
                inner join usrlog ul 
                on ((yr_dte || '-' || mn_dte || '-' || dy_dte) = ul.logdte)
                inner join funusr fu 
                on (ul.usr_id = fu.usr_id) 
                inner join usrfun uf 
                on (fu.fun_id = uf.fun_id)
            WHERE
                wk_day not in ('sábado', 'domingo')
                
            GROUP BY
                (yr_dte || '-' || mn_dte || '-' || dy_dte),
                ul.usr_id,
                yi.shftim,
                uf.funidx,
                (yi.shftim * uf.funidx)
            UNION
            SELECT
                (yr_dte || '-' || mn_dte || '-' || dy_dte) as data,
                ui.usr_id,
                yi.shftim,
                uf.funidx,
                (yi.shftim * uf.funidx) as indice
            FROM
                yr_idx yi
                inner join usrind ui
                on ((yr_dte || '-' || mn_dte || '-' || dy_dte) = ui.inddte)
                inner join funusr fu 
                on (ui.usr_id = fu.usr_id) 
                inner join usrfun uf 
                on (fu.fun_id = uf.fun_id)
            WHERE
                wk_day not in ('sábado', 'domingo')
                
            GROUP BY
                (yr_dte || '-' || mn_dte || '-' || dy_dte),
                ui.usr_id,
                yi.shftim,
                uf.funidx,
                (yi.shftim * uf.funidx)
            )
        ";
    $sel_app_3 = "
        select 
            op.oprnme,
            count(distinct ul.usr_id),
            sum((julianday(to_logtim) - julianday(fr_logtim))*24 * uf.funidx) as diff_jd
        from 
            usrlog ul 
            inner join usrprd pr 
            on (ul.prd_id = pr.prd_id)
            inner join usropr op 
            on (ul.opr_id = op.opr_id) 
            inner join funusr fu 
            on (ul.usr_id = fu.usr_id) 
            inner join usrfun uf 
            on (fu.fun_id = uf.fun_id)
        group by
            op.oprnme
        order by 
            op.opr_id
        ";
    $sel_ind_3 = "
        SELECT IFNULL(sum((julianday(to_logtim) - julianday(fr_logtim))*24),0) * uf.funidx as hr_inds
            from usrind ul
             inner join funusr fu 
            on (ul.usr_id = fu.usr_id) 
            inner join usrfun uf 
            on (fu.fun_id = uf.fun_id) 
        ";

    $sel_dsp_4 = "
        SELECT
            sum(shftim) as disponibilidade,
            sum(indice) as dispon_index
        FROM
            (
            SELECT
                (yr_dte || '-' || mn_dte || '-' || dy_dte) as data,
                ul.usr_id,
                yi.shftim,
                uf.funidx,
                (yi.shftim * uf.funidx) as indice
            FROM
                yr_idx yi
                inner join usrlog ul 
                on ((yr_dte || '-' || mn_dte || '-' || dy_dte) = ul.logdte)
                inner join funusr fu 
                on (ul.usr_id = fu.usr_id) 
                inner join usrfun uf 
                on (fu.fun_id = uf.fun_id)
            WHERE
                wk_day not in ('sábado', 'domingo')
                and substr(ul.logdte, 0,8) = substr(date(),0,8)
            GROUP BY
                (yr_dte || '-' || mn_dte || '-' || dy_dte),
                ul.usr_id,
                yi.shftim,
                uf.funidx,
                (yi.shftim * uf.funidx)
            UNION
            SELECT
                (yr_dte || '-' || mn_dte || '-' || dy_dte) as data,
                ui.usr_id,
                yi.shftim,
                uf.funidx,
                (yi.shftim * uf.funidx) as indice
            FROM
                yr_idx yi
                inner join usrind ui
                on ((yr_dte || '-' || mn_dte || '-' || dy_dte) = ui.inddte)
                inner join funusr fu 
                on (ui.usr_id = fu.usr_id) 
                inner join usrfun uf 
                on (fu.fun_id = uf.fun_id)
            WHERE
                wk_day not in ('sábado', 'domingo')
                and substr(ui.inddte, 0,8) = substr(date(),0,8)
            GROUP BY
                (yr_dte || '-' || mn_dte || '-' || dy_dte),
                ui.usr_id,
                yi.shftim,
                uf.funidx,
                (yi.shftim * uf.funidx)
            )
        ";

    $sel_app_4 = "
        select 
            op.oprnme,
            count(distinct ul.usr_id),
            sum((julianday(to_logtim) - julianday(fr_logtim))*24 * uf.funidx) as diff_jd
        from 
            usrlog ul 
            inner join usrprd pr 
            on (ul.prd_id = pr.prd_id)
            inner join usropr op 
            on (ul.opr_id = op.opr_id) 
            inner join funusr fu 
            on (ul.usr_id = fu.usr_id) 
            inner join usrfun uf 
            on (fu.fun_id = uf.fun_id) 
        where 
            logdte between (select 
                                min(logdte) 
                                from 
                                    usrlog 
                                where 
                                    substr(logdte, 0,8) = substr(date(),0,8)) 
            and (select 
                    max(logdte) 
                    from 
                        usrlog 
                    where 
                        substr(logdte, 0,8) = substr(date(),0,8))
        group by
            op.oprnme
        order by 
            op.opr_id
        ";
        
    $sel_ind_4 = "
        SELECT IFNULL(sum((julianday(to_logtim) - julianday(fr_logtim))*24),0) * uf.funidx as hr_inds
            from usrind ul
             inner join funusr fu 
            on (ul.usr_id = fu.usr_id) 
            inner join usrfun uf 
            on (fu.fun_id = uf.fun_id) 
        where inddte between (select min(logdte)
                                from usrlog where substr(logdte, 0,8) = substr(date(),0,8))
                        and (select max(logdte)
                        from usrlog where substr(logdte, 0,8) = substr(date(),0,8));
        ";
?>