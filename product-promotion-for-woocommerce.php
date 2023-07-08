<?php
/**
 * Plugin Name: Product Promotion for WooCommerce
 * Plugin URI: https://fernandoroche.com/product-promotion-for-woocommerce
 * Description: This plugin allows you to promote a product across your WooCommerce store.
 * Version: 1.0
 * Author: Fernando Roche
 * Author URI: https://fernandoroche.com
 * License: GPL2
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    return; // Check if WooCommerce is active
}

require __DIR__ . '/vendor/autoload.php';

$plugin = new FernandoRoche\ProductPromotion\ProductPromotionForWooCommerce();