<?php
class ManagerModel extends Model {
 
    function lists($type = 1, $term = '', $label = '') { 
        $sql = "SELECT * FROM users WHERE user_type=? AND label=? ";
        $param = array($type, $label); 
        if ($term ) {
            $term = '%'.$term.'%';
            $sql .= " AND (username LIKE ? OR full_name LIKE ? OR email LIKE ? ) ";
            $param[] = $term;
            $param[] = $term;
            $param[] = $term;
        }

        $sql .= " ORDER BY id DESC ";
        return $this->db->paginate($sql, $param, config('users-limit', 10));
    }	

    public function countUsers($type, $label) {
        $query = $this->db->query("SELECT * FROM users WHERE user_type=? AND label=?", $type, $label);
        return $query->rowCount();
    }   

    public function countLabels($type = 1) {
        $query = $this->db->query("SELECT * FROM users WHERE is_label=?", $type);
        return $query->rowCount();
    }    

}
