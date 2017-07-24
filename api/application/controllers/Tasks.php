<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

class Tasks extends REST_Controller {

	 function __construct() {
        // Construct the parent class
        parent::__construct();
        $this->load->model('taskmodel');
    }

    function index_get() {
    	$userId = $this->get('userId');
    	$tasksList = $this->taskmodel->get_rows($userId);
	    if (!isset($tasksList)) {
				$this->set_response([
	            ], 204);
			}else {
				$this->set_response($tasksList, REST_Controller::HTTP_OK);
			}
	}

	function index_post() {
		$data= $this->post();
		$newtask = $this->taskmodel->add($data);
		
		if (!isset($newtask)) {
			$this->set_response([
				'status' => FALSE,
	           	'message' => 'Failed, the task could not be inserted in the db'
	          ], 400);
		}else {
			$this->set_response($newtask, REST_Controller::HTTP_OK);
	   	}
	}

	function index_put() {
		$id = $this->uri->segment(2);
		$data = $this->put();
		$updatedTaskId = $this->taskmodel->update($data);
		$taskData = $this->taskmodel->get_row($updatedTaskId);

		if (!isset($taskData)) {
			$this->set_response([
				'status' => FALSE,
	           	'message' => 'Failed, the task could not be updated'
	          ], 400);
		}else {
			$this->set_response($taskData, REST_Controller::HTTP_OK);
	   	}
	}

	function index_delete() {
		$id = $this->uri->segment(2);
		$deleteItem = $this->taskmodel->delete($id);
		if (!isset($deleteItem)) {
			$this->set_response('', 202);
		}else {
			$this->set_response([
	          ], 204);
	   	}
	}
}