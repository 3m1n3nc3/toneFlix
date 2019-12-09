<?php
Hook::getInstance()->register('admin.settings.integrations', function() {
    echo view('paystack::admin/settings/integration');
});

Hook::getInstance()->register('header.after.css', function() {
    echo '<link href="'.assetUrl('module/paystack/styles/loader.css').'?time='.fileatime(path('module/paystack/styles/loader.css')).'" rel="stylesheet">';
});

Hook::getInstance()->register('home-footer-links', function() {
    echo '<a  href="'.url('paystack').'">'.l('paystack::paystack').'</a>';
});

Hook::getInstance()->register('payment.method', function() {
    if(config('paystack_public_key', '')) { 
    	$logo = (getThemeMode() == 'dark-mode' ? 'paystack_logo_light.svg' : 'paystack_logo.png');
    	echo
	    '<div class="each">
	        <a href="'.url('paystack/payment', array('type' => session_get('about-pay-type'), 'typeid' => session_get('about-pay-typeid'), 'price' => session_get('about-pay-price'))).'"><img src="'.assetUrl('module/paystack/images/'.$logo).'"/> </a>
	    </div>';
	} 
});

Hook::getInstance()->register('footer.end', function() {
    echo view('paystack::payment/footer');
});
 
$request->any("paystack", array('uses' => 'paystack::paystack@index'));
$request->any("paystack/payment", array('uses' => 'paystack::paystack@payment'));
$request->any("paystack/success", array('uses' => 'paystack::paystack@success')); 
