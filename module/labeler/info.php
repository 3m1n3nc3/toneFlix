<?php
return array(
  'title' => 'Labeler',
  'desc'  => 'This plugin allows your users who to set themselves up as record labels and add an artist name to overide the logged in users name',
    'version' => '1.0'
);
   
/**
 * after an upgrade, open the file: /app/views/upload/song.phtml
 * below this line: <div class="tab-pane fade show active" id="general" role="tabpanel">
 * above this line: <div class="form-group">
                        <label><?php _l('description')?></label>
 * around line: 225
 * add this line: <?php Hook::getInstance()->fire('upload.extra.fields')?>
 */
