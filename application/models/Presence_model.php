<?php

class Presence_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function create($sso_id, $session_id) {
        $this->db->insert('presence', [
            'sso_id' => $sso_id,
            'session_id' => $session_id,
            'imp_msg' => '',
            'suggestions' => '',
            'speaker_req' => ''
        ]);
    }

    public function get($sso_id, $session_id, $columns = '*') {
        return $this->db->select($columns)->from('presence')->where(['sso_id' => $sso_id, 'session_id' => $session_id])->get()->row_array(0);
    }

    public function set($sso_id, $session_id, $data) {
        $this->db->where(['sso_id' => $sso_id, 'session_id' => $session_id])->update('presence', $data);
    }

}

