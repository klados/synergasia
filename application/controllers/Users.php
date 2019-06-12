<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends CI_Controller {

	public function searchForAFriendBasedOnUsername(){

		if($this->input->server('REQUEST_METHOD') === 'POST'){
			
			if(!$this->session->userdata('email')){
				echo -1;
				exit();
			}

			$txt = $this->input->post('searchString');
			$this->load->model('users_model');
			$ans = $this->users_model->searchFriend($txt);

			echo json_encode($ans);
		}
	}


	public function createFriendship(){

		if($this->input->server('REQUEST_METHOD') === 'POST'){

			if(!$this->session->userdata('email')){
				echo -1;
				exit();
			}

			$friendId = $this->input->post('friendId');
			$userEmail = $this->session->userdata('email');

			$this->load->model('users_model');
			$ans = $this->users_model->addNewFriend($friendId, $userEmail);

			echo json_encode($ans);
		
		}
	}


	public function getAllFriends(){

		if($this->input->server('REQUEST_METHOD') === 'POST'){
			if(!$this->session->userdata('email')){
				echo -1;
				exit();
			}

			$email = $this->session->userdata('email');
			$this->load->model('users_model');
			$ans = $this->users_model->getFriends($email);
			echo json_encode($ans);
		}
	}


	public function removeFromFriendList(){

		if($this->input->server('REQUEST_METHOD') === 'POST'){
			if(!$this->session->userdata('email')){
				echo -1;
				exit();
			}

			$email = $this->session->userdata('email');
			$friendId = $this->input->post('friendId');

			$this->load->model('users_model');
			$ans = $this->users_model->removeFromFriendList($friendId, $email);
			echo json_encode($ans);

		}
	}


	public function addFriendToProject(){

		if($this->input->server('REQUEST_METHOD') === 'POST'){
			if(!$this->session->userdata('email')){
				echo -1;
				exit();
			}

			$email = $this->session->userdata('email');
			$friendId = $this->input->post('friendId');
			$projectId = $this->input->post('projectId');

			$this->load->model('users_model');
			$ans = $this->users_model->addFriendToProject($friendId, $projectId, $email);
			echo json_encode($ans);
		}
	}


	public function removeMemberFromProject(){
		if($this->input->server('REQUEST_METHOD') === 'POST'){
			if(!$this->session->userdata('email')){
				echo -1;
				exit();
			}

			$email = $this->session->userdata('email');
			$projectId = $this->input->post('projectId');
			$userId = $this->input->post('userId');

			$this->load->model('users_model');
			$ans = $this->users_model->removeMemberFromProject($projectId, $userId, $email);
			echo json_encode($ans);
		}
	}


	public function getMembersOfProject(){

		if($this->input->server('REQUEST_METHOD') === 'POST'){
			if(!$this->session->userdata('email')){
				echo -1;
				exit();
			}

			$email = $this->session->userdata('email');
			$projectId = $this->input->post('projectId');

			$this->load->model('users_model');
			$ans = $this->users_model->getMembersOfProject($email, $projectId);
			echo json_encode($ans);
		}
	}


}
