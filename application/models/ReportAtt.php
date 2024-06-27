<?php

class ReportAtt extends CI_Model
{

    // public function __construct() {
    //     parent::__construct();
    //     // $this->access_db = $this->load->database('default', TRUE);
    //     $this->load->database();
    // }

    public function get_data_attparam($params)
    {
        $query = $this->db->query(
            "SELECT * FROM ATTPARAM WHERE PARANAME = '$params'"
        );

        return $query->result();
    }

    public function get_data_leaveclass()
    {
        $query = $this->db->query(
            "SELECT * FROM LEAVECLASS1 WHERE LEAVEID = 999"
        );

        return $query->result()[0];
    }

    public function get_userinfo_byid($userid)
    {
        $query = $this->db->query(
            "SELECT * FROM USERINFO WHERE USERID = " .$userid
        );

        return $query->result();
    }

    public function check_user_tmp_sch($user_id, $dt)
    {
        $query = $this->db->query(
            "SELECT * FROM USER_TEMP_SCH WHERE FORMAT(COMETIME, 'yyyy-MM-dd') = '$dt' and USERID = $user_id AND SCHCLASSID > 0"
        );

        return $query->result();
    }

    public function get_schclass_byid($schid)
    {
        $query = $this->db->query(
            "SELECT * FROM SCHCLASS WHERE SCHCLASSID IN ($schid)"
        );

        return $query->result()[0];
    }

    public function get_data_user_of_run_by_userid($user_id)
    {
        $query = $this->db->query(
            "SELECT * FROM USER_OF_RUN WHERE USERID = ".$user_id
        );
        

        return $query->result();
    }

    public function get_data_num_run($num_run_id)
    {
        $query = $this->db->query(
            "SELECT * FROM NUM_RUN WHERE NUM_RUNID = " . $num_run_id
        );

        return $query->result();
    }

    public function get_data_num_run_deil($num_run_id, $sdays)
    {
        $query = $this->db->query(
            "SELECT * FROM NUM_RUN_DEIL WHERE NUM_RUNID = $num_run_id AND SDAYS = ".$sdays
        );

        return $query->result();
    }

    public function get_checkinout_bydate($userid, $date)
    {
        $query = $this->db->query(
            "SELECT * FROM CHECKINOUT WHERE USERID = $userid AND FORMAT(CHECKTIME, 'yyyy-MM-dd') = '$date'"
        );

        return $query->result();
    }

    public function get_holidays($date) 
    {
        $query = $this->db->query(
            "SELECT * FROM HOLIDAYS WHERE FORMAT(STARTTIME, 'yyyy-MM-dd') = '$date'"
        );

        return $query->result();
    }

    public function get_all_user() 
    {
        $query = $this->db->query(
            "SELECT USERINFO.USERID, USERINFO.Name, USERINFO.Badgenumber FROM USERINFO INNER JOIN USER_OF_RUN ON USERINFO.USERID = USER_OF_RUN.USERID GROUP BY USERINFO.USERID, USERINFO.Name, USERINFO.Badgenumber"
        );

        return $query->result();
    }

    public function get_user_used_class($userid)
    {
        $query = $this->db->query(
            "SELECT * FROM UserUsedSClasses WHERE UserId = ".$userid
        );

        return $query->result();
    }

    public function get_checkinout_byhour($userid, $start_time, $end_time) {
        $query = $this->db->query(
            "SELECT * FROM CHECKINOUT WHERE USERID = $userid AND FORMAT(CHECKTIME, 'yyyy-MM-dd HH:mm:ss') BETWEEN '$start_time' AND '$end_time'"
        );

        return $query->result();
    }

    public function get_departement($column = null, $value = null) {

        $this->db->select('id, name, parent');
        $this->db->from('tbdepartements');
        // $this->db->where('parent <>', 0);
        
        if ($column) {
            $this->db->where($column, $value);
        }

        $query = $this->db->get();
        return $query->result_array();
    }

}