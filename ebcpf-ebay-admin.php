<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Required admin files
 *
 */
require_once 'ebcpf-setup.php';

/**
 * Hooks for adding admin specific styles and scripts
 *
 */
function ebcpf_styles_ands_scripts_register($hook)
{
    if(!strchr($hook,'ebcpf'))
        return false;
	wp_register_style( 'ebcpf-exportfeed-ebay-feeds-style', plugins_url( 'css/ebcpf.css', __FILE__ ) );
	wp_enqueue_style( 'ebcpf-exportfeed-ebay-feeds-style' );

	wp_register_style( 'ebcpf-exportfeed-ebay-feeds-colorstyle', plugins_url( 'css/colorbox.css', __FILE__ ) );
	wp_enqueue_style( 'ebcpf-exportfeed-ebay-feeds-colorstyle' );

	wp_enqueue_script( 'jquery' );

	wp_register_script( 'ebcpf-exportfeed-ebay-feeds-script', plugins_url( 'js/ebcpf.js', __FILE__ ), array( 'jquery' ),true );
	wp_enqueue_script( 'ebcpf-exportfeed-ebay-feeds-script' );
    wp_localize_script('ebcpf-exportfeed-ebay-feeds-script','ebcpf_object',[
        'security'                          => wp_create_nonce('ebcpf_ebay_nonce'),
        'action'                            => 'ebcpf_ebayseller_handles',
        'cmdFetchCategory'                  => "core/ajax/wp/fetch_category.php",
        'cmdFetchLocalCategories'           => "core/ajax/wp/fetch_local_categories.php",
        'cmdFetchTemplateDetails'           => "core/ajax/wp/fetch_template_details.php",
        'cmdGetFeed'                        => "core/ajax/wp/get_feed.php",
        'cmdGetFeedStatus'                  => "core/ajax/wp/get_feed_status.php",
        'cmdMappingsErase'                  => "core/ajax/wp/attribute_mappings_erase.php",
        'cmdRemember'                       => "core/ajax/wp/update_remember.php",
        'cmdSearsPostByRestAPI'             => "core/ajax/wp/sears_post.php",
        'cmdSaveAggregateFeedSetting'       => "core/ajax/wp/save_aggregate_feed_setting.php",
        'cmdSelectFeed'                     => "core/ajax/wp/select_feed.php",
        'cmdSetAttributeOption'             => "core/ajax/wp/attribute_mappings_update.php",
        'cmdSetAttributeUserMap'            => "core/ajax/wp/attribute_user_map.php",
        'cmdUpdateAllFeeds'                 => "core/ajax/wp/update_all_feeds.php",
        'cmdUpdateSetting'                  => "core/ajax/wp/update_setting.php",
        'cmdUploadFeed'                     => "core/ajax/wp/upload_feed.php",
        'cmdUploadFeedStatus'               => "core/ajax/wp/upload_feed_status.php",
        'cmdEbayFetchCategory'              => "core/ajax/wp/fetch_ebay_category.php",
        'plugin_path'                       => plugin_dir_url(__FILE__),
        'cmdFetchProductAjax'               => "core/ajax/wp/fetch_product_ajax.php",
        'cmdFetchLocalCategories_custom'    => "core/ajax/wp/fetch_local_categories_custom.php",
        'cmdFetchCategory_custom'           => "core/ajax/wp/fetch_category_custom.php",
        'cmdGetCustomFeed'                  => "core/ajax/wp/get_custom_feed.php",
        'cmdUpdateFeedConfig'               => "core/ajax/wp/update_feed_config.php",
        'cmdGeneralSettingseBay'            => "ajax/wp/eBay_general_settings.php"
    ]);

	wp_register_script( 'ebcpf-exportfeed-ebay-feeds-colorbox', plugins_url( 'js/jquery.colorbox-min.js', __FILE__ ), array( 'jquery' ) );
	wp_enqueue_script( 'ebcpf-exportfeed-ebay-feeds-colorbox' );

}
add_action( 'admin_enqueue_scripts', 'ebcpf_styles_ands_scripts_register' );
/**
 * Add menu items to the admin
 *
 */
function ebcpf_admn_menu() {

    /* add new top level */
    add_menu_page(
		__( 'ExportFeed for eBay', 'ebcpf-exportfeed-ebay-strings' ),
		__( 'ExportFeed for eBay', 'ebcpf-exportfeed-ebay-strings' ),
		'manage_options',
		'ebcpf-ebay-admin',
		'ebcpf_admin_page',
		plugins_url( '/', __FILE__ ) . '/images/xml-icon.png'
	);

	/* add the submenus */
    add_submenu_page(
		'ebcpf-ebay-admin',
		__( 'Create New Feed', 'ebcpf-exportfeed-ebay-strings' ),
		__( 'Create New Feed', 'ebcpf-exportfeed-ebay-strings' ),
		'manage_options',
		'ebcpf-ebay-admin',
		'ebcpf_admin_page'
	);

    add_submenu_page(
		'ebcpf-ebay-admin',
		__( 'Manage Feeds', 'ebcpf-exportfeed-ebay-strings' ),
		__( 'Manage Feeds', 'ebcpf-exportfeed-ebay-strings' ),
		'manage_options',
		'ebcpf-manage-page',
		'ebcpf_manage_page'
	);

	add_submenu_page(
		'ebcpf-ebay-admin',
		__( 'eBay Connect', 'ebcpf-exportfeed-ebay-strings' ),
		__( 'eBay Connect',  'ebcpf-exportfeed-ebay-strings' ),
		'manage_options',
		'ebcpf-ebay-settings-tabs',
		'ebcpf_settings_tab_action'
	);

    add_submenu_page(
        'ebcpf-ebay-admin',
        __( 'Tutorials', 'ebcpf-exportfeed-ebay-strings' ),
        __( 'Tutorials',  'ebcpf-exportfeed-ebay-strings' ),
        'manage_options',
        'ebcpf-tutorials-tab',
        'ebcpf_settings_tutorials_action'
    );
}

add_action( 'admin_menu', 'ebcpf_admn_menu' );
add_action( 'ebcpf_pageview', 'ebcpf_admin_page_action' );
function ebcpf_admin_page() {

	require_once 'ebcpf-wpincludes.php';
	require_once 'core/classes/dialoglicensekey.php';
	include_once 'core/classes/dialogfeedpage.php';
	require_once 'core/feeds/basicfeed.php';

	global $pfcore;
	$pfcore->trigger('ebebcpf_init_feeds');

	do_action('ebcpf_pageview');
}

//include_once('cart-product-version-check.php');
/**
 * Create news feed page
 */
function ebcpf_admin_page_action()
{
	echo 	"<div class='wrap'>";
	echo 	'<h2>Create Product Feed';
	$url = site_url() . '/wp-admin/admin.php?page=ebcpf-manage-page';
    echo '<input style="margin-top:12px;" type="button" class="add-new-h2" onclick="document.location=\'' . $url . '\';" value="' . __( 'Manage Feeds', 'ebcpf-exportfeed-ebay-strings' ) . '" />
    </h2>';
	ebcpf_print_info();
	$action = '';
	$source_feed_id = -1;
    $feed_type = -1;
	$message2 = NULL;
	
	//check action
	if ( isset( $_POST['action'] ) )
		$action = sanitize_text_field($_POST['action']);
	if ( isset( $_GET['action'] ) )
		$action = sanitize_text_field($_GET['action']);

	switch ($action) {
		case 'update_license':
			//I think this is AJAX only now -K
			//No... it is still used (2014/08/25) -K
			if ( isset( $_POST['license_key'] ) ) {
				$licence_key = sanitize_text_field($_POST['license_key']);
				if ( $licence_key != '' )
					update_option( 'ebcpf_licensekey', $licence_key );
			}
			break;
		case 'reset_attributes':
			//I don't think this is used -K
			global $wpdb, $woocommerce;
			$attr_table = $wpdb->prefix . 'woocommerce_attribute_taxonomies';
			$sql = $wpdb->prepare("SELECT attribute_name FROM " . $attr_table . " WHERE %d",[1]);
			$attributes = $wpdb->get_results( $sql );
			foreach ( $attributes as $attr )
				delete_option( $attr->attribute_name );
			break;
		case 'edit':
			$action = '';
			$source_feed_id = intval($_GET['id']);
            $feed_type = $_GET['feed_type'];
			break;
	//ebay handle request
    case 'delete_account' :
		$message = NULL;
		require_once('core/model/eBayAccount.php');
		$account_id = intval($_GET['id']);
		$eBayAccount = new EBCPF_eBayAccount($account_id);
		$eBayAccount->delete();
		echo "<script> window.location.assign( '" . admin_url() . "admin.php?page=ebcpf-ebay-settings-tabs' );</script>";
		break;

	case 'make_account_default' : 
		require_once('core/model/eBayAccount.php');
		$account_id = intval($_GET['id']);
		$eBayAccount = new EBCPF_eBayAccount($account_id);
		$eBayAccount->makeAccountDefault();
		echo "<script> window.location.assign( '" . admin_url() . "admin.php?page=ebcpf-ebay-settings-tabs' );</script>";
		break;
}

	if (isset($action) && (strlen($action) > 0) )
		echo "<script> window.location.assign( '" . admin_url() . "admin.php?page=ebcpf-ebay-admin' );</script>";

	if (isset( $_GET['debug'])) {
		$debug = sanitize_text_field($_GET['debug']);
		if ($debug == 'phpinfo') {
			phpinfo(INFO_GENERAL + INFO_CONFIGURATION + INFO_MODULES);
			return;
		}
		if ($debug == 'reg') {
			echo "<pre>\r\n";
			new EBCPF_License(true);
			echo "</pre>\r\n";
		}
	}

  # Get Variables from storage ( retrieve from wherever it's stored - DB, file, etc... )

	$reg = new EBCPF_License();

	//Main content
	echo '
	<script type="text/javascript">
	jQuery( document ).ready( function( $ ) {
	    feed_type = '.$feed_type.';
		ajaxhost = "' . plugins_url( '/', __FILE__ ) . '";
		jQuery( "#selectFeedType" ).val( "eBaySeller" ).parent().parent().parent().hide();
		doSelectFeed();
		doFetchLocalCategories();
		feed_id = ' . $source_feed_id . ';
		if(feed_id > 0 && feed_type == 1){
			saveTocustomTable(feed_id);
			showSelectedProductTables(feed_id);
		}
	} );
	</script>';

	//WordPress Header ( May contain a message )

	global $message;
	if ( strlen($message) > 0 && strlen($reg->error_message) > 0 )
		$message .= '<br>'; //insert break after local message (if present)
	$message .= $reg->error_message;
	if ( strlen($message) > 0 ) 
	{
		//echo '<div id="setting-error-settings_updated" class="error settings-error">'
		echo '<div id="setting-error-settings_updated" class="updated settings-error">
			  <p>' . $message . '</p>
			  </div>';
	}

	if ($source_feed_id == -1) {
		//Page Header
		echo EBCPF_FeedPageDialogs::pageHeader();
		//Page Body
		echo EBCPF_FeedPageDialogs::pageBody();
	} else {
		require_once dirname(__FILE__) . '/core/classes/dialogeditfeed.php';
		echo EBCPF_EditFeedDialog::pageBody($source_feed_id,$feed_type);
	}

	if ( !$reg->valid ) {}

}

/**
 * Display the manage feed page
 *
 */

add_action( 'ebcpf_pageview_manage', 'ebcpf_manage_page_action' );

function ebcpf_manage_page() {

	require_once 'ebcpf-wpincludes.php';
	require_once 'core/classes/dialoglicensekey.php';
	include_once 'core/classes/dialogfeedpage.php';

	global $pfcore;
	$pfcore->trigger('ebcpf_init_feeds');

	do_action('ebcpf_pageview_manage');

}

function ebcpf_manage_page_action() {

	$reg = new EBCPF_License();

	require_once 'ebcpf-manage-feeds.php';

}

function ebcpf_settings_tab_action()
{
    global $message;
	require_once 'ebcpf-wpincludes.php';
	require_once('ebcpf-ebay-settings-tabs.php');
	$pageObj = new EBCPF_SettingsPage();
	$pageObj->loadNavigationTab();
}
function ebcpf_settings_tutorials_action()
{
    global $message;
	require_once 'ebcpf-wpincludes.php';
	require_once('ebcpf-ebay-settings-tabs.php');
    $_GET['tab'] = 'tutorials';
	$pageObj = new EBCPF_SettingsPage();
	$pageObj->loadNavigationTab();
}



if ( isset( $_POST['action'] ) && $_POST['action'] == 'ebcpf_ebayseller_handles' ){
    add_action( 'wp_ajax_ebcpf_ebayseller_handles','ebcpf_ebayseller_handles' );
}

if ( isset( $_GET['action'] ) && $_GET['action'] == 'ebcpf_ebayseller_handles' )
    add_action( 'wp_ajax_amazon_handles','ebcpf_ebayseller_handles' );

function ebcpf_ebayseller_handles(){
    $check = check_ajax_referer('ebcpf_ebay_nonce', 'security');
    if ($check){
        include_once(plugin_dir_path(__FILE__).$_POST['feedpath']);
    }
    wp_die();
}

