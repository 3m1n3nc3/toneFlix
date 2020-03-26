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

    public function addAnonymousDownload($id) {
        $this->db->query("INSERT INTO anonymous_downloads (ip,track,time)VALUES(?,?,?)", get_ip(), $id, time());
    } 

    public function countAnonymousDownloads($id) {
        $query = $this->db->query("SELECT ip FROM anonymous_downloads WHERE track=? ", $id);
        return $query->rowCount();
    }

    public function countAllDownloads() {
        $query = $this->db->query("SELECT id FROM downloads WHERE 1 "); 
        return $query->rowCount();
    }

    public function countAllAnonymousDownloads() { 
        $query = $this->db->query("SELECT id FROM anonymous_downloads WHERE 1"); 
        return $query->rowCount();
    }

    public function countAllAnonymousDownloadsStatistics($id, $start, $end = null) {
        $end = ($end) ? $end : time();
        $tracks = ($id != 'all') ? array($id) : $this->getMyTracksId();
        $tracks[] = 0;
        $tracks = implode(',', $tracks);

        $query = $this->db->query("SELECT(SELECT COUNT(track) FROM anonymous_downloads WHERE track IN ($tracks) AND time BETWEEN $start AND $end) as anonymous_downloads");
        return $query->fetch(PDO::FETCH_ASSOC);

    }
}
