<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
class EBCPF_FeedFolder {
  
	/********************************************************************
	feedURL is where the client should be sent to generate the new feed
	It's unclear if it's still used
	********************************************************************/

	public static function feedURL() {
		global $pfcore;
		$feedURL = 'feedURL' . $pfcore->callSuffix;
		return EBCPF_FeedFolder::$feedURL();
	}
  
	private static function feedURLJ() {
		global $pfcore;
		return $pfcore->siteHost . '/administrator/index.php?option=com_cartproductfeed&view=instantiatefeed';
	}

	private static function feedURLJH() {
		global $pfcore;
		return $pfcore->siteHost . '/administrator/index.php?option=com_cartproductfeed&view=instantiatefeed';
	}

	private static function feedURLJS() {
		global $pfcore;
		return $pfcore->siteHost . '/administrator/index.php?option=com_cartproductfeed&view=instantiatefeed';
	}

	private static function feedURLW() {
		global $pfcore;
		return $pfcore->siteHost;
	}

	private static function feedURLWe() {
		global $pfcore;
		return $pfcore->siteHost;
	}

	/********************************************************************
	uploadFolder is where the plugin should make the file
	********************************************************************/
	public static function uploadFolder() {
		global $pfcore;
		$uploadFolder = 'uploadFolder' . $pfcore->callSuffix;
		return EBCPF_FeedFolder::$uploadFolder();
	}

	private static function uploadFolderJ() {
		return JPATH_SITE . '/media/ebay_export_feeds/';
	}

	private static function uploadFolderJH() {
		return JPATH_SITE . '/media/ebay_export_feeds/';
	}

	private static function uploadFolderJS() {
		return JPATH_SITE . '/media/ebay_export_feeds/';
	}

	private static function uploadFolderW() {
		$upload_dir = wp_upload_dir();
		return $upload_dir['basedir'] . '/ebay_export_feeds/';
	}

	private static function uploadFolderWe() {
		$upload_dir = wp_upload_dir();
		return $upload_dir['basedir'] . '/ebay_export_feeds/';
	}

	/********************************************************************
	uploadRoot is where the plugin should make the file (same as uploadFolder)
	but no "cart_product_feeds". Useful for ensuring folder exists
	********************************************************************/

	public static function uploadRoot() {
		global $pfcore;
		$uploadRoot = 'uploadRoot' . $pfcore->callSuffix;
		return EBCPF_FeedFolder::$uploadRoot();
	}

	private static function uploadRootJ() {
		return  JPATH_SITE . '/media/';
	}

	private static function uploadRootJH() {
		return  JPATH_SITE . '/media/';
	}

	private static function uploadRootJS() {
		return  JPATH_SITE . '/media/';
	}

	private static function uploadRootW() {
		$upload_dir = wp_upload_dir();
		return $upload_dir['basedir'];
	}

	private static function uploadRootWe() {
		$upload_dir = wp_upload_dir();
		return $upload_dir['basedir'];
	}

	/********************************************************************
	URL we redirect the client to in order for the user to see the feed
	********************************************************************/

	public static function uploadURL() {
		global $pfcore;
		$uploadURL = 'uploadURL' . $pfcore->callSuffix;
		return EBCPF_FeedFolder::$uploadURL();
	}

	private static function uploadURLJ() {
		return JURI::root() . 'media/ebay_export_feeds/';
	}

	private static function uploadURLJH() {
		return JURI::root() . 'media/ebay_export_feeds/';
	}

	private static function uploadURLJS() {
		return JURI::root() . 'media/ebay_export_feeds/';
	}

	private static function uploadURLW() {
		$upload_dir = wp_upload_dir();
		return $upload_dir['baseurl'] . '/ebay_export_feeds/';
	}

	private static function uploadURLWe() {
		$upload_dir = wp_upload_dir();
		return $upload_dir['baseurl'] . '/ebay_export_feeds/';
	}

}
