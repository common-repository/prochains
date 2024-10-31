<?php
/**
 * @package ProChains\Views
 * @since 1.1.0
 */

defined( 'ABSPATH' ) || exit;

class PCH_Metabox {
  public static function render_product_settings($product) {
    $index = $product->ID;
    $current_product = wc_get_product($index);

    $connected_product = get_post_meta( $product->ID, '_pch_connected_product', true );
    
    $product_alias = get_post_meta( $product->ID, '_pch_product_alias', true );

    $product_title_sync = get_post_meta( $product->ID, '_pch_product_title_sync', true );
    $product_description_sync = get_post_meta( $product->ID, '_pch_product_description_sync', true );
    $product_status_sync = get_post_meta( $product->ID, '_pch_product_status_sync', true );
    $product_categories_sync = get_post_meta( $product->ID, '_pch_product_categories_sync', true );
    $product_featured_image_sync = get_post_meta( $product->ID, '_pch_product_featured_image_sync', true );
    $product_galleries_sync = get_post_meta( $product->ID, '_pch_product_galleries_sync', true );
    $product_price_sync = get_post_meta( $product->ID, '_pch_product_price_sync', true );
    $product_stock_sync = get_post_meta( $product->ID, '_pch_product_stock_sync', true );
    $product_attributes_sync = get_post_meta( $product->ID, '_pch_product_attributes_sync', true );
    $product_variations_sync = get_post_meta( $product->ID, '_pch_product_variations_sync', true );
    $product_reviews_ratings_sync = get_post_meta( $product->ID, '_pch_product_reviews_ratings_sync', true );
    
    $shipping_cost_dropship = get_post_meta( $product->ID, '_pch_shipping_cost_dropship', true );
    $markup_type_dropship = get_post_meta( $product->ID, '_pch_markup_type_dropship', true );
    $markup_price_dropship = get_post_meta( $product->ID, '_pch_markup_price_dropship', true );
    
    $product_url_affiliate = get_post_meta( $product->ID, '_pch_product_url_affiliate', true );
    $button_text_affiliate = get_post_meta( $product->ID, '_pch_button_text_affiliate', true );
    
    $last_product_updated = get_post_meta( $product->ID, '_pch_last_product_updated', true );
    ?>
    <?php if($connected_product) { ?>
    <div class="theme-browser rendered">
      <div class="themes wp-clearfix">
        <div class="theme">
          <table class="form-table" role="presentation">
            <tbody>
              <!-- <tr>
                <th style="padding: 0;"><label for="pch-product-alias<?php // echo esc_html($index); ?>"><?php // echo __('Product will be set to', 'prochains'); ?></label></th>
              </tr>
              <tr>
                <td style="padding: 15px 0;">
                  <select name="pch-product-alias" id="pch-product-alias<?php // echo esc_html($index); ?>" class="postform">
                    <option value="dropship" <?php // echo ($current_product->is_type('external') === false ? 'selected="selected"' : ''); ?>><?php // echo __('Dropship', 'prochains'); ?></option>
                    <option value="affiliate" <?php // echo ($current_product->is_type('external') === true ? 'selected="selected"' : ''); ?>><?php // echo __('Affiliate', 'prochains'); ?></option>
                  </select>
                  &nbsp;<?php // echo __('product', 'prochains'); ?>
                </td>
              </tr> -->
              <tr>
                <th style="padding: 0;"><label for="pch-product-sync<?php echo esc_html($index); ?>"><?php echo __('Auto synchronization', 'prochains'); ?></label></th>
              </tr>
              <tr>
                <td style="padding: 15px 0;">
                  <input type="hidden" name="pch-auto-sync" id="pch-auto-sync" value="">
                  <div class="pch-wrap-list-items">
                    <fieldset>
                      <label for="pch-product-title-sync<?php echo esc_html($index); ?>">
                        <input style="margin-left: 0;" name="pch-product-title-sync" class="pch-checkbox-boolean" type="checkbox" id="pch-product-title-sync<?php echo esc_html($index); ?>" value="1" <?php echo ($product_title_sync ? 'checked="checked"' : ''); ?>>
                        &nbsp;<?php echo __('Title', 'prochains'); ?>
                      </label>
                    </fieldset>
                    <fieldset>
                      <label for="pch-product-description-sync<?php echo esc_html($index); ?>">
                        <input style="margin-left: 0;" name="pch-product-description-sync" class="pch-checkbox-boolean" type="checkbox" id="pch-product-description-sync<?php echo esc_html($index); ?>" value="1" <?php echo ($product_description_sync ? 'checked="checked"' : ''); ?>>
                        &nbsp;<?php echo __('Description', 'prochains'); ?>
                      </label>
                    </fieldset>
                    <fieldset>
                      <label for="pch-product-status-sync<?php echo esc_html($index); ?>">
                        <input style="margin-left: 0;" name="pch-product-status-sync" class="pch-checkbox-boolean" type="checkbox" id="pch-product-status-sync<?php echo esc_html($index); ?>" value="1" <?php echo ($product_status_sync ? 'checked="checked"' : ''); ?>>
                        &nbsp;<?php echo __('Status', 'prochains'); ?>
                      </label>
                    </fieldset>
                    <fieldset>
                      <label for="pch-product-categories-sync<?php echo esc_html($index); ?>">
                        <input style="margin-left: 0;" name="pch-product-categories-sync" class="pch-checkbox-boolean" type="checkbox" id="pch-product-categories-sync<?php echo esc_html($index); ?>" value="1" <?php echo ($product_categories_sync ? 'checked="checked"' : ''); ?>>
                        &nbsp;<?php echo __('Categories', 'prochains'); ?>
                      </label>
                    </fieldset>
                    <fieldset>
                      <label for="pch-product-featured-image-sync<?php echo esc_html($index); ?>">
                        <input style="margin-left: 0;" name="pch-product-featured-image-sync" class="pch-checkbox-boolean" type="checkbox" id="pch-product-featured-image-sync<?php echo esc_html($index); ?>" value="1" <?php echo ($product_featured_image_sync ? 'checked="checked"' : ''); ?>>
                        &nbsp;<?php echo __('Featured Image', 'prochains'); ?>
                      </label>
                    </fieldset>
                    <fieldset>
                      <label for="pch-product-galleries-sync<?php echo esc_html($index); ?>">
                        <input style="margin-left: 0;" name="pch-product-galleries-sync" class="pch-checkbox-boolean" type="checkbox" id="pch-product-galleries-sync<?php echo esc_html($index); ?>" value="1" <?php echo ($product_galleries_sync ? 'checked="checked"' : ''); ?>>
                        &nbsp;<?php echo __('Galleries', 'prochains'); ?>
                      </label>
                    </fieldset>
                    <fieldset>
                      <label for="pch-product-price-sync<?php echo esc_html($index); ?>">
                        <input style="margin-left: 0;" name="pch-product-price-sync" class="pch-checkbox-boolean" type="checkbox" id="pch-product-price-sync<?php echo esc_html($index); ?>" value="1" <?php echo ($product_price_sync ? 'checked="checked"' : ''); ?>>
                        &nbsp;<?php echo __('Prices', 'prochains'); ?>
                      </label>
                    </fieldset>
                    <fieldset>
                      <label for="pch-product-stock-sync<?php echo esc_html($index); ?>">
                        <input style="margin-left: 0;" name="pch-product-stock-sync" class="pch-checkbox-boolean" type="checkbox" id="pch-product-stock-sync<?php echo esc_html($index); ?>" value="1" <?php echo ($product_stock_sync ? 'checked="checked"' : ''); ?>>
                        &nbsp;<?php echo __('Stock', 'prochains'); ?>
                      </label>
                    </fieldset>
                    <fieldset>
                      <label for="pch-product-attributes-sync<?php echo esc_html($index); ?>">
                        <input style="margin-left: 0;" name="pch-product-attributes-sync" class="pch-checkbox-boolean" type="checkbox" id="pch-product-attributes-sync<?php echo esc_html($index); ?>" value="1" <?php echo ($product_attributes_sync ? 'checked="checked"' : ''); ?>>
                        &nbsp;<?php echo __('Attributes', 'prochains'); ?>
                      </label>
                    </fieldset>
                    <fieldset>
                      <label for="pch-product-variations-sync<?php echo esc_html($index); ?>">
                        <input style="margin-left: 0;" name="pch-product-variations-sync" class="pch-checkbox-boolean" type="checkbox" id="pch-product-variations-sync<?php echo esc_html($index); ?>" value="1" <?php echo ($product_variations_sync ? 'checked="checked"' : ''); ?>>
                        &nbsp;<?php echo __('Variations', 'prochains'); ?>
                      </label>
                    </fieldset>
                    <fieldset>
                      <label for="pch-product-review-rating-sync<?php echo esc_html($index); ?>">
                        <input style="margin-left: 0;" name="pch-product-review-rating-sync" class="pch-checkbox-boolean" type="checkbox" id="pch-product-review-rating-sync<?php echo esc_html($index); ?>" value="1" disabled>
                        &nbsp;<?php echo __('Reviews & Ratings (Coming Soon)', 'prochains'); ?>
                      </label>
                    </fieldset>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <?php if($current_product->is_type('external') === false) { ?>
        <div class="theme">
          <h3 style="color: #2e2e2e; margin: 10px 0;"><?php echo __('Dropshipping fields', 'prochains'); ?></h3>
          <table class="form-table" role="presentation">
            <tbody>
              <tr>
                <th style="padding: 0;"><label for="pch-shipping-cost-dropship<?php echo esc_html($index); ?>"><?php echo __('Shipping cost (' . get_woocommerce_currency_symbol() .  ')', 'prochains'); ?></label></th>
              </tr>
              <tr>
                <td style="padding: 15px 0;">
                  <input style="margin-left: 0; padding: 0 10px;" type="number" min="0" step="1" name="pch-shipping-cost-dropship" id="pch-shipping-cost-dropship<?php echo esc_html($index); ?>" value="<?php echo esc_html($shipping_cost_dropship); ?>" class="regular-text code">
                </td>
              </tr>
              <tr>
                <th style="padding: 0;"><label for="pch-markup-type-dropship<?php echo esc_html($index); ?>"><?php echo __('Markup type', 'prochains'); ?></label></th>
              </tr>
              <tr>
                <td style="padding: 15px 0;">
                  <select name="pch-markup-type-dropship" id="pch-markup-type-dropship<?php echo esc_html($index); ?>" class="postform">
                    <option value="percentage" <?php echo ($markup_type_dropship === 'percentage' ? 'selected="selected"' : ''); ?>><?php echo __('Percentage', 'prochains'); ?></option>
                    <option value="fixed" <?php echo ($markup_type_dropship === 'fixed' ? 'selected="selected"' : ''); ?>><?php echo __('Fixed', 'prochains'); ?></option>
                  </select>
                </td>
              </tr>
              <tr>
                <th style="padding: 0;"><label for="pch-markup-price-dropship<?php echo esc_html($index); ?>"><?php echo __('Markup value', 'prochains'); ?></label></th>
              </tr>
              <tr>
                <td style="padding: 15px 0;">
                  <input style="margin-left: 0; padding: 0 10px;" type="number" min="0" step="1" name="pch-markup-price-dropship" id="pch-markup-price-dropship<?php echo esc_html($index); ?>" value="<?php echo esc_html($markup_price_dropship); ?>" class="regular-text code">
                </td>
              </tr>
              <tr>
                <td style="padding: 0 0 15px 0;">
                  <small><em><?php echo __('Selling price = Product price + Shipping cost + Markup value', 'prochains'); ?></em></small>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        
        <?php } else { ?>

        <div class="theme">
          <h3 style="color: #2e2e2e; margin: 10px 0;"><?php echo __('Affiliate fields', 'prochains'); ?></h3>
          <table class="form-table" role="presentation">
            <tbody>
              <tr>
                <th style="padding: 0;"><label for="pch-product-url-affiliate<?php echo esc_html($index); ?>"><?php echo __('Product URL', 'prochains'); ?></label></th>
              </tr>
              <tr>
                <td style="padding: 15px 0;">
                  <input style="margin-left: 0; padding: 0 10px;" type="url" name="pch-product-url-affiliate" id="pch-product-url-affiliate<?php echo esc_html($index); ?>" value="<?php echo esc_url($product_url_affiliate); ?>" class="regular-text code">
                </td>
              </tr>
              <tr>
                <th style="padding: 0;"><label for="pch-product-button-text-affiliate<?php echo esc_html($index); ?>"><?php echo __('Button text', 'prochains'); ?></label></th>
              </tr>
              <tr>
                <td style="padding: 15px 0;">
                  <input style="margin-left: 0; padding: 0 10px;" type="text" name="pch-product-button-text-affiliate" id="pch-product-button-text-affiliate<?php echo esc_html($index); ?>" value="<?php echo esc_html($button_text_affiliate); ?>" class="regular-text code">
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <?php } ?>

        <div class="theme">
          <table class="form-table" role="presentation">
            <tbody>
              <tr>
                <th style="padding: 0;"><label for="pch-remove-url"><?php echo __('Product link', 'prochains'); ?></label></th>
              </tr>
              <tr>
                <td style="padding: 15px 0;">
                  <p><span class="dashicons dashicons-yes-alt pch-text-success"></span>&nbsp;<?php echo __('This product is linked with <a href="' . esc_url($connected_product) . '" target="_blank" rel="nofollow">' . esc_url(substr($connected_product, 0, 100) . '...') . '</a>', 'prochains'); ?></p>
                </td>
              </tr>
              <tr>
                <td style="padding: 0;">
                  <fieldset>
                    <label for="pch-remove-url">
                      <input type="hidden" name="pch-remove-url-value" id="pch-remove-url-value" value="">
                      <input style="margin-left: 0;" name="pch-remove-url" type="checkbox" id="pch-remove-url" value="1">
                      &nbsp;<?php echo __('Remove url from this product.', 'prochains'); ?>
                    </label>
                  </fieldset>
                </td>
              </tr>
              <tr>
                <td style="padding: 15px 0;">
                  <small><em><?php echo __('Last updated: ' . ($last_product_updated ? esc_html($last_product_updated) : 'None yet'), 'prochains'); ?></em></small>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <?php } else { ?>
    <div class="theme-browser rendered">
      <div class="themes wp-clearfix">
        <div class="theme" style="width: 100%;">
          <table class="form-table" role="presentation">
            <tbody>
              <tr>
                <th style="padding: 0;"><label for="pch-connnected-product"><?php echo __('Product link', 'prochains'); ?></label></th>
              </tr>
              <tr>
                <td style="padding: 15px 0;">
                  <p><span class="dashicons dashicons-yes-alt"></span>&nbsp;<?php echo __('There are no linked products.', 'prochains'); ?></p>
                </td>
              </tr>
              <tr>
                <td style="padding: 0;">
                  <input style="margin-left: 0; padding: 0 10px;" type="url" name="pch-connnected-product" id="pch-connnected-product" value="" class="regular-text code">
                </td>
              </tr>
              <tr>
                <td style="padding: 15px 0;">
                  <small><em><?php echo __('Enter the source link in the "Product link" field to link to your product.', 'prochains'); ?></em></small>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <?php
    }
  }
}