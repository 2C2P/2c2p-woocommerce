<?php
/*
Plugin Name: 2C2P Redirect API for WooCommerce
Description: Accept Payment (Credit/Debit Cards, Alipay, Alternative/Cash Payments) on your WooCommerce webstore.
Version: 7.0.0
Author: 2C2P 
Author URI: http://www.2c2p.com/
*/

add_action('plugins_loaded', 'fun2c2p_init', 0);

function fun2c2p_init()
{
    if (!class_exists('WC_Payment_Gateway')) return;

    //Included required files.
    require_once(dirname(__FILE__) . '/Includes/wc-2c2p-constant.php');
    require_once(dirname(__FILE__) . '/Includes/wc-2c2p-request-helper.php');
    require_once(dirname(__FILE__) . '/Includes/wc-2c2p-hash-helper.php');
    require_once(dirname(__FILE__) . '/Includes/wc-2c2p-validation-helper.php');
    require_once(dirname(__FILE__) . '/Includes/wc-2c2p-meta-data-helper.php');

    //Gateway class    
    class WC_Gateway_2c2p extends WC_Payment_Gateway {
        //Make __construct()
        public function __construct(){

            $this->id                 = '2c2p'; // ID for WC to associate the gateway values
            $this->method_title       = '2C2P'; // Gateway Title as seen in Admin Dashboad
            $this->method_description = '2C2P - Redefining Payments, Simplifying Lives'; // Gateway Description as seen in Admin Dashboad
            $this->has_fields         = false; // Inform WC if any fileds have to be displayed to the visitor in Frontend 
            
            $this->init_form_fields(); // defines your settings to WC
            $this->init_settings(); // loads the Gateway settings into variables for WC
            
            // Special settigns if gateway is on Test Mode
            $test_title       = '';
            $test_description = '';
            if (strcasecmp($this->settings['test_mode'], 'demo2') == 0 ) {
                $demo             = '2C2PFrontEnd/';
                $test_description = '<br/><br/>Test Mode is <strong>ACTIVE</strong>, Do not use personal detail to pay. Use only test detail for payment.<br/>';
            } else {
                $demo = '';
            }
            //END--test_mode=yes
            
            $this->title            = $this->settings['title'] . $test_title; // Title as displayed on Frontend
            $this->description      = $this->settings['description'] . $test_description; // Description as displayed on Frontend
            $this->liveurl          = 'https://' . $this->settings['test_mode'] . '.2c2p.com/' . $demo . 'RedirectV3/payment';
            $this->service_provider = $this->settings['service_provider'];
            $this->msg['message']   = '';
            $this->msg['class']     = '';
            
            add_action('init', array(&$this,'check_2c2p_response'));
            add_action('woocommerce_api_' . strtolower(get_class($this)), array($this,'check_2c2p_response')); //update for woocommerce >2.0
            add_action('woocommerce_receipt_2c2p', array(&$this,'receipt_page'));
            add_action('woocommerce_checkout_update_order_meta', array(&$this,'wc_2c2p_custom_checkout_field_update_order_meta'));            

            //Load script's
            add_action('wp_enqueue_scripts', array( &$this, 'wc_2c2p_load_scripts'));

            if (version_compare(WOOCOMMERCE_VERSION, '2.0.0', '>=')) {
                add_action('woocommerce_update_options_payment_gateways_' . $this->id, array(&$this,'process_admin_options')); //update for woocommerce >2.0
            } 
            else {
                add_action('woocommerce_update_options_payment_gateways', array(&$this,'process_admin_options')); // WC-1.6.6
            }

        } //END-__construct    

        //Validating 123 payment expiry textbox    
        public function validate_wc_2c2p_123_payment_expiry_field( $key, $value ) {
            if (empty($value)) {
                WC_Admin_Settings::add_error( esc_html__( 'Please enter 123 payment expiry like (8 - 720)', 'woo_2c2p'));
                return $value = 0;
            }
            else if(!is_numeric($value)){
                WC_Admin_Settings::add_error( esc_html__( 'Please enter 123 payment expiry in numeric like (8 - 720)', 'woo_2c2p'));
                return $value = 0;
            }
            else if(!($value >= 8 && $value <= 720)){
                WC_Admin_Settings::add_error( esc_html__( 'Please enter 123 payment expiry in between 8 - 720 hours only', 'woo_2c2p'));
                return $value = 0;
            }

            return $value;
        }

        public function validate_key_id_field( $key, $value ) {
            if (empty($value)) {
                WC_Admin_Settings::add_error( esc_html__( 'Please Enter Merchant Id', 'woo_2c2p'));
                return $value;
            }
            return $value;
        }

         public function validate_key_secret_field( $key, $value ) {
            if (empty($value)) {
                WC_Admin_Settings::add_error( esc_html__( 'Please Enter Secret Key', 'woo_2c2p'));
                return $value;
            }
            return $value;
        }

        function wc_2c2p_custom_checkout_field_update_order_meta($order_id){
            if ($_POST['wc_2c2p_stored_card'] != 0) {                
                update_post_meta($order_id, 'wc_2c2p_stored_card_token_id', $_POST['wc_2c2p_stored_card']);
            }
        }

        //load script for 2c2p payment.
        function wc_2c2p_load_scripts(){            
            wp_enqueue_script('wc-2c2p-scripts', plugin_dir_url( __FILE__ ) . 'Includes/wc-2c2p-script.js',array('jquery'));            
        }

        public function wc_2c2p_get_setting(){
            return $this->settings;
        }
        public function wc_2c2p_response_url($order_Id){
            $order = new WC_Order($order_Id);
            
            $redirect_url = $this->get_return_url($order);
            
            if (version_compare(WOOCOMMERCE_VERSION, '2.0.0', '>=')) {
                $redirect_url = add_query_arg('wc-api', get_class($this), $redirect_url);
            }
            
            return $redirect_url;
        }
        
        //Initiate Form Fields in the Admin Backend        
        function init_form_fields(){
            $this->form_fields = include(dirname(__FILE__) . '/Includes/wc-2c2p-setting.php');
        }
        
        //Admin Panel Options.Show info on Admin Backend
        public function admin_options() {            
            echo '<h3>' . __('2C2P', 'woo_2c2p') . '</h3>';
            echo '<p>' . __('2C2P provides a wide range of payment. you just save your account detail in it and enjoy shopping just in one click on 2C2P', 'woo_2c2p') . '</p>';
            echo '<p><small><strong>' . __('Confirm your Mode: Is it LIVE or TEST.') . '</strong></small></p>';
            echo '<table class="form-table">';
            // Generate the HTML For the settings form.
            $this->generate_settings_html();
            echo '</table>';
        }
        
        //There are no payment fields, but we want to show the description if set.
        function payment_fields(){
            if (!empty($this->description)) {
                echo wpautop(wptexturize($this->description));

                if(is_user_logged_in()){
                    if(strcasecmp($this->settings['wc_2c2p_stored_card_payment'], "yes") == 0){
                        $strHtml = "";
                        $wc_2c2p_stored_card = get_user_meta(get_current_user_id(),"wc_2c2p_stored_card");                        
                        foreach ($wc_2c2p_stored_card as $key => $value) {                            
                            foreach ($value as $innerKey => $innerValue) {
                                $strHtml .= "<option value='".key($value) ."'>".key($innerValue)."</option>";
                            }
                        }

                        if(!empty($strHtml)){
                            echo "<table id='tblToken'>";
                            echo "<tr>";
                            echo "<th style='width:140px;'>Select my card</th>";
                            echo "<td> <select id='wc_2c2p_stored_card' name='wc_2c2p_stored_card' >";                            
                            echo "<option value='0'>I'll use a new card</option>";
                            echo $strHtml;
                            echo "</select></td>";
                            echo "<td><input type='button' name='btn1' value='Remove Card' onclick='doTokenRemove();' ></td>";
                            echo "</tr>";
                            //echo "<tr><td colspan='3'><p id='wc_2c2p_message'></p></td>";
                            echo "</table>";                            
                        }  

                        echo "<input type='hidden' value='".admin_url('admin-ajax.php') ."' id='ajax_url' />";                                
                    }
                }
            }
        }
        
        //Receipt Page
        function receipt_page($order) {            
            echo $this->generate_2c2p_form($order);
        }
        
        //Generate button link
        function generate_2c2p_form($order_id) {

            $wc_2c2p_stored_card_token_id = 0;

            if(is_user_logged_in()){                
                $wc_2c2p_token_id = get_post_meta($order_id, 'wc_2c2p_stored_card_token_id', true);
                $wc_2c2p_stored_card = get_user_meta(get_current_user_id(),"wc_2c2p_stored_card");

                foreach ($wc_2c2p_stored_card as $key => $value) {
                    foreach ($value as $innerKey => $innerValue) {
                        if(strcasecmp(key($value), $wc_2c2p_token_id) == 0){
                            $wc_2c2p_stored_card_token_id = $innerValue[key($innerValue)];
                            break;
                        }
                    }
                }
            }        

            global $woocommerce;
            $order = new WC_Order( $order_id );
            $redirect_url=$this->get_return_url( $order );

            if (strcasecmp($this->service_provider,'money')  == 0) {
                $service_provider = '';
            } 
            else {
                $service_provider = '2C2P';
            }
    
            if(is_user_logged_in()){ // Customer is loggedin.
                $loggedin_user_data = wp_get_current_user();                
                $cust_email         = $loggedin_user_data->data->user_email;    
            }
            else{
                $cust_email = $order->data['billing']['email']; //Gust customer.
            }
            
            $item_count = count($order->get_items());
            $current_count = 0;
            foreach($order->get_items() as $item){                
                $product_name .= $item['name'];
                $current_count++;

                if($item_count !== $current_count)
                    $product_name .= ',';                
            }

            $product_name .= '.';

            $fun2c2p_args = array(
                'payment_description'   => $product_name,
                'order_id'              => $order_id,
                'invoice_no'            => $order_id,
                'amount'                => $order->order_total,
                'customer_email'        => $cust_email,
                'pay_category_id'       => "",
                'promotion'             => "",
                'user_defined_1'        => "",
                'user_defined_2'        => "",
                'user_defined_3'        => "",
                'user_defined_4'        => "",
                'user_defined_5'        => "",
                'stored_card_unique_id' => $wc_2c2p_stored_card_token_id != 0 ? $wc_2c2p_stored_card_token_id : "",
                );
            
            $objWC_2C2P_Validation_Helper = new WC_2C2P_Validation_Helper();
            $isValid = $objWC_2C2P_Validation_Helper->wc_2c2p_is_valid_merchant_request($fun2c2p_args);

            if (!$isValid) {                
                foreach ($objWC_2C2P_Validation_Helper->wc_2c2p_error as $key => $value) {
                    echo _e($value, 'woo_2c2p');
                }

                echo _e("</br>","woo_2c2p");
                echo _e('&nbsp;&nbsp;&nbsp;&nbsp<a class="button cancel" href="' . $order->get_cancel_order_url() . '">' . __('Cancel order &amp; restore cart', 'woo_2c2p') . '</a>','woo_2c2p');
                return;
            }
            else{
                echo '<p><strong>' . __('Thank you for your order.', 'woo_2c2p') . '</strong><br/>' . __('The payment page will open if you click on button "Pay via 2C2P".', 'woo_2c2p') . '</p>';
            }
            
            $fun2c2p_args['amount'] = $objWC_2C2P_Validation_Helper->wc_2c2p_validate_currency_exponent($fun2c2p_args['amount']);
            
            $objwc_2c2p_construct_request = new wc_2c2p_construct_request_helper();            
            $wc_2c2p_form_field = $objwc_2c2p_construct_request->wc_2c2p_construct_request(is_user_logged_in(),$fun2c2p_args);
            
            $strHtml .= '<form action="' . $this->liveurl . '" method="post" id="2c2p_payment_form">';
            $strHtml .= $wc_2c2p_form_field;
            $strHtml .= '<input type="submit" class="button-alt" id="submit_2c2p_payment_form" value="' . __('Pay via 2C2P', 'woo_2c2p') . '" />';
            $strHtml .= '&nbsp;&nbsp;&nbsp;&nbsp<a class="button cancel" href="' . $order->get_cancel_order_url() . '">' . __('Cancel order &amp; restore cart', 'woo_2c2p') . '</a>';
            $strHtml .= '</form>';
            
            return $strHtml;
        }
        
        //Process the payment and return the result         
        function process_payment($order_id)
        {
            global $woocommerce;
            $order = new WC_Order($order_id);
            
            if (version_compare(WOOCOMMERCE_VERSION, '2.1.0', '>=')) { // For WC 2.1.0
                $checkout_payment_url = $order->get_checkout_payment_url(true);
            } else {
                $checkout_payment_url = get_permalink(get_option('woocommerce_pay_page_id'));
            }
            
            return array('result' => 'success','redirect' => add_query_arg('order', $order->id, add_query_arg('key', $order->order_key, $checkout_payment_url)));
        }
        
        function check_2c2p_response() {

            global $woocommerce;
                            
            if (isset($_REQUEST['order_id']) && isset($_REQUEST['merchant_id'])) {

                $order_id = $_REQUEST['order_id'];
                
                if (!empty($order_id)) {
                    try {
                        $order = new WC_Order($order_id);
                
                        $hash  = $_REQUEST['hash_value'];                        
                        $status = $_REQUEST['payment_status'];                        
                        
                        $objwc_2c2p_is_valid_hash = new wc_2c2p_hash_helper();
                        $isValidHash = $objwc_2c2p_is_valid_hash->wc_2c2p_is_valid_hash($_REQUEST);
                        
                        $trans_authorised = false;
                        
                        if ($order->status !== 'completed') {
                                                
                            if ($isValidHash) {
                                //Save data to meta data table.
                                $objWC_2C2P_Meta_Data_Helper = new WC_2C2P_Meta_Data_Helper();
                                $objWC_2C2P_Meta_Data_Helper-> wc_2c2p_store_response_meta($_REQUEST);

                                if (strcasecmp($status, "000") == 0) { //Success payment.                            
                                    $isFounded = false;

                                    //Stored stored card toek into user meta table with loggedin users only.
                                    if(is_user_logged_in() || $order->user_id > 0 ){
                                        $stored_card = get_user_meta($order->user_id,"wc_2c2p_stored_card");

                                        $stored_card_data = array($_REQUEST['order_id'] => 
                                                            array($_REQUEST['masked_pan']  => $_REQUEST['stored_card_unique_id']));

                                        if(empty($stored_card)){                                            
                                            if(!empty($_REQUEST['stored_card_unique_id'])){
                                                add_user_meta($order->user_id, "wc_2c2p_stored_card", $stored_card_data);
                                            }
                                        }
                                        else{
                                            foreach ($stored_card as $key => $value) {
                                                foreach ($value as $innerKey => $innerValue) {                                                    
                                                    if(array_key_exists('masked_pan',$_REQUEST) && array_key_exists('stored_card_unique_id',$_REQUEST)){
                                                        if((strcasecmp(Key($innerValue), $_REQUEST['masked_pan']) == 0 && strcasecmp($innerValue[key($innerValue)], $_REQUEST['stored_card_unique_id']) == 0)){
                                                            $isFounded = true;
                                                            break;
                                                        }
                                                    }
                                                }
                                            }                                        

                                            if(!$isFounded) {
                                                if(!empty($_REQUEST['masked_pan']) && !empty($_REQUEST['stored_card_unique_id']))
                                                    add_user_meta($order->user_id, "wc_2c2p_stored_card", $stored_card_data);
                                            }
                                        }
                                    } 

                                    $trans_authorised     = true;
                                    $this->msg['message'] = "Thank you for shopping with us. Your account has been charged and your transaction is successful.";
                                    $this->msg['class']   = 'woocommerce-message';
                                    
                                    if (strcasecmp($order->status, 'processing') == 0) {
                                        $order->add_order_note('order_id: ' . $_REQUEST['order_id'] . '<br/>transaction_ref: ' . $_REQUEST['transaction_ref'] . '<br/>payment status: ' . $_REQUEST['payment_status'] . '<br/>amount: ' . $_REQUEST['amount'] . '<br/>eci: ' . $_REQUEST['eci'] . '<br/>transaction_datetime: ' . $_REQUEST['transaction_datetime'] . '<br/>approval_code: ' . $_REQUEST['approval_code']);
                                        $order->update_status('completed');
                                    } 
                                    else {
                                        $order->update_status('completed');                                        
                                        $order->payment_complete();
                                        $order->add_order_note('2C2P payment transaction successful.<br/>order_id: ' . $_REQUEST['order_id'] . '<br/>transaction_ref: ' . $_REQUEST['transaction_ref'] . '<br/>eci: ' . $_REQUEST['eci'] . '<br/>transaction_datetime: ' . $_REQUEST['transaction_datetime'] . '<br/>approval_code: ' . $_REQUEST['approval_code']);
                                        $woocommerce->cart->empty_cart();
                                    }
                                } 
                                else if (strcasecmp($status, "001") == 0) {
                                    $trans_authorised     = true;
                                    $this->msg['message'] = "Thank you for shopping with us. Right now your payment status is pending. We will keep you posted regarding the status of your order through eMail";
                                    $this->msg['class']   = 'woocommerce-info';
                                    $order->add_order_note('2C2P payment status is pending<br/>order_id: ' . $_REQUEST['order_id'] . '<br/>transaction_ref: ' . $_REQUEST['transaction_ref'] . '<br/>eci: ' . $_REQUEST['eci'] . '<br/>transaction_datetime: ' . $_REQUEST['transaction_datetime'] . '<br/>approval_code: ' . $_REQUEST['approval_code']);
                                    $order->update_status('on-hold');
                                    $woocommerce->cart->empty_cart();
                                } 
                                else {
                                    $this->msg['class']   = 'woocommerce-error';
                                    $this->msg['message'] = "Thank you for shopping with us. However, the transaction has been declined.";
                                    $order->add_order_note('order_id: ' . $_REQUEST['order_id'] . '<br/>transaction_ref: ' . $_REQUEST['transaction_ref'] . '<br/>eci: ' . $_REQUEST['eci'] . '<br/>transaction_datetime: ' . $_REQUEST['transaction_datetime'] . '<br/>approval_code: ' . $_REQUEST['approval_code']);
                                }
                            } 
                            else {
                                $this->msg['class']   = 'error';
                                $this->msg['message'] = "Security Error. Illegal access detected.";
                                $order->add_order_note('Checksum ERROR: ' . json_encode($_REQUEST));
                            }
                            if (!$trans_authorised) {
                                //$order->update_status($order->status);
                                $order->update_status('cancelled');                                
                            }                            
                        }
                    }
                    catch (Exception $e){                        
                        $msg = "Error";
                    }
                }
                
                if (strcasecmp($status, "000")  == 0  || strcasecmp($status, "001")  == 0) {                    
                    $redirect_url = $this->get_return_url($order);
                } 
                else {
                    $redirect_url = get_site_url() . "/cart";                    
                }
                
                if (version_compare(WOOCOMMERCE_VERSION, '2.1.0', '>=')) { // For WC 2.1.0
                    $checkout_payment_url = $order->get_checkout_payment_url(true);
                } 
                else {
                    $checkout_payment_url = get_permalink(get_option('woocommerce_pay_page_id'));
                }
                
                $redirect_url1 = get_site_url() . "/cart";
                wp_redirect($redirect_url);
                exit;
            }
        }

        public function thanku_page() { }    
        
    } //END-class
    
    add_action('wp_ajax_paymentajax', 'wp_2c2p_remove_stored_card_Id_ajax');
    add_action('wp_ajax_nopriv_paymentajax', 'wp_2c2p_remove_stored_card_Id_ajax');

    function wp_2c2p_remove_stored_card_Id_ajax(){
        $data = $_POST['data']; 

        $wc_2c2p_stored_card = get_user_meta(get_current_user_id(),"wc_2c2p_stored_card");

        foreach ($wc_2c2p_stored_card as $key => $value) {
            foreach ($value as $innerKey => $innerValue) {
                if(strcasecmp(key($value), $data['token_id']) == 0){                    
                    $stored_card_data = array($data['token_id'] => 
                    array(key($innerValue)  => $innerValue[key($innerValue)]));
                    break;
                }
            }
        }

        echo delete_user_meta(get_current_user_id(), 'wc_2c2p_stored_card',$stored_card_data); die;        
    }

    //Add the Gateway to WooCommerce
    function woocommerce_add_gateway_2c2p_gateway($methods) {
        $methods[] = 'WC_Gateway_2c2p';
        return $methods;
    } //END-wc_add_gateway
    
    add_filter('woocommerce_payment_gateways', 'woocommerce_add_gateway_2c2p_gateway');
    
} //END-init

//Virasat Solutions 'Settings' link on plugin page
add_filter('plugin_action_links', 'fun2c2p_add_action_plugin', 10, 5);
function fun2c2p_add_action_plugin($actions, $plugin_file)
{
    static $plugin;
    
    if (!isset($plugin))
        $plugin = plugin_basename(__FILE__);
    if ($plugin == $plugin_file) {

        $settings = array(
            'settings' => '<a href="admin.php?page=wc-settings&tab=checkout&section=WC_Gateway_2c2p">' . __('Settings') . '</a>'
            );
        
        $actions = array_merge($settings, $actions);
    }
    
    return $actions;
} //END-settings_add_action_link