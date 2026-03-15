<?php
class User_model extends CI_Model {

    public function register($data)
    {
        $this->db->insert('users',$data);
        return $this->db->insert_id();
    }

    public function checkEmail($email)
    {
        return $this->db->where('email',$email)->get('users')->row();
    }

    public function login($email,$password)
    {
        return $this->db->where('email',$email)
                        ->where('password',$password)
                        ->get('users')
                        ->row();
    }

    public function complete_first_login($user_id)
    {
        $this->db->where('id', $user_id)->update('users', ['is_first_login' => 0]);
    }

    public function get_by_id($id)
    {
        return $this->db->where('id', $id)->get('users')->row();
    }
}