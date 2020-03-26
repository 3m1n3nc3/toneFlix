<?php

Hook::getInstance()->register('admin.settings.general', function() {
    echo view('manager::admin/settings/integration');
}); 

Hook::getInstance()->register('admin.dashboard.bottom', function() {
    echo view('manager::admin/settings/dashboard');
}); 

Hook::getInstance()->register('profile.right.buttons', function($user) {
    echo view('manager::management/quick_links', array('user' => $user, 'type' => 'claim'));
}); 

Hook::getInstance()->register('admin.track.action', function($track) {
    echo view('manager::management/quick_links', array('track' => $track, 'type' => 'stats'));
}); 

Hook::getInstance()->register('profile.name.section', function($user) {
    echo ($user['is_label'] ? ' ('.l('manager::record_label').')' : '');
}); 

Hook::getInstance()->register('admin.user.edit', function($user) {
	$this->user = $user;
    echo view('manager::admin/settings/useredit', array('user' => $user));
});  
 
Hook::getInstance()->register('user.save', function($userId, $val) {
	if (isset($val['is_label'])) {
		if (model('user')->getUser(model('user')->authId)['is_label'] || model('user')->hasRole('manage-users')) {
			Database::getInstance()->query("UPDATE users SET is_label=? WHERE id=?", $val['is_label'], $userId); 
		}
	}
	if (isset($val['password'])) {
	    if ($val['password'] !== '') {
	    	$user = array('id' => $userId);
	  		model('user')->resetPassword($val['password'], $user);
		}
	}
}); 

Hook::getInstance()->register('user.signup.success', function($userId, $val) {
	if (model('user')->getUser(model('user')->authId)['is_label']) {
		Database::getInstance()->query("UPDATE users SET label=?, claim=? WHERE id=?", model('user')->authId, 1, $userId); 
	}
});

Hook::getInstance()->register('track.added', function($trackId, $val) { 
	$C = getController();
	if (model('user')->getUser(model('user')->authId)['is_label'] && $C->request->input('id')) {
		Database::getInstance()->query("UPDATE tracks SET userid=? WHERE id=?", $C->request->input('id'), $trackId); 
	}
});   

Hook::getInstance()->register('main.menu.bottom', function() { 
    if(config('allow_artist_manager') && model('user')->getUser(model('user')->authId)['is_label']) {
    	echo view('manager::management/sidemenu');
	} 
}); 

Hook::getInstance()->register('admin.menu.end', function() { 
    if(config('allow_artist_manager') && model('user')->getUser(model('user')->authId)['is_label']) {
        echo view('manager::management/sidemenu');
    } 
}); 

Hook::getInstance()->register('track.anonymous.downloads', function($track) { 
    if(config('anonymous_downloads')) {
        if (!config('sum_anonymous_downloads')) { 
            echo view('manager::public/count_downloads', array('track' => $track));
        }
    } 
}); 

Hook::getInstance()->register('user.charts', function($result, $times) { 
    $C = getController(); 
    $anonymous_downloads = array(
        'name' => l('manager::anonymous_downloads_lang'),
        'points' => array()
    );
    $id = $C->request->input('id');

    foreach($times as $name => $time) {
        $stats = model('manager::manager')->countAllAnonymousDownloadsStatistics($id,$time[0], $time[1]);  
        $anonymous_downloads['points'][$name] = $stats['anonymous_downloads'];
    }  
    $result['charts']['stats'][] = $anonymous_downloads;

    return $result;
});   

$request->any("label/manager", array('uses' => 'manager::manager@index')); 
$request->any("label/manager/tracks", array('uses' => 'manager::manager@tracks')); 
$request->any("label/manager/users", array('uses' => 'manager::manager@index')); 
$request->any("label/manager/user/edit", array('uses' => 'manager::manager@userEdit'));
$request->any("label/manager/user/edit/picture", array('uses' => 'manager::manager@userPictureEdit'));
$request->any("label/manager/user/action", array('uses' => 'manager::manager@userAction'));
$request->any("label/track/add/anonymous/download", array('uses' => 'manager::manager@addAnonymousDownload', 'secure' => false));
