<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
//Checks exportfeed-list-woocoomerce-products-on-ebay-store version
function ebcpf_check_version()
{
	//taken from /include/update.php line 270
	$plugin_info = get_site_transient( 'update_plugins' );
	
	//we want to always display 'up to date', therefore we don't need the below check
	if ( !isset( $plugin_info->response[ EBCPF_BASENAME ] ) )
		return ' | You are up to date';
	
	$CPF_WP_version = $plugin_info->response[ EBCPF_BASENAME ]->new_version; //wordpress repository version
	//version_compare:
	//returns -1 if the first version is lower than the second, 
	//0 if they are equal, 
	//1 if the second is lower.
	$doUpdate = version_compare( $CPF_WP_version, EBCPF_VERSION );
	//if current version is older than wordpress repo version
	if ( $doUpdate == 1 ) return ' | <a href=\'plugins.php\'>Out of date - please update</a>';
	//else, up to date
	return ' | You are up to date';
}

function ebcpf_print_info()
{
	$iconurl = plugins_url( '/images/exf-sm-logo.png', __FILE__ );
	$gts_iconurl = plugins_url( '/images/gts-logo.png', __FILE__ );
	echo 
	'<div class="exf-logo-header">
		<div class="exf-logo-link">
	 		<a target="_blank" href="http://www.exportfeed.com"><img class="exf-logo-style" src=' . $iconurl . ' alt="shopping cart logo"></a>
	 	</div>
	 	<div class=\'version-style\'>
	 		<a target="_blank" href="http://www.exportfeed.com/woocommerce-product-feed/">Product Site</a> | 
	 		<a target="_blank" href="http://www.exportfeed.com/faq/">FAQ/Help</a> 
	 		'//| <a target="_blank" href="http://www.exportfeed.com/?s=">SEARCH</a> <br>
	 		.'<br>Version: ' . EBCPF_VERSION . ebcpf_check_version() .'<br>
	 	</div>
	 	<div class="gts-link">
	 		<a target="_blank" href="http://www.exportfeed.com/google-trusted-store-woocommerce/">Get the Google Trusted Store Plugin<br>Sell More - Be placed 1st!</a>
	 	</div>
	 	<div class="gts-logo-link" >
	 		<a target="_blank" href="http://www.exportfeed.com/google-trusted-store/"><img class="gts-logo-style" src=' . $gts_iconurl . ' alt="google trusted stores"></a>
	 	</div>
	 </div>
	 <div style="clear:both"></div>';	 
}
