<?php

class Auth_model extends CI_Model{


	function login($email, $password){

		$query = $this->db->select('username,email,fullname,password')
			->where('email', $email)->get('users',1);

		$user = $query->row();

		if(password_verify($this->input->post('password'), $user->password)){

			$this->session->set_userdata('email', $user->email);
			$this->session->set_userdata('fullname', $user->fullname);
			$this->session->set_userdata('username', $user->username);

			return 1;
		} 
		else{
			return -1;
		}
	} 

	function register($data){

		$arr = array('email'=> $data['email'], 'username'=> $data['username']);
		$this->db->or_where($arr);
		$query = $this->db->get('users');
		$count_row = $query->num_rows();

		//email exists
		if ($count_row > 0) {
			return -2;
		}

		$ans = $this->db->insert('users',$data);
		return $ans;
	}

}
