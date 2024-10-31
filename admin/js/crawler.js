(function($) {
  var arrProducts = [];
  var arrSelectedProducts = [];
  var totals = 0;
  var indexCrawling = 0;
  var arrListError = [];
  var arrListSuccess = [];

  $(window).on('load', (function() {
    displayPrice = function(price_min, price_max) {
      return (price_min !== price_max ? price_min.toString() + ' - ' + price_max.toString() : price_max.toString());
    }

    displaySellingPrice = function(index, display_price) {
      var display_selling_price = "";

      const shipping_cost = $('#pch-shipping-cost-dropship' + index).val();
      const markup_type = $('#pch-markup-type-dropship' + index).find(':selected').val();
      const markup_price = $('#pch-markup-price-dropship' + index).val();

      let arrNewPrices = [];
      const arrPrices = display_price.split(' - ');

      $.each(arrPrices, function(index, price) {
        let sPrice = parseInt(price) + parseInt(shipping_cost) + (markup_type === 'fixed' ? parseInt(markup_price) : (parseInt(markup_price) * parseInt(price)) / 100);

        arrNewPrices.push(sPrice);
      })

      if(arrNewPrices.length > 1) {
        display_selling_price = displayPrice(arrNewPrices[0], arrNewPrices[1]);
      } else {
        display_selling_price = displayPrice(arrNewPrices[0], arrNewPrices[0]);
      }

      if(display_selling_price !== display_price) {
        $('#pch-product-list > tbody tr#pch-product-item' + index + ' th strong.pch-origin-product-price').attr('style', 'text-decoration: line-through;');
        $('#pch-product-list > tbody tr#pch-product-item' + index + ' th .pch-selling-price').html('<p class="pch-mb-0"><strong>Selling price</strong></p><p class="pch-mt-0" style="font-size: 14px;color: green;"><strong>' + display_selling_price + '</strong></p>');
      } else {
        $('#pch-product-list > tbody tr#pch-product-item' + index + ' th strong.pch-origin-product-price').removeAttr('style');
        $('#pch-product-list > tbody tr#pch-product-item' + index + ' th .pch-selling-price').html('');
      }
    }

    resetFormTableCrawler = function(index, total, count, urls=[]) {
      if(index === total) {
        // Reset Table
        $('#pch-product-list > tbody #pch-product-loading').remove();

        if(count != 0) {
          $('#pch-product-list > tbody #pch-product-empty').remove();
        } else {
          $('#pch-product-list > tbody').html('<tr id="pch-product-empty"><th colspan="5">No products found.</th></tr>');
        }

        // Reset Form
        $('#pch-product-urls').val('');
        $('#pch-product-urls').removeClass('pch-input-err');
        $('#pch-product-urls').removeAttr('disabled');

        $('#pch-run-crawler #runCrawler').val('Run MagicTool!');
        $('#pch-run-crawler #runCrawler').attr('type', 'submit');
        $('#pch-run-crawler #runCrawler').addClass('button-primary');
        $('#pch-run-crawler #runCrawler').removeClass('button-disabled');

        // Request timeout
        // setTimeout(function() {
        //   if(urls.length) {
        //     urls.forEach(function(url, index) {
        //       if(arrListError.includes(url) === false && arrListSuccess.includes(url) === false) {
        //         addListError(url, 'because the request was not completed');
        //       }
        //     })
        //   }
        // }, 15000)

        indexCrawling = 0;
        totals = 0;
      }
    }
    
    getProductItem = function(arr_urls, i) {
      const nextIsExists = (arr_urls[i + 1] === 'undefined' || typeof arr_urls[i + 1] === 'undefined' ? null : arr_urls[i + 1]);

      if (arr_urls[i].indexOf("shopee") >= 0) {
        // MAINTENANCE
        $('li#pch-status-shopee').attr('title', 'Disruption');
        $('li#pch-status-shopee label span').addClass('pch-text-error');
        $('li#pch-status-shopee label span').removeClass('pch-text-success');

        if(response.status === 403 || response.status === 401) {
          updateTokenAPI();
          
          addListError(arr_urls[i], 'Server for shopee products is maintenance.');
        } else {
          addListError(arr_urls[i], 'Server for shopee products is maintenance.');
        }
        
        // $.ajax({
        //   method: 'POST',
        //   url: ajaxObj.api_endpoint_app_get_product_shopee,
        //   crossDomain: true,
        //   // timeout: 90000,
        //   data: JSON.stringify({
        //     url: arr_urls[i],
        //     token: ajaxObj.api_token
        //   }),
        //   contentType: "application/json; charset=utf-8",
        //   success: function(response) {
        //     if(response.post_title !== null) {
        //       getHtmlMoreSettings('#pch-product-list > tbody', i, arr_urls[i], 'Buy on Shopee', response);
  
        //       response.product_type = "simple";
        //       response.is_imported = false;
        //       arrProducts['pch-product-item' + i] = response;
  
        //       setProductSettings('pch-product-item' + i, arr_urls[i], 'Buy on Shopee');
              
        //       totals++;
        //     }
        //   },
        //   // error: function(error) {
        //   //   indexCrawling++;

        //   //   resetFormTableCrawler(indexCrawling, arr_urls.length, totals, arr_urls);

        //   //   if(nextIsExists !== null) {
        //   //     setTimeout(function() {
        //   //       getProductItem(arr_urls, (i + 1));
        //   //     }, 3000)
        //   //   }
        //   // },
        //   complete: function(response, status) {
        //     indexCrawling++;

        //     if(status === 'success') {
        //       $('#pch-product-list > tbody #pch-product-loading').remove();
        //       $('#pch-product-list > tbody #pch-product-empty').remove();

        //       $('li#pch-status-shopee').attr('title', 'Inactive');
        //       $('li#pch-status-shopee label span').removeClass('pch-text-success');
        //       $('li#pch-status-shopee label span').addClass('pch-text-error');
        //     } else {
        //       $('li#pch-status-shopee').attr('title', 'Disruption');
        //       $('li#pch-status-shopee label span').addClass('pch-text-error');
        //       $('li#pch-status-shopee label span').removeClass('pch-text-success');

        //       if(response.status === 403 || response.status === 401) {
        //         updateTokenAPI();
                
        //         addListError(arr_urls[i], 'due to an ' + response.responseJSON.message.toLowerCase().replace('.', '') + ' request. Please click <a href="javascript:window.location.reload(true)">here</a> and try again');
        //       } else {
        //         addListError(arr_urls[i], 'due to an bad request');
        //       }
        //     }

        //     resetFormTableCrawler(indexCrawling, arr_urls.length, totals, arr_urls);

        //     if(nextIsExists !== null) {
        //       setTimeout(function() {
        //         getProductItem(arr_urls, (i + 1));
        //       }, 1500)
        //     }
        //   }
        // });
      } else if (arr_urls[i].indexOf("tokopedia") >= 0) {
        $.ajax({
          method: 'POST',
          url: ajaxObj.api_endpoint_app_get_product_tokopedia,
          crossDomain: true,
          // timeout: 90000,
          data: JSON.stringify({
            url: arr_urls[i],
            token: ajaxObj.api_token
          }),
          contentType: "application/json; charset=utf-8",
          success: function(response) {
            if(response.post_title !== null) {
              getHtmlMoreSettings('#pch-product-list > tbody', i, arr_urls[i], 'Buy on Tokopedia', response);
  
              response.product_type = "simple";
              response.is_imported = false;
              arrProducts['pch-product-item' + i] = response;
  
              setProductSettings('pch-product-item' + i, arr_urls[i], 'Buy on Tokopedia');

              totals++;
            }
          },
          // error: function(error) {
          //   indexCrawling++;

          //   resetFormTableCrawler(indexCrawling, arr_urls.length, totals, arr_urls);

          //   if(nextIsExists !== null) {
          //     setTimeout(function() {
          //       getProductItem(arr_urls, (i + 1));
          //     }, 3000)
          //   }
          // },
          complete: function(response, status) {
            indexCrawling++;

            if(status === 'success') {
              $('#pch-product-list > tbody #pch-product-loading').remove();
              $('#pch-product-list > tbody #pch-product-empty').remove();

              $('li#pch-status-tokopedia').attr('title', 'Active');
              $('li#pch-status-tokopedia label span').addClass('pch-text-success');
              $('li#pch-status-tokopedia label span').removeClass('pch-text-error');
            } else {
              $('li#pch-status-tokopedia').attr('title', 'Disruption');
              $('li#pch-status-tokopedia label span').addClass('pch-text-error');
              $('li#pch-status-tokopedia label span').removeClass('pch-text-success');

              if(response.status === 403 || response.status === 401) {
                updateTokenAPI();
                
                addListError(arr_urls[i], 'due to an ' + response.responseJSON.message.toLowerCase().replace('.', '') + ' request. Please click <a href="javascript:window.location.reload(true)">here</a> and try again');
              } else {
                addListError(arr_urls[i], 'due to an bad request');
              }
            }

            resetFormTableCrawler(indexCrawling, arr_urls.length, totals, arr_urls);

            if(nextIsExists !== null) {
              setTimeout(function() {
                getProductItem(arr_urls, (i + 1));
              }, 1500)
            }
          }
        });
      } else if (arr_urls[i].indexOf("bukalapak") >= 0) {
        $.ajax({
          method: 'POST',
          url: ajaxObj.api_endpoint_app_get_product_bukalapak,
          crossDomain: true,
          // timeout: 90000,
          data: JSON.stringify({
            url: arr_urls[i],
            token: ajaxObj.api_token
          }),
          contentType: "application/json; charset=utf-8",
          success: function(response) {
            if(response.post_title !== null) {
              getHtmlMoreSettings('#pch-product-list > tbody', i, arr_urls[i], 'Buy on Bukalapak', response);
  
              response.product_type = "simple";
              response.is_imported = false;
              arrProducts['pch-product-item' + i] = response;
  
              setProductSettings('pch-product-item' + i, arr_urls[i], 'Buy on Bukalapak');

              totals++;
            }
          },
          // error: function(error) {
          //   indexCrawling++;

          //   resetFormTableCrawler(indexCrawling, arr_urls.length, totals, arr_urls);

          //   if(nextIsExists !== null) {
          //     setTimeout(function() {
          //       getProductItem(arr_urls, (i + 1));
          //     }, 3000)
          //   }
          // },
          complete: function(response, status) {
            indexCrawling++;

            if(status === 'success') {
              $('#pch-product-list > tbody #pch-product-loading').remove();
              $('#pch-product-list > tbody #pch-product-empty').remove();

              $('li#pch-status-bukalapak').attr('title', 'Active');
              $('li#pch-status-bukalapak label span').addClass('pch-text-success');
              $('li#pch-status-bukalapak label span').removeClass('pch-text-error');
            } else {
              $('li#pch-status-bukalapak').attr('title', 'Disruption');
              $('li#pch-status-bukalapak label span').addClass('pch-text-error');
              $('li#pch-status-bukalapak label span').removeClass('pch-text-success');

              if(response.status === 403 || response.status === 401) {
                updateTokenAPI();
                
                addListError(arr_urls[i], 'due to an ' + response.responseJSON.message.toLowerCase().replace('.', '') + ' request. Please click <a href="javascript:window.location.reload(true)">here</a> and try again');
              } else {
                addListError(arr_urls[i], 'due to an bad request');
              }
            }

            resetFormTableCrawler(indexCrawling, arr_urls.length, totals, arr_urls);

            if(nextIsExists !== null) {
              setTimeout(function() {
                getProductItem(arr_urls, (i + 1));
              }, 1500)
            }
          }
        });
      } else {
        indexCrawling++;

        addListError(arr_urls[i], 'due to an invalid request');

        resetFormTableCrawler(indexCrawling, arr_urls.length, totals, arr_urls);

        if(nextIsExists !== null) {
          setTimeout(function() {
            getProductItem(arr_urls, (i + 1));
          }, 3000)
        }
      }
    }

    addListError = function(url, message) {
      arrListError.push(url);

      $('#pch-list-error').append('<li>Product url <a href="' + url + '" target="_blank" rel="nofollow">' + url + '</a> failed to extract ' + message + '.</li>');

      $('#pch-total-errors').html('(' + arrListError.length + ')');
    }

    resetListError = function() {
      arrListError = [];

      $('#pch-list-error').html('');

      $('#pch-total-errors').html('(0)');
    }

    getHtmlMoreSettings = function(elm, index, product_url, button_text, response) {
      $.ajax({
        method: 'POST',
        url: ajaxObj.ajax_url,
        data: 'action=html_more_settings&index=' + index + '&product_url=' + product_url + '&button_text=' + button_text + '&nonce=' + ajaxObj.nonce,
        dataType: 'html',
        processData: false,
        success: function(html) {
          if(html !== '') {
            arrListSuccess.push(product_url);

            $(elm).append('<tr class="pch-product-item" id="pch-product-item' + index + '">'+
            '<th width="20">'+
              '<input type="checkbox" class="pch-cb-product" name="products[]" value="pch-product-item' + index + '" />'+
            '</th>'+
            '<th width="100"><img src="' + response.base_thumbnail_url.toString() + response.post_thumbnail.toString() + '" alt="' + response.post_title.toString() + '" width="80" height="80" /></th>'+
            '<th><label class="row-title" title="' + response.post_title.toString() + '">' + (response.post_title.toString().length > 100 ? response.post_title.toString().substr(0, 100) + '...' : response.post_title.toString()) + '</label><p class="pch-mt-0" title="' + response.from_url.toString() + '">' + (response.from_url.toString().length > 100 ? response.from_url.toString().substr(0, 100) + '...' : response.from_url.toString()) + '</p><a href="#" class="pch-more-settings" data-id="pch-product-item-more-settings' + index + '">More Settings</a></th>'+
            '<th width="250"><strong class="pch-origin-product-price">' + displayPrice(response.price_min, response.price_max) + '</strong><div class="pch-selling-price"></div></th>'+
            '<th width="300">'+
              '<ul class="pch-inline-list pch-mt-0 pch-mb-0">'+
                '<li><button type="button" class="button button-primary pch-import-product" data-id="pch-product-item' + index + '">Import</button></li>'+
                '<li><button type="button" class="button pch-delete-product" data-id="pch-product-item' + index + '">Delete</button></li>'+
              '</ul>'+
            '</th>'+
            '</tr>');

            $(elm).append(html);
          }
        }
      })
    }

    updateTokenAPI = function() {
      $.ajax({
        method: 'POST',
        url: ajaxObj.ajax_url,
        data: 'action=new_token_api&nonce=' + ajaxObj.nonce,
        dataType: 'html',
        processData: false,
        success: function(token) {
          
        },
        error: function(err) {
          
        },
        complete: function(response, status) {
          ajaxObj.api_token = response.responseText;
        }
      })
    }

    setProductSettings = function(product_id, product_url, button_text) {
      $.ajax({
        method: 'POST',
        url: ajaxObj.ajax_url,
        data: 'action=default_product_settings&product_url=' + product_url + '&button_text=' + button_text + '&nonce=' + ajaxObj.nonce,
        dataType: 'json',
        processData: false,
        success: function(response) {
          if(response.product_alias) {
            arrProducts[product_id].product_alias = response.product_alias;
            arrProducts[product_id].synchronization = response.synchronization;
            arrProducts[product_id].dropship = response.dropship;
            arrProducts[product_id].affiliate = response.affiliate;
          }
        }
      })
    }

    // Import Product
    $(document).on('click', '.pch-import-product', function() {
      var elm = $(this);
      const product_id = elm.data('id');
      
      elm.html('Loading..');
      elm.removeClass('pch-import-product');
      elm.removeClass('button-primary');
      elm.addClass('button-disabled');

      $.ajax({
        method: 'POST',
        url: ajaxObj.api_endpoint_web_create_product,
        // crossDomain: true,
        // timeout: 90000,
        data: JSON.stringify(arrProducts[product_id]),
        contentType: "application/json; charset=utf-8",
        success: function(response) {
          arrProducts[product_id].is_imported = true;
          $('#pch-product-list > tbody tr#' + product_id + ' th').first().html('');
          $('#pch-product-list > tbody tr#' + product_id + ' + tr.pch-product-item-more-settings').remove();
          $('#pch-product-list > tbody tr#' + product_id + ' th a.pch-more-settings').remove();

          if(response.success && response.permalink) {
            $('button.pch-delete-product[data-id="' + product_id + '"]').remove();
            elm.after('<label><span class="dashicons dashicons-yes-alt pch-text-success"></span>&nbsp;Product was successfully imported.</label><p class="pch-mt-5"><a href="' + response.permalink + '" target="_blank">Visit the product page</a></p>');

            elm.remove();
          } else {
            $('button.pch-delete-product[data-id="' + product_id + '"]').remove();
            elm.after('<label><span class="dashicons dashicons-dismiss pch-text-error"></span>&nbsp;Product failed to import..</label>');

            elm.remove();
          }
        },
        error: function(error) {
          $('button.pch-delete-product[data-id="' + product_id + '"]').remove();
          elm.after('<label><span class="dashicons dashicons-dismiss pch-text-error"></span>&nbsp;Product failed to import..</label>');

          elm.remove();
        }
      });
    })

    // Delete Product
    $(document).on('click', '.pch-delete-product', function() {
      var elm = $(this);
      const product_id = elm.data('id');
      
      // Remove from the list
      delete arrProducts[product_id];
      // totals--;

      $('#pch-product-list > tbody tr#' + product_id + ' + tr.pch-product-item-more-settings').remove();
      $('#pch-product-list > tbody tr#' + product_id).remove();

      if($('#pch-product-list > tbody tr.pch-product-item').length === 0) {
        $('#pch-product-list > tbody').html('<tr id="pch-product-empty"><th colspan="5">No products found.</th></tr>');
      }
    })

    // Show More Settings
    $(document).on('click', '.pch-more-settings', function(e) {
      e.preventDefault();

      var elm = $(this);
      const elmTarget = elm.data('id');

      $('#pch-product-list > tbody tr#' + elmTarget).removeClass('pch-hide');
    })

    // Close More Settings
    $(document).on('click', '.pch-close-more-settings', function(e) {
      e.preventDefault();

      var elm = $(this);
      const elmTarget = elm.data('id');

      $('#pch-product-list > tbody tr#' + elmTarget).addClass('pch-hide');
    })

    // Product Alias
    $(document).on('change', 'select[name="pch-product-alias"]', function(e) {
      e.preventDefault();

      var elm = $(this);
      const index = elm.data('id');
      const product_alias = elm.find(':selected').val();

      if(product_alias === 'dropship') {
        const display_price = $('#pch-product-list > tbody tr#pch-product-item' + index + ' th .pch-origin-product-price').text();
        
        $('input#pch-shipping-cost-dropship' + index).removeAttr('disabled');
        $('select#pch-markup-type-dropship' + index).removeAttr('disabled');
        $('input#pch-markup-price-dropship' + index).removeAttr('disabled');

        $('input#pch-product-url-affiliate' + index).attr('disabled', 'disabled');
        $('input#pch-product-button-text-affiliate' + index).attr('disabled', 'disabled');

        displaySellingPrice(index, display_price);
      } else {
        $('input#pch-shipping-cost-dropship' + index).attr('disabled', 'disabled');
        $('select#pch-markup-type-dropship' + index).attr('disabled', 'disabled');
        $('input#pch-markup-price-dropship' + index).attr('disabled', 'disabled');

        $('input#pch-product-url-affiliate' + index).removeAttr('disabled');
        $('input#pch-product-button-text-affiliate' + index).removeAttr('disabled');

        $('#pch-product-list > tbody tr#pch-product-item' + index + ' th strong.pch-origin-product-price').removeAttr('style');
        $('#pch-product-list > tbody tr#pch-product-item' + index + ' th .pch-selling-price').html('');
      }

      arrProducts['pch-product-item' + index].product_alias = product_alias;
    })

    // Synchronization: Title
    $(document).on('change', 'input[name="pch-product-title-sync"]', function(e) {
      e.preventDefault();

      var elm = $(this);
      const index = elm.data('id');

      if(elm.is(':checked')) {
        arrProducts['pch-product-item' + index].synchronization.title = true;
      } else {
        arrProducts['pch-product-item' + index].synchronization.title = false;
      }
    })

    // Synchronization: description
    $(document).on('change', 'input[name="pch-product-description-sync"]', function(e) {
      e.preventDefault();

      var elm = $(this);
      const index = elm.data('id');

      if(elm.is(':checked')) {
        arrProducts['pch-product-item' + index].synchronization.description = true;
      } else {
        arrProducts['pch-product-item' + index].synchronization.description = false;
      }
    })

    // Synchronization: status
    $(document).on('change', 'input[name="pch-product-status-sync"]', function(e) {
      e.preventDefault();

      var elm = $(this);
      const index = elm.data('id');

      if(elm.is(':checked')) {
        arrProducts['pch-product-item' + index].synchronization.status = true;
      } else {
        arrProducts['pch-product-item' + index].synchronization.status = false;
      }
    })

    // Synchronization: categories
    $(document).on('change', 'input[name="pch-product-categories-sync"]', function(e) {
      e.preventDefault();

      var elm = $(this);
      const index = elm.data('id');

      if(elm.is(':checked')) {
        arrProducts['pch-product-item' + index].synchronization.categories = true;
      } else {
        arrProducts['pch-product-item' + index].synchronization.categories = false;
      }
    })

    // Synchronization: featured-image
    $(document).on('change', 'input[name="pch-product-featured-image-sync"]', function(e) {
      e.preventDefault();

      var elm = $(this);
      const index = elm.data('id');

      if(elm.is(':checked')) {
        arrProducts['pch-product-item' + index].synchronization.featured_image = true;
      } else {
        arrProducts['pch-product-item' + index].synchronization.featured_image = false;
      }
    })

    // Synchronization: galleries
    $(document).on('change', 'input[name="pch-product-galleries-sync"]', function(e) {
      e.preventDefault();

      var elm = $(this);
      const index = elm.data('id');

      if(elm.is(':checked')) {
        arrProducts['pch-product-item' + index].synchronization.galleries = true;
      } else {
        arrProducts['pch-product-item' + index].synchronization.galleries = false;
      }
    })

    // Synchronization: price
    $(document).on('change', 'input[name="pch-product-price-sync"]', function(e) {
      e.preventDefault();

      var elm = $(this);
      const index = elm.data('id');

      if(elm.is(':checked')) {
        arrProducts['pch-product-item' + index].synchronization.price = true;
      } else {
        arrProducts['pch-product-item' + index].synchronization.price = false;
      }
    })

    // Synchronization: stock
    $(document).on('change', 'input[name="pch-product-stock-sync"]', function(e) {
      e.preventDefault();

      var elm = $(this);
      const index = elm.data('id');

      if(elm.is(':checked')) {
        arrProducts['pch-product-item' + index].synchronization.stock = true;
      } else {
        arrProducts['pch-product-item' + index].synchronization.stock = false;
      }
    })

    // Synchronization: attributes
    $(document).on('change', 'input[name="pch-product-attributes-sync"]', function(e) {
      e.preventDefault();

      var elm = $(this);
      const index = elm.data('id');

      if(elm.is(':checked')) {
        arrProducts['pch-product-item' + index].synchronization.attributes = true;
      } else {
        arrProducts['pch-product-item' + index].synchronization.attributes = false;
      }
    })

    // Synchronization: variations
    $(document).on('change', 'input[name="pch-product-variations-sync"]', function(e) {
      e.preventDefault();

      var elm = $(this);
      const index = elm.data('id');

      if(elm.is(':checked')) {
        arrProducts['pch-product-item' + index].synchronization.variations = true;
      } else {
        arrProducts['pch-product-item' + index].synchronization.variations = false;
      }
    })

    // Synchronization: review-rating
    $(document).on('change', 'input[name="pch-product-review-rating-sync"]', function(e) {
      e.preventDefault();

      var elm = $(this);
      const index = elm.data('id');

      if(elm.is(':checked')) {
        arrProducts['pch-product-item' + index].synchronization.reviews_ratings = true;
      } else {
        arrProducts['pch-product-item' + index].synchronization.reviews_ratings = false;
      }
    })

    // Dropship: Shipping cost
    $(document).on('keyup', 'input[name="pch-shipping-cost-dropship"]', function(e) {
      e.preventDefault();

      var elm = $(this);
      const index = elm.data('id');
      const shipping_cost = elm.val();

      if(shipping_cost !== '') {
        const display_price = $('#pch-product-list > tbody tr#pch-product-item' + index + ' th .pch-origin-product-price').text();
        arrProducts['pch-product-item' + index].dropship.shipping_cost = shipping_cost;

        displaySellingPrice(index, display_price);
      }
    })

    // Dropship: Markup type
    $(document).on('change', 'select[name="pch-markup-type-dropship"]', function(e) {
      e.preventDefault();

      var elm = $(this);
      const index = elm.data('id');
      const markup_type = elm.find(':selected').val();

      const display_price = $('#pch-product-list > tbody tr#pch-product-item' + index + ' th .pch-origin-product-price').text();

      arrProducts['pch-product-item' + index].dropship.markup_type = markup_type;

      displaySellingPrice(index, display_price);
    })

    // Dropship: Markup price
    $(document).on('keyup', 'input[name="pch-markup-price-dropship"]', function(e) {
      e.preventDefault();

      var elm = $(this);
      const index = elm.data('id');
      const markup_price = elm.val();

      if(markup_price !== '') {
        const display_price = $('#pch-product-list > tbody tr#pch-product-item' + index + ' th .pch-origin-product-price').text();
        arrProducts['pch-product-item' + index].dropship.markup_price = markup_price;

        displaySellingPrice(index, display_price);
      }
    })

    // Affiliate: Product URL
    $(document).on('keyup', 'input[name="pch-product-url-affiliate"]', function(e) {
      e.preventDefault();

      var elm = $(this);
      const index = elm.data('id');
      const product_url = elm.val();

      if(product_url !== '') {
        arrProducts['pch-product-item' + index].affiliate.product_url = product_url;
      }
    })

    // Affiliate: Product URL
    $(document).on('keyup', 'input[name="pch-product-button-text-affiliate"]', function(e) {
      e.preventDefault();

      var elm = $(this);
      const index = elm.data('id');
      const button_text = elm.val();

      if(button_text !== '') {
        arrProducts['pch-product-item' + index].affiliate.button_text = button_text;
      }
    })

    // Toggle Select Product
    if($('#pch-select-all-products').length) {
      $('#pch-select-all-products').on('click', (function() {
        arrSelectedProducts = [];
        
        if($(this).is(':checked')) {
          $('.pch-cb-product').prop('checked', true);

          $('.pch-cb-product').each(function() {
            arrSelectedProducts.push($(this).val());
          }) 
        } else {
          $('.pch-cb-product').prop('checked', false);

          arrSelectedProducts = [];
        }
      }))
    }

    // Select Product
    $(document).on('click', '.pch-cb-product', function() {
      arrSelectedProducts = [];

      $('.pch-cb-product').each(function() {
        if($(this).is(':checked')) {
          arrSelectedProducts.push($(this).val());
        }
      })
    })
    
    // Check Status API Shopee
    if($('li#pch-status-shopee').length) {
      $('li#pch-status-shopee').attr('title', 'Inactive');
      $('li#pch-status-shopee label span').removeClass('pch-text-success');
      $('li#pch-status-shopee label span').addClass('pch-text-error');

      // $.ajax({
      //   method: 'POST',
      //   url: ajaxObj.api_endpoint_app_check_status_shopee,
      //   crossDomain: true,
      //   timeout: 90000,
      //   data: JSON.stringify({
      //     token: ajaxObj.api_token
      //   }),
      //   contentType: "application/json; charset=utf-8",
      //   success: function(response) {
      //     if(response.is_active) {
      //       $('li#pch-status-shopee').attr('title', 'Inactive');
      //       $('li#pch-status-shopee label span').removeClass('pch-text-success');
      //       $('li#pch-status-shopee label span').addClass('pch-text-error');
      //     } else {
      //       $('li#pch-status-shopee').attr('title', 'Disruption');
      //       $('li#pch-status-shopee label span').addClass('pch-text-error');
      //       $('li#pch-status-shopee label span').removeClass('pch-text-success');
      //     }
      //   },
      //   error: function(response, status) {
      //     $('li#pch-status-shopee').attr('title', 'Disruption');
      //     $('li#pch-status-shopee label span').addClass('pch-text-error');
      //     $('li#pch-status-shopee label span').removeClass('pch-text-success');
      //   }
      // });
    }

    // Check Status API Tokopedia
    if($('li#pch-status-tokopedia').length) {
      $('li#pch-status-tokopedia').attr('title', 'Active');
      $('li#pch-status-tokopedia label span').addClass('pch-text-success');
      $('li#pch-status-tokopedia label span').removeClass('pch-text-error');

      // $.ajax({
      //   method: 'POST',
      //   url: ajaxObj.api_endpoint_app_check_status_tokopedia,
      //   crossDomain: true,
      //   timeout: 90000,
      //   data: JSON.stringify({
      //     token: ajaxObj.api_token
      //   }),
      //   contentType: "application/json; charset=utf-8",
      //   success: function(response) {
      //     if(response.is_active) {
      //       $('li#pch-status-tokopedia').attr('title', 'Active');
      //       $('li#pch-status-tokopedia label span').addClass('pch-text-success');
      //       $('li#pch-status-tokopedia label span').removeClass('pch-text-error');
      //     } else {
      //       $('li#pch-status-tokopedia').attr('title', 'Disruption');
      //       $('li#pch-status-tokopedia label span').addClass('pch-text-error');
      //       $('li#pch-status-tokopedia label span').removeClass('pch-text-success');
      //     }
      //   },
      //   error: function(response, status) {
      //     $('li#pch-status-tokopedia').attr('title', 'Disruption');
      //     $('li#pch-status-tokopedia label span').addClass('pch-text-error');
      //     $('li#pch-status-tokopedia label span').removeClass('pch-text-success');
      //   }
      // });
    }

    // Check Status API Bukalapak
    if($('li#pch-status-bukalapak').length) {
      $('li#pch-status-bukalapak').attr('title', 'Active');
      $('li#pch-status-bukalapak label span').addClass('pch-text-success');
      $('li#pch-status-bukalapak label span').removeClass('pch-text-error');

      // $.ajax({
      //   method: 'POST',
      //   url: ajaxObj.api_endpoint_app_check_status_bukalapak,
      //   crossDomain: true,
      //   timeout: 90000,
      //   data: JSON.stringify({
      //     token: ajaxObj.api_token
      //   }),
      //   contentType: "application/json; charset=utf-8",
      //   success: function(response) {
      //     if(response.is_active) {
      //       $('li#pch-status-bukalapak').attr('title', 'Active');
      //       $('li#pch-status-bukalapak label span').addClass('pch-text-success');
      //       $('li#pch-status-bukalapak label span').removeClass('pch-text-error');
      //     } else {
      //       $('li#pch-status-bukalapak').attr('title', 'Disruption');
      //       $('li#pch-status-bukalapak label span').addClass('pch-text-error');
      //       $('li#pch-status-bukalapak label span').removeClass('pch-text-success');
      //     }
      //   },
      //   error: function(response, status) {
      //     $('li#pch-status-bukalapak').attr('title', 'Disruption');
      //     $('li#pch-status-bukalapak label span').addClass('pch-text-error');
      //     $('li#pch-status-bukalapak label span').removeClass('pch-text-success');
      //   }
      // });
    }

    // Run Crawler
    if($('#pch-run-crawler').length) {
      $('#pch-run-crawler').on('submit', (function(e) {
        e.preventDefault();

        var urls = $('#pch-product-urls').val().split('\n');

        // Remove duplicates
        urls = urls.filter((c, index) => {
          return urls.indexOf(c) === index;
        })

        // Limit 3 urls
        urls = urls.slice(0, 3);

        if($.trim($('#pch-product-urls').val()).length < 1) {
          // Reset Form with Alert
          alert('It looks like your product link is still empty or the format is invalid.');
          $('#pch-product-urls').addClass('pch-input-err');
          $('#pch-product-urls').removeAttr('disabled');

          $('#pch-run-crawler #runCrawler').val('Run MagicTool!');
          $('#pch-run-crawler #runCrawler').attr('type', 'submit');
          $('#pch-run-crawler #runCrawler').addClass('button-primary');
          $('#pch-run-crawler #runCrawler').removeClass('button-disabled');
        } else {
          // Animation Form
          $('#pch-product-urls').attr('disabled', 'disabled');
          $('#pch-run-crawler #runCrawler').val('Loading..');
          $('#pch-run-crawler #runCrawler').attr('type', 'button');
          $('#pch-run-crawler #runCrawler').removeClass('button-primary');
          $('#pch-run-crawler #runCrawler').addClass('button-disabled');

          // Animation Table
          $('#pch-product-list > tbody').html('<tr id="pch-product-loading"><th colspan="5">Wait for the magic to do its job..</th></tr>');

          // List errors
          resetListError();

          // List success
          arrListSuccess = [];

          getProductItem(urls, 0);
        }
      }))
    }

    // Bulk Actions
    if($('#pch-bulk-actions').length) {
      $('#pch-bulk-actions').on('submit', (function(e) {
        e.preventDefault();

        const option = $('#pch-bulk-action-options').find(":selected").val();

        if(option === '') {
          // Reset Form with Alert
          alert('You have to select the bulk action option to perform.');
          $('#pch-bulk-action-options').addClass('pch-input-err');

          $('#pch-bulk-actions #bulkActions').val('Apply');
          $('#pch-bulk-actions #bulkActions').attr('type', 'submit');
          $('#pch-bulk-actions #bulkActions').removeClass('button-disabled');
        } else {
          // Animation Form
          $('#pch-bulk-actions #bulkActions').val('Loading..');
          $('#pch-bulk-actions #bulkActions').attr('type', 'button');
          $('#pch-bulk-actions #bulkActions').addClass('button-disabled');

          if(arrSelectedProducts.length) {
            let indexImporting = 0;

            if(option === 'import') {
              $.each(arrSelectedProducts, function(index, productId) {
                setTimeout(function() {
                  $('#pch-product-list > tbody tr#' + productId + ' button.pch-import-product').click();
                  
                  indexImporting++;
                }, 1500)
              })
            } else if(option === 'delete') {
              $.each(arrSelectedProducts, function(index, productId) {
                setTimeout(function() {
                  $('#pch-product-list > tbody tr#' + productId + ' button.pch-delete-product').click();
                  
                  indexImporting++;
                }, 1500)
              })
            }

            importingIsDone = setInterval(function() {
              if(indexImporting === arrSelectedProducts.length) {
                // Reset Form with Alert
                $('#pch-bulk-action-options option').first().prop('selected', true);
                $('#pch-bulk-action-options').removeClass('pch-input-err');

                $('#pch-select-all-products').prop('checked', false);

                $('#pch-bulk-actions #bulkActions').val('Apply');
                $('#pch-bulk-actions #bulkActions').attr('type', 'submit');
                $('#pch-bulk-actions #bulkActions').removeClass('button-disabled');
  
                clearInterval(importingIsDone);
              }
            }, 1000)
          } else {
            // Reset Form with Alert
            alert("You haven't selected a product for bulk action.");
            $('#pch-bulk-action-options').removeClass('pch-input-err');
            
            $('#pch-bulk-actions #bulkActions').val('Apply');
            $('#pch-bulk-actions #bulkActions').attr('type', 'submit');
            $('#pch-bulk-actions #bulkActions').removeClass('button-disabled');
          }
        }
      }))
    }
  }))
})(jQuery);