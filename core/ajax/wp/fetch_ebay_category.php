<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
define('XMLRPC_REQUEST', true);
require_once dirname(__FILE__) . '/../../data/feedcore.php';
require_once dirname(__FILE__) . '/ebayApp.php';

$service_name = sanitize_text_field($_POST['service_name']);
$parent_id = sanitize_text_field($_POST['parent_id']);
$ebay_site_id = 0;
$data = '';
$count = 0;
$canDisplay = false;
$appID = 'subashgh-ebayplug-PRD-62f871c7c-579015ad';

$siteId = 0;

if ($appID) {
    $ebay_app = New EBCPF_EbayApp($appID);
    $cats = $ebay_app->getCats($parent_id);
}
/*echo '<pre>';
print_r($cats);
echo '</pre>';*/

$html = '';
$span = '';

foreach ($cats->Category as $key => $records) {
    if ($records->LeafCategory) {
        $span = '<span class="dashicons dashicons-arrow-right-alt2"  onclick="fetchChildCategory(' . $records->CategoryID . ', this )" style="cursor: pointer;"><input type="hidden" value = "' . $records->CategoryName . '" /></span>';
    } else {
        $span = '<span class="dashicons dashicons-minus"></span>';
    }
    $html .= '<div class="ebayCatList" style ="padding-left : 12px;">' . $span . '<span class="categorytitle" onclick="doSelecteBayCategories(' . $records->CategoryID . ')" style="cursor :pointer;">' . $records->CategoryName . '</span></div><div id ="child-' . $records->CategoryID . '" style="padding-left:30px "></div>';
    $html .= '<input type="hidden" value = "' . $records->CategoryNamePath . '" id="hiddenCategoryName-' . $records->CategoryID . '"/>';


}
echo $html;
//echo json_encode($cats->Category);
wp_die();




