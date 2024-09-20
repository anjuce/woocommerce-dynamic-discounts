<?php

/*
Plugin Name: WooCommerce Dynamic Discounts
Description: Adds the ability to set dynamic discounts depending on the quantity of the product.
Version: 1.0
Author: Anzhela Zdorovtsova
*/

// Protection against direct access
if (!defined('ABSPATH')) {
    exit;
}

// Plugin initialization
function wcd_dynamic_discounts_init() {
    // WooCommerce availability check
    if ( ! class_exists( 'WooCommerce' ) ) {
        add_action( 'admin_notices', 'wcd_wc_missing_notice' );
        return;
    }

    // Downloading the necessary functions
    include_once dirname( __FILE__ ) . '/includes/wcd-dynamic-discounts-functions.php';
}
add_action( 'plugins_loaded', 'wcd_dynamic_discounts_init' );

// A function that shows a WooCommerce missing message
function wcd_wc_missing_notice() {
    echo '<div class="error"><p><strong>WooCommerce Dynamic Discounts</strong> потребує WooCommerce для роботи. Будь ласка, встановіть та активуйте WooCommerce.</p></div>';
}
