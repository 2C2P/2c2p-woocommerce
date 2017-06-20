 <?php

return array(    
    'enabled' => array(
        'title' => __('Enable/Disable', 'woo_2c2p'),
        'type' => 'checkbox',
        'label' => __('Enable 2c2p', 'woo_2c2p'),
        'default' => 'yes',
        'description' => 'If checked, 2C2P will be available as payment option at checkout page'
    ),    
    'title' => array(
        'title' => __('Title', 'woo_2c2p'),
        'type' => 'text',
        'default' => __('2C2P Payment', 'woo_2c2p'),
        'description' => __('This controls the title which the user sees during checkout.', 'woo_2c2p'),
        'desc_tip' => true
    ),    
    'description' => array(
        'title' => __('Description', 'woo_2c2p'),
        'type' => 'textarea',
        'default' => __('Pay Securely by Credit / Debit card or Internet banking through 2C2P.', 'woo_2c2p'),
        'description' => __('This controls the description which the user sees during checkout.', 'woo_2c2p'),
        'desc_tip' => true
    ),    
    'wc_2c2p_api_details' => array(
        'title'       => __( 'API credentials', 'woocommerce' ),
        'type'        => 'title',
        'description' => '',
    ),
    'key_id' => array(
        'title' => __('Merchant ID', 'woo_2c2p'),
        'type' => 'text',
        'description' => __('Provided by 2C2P'),
        'desc_tip' => true
    ),    
    'key_secret' => array(
        'title' => __('Secret Key', 'woo_2c2p'),
        'type' => 'text',
        'description' => __('Provided by 2C2P, available in my2C2P Portal'),
        'desc_tip' => true
    ),    
    'test_mode' => array(
        'title' => __('Mode', 'woo_2c2p'),
        'type' => 'select',
        'label' => __('Tranasction Mode.', 'woo_2c2p'),    
        'default' => 'demo2',
        'description' => __('Tranasction Mode'),
        'desc_tip' => true,
        'class'        => 'wc-enhanced-select',
        'options' => array(
            'demo2' => 'Test Mode',
            't' => 'Live Mode'
        ),
    ),
    'wc_2c2p_advanced_options' => array(
        'title'       => __( 'Advanced options', 'woocommerce' ),
        'type'        => 'title',
        'description' => '',
    ),
    'wc_2c2p_stored_card_payment' => array(
        'title' => __('Enable/Disable', 'woo_2c2p'),
        'type' => 'checkbox',
        'label' => __('Stored Card Payment', 'woo_2c2p'),  
		'description' => __('Allow customers to save their card details for future payments.'),
        'desc_tip' => true
    ),   
    'wc_2c2p_123_payment_expiry' => array(
        'title' => __('123 Payment Expiry (hours)', 'woo_2c2p'),
        'type' => 'text',
		'default' => '72',
        'description' => __('123 Payment Expiry in hours, valid between (8-720)', 'woo_2c2p'),
        'desc_tip' => true,        
    ),
);
?> 