<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class App extends CI_Controller {

	private $pathToFile = '/home/klados/uploads';

	public function index(){
		redirect(base_url('/app/dashboard'));
	}


	/*
	 * create new project
	 */
	public function create(){

		if(!$this->session->userdata('email')){
			redirect(base_url('/auth/login'));
		}

		if($this->input->server('REQUEST_METHOD') === 'POST'){

			$this->form_validation->set_rules('name','Name','trim|required|xss_clean');
			$this->form_validation->set_rules('desc','Desc','trim|xss_clean');
			$this->form_validation->set_rules('category','Category','trim|xss_clean');

			if($this->form_validation->run() === TRUE){

				$this->load->model('project');
				$ans = $this->project->createProject($this->input->post('name'), 
																					   $this->session->userdata('email'),
																						 $this->input->post('category'),
																					   $this->input->post('desc'));

				if(!$ans){
					$this->session->set_flashdata('error','Error, project did not created');
					redirect(base_url('/app/create'));
				}
				else{
					redirect(base_url('/app/project/'.$ans));
				}

			}
			else{
				$this->session->set_flashdata('error','Error, wrong input data');
				redirect(base_url('/app/create'));
			}

		}
		else{
			$this->load->view('header');
			$this->load->view('templates/nav.php'); 
			$this->load->view('create');
			$this->load->view('footer');
		}
	}


	/*
	 * load the user's dashboard
	 */
	public function dashboard(){

		if(!$this->session->userdata('email')){
			redirect(base_url('/auth/login'));
		}

		if($this->input->server('REQUEST_METHOD') === 'GET'){

			$email = $this->session->userdata('email');
			$this->load->model('project');
			$ans['projects'] = $this->project->returnUsersProjects($email);

			$this->load->view('header');
			$this->load->view('templates/nav.php'); 
			$this->load->view('dashboard', $ans);
			$this->load->view('footer');
		}
	}

	
	public function deleteProject(){

		if($this->input->server('REQUEST_METHOD') !== 'POST'){
			echo -1;
			exit();
		}

		if(!$this->session->userdata('email')){
			echo -2;
			exit();
		}

		$this->form_validation->set_rules('id','Select project','trim|xss_clean|required');
		if($this->form_validation->run() === FALSE){
			echo -3;
			exit();
		}

		$this->load->model('project');
		$ans = $this->project->deleteProject($this->input->post('id'), $this->session->userdata('email'));

		echo $ans;
	}


	/*
	 * load specific project (user must be a member)
	 */
	public function project(){

		if(!$this->session->userdata('email')){
			redirect(base_url('/auth/login'));
			exit();
		}

		$data['projectId'] = $this->uri->segment(3); 

		$this->load->model('project');
		$ans = $this->project->returnProjectsData($data['projectId'],$this->session->userdata('email'));

		if($ans == -1){
			$this->session->set_flashdata('error','You do not have access to this project');
			redirect(base_url('/app/dashboard'));
		}

		$data['project'] = $ans[0];
		$data['notes'] = $ans[1];

		$this->load->view('header');
		$this->load->view('templates/nav.php'); 
		$this->load->view('project', $data);
		$this->load->view('footer');
	}


	/**
	 *	user action from: view/project
	 *	add a note to a project
	 */
	public function addToProject(){
		if($this->input->server('REQUEST_METHOD') !== 'POST'){
			echo -1;
			exit();
		}

		if(!$this->session->userdata('email')){
			echo -2;
			exit();
		}

		$this->form_validation->set_rules('project','project','trim|xss_clean|required');
		$this->form_validation->set_rules('type','Type','trim|xss_clean|required');
		$this->form_validation->set_rules('name','Name','trim|xss_clean|required');

		if(empty($_FILES['src']['name'])){
			$this->form_validation->set_rules('src','src','trim|xss_clean|required');
		}

		if($this->form_validation->run() === FALSE){
			echo -3;
			exit();
		}

		$src = $this->input->post('src');

		if( $this->input->post('type') == 'file' ){
			$src = pathinfo($_FILES['src']['name'], PATHINFO_EXTENSION);
		}

		$this->load->model('project');
		$ans = $this->project->addResourcesToProject($this->input->post('project'),
																							$this->session->userdata('email'),
																							$this->input->post('type'),
																							$this->input->post('name'),
																						  $src);

		// only if type is file
		if($this->input->post('type') == 'file' and strlen($ans)>2 ){

			$config['upload_path'] = $this->pathToFile;
			$config['allowed_types'] = '*';
			$config['max_size'] = '2048';
			$config['file_name'] = $ans;
			$this->upload->initialize($config);
			
			// caught all file upload's problems
			if(!$this->upload->do_upload('src')){
				// TODO remove insert from the database
				echo $this->upload->display_errors();
				exit();
			}
		}

		echo $ans; 
	}


	/*
	 * user action from: view/project
	 * update a note of a specific project 
	 */
	public function updateNote(){

		if($this->input->server('REQUEST_METHOD') !== 'POST'){
			echo -1;
			exit();
		}

		if(!$this->session->userdata('email')){
			echo -2;
			exit();
		}

		$this->form_validation->set_rules('projectId','project','trim|xss_clean|required');
		$this->form_validation->set_rules('noteId','noteId','trim|xss_clean|required');

		if($this->form_validation->run() === FALSE){
			echo -3;
			exit();
		}

		$src = $this->input->post('src');
		$email = $this->session->userdata('email');
		$projectId = $this->input->post('projectId');
		$noteId = $this->input->post('noteId');

		$this->load->model('project');
		$ans = $this->project->updateNote($email, $projectId, $src, $noteId);
		echo $ans;
	
	}


	/**
	 *	user action from: view/project
	 *	delete a selected note from a project
	 */
	public function deleteItemFromProject(){

		if($this->input->server('REQUEST_METHOD') !== 'POST'){
			echo -1;
			exit();
		}

		if(!$this->session->userdata('email')){
			echo -2;
			exit();
		}

		$this->form_validation->set_rules('id','item id','trim|xss_clean|required');
		$this->form_validation->set_rules('projId','project id','trim|xss_clean|required');

		if($this->form_validation->run() === FALSE){
			echo -3;
			exit();
		}

		$this->load->model('project');
		echo $this->project->deleteItemFromProject($this->input->post('id'),
																								 $this->input->post('projId'),
																					       $this->session->userdata('email'));	
		// if($ans){
		// 	delete_files($this->pathToFile.'/'.$this->input->post('id'));
		// }

	}


	/**
	 * display file from protected filesystem
	 */
	public function showProtectedFile(){
		
		if(!$this->session->userdata('email')){
			redirect(base_url('/auth/login'));
		}

		$fileId = $this->input->get('id', TRUE);
		$email = $this->session->userdata('email');

		// check if user has writes to access the file
		$this->load->model('project');
		$ans = $this->project->checkFileAccess($fileId, $email);
		
		if( $ans == FALSE ){
			$this->session->set_flashdata('error','Error, You do not have permission to see that file');
			redirect(base_url('/app/dashboard'));
		}


		$pathOfFile = $this->pathToFile . '/' . $fileId . '.' . $ans;
		$fp = fopen($pathOfFile, 'rb');

			// send the right headers
			// - adjust Content-Type as needed (read last 4 chars of file name)
			// -- image/jpeg - jpg
			// -- image/png - png
			// -- etc.
			$ext = array();
			$ext['png']='png';
			$ext['jpg']='jpeg';
			$ext['pdf']='pdf';

			header("Content-Type: image/" . $ext[$ans]);
			header("Content-Length: " . filesize($pathOfFile));

			// dump the picture and stop the script
			fpassthru($fp);
			fclose($fp);

	}

}
