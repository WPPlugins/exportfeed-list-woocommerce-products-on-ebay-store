<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
define('XMLRPC_REQUEST', true);

require_once dirname(__FILE__) . '/../../data/feedcore.php';
require_once dirname(__FILE__) . '/../../model/eBayAccount.php';

$default_account = EBCPF_eBayAccount::getDefaultAccount();
global $wpdb;
global $EC;

$table_accounts = $wpdb->prefix .'ebcpf_ebay_accounts';
$table_currency = $wpdb->prefix .'ebcpf_ebay_currency';
$site_info = $wpdb->get_row("SELECT acc.site_id,acc.site_code,cur.site_abbr ,cur.currency_code  FROM $table_accounts AS acc 
LEFT JOIN $table_currency as cur on acc.site_id = cur.site_id WHERE default_account = 1" , ARRAY_A);
$table = $wpdb->prefix .'ebcpf_ebay_shipping';
$hidden_id = sanitize_text_field($_POST['hiddenId']);
if(!$hidden_id){
    $wpdb->insert($table , array(
        'paypal_email' => sanitize_email($_POST['paypal_email']),
        'paypal_accept' => intval($_POST['ebayPaypalAccepted']),
        'shippingfee' => sanitize_text_field($_POST['flatShipping']),
        'ebayShippingType' => sanitize_text_field($_POST['ebayShippingType']),
        'dispatchTime' => sanitize_text_field($_POST['dispatchTime']),
        'default_account' => $default_account,
        'shipping_service' => sanitize_text_field($_POST['shippingService']),
        'listingDuration' => sanitize_text_field($_POST['listingDuration']),
        'listingType' => sanitize_text_field($_POST['listingType']),
        'refundOption' => sanitize_text_field($_POST['refundOption']),
        'refundDesc' => sanitize_text_field($_POST['refundDesc']),
        'returnwithin' => sanitize_text_field($_POST['returnwithin']),
        'postalcode' => intval($_POST['postalcode']),
        'additionalshippingservice' => sanitize_text_field($_POST['additionalshippingservice']),
        'conditionType' => sanitize_text_field($_POST['conditionType']),
        'quantity' => intval($_POST['quantity']),
        'site_id' => $site_info['site_id'],
        'site_code' => $site_info['site_code'],
        'currency_code' => $site_info['currency_code'],
        'site_abbr' => $site_info['site_abbr']
    ));
}else{
     $wpdb->update($table , array(
        'paypal_email' => sanitize_email($_POST['paypal_email']),
        'paypal_accept' => intval($_POST['ebayPaypalAccepted']),
        'shippingfee' => sanitize_text_field($_POST['flatShipping']),
        'ebayShippingType' => sanitize_text_field($_POST['ebayShippingType']),
        'dispatchTime' => sanitize_text_field($_POST['dispatchTime']),
        'default_account' => $default_account,
        'shipping_service' => sanitize_text_field($_POST['shippingService']),
        'listingDuration' => sanitize_text_field($_POST['listingDuration']),
        'listingType' => sanitize_text_field($_POST['listingType']),
        'refundOption' => sanitize_text_field($_POST['refundOption']),
        'refundDesc' => sanitize_text_field($_POST['refundDesc']),
        'returnwithin' => sanitize_text_field($_POST['returnwithin']),
        'postalcode' => intval($_POST['postalcode']),
        'additionalshippingservice' => sanitize_text_field($_POST['additionalshippingservice']),
        'conditionType' => sanitize_text_field($_POST['conditionType']),
        'quantity' => intval($_POST['quantity']),
        'site_id' => $site_info['site_id'],
        'site_code' => $site_info['site_code'],
        'currency_code' => $site_info['currency_code'],
        'site_abbr' => $site_info['site_abbr']
        ),
        array( 'ID' => sanitize_text_field($_POST['hiddenId']) )
    );
}





