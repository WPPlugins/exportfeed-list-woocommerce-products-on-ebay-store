<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
 //Retrieves user-defined shipping settings and saves into class-local variable
class EBCPF_ShippingData {

	function __construct($parentfeed) {
		global $pfcore;
		$loadProc = 'loadShippingData' . $pfcore->callSuffix;
		return $this->$loadProc($parentfeed);
	}

	function loadShippingDataJ($parentfeed) {
	}

	function loadShippingDataJH($parentfeed) {
	}

	function loadShippingDataJS($parentfeed) {
	}

	function loadShippingDataW( $parentfeed ) 
	{
		

	}

	function loadShippingDataWe($parentfeed) {
	}

}