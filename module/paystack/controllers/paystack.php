<?php
class PaystackController extends Controller {
    public function index() {
        $type = $this->request->input('type');
        $typeId = $this->request->input('typeid');
        $price = $this->request->input('price');

        session_put("about-pay-reference", config('paystack_ref_prefix').rand(100000000, 900000000));
        if ($this->request->input('refresh')) {
            $this->request->redirect('paystack/payment', array('type' => $type, 'typeid' => $typeid, 'price' => $price)); 
        }
    }

    public function payment() { 

        if (! $this->userCurrencySupportPaystack()) {
            $price = convertBackToBase($price);
            $currency = config('currency-converter-source', 'USD');
        }

        $referrence = session_get('about-pay-reference');
        if (! session_get('about-pay-reference')) {
            $referrence = 'TFLIX-'.rand(100000000, 900000000);
        }
        session_put("about-pay-currency", $this->userCurrencySupportPaystack(1));
        session_put("about-pay-reference", $referrence);
        session_put("about-pay-details", model('admin')->getPaymentDetails(session_get('about-pay-type'), session_get('about-pay-typeid'))); 
        return $this->render($this->view('paystack::payment/payment'), true);
    }

    public function success() {
        require_once path('module/paystack/vendor/Paystack/src/autoload.php');

        $reference = $this->request->input('reference'); 
        $type = session_get('about-pay-type');
        $typeId = session_get('about-pay-typeid');

        $url = ($type == 'pro' or $type == 'pro-users') ? url('settings/pro') : url();
        $url = Hook::getInstance()->fire('payment.success.url', $url, array($type, $typeId));

        $paystack = new Yabacon\Paystack(config('paystack_secret_key', ''));

        $trx = $paystack->transaction->verify( [ 'reference' => $reference ] );
        if(!$trx->status){
            set_flashdata('msg', '<div class="alert alert-danger"><b>'.$trx->data->message. '</b> '.$trx->message.'</div>'); 
        } elseif('success' == $trx->data->status){
            // use trx info including metadata and session info to confirm that cartid
            // matches the one for which we accepted payment
            $params = array(
                'status' => $trx->data->status,
                'reference' => $_GET['reference']
            ); 
 
            $this->model('admin')->addTransaction(array(
                'amount' => $trx->data->amount,
                'type' => session_get('about-pay-type'),
                'type_id' => session_get('about-pay-typeid'),
                'sale_id' => $reference,
                'name' => model('user')->authUser['full_name'],
                'country' => model('user')->authUser['country'],
                'email' => model('user')->authUser['email'],
                'userid' => model('user')->authId
            ));
            Hook::getInstance()->fire('payment.success', null, array($type, $typeId));
            
            session_forget('about-pay-type');
            session_forget('about-pay-typeid');
            session_forget('about-pay-reference');
            session_forget('about-pay-currency');
            session_forget('about-pay-details');

            if (session_get('mobile-pay') == 1) $this->request->redirect(url('api/pay/success'));
            $this->request->redirect($url); 
        } else {
            set_flashdata('msg', '<div class="alert alert-danger">'.$trx->data->message. '</b> '.$trx->message.'</div>');  
        }
        return $this->render($this->view('paystack::payment/payment'), true);     
    } 

    function userCurrencySupportPaystack($ret = 0) {
        $supported = explode(',', config('paystack-supported-currency','ARS,AUD,BRL,CAD,CHF,CZK,DKK,EUR,GBP,HKD,HUF,INR,ILS,JPY,MXN,MYR,NGN,NOK,NZD,PHP,PLN,RUB,SEK,SGD,THB,TWD,USD'));
        $currency = model('user')->getUserCurrency();
        if ($ret) {
           return $currency; 
        }
        return in_array($currency, $supported);
    }
}

function set_flashdata($key = '', $value = '') { 
    $_SESSION[$key] = $value; 
}

function read_flashdata($key = '') { 
    if (isset($_SESSION[$key])) {
        $flash = $_SESSION[$key];
        unset($_SESSION[$key]);
        return $flash;
    } 
}
