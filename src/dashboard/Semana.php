<?php
    class Semana {
        public $data_limite;
        public $semana_fechada;
        public $db;

        public function __construct()
        {
            $this->db = new SQLite3('../sqlite/apontamentos.db');

            $select_semana_indisponivel = "
                select 
                    (yy.yr_dte||'-'||mn_dte||'-'||dy_dte||' 12:00') as fechamento,
                    strftime('%W',(yy.yr_dte||'-'||mn_dte||'-'||dy_dte||' 12:00')) - 1 as semana_indisponivel
                from
                    yr_idx yy
                WHERE 
                    strftime('%W', (yy.yr_dte||'-'||mn_dte||'-'||dy_dte)) = strftime('%W', 'now')
                    and wk_day = 'terça-feira'
            ";

            $comando_semana_fechamento = $this->db->prepare($select_semana_indisponivel);
            $resultado_semana_fechamento = $comando_semana_fechamento->execute();

            while($rows = $resultado_semana_fechamento->fetchArray(SQLITE3_ASSOC)){
                $this->data_limite = $rows['fechamento'];
                $this->semana_fechada = $rows['semana_indisponivel'];
            }

        }
        public function usuarios(){
            $select_usuarios_cadastrados = "
                select 
                    * 
                from 
                    usrsys
                ORDER BY
                    usrnme
            ";
            $comando_usuarios_cadastrados = $this->db->prepare($select_usuarios_cadastrados);
            $resultado_usuarios_cadastrados = $comando_usuarios_cadastrados->execute();

            $retorno = array();
            while($rows = $resultado_usuarios_cadastrados->fetchArray(SQLITE3_ASSOC)){
                $array_associativo = array (
                    "usr_id"=> $rows['usr_id'],
                    "usrnme"=> $rows['usrnme']
                );
                $retorno[] = $array_associativo;
            }

            return $retorno;
        }

        public function datas($week){
            $select_datas_semana_fechada = "
                select 
                    (yr_dte||'-'||mn_dte||'-'||dy_dte) as dias
                from 
                    yr_idx
                where
                    strftime('%W', (yr_dte||'-'||mn_dte||'-'||dy_dte)) =  :week
                    and wk_day not in ('sábado','domingo')
            ";
            $comando_datas_semana_fechada = $this->db->prepare($select_datas_semana_fechada);
            $comando_datas_semana_fechada->bindValue('week', $week);
            $resultado_datas_semana_fechada = $comando_datas_semana_fechada->execute();

            $retorno = array();
            while($rows = $resultado_datas_semana_fechada->fetchArray(SQLITE3_ASSOC)){
                $retorno[] = $rows['dias'];
            }

            return $retorno;
        }

        public function checarDia($usr_id, $data)
        {
            $select_check_dia = "
            select 
                usr_id,
                count(log_id) as lancamentos,
                sum((julianday(to_logtim) - julianday(fr_logtim)) *24) as apontamentos
            from 
                usrlog
            where
                usr_id = :usr_id
                and logdte = :data
            ";

            $comando_check_dia = $this->db->prepare($select_check_dia);
            $comando_check_dia->bindValue('usr_id', $usr_id);
            $comando_check_dia->bindValue('data', $data);
            $resultado_check_dia = $comando_check_dia->execute();

            $retorno_a = false;
            $retorno_i = false;
            $retorno = false;

            while($rows = $resultado_check_dia->fetchArray(SQLITE3_ASSOC)){
                if(($rows['lancamentos'] > 0) && ($rows['apontamentos'] > 0)){
                    $retorno_a = 1;
                }
            }

            $select_check_dia_indisponivel = "
            select 
                usr_id,
                count(ind_id) as lancamentos,
                sum((julianday(to_logtim) - julianday(fr_logtim)) *24) as apontamentos
            from 
                usrind
            where
                usr_id = :usr_id
                and inddte = :data
            ";

            $comando_check_dia_indisponivel = $this->db->prepare($select_check_dia_indisponivel);
            $comando_check_dia_indisponivel->bindValue('usr_id', $usr_id);
            $comando_check_dia_indisponivel->bindValue('data', $data);
            $resultado_check_dia_indisponivel = $comando_check_dia_indisponivel->execute();

            while($rows = $resultado_check_dia_indisponivel->fetchArray(SQLITE3_ASSOC)){
                if(($rows['lancamentos'] > 0)){
                    $retorno_i = 2;
                }
            }

            if($retorno_a ){
                $retorno = $retorno_a;
            }else if($retorno_i) {
                $retorno = $retorno_i;
            }else{
                $retorno = 0;
            }

            return $retorno;

        }

        public function checarDiaHoras($usr_id, $data)
        {
            $select_check_dia = "
            select 
                usr_id,
                count(log_id) as lancamentos,
                sum((julianday(to_logtim) - julianday(fr_logtim)) *24) as apontamentos
            from 
                usrlog
            where
                usr_id = :usr_id
                and logdte = :data
            ";

            $comando_check_dia = $this->db->prepare($select_check_dia);
            $comando_check_dia->bindValue('usr_id', $usr_id);
            $comando_check_dia->bindValue('data', $data);
            $resultado_check_dia = $comando_check_dia->execute();

            $retorno_a = 0;
            $retorno_i = 0;
            $retorno = 0;

            while($rows = $resultado_check_dia->fetchArray(SQLITE3_ASSOC)){
                if(($rows['lancamentos'] > 0) && ($rows['apontamentos'] > 0)){
                    $retorno_a = $rows['apontamentos'];
                }
            }

            $select_check_dia_indisponivel = "
            select 
                usr_id,
                count(ind_id) as lancamentos,
                sum((julianday(to_logtim) - julianday(fr_logtim)) *24) as apontamentos
            from 
                usrind
            where
                usr_id = :usr_id
                and inddte = :data
            ";

            $comando_check_dia_indisponivel = $this->db->prepare($select_check_dia_indisponivel);
            $comando_check_dia_indisponivel->bindValue('usr_id', $usr_id);
            $comando_check_dia_indisponivel->bindValue('data', $data);
            $resultado_check_dia_indisponivel = $comando_check_dia_indisponivel->execute();

            while($rows = $resultado_check_dia_indisponivel->fetchArray(SQLITE3_ASSOC)){
                if(($rows['lancamentos'] > 0)){
                    $retorno_i = $rows['apontamentos'];
                }
            }

            $retorno  = $retorno_a + $retorno_i;

            return $retorno;

        }

        public function formatarHora($horario)
        {
            $hora = floor($horario);
            $minuto = floor(($horario - floor($horario)) * 60);
            $timestamp = mktime($hora, $minuto);
            return date('H:i', $timestamp);
        }
        
    }
?>
