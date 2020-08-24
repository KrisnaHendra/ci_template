<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

  public function index()
	{
        $this->form_validation->set_rules('email','Email','trim|required|valid_email',[
            'required' => 'Email tidak boleh kosong!',
            'valid_email' => 'Email tidak benar!'
        ]);
        $this->form_validation->set_rules('password','Password','trim|required',[
            'required' => 'Password tidak boleh kosong!',
        ]);

        if($this->form_validation->run()==FALSE){
            $this->load->view('login');
        }else{
            $this->proses_login();
        }

    }

    public function proses_login(){
        $email = $this->input->post('email');
        $password = $this->input->post('password');

        $user = $this->db->get_where('user',['email'=>$email])->row_array();

        if($user){
            if(password_verify($password,$user['password'])){
                $data = [
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'password' => $user['password'],
                    'logged_in' => TRUE
                ];
                $this->session->set_userdata($data);
                if($user['role_id']==1){
                  redirect('admin');
                }else{
                  redirect('customer');
                }
            } $this->session->set_flashdata('notif','<div class="alert alert-danger alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
            <i class="fa fa-times-circle"></i> Password Salah!
            </div>');
            redirect('auth');
        }else{
            $this->session->set_flashdata('notif','<div class="alert alert-danger alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
            <i class="fa fa-times-circle"></i> Akun Tidak Ditemukan!
            </div>');
            redirect('auth');
        }
    }

    public function register(){
          $this->form_validation->set_rules('name','Name','trim|required');
          $this->form_validation->set_rules('email','Email','trim|required|is_unique[user.email]');
          $this->form_validation->set_rules('password','Password','trim|required|matches[password_confirmation]',[
            'matches' => 'Password not matches'
          ]);
          $this->form_validation->set_rules('password_confirmation','Password','trim|required|matches[password]',[
            'matches' => 'Password not matches'
          ]);

          if($this->form_validation->run()==FALSE){
              $this->load->view('register');
          }else{
              $register=[
                  'name' => $this->input->post('name'),
                  'email' => $this->input->post('email'),
                  'password' => password_hash($this->input->post('password'),PASSWORD_DEFAULT),
                  'role_id' => 2,
                  'aktif' => 1,
                  'created_at' => time(),
                  'updated_at' => time(),
              ];
              $this->db->insert('user',$register);
              $this->session->set_flashdata('notif','<div class="alert alert-success alert-dismissible" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
              <i class="fa fa-check-circle"></i> <strong>Account Has Been Created!</strong>
              </div>');
              redirect('auth');
          }
      }

      public function logout(){
          $this->session->unset_userdata('email');
          $this->session->unset_userdata('password');
          $this->session->set_flashdata('notif','<div class="alert alert-success alert-dismissible" role="alert">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
          <i class="fa fa-check-circle"></i> You Have Been Logged Out!
          </div>');
          redirect('auth');
      }

}
