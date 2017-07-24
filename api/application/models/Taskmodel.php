<?php 
class taskmodel extends CI_Model {
	public function __construct() {

	}

	function get_rows($userId, $limit=5, $offset= 0){
		return $this->db->get_where('tasks', array('user_id'=> $userId), $limit, $offset)->result_array();
	}

	function get_row($taskId){
		$result = $this->db->get_where('tasks', array('id'=>$taskId))->row_array();
		return $result;

	} 

	function validate($task) {
		if (!isset($task['title'], $task['user_id'] )) {
			throw new Exception('Not all required fields are provided : ' 
                . print_r($task, TRUE));
		}
	}

	function add($task) {
		$this->validate($task);
		if (array_key_exists('id', $task )) {
			$id = $this->update($task);
		} else {
			$id = $this->insert($task);
		}
		return $this->get_row($id);
	}

	function insert($task) {
		if (!$this->db->insert('tasks',$task)) {
			 throw new Exception('Could not insert task in the database.');
			}
		return intval($this->db->insert_id());
	}

	function update($task) {
        foreach ($task as $key => $value) {
            if ($value === '') 
                unset($task[$key]);
        }

        $this->db->where('id', $task['id']);
        if (!$this->db->update('tasks', $task)) {
            throw new Exception('Could not update task');
        }

     	return intval($task['id']);
    }

    function delete($taskId) {
    	$this->db->where('id', $taskId);
    	$this->db->delete('tasks');
    	return true;
    }


}