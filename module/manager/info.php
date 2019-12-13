<?php
return array(
  'title' => 'Artist Manager',
  'desc'  => 'This plugin allows your users to create and manage other user accounts and their tracks',
    'version' => '1.0'
); 

/**
 * Add this line within the <div class="right-buttons dropdown"> class (views/user/profile/layout.phtml)
 * <?php Hook::getInstance()->fire('profile.right.buttons', null, array($user,$page))?>
 *
 * Add this line after the full_name variable (views/user/profile/layout.phtml)
 * <?php Hook::getInstance()->fire('profile.name.section', null, array($user,$page))?>
 */
