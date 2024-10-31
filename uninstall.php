<?php
/**
 * ProChains Uninstall
 *
 * @package ProChains\Uninstaller
 * @since 1.0.0
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

global $wpdb, $wp_version;

// API Statistics
$url = "https://customify.id/statistics";
  
$body = array(
  'website' => esc_url(home_url()),
  'action'  => 'Delete'
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