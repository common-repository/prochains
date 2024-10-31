<?php
/**
 * @package ProChains\Functions
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

// REST API
add_action('rest_api_init', 'pch_register_api_create_product');
function pch_register_api_create_product() {
  register_rest_route('prochains/v1', '/product/create', array(
    'methods'  => 'POST',
    'callback' => 'pch_api_create_product',
    // 'permission_callback' => function() {
    //   return is_user_logged_in();
    // }
  ));
}

function pch_api_create_product($request) {
  $type = ($request['product_alias'] === 'affiliate' ? 'external' : ($request['variation_attributes'][0]['name'] !== '' ? 'variable' : $request['product_type']));
  
  $args = array(
    'type' => sanitize_text_field($type),
    'name' => sanitize_text_field($request['post_title']),
    'description' => $request['post_content'],
    'reviews_allowed' => ($request['comment_status'] === 'open' ? true : false),
    'status' => ($request['post_status'] === 'publish' ? 'publish' : 'draft')
  );

  // Category
  $category_ids = pch_get_category_ids($request['product_cat']);

  if(count($category_ids) > 0) {
    $args['category_ids'] = $category_ids;
  }

  // Featured Image
  $image_id = pch_get_image_id($request['base_thumbnail_url'], $request['post_thumbnail']);

  if(!is_null($image_id)) {
    $args['image_id'] = $image_id;
  }

  // Gallery Image
  $gallery_ids = pch_get_gallery_ids($request['base_thumbnail_url'], $request['post_thumbnail'], $request['post_meta']['_product_image_gallery']);

  if(count($gallery_ids) > 0) {
    $args['gallery_ids'] = $gallery_ids;
  }

  // Simple Product
  if($args['type'] === 'simple') {
    $regular_price = ($request['variations'][0]['price_before_discount'] === 0 ? $request['variations'][0]['price'] : $request['variations'][0]['price_before_discount']) / $request['spend_cash_unit'];
    $final_regular_price = $regular_price + $request['dropship']['shipping_cost'] + ($request['dropship']['markup_type'] === 'fixed' ? $request['dropship']['markup_price'] : ($request['dropship']['markup_price'] * $regular_price) / 100);

    $sale_price = $request['variations'][0]['price'] / $request['spend_cash_unit'];
    $final_sale_price = $sale_price + $request['dropship']['shipping_cost'] + ($request['dropship']['markup_type'] === 'fixed' ? $request['dropship']['markup_price'] : ($request['dropship']['markup_price'] * $sale_price) / 100);

    $args['regular_price'] = $final_regular_price;
    $args['sale_price'] = $final_sale_price;
  }

  // External Product
  if($args['type'] === 'external') {
    $args['regular_price'] = ($request['variations'][0]['price_before_discount'] === 0 ? $request['variations'][0]['price'] : $request['variations'][0]['price_before_discount']) / $request['spend_cash_unit'];
    $args['sale_price'] = $request['variations'][0]['price'] / $request['spend_cash_unit'];
  }

  $product_id = pch_create_product($args);

  if($args['type'] === 'simple') {
    // Stock Management
    update_post_meta( $product_id, '_manage_stock', $request['post_meta']['_manage_stock'] );
    update_post_meta( $product_id, '_stock', $request['variations'][0]['stock'] );

    // Attributes
    $attributes = array();
    $product_attributes = $request['post_meta']['_product_attributes'];

    if(!is_null($product_attributes)) {
      foreach($product_attributes as $pa) {
        $attributes[sanitize_title($pa['name'])] = array(
          'name' => sanitize_text_field($pa['name']),
          'value' => sanitize_text_field($pa['value']),
          'is_visible' => '1',
          'is_variation' => '0'
        );
      }

      update_post_meta( $product_id, '_product_attributes', $attributes );
    }
  }

  if($args['type'] === 'variable') {
    // Attributes
    $attributes = array();
    $product_attributes = $request['post_meta']['_product_attributes'];

    if(!is_null($product_attributes)) {
      foreach($product_attributes as $pa) {
        $attributes[sanitize_title($pa['name'])] = array(
          'name' => sanitize_text_field($pa['name']),
          'value' => sanitize_text_field($pa['value']),
          'is_visible' => '1',
          'is_variation' => '0'
        );
      }

      update_post_meta( $product_id, '_product_attributes', $attributes );
    }

    // Variation Attributes
    $tier_product_variation_attributes = array();
    $product_variation_attributes = $request['variation_attributes'];

    if(!is_null($product_variation_attributes)) {
      foreach($product_variation_attributes as $va) {
        $tier_product_variation_attributes[] = sanitize_title($va['name']);

        $attributes[sanitize_title($va['name'])] = array(
          'name' => sanitize_text_field($va['name']),
          'value' => implode('|', $va['options']),
          'is_visible' => '1',
          'is_variation' => '1'
        );
      }

      update_post_meta( $product_id, '_product_attributes', $attributes );
    }

    // Product Variations
    $product_variations = $request['variations'];

    foreach($product_variations as $pv) {
      if($pv['status']) {
        $pv_name = explode(',', $pv['name']);
        $pv_attributes = array();

        for($i=0; $i<count($pv_name); $i++) {
          $pv_attributes[$tier_product_variation_attributes[$i]] = $pv_name[$i];
        }

        $regular_price = ($pv['price_before_discount'] === 0 ? $pv['price'] : $pv['price_before_discount']) / $request['spend_cash_unit'];
        $final_regular_price = $regular_price + $request['dropship']['shipping_cost'] + ($request['dropship']['markup_type'] === 'fixed' ? $request['dropship']['markup_price'] : ($request['dropship']['markup_price'] * $regular_price) / 100);

        $sale_price = $pv['price'] / $request['spend_cash_unit'];
        $final_sale_price = $sale_price + $request['dropship']['shipping_cost'] + ($request['dropship']['markup_type'] === 'fixed' ? $request['dropship']['markup_price'] : ($request['dropship']['markup_price'] * $sale_price) / 100);

        $variation_data =  array(
          'attributes' => $pv_attributes,
          'regular_price' => $final_regular_price,
          'sale_price' => $final_sale_price,
          'stock_qty' => $pv['stock']
        );

        pch_create_product_variation( $product_id, $variation_data );
      }
    }
  }

  if($args['type'] === 'external') {
    // Attributes
    $attributes = array();
    $product_attributes = $request['post_meta']['_product_attributes'];

    if(!is_null($product_attributes)) {
      foreach($product_attributes as $pa) {
        $attributes[sanitize_title($pa['name'])] = array(
          'name' => sanitize_text_field($pa['name']),
          'value' => sanitize_text_field($pa['value']),
          'is_visible' => '1',
          'is_variation' => '0'
        );
      }

      update_post_meta( $product_id, '_product_attributes', $attributes );
    }

    // Affiliate part
    update_post_meta( $product_id, '_product_url', sanitize_text_field($request['affiliate']['product_url']) );
    update_post_meta( $product_id, '_button_text', sanitize_text_field($request['affiliate']['button_text']) );
  }

  // Save product settings
  $current_time = date('Y-m-d H:i:s', current_time('timestamp', 0));

  update_post_meta( $product_id, '_pch_connected_product', sanitize_url($request['from_url']) );
  update_post_meta( $product_id, '_pch_last_product_updated', $current_time );
  update_post_meta( $product_id, '_pch_product_alias', sanitize_text_field($request['product_alias']) );
  update_post_meta( $product_id, '_pch_product_title_sync', $request['synchronization']['title'] );
  update_post_meta( $product_id, '_pch_product_description_sync', $request['synchronization']['description'] );
  update_post_meta( $product_id, '_pch_product_status_sync', $request['synchronization']['status'] );
  update_post_meta( $product_id, '_pch_product_categories_sync', $request['synchronization']['categories'] );
  update_post_meta( $product_id, '_pch_product_featured_image_sync', $request['synchronization']['featured_image'] );
  update_post_meta( $product_id, '_pch_product_galleries_sync', $request['synchronization']['galleries'] );
  update_post_meta( $product_id, '_pch_product_price_sync', $request['synchronization']['price'] );
  update_post_meta( $product_id, '_pch_product_stock_sync', $request['synchronization']['stock'] );
  update_post_meta( $product_id, '_pch_product_attributes_sync', $request['synchronization']['attributes'] );
  update_post_meta( $product_id, '_pch_product_variations_sync', $request['synchronization']['variations'] );
  update_post_meta( $product_id, '_pch_product_reviews_ratings_sync', $request['synchronization']['reviews_ratings'] );
  update_post_meta( $product_id, '_pch_shipping_cost_dropship', sanitize_text_field($request['dropship']['shipping_cost']) );
  update_post_meta( $product_id, '_pch_markup_type_dropship', sanitize_text_field($request['dropship']['markup_type']) );
  update_post_meta( $product_id, '_pch_markup_price_dropship', sanitize_text_field($request['dropship']['markup_price']) );
  update_post_meta( $product_id, '_pch_product_url_affiliate', sanitize_url($request['affiliate']['product_url']) );
  update_post_meta( $product_id, '_pch_button_text_affiliate', sanitize_text_field($request['affiliate']['button_text']) );

  $parser = array(
    'success' => ($product_id ? true : false),
    'permalink' => esc_url(get_the_permalink($product_id))
  );

  $response = new WP_REST_Response($parser);
  $response->set_status(200);

  return $response;
}

// Sync Product API
add_action('rest_api_init', 'pch_register_api_product_sync');
function pch_register_api_product_sync() {
  register_rest_route('prochains/v1', '/product/sync', array(
    'methods'  => 'POST',
    'callback' => 'pch_api_product_sync',
    // 'permission_callback' => function() {
    //   return is_user_logged_in();
    // }
  ));
}

function pch_api_product_sync($request) {
  $product = wc_get_product( $request['product_id'] );

  $type = ($request['product_alias'] === 'affiliate' ? 'external' : ($request['variation_attributes'][0]['name'] !== '' ? 'variable' : $request['product_type']));
  $product_title_sync = get_post_meta( $request['product_id'], '_pch_product_title_sync', true );
  $product_description_sync = get_post_meta( $request['product_id'], '_pch_product_description_sync', true );
  $product_status_sync = get_post_meta( $request['product_id'], '_pch_product_status_sync', true );
  $product_categories_sync = get_post_meta( $request['product_id'], '_pch_product_categories_sync', true );
  $product_featured_image_sync = get_post_meta( $request['product_id'], '_pch_product_featured_image_sync', true );
  $product_galleries_sync = get_post_meta( $request['product_id'], '_pch_product_galleries_sync', true );
  $product_price_sync = get_post_meta( $request['product_id'], '_pch_product_price_sync', true );
  $product_stock_sync = get_post_meta( $request['product_id'], '_pch_product_stock_sync', true );
  $product_attributes_sync = get_post_meta( $request['product_id'], '_pch_product_attributes_sync', true );
  $product_variations_sync = get_post_meta( $request['product_id'], '_pch_product_variations_sync', true );
  $product_reviews_ratings_sync = get_post_meta( $request['product_id'], '_pch_product_reviews_ratings_sync', true );

  $shipping_cost_dropship = get_post_meta( $request['product_id'], '_pch_shipping_cost_dropship', true );
  $markup_type_dropship = get_post_meta( $request['product_id'], '_pch_markup_type_dropship', true );
  $markup_price_dropship = get_post_meta( $request['product_id'], '_pch_markup_price_dropship', true );

  $last_product_update = date('Y-m-d H:i:s', current_time('timestamp', 0));

  // Title
  if($product_title_sync) {
    $product->set_name(sanitize_text_field($request['post_title']));
  }

  // Description
  if($product_description_sync) {
    $product->set_description($request['post_content']);
  }

  // Status
  if($product_status_sync) {
    $product->set_status(($request['post_status'] === 'publish' ? 'publish' : 'draft'));
  }

  // Categories
  if($product_categories_sync) {
    $category_ids = pch_get_category_ids($request['product_cat']);

    if(count($category_ids) > 0) {
      $product->set_category_ids($category_ids);
    }
  }

  // Featured Image
  if($product_featured_image_sync) {
    $image_id = pch_get_image_id($request['base_thumbnail_url'], $request['post_thumbnail']);

    if(!is_null($image_id)) {
      $product->set_image_id($image_id);
    }
  }

  // Gallery Image
  if($product_galleries_sync) {
    $gallery_ids = pch_get_gallery_ids($request['base_thumbnail_url'], $request['post_thumbnail'], $request['post_meta']['_product_image_gallery']);

    if(count($gallery_ids) > 0) {
      $product->set_gallery_image_ids($gallery_ids);
    }
  }

  // Price
  if($product_price_sync) {
    // Simple Product
    if($type === 'simple') {
      $regular_price = ($request['variations'][0]['price_before_discount'] === 0 ? $request['variations'][0]['price'] : $request['variations'][0]['price_before_discount']) / $request['spend_cash_unit'];
      $final_regular_price = $regular_price + $shipping_cost_dropship + ($markup_type_dropship === 'fixed' ? $markup_price_dropship : ($markup_price_dropship * $regular_price) / 100);

      $sale_price = $request['variations'][0]['price'] / $request['spend_cash_unit'];
      $final_sale_price = $sale_price + $shipping_cost_dropship + ($markup_type_dropship === 'fixed' ? $markup_price_dropship : ($markup_price_dropship * $sale_price) / 100);

      $product->set_regular_price($final_regular_price);
      $product->set_sale_price($final_sale_price);
    }

    // External Product
    if($type === 'external') {
      $regular_price = ($request['variations'][0]['price_before_discount'] === 0 ? $request['variations'][0]['price'] : $request['variations'][0]['price_before_discount']) / $request['spend_cash_unit'];
      $sale_price = $request['variations'][0]['price'] / $request['spend_cash_unit'];

      $product->set_regular_price($regular_price);
      $product->set_sale_price($sale_price);
    }
  }

  // Stock
  if($product_stock_sync) {
    // Simple Product
    if($type === 'simple') {
      // Stock Management
      update_post_meta( $request['product_id'], '_manage_stock', $request['post_meta']['_manage_stock'] );
      update_post_meta( $request['product_id'], '_stock', $request['variations'][0]['stock'] );
    }
  }

  // Attributes
  if($product_attributes_sync) {
    $attributes = array();
    $product_attributes = $request['post_meta']['_product_attributes'];

    if(!is_null($product_attributes)) {
      foreach($product_attributes as $pa) {
        $attributes[sanitize_title($pa['name'])] = array(
          'name' => sanitize_text_field($pa['name']),
          'value' => sanitize_text_field($pa['value']),
          'is_visible' => '1',
          'is_variation' => '0'
        );
      }

      update_post_meta( $request['product_id'], '_product_attributes', $attributes );
    }
  }

  // Variations
  if($product_variations_sync) {
    if($type === 'variable') {
      // Variation Attributes
      $tier_product_variation_attributes = array();
      $product_variation_attributes = $request['variation_attributes'];
  
      if(!is_null($product_variation_attributes)) {
        foreach($product_variation_attributes as $va) {
          $tier_product_variation_attributes[] = sanitize_title($va['name']);
  
          $attributes[sanitize_title($va['name'])] = array(
            'name' => sanitize_text_field($va['name']),
            'value' => implode('|', $va['options']),
            'is_visible' => '1',
            'is_variation' => '1'
          );
        }
  
        update_post_meta( $request['product_id'], '_product_attributes', $attributes );
      }
  
      // Product Variations
      $product_variations = $request['variations'];
      
      pch_delete_variations($request['product_id'], true);
  
      foreach($product_variations as $pv) {
        if($pv['status']) {
          $pv_name = explode(',', $pv['name']);
          $pv_attributes = array();
  
          for($i=0; $i<count($pv_name); $i++) {
            $pv_attributes[$tier_product_variation_attributes[$i]] = $pv_name[$i];
          }
  
          $regular_price = ($pv['price_before_discount'] === 0 ? $pv['price'] : $pv['price_before_discount']) / $request['spend_cash_unit'];
          $final_regular_price = $regular_price + $shipping_cost_dropship + ($markup_type_dropship === 'fixed' ? $markup_price_dropship : ($markup_price_dropship * $regular_price) / 100);
  
          $sale_price = $pv['price'] / $request['spend_cash_unit'];
          $final_sale_price = $sale_price + $shipping_cost_dropship + ($markup_type_dropship === 'fixed' ? $markup_price_dropship : ($markup_price_dropship * $sale_price) / 100);
  
          $variation_data =  array(
            'attributes' => $pv_attributes,
            'regular_price' => $final_regular_price,
            'sale_price' => $final_sale_price,
            'stock_qty' => $pv['stock']
          );
  
          pch_create_product_variation( $request['product_id'], $variation_data );
        }
      }
    }
  }

  update_post_meta( $request['product_id'], '_pch_last_product_updated', $last_product_update );

  $product->save();

  $parser = array(
    'success' => true
  );

  $response = new WP_REST_Response($parser);
  $response->set_status(200);

  return $response;
}