<?php

class Report extends CI_Model
{

    // public function __construct() {
    //     $this->load->database();
    // }

    public function get_checkinout()
    {
        $query = $this->db->query('SELECT * FROM t_checkinout where date_format(check_time, "%Y-%m-%d") between "2012-01-01" and "2012-12-30"');
        return $query->result();
    }

    public function get_time_work_on_shift($user_id, $sdays) 
    {
        $query = $this->db->query('select 
                                        us_of_run.user_id, usr.badge_number, dpt.name departement, 
                                        us_of_run.num_run_id, num_run.name num_run_name, num_run.name shift_name,
                                        run_deil.start_time start_work, run_deil.end_time end_work
                                    from 
                                        db_simple_att.t_user_of_run us_of_run 
                                    join 
                                        db_simple_att.t_user_info usr on usr.id = us_of_run.user_id
                                    join 
                                        db_simple_att.t_departement dpt on usr.departement_id
                                    join 
                                        db_simple_att.t_num_run num_run on us_of_run.num_run_id = num_run.id
                                    join 
                                        db_simple_att.t_num_run_deil run_deil on num_run.id = run_deil.num_run_id and run_deil.sdays = '.$sdays.'
                                    where 
                                        us_of_run.user_id = ' . $user_id);
        return $query->result();
    }

    public function get_data_attparam($params) 
    {
        $query = $this->db->query(
            'SELECT * FROM ATTPARAM WHERE PARANAME = "'.$params.'"'
        );

        return $query->result()[0];
    }

    public function get_data_leaveclass()
    {
        $query = $this->db->query(
            'SELECT * FROM t_leaveclass1 WHERE LEAVEID = 999'
        );

        return $query->result()[0];
    }

    public function check_user_tmp_sch($user_id, $dt)
    {
        $query = $this->db->query(
            'SELECT * FROM t_user_temp_sch WHERE USERID = '.$user_id.' AND DATE_FORMAT(COMETIME, "%Y-%m-%d") = "'.$dt.'" AND SCHCLASSID > 0'
        );

        return $query->result();
    }

    public function get_schclass_byid($schid)
    {
        $query = $this->db->query(
            'SELECT * FROM t_schclass WHERE id = ' . $schid
        );

        return $query->result()[0];
    }

    public function get_data_user_of_run_by_userid($user_id)
    {
        $query = $this->db->query(
            'SELECT * FROM t_user_of_run WHERE user_id = '.$user_id
        );

        return $query->result();
    }

    public function get_data_num_run($num_run_id)
    {
        $query = $this->db->query(
            'SELECT * FROM t_num_run WHERE id = ' . $num_run_id
        );

        return $query->result();
    }

    public function get_data_num_run_deil($num_run_id, $sdays)
    {
        $query = $this->db->query(
            'SELECT * FROM t_num_run_deil WHERE num_run_id = '.$num_run_id.' AND sdays = '.$sdays
        );

        return $query->result();
    }

    public function get_userinfo_byid($userid)
    {
        $query = $this->db->query(
            'SELECT * FROM t_user_info WHERE id = ' .$userid
        );

        return $query->result();
    }

    public function get_checkinout_bydate($userid, $date)
    {
        $query = $this->db->query(
            'SELECT * FROM t_checkinout where user_id = '.$userid.' AND date_format(check_time, "%Y-%m-%d") = "'.$date.'"'
        );

        return $query->result();
    }

} 