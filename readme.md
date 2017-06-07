=== 2C2P Redirect API, Secure Payment by 2C2P  ===
Contributors: 2C2P
Tags: ecommerce, e-commerce, woocommerce, wordpress ecommerce, 2c2p, payment gateway, credit card
Requires at least: 2.6.0  
Tested up to: 7.0.0
Stable tag: 7.0.0
License: GPLv3
License URI: https://www.2c2p.com/

Accept Credit Cards and Debit Cards on your WooCommerce store.

== Description == 

In order to connect to 2C2P with the plugin you will need a sandbox account or a test account.
Additional details on the payment process and configuration can be found in this secure link - https://developer.2c2p.com/v1.0/docs/redirect-api-red.

== Installation == 

1. Upload the plugin files to the `/wp-content/plugins/plugin-name` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the Settings->Plugin Name screen to configure the plugin enter the below data for setting page.
	1. Enable/Status -> Enable/Disable the plugin. If disabled , it will not show 2C2P on the checkout page
	2. Title      	 -> Display title for 2c2p payment getaway.
	3. Description   -> Display the description when you select payment type as 2c2p.
	2. Merchant ID   -> MID as shown in the 2C2P merchant interface
	3. Secrect Key   -> Secret key from the 2C2P merchant interface
	4. Mode			 -> Select model for payment like Test,Live
	5. Enable/Diable -> Enable the stored card payment when you stick the checkbox.
	6. 123 payment expiry -> Enter valid hours between (8-720).	

== Getting sandbox account == 
For testing, you may use the sandbox credentials provided at our developer portal @ https://developer.2c2p.com/docs/sandbox

= Updating =

Automatic updates should work like a charm; as always though, ensure you backup your site just in case.

== Frequently Asked Questions == (Can we add any Frequently Asked Questions ? if yes the please privide the list of question and answers)

== Screenshots == 

1. Click the setting button once plugin is actived.
2. Opened the setting page and enter configuration setting field for 2c2p payment.

== Changelog == 

== Upgrade Notice == 

= 1.2.4 =
* Fix - Free subscription trails not allowed.
* Fix - Subscription recurring billing after free trial not working.