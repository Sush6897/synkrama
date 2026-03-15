<?php
class Dealer_model extends CI_Model {

    public function insert_details($data)
    {
        return $this->db->insert('dealer_details', $data);
    }

    public function update_details($user_id, $data)
    {
        return $this->db->where('user_id', $user_id)->update('dealer_details', $data);
    }

    public function get_details($user_id)
    {
        return $this->db->where('user_id', $user_id)->get('dealer_details')->row();
    }

    public function getDealers($limit, $start, $zip = null)
    {
        $this->db->select('users.id, users.first_name, users.last_name, users.email, dealer_details.city, dealer_details.state, dealer_details.zip_code');
        $this->db->from('users');
        $this->db->join('dealer_details', 'dealer_details.user_id = users.id', 'left');
        $this->db->where('users.user_type', 'dealer');
        if (!empty($zip)) {
            $this->db->where('dealer_details.zip_code', $zip);
        }
        $this->db->limit($limit, $start);
        return $this->db->get()->result();
    }

    public function getTotalDealers($zip = null)
    {
        $this->db->from('users');
        $this->db->join('dealer_details', 'dealer_details.user_id = users.id', 'left');
        $this->db->where('users.user_type', 'dealer');
        if (!empty($zip)) {
            $this->db->where('dealer_details.zip_code', $zip);
        }
        return $this->db->count_all_results();
    }
}
