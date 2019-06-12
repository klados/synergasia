<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	public function index(){
		$this->load->view('header');
		$this->load->view('templates/nav.php'); 
		$this->load->view('welcome_message');
		$this->load->view('footer');
	}


	public function about(){
		$this->load->view('header');
		$this->load->view('templates/nav.php'); 
		$this->load->view('about');
		$this->load->view('footer');
	}
}
