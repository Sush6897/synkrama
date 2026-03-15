<?php
class Auth extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $this->load->view('login.html');
    }

    public function register_view()
    {
        $this->load->view('register.html');
    }

    public function check_email()
    {
        $email = $this->input->post('email');
        if (strpos($email, 'synkrama@gmail') !== false) {
            echo json_encode(['status' => false, 'message' => 'This email domain is restricted.']);
            return;
        }
        if($this->User_model->checkEmail($email)){
            echo json_encode(['status' => false, 'message' => 'Email already exists']);
        } else {
            echo json_encode(['status' => true, 'message' => 'Email available']);
        }
    }

    public function register()
    {
        $this->form_validation->set_rules('first_name', 'First Name', 'required');
        $this->form_validation->set_rules('last_name', 'Last Name', 'required');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
        
        $user_type = $this->input->post('user_type');
        if (!in_array($user_type, ['Employee', 'Dealer'])) {
            echo json_encode(['status' => false, 'message' => 'Invalid User Type']);
            return;
        }

        if($this->form_validation->run() == FALSE){
            echo json_encode(['status' => false, 'message' => validation_errors()]);
            return;
        }

        $email = $this->input->post('email');
        if (strpos($email, 'synkrama@gmail') !== false) {
            echo json_encode(['status' => false, 'message' => 'This email domain is restricted.']);
            return;
        }

        if($this->User_model->checkEmail($email)){
            echo json_encode(['status' => false, 'message' => 'Email already exists']);
            return;
        }

        $data = [
            'first_name' => $this->input->post('first_name'),
            'last_name' => $this->input->post('last_name'),
            'email' => $email,
            'password' => md5($this->input->post('password')),
            'user_type' => $user_type,
            'is_first_login' => 1
        ];

        $this->User_model->register($data);

        echo json_encode(['status' => true, 'message' => 'Registration Successful']);
    }

    public function login()
    {
        $email = $this->input->post('email');
        $password = md5($this->input->post('password'));

        $user = $this->User_model->login($email, $password);

        if($user)
        {
            $this->session->set_userdata('user', $user);
            
            $redirect_url = '';
            if (strtolower($user->user_type) == 'dealer') {
                if ($user->is_first_login == 1) {
                    $redirect_url = site_url('dealer/complete_profile');
                } else {
                    $redirect_url = site_url('dealer'); // Dealer dashboard
                }
            } else {
                $redirect_url = site_url('employee'); // Employee dashboard
            }

            echo json_encode([
                'status' => true,
                'redirect' => $redirect_url
            ]);
        }
        else
        {
            echo json_encode(['status' => false, 'message' => 'Invalid email or password']);
        }
    }

    public function logout()
    {
        $this->session->sess_destroy();
        redirect('auth');
    }
}