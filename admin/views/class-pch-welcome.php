<?php
/**
 * @package ProChains\Views
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

class PCH_Welcome {
  public static function render_page() {
    ?>
    <div class="wrap">
      <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
      <p class="pch-mt-0"><?php echo __('An easy way to connect products from multiple marketplaces to your WooCommerce.', 'prochains'); ?></p>

      <hr>

      <img class="pch-width-full" src="<?php echo esc_url(PCH_PLUGIN_URL . 'admin/img/banner-prochains.png'); ?>" alt="prochains" />
    </div>
    <?php
  }
}