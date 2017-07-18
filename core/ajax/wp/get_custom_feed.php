<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
define ('XMLRPC_REQUEST', true);
//ob_start(null, 0, PHP_OUTPUT_HANDLER_FLUSHABLE | PHP_OUTPUT_HANDLER_CLEANABLE);
ob_start(null);

function safeGetPostData($index) {
    if (isset($_POST[$index]))
        return $_POST[$index];
    else
        return '';
}

function doOutput($output) {
    ob_clean();
    echo json_encode($output);
}
require_once dirname(__FILE__) . '/../../../ebcpf-wpincludes.php';

do_action('ebcpf_load_modifiers');
global $pfcore;
$pfcore->feedType = 1;
$pfcore->trigger('ebcpf_init_feeds');

add_action('ebcpf_main_feed', 'ebcpf_feed_main');
do_action('ebcpf_main_feed');


function ebcpf_feed_main() {
    
    $requestCode = safeGetPostData('provider');
    $file_name = safeGetPostData('file_name');
    $feedIdentifier = safeGetPostData('feed_identifier');
    $saved_feed_id = safeGetPostData('feed_id');
    $feed_list = safeGetPostData('feed_ids'); //For Aggregate Feed Provider
    $feedLimit = safeGetPostData('feedLimit');
    $output = new stdClass();
    $output->url = '';

   /* if (strlen($requestCode) * count($local_category) == 0) {
        $output->errors = 'Error: error in AJAX request. Insufficient data or categories supplied.';
        doOutput($output);
        return;
    }
*/
    if (!($file_name)) {
       $output->errors = 'Error: Please mention file name for the feed';
        doOutput($output);
        return;
    }


    // Check if form was posted and select task accordingly
    $dir = EBCPF_FeedFolder::uploadRoot();
    if (!is_writable($dir)) {
        $output->errors = "Error: $dir should be writeable";
        doOutput($output);
        return;
    }
    $dir = EBCPF_FeedFolder::uploadFolder();
    if (!is_dir($dir)) {
        mkdir($dir);
    }
    if (!is_writable($dir)) {
        $output->errors = "Error: $dir should be writeable";
        doOutput($output);
        return;
    }

    $providerFile = 'feeds/' . strtolower($requestCode) . '/feed.php';
    if (!file_exists(dirname(__FILE__) . '/../../' . $providerFile))
        if (!class_exists('P' . $requestCode . 'Feed')) {
            $output->errors = 'Error: Provider file not found.';
            doOutput($output);
            return;
        }

    $providerFileFull = dirname(__FILE__) . '/../../' . $providerFile;
    if (file_exists($providerFileFull))
        require_once $providerFileFull;

    //Load form data
    $file_name = sanitize_title_with_dashes($file_name);
    if ($file_name == '')
        $file_name = 'feed' . rand(10, 1000);

    $saved_feed = null;
    if ( (strlen($saved_feed_id) > 0) && ($saved_feed_id > -1) ) {
        require_once dirname(__FILE__) . '/../../data/savedfeed.php';
        $saved_feed = new EBCPF_SavedFeed($saved_feed_id);
    }
    $providerClass = 'P' . $requestCode . 'Feed';
    $x = new $providerClass;

    $x->feed_list = $feed_list; //For Aggregate Provider only
    if (strlen($feedIdentifier) > 0)
        $x->activityLogger = new EBCPF_FeedActivityLog($feedIdentifier);
    $x->getCustomFeedData($file_name, $saved_feed);

     if ($x->success)
        $output->url = EBCPF_FeedFolder::uploadURL() . $x->providerName . '/' . $file_name . '.' . $x->fileformat;
    $output->errors = $x->getErrorMessages();

    doOutput($output);
}

function getCustomProductFeed(){
    global $wpdb;
    $tableName = $wpdb->prefix .'ebcpf_custom_products';
    $sql = "
        SELECT category , product_title , category_name , remote_category
        FROM $tableName
    ";
     return $wpdb->get_results($sql , ARRAY_A);
}
