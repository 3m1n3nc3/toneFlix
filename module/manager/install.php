<?php
$db = Database::getInstance();


$query = $this->db->query("SELECT COUNT(*) AS exist FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = (SELECT DATABASE()) AND TABLE_NAME = 'users' AND column_name = 'is_label'"); 

if (!$query->fetch(PDO::FETCH_ASSOC)['exist']) {
    try {
        $db->query("ALTER TABLE `users` ADD `is_label` ENUM('0','1') NOT NULL DEFAULT '0' AFTER `user_type`;");
    } catch (Exception $e){}
}

$query1 = $this->db->query("SELECT COUNT(*) AS exist FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = (SELECT DATABASE()) AND TABLE_NAME = 'users' AND column_name = 'label'"); 

if (!$query1->fetch(PDO::FETCH_ASSOC)['exist']) {
    try {
        $db->query("ALTER TABLE `users` ADD `label` INT(11) NULL DEFAULT NULL AFTER `is_label`;");
    } catch (Exception $e){}
}

$query2 = $this->db->query("SELECT COUNT(*) AS exist FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = (SELECT DATABASE()) AND TABLE_NAME = 'users' AND column_name = 'claim'"); 

if (!$query2->fetch(PDO::FETCH_ASSOC)['exist']) {
    try {
        $db->query("ALTER TABLE `users` ADD `claim` TEXT NULL DEFAULT NULL AFTER `label`;");
    } catch (Exception $e){}
} 
 
try {
    $db->query(
        "CREATE TABLE IF NOT EXISTS `anonymous_downloads` ( 
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `ip` varchar(255) DEFAULT NULL,
          `track` int(11) NOT NULL,
          `time` int(11) NOT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8;"
    );
} catch (Exception $e){} 

