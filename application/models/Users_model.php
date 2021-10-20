<?php

class Users_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function add($user) {
        $this->db->insert('users', $user);
    }

    public function get($id, $columns = '*') {
        return $this->db->select($columns)->from('users')->where('sso_id', $id)->get()->row_array(0);
    }

    public function exists($id) {
        return $this->get($id, 'sso_id') !== NULL;
    }

}

