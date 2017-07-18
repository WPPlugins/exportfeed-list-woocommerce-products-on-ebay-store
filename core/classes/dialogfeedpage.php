<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class EBCPF_FeedPageDialogs {

	public static function pageHeader() {

		global $pfcore;

		$gap = '
			<div style="float:left; width: 50px;">
				&nbsp;
			</div>';

		if ($pfcore->cmsName == 'WordPress') {
			$reg = new EBCPF_License();
			if ($reg->valid)
				$lic = '<div style="position:absolute; left:300px; top:60px">
					 <a class="button-primary" type="submit" value="" id="submit" name="submit" href="http://www.exportfeed.com/support/" target="_blank">Thank You For Supporting The Project</a>
						</div>';
			else
				$lic = EBCPF_LicenseKeyDialog::small_registration_dialog('');
		} else
			$lic = '';

		$providers = new EBCPF_ProviderList();
		if ($_GET['page'] == 'ebcpf-ebay-settings-tabs'){
			$style = 'display : none';
		}else{
			$style = 'display : block';
		}
		$output = '
			<div class="postbox" style="width:98%;">
				<div class="inside-export-target">
					<div style = "'.$style.'">
						<h4>Select Merchant Type</h4>
						<select id="selectFeedType" onchange="doSelectFeed();">
						<option></option>' . 
							$providers->asOptionList() . '
						</select>
					</div>				
					' . $lic . '
				</div>
			</div>
			<div class="clear"></div>';

		return $output;

  }

  public static function pageBody()
  {
    $output = '

	  <div id="feedPageBody" class="postbox" style="width: 98%;">
	    <div class="inside export-target">
	      <h4>No feed type selected.</h4>
		  <hr />
		</div>
	  </div>
	  ';
	return $output;
  }

}
