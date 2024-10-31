<?php
/**
 * @package ProChains\Functions
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

// Default Product Settings
add_action('wp_ajax_nopriv_default_product_settings', 'pch_nopriv_default_product_settings');
add_action('wp_ajax_default_product_settings', 'pch_get_default_product_settings');

function pch_nopriv_default_product_settings() {
  $response = [];

  echo json_encode($response);

  die();
}

function pch_get_default_product_settings() {
  if ( wp_verify_nonce( $_REQUEST['nonce'], 'prochains_ajax' ) ) {
    $product_url = sanitize_text_field($_REQUEST['product_url']);
    $button_text = sanitize_text_field($_REQUEST['button_text']);

    $response = [
      'product_alias' => 'dropship',
      'synchronization' => [
        'title' => true,
        'description' => true,
        'status' => true,
        'categories' => true,
        'featured_image' => true,
        'galleries' => true,
        'price' => true,
        'stock' => true,
        'attributes' => true,
        'variations' => true,
        'reviews_ratings' => false
      ],
      'dropship' => [
        'shipping_cost' => 0,
        'markup_type' => 'percentage',
        'markup_price' => 0
      ],
      'affiliate' => [
        'product_url' => esc_url($product_url),
        'button_text' => esc_html($button_text)
      ]
    ];

    echo json_encode($response);

    die();
  } else {
    $response = [];

    echo json_encode($response);

    die();
  }
}

// Get HTML more settings
add_action('wp_ajax_nopriv_html_more_settings', 'pch_nopriv_html_more_settings');
add_action('wp_ajax_html_more_settings', 'pch_get_html_more_settings');

function pch_nopriv_html_more_settings() {
  echo "";

  die();
}

function pch_get_html_more_settings() {
  if ( wp_verify_nonce( $_REQUEST['nonce'], 'prochains_ajax' ) ) {
    $index = sanitize_text_field($_REQUEST['index']);
    $product_url = sanitize_text_field($_REQUEST['product_url']);
    $button_text = sanitize_text_field($_REQUEST['button_text']);
    ?>

    <tr class="pch-product-item-more-settings pch-hide" id="pch-product-item-more-settings<?php echo esc_html($index); ?>">
      <th></th>
      <th colspan="4">
        <div class="theme-browser rendered">
          <div class="themes wp-clearfix">
            <div class="theme">
              <table class="form-table" role="presentation">
                <tbody>
                  <tr>
                    <th style="padding: 0;"><label for="pch-product-alias<?php echo esc_html($index); ?>"><?php echo __('Product will be set to', 'prochains'); ?></label></th>
                  </tr>
                  <tr>
                    <td style="padding: 15px 0;">
                      <select name="pch-product-alias" id="pch-product-alias<?php echo esc_html($index); ?>" data-id="<?php echo esc_html($index); ?>" class="postform">
                        <option value="dropship" selected="selected"><?php echo __('Dropship', 'prochains'); ?></option>
                        <option value="affiliate"><?php echo __('Affiliate', 'prochains'); ?></option>
                      </select>
                      &nbsp;<?php echo __('product', 'prochains'); ?>
                    </td>
                  </tr>
                  <tr>
                    <th style="padding: 0;"><label for="pch-product-sync<?php echo esc_html($index); ?>"><?php echo __('Auto synchronization (Coming Soon)', 'prochains'); ?></label></th>
                  </tr>
                  <tr>
                    <td style="padding: 15px 0;">
                      <div class="pch-wrap-list-items">
                        <fieldset>
                          <label for="pch-product-title-sync<?php echo esc_html($index); ?>">
                            <input style="margin-left: 0;" name="pch-product-title-sync" type="checkbox" id="pch-product-title-sync<?php echo esc_html($index); ?>" data-id="<?php echo esc_html($index); ?>" value="1" disabled>
                            &nbsp;<?php echo __('Title (Coming Soon)', 'prochains'); ?>
                          </label>
                        </fieldset>
                        <fieldset>
                          <label for="pch-product-description-sync<?php echo esc_html($index); ?>">
                            <input style="margin-left: 0;" name="pch-product-description-sync" type="checkbox" id="pch-product-description-sync<?php echo esc_html($index); ?>" data-id="<?php echo esc_html($index); ?>" value="1" disabled>
                            &nbsp;<?php echo __('Description (Coming Soon)', 'prochains'); ?>
                          </label>
                        </fieldset>
                        <fieldset>
                          <label for="pch-product-status-sync<?php echo esc_html($index); ?>">
                            <input style="margin-left: 0;" name="pch-product-status-sync" type="checkbox" id="pch-product-status-sync<?php echo esc_html($index); ?>" data-id="<?php echo esc_html($index); ?>" value="1" disabled>
                            &nbsp;<?php echo __('Status (Coming Soon)', 'prochains'); ?>
                          </label>
                        </fieldset>
                        <fieldset>
                          <label for="pch-product-categories-sync<?php echo esc_html($index); ?>">
                            <input style="margin-left: 0;" name="pch-product-categories-sync" type="checkbox" id="pch-product-categories-sync<?php echo esc_html($index); ?>" data-id="<?php echo esc_html($index); ?>" value="1" disabled>
                            &nbsp;<?php echo __('Categories (Coming Soon)', 'prochains'); ?>
                          </label>
                        </fieldset>
                        <fieldset>
                          <label for="pch-product-featured-image-sync<?php echo esc_html($index); ?>">
                            <input style="margin-left: 0;" name="pch-product-featured-image-sync" type="checkbox" id="pch-product-featured-image-sync<?php echo esc_html($index); ?>" data-id="<?php echo esc_html($index); ?>" value="1" disabled>
                            &nbsp;<?php echo __('Featured Image (Coming Soon)', 'prochains'); ?>
                          </label>
                        </fieldset>
                        <fieldset>
                          <label for="pch-product-galleries-sync<?php echo esc_html($index); ?>">
                            <input style="margin-left: 0;" name="pch-product-galleries-sync" type="checkbox" id="pch-product-galleries-sync<?php echo esc_html($index); ?>" data-id="<?php echo esc_html($index); ?>" value="1" disabled>
                            &nbsp;<?php echo __('Galleries (Coming Soon)', 'prochains'); ?>
                          </label>
                        </fieldset>
                        <fieldset>
                          <label for="pch-product-price-sync<?php echo esc_html($index); ?>">
                            <input style="margin-left: 0;" name="pch-product-price-sync" type="checkbox" id="pch-product-price-sync<?php echo esc_html($index); ?>" data-id="<?php echo esc_html($index); ?>" value="1" disabled>
                            &nbsp;<?php echo __('Prices (Coming Soon)', 'prochains'); ?>
                          </label>
                        </fieldset>
                        <fieldset>
                          <label for="pch-product-stock-sync<?php echo esc_html($index); ?>">
                            <input style="margin-left: 0;" name="pch-product-stock-sync" type="checkbox" id="pch-product-stock-sync<?php echo esc_html($index); ?>" data-id="<?php echo esc_html($index); ?>" value="1" disabled>
                            &nbsp;<?php echo __('Stock (Coming Soon)', 'prochains'); ?>
                          </label>
                        </fieldset>
                        <fieldset>
                          <label for="pch-product-attributes-sync<?php echo esc_html($index); ?>">
                            <input style="margin-left: 0;" name="pch-product-attributes-sync" type="checkbox" id="pch-product-attributes-sync<?php echo esc_html($index); ?>" data-id="<?php echo esc_html($index); ?>" value="1" disabled>
                            &nbsp;<?php echo __('Attributes (Coming Soon)', 'prochains'); ?>
                          </label>
                        </fieldset>
                        <fieldset>
                          <label for="pch-product-variations-sync<?php echo esc_html($index); ?>">
                            <input style="margin-left: 0;" name="pch-product-variations-sync" type="checkbox" id="pch-product-variations-sync<?php echo esc_html($index); ?>" data-id="<?php echo esc_html($index); ?>" value="1" disabled>
                            &nbsp;<?php echo __('Variations (Coming Soon)', 'prochains'); ?>
                          </label>
                        </fieldset>
                        <fieldset>
                          <label for="pch-product-review-rating-sync<?php echo esc_html($index); ?>">
                            <input style="margin-left: 0;" name="pch-product-review-rating-sync" type="checkbox" id="pch-product-review-rating-sync<?php echo esc_html($index); ?>" data-id="<?php echo esc_html($index); ?>" value="1" disabled>
                            &nbsp;<?php echo __('Reviews & Ratings (Coming Soon)', 'prochains'); ?>
                          </label>
                        </fieldset>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td style="padding: 15px 0;">
                      <button type="button" class="button pch-close-more-settings" data-id="pch-product-item-more-settings<?php echo esc_html($index); ?>"><?php echo __('Close', 'prochains'); ?></button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div class="theme">
              <h3 style="color: #2e2e2e; margin: 10px 0;"><?php echo __('Dropshipping fields', 'prochains'); ?></h3>
              <table class="form-table" role="presentation">
                <tbody>
                  <tr>
                    <th style="padding: 0;"><label for="pch-shipping-cost-dropship<?php echo esc_html($index); ?>"><?php echo __('Shipping cost (' . get_woocommerce_currency_symbol() . ')', 'prochains'); ?></label></th>
                  </tr>
                  <tr>
                    <td style="padding: 15px 0;">
                      <input style="margin-left: 0; padding: 0 10px;" type="number" min="0" step="1" name="pch-shipping-cost-dropship" id="pch-shipping-cost-dropship<?php echo esc_html($index); ?>" data-id="<?php echo esc_html($index); ?>" value="0" class="regular-text code">
                    </td>
                  </tr>
                  <tr>
                    <th style="padding: 0;"><label for="pch-markup-type-dropship<?php echo esc_html($index); ?>"><?php echo __('Markup type', 'prochains'); ?></label></th>
                  </tr>
                  <tr>
                    <td style="padding: 15px 0;">
                      <select name="pch-markup-type-dropship" id="pch-markup-type-dropship<?php echo esc_html($index); ?>" data-id="<?php echo esc_html($index); ?>" class="postform">
                        <option value="percentage" selected="selected"><?php echo __('Percentage', 'prochains'); ?></option>
                        <option value="fixed"><?php echo __('Fixed', 'prochains'); ?></option>
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <th style="padding: 0;"><label for="pch-markup-price-dropship<?php echo esc_html($index); ?>"><?php echo __('Markup value', 'prochains'); ?></label></th>
                  </tr>
                  <tr>
                    <td style="padding: 15px 0;">
                      <input style="margin-left: 0; padding: 0 10px;" type="number" min="0" step="1" name="pch-markup-price-dropship" id="pch-markup-price-dropship<?php echo esc_html($index); ?>" data-id="<?php echo esc_html($index); ?>" value="0" class="regular-text code">
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

            <div class="theme">
              <h3 style="color: #2e2e2e; margin: 10px 0;"><?php echo __('Affiliate fields', 'prochains'); ?></h3>
              <table class="form-table" role="presentation">
                <tbody>
                  <tr>
                    <th style="padding: 0;"><label for="pch-product-url-affiliate<?php echo esc_html($index); ?>"><?php echo __('Product URL', 'prochains'); ?></label></th>
                  </tr>
                  <tr>
                    <td style="padding: 15px 0;">
                      <input style="margin-left: 0; padding: 0 10px;" type="url" name="pch-product-url-affiliate" id="pch-product-url-affiliate<?php echo esc_html($index); ?>" data-id="<?php echo esc_html($index); ?>" value="<?php echo esc_url($product_url); ?>" class="regular-text code" disabled>
                    </td>
                  </tr>
                  <tr>
                    <th style="padding: 0;"><label for="pch-product-button-text-affiliate<?php echo esc_html($index); ?>"><?php echo __('Button text', 'prochains'); ?></label></th>
                  </tr>
                  <tr>
                    <td style="padding: 15px 0;">
                      <input style="margin-left: 0; padding: 0 10px;" type="text" name="pch-product-button-text-affiliate" id="pch-product-button-text-affiliate<?php echo esc_html($index); ?>" data-id="<?php echo esc_html($index); ?>" value="<?php echo esc_html($button_text); ?>" class="regular-text code" disabled>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </th>
    </tr>

    <?php
    die();
  } else {
    echo "";

    die();
  }
}

// Get token API
add_action('wp_ajax_nopriv_new_token_api', 'pch_nopriv_new_token_api');
add_action('wp_ajax_new_token_api', 'pch_get_new_token_api');

function pch_nopriv_new_token_api() {
  echo "";

  die();
}

function pch_get_new_token_api() {
  if ( wp_verify_nonce( $_REQUEST['nonce'], 'prochains_ajax' ) ) {
    require_once(PCH_PLUGIN_DIR . 'admin/class-pch-auth.php');

    $pch_auth = new PCH_Auth();

    // delete_option('_auth_prochains4wp');
    if(get_option('_auth_prochains4wp') === false) {
      $token = $pch_auth->generate_token();

      add_option('_auth_prochains4wp', $token);
      
      echo $token;
    } else {
      if($pch_auth->check_token() === false) {
        $new_token = $pch_auth->generate_token();

        update_option('_auth_prochains4wp', $new_token);

        echo $new_token;
      } else {
        echo esc_html(get_option('_auth_prochains4wp'));
      }
    }

    die();
  } else {
    echo "";

    die();
  }
}