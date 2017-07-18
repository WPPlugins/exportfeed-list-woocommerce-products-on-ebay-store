<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
require_once dirname(__FILE__) . '/../../data/feedcore.php';
require_once dirname(__FILE__) . '/../../classes/dialogbasefeed.php';
require_once dirname(__FILE__) . '/../../classes/providerlist.php';

do_action('ebcpf_load_modifiers');

global $pfcore;
$pfcore->trigger('ebcpf_init_feeds');

add_action('ebcpf_select_feed_main_hook', 'ebcpf_select_feed_main');
do_action('ebcpf_select_feed_main_hook');

function ebcpf_select_feed_main()
{
    $feedType = isset($_POST['feedtype']) ? sanitize_text_field($_POST['feedtype']) : "eBaySeller";

    if (strlen($feedType) == 0)
        return;

    $inc = dirname(__FILE__) . '/../../feeds/' . strtolower($feedType) . '/dialognew.php';
    $feedObjectName = $feedType . 'Dlg';
    if (file_exists($inc))
        include_once $inc;
    $f = new $feedObjectName();
    echo $f->mainDialog();
}
