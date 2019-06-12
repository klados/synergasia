<?php

class Project extends CI_Model{

	private $pathToFile = '/home/klados/uploads';

	/*
	 * return -1 if the user does not has access to the project
	 * else return the project id (no hash) 
	 */
	private function checkIfUserHasWritesToProject($projectId, $email){
		$toStore = array('md5(project_id)' => $projectId, 'email' => $email);
		$query = $this->db->select('project_id')->get_where('project_members', $toStore);

		if($query->num_rows() != 1) return -1;
		return $query->row()->project_id;
	}


	/*
	 * create a new project
	 * return FALSE if any error
	 * else hash of the projectId
	 */
	public function createProject($name, $email, $cat, $desc){

		$dataToStore = array(
			'name' => $name,
			'owner_email' => $email,
			'category' => $cat,
			'description' => $desc
		);

		$this->db->trans_start();
		$this->db->insert('projects',$dataToStore);
		$projectId = $this->db->insert_id();

		$dataToStore = array(
			'project_id' => $projectId,
			'email' => $email
		);

		$this->db->insert('project_members', $dataToStore);
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE) return FALSE;
		return md5($projectId);
	}


	/*
	 * returns all the projects that a user take part
	 * return the query result
	 */
	public function returnUsersProjects($email){

		$this->db->select('md5(projects.id) as id, owner_email, name, created_at, description, category');
		$this->db->from('projects');
		$this->db->join('project_members', 'project_members.project_id = projects.id', 'right');
		$this->db->where('project_members.email', $email);
		$query = $this->db->get();

		return $query->result_array();
	}

	
	/*
	 *return array of arrays
	 * first subarray contains info about the project
	 * seccond subarray contains stored notes
	 */
	public function returnProjectsData($projectId, $email){

		$auth =$this->checkIfUserHasWritesToProject( $projectId, $email);
		if($auth == -1) return -1;
		//$auth --> projectId int not hash

		$this->db->select('name, created_at, description, category');
		$this->db->from('projects');
		$this->db->where( array('status' => 'active', 'id' => $auth) );
		$projectQuery = $this->db->get();

		$this->db->select('md5(id) as id, type, date, data, description');
		$this->db->from('project_data');
		$this->db->where( array('project_id' => $auth) );
		$notesQuery = $this->db->get();

		return array($projectQuery->row(), $notesQuery->result_array());
	}


	/*
	 *delete an item from a project
	 */
	public function deleteItemFromProject($itemId, $projectId, $email){

		$auth =$this->checkIfUserHasWritesToProject($projectId, $email);
		if($auth == -1) return -1;

		$projId = array('md5(id)' => $itemId,'project_id' => $auth);

		$this->db->select('type, data');
		$ans = $this->db->get_where('project_data', array('md5(id)' => $itemId));
		
		if($ans->num_rows() == 1){
			$res = $ans->result()[0];
			if($res->type == 'file'){
				unlink( $this->pathToFile . '/' . $itemId . '.' . $res->data);
				// echo $this->pathToFile . '/' . $itemId . '.' . $res->data;
			}
 		}


		$this->db->where($projId);
		$ans = $this->db->delete('project_data');
		return $ans;
	}


	/*
	 * delete the selected project 
	 * return TRUE if no errors
	 * else FALSE 
	 */
	public function deleteProject($id, $email){
	// delete project with specific id
		
		$this->db->select('owner_email');
		$this->db->from('projects');
		$this->db->where('md5(id)', $id);
		$query = $this->db->get();

		if($query->num_rows() != 1){
			return -2;
		}

		if($query->result()[0]->owner_email != $email){
			return -3;
		}

		$projectToDelete = array(
			'md5(id)' => $id,
			'owner_email' => $email 
		);

		$this->db->where($projectToDelete);
		$ans = $this->db->delete('projects');
		return $ans;
	}


	/*
	 * add notes to a project
	 * return -1 if any error
	 * else noteId (hash)
	 */
	public function addResourcesToProject($projectId, $email, $type, $desc,
	$rowData){

		$auth = $this->checkIfUserHasWritesToProject($projectId, $email);
		if($auth == -1) return -4;

		$dataToStore = array(
			'type' => $type,
			'description' => $desc,
			'owner_email' => $email,
			'project_id' => $auth,
			'data' => $rowData
		);

		$this->db->insert('project_data',$dataToStore);
		$noteId = $this->db->insert_id();
		return ($this->db->affected_rows() != 1) ? -5: md5($noteId);
	}


	/*
	 * update a note of a project
	 */
	public function updateNote($email, $projectId, $src, $noteId){
		$auth = $this->checkIfUserHasWritesToProject($projectId, $email);
		if($auth == -1) return -4;

		//check the type of the note
		$this->db->select('type');
		$this->db->from('project_data');
		$this->db->where('md5(id)', $noteId);
		$query = $this->db->get();

		if($query->num_rows() != 1){
			return -5;
		}
		if( $query->result()[0]->type != 'notes' ){
			return -6;
		}

		// update data
		$this->db->set('data', $src);
		$this->db->where('md5(id)', $noteId);
		$this->db->update('project_data'); 

		return 1;

	}

	/**
	 *	check if user has writes to access the file
	 */
	public function checkFileAccess($fileId, $email){

		$this->db->select('project_data.data');
		$this->db->from('project_data');
		$this->db->join('project_members', 'project_data.project_id = project_members.project_id');
		$this->db->where('md5(project_data.id)', $fileId);
		$this->db->where('project_members.email', $email);
		$this->db->where('project_data.type', 'file');
		$query = $this->db->get();

		if($query->num_rows() != 1) return FALSE;
		else return $query->result()[0]->data;

		// return $query->row()->project_id;
		//select type from project_data right join project_members on project_members.project_id = project_data.project_id where type ='file' and email = $email

	}

}
