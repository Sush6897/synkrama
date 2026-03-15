<?php
class Dealer extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->model('Dealer_model');
        
        $user = $this->session->userdata('user');
        if (!$user || strtolower($user->user_type) != 'dealer') {
            redirect('auth');
        }
    }

    public function index()
    {
        $user = $this->session->userdata('user');
        if ($user->is_first_login == 1) {
            redirect('dealer/complete_profile');
        }

        $data['details'] = $this->Dealer_model->get_details($user->id);
        $this->load->view('dealer_dashboard.html', $data);
    }

    public function complete_profile()
    {
        $user = $this->session->userdata('user');
        if ($user->is_first_login == 0) {
            redirect('dealer');
        }
        $this->load->view('dealer_complete_profile.html');
    }

    public function updateProfile()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('city', 'City', 'required');
        $this->form_validation->set_rules('state', 'State', 'required');
        $this->form_validation->set_rules('zip', 'Zip', 'required|numeric|exact_length[6]');

        if($this->form_validation->run() == FALSE){
            echo json_encode(['status' => false, 'message' => validation_errors()]);
            return;
        }

        $user = $this->session->userdata('user');
        
        $data = [
            'user_id' => $user->id,
            'city' => $this->input->post('city'),
            'state' => $this->input->post('state'),
            'zip_code' => $this->input->post('zip')
        ];

        // Check if details already exist, insert or update
        if ($this->Dealer_model->get_details($user->id)) {
            $this->Dealer_model->update_details($user->id, $data);
        } else {
            $this->Dealer_model->insert_details($data);
        }

        if ($user->is_first_login == 1) {
            $this->User_model->complete_first_login($user->id);
            // Update session data
            $user->is_first_login = 0;
            $this->session->set_userdata('user', $user);
        }

        echo json_encode(['status' => true, 'message' => 'Profile Updated', 'redirect' => site_url('dealer')]);
    }

    // Allow Dealer to edit city, state, zip
    public function edit()
    {
        $user = $this->session->userdata('user');
        $data['dealer'] = $this->Dealer_model->get_details($user->id);
        $this->load->view('edit_dealer.html', $data);
    }
}