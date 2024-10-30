<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// die when the file is called directly
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

require_once plugin_dir_path(__FILE__) . 'config.php';
require_once plugin_dir_path(__FILE__) . 'mt-web-rating.php';

function deleteOption( $optionFields ){
	foreach ($optionFields as $field) {
		$option_name = $field['uid'];
		delete_option($option_name);
	}
}

deleteOption( $fields );