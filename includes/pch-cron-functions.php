<?php
/**
 * @package ProChains\Functions
 * @since 1.1.0
 */

defined( 'ABSPATH' ) || exit;

// Add interval
add_filter( 'cron_schedules', 'pch_add_every_thirty_minutes' );
function pch_add_every_thirty_minutes( $schedules ) {
  $schedules['every_thirty_minutes'] = array(
    'interval'  => 1800,
    'display'   => __( 'Every 30 Minutes', 'prochains' )
  );
  
  return $schedules;
}

if(!wp_next_scheduled('pch_add_every_thirty_minutes')) {
  wp_schedule_event( time(), 'every_thirty_minutes', 'pch_add_every_thirty_minutes' );
}

add_action( 'pch_add_every_thirty_minutes', 'pch_cron_product_sync' );
function pch_cron_product_sync() {
  $current_time = date('Y-m-d H:i:s', current_time('timestamp', 0));

  $one_last_day = date('Y-m-d H:i:s', strtotime($current_time . ' -1 day'));
  $args = array(
    'post_type'       => 'product',
    'post_status'     => 'publish',
    'posts_per_page'  => 3, // Max: 3
    'orderby'         => 'meta_value_num',
    'order'           => 'DESC',
    'meta_key'        => 'total_sales',
    'meta_query'      => array(
      array(
        'key'     => '_pch_last_product_updated',
        'compare' => '<=',
        'value'   => $one_last_day,
        'type'    => 'DATETIME'
      )
    ),
    'fields'          => 'ids'
  );

  $products = new WP_Query($args);

  if($products->have_posts()) {
    while($products->have_posts()) : $products->the_post();
      $connected_product = get_post_meta(get_the_ID(), '_pch_connected_product', true);
      $last_product_update = get_post_meta(get_the_ID(), '_pch_last_product_updated', true);
      
      if($connected_product) {
        if(!$last_product_update) {
          pch_run_product_sync(get_the_ID(), $connected_product);
        } else {
          $day_diff = floor((abs(strtotime($current_time) - strtotime($last_product_update))) / (60 * 60 * 24));
    
          if($day_diff >= 1) {
            pch_run_product_sync(get_the_ID(), $connected_product);
          }
        }
      }
    endwhile;
  }

  wp_reset_query();
}

function pch_run_product_sync($product_id, $connected_product) {
  $api_endpoint_get_product = "";
  $token = "";

  // Token
  require_once(PCH_PLUGIN_DIR . 'admin/class-pch-auth.php');

  $pch_auth = new PCH_Auth();

  // delete_option('_auth_prochains4wp');
  if(get_option('_auth_prochains4wp') === false) {
    $token = $pch_auth->generate_token();

    add_option('_auth_prochains4wp', $token);
  } else {
    if($pch_auth->check_token() === false) {
      $token = $pch_auth->generate_token();

      update_option('_auth_prochains4wp', $token);
    }
  }

  // URL
  $_url = "https://customify.id/index.php";

  $_headers = array(
    'Accept: application/json',
    'Content-type: application/json'
  );

  $_curl = curl_init();

  curl_setopt($_curl, CURLOPT_URL, $_url);
  curl_setopt($_curl, CURLOPT_CUSTOMREQUEST, 'GET');
  curl_setopt($_curl, CURLOPT_HTTPHEADER, $_headers);
  curl_setopt($_curl, CURLOPT_RETURNTRANSFER, true);

  $_response = json_decode(curl_exec($_curl));

  curl_close($_curl);

  if (str_contains($connected_product, 'shopee')) { 
    $api_endpoint_get_product = esc_url($_response->base_url . PCH_API_ENDPOINT_GET_PRODUCT_SHOPEE);
  } else if (str_contains($connected_product, 'tokopedia')) { 
    $api_endpoint_get_product = esc_url($_response->base_url . PCH_API_ENDPOINT_GET_PRODUCT_TOKOPEDIA);
  } else if (str_contains($connected_product, 'bukalapak')) { 
    $api_endpoint_get_product = esc_url($_response->base_url . PCH_API_ENDPOINT_GET_PRODUCT_BUKALAPAK);
  }

  $curl = curl_init($api_endpoint_get_product);
  curl_setopt($curl, CURLOPT_URL, $api_endpoint_get_product);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

  $headers = array(
    "Origin: " . home_url(),
    "Content-Type: application/json"
  );

  $data = '{"url": "' . $connected_product . '", "token": "' . $token . '"}';

  curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

  $response_get_product = curl_exec($curl);
  curl_close($curl);

  $product_data = json_decode($response_get_product);

  if($product_data->post_title !== null) {
    $product_data->product_id = $product_id;
    $api_endpoint_product_sync = esc_url(PCH_API_ENDPOINT_PRODUCT_SYNC);

    $curl = curl_init($api_endpoint_product_sync);
    curl_setopt($curl, CURLOPT_URL, $api_endpoint_product_sync);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $headers = array(
      "Origin: " . home_url(),
      "Content-Type: application/json"
    );

    $data = json_encode($product_data);

    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

    $response_product_sync = curl_exec($curl);
    curl_close($curl);
  }
}