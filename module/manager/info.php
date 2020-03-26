<?php
return array(
  'title' => 'Artist Manager',
  'desc'  => 'This plugin allows your users to create and manage other user accounts and their tracks',
    'version' => '1.0'
); 

/**
 * Add this line inside the <div class="right-buttons dropdown"> div in (views/user/profile/layout.phtml)
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
 * Update this line
 * <?php if($track['downloads']):$countDownloads = model('track')->countDownloads($track['id'])?>
 * in (views/track/profile/layout.phtml)
 * 
 * to
 * 
 * <?php if($track['downloads']):
 *   $countDownloads = model('track')->countDownloads($track['id']);
 *   if (config('sum_anonymous_downloads')) {
 *     $countDownloads = model('manager::manager')->countAnonymousDownloads($track['id']) + $countDownloads;
 *   } else {
 *     Hook::getInstance()->fire('track.anonymous.downloads', null, array($track));
 *   }
 * ?>
 *
 *  Replace
 *  
 *   <li>
 *     <a href="javascript:void(0)" style="<?php echo ($countDownloads < 1) ? 'display:none': null?>"><i class="la 
 *     la-download"></i> <span><?php echo $countDownloads?></span></a>
 *   </li>
 *
 * With
 * 
 *   <?php if($countDownloads):?>
 *     <li>
 *       <a href="javascript:void(0)" style="<?php echo ($countDownloads < 1) ? 'display:none': null?>"><i class="la 
 *       la-download"></i> <span><?php echo $countDownloads?></span></a>
 *     </li>
 *   <?php endif;?> 
 *
 * 
 * Then add this function to styles/main/js/main.js
 *   function addAnonymousDownload(id) {
 *       $.ajax({
 *          url : buildLink('label/track/add/anonymous/download', [{key: 'id', value: id}])
 *       });
 *   }
 *
 * Find addDownload(id) function in styles/main/js/main.js and Call addAnonymousDownload(id) inside it
 */
