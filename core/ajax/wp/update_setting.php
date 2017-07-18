<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly

if (!isset($_POST['setting']) || !isset($_POST['value'])) {
    echo 'Error in setting';
    return;
}

require_once dirname(__FILE__) . '/../../classes/cron.php';

$setting = sanitize_text_field($_POST['setting']);
if (isset($_POST['feedid']))
    $feedid = intval($_POST['feedid']);
else
    $feedid = '';
$value = sanitize_text_field($_POST['value']);

//Don't update here - security issue would allow any option to be updated
//Only update within an if()
if ($setting == 'ebcpf_feed_delay') {

    update_option($setting, $value);

    //Is this event scheduled?
    $next_refresh = wp_next_scheduled('ebcpf_update_cartfeeds');
    if ($next_refresh)
        wp_unschedule_event($next_refresh, 'ebcpf_update_cartfeeds');
    wp_schedule_event(time(), 'refresh_interval', 'ebcpf_update_cartfeeds');
}

if ($setting == 'ebcpf_gts_licensekey')
    update_option($setting, $value);
if ($setting == 'ebcpf_licensekey')
    update_option($setting, $value);

//Some PHPs don't return the post correctly when it's long data
if (strlen($setting) == 0) {
    $lines = explode('&', file_get_contents("php://input"));
    foreach ($lines as $line) {
        if ((strpos($line, 'feedid') == 0) && (strlen($feedid) == 0))
            $feedid = substr($line, 7);
        if ((strpos($line, 'setting') == 0) && (strlen($setting) == 0))
            $setting = substr($line, 8);
    }
}

if (strpos($setting, 'cp_advancedFeedSetting') !== false) {

    //$value may get truncated on an & because $_POST can't parse
    //so pull value manually
    $postdata = file_get_contents("php://input");
    $i = strpos($postdata, '&value=');
    if ($i !== false)
        $postdata = substr($postdata, $i + 7);

    //Strip the provider name out of the setting
    $target = substr($setting, strpos($setting, '-') + 1);

    //Save new advanced setting
    if (strlen($feedid) == 0)
        update_option($target . '-ebcpf-settings', $postdata);
    else {
        global $wpdb;
        $feed_table = $wpdb->prefix . 'ebcpf_feeds';

        $sql = $wpdb->prepare("UPDATE $feed_table 
				SET
					`own_overrides`=%d,
					`feed_overrides`='%s'
				WHERE `id`=$feedid",[1,$postdata]);
        $wpdb->query($sql);
    }
}
echo 'Updated.';
