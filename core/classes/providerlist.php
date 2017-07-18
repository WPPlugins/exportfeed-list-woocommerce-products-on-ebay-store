<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
class EBCPF_ProviderList {

	public $items = array();

	public function __construct() {

		global $pfcore;

		//***************************************************
		//Targetted Feeds
		//***************************************************

		$this->addProvider('eBaySeller', 'eBay Seller','csv');

	}

	public function addProvider($name, $description, $fileformat = 'xml') {
		$np = new stdClass();
		$np->name = $name;
		$np->prettyName = $name; //Used by ManageFeeds Page
		$np->description = $description;
		$np->fileformat = $fileformat;
		$this->items[] = $np;
		return $np;
	}

	public function asOptionList() {
		$output = '';
		foreach($this->items as $item)
			$output .= '
						<option value="' . $item->name . '">' . $item->description . '</option>';
		return $output;
	}

	public function getExtensionByType($type) {
		//Used by ManageFeeds to create a filename
		foreach($this->items as $provider)
			if ($provider->name == $type)
				return $provider->fileformat;
		return '';
	}

	public function getFileFormatByType($type) {
		//Used by ManageFeeds to create a filename
		foreach($this->items as $provider)
			if ($provider->name == $type)
				return $provider->fileformat;
		return '';
	}

	public function getPrettyNameByType($type) {
		//Used by ManageFeeds to create a filename
		foreach($this->items as $provider)
			if ($provider->name == $type)
				return $provider->prettyName;
		return '';
	}

}
