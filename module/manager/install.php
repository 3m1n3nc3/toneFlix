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

