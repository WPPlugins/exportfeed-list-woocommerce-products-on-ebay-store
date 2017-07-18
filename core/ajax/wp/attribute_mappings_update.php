<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
update_option(sanitize_text_field($_POST['service_name']) . '_ebcpf_' . sanitize_text_field($_POST['attribute']), sanitize_text_field($_POST['mapto']));
