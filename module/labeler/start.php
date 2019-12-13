<?php
Hook::getInstance()->register('admin.settings.general', function() {
    echo view('labeler::admin/settings/integration');
}); 

Hook::getInstance()->register('upload.extra.fields', function() {
    if(config('allow_label_manager')) {  
    	echo view('labeler::user/upload');
	} 
});

Hook::getInstance()->register('footer.end', function() {
    echo view('labeler::user/footer');
});
 
$request->any("labeler", array('uses' => 'labeler::labeler@index'));
$request->any("labeler/search", array('uses' => 'labeler::labeler@search'));
$request->any("paystack/success", array('uses' => 'paystack::paystack@success')); 
