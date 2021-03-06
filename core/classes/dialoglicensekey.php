<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class EBCPF_LicenseKeyDialog {

	public static function gts_registration_bar($current_licensekey = '') {
		global $pfcore;
		$result = '
			<input name="ebcpf_gts_licensekey"  id="ebcpf_gts_licensekey" class="text_large" value="' . $current_licensekey . '"/>';
			//<input class="navy_blue_button" type="submit" value="Save Key" id="submit" name="submit" onclick="gtsSubmitKey()">

		return $result;
	}

	public static function large_registration_dialog($current_licensekey) {
		global $pfcore;
		$result = " <div id='poststuff'><div class='postbox' style='width: 98%;'><form name='feed_update_delay_form'  action='" . $pfcore->siteHostAdmin . "admin.php?page=ebcpf-ebay-admin' id='cat-product-feeds-xml-form' method='post' target=''>
			<h3 class='hndle'>Unlock more features</h3>
			<div class='inside export-target'>
			<table class='form-table' >
			<tbody><tr>";
		$result .= "<th style='width:300px;'><label>Enter Valid License Key for Cart Product Feeds : </label></th>";
		$result .= '<td><input name="license_key"  id="feed_update_delay" class="text_large" value="' . $current_licensekey . '"/></td>';
		$result .= "<td><input type='hidden' name='action' value='update_license' /><input class='navy_blue_button' style='float:right;' type='submit' value='Save Changes' id='submit' name='submit'></td>";
		$result .= "</tr><tr><td colspan='3'><br /><br /><br /><p>
			<a class='multi-line-button green' href='https://www.purpleturtle.pro/cart.php?gid=8' target='_blank' style='width:22em'>
			<span class='title'>Buy a License Key</span><span class='subtitle'>Multi-Site License - $399.99USD</span><span class='subtitle'>Single Site License - $69.99USD</span>
			</a></p></td></tr></tbody></table></div></form></div></div></div>";
		$result .= "<br/><br/><br/><br/><br/><br/>";
		$result .= '<div class="clear"></div>';
		return $result;
	}

	public static function small_registration_dialog($current_licensekey = '', $key_name = 'ebcpf_licensekey') {

		global $pfcore;

		//Only load absolute position in WordPress
		if ($pfcore->cmsName == 'WordPress')
			$style = 'style="position:absolute; left:300px; top:59px"';
		else
			$style = '';

		$result =
	  	'
		    <div ' . $style . '>
		     <label for="edtLicenseKey">License:</label>
		      <input style="width:300px" type="text" name="license_key" id="edtLicenseKey" value="' . $current_licensekey . '" placeholder="Enter full license key"/>
		      <input class="button-primary" type="submit" value="Save Key" id="submit" name="submit" onclick="submitLicenseKey(\'' . $key_name . '\')">
		    </div>
		    ';

		return $result;
	}

}
