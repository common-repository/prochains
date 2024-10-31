<?php
/**
 * @package ProChains\Functions
 * @since 1.1.0
 */

defined( 'ABSPATH' ) || exit;

// Custom Meta Box
function pch_add_product_settings_metabox() {
  $screens = ['product'];

  foreach($screens as $screen) {
    add_meta_box(
      'pch_product_settings_metabox',
      __('ProChains: Product settings', 'prochains'),
      'pch_html_product_settings_metabox',
      $screen
    );
  }
}
add_action( 'add_meta_boxes', 'pch_add_product_settings_metabox' );

function pch_html_product_settings_metabox( $product ) {
  require_once(PCH_PLUGIN_DIR . 'admin/views/class-pch-metabox.php');

  $pch_metabox = new PCH_Metabox();

  $pch_metabox->render_product_settings($product);
}

function pch_save_postdata($post_id) {
  if(array_key_exists('pch-product-alias', $_POST)) {
    update_post_meta(
      $post_id,
      '_pch_product_alias',
      sanitize_text_field($_POST['pch-product-alias'])
    );
  }

  if(array_key_exists('pch-auto-sync', $_POST)) {
    $auto_sync = sanitize_text_field($_POST['pch-auto-sync']);

    $auto_sync = str_replace('{', '', $auto_sync);
    $auto_sync = str_replace('}', '', $auto_sync);
    $auto_sync = str_replace('"', '', $auto_sync);

    $autoSync = explode(',', $auto_sync);

    foreach($autoSync as $i => $v) {
      if($v) {
        $autoSync2 = explode(':', $v);

        update_post_meta(
          $post_id,
          '_' . $autoSync2[0],
          sanitize_text_field($autoSync2[1])
        );
      }
    }
  }

  if(array_key_exists('pch-shipping-cost-dropship', $_POST)) {
    update_post_meta(
      $post_id,
      '_pch_shipping_cost_dropship',
      sanitize_text_field($_POST['pch-shipping-cost-dropship'])
    );
  }

  if(array_key_exists('pch-markup-type-dropship', $_POST)) {
    update_post_meta(
      $post_id,
      '_pch_markup_type_dropship',
      sanitize_text_field($_POST['pch-markup-type-dropship'])
    );
  }

  if(array_key_exists('pch-markup-price-dropship', $_POST)) {
    update_post_meta(
      $post_id,
      '_pch_markup_price_dropship',
      sanitize_text_field($_POST['pch-markup-price-dropship'])
    );
  }

  if(array_key_exists('pch-product-url-affiliate', $_POST)) {
    update_post_meta(
      $post_id,
      '_pch_product_url_affiliate',
      sanitize_url($_POST['pch-product-url-affiliate'])
    );
  }

  if(array_key_exists('pch-product-button-text-affiliate', $_POST)) {
    update_post_meta(
      $post_id,
      '_pch_button_text_affiliate',
      sanitize_text_field($_POST['pch-product-button-text-affiliate'])
    );
  }

  if(array_key_exists('pch-remove-url-value', $_POST)) {
    if($_POST['pch-remove-url-value']) {
      delete_post_meta( $post_id, '_pch_connected_product' );
      delete_post_meta( $post_id, '_pch_last_product_updated' );
      delete_post_meta( $post_id, '_pch_product_alias' );
      delete_post_meta( $post_id, '_pch_product_title_sync' );
      delete_post_meta( $post_id, '_pch_product_description_sync' );
      delete_post_meta( $post_id, '_pch_product_status_sync' );
      delete_post_meta( $post_id, '_pch_product_categories_sync' );
      delete_post_meta( $post_id, '_pch_product_featured_image_sync' );
      delete_post_meta( $post_id, '_pch_product_galleries_sync' );
      delete_post_meta( $post_id, '_pch_product_price_sync' );
      delete_post_meta( $post_id, '_pch_product_stock_sync' );
      delete_post_meta( $post_id, '_pch_product_attributes_sync' );
      delete_post_meta( $post_id, '_pch_product_variations_sync' );
      delete_post_meta( $post_id, '_pch_product_reviews_ratings_sync' );
      delete_post_meta( $post_id, '_pch_shipping_cost_dropship' );
      delete_post_meta( $post_id, '_pch_markup_type_dropship' );
      delete_post_meta( $post_id, '_pch_markup_price_dropship' );
      delete_post_meta( $post_id, '_pch_product_url_affiliate' );
      delete_post_meta( $post_id, '_pch_button_text_affiliate' );
    }
  }

  if(array_key_exists('pch-connnected-product', $_POST)) {
    update_post_meta(
      $post_id,
      '_pch_connected_product',
      sanitize_url($_POST['pch-connnected-product'])
    );

    update_post_meta( $post_id, '_pch_product_alias', 'dropship' );
    update_post_meta( $post_id, '_pch_product_title_sync', true );
    update_post_meta( $post_id, '_pch_product_description_sync', true );
    update_post_meta( $post_id, '_pch_product_status_sync', true );
    update_post_meta( $post_id, '_pch_product_categories_sync', true );
    update_post_meta( $post_id, '_pch_product_featured_image_sync', true );
    update_post_meta( $post_id, '_pch_product_galleries_sync', true );
    update_post_meta( $post_id, '_pch_product_price_sync', true );
    update_post_meta( $post_id, '_pch_product_stock_sync', true );
    update_post_meta( $post_id, '_pch_product_attributes_sync', true );
    update_post_meta( $post_id, '_pch_product_variations_sync', true );
    update_post_meta( $post_id, '_pch_product_reviews_ratings_sync', true );
    update_post_meta( $post_id, '_pch_shipping_cost_dropship', 0 );
    update_post_meta( $post_id, '_pch_markup_type_dropship', 'percentage' );
    update_post_meta( $post_id, '_pch_markup_price_dropship', 0 );
    update_post_meta( $post_id, '_pch_product_url_affiliate', sanitize_url($_POST['pch-connnected-product']) );
    update_post_meta( $post_id, '_pch_button_text_affiliate', 'Buy Now' );
  }
}
add_action( 'save_post', 'pch_save_postdata' );