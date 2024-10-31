<?php
/**
 * @package ProChains\Views
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

class PCH_Crawler {
  public static function render_page() {
    ?>
    <div class="wrap">
      <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
      <p class="pch-mt-0"><?php echo __('You can get products and sell them on your WooCommerce from marketplaces just from the url.', 'prochains'); ?></p>

      <hr>
      
      <form id="pch-run-crawler" action="#">
        <h2><?php echo __('Extract Products', 'prochains'); ?></h2>
        <p class="pch-mt-0"><?php echo __('Enter product urls from marketplaces, ProChains will automatically extract product data.', 'prochains'); ?></p>

        <ul class="pch-list-disc">
          <li><?php echo __('Separate multiple product urls with line breaks.', 'prochains'); ?></li>
          <li><?php echo __('Maximum 3 urls to execute per session.', 'prochains'); ?></li>
          <li><?php echo __('Duplicate urls will be removed automatically.', 'prochains'); ?></li>
        </ul>

        <textarea id="pch-product-urls" name="product_urls" class="large-text code" rows="5" required></textarea>
        <ul class="pch-inline-list pch-mt-5">
          <li id="pch-status-shopee" title="Inactive"><label><span class="dashicons dashicons-yes-alt"></span>&nbsp;<?php echo __('Shopee', 'prochains'); ?></label></li>
          <li id="pch-status-tokopedia" title="Inactive"><label><span class="dashicons dashicons-yes-alt"></span>&nbsp;<?php echo __('Tokopedia', 'prochains'); ?></label></li>
          <li id="pch-status-bukalapak" title="Inactive"><label><span class="dashicons dashicons-yes-alt"></span>&nbsp;<?php echo __('Bukalapak', 'prochains'); ?></label></li>
          <li id="pch-status-lazada" title="Inactive"><label><span class="dashicons dashicons-yes-alt"></span>&nbsp;<?php echo __('Lazada', 'prochains'); ?></label></li>
          <li id="pch-status-lazada" title="Inactive"><label><span class="dashicons dashicons-yes-alt"></span>&nbsp;<?php echo __('AliExpress', 'prochains'); ?></label></li>
        </ul>
        <ul class="pch-inline-list pch-mt-0">
          <li><input type="submit" id="runCrawler" class="button button-primary" value="<?php echo __('Run MagicTool!', 'prochains'); ?>"></li>
        </ul>
      </form>

      <hr>

      <div class="tablenav top">
        <div class="alignleft actions bulkactions">
          <form id="pch-bulk-actions" action="#">
            <label for="pch-bulk-action-options" class="screen-reader-text"><?php echo __('Select bulk action', 'prochains'); ?></label>
            <select name="action" id="pch-bulk-action-options" required>
              <option value=""><?php echo __('Bulk actions', 'prochains'); ?></option>
              <option value="import"><?php echo __('Import', 'prochains'); ?></option>
              <option value="delete"><?php echo __('Delete', 'prochains'); ?></option>
            </select>
            <input type="submit" id="bulkActions" class="button action" value="<?php echo __('Apply', 'prochains'); ?>">
          </form>
		    </div>
	    </div>

      <table id="pch-product-list" class="widefat pch-table">
        <thead>
          <tr>
            <th width="20">
              <input id="pch-select-all-products" type="checkbox">
            </th>
            <th colspan="2"><?php echo __('Product', 'prochains'); ?></th>
            <th><?php echo __('Price (' . get_woocommerce_currency_symbol() . ')', 'prochains'); ?></th>
            <th><?php echo __('Actions', 'prochains'); ?></th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <th colspan="5"><?php echo __("You've never run the magic tool ProChains.", 'prochains'); ?></th>
          </tr>
        </tbody>
      </table>

      <br>

      <h2><?php echo __('All products that failed to extract', 'prochains'); ?>&nbsp;<span id="pch-total-errors">(0)</span></h2>
      <ul id="pch-list-error" class="pch-list-disc"></ul>
    </div>
    <?php
  }
}