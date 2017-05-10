<?php

class WC_2C2P_Validation_Helper{

	private $pg_2c2p_setting_values;

	public $wc_2c2p_error = array(		    
		"payment_description" 	=> "",
		"order_id" 				=> "",			
		"amount" 				=> "",
		"currency"				=> "",
		);

	function __construct() {
		$objWC_Gateway_2c2p = new WC_Gateway_2c2p();
		$this->pg_2c2p_setting_values = $objWC_Gateway_2c2p->wc_2c2p_get_setting();
	}

	function wc_2c2p_is_valid_merchant_request($parameter){

		if(empty($parameter['order_id'])){
			$this->wc_2c2p_error['order_id'] = "Order id cannot be blank.";
		}
		if(empty($parameter['payment_description'])){
			$this->wc_2c2p_error['payment_description'] = "Payment Description cannot be blank.";
		}
		if(empty($parameter['amount'])){
			$this->wc_2c2p_error['amount'] = "Amount cannot be blank.";
		}

		if(!empty($parameter['order_id'])){
			if(strcasecmp($parameter['payment_option'], '1') == 0){
				if(strlen($parameter['order_id']) > 12){
					$this->wc_2c2p_error['order_id'] = "Order id is limited to 12 character when payment option is 123.";
				}					
			}
			else{
				if(strlen($parameter['order_id']) > 20){
					$this->wc_2c2p_error['order_id'] = "Order id is limited to 20 character.";
				}	
			}
		}
		if(!empty($parameter['amount'])){
			if($parameter['amount'] <= 0){
				$this->wc_2c2p_error['amount'] = "Amount must be greater than 0.";
			}
		}
		if(!empty($parameter['amount'])){
			if(strlen($parameter['amount']) > 12){
				$this->wc_2c2p_error['amount'] = "Amount is limited to 12 character.";
			}
			else{
					//Calculate currenycy by methods.
				if(!is_numeric($parameter['amount'])){
					$this->wc_2c2p_error['amount'] = "Please enter amount is digit's.";
				}
				else{
					$this->wc_2c2p_validate_currency_exponent($parameter['amount']);
				}
			}
		}

		if(strcasecmp($this->pg_2c2p_setting_values['wc_2c2p_currency'], "0") == 0){			
			$this->wc_2c2p_error['currency'] = "Currency code is not selected. Please try to contact your website admin.";
		}

		foreach ($this->wc_2c2p_error as $key => $value) {
			if(!empty($value)){
				return false;
			}
		}
		return true;
	}
	//This function is used to validate the CurrencyExponent.
	public function wc_2c2p_validate_currency_exponent($amount){

		$objWC_2c2p_currency = new WC_2c2p_currency();

		$exponent = 0;
		$isFouned = false;

		$currenyCode = $this->pg_2c2p_setting_values['wc_2c2p_currency'];
		//$currencyCodeArray = WC_2c2p_currency::$wc_2c2p_currency_code;
		$currencyCodeArray = $objWC_2c2p_currency->get_currency_code();

		foreach ($currencyCodeArray as $key => $value) {
			if($value['Num'] === $currenyCode){                        
				$exponent = $value['exponent'];
				$isFouned = true;
				break;
			}
		}

		if($isFouned){
			if($exponent == 0){
				$amount = (int) $amount;
			}
			else{

				//$pg_2c2p_exponent = WC_2c2p_currency::$wc_2c2p_exponent;
				$pg_2c2p_exponent = $objWC_2c2p_currency->get_currency_exponent();
				$multi_value = $pg_2c2p_exponent[$exponent];

				$amount = ($amount * $multi_value);
			}

			$amount = str_pad($amount, 12, '0', STR_PAD_LEFT);
		}		
		return $amount;
	}
}


?>