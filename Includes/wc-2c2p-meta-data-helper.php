<?php

class WC_2C2P_Meta_Data_Helper{

	function __construct() { }

	//This function is stored meta data value into wordpress meta table.
	public function wc_2c2p_store_response_meta($parameter){

		$order_id = $parameter['order_id'];

		update_post_meta($order_id, 'wc_2c2p_order_id_meta', $order_id);

		if(array_key_exists('version',$parameter)) 
			update_post_meta($order_id, 'wc_2c2p_version_meta', $parameter['version']);

		if(array_key_exists('request_timestamp',$parameter)) 
			update_post_meta($order_id, 'wc_2c2p_request_timestamp_meta', $parameter['request_timestamp']);

		if(array_key_exists('merchant_id',$parameter)) 
			update_post_meta($order_id, 'wc_2c2p_merchant_id_meta', $parameter['merchant_id']);	

		if(array_key_exists('invoice_no',$parameter)) 			
			update_post_meta($order_id, 'wc_2c2p_invoice_no_meta', $parameter['invoice_no']);

		if(array_key_exists('currency',$parameter)) 
			update_post_meta($order_id, 'wc_2c2p_currency_meta', $parameter['currency']);

		if(array_key_exists('amount',$parameter))
			update_post_meta($order_id, 'wc_2c2p_amount_meta', $parameter['amount']);		

		if(array_key_exists('transaction_ref',$parameter)) 
			update_post_meta($order_id, 'wc_2c2p_transaction_ref_meta', $parameter['transaction_ref']);

		if(array_key_exists('approval_code',$parameter)) 			
			update_post_meta($order_id, 'wc_2c2p_approval_code_meta', $parameter['approval_code']);

		if(array_key_exists('eci',$parameter)) 
			update_post_meta($order_id, 'wc_2c2p_eci_meta', $parameter['eci']);

		if(array_key_exists('transaction_datetime',$parameter))
			update_post_meta($order_id, 'wc_2c2p_transaction_datetime_meta', $parameter['transaction_datetime']);

		if(array_key_exists('payment_channel',$parameter))
			update_post_meta($order_id, 'wc_2c2p_payment_channel_meta', $parameter['payment_channel']);

		if(array_key_exists('payment_status',$parameter))
			update_post_meta($order_id, 'wc_2c2p_payment_status_meta', $parameter['payment_status']);		

		if(array_key_exists('channel_response_code',$parameter))
			update_post_meta($order_id, 'wc_2c2p_channel_response_code_meta', $parameter['channel_response_code']);		

		if(array_key_exists('channel_response_desc',$parameter))
			update_post_meta($order_id, 'wc_2c2p_channel_response_desc_meta', $parameter['channel_response_desc']);

		if(array_key_exists('masked_pan',$parameter))
			update_post_meta($order_id, 'wc_2c2p_masked_pan_desc_meta', $parameter['masked_pan']);

		if(array_key_exists('stored_card_unique_id',$parameter))
			update_post_meta($order_id, 'wc_2c2p_stored_card_unique_id_meta', $parameter['stored_card_unique_id']);

		if(array_key_exists('backend_invoice',$parameter))
			update_post_meta($order_id, 'wc_2c2p_backend_invoice_meta', $parameter['backend_invoice']);		

		if(array_key_exists('paid_channel',$parameter))
			update_post_meta($order_id, 'wc_2c2p_paid_channel_meta', $parameter['paid_channel']);		

		if(array_key_exists('paid_agent',$parameter))
			update_post_meta($order_id, 'wc_2c2p_paid_agent_meta', $parameter['paid_agent']);		

		if(array_key_exists('recurring_unique_id',$parameter))
			update_post_meta($order_id, 'wc_2c2p_recurring_unique_id_meta', $parameter['recurring_unique_id']);		

		if(array_key_exists('user_defined_1',$parameter))
			update_post_meta($order_id, 'wc_2c2p_user_defined_1_meta', $parameter['user_defined_1']);

		if(array_key_exists('user_defined_2',$parameter))
			update_post_meta($order_id, 'wc_2c2p_user_defined_2_meta', $parameter['user_defined_2']);

		if(array_key_exists('user_defined_3',$parameter))
			update_post_meta($order_id, 'wc_2c2p_user_defined_3_meta', $parameter['user_defined_3']);		

		if(array_key_exists('user_defined_4',$parameter))
			update_post_meta($order_id, 'wc_2c2p_user_defined_4_meta', $parameter['user_defined_4']);

		if(array_key_exists('user_defined_5',$parameter))
			update_post_meta($order_id, 'wc_2c2p_user_defined_5_meta', $parameter['user_defined_5']);

		if(array_key_exists('browser_info',$parameter))
			update_post_meta($order_id, 'wc_2c2p_browser_info_meta', $parameter['browser_info']);

		if(array_key_exists('ippPeriod',$parameter))
			update_post_meta($order_id, 'wc_2c2p_ippPeriod_meta', $parameter['ippPeriod']);

		if(array_key_exists('ippInterestType',$parameter))
			update_post_meta($order_id, 'wc_2c2p_ippInterestType_meta', $parameter['ippInterestType']);

		if(array_key_exists('ippInterestRate',$parameter))
			update_post_meta($order_id, 'wc_2c2p_ippInterestRate_meta', $parameter['ippInterestRate']);

		if(array_key_exists('ippMerchantAbsorbRate',$parameter)) 
			update_post_meta($order_id, 'wc_2c2p_ippMerchantAbsorbRate_meta', $parameter['ippMerchantAbsorbRate']);
	}
}

?>