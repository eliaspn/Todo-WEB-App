<?php
class Usermodel extends CI_Model {
	public function __construct() {

	}

	function validate($user) {
		if (!isset($user['request_ip'])) {
			throw new Exception('Not all required fields are provided : ' 
                . print_r($user, TRUE));
		}
	}

	function get_row($id) {
		if (is_int($id)) {
			$query = $this->db->get_where('users', array('id'=> $id))->row_array();
			$userDetails = ($query) ? $query : NULL;
		}else {
			throw new Exception("Invalid Param");
		}
		return $userDetails;
	}

	function add($user) {
		$this->validate($user);
		if (array_key_exists('id',$user)) {
			$id = $this->db->update($user);
		} else {
			$id = $this->insert($user);
		}
		return $id;
	}

	function insert($user) {
		if (!$this->db->insert('users',$user)) {
			 throw new Exception('Could not insert customer to the database.');
			}
		return intval($this->db->insert_id());
	}

	function update($user) {
        foreach ($user as $key => $value) {
            if ($value === '') 
                unset($user[$key]);
        }

        $this->db->where('id', $user['id']);
        if (!$this->db->update('users', $user)) {
            throw new Exception('Could not update customer');
        }

     	return intval($user['id']);
     }
 }
