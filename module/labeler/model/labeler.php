<?php
class LabelerModel extends Model {

	function findUser($str = '') { 
        $query = $this->db->query("SELECT username, full_name, id FROM users WHERE username LIKE? OR full_name LIKE?", '%'.$str.'%', '%'.$str.'%');
        return $query->fetch(PDO::FETCH_ASSOC);
	}

}
