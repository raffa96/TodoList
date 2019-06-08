<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class App extends CI_Controller {

	function __construct() {
		parent::__construct();

		$loggedIn = $this->session->userdata('logged-in');

		if (!isset($loggedIn) || $loggedIn != true) {
			// Non logged in
			show_404();
		}
	}

	function index() {

		$data['todos'] = $this->model->ra_object('todo', 'idAccess', $this->session->userdata('id'));

		foreach ($data['todos'] as $todo) {
			$attachments = $this->model->ra_object('attachment', 'idTodo', $todo->id);

			if ($attachments) {
				$todo->attachments = $attachments;
			}
		}

		$this->load->view('header');
		$this->load->view('list', $data);
		$this->load->view('footer');
	}

	function todo() {

		$id = $this->uri->segment(3);

		$data['todo'] = $this->model->r_object('todo', $id, $this->session->userdata('id'));

		if ($data['todo']) {
			$this->load->view('header');
			$this->load->view('single_todo', $data);
			$this->load->view('footer');
		} else {
			show_404();
		}
		
	}

	function new_todo() {

		$this->load->library('form_validation');

		$this->form_validation->set_rules('todo', 'Todo Text', 'trim|required');

		if ($this->form_validation->run() == FALSE) {
			// Failed
			$this->index();
		} else {
			// OK
			$data = array(
				'idAccess' => $this->session->userdata('id'),
				'text' => $this->input->post('todo'),
				'createdAt' => date('Y-m-d H:i:s')
			);

			$this->model->c_object('todo', $data);

			redirect('app');
		}
	}

	function check() {
		$id = $this->uri->segment(3);

		$data = array(
			'completed' => 1
		);
		
		$this->model->u_object('todo', $id, $data, $this->session->userdata('id'));

		redirect('app');
	}

	function uncheck() {
		$id = $this->uri->segment(3);

		$data = array(
			'completed' => 0
		);
		
		$this->model->u_object('todo', $id, $data, $this->session->userdata('id'));

		redirect('app');
	}

	function destroy_todo() {
		$id = $this->uri->segment(3);

		$this->model->d_object('todo', $id, $this->session->userdata('id'));

		$data['todos'] = $this->model->ra_object('todo', 'idAccess', $this->session->userdata('id'));
		foreach ($data['todos'] as $todo) {
			$attachments = $this->model->ra_object('attachment', 'idTodo', $todo->id);

			if ($attachments) {
				$todo->attachments = $attachments;
			}
		}

		$this->load->view('ajax/list', $data);
	}

	function upd_todo() {
		$id = $this->input->post('id');

		$data = array(
			'text' => $this->input->post('todo')
		);
		
		$this->model->u_object('todo', $id, $data, $this->session->userdata('id'));

		redirect('app');
	}

	// function upd_todo() {

	// 	$this->load->library('form_validation');

	// 	$this->form_validation->set_rules('todo', 'Todo Text', 'trim|required');

	// 	if ($this->form_validation->run() == FALSE) {
	// 		// Failed
	// 		$this->upd_todo();
	// 	} else {
	// 		$id = $this->input->post('id');

	// 		$data = array(
	// 			'text' => $this->input->post('todo')
	// 		);
			
	// 		$this->model->u_object('todo', $id, $data, $this->session->userdata('id'));

	// 		redirect('app');
	// 	}	
	// }





	function new_attachment() {
		if (!($_FILES['file']['size'] == 0)) {
			$this->load->library('upload');

			$config['upload_path'] = "./resources/attachments";
			$config['allowed_types'] = "jpg|png";
			// file.JPG -> file.jpg
			$config['file_ext_tolower'] = true;
			$config['overwrite'] = false;

			$this->upload->initialize($config);

			if ($this->upload->do_upload('file')) {
				$file = $this->upload->data();

				// Save to DB
				$data = array(
					'idTodo' => $this->uri->segment(3), 
					'attachment' => $file['raw_name'],
					'type' => $file['file_ext']
				);

				$this->model->c_object('attachment', $data);

				// Redirect
				redirect('app');

			} else {
				// Error
			}
		} else {
			// No file
		}
	}

}