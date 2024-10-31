<?php
/**
 * @package ProChains\Functions
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

// Custom function for product creation (For Woocommerce 3+ only)
function pch_create_product( $args ){

    if( ! function_exists('wc_get_product_object_type') )
        return false;

    // Get an empty instance of the product object (defining it's type)
    $product = wc_get_product_object_type( $args['type'] );
    if( ! $product )
        return false;

    // Product name (Title) and slug
    $product->set_name( $args['name'] ); // Name (title).
    if( isset( $args['slug'] ) )
        $product->set_name( $args['slug'] );

    // Description and short description:
    $product->set_description( $args['description'] );
    $product->set_short_description( $args['short_description'] );

    // Status ('publish', 'pending', 'draft' or 'trash')
    $product->set_status( isset($args['status']) ? $args['status'] : 'publish' );

    // Visibility ('hidden', 'visible', 'search' or 'catalog')
    $product->set_catalog_visibility( isset($args['visibility']) ? $args['visibility'] : 'visible' );

    // Featured (boolean)
    $product->set_featured(  isset($args['featured']) ? $args['featured'] : false );

    // Virtual (boolean)
    $product->set_virtual( isset($args['virtual']) ? $args['virtual'] : false );

    // Prices
    $product->set_regular_price( $args['regular_price'] );
    $product->set_sale_price( isset( $args['sale_price'] ) ? $args['sale_price'] : '' );
    $product->set_price( isset( $args['sale_price'] ) ? $args['sale_price'] :  $args['regular_price'] );
    if( isset( $args['sale_price'] ) ){
        $product->set_date_on_sale_from( isset( $args['sale_from'] ) ? $args['sale_from'] : '' );
        $product->set_date_on_sale_to( isset( $args['sale_to'] ) ? $args['sale_to'] : '' );
    }

    // Downloadable (boolean)
    $product->set_downloadable(  isset($args['downloadable']) ? $args['downloadable'] : false );
    if( isset($args['downloadable']) && $args['downloadable'] ) {
        $product->set_downloads(  isset($args['downloads']) ? $args['downloads'] : array() );
        $product->set_download_limit(  isset($args['download_limit']) ? $args['download_limit'] : '-1' );
        $product->set_download_expiry(  isset($args['download_expiry']) ? $args['download_expiry'] : '-1' );
    }

    // Taxes
    if ( get_option( 'woocommerce_calc_taxes' ) === 'yes' ) {
        $product->set_tax_status(  isset($args['tax_status']) ? $args['tax_status'] : 'taxable' );
        $product->set_tax_class(  isset($args['tax_class']) ? $args['tax_class'] : '' );
    }

    // SKU and Stock (Not a virtual product)
    if( isset($args['virtual']) && ! $args['virtual'] ) {
        $product->set_sku( isset( $args['sku'] ) ? $args['sku'] : '' );
        // $product->set_manage_stock( isset( $args['manage_stock'] ) ? $args['manage_stock'] : false );
        $product->set_stock_status( isset( $args['stock_status'] ) ? $args['stock_status'] : 'instock' );
        if( isset( $args['manage_stock'] ) && $args['manage_stock'] ) {
            // $product->set_stock_status( $args['stock_qty'] );
            $product->set_backorders( isset( $args['backorders'] ) ? $args['backorders'] : 'no' ); // 'yes', 'no' or 'notify'
        }
    }

    // Sold Individually
    $product->set_sold_individually( isset( $args['sold_individually'] ) ? $args['sold_individually'] : false );

    // Weight, dimensions and shipping class
    $product->set_weight( isset( $args['weight'] ) ? $args['weight'] : '' );
    $product->set_length( isset( $args['length'] ) ? $args['length'] : '' );
    $product->set_width( isset(  $args['width'] ) ?  $args['width']  : '' );
    $product->set_height( isset( $args['height'] ) ? $args['height'] : '' );
    if( isset( $args['shipping_class_id'] ) )
        $product->set_shipping_class_id( $args['shipping_class_id'] );

    // Upsell and Cross sell (IDs)
    $product->set_upsell_ids( isset( $args['upsells'] ) ? $args['upsells'] : '' );
    $product->set_cross_sell_ids( isset( $args['cross_sells'] ) ? $args['upsells'] : '' );

    // Attributes et default attributes
    // if( isset( $args['attributes'] ) )
    //     $product->set_attributes( wc_prepare_product_attributes($args['attributes']) );
    // if( isset( $args['default_attributes'] ) )
    //     $product->set_default_attributes( $args['default_attributes'] ); // Needs a special formatting

    // Reviews, purchase note and menu order
    $product->set_reviews_allowed( isset( $args['reviews_allowed'] ) ? $args['reviews_allowed'] : false );
    $product->set_purchase_note( isset( $args['note'] ) ? $args['note'] : '' );
    if( isset( $args['menu_order'] ) )
        $product->set_menu_order( $args['menu_order'] );

    // Product categories and Tags
    if( isset( $args['category_ids'] ) )
        $product->set_category_ids( $args['category_ids'] );
    if( isset( $args['tag_ids'] ) )
        $product->set_tag_ids( $args['tag_ids'] );


    // Images and Gallery
    $product->set_image_id( isset( $args['image_id'] ) ? $args['image_id'] : "" );
    $product->set_gallery_image_ids( isset( $args['gallery_ids'] ) ? $args['gallery_ids'] : array() );

    ## --- SAVE PRODUCT --- ##
    $product_id = $product->save();

    return $product_id;
}

// Utility function that returns the correct product object instance
function wc_get_product_object_type( $type ) {
    // Get an instance of the WC_Product object (depending on his type)
    if( isset($type) && $type === 'variable' ){
        $product = new WC_Product_Variable();
    } elseif( isset($type) && $type === 'grouped' ){
        $product = new WC_Product_Grouped();
    } elseif( isset($type) && $type === 'external' ){
        $product = new WC_Product_External();
    } else {
        $product = new WC_Product_Simple(); // "simple" By default
    } 
    
    if( ! is_a( $product, 'WC_Product' ) )
        return false;
    else
        return $product;
}

function pch_create_product_variation( $product_id, $variation_data ){
    // Get the Variable product object (parent)
    $product = wc_get_product($product_id);

    $variation_post = array(
        'post_title'  => $product->get_name(),
        'post_name'   => 'product-'.$product_id.'-variation',
        'post_status' => 'publish',
        'post_parent' => $product_id,
        'post_type'   => 'product_variation',
        'guid'        => $product->get_permalink()
    );

    // Creating the product variation
    $variation_id = wp_insert_post( $variation_post );

    // Get an instance of the WC_Product_Variation object
    $variation = new WC_Product_Variation( $variation_id );

    // Iterating through the variations attributes
    foreach ($variation_data['attributes'] as $attribute => $term_name )
    {
        $taxonomy = $attribute; // The attribute taxonomy

        // If taxonomy doesn't exists we create it (Thanks to Carl F. Corneil)
        if( ! taxonomy_exists( $taxonomy ) ){
            register_taxonomy(
                $taxonomy,
               'product_variation',
                array(
                    'hierarchical' => false,
                    'label' => ucfirst( $attribute ),
                    'query_var' => true,
                    'rewrite' => array( 'slug' => sanitize_title($attribute) ), // The base slug
                ),
            );
        }

        // Check if the Term name exist and if not we create it.
        if( ! term_exists( $term_name, $taxonomy ) )
            wp_insert_term( $term_name, $taxonomy ); // Create the term

        $term_slug = get_term_by('name', $term_name, $taxonomy )->slug; // Get the term slug

        // Get the post Terms names from the parent variable product.
        $post_term_names = wp_get_post_terms( $product_id, $taxonomy, array('fields' => 'names') );

        // Check if the post term exist and if not we set it in the parent variable product.
        if( ! in_array( $term_name, $post_term_names ) )
            wp_set_post_terms( $product_id, $term_name, $taxonomy, true );

        // Set/save the attribute data in the product variation
        update_post_meta( $variation_id, 'attribute_'.$taxonomy, $term_name );
    }

    ## Set/save all other data

    // SKU
    if( ! empty( $variation_data['sku'] ) )
        $variation->set_sku( $variation_data['sku'] );

    // Prices
    if( empty( $variation_data['sale_price'] ) ){
        $variation->set_price( $variation_data['regular_price'] );
    } else {
        $variation->set_price( $variation_data['sale_price'] );
        $variation->set_sale_price( $variation_data['sale_price'] );
    }
    $variation->set_regular_price( $variation_data['regular_price'] );

    // Stock
    if( ! empty($variation_data['stock_qty']) ){
        $variation->set_stock_quantity( $variation_data['stock_qty'] );
        $variation->set_manage_stock(true);
        $variation->set_stock_status('');
    } else {
        $variation->set_manage_stock(false);
    }
    
    $variation->set_weight(''); // weight (reseting)

    $variation->save(); // Save the data
}

function pch_get_category_ids($categories=array()) {
    $category_ids = array();
    $previous_cat = "";

    if(count($categories) > 0) {
        foreach($categories as $category) {
            $category_exists = term_exists($category['display_name'], 'product_cat');

            if(is_null($category_exists)) {
                $new_category = wp_insert_term( $category['display_name'], 'product_cat', array(
                    'parent' => ($category['no_sub'] === true && $previous_cat !== '' ? $previous_cat : 0)
                ) );

                $category_ids[] = $new_category['term_id'];
                $previous_cat = (int) $new_category['term_id'];
            } else {
                $category_ids[] = $category_exists['term_id'];
                $previous_cat = (int) $category_exists['term_id'];
            }
        }
    }

    return $category_ids;
}

function pch_get_image_id($base_url, $url) {
    $full_url = $base_url . $url;
    $image_id = 0;

    if(str_contains(strtolower($full_url), '.jpg') || str_contains(strtolower($full_url), '.jpeg') || str_contains(strtolower($full_url), '.png') || str_contains(strtolower($full_url), '.webp') || str_contains(strtolower($full_url), '.tiff') || str_contains(strtolower($full_url), '.gif')) {
        // Media handle
        $tmp = download_url($full_url);

        $file_array = array(
            'name'      => basename($full_url),
            'tmp_name'  => $tmp
        );

        if ( is_wp_error( $tmp ) ) {
            @unlink( $file_array[ 'tmp_name' ] );
            return $tmp;
        }

        $image_id = media_handle_sideload( $file_array, 0 );

        if ( is_wp_error( $image_id ) ) {
            @unlink( $file_array['tmp_name'] );
            return $image_id;
        }

        if($image_id) {
            unlink($file_array['tmp_name']);
    
            return $image_id;
        } else {
            return null;
        }
    } else {
        $img_name = rand(10000, 99999) . ".jpg";

        file_put_contents(PCH_PLUGIN_DIR . 'uploads/temp/' . $img_name, file_get_contents($full_url));

        // Media handle
        $tmp = PCH_PLUGIN_DIR . 'uploads/temp/' . $img_name;

        $file_array = array(
            'name'      => $img_name,
            'tmp_name'  => $tmp
        );

        $image_id = media_handle_sideload( $file_array, 0 );

        if($image_id) {
            unlink($tmp);
    
            return $image_id;
        } else {
            return null;
        }
    }
}

function pch_get_gallery_ids($base_url='', $featured='', $urls=array()) {
    $gallery_ids = array();

    if(count($urls) > 0) {
        foreach($urls as $url) {
            if($url === $featured) {
                continue;
            }
            
            $full_url = $base_url . $url;
            $image_id = 0;

            if(str_contains(strtolower($full_url), '.jpg') || str_contains(strtolower($full_url), '.jpeg') || str_contains(strtolower($full_url), '.png') || str_contains(strtolower($full_url), '.webp') || str_contains(strtolower($full_url), '.tiff') || str_contains(strtolower($full_url), '.gif')) {
                // Media handle
                $tmp = download_url($full_url);

                $file_array = array(
                    'name'      => basename($full_url),
                    'tmp_name'  => $tmp
                );

                if ( is_wp_error( $tmp ) ) {
                    @unlink( $file_array[ 'tmp_name' ] );
                    return $tmp;
                }

                $image_id = media_handle_sideload( $file_array, 0 );

                if ( is_wp_error( $image_id ) ) {
                    @unlink( $file_array['tmp_name'] );
                    return $image_id;
                }

                if($image_id) {
                    unlink($file_array['tmp_name']);
            
                    $gallery_ids[] = $image_id;
                }
            } else {
                $img_name = rand(10000, 99999) . ".jpg";
        
                file_put_contents(PCH_PLUGIN_DIR . 'uploads/temp/' . $img_name, file_get_contents($full_url));

                // Media handle
                $tmp = PCH_PLUGIN_DIR . 'uploads/temp/' . $img_name;

                $file_array = array(
                    'name'      => $img_name,
                    'tmp_name'  => $tmp
                );

                $image_id = media_handle_sideload( $file_array, 0 );

                if($image_id) {
                    unlink($tmp);
            
                    $gallery_ids[] = $image_id;
                }
            }
        }
    }

    return $gallery_ids;
}

function pch_delete_variations( $product_id, $force_delete = false ) {
    if ( ! is_numeric( $product_id ) || 0 >= $product_id ) {
      return;
    }

    $variation_ids = wp_parse_id_list(
        get_posts(
            array(
                'post_parent' => $product_id,
                'post_type'   => 'product_variation',
                'fields'      => 'ids',
                'post_status' => array( 'any', 'trash', 'auto-draft' ),
                'numberposts' => -1, // phpcs:ignore WordPress.VIP.PostsPerPage.posts_per_page_numberposts
            )
        )
    );

    if ( ! empty( $variation_ids ) ) {
        foreach ( $variation_ids as $variation_id ) {
            if ( $force_delete ) {
                do_action( 'woocommerce_before_delete_product_variation', $variation_id );
                wp_delete_post( $variation_id, true );
                do_action( 'woocommerce_delete_product_variation', $variation_id );
            } else {
                wp_trash_post( $variation_id );
                do_action( 'woocommerce_trash_product_variation', $variation_id );
            }
        }
    }

    delete_transient( 'wc_product_children_' . $product_id );
}