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
                'description'   => __( 'Check this if you want to set an expiration date for the promotion.', 'woocommerce' )
            )
        );

        // Date Picker for Expiration
        woocommerce_wp_text_input(
            array(
                'id'            => '_expiration_date',
                'label'         => __( 'Expiration Date', 'woocommerce' ),
                'placeholder'   => __( 'Enter the expiration date', 'woocommerce' ),
                'desc_tip'      => 'true',
                'description'   => __( 'Enter the expiration date for the promotion.', 'woocommerce' ),
                'type'          => 'date'
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
    
        if ( isset( $_POST['_expiration_date'] ) ) {
            update_post_meta( $post_id, '_expiration_date', sanitize_text_field( $_POST['_expiration_date'] ) );
        }
    }
}
