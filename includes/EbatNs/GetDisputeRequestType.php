<?php
/* Generated on 6/26/15 3:23 AM by globalsync
 * $Id: $
 * $Log: $
 */

require_once 'AbstractRequestType.php';
require_once 'DisputeIDType.php';

/**
  * Retrieves the details of a specific eBay dispute corresponding to the supplied dispute ID.
  * 
 **/

class GetDisputeRequestType extends AbstractRequestType
{
	/**
	* @var DisputeIDType
	**/
	protected $DisputeID;


	/**
	 * Class Constructor 
	 **/
	function __construct()
	{
		parent::__construct('GetDisputeRequestType', 'urn:ebay:apis:eBLBaseComponents');
		if (!isset(self::$_elements[__CLASS__]))
		{
			self::$_elements[__CLASS__] = array_merge(self::$_elements[get_parent_class()],
			array(
				'DisputeID' =>
				array(
					'required' => false,
					'type' => 'DisputeIDType',
					'nsURI' => 'urn:ebay:apis:eBLBaseComponents',
					'array' => false,
					'cardinality' => '0..1'
				)));
		}
		$this->_attributes = array_merge($this->_attributes,
		array(
));
	}

	/**
	 * @return DisputeIDType
	 **/
	function getDisputeID()
	{
		return $this->DisputeID;
	}

	/**
	 * @return void
	 **/
	function setDisputeID($value)
	{
		$this->DisputeID = $value;
	}

}
?>
