<?php

namespace FernandoRoche\ProductPromotion;

/**
 * Class ProductPromotionForWooCommerce
 *
 * Handles the product promotion functionality.
 */
class ProductPromotionForWooCommerce { 

    /**
     * ProductPromotionForWooCommerce constructor.
     *
     * Sets up the necessary actions and filters.
     */
    public function __construct() {
        add_filter( 'woocommerce_get_sections_products', array( $this, 'add_section' ) );
        add_filter( 'woocommerce_get_settings_products', array( $this, 'add_settings' ), 10, 2 );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_action('wp_footer', array($this, 'append_header_content'));
    
        new ProductMetaFields();
    }

    /**
     * Enqueues necessary scripts.
     */
    public function enqueue_scripts() {
        wp_enqueue_script( 'product-promotion-admin', plugins_url( '../js/admin.js', __FILE__ ), array( 'jquery' ), '1.0.0', true );
    }  

    /**
     * Adds a new section to the WooCommerce settings.
     *
     * @param array $sections The existing sections.
     * @return array The updated sections.
     */
    public function add_section( $sections ) {
        $sections['product_promotion'] = __( 'Product Promotion', 'text-domain' );
        return $sections;
    }

    /**
     * Adds new settings to the WooCommerce settings.
     *
     * @param array $settings The existing settings.
     * @param string $current_section The current section.
     * @return array The updated settings.
     */
    public function add_settings( $settings, $current_section ) {
        if ( 'product_promotion' == $current_section ) {
            $settings_promotion = array();
            $product_id = get_option( 'promoted_product' );
            $promote_product = get_post_meta( $product_id, '_promote_product', true );
    
            if ( $promote_product === 'yes' ) {
                // Display settings when a product is being promoted
                // Add Title to the Settings
                $settings_promotion[] = array(
                    'name' => __( 'Product Promotion Settings', 'text-domain' ),
                    'type' => 'title',
                    'desc' => __( 'The following options are used to configure Product Promotion', 'text-domain' ),
                    'id' => 'product_promotion'
                );
        
                // Add first text field option
                $settings_promotion[] = array(
                    'name' => __( 'Promoted Product Title', 'text-domain' ),
                    'desc_tip' => __( 'This will be the title of the promoted product', 'text-domain' ),
                    'id' => 'promoted_product_title',
                    'type' => 'text',
                    'css' => 'min-width:300px;',
                    'desc' => __( 'Enter the title of the promoted product', 'text-domain' ),
                );
        
                // Add color picker for background color
                $settings_promotion[] = array(
                    'name' => __( 'Background Color', 'text-domain' ),
                    'desc_tip' => __( 'This will set the background color for the promoted product', 'text-domain' ),
                    'id' => 'promoted_product_bg_color',
                    'type' => 'color',
                    'css' => 'width:6em;',
                    'desc' => __( 'Select the background color', 'text-domain' ),
                );
        
                // Add color picker for text color
                $settings_promotion[] = array(
                    'name' => __( 'Text Color', 'text-domain' ),
                    'desc_tip' => __( 'This will set the text color for the promoted product', 'text-domain' ),
                    'id' => 'promoted_product_text_color',
                    'type' => 'color',
                    'css' => 'width:6em;',
                    'desc' => __( 'Select the text color', 'text-domain' ),
                );
        
                // Display of active promoted product
                $settings_promotion[] = array(
                    'name' => __( 'Active Promoted Product', 'text-domain' ),
                    'type' => 'title',
                    'desc' => $this->get_active_promoted_product(),
                    'id' => 'active_promoted_product'
                );
            } else {
                // Display message and button when no product is being promoted
                $settings_promotion[] = array(
                    'name' => __( 'Active Promoted Product', 'text-domain' ),
                    'type' => 'title',
                    'desc' => $this->get_active_promoted_product(),
                    'id' => 'active_promoted_product'
                );
            }
    
            $settings_promotion[] = array( 'type' => 'sectionend', 'id' => 'product_promotion' );
    
            return $settings_promotion;
        } else {
            return $settings;
        }
    }

    /**
     * Retrieves the product options.
     *
     * @return array The product options.
     */
    public function get_product_options() {
        $args = array(
            'post_type'      => 'product',
            'posts_per_page' => -1,
        );
    
        $loop = new \WP_Query( $args ); // Note the backslash before WP_Query
        $products = array();
    
        while ( $loop->have_posts() ) : $loop->the_post();
            global $product;
            $products[ get_the_ID() ] = get_the_title();
        endwhile;
    
        wp_reset_query();
        return $products;
    }

    /**
     * Retrieves the active promoted product.
     *
     * @return string The active promoted product.
     */
    public function get_active_promoted_product() {
        $product_id = get_option( 'promoted_product' );    
        $product = wc_get_product( $product_id );
        $promote_product = get_post_meta( $product_id, '_promote_product', true );
        $set_expiration = get_post_meta( $product_id, '_set_expiration', true );
        $expiration_date = get_post_meta( $product_id, '_expiration_date', true );
    
        // Check if the product is marked for promotion and (the expiration date is not set or is not outdated)
        if ( $promote_product === 'yes' && ($set_expiration !== 'yes' || strtotime($expiration_date) > time()) ) {
            $edit_link = get_edit_post_link( $product_id );
    
            return sprintf(
                __( 'The currently promoted product is <a href="%s">%s</a>. <a href="%s">Edit Product</a>', 'text-domain' ),
                $product->get_permalink(),
                $product->get_name(),
                $edit_link
            );
        } else {
            $products_page_url = admin_url( 'edit.php?post_type=product' );
            return __( 'No product is currently being promoted. <a class="button" href="' . $products_page_url . '">Promote a Product</a>', 'text-domain' );
        }
    } 

    /**
     * Appends content to the header.
     */
    public function append_header_content(){
        $product_id = get_option('promoted_product');
        $product = wc_get_product($product_id);
        $promoted_product_title = get_option('promoted_product_title');
        $promote_product = get_post_meta($product_id, '_promote_product', true);
        $text_color = get_option('promoted_product_text_color');
        $bg_color = get_option('promoted_product_bg_color');
        $expiration_date = get_post_meta($product_id, '_expiration_date', true);
        $current_time = time();

        if ($product && $promoted_product_title && $promote_product === 'yes' && strtotime($expiration_date) > $current_time) {
            $title = get_post_meta($product_id, '_promoted_product_title', true) ?: $product->get_title();

            ?>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    var headerContent = document.createElement('div');
                    headerContent.style.width = '100%';
                    headerContent.style.backgroundColor = '<?php echo esc_html($bg_color); ?>';
                    headerContent.style.padding = '10px';
                    headerContent.style.textAlign = 'center';

                    var span1 = document.createElement('span');
                    span1.style.fontWeight = 'bold';
                    span1.style.color = '<?php echo esc_html($text_color); ?>';
                    span1.textContent = '<?php echo esc_html($promoted_product_title); ?>:';

                    var span2 = document.createElement('span');
                    span2.style.marginLeft = '5px';
                    span2.style.color = '<?php echo esc_html($text_color); ?>';
                    span2.innerHTML = '<a href="<?php echo esc_url($product->get_permalink()); ?>" style="color: <?php echo esc_html($text_color); ?>;"><?php echo esc_html($title); ?></a>';

                    headerContent.appendChild(span1);
                    headerContent.appendChild(span2);

                    var header = document.getElementsByTagName('header')[0];
                    if (header) {
                        header.appendChild(headerContent);
                    }
                });
            </script>
            <?php
        }
    }
    
}
