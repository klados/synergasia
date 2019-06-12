<?php

class Users_model extends CI_Model{

	public function searchFriend($txt){

		$this->db->select('username, md5(id) as id');
		$this->db->from('users');
		$this->db->like('username', $txt);

		$query = $this->db->get();
		return $query->result_array();
	}


	public function addNewFriend($friendId, $userEmail){

			$this->db->select('email');
			$query = $this->db->get_where('users', array('md5(id)' => $friendId));

			if($query->num_rows() != 1){
				return -2;
			}


			$targetEmail = $query->result()[0]->email;

			// you can not follow yourself
			if( $targetEmail == $userEmail ){
				return -4;
			}

			$check_query = $this->db->get_where('friends', array('myself'=> $userEmail, 'target'=> $targetEmail));
			
			if( $check_query->num_rows() != 0 ){
				//friendship already exists
				return -3;
			}

			$this->db->insert('friends', array('myself'=> $userEmail, 'target'=> $targetEmail));

			return ($this->db->affected_rows() != 1) ? -4 : 1;
	}


	public function getFriends($email){

		$this->db->select('username, md5(id) as id');
		$this->db->from('friends');
		$this->db->join('users', 'users.email = friends.target');
		$this->db->where('myself', $email);
		$query = $this->db->get();
		// $query = $this->db->get_where('friends', array('myself' => $email));
		return $query->result();
	}


	public function addFriendToProject($friendId, $projectId, $email){

		// check if user owns the project
		$this->db->select('owner_email');
		$this->db->from('projects');
		$this->db->where('md5(id)', $projectId);
		$query = $this->db->get();
		
		if($query->num_rows() != 1){
			return -2;
		}

		if( $query->result()[0]->owner_email != $email ){
			return -3;
		}
		
		// get target user's email from the hashed id
		$this->db->select('email');
		$this->db->from('users');
		$this->db->where('md5(id)', $friendId);
		$query = $this->db->get();
		$query_friend_email = $query->result()[0]->email; 
		
		// get project id from hash 
		$this->db->select('id');
		$this->db->from('projects');
		$this->db->where('md5(id)', $projectId);
		$query= $this->db->get();
		$query_project_id = $query->result()[0]->id;

		$params = array('project_id'=> $query_project_id, 'email'=> $query_friend_email);

		// check if client is already member of the project
		$this->db->select('id');
		$this->db->from('project_members');
		$this->db->where($params);
		$query= $this->db->get();
		if($query->num_rows() >= 1){
			return -4;
		}

		// insert new  member to the database
		$this->db->insert('project_members', $params);

		return ($this->db->affected_rows() != 1) ? -5 : 1;
	}


	public function removeFromFriendList($friendId, $email){

		// get friend's email from id 
		$this->db->select('email');
		$this->db->from('users');
		$this->db->where('md5(id)', $friendId);
		$query= $this->db->get();
		if($query->num_rows() != 1){
			return -2;
		}

		$targetEmail = $query->result()[0]->email;

		// remove friend from friend list
		$this->db->delete('friends', array('myself' => $email, 'target'=>$targetEmail));
		return 1;
	}


	public function getMembersOfProject($email, $projectId){

		// check if the user is a member of the project
		$this->db->select('email');
		$this->db->from('project_members');
		$this->db->where('md5(project_id)', $projectId);
		$query = $this->db->get();
		
		// return $query->result();
		// if( in_array($email, $query->result())[0]->email ){
		// 	return -2;
		// }
	
		$exists = FALSE;
		foreach($query->result() as $item){
			if($item->email == $email){
				$exists = TRUE;
				break;
			}
		}
		if($exists == FALSE) return -2;
		
		// get the members
		$this->db->select('md5(users.id) as id, username');
		$this->db->from('project_members');
		$this->db->join('users', 'users.email = project_members.email');
		$this->db->where('md5(project_members.project_id)', $projectId);
		$query = $this->db->get();

		return $query->result_array();
	}


	public function removeMemberFromProject($projectId, $userId, $email){

		// check if the user is the owner of the project
		$data = array('owner_email' => $email, 'md5(id)' => $projectId);
		$this->db->select('md5(id) as id');
		$this->db->from('projects');
		$this->db->where($data);
		$query = $this->db->get();
		
		if($query->num_rows() != 1){
			return -2;
		}

		// ckeck if the owner tries to remove himself
		if($query->result()[0]->id == $userId){
			return -3;
		}
		
		// check if the target user is member of the project ???
		
		// get user's email from id, check if target user exists
		$this->db->select('email');
		$this->db->from('users');
		$this->db->where('md5(id)', $userId);
		$query = $this->db->get();

		if($query->num_rows() != 1){
			return -4;
		}
		$targetEmail = $query->result()[0]->email;

		// remove the user from the project   
		$this->db->delete('project_members', array('email' => $targetEmail, 'md5(project_id)'=>$projectId));
		return 1;

	}


}
