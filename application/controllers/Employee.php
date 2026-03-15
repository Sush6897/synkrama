<?php
class Employee extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->model('Dealer_model');
        
        $user = $this->session->userdata('user');
        if (!$user || strtolower($user->user_type) != 'employee') {
            redirect('auth');
        }
    }

    public function index()
    {
        $this->load->library('pagination');

        $zip = $this->input->get('zip');

        $config['base_url'] = site_url('employee/index');
        $config['total_rows'] = $this->Dealer_model->getTotalDealers($zip);
        $config['per_page'] = 5;
        $config['page_query_string'] = TRUE;
        $config['query_string_segment'] = 'per_page'; // using query string instead of uri segment
        
        if (!empty($zip)) {
            $config['first_url'] = $config['base_url'].'?zip='.$zip;
            $config['suffix'] = '&zip='.$zip;
        }

        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li class="page-item">';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="page-item active"><a class="page-link" href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['next_tag_open'] = '<li class="page-item">';
        $config['next_tag_close'] = '</a></li>';
        $config['prev_tag_open'] = '<li class="page-item">';
        $config['prev_tag_close'] = '</li>';
        $config['first_tag_open'] = '<li class="page-item disabled">';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li class="page-item">';
        $config['last_tag_close'] = '</a></li>';
        $config['attributes'] = array('class' => 'page-link');

        $this->pagination->initialize($config);

        $page = $this->input->get('per_page') ? $this->input->get('per_page') : 0;

        $data['dealers'] = $this->Dealer_model->getDealers($config['per_page'], $page, $zip);
        $data['zip'] = $zip;
        
        $this->load->view('employee_dashboard.html', $data);
    }

    public function edit_dealer($user_id)
    {
        $target_user = $this->User_model->get_by_id($user_id);
        if (!$target_user || strtolower($target_user->user_type) != 'dealer') {
            show_404();
            return;
        }

        $data['user_id'] = $user_id;
        $data['dealer'] = $this->Dealer_model->get_details($user_id);
        $this->load->view('edit_dealer.html', $data);
    }
    
    public function update_dealer()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('user_id', 'User ID', 'required|numeric');
        $this->form_validation->set_rules('city', 'City', 'required');
        $this->form_validation->set_rules('state', 'State', 'required');
        $this->form_validation->set_rules('zip', 'Zip', 'required|numeric|exact_length[6]');

        if($this->form_validation->run() == FALSE){
            echo json_encode(['status' => false, 'message' => validation_errors()]);
            return;
        }

        $user_id = $this->input->post('user_id');
        $target_user = $this->User_model->get_by_id($user_id);
        
        if (!$target_user || strtolower($target_user->user_type) != 'dealer') {
            echo json_encode(['status' => false, 'message' => 'Invalid Dealer ID']);
            return;
        }
        
        $data = [
            'city' => $this->input->post('city'),
            'state' => $this->input->post('state'),
            'zip_code' => $this->input->post('zip')
        ];

        if ($this->Dealer_model->get_details($user_id)) {
            $this->Dealer_model->update_details($user_id, $data);
        } else {
            $data['user_id'] = $user_id;
            $this->Dealer_model->insert_details($data);
        }

        // Also mark as first login complete if it wasn't already
        if ($target_user->is_first_login == 1) {
            $this->User_model->complete_first_login($user_id);
        }

        echo json_encode(['status' => true, 'message' => 'Dealer details updated', 'redirect' => site_url('employee')]);
    }
}