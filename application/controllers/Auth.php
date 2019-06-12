<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

	private function hash_password($password){
  	return password_hash($password, PASSWORD_BCRYPT);
	}


	public function index(){
		redirect(base_url('/auth/login'));
	}

	
	public function logout(){
		// unset($_SESSION);
		$this->session->sess_destroy();
		redirect(base_url('/auth/login'));
	}


	public function login(){

		if($this->input->server('REQUEST_METHOD') === 'POST'){

			$this->form_validation->set_rules('email','Email','trim|required|xss_clean');
			$this->form_validation->set_rules('password','Password','trim|required|xss_clean');

			if($this->form_validation->run() === TRUE){

				$email = $this->input->post('email');
				$password = $this->input->post('password');

				//load database module
				$this->load->model('auth_model');
				$ans = $this->auth_model->login($email, $password);

				if($ans > 0){
					$this->session->set_flashdata('success','You are logged in');
					redirect( base_url('/welcome/index'));
				}
				else{
					$this->session->set_flashdata('error','Error, check your email or your password');
					redirect(base_url('/auth/login'));
				}

			}
			else{
				$this->session->set_flashdata('error','Error, fill all the fields');
				redirect(base_url('/auth/login'));
			}

		}
		else{
			$this->load->view('header');
			$this->load->view('templates/nav.php'); 
			$this->load->view('login');
			$this->load->view('footer');
		}
	}


	public function register(){

		// if(isset($_POST['register'])){
		if($this->input->post('register')){

			$this->form_validation->set_rules('username','Username','trim|required|xss_clean');
			$this->form_validation->set_rules('fullname','Fullname','trim|required|xss_clean');
			$this->form_validation->set_rules('email','Email','trim|required|xss_clean');
			$this->form_validation->set_rules('password','Password','trim|required|xss_clean');
			$this->form_validation->set_rules('passconf', 'Password Confirmation','required|matches[password]');
			$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|xss_clean');

			if($this->form_validation->run() == TRUE){

				$data = array(
						'username' => $this->input->post('username'),
						'email' => $this->input->post('email'),
						'fullname' => $this->input->post('fullname'),
						'password' => $this->hash_password($this->input->post('password'))
						);

				$this->load->model('auth_model');
				$ans = $this->auth_model->register($data);

				if($ans < 0){
					if($ans == -2){
						//dublicate email 
						$this->session->set_flashdata('error', 'Sorry, this email already in use');
					}
					else{
						$this->session->set_flashdata('error', 'Your account has not been created');
					}
					redirect( base_url('/auth/login'));
				}
				else{
					$this->session->set_flashdata('success', 'Your account has been created');

					$this->session->set_userdata('email', $_POST['email']);
					$this->session->set_userdata('fullname', $_POST['fullname']);
					$this->session->set_userdata('username', $_POST['username']);

					redirect( base_url('/welcome/'));
				}

			}
			else{
				redirect( base_url('/auth/login'));
				// $this->load->view('header');
				// $this->load->view('templates/nav.php'); 
				// $this->load->view('register');
				// $this->load->view('footer');
			}
		}
		else{
			$this->load->view('header');
			$this->load->view('templates/nav.php'); 
			$this->load->view('register');
			$this->load->view('footer');
		}
	}

	}
