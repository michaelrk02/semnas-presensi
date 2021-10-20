<?php

class Sessions_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function get($id = NULL, $columns = '*') {
        $this->db->select($columns)->from('sessions');
        if (isset($id)) {
            return $this->db->where('session_id', $id)->get()->row_array(0);
        }
        return $this->db->get()->result_array();
    }

    public function is_scheduled($session) {
        if (!isset($session)) return FALSE;
        return !(($session['time_open'] == 0 && $session['time_close'] == 0) || ($session['time_open'] == 1 && $session['time_close'] == 1));
    }

    public function is_open($session) {
        if (!isset($session)) return FALSE;
        if (!$this->is_scheduled($session)) {
            return !empty($session['time_open']);
        }
        $time = time();
        return $session['time_open'] <= $time && $time < $session['time_close'];
    }

}

