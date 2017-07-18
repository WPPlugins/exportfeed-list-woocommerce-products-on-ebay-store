<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly
$map_string = get_option('ebcpf_attribute_user_map_' . sanitize_text_field($_POST['service_name']));

if (strlen($map_string) == 0)
    $map = array();
else {
    $map = json_decode($map_string);
    $map = get_object_vars($map);
}

$attr = sanitize_text_field($_POST['attribute']);
$mapto = sanitize_text_field($_POST['mapto']);
$map[$mapto] = $attr;

if ($attr == '(Reset)') {
    $new_map = array();
    foreach ($map as $index => $item)
        if ($index != $mapto)
            $new_map[$index] = $item;
    $map = $new_map;
}

update_option('ebcpf_attribute_user_map_' . sanitize_text_field($_POST['service_name']), json_encode($map));
