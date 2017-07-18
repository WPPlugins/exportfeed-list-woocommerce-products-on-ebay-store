<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
class EBCPF_FoundAttribute {

	public $attributes;
	public $attrOptionsTableName = '';
	public $attrOptions;

	function __construct() {
		global $pfcore;
		$fetchAttributes = 'fetchAttributes' . $pfcore->callSuffix;
		$this->$fetchAttributes();
	}

	function fetchAttributesJ() {
		//From Joomla / Virtuemart
		$db = JFactory::getDBO();
		$query = '
			SELECT a.custom_title as attribute_name
			FROM #__virtuemart_customs a
			WHERE (CHAR_LENGTH(a.custom_element) > 0) AND (a.published = 1)';
		$db->setQuery($query);
		$this->attributes = $db->loadObjectList();
	}

	function fetchAttributesJH() {
		//From Hikashop
		$this->attributes = array();
	}

	function fetchAttributesJS() {
		$this->attributes = array();
	}

	function fetchAttributesW() {
		//From WordPress / Woocommerce
		global $wpdb;
		$attr_table = $wpdb->prefix . 'woocommerce_attribute_taxonomies';
		$this->attrOptionsTableName = $wpdb->prefix . 'options';
		$sql = $wpdb->prepare("SELECT attribute_name FROM " . $attr_table . " WHERE %d",[1]);
		$this->attributes = $wpdb->get_results($sql);
	}

	function fetchAttributesWe() {
		//From WordPress / WP-ECommerce
		$this->attributes = array();
		global $wpdb;
		$this->attrOptionsTableName = $wpdb->prefix . 'options';
		$sql = $wpdb->prepare("
			SELECT terms.name as attribute_name 
			FROM $wpdb->term_taxonomy taxo 
			LEFT JOIN $wpdb->terms terms ON taxo.term_id = terms.term_id
			WHERE (taxo.parent = %d) AND (taxo.taxonomy = %s)",[1,'wpsc-variation']);
		$this->attributes = $wpdb->get_results($sql);
	}

  /*function fetchAttrOptions($attrVal) {
    global $wpdb;
    $sql = "SELECT option_value FROM " . $this->attrOptionsTableName . " WHERE option_name='" . $attrVal . "'";
    $this->attrOptions = $wpdb->get_results($sql);
  }*/

}

class EBCPF_FoundOptions {

	public $option_value = '';

	function __construct($service_name, $attribute) {
		global $pfcore;
		$internalFetch = 'internalFetch' . $pfcore->callSuffix;
		$this->$internalFetch($service_name, $attribute);
	}

	function internalFetchJ($service_name, $attribute) {
		$option_name = $service_name . '_ebcpf_' . $attribute;
		$db = JFactory::getDBO();
		$db->setQuery('
			SELECT a.value
			FROM #__cartproductfeed_options a
			WHERE (a.state = 1) AND (a.name=' . $db->quote($option_name) . ')');
		$this->option_value = $db->loadResult();
	}

	function internalFetchJH($service_name, $attribute) {
		$this->internalFetchJ($service_name, $attribute);
	}

	function internalFetchJS($service_name, $attribute) {

		global $pfcore;

		$shopID = $pfcore->shopID;

		$option_name = $service_name . '_ebcpf_' . $attribute;
		$db = JFactory::getDBO();
		$db->setQuery("
			SELECT a.value
			FROM #__cartproductfeed_options a
			WHERE (a.state = 1) AND (a.name='$option_name') AND (shop_id = $shopID)"
			);
		$this->option_value = $db->loadResult();
	}

	function internalFetchW($service_name, $attribute) {
		$this->option_value = get_option($service_name . '_ebcpf_' . $attribute);
	}

	function internalFetchWe($service_name, $attribute) {
		$this->option_value = get_option($service_name . '_ebcpf_' . $attribute);
	}

}
