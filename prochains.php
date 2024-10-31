<?php
/**
 * Plugin Name: ProChains
 * Plugin URI: https://customify.id/wordpress/plugins/prochains/
 * Description: An easy way to connect products from multiple marketplaces to your WooCommerce.
 * Version: 2.6.1
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Author: Artareja Media
 * Author URI: https://customify.id/
 * Text Domain: prochains
 * 
 * @package ProChains
 */

/*
ProChains is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
ProChains is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with ProChains. If not, see https://www.gnu.org/licenses/gpl-3.0.txt.
*/

// Constant
defined('PCH_PLUGIN_DIR') or define('PCH_PLUGIN_DIR', plugin_dir_path( __FILE__ ));
defined('PCH_PLUGIN_URL') or define('PCH_PLUGIN_URL', plugin_dir_url( __FILE__ ));

defined('PCH_API_ENDPOINT_CREATE_PRODUCT') or define('PCH_API_ENDPOINT_CREATE_PRODUCT', get_site_url() . '/wp-json/prochains/v1/product/create');
defined('PCH_API_ENDPOINT_PRODUCT_SYNC') or define('PCH_API_ENDPOINT_PRODUCT_SYNC', get_site_url() . '/wp-json/prochains/v1/product/sync');

defined('PCH_API_ENDPOINT_GET_PRODUCT_SHOPEE') or define('PCH_API_ENDPOINT_GET_PRODUCT_SHOPEE', '/shopee/product');
defined('PCH_API_ENDPOINT_CHECK_STATUS_SHOPEE') or define('PCH_API_ENDPOINT_CHECK_STATUS_SHOPEE', '/shopee/status');

defined('PCH_API_ENDPOINT_GET_PRODUCT_TOKOPEDIA') or define('PCH_API_ENDPOINT_GET_PRODUCT_TOKOPEDIA', '/tokopedia/product');
defined('PCH_API_ENDPOINT_CHECK_STATUS_TOKOPEDIA') or define('PCH_API_ENDPOINT_CHECK_STATUS_TOKOPEDIA', '/tokopedia/status');

defined('PCH_API_ENDPOINT_GET_PRODUCT_BUKALAPAK') or define('PCH_API_ENDPOINT_GET_PRODUCT_BUKALAPAK', '/bukalapak/product');
defined('PCH_API_ENDPOINT_CHECK_STATUS_BUKALAPAK') or define('PCH_API_ENDPOINT_CHECK_STATUS_BUKALAPAK', '/bukalapak/status');

// Requirements
require_once(ABSPATH . 'wp-admin/includes/media.php');
require_once(ABSPATH . 'wp-admin/includes/file.php');
require_once(ABSPATH . 'wp-admin/includes/image.php');

require_once( PCH_PLUGIN_DIR . 'includes/pch-cron-functions.php');
require_once( PCH_PLUGIN_DIR . 'includes/pch-admin-notice-functions.php');
require_once( PCH_PLUGIN_DIR . 'includes/pch-admin-menu-functions.php');
require_once( PCH_PLUGIN_DIR . 'includes/pch-metabox-functions.php');
require_once( PCH_PLUGIN_DIR . 'includes/pch-woocommerce-functions.php');
require_once( PCH_PLUGIN_DIR . 'includes/pch-api-functions.php');
require_once( PCH_PLUGIN_DIR . 'includes/pch-ajax-functions.php');

// Check requirements
function pch_check_requirements() {
  if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    $plugins = array(
      'prochains/prochains.php'
    );

    require_once(ABSPATH . 'wp-admin/includes/plugin.php');
    deactivate_plugins($plugins);
    
    set_transient( 'pch-an-woocommerce-is-required', true, 5 );
  }
}
add_action( 'admin_init', 'pch_check_requirements' );

// Get base url
function pch_get_api_base_url() {
  $url = "https://customify.id/index.php";

  $headers = array(
    'Accept: application/json',
    'Content-type: application/json'
  );

  $curl = curl_init();

  curl_setopt($curl, CURLOPT_URL, $url);
  curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
  curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

  $response = json_decode(curl_exec($curl));

  curl_close($curl);

  defined('PCH_API_ENDPOINT_BASE_URL') or define('PCH_API_ENDPOINT_BASE_URL', $response->base_url);
}
add_action( 'admin_init', 'pch_get_api_base_url' );

/**
* Activate the plugin.
*/
function pch_activate() { 
  // API Statistics
  $url = "https://customify.id/statistics";
  
  $body = array(
    'website' => esc_url(home_url()),
    'action'  => 'Activate'
  );

  $headers = array(
    'Accept: application/json',
    'Content-type: application/json'
  );

  $curl = curl_init();

  curl_setopt($curl, CURLOPT_URL, $url);
  curl_setopt($curl, CURLOPT_POST, true);
  curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($body));

  $response = json_decode(curl_exec($curl));

  curl_close($curl);
}
register_activation_hook( __FILE__, 'pch_activate' );

/**
 * Deactivation hook.
 */
function pch_deactivate() {
  // API Statistics
  $url = "https://customify.id/statistics";
  
  $body = array(
    'website' => esc_url(home_url()),
    'action'  => 'Deactivate'
  );

  $headers = array(
    'Accept: application/json',
    'Content-type: application/json'
  );

  $curl = curl_init();

  curl_setopt($curl, CURLOPT_URL, $url);
  curl_setopt($curl, CURLOPT_POST, true);
  curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($body));

  $response = json_decode(curl_exec($curl));

  curl_close($curl);
}
register_deactivation_hook( __FILE__, 'pch_deactivate' );