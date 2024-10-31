<?php
/**
 * @package ProChains\Functions
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

// Admin Notices
add_action( 'admin_notices', 'pch_admin_notice' );
function pch_admin_notice(){
  if( get_transient( 'pch-an-woocommerce-is-required' ) ) {
    ?>
    <div class='error notice is-dismissible'>
    <p><?php echo __("Sorry, it looks like you haven't installed the WooCommerce plugin.", "prochains"); ?></p>
    </div>

    <?php
    delete_transient( 'pch-an-woocommerce-is-required' );
  }
}