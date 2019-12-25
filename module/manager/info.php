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
 * Add this line: 
 * <?php Hook::getInstance()->fire('profile.name.section', null, array($user))?>
 * after the full_name variable (<?php echo $user['full_name']?>) in the following files:
 * a. (views/user/profile/layout.phtml)
 * b. (views/admin/users/lists.phtml)
 * c. (views/discover/overview.phtml)
 * d. (views/user/card.phtml)
 * e. (views/user/display-grid.phtml)
 * e. (views/user/display-inline.phtml)
 * f. (views/welcome/users.phtml)
 * g. (views/track/comment/format.phtml)
 * 
 */
