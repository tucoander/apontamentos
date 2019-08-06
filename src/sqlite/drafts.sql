--comando de calculo das horas apontadas
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


-- SQLite
select 
    ul.usr_id,
    uf.funidx,
    ul.logdte,
    (yi.shftim * (1-0.146)) as tempo
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
        inner join yr_idx yi 
        on (ul.logdte = (yi.yr_dte||'-'||yi.mn_dte||'-'||yi.dy_dte) )
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
        ul.usr_id,
        uf.funidx,
        ul.logdte,
        yi.shftim
