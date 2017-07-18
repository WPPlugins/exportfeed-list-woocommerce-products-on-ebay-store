<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
class eBaySellerDlg extends EBCPF_BaseFeedDialog
{

	function __construct() 
	{
		parent::__construct();
		$this->service_name = 'eBaySeller';
		$this->service_name_long = 'eBay Seller';
		$this->options = array(
			'Category',
			'Title',
			'Description',
			'ConditionID',
			'picURL',
			'Quantity',
			'Format',
			'Duration',
			'Location',
			'StartPrice',
			'BuyItNowPrice',
			'Location',
			'ReturnsAcceptedOption',
			'ShippingType'
			);
	}

}
