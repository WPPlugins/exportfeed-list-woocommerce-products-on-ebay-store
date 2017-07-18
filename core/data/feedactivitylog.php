<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
class EBCPF_FeedActivityLog
{
    function __construct($feedIdentifier = '')
    {
        //When instantiated (as opposed to static calls) it means we need to log the phases
        //therefore, save the feedIdentifier
        $this->feedIdentifier = $feedIdentifier;
    }

    function __destruct()
    {
        global $pfcore;
        if (!empty($pfcore) && (strlen($pfcore->callSuffix) > 0)) {
            $deleteLogData = 'deleteLogData' . $pfcore->callSuffix;
            $this->$deleteLogData();
        }
    }

    /********************************************************************
     * Add a record to the activity log for "Manage Feeds"
     ********************************************************************/

    private static function addNewFeedData($category, $remote_category, $file_name, $file_path, $providerName, $productCount)
    {
        global $pfcore;
        $addNewFeedData = 'addNewFeedData' . $pfcore->callSuffix;
        EBCPF_FeedActivityLog::$addNewFeedData($category, $remote_category, $file_name, $file_path, $providerName, $productCount);
    }
    private static function addNewFeedDataW($category, $remote_category, $file_name, $file_path, $providerName, $productCount)
    {
        global $wpdb;
        global $pfcore;
        /*
         * $pfcore->feed_type == 1 Custom product feed type
         * $pfcore->feed_type == 0 Default product feed type
         * */
        $product_details = '';
        if ($pfcore->feedType == 1) {
            $feed_type = 1;
            $sql = "SELECT * from {$wpdb->prefix}ebcpf_custom_products";
            $product_details = serialize($wpdb->get_results($sql, ARRAY_A));
        }
        if ($pfcore == 0) {
            $feed_type = 0;
            $product_details = NULL;
        }
        $feed_table = $wpdb->prefix . 'ebcpf_feeds';
        $sql = "INSERT INTO $feed_table(`category`, `remote_category`, `filename`, `url`, `type`, `product_count`,`feed_type`,`product_details`) VALUES ('$category','$remote_category','$file_name','$file_path','$providerName', '$productCount','$feed_type','$product_details')";
        if ($wpdb->query($sql)) {
            $sql_custom = "TRUNCATE {$wpdb->prefix}ebcpf_custom_products";
            $wpdb->query($sql_custom);
        }
    }

    private static function addNewFeedDataWe($category, $remote_category, $file_name, $file_path, $providerName, $productCount)
    {
        EBCPF_FeedActivityLog::addNewFeedDataW($category, $remote_category, $file_name, $file_path, $providerName, $productCount);
    }

    /********************************************************************
     * Search the DB for a feed matching filename / providerName
     ********************************************************************/

    public static function feedDataToID($file_name, $providerName)
    {
        global $pfcore;
        $feedDataToID = 'feedDataToID' . $pfcore->callSuffix;
        return EBCPF_FeedActivityLog::$feedDataToID($file_name, $providerName);
    }

    private static function feedDataToIDW($file_name, $providerName)
    {
        global $wpdb;
        $feed_table = $wpdb->prefix . 'ebcpf_feeds';
        $sql = "SELECT * from $feed_table WHERE `filename`='$file_name' AND `type`='$providerName'";
        $list_of_feeds = $wpdb->get_results($sql, ARRAY_A);
        if ($list_of_feeds) {
            return $list_of_feeds[0]['id'];
        } else {
            return -1;
        }
    }

    private static function feedDataToIDWe($file_name, $providerName)
    {
        return EBCPF_FeedActivityLog::feedDataToIDW($file_name, $providerName);
    }

    /********************************************************************
     * Called from outside... this class has to make sure the feed shows under "Manage Feeds"
     ********************************************************************/

    public static function updateFeedList($category, $remote_category, $file_name, $file_path, $providerName, $productCount)
    {
        $id = EBCPF_FeedActivityLog::feedDataToID($file_name, $providerName);
        if ($id == -1)
            EBCPF_FeedActivityLog::addNewFeedData($category, $remote_category, $file_name, $file_path, $providerName, $productCount);
        else
            EBCPF_FeedActivityLog::updateFeedData($id, $category, $remote_category, $file_name, $file_path, $providerName, $productCount);
    }

    public static function updateCustomFeedList($category, $remote_category, $file_name, $file_path, $providerName, $productCount)
    {
        $category = implode(',', $category);
        $remote_category = implode('::', $remote_category);
        $id = EBCPF_FeedActivityLog::feedDataToID($file_name, $providerName);
        if ($id == -1)
            EBCPF_FeedActivityLog::addNewFeedData($category, $remote_category, $file_name, $file_path, $providerName, $productCount);
        else
            EBCPF_FeedActivityLog::updateFeedData($id, $category, $remote_category, $file_name, $file_path, $providerName, $productCount);
    }

    /********************************************************************
     * Update a record in the activity log
     ********************************************************************/

    private static function updateFeedData($id, $category, $remote_category, $file_name, $file_path, $providerName, $productCount)
    {
        global $pfcore;
        $updateFeedData = 'updateFeedData' . $pfcore->callSuffix;
        EBCPF_FeedActivityLog::$updateFeedData($id, $category, $remote_category, $file_name, $file_path, $providerName, $productCount);
    }

    private static function updateFeedDataW($id, $category, $remote_category, $file_name, $file_path, $providerName, $productCount)
    {
        global $wpdb;
        global $pfcore;
        /*
         * $pfcore->feed_type == 1 Custom product feed type
         * $pfcore->feed_type == 0 Default product feed type
         * */
        $product_details = '';

        if ($pfcore->feedType == 1) {
            $feed_type = 1;
            $sql = "SELECT * from {$wpdb->prefix}ebcpf_custom_products;";
            echo $sql;
            $product_details = maybe_serialize($wpdb->get_results($sql, ARRAY_A));
        }
        if ($pfcore->feedType == 0) {
            $feed_type = 0;
            $product_details = NULL;
        }


        $feed_table = $wpdb->prefix . 'ecpf_feeds';
        $sql = "
			UPDATE $feed_table 
			SET 
				`category`='$category',
				`remote_category`='$remote_category',
				`filename`='$file_name',
				`url`='$file_path',
				`type`='$providerName',
				`product_count`='$productCount',
				`feed_type` = '$feed_type',
				`product_details` = '$product_details'
			WHERE `id`=$id;";
        $wpdb->query($sql);


    }

    private static function updateFeedDataWe($id, $category, $remote_category, $file_name, $file_path, $providerName, $productCount)
    {
        EBCPF_FeedActivityLog::updateFeedDataW($id, $category, $remote_category, $file_name, $file_path, $providerName, $productCount);
    }

    /********************************************************************
     * Save a Feed Phase
     ********************************************************************/

    function logPhase($activity)
    {
        global $pfcore;
        $pfcore->settingSet('ebcpf_feedActivity_' . $this->feedIdentifier, $activity);
    }
}
/********************************************************************
Remove Log info
 ****************************/