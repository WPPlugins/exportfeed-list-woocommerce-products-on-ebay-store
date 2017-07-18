<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/***********************************************************
Plugin Name: ExportFeed: List WooCommerce Products on eBay Store
Plugin URI: www.exportfeed.com
Description: WooCommerce Product Feed Export :: <a target="_blank" href="http://www.exportfeed.com/tos/">How-To Click Here</a>
Author: ExportFeed.com
Version: 1.1.0
Author URI: www.exportfeed.com
Authors: roshanbh, sabinthapa8
***********************************************************/
// Create a helper function for easy SDK access.
require_once ABSPATH.'/wp-admin/includes/plugin.php';
$plugin_version_data = get_plugin_data( __FILE__ );
//current version: used to show version throughout plugin pages
define('EBCPF_VERSION', $plugin_version_data[ 'Version' ] );
define('EBCPF_BASENAME', plugin_basename( __FILE__ ) ); //exportfeed-list-woocoomerce-products-on-ebay-store/exportfeed-list-woocommerce-products-on-ebay-store.php
define('EBCPF_PATH', realpath(dirname(__FILE__)));
define('EBCPF_URL', plugins_url(). '/'. basename(dirname(__FILE__)) . '/' );
//functions to display exportfeed-list-woocoomerce-products-on-ebay-store version and checks for updates
include_once('ebcpf-information.php');

//action hook for plugin activation
register_activation_hook( __FILE__, 'ebcpf_plugin_activation' );
register_deactivation_hook( __FILE__, 'ebcpf_plugin_deactivation' );

global $ebcpf_feed_order, $ebcpf_feed_order_reverse;

require_once 'core/classes/cron.php';
require_once 'core/data/feedfolders.php';

if (get_option('ebcpf_feed_order_reverse') == '')
    add_option('ebcpf_feed_order_reverse', false);

if (get_option('ebcpf_feed_order') == '')
    add_option('ebcpf_feed_order', "id");

if (get_option('ebcpf_feed_delay') == '')
    add_option('ebcpf_feed_delay', "43200");

if (get_option('ebcpf_licensekey') == '')
    add_option('ebcpf_licensekey', "none");

if (get_option('ebcpf_localkey') == '')
    add_option('ebcpf_localkey', "none");
//***********************************************************
// cron schedules for Feed Updates
//***********************************************************

EBCPF_Cron::doSetup();
EBCPF_Cron::scheduleUpdate();

//***********************************************************
// Update Feeds (Cron)
//   2014-05-09 Changed to now update all feeds... not just Google Feeds
//***********************************************************

add_action('ebcpf_update_cartfeeds', 'ebcpf_update_all_cart_feeds');

function ebcpf_update_all_cart_feeds($doRegCheck = true) {

	require_once 'ebcpf-wpincludes.php'; //The rest of the required-files moved here
	require_once 'core/data/savedfeed.php';

	$reg = new EBCPF_License();
	if ($doRegCheck && ($reg->results["status"] != "Active"))
		return;

	do_action('ebcpf_load_feed_modifiers');
	add_action( 'ebcpf_feed_main_hook', 'ebcpf_update_all_feeds_step_2' );
	do_action('ebcpf_feed_main_hook');
}

function ebcpf_update_all_feeds_step_2() {
	global $wpdb;
	$feed_table = $wpdb->prefix . 'ebcpf_feeds';
	$sql = 'SELECT id, type, filename FROM ' . $feed_table;
	$feed_ids = $wpdb->get_results($sql);
	$savedProductList = null;
    $aggregateProviders = array();

	//***********************************************************
	//Main
	//***********************************************************
	foreach ($feed_ids as $index => $this_feed_id) {

		$saved_feed = new EBCPF_SavedFeed($this_feed_id->id);

		$providerName = $saved_feed->provider;

		//Make sure someone exists in the core who can provide the feed
		$providerFile = 'core/feeds/' . strtolower($providerName) . '/feed.php';
		if (!file_exists(dirname(__FILE__) . '/' . $providerFile))
			continue;
		require_once $providerFile;

		//Initialize provider data
		$providerClass = 'P' . $providerName . 'Feed';
		$x = new $providerClass();
		$x->aggregateProviders = $aggregateProviders;
		$x->savedFeedID = $saved_feed->id;

		$x->productList = $savedProductList;
		$x->getFeedData($saved_feed->category_id, $saved_feed->remote_category, $saved_feed->filename, $saved_feed);
		
		$savedProductList = $x->productList;
		$x->products = null;

	}

}

//***********************************************************
// Links From the Install Plugins Page (WordPress)
//***********************************************************

if (is_admin()) {

	require_once 'ebcpf-ebay-admin.php';
	$plugin = plugin_basename(__FILE__);
	add_filter("plugin_action_links_" . $plugin, 'ebcpf_manage_feeds_link');

}

//***********************************************************
//Function to create feed generation link  in installed plugin page
//***********************************************************
function ebcpf_manage_feeds_link($links) {

	$settings_link = '<a href="admin.php?page=ebcpf-manage-page">Manage Feeds</a>';
	array_unshift($links, $settings_link);
	return $links;

}