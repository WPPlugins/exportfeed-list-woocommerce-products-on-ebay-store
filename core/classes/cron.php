<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
//Create a custom refresh_interval so that scheduled events will be able to display
//  in Cron job manager
function ebcpf_refresh_interval() {
	$current_delay = get_option('ebcpf_feed_delay');

	 return array(
	 	'refresh_interval' => array('interval' => $current_delay, 'display' => 'XML refresh interval'),
	 );
}

class EBCPF_Cron {

	public static function doSetup() {
		add_filter('cron_schedules', 'ebcpf_refresh_interval');
		//Delete old (faulty) scheduled cron job from prior versions
		$next_refresh = wp_next_scheduled('ebcpf_custom_interval');
		if ($next_refresh)
			wp_unschedule_event($next_refresh, 'ebcpf_custom_interval');
		$next_refresh = wp_next_scheduled('ebcpf_updatefeeds');
		if ($next_refresh)
			wp_unschedule_event($next_refresh, 'ebcpf_updatefeeds');
	}

	public static function scheduleUpdate () {
		//Set the Cron job here. Params are (when, display, hook)
		$next_refresh = wp_next_scheduled('ebcpf_update_cartfeeds');
		if (!$next_refresh )
			wp_schedule_event(time(), 'refresh_interval', 'ebcpf_update_cartfeeds');
	}

}