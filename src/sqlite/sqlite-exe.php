<?php
    //Create novo objeto SQLite3
    $db = new SQLite3('apontamentos.db');

    $c_tblusr = "
        CREATE TABLE IF NOT EXISTS usrsys (
            usr_id TEXT PRIMARY KEY,
            usrpsw TEXT NOT NULL,
            adddte DATETIME,
            lstupd_dte DATETIME,
            lstlog_dte DATETIME
        );
    ";

    $db->exec($c_tblusr);
    
    $c_tblprd = "
        CREATE TABLE IF NOT EXISTS usrprd (
            prd_id INTEGER PRIMARY KEY,
            prdnme TEXT NOT NULL,
            rspare TEXT
        );
    ";
    $db->exec($c_tblprd);

    $c_tblcty= "
        CREATE TABLE IF NOT EXISTS usrcty (
            cty_id INTEGER PRIMARY KEY,
            ctynme TEXT NOT NULL,
            ctysgl TEXT
        );
    ";
    $db->exec($c_tblcty);

    $c_tblopr= "
        CREATE TABLE IF NOT EXISTS usropr (
            opr_id INTEGER PRIMARY KEY,
            oprnme TEXT NOT NULL,
            cty_id NOT NULL
        );
    ";
    $db->exec($c_tblopr);


    $c_tbllog = "
        CREATE TABLE IF NOT EXISTS usrlog (
            log_id INTEGER PRIMARY KEY,
            usr_id TEXT,
            prd_id INTEGER,
            opr_id INTEGER,
            cty_id INTEGER,
            to_usr_id TEXT,
            logdte TEXT,
            fr_logtim TEXT,
            to_logtim TEXT,
            usrobs TEXT
        );
    ";
    $db->exec($c_tbllog);

    $c_tbllog = "
        CREATE TABLE IF NOT EXISTS usraut (
            authid INTEGER PRIMARY KEY,
            usr_id TEXT,
            usrrol TEXT
        );
    ";
    $db->exec($c_tbllog);

    
                    
?>