<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly
define('XMLRPC_REQUEST', true);

define('EBCPF_URL', plugins_url() . '/' . basename(dirname(__FILE__)) . '/');
global $wpdb;
$account_id = intval($_POST['account_id']);
$result = array();
$status = false;

$tableName = 'ebcpf_ebay_accounts';
$table = $wpdb->prefix . $tableName;

//first find the default account
$sql = $wpdb->prepare("
			SELECT id
			FROM $table
            WHERE default_account = %d",[1]);
$default_account = $wpdb->get_var(
);

//Set data for new default account
$data = array(
    'default_account' => 1
);

$where = array(
    'id' => $account_id
);

//set data for previously default account
$data1 = array(
    'default_account' => 0
);
$where1 = array(
    'id' => $default_account
);


if (($wpdb->update($table, $data, $where))) {
    $wpdb->update($table, $data1, $where1);
    $status = true;
}

if ($status) {
    $result = array(
        'msg' => $wpdb->last_error,
        'status' => true
    );
} else {
    $result = array(
        'msg' => $wpdb->last_error,
        'status' => false,
    );
}
echo json_encode($result);
