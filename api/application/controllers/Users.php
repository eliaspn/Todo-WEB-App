<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

class Users extends REST_Controller {
	 function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->model('Usermodel');
    }


	//check if user exist and then return its object details
    function index_get() {
    	$userId = (int) $this->get('userId');
 	   	$userDetails = $this->Usermodel->get_row($userId);
		if (!isset($userDetails)) {
			$this->set_response([
                'status' => FALSE,
                'message' => 'User could not be found'
            ], REST_Controller::HTTP_NOT_FOUND);
		}else {
			$this->set_response($userDetails, REST_Controller::HTTP_OK);
		}
    }

    //create a new user object in db
    function index_post() {
    	$ip = $_SERVER['REMOTE_ADDR']; //get ip of client
    	$newUserId = $this->Usermodel->add(array('request_ip' =>$ip));
        $user = $this->Usermodel->get_row($newUserId);
        if (!isset($user)) {
            $this->set_response([
                'status' => FALSE,
                'message' => 'User could not be inserted'
            ], 202);
        }else {
            $this->set_response($user, 201);
        }
    }
}