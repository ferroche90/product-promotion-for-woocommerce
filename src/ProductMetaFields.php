<?php

namespace FernandoRoche\ProductPromotion;

/**
 * Class ProductMetaFields
 *
 * Handles the product meta fields for product promotion.
 */
class ProductMetaFields {

    /**
     * ProductMetaFields constructor.
     *
     * Sets up the necessary actions.
     */
    public function __construct() {
        add_action( 'woocommerce_product_options_general_product_data', array( $this, 'add_custom_fields' ) );
        add_action( 'woocommerce_process_product_meta', array( $this, 'save_custom_fields' ) );
    }

    /**
     * Adds custom fields to the product data meta box.
     */
    public function add_custom_fields() {
        global $woocommerce, $post;

        echo '<div class="options_group">';

        // Checkbox
        woocommerce_wp_checkbox(
            array(
                'id'            => '_promote_product',
                'label'         => __( 'Promote this product', 'woocommerce' ),
                'description'   => __( 'Check this if you want to promote this product.', 'woocommerce' )
            )
        );

        // Text Field
        woocommerce_wp_text_input(
            array(
                'id'            => '_promoted_product_title',
                'label'         => __( 'Promoted Product Title', 'woocommerce' ),
                'placeholder'   => __( 'Enter the promoted product title', 'woocommerce' ),
                'desc_tip'      => 'true',
                'description'   => __( 'Enter a custom title for the promoted product. If left empty, the product title will be used.', 'woocommerce' )
            )
        );

        // Checkbox for Expiration
        woocommerce_wp_checkbox(
            array(
                'id'            => '_set_expiration',
                'label'         => __( 'Set expiration date', 'woocommerce' ),
                'description'   => __( 'Check this if you want to set an expiration date for the promotion.', 'woocommerce' ),
                'cbvalue'       => 'yes',
                'class'         => 'expiration-checkbox'
            )
        );

        // Date and Time Picker for Expiration
        $expiration_date_meta = get_post_meta($post->ID, '_expiration_datetime', true);
        if (empty($expiration_date_meta)) {
            $expiration_date_default = date('Y-m-d') . 'T23:59';
            update_post_meta($post->ID, '_expiration_datetime', $expiration_date_default);
        }
        woocommerce_wp_text_input(
            array(
                'id'            => '_expiration_datetime',
                'label'         => __( 'Expiration Date and Time', 'woocommerce' ),
                'placeholder'   => __( 'Enter the expiration date and time', 'woocommerce' ),
                'desc_tip'      => 'true',
                'description'   => __( 'Enter the expiration date and time for the promotion.', 'woocommerce' ),
                'type'          => 'datetime-local',
                'class'         => 'expiration-date'
            )
        );

        echo '</div>';
    }

    /**
     * Saves the custom fields.
     *
     * @param int $post_id The post ID.
     */
    public function save_custom_fields( $post_id ) {
        $promote_product = isset( $_POST['_promote_product'] ) ? 'yes' : 'no';

        // If this product is being promoted, un-promote the currently promoted product.
        if ( $promote_product === 'yes' ) {
            $current_promoted_product_id = get_option( 'promoted_product' );
            if ( $current_promoted_product_id && $current_promoted_product_id != $post_id ) {
                update_post_meta( $current_promoted_product_id, '_promote_product', 'no' );
            }

            // Now promote this product.
            update_option( 'promoted_product', $post_id );
        }

        update_post_meta( $post_id, '_promote_product', $promote_product );

        if ( isset( $_POST['_promoted_product_title'] ) ) {
            update_post_meta( $post_id, '_promoted_product_title', sanitize_text_field( $_POST['_promoted_product_title'] ) );
        }

        $set_expiration = isset( $_POST['_set_expiration'] ) ? 'yes' : 'no';
        update_post_meta( $post_id, '_set_expiration', $set_expiration );

        if ( $set_expiration === 'yes' ) {
            if ( isset( $_POST['_expiration_datetime'] ) ) {
                $expiration_datetime = sanitize_text_field( $_POST['_expiration_datetime'] );

                // Convert the datetime string to a UNIX timestamp
                $expiration_timestamp = strtotime( $expiration_datetime );

                // Save the expiration date and time as a string
                update_post_meta( $post_id, '_expiration_datetime', $expiration_datetime );

                // Save the expiration timestamp separately (optional)
                update_post_meta( $post_id, '_expiration_timestamp', $expiration_timestamp );
            } 
        } else {
            // If "Set expiration date" is not checked, always promote the product
            delete_post_meta( $post_id, '_expiration_datetime' );
            delete_post_meta( $post_id, '_expiration_timestamp' );
        }
    }

}