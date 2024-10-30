<?php

/**
 * Plugin Name:       MT Web Rating
 * Description:       Display your Google, Tripadvisor etc... ratings
 * Version:           1.0.2
 * Author:            Mathis Tailland
 * Author URI:        https://mathistailland.com
 * Text Domain:       mt-web-rating
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * GitHub Plugin URI: https://mathistailland.com
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require_once plugin_dir_path(__FILE__) . 'config.php';
require_once plugin_dir_path(__FILE__) . 'includes/functions.php';

$sections = array(
    array(
        'id' => PLUGIN_PREFIX . 'rating_ids',
        'title' => 'Identifiants Web',
    ),
);

$fields = array(
    array(
        'uid' => PLUGIN_PREFIX . 'google_id',
        'label' => 'Google Place ID',
        'section' => PLUGIN_PREFIX . 'rating_ids',
        'type' => 'text',
        'options' => false,
        'placeholder' => '',
        'helper' => '',
        'supplemental' => '',
        'default' => ''
    ),
    array(
        'uid' => PLUGIN_PREFIX . 'facebook_id',
        'label' => 'Facebook Slug',
        'section' => PLUGIN_PREFIX . 'rating_ids',
        'type' => 'text',
        'options' => false,
        'placeholder' => '',
        'helper' => '',
        'supplemental' => '',
        'default' => ''
    ),
    array(
        'uid' => PLUGIN_PREFIX . 'tripadvisor_id',
        'label' => 'Tripadvisor Slug',
        'section' => PLUGIN_PREFIX . 'rating_ids',
        'type' => 'text',
        'options' => false,
        'placeholder' => '',
        'helper' => '',
        'supplemental' => '',
        'default' => ''
    ),
);

$settings_page = new plugin_settings_page($sections, $fields,'MT Star Rating Settings Page', 'MT Star Rating', PLUGIN_PREFIX .'settings');
