<?php
/**
 * @package ProChains\Functions
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

function pch_enqueue_custom_admin_script() {
  // CSS
  wp_enqueue_style('pch-global-admin-css', PCH_PLUGIN_URL . 'admin/css/global.css', array(), filemtime(PCH_PLUGIN_DIR . 'admin/css/global.css'), 'all');
  
  // JS
  wp_enqueue_script('pch-global-admin-js', PCH_PLUGIN_URL . 'admin/js/global.js', array('jquery'), filemtime(PCH_PLUGIN_DIR . 'admin/js/global.js'), true);
}
add_action( 'admin_enqueue_scripts', 'pch_enqueue_custom_admin_script' );

// Admin Menus
add_action( 'admin_menu', 'pch_admin_menu' );
function pch_admin_menu() {
  $welcome_menu = add_menu_page(
    __('Welcome to ProChains', 'prochains'),
    __('ProChains', 'prochains'),
    'manage_options',
    'pch-welcome',
    'pch_welcome_view',
    'dashicons-admin-links',
    56
  );

  $crawler_submenu = add_submenu_page(
    'pch-welcome',
    __('Fetching Bulk Products', 'prochains'),
    __('Fetching Bulk Products', 'prochains'),
    'read',
    'pch-crawler',
    'pch_crawler_view'
  );

  add_action('admin_print_styles-' . $welcome_menu, 'pch_welcome_styles');
  add_action('admin_print_styles-' . $crawler_submenu, 'pch_crawler_styles');

  add_action('admin_print_scripts-' . $welcome_menu, 'pch_welcome_scripts');
  add_action('admin_print_scripts-' . $crawler_submenu, 'pch_crawler_scripts');
}

function pch_welcome_view() {
  require_once(PCH_PLUGIN_DIR . 'admin/views/class-pch-welcome.php');

  $pch_welcome = new PCH_Welcome();

  $pch_welcome->render_page();
}

function pch_crawler_view() {
  require_once(PCH_PLUGIN_DIR . 'admin/class-pch-auth.php');
  require_once(PCH_PLUGIN_DIR . 'admin/views/class-pch-crawler.php');

  $pch_auth = new PCH_Auth();
  $pch_crawler = new PCH_Crawler();

  // delete_option('_auth_prochains4wp');
  if(get_option('_auth_prochains4wp') === false) {
    $token = $pch_auth->generate_token();

    add_option('_auth_prochains4wp', $token);
  } else {
    if($pch_auth->check_token() === false) {
      $new_token = $pch_auth->generate_token();

      update_option('_auth_prochains4wp', $new_token);
    }
  }

  $pch_crawler->render_page();
}

function pch_welcome_styles() {

}

function pch_crawler_styles() {
  
}

function pch_welcome_scripts() {
  
}

function pch_crawler_scripts() {
  wp_enqueue_script('pch-crawler-js', PCH_PLUGIN_URL . 'admin/js/crawler.js', array('jquery'), filemtime(PCH_PLUGIN_DIR . 'admin/js/crawler.js'), true);

  wp_localize_script(
    'pch-crawler-js',
    'ajaxObj',
    array(
      'ajax_url' => admin_url('admin-ajax.php'),
      'nonce' => wp_create_nonce('prochains_ajax'),
      'api_endpoint_web_create_product' => esc_url(PCH_API_ENDPOINT_CREATE_PRODUCT),
      'api_endpoint_app_get_product_shopee' => esc_url(PCH_API_ENDPOINT_BASE_URL . PCH_API_ENDPOINT_GET_PRODUCT_SHOPEE),
      'api_endpoint_app_check_status_shopee' => esc_url(PCH_API_ENDPOINT_BASE_URL . PCH_API_ENDPOINT_CHECK_STATUS_SHOPEE),
      'api_endpoint_app_get_product_tokopedia' => esc_url(PCH_API_ENDPOINT_BASE_URL . PCH_API_ENDPOINT_GET_PRODUCT_TOKOPEDIA),
      'api_endpoint_app_check_status_tokopedia' => esc_url(PCH_API_ENDPOINT_BASE_URL . PCH_API_ENDPOINT_CHECK_STATUS_TOKOPEDIA),
      'api_endpoint_app_get_product_bukalapak' => esc_url(PCH_API_ENDPOINT_BASE_URL . PCH_API_ENDPOINT_GET_PRODUCT_BUKALAPAK),
      'api_endpoint_app_check_status_bukalapak' => esc_url(PCH_API_ENDPOINT_BASE_URL . PCH_API_ENDPOINT_CHECK_STATUS_BUKALAPAK),
      'api_token' => esc_html(get_option('_auth_prochains4wp', ''))
    )
  );
}