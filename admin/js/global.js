(function($) {
  if($('#adminmenu li#toplevel_page_pch-welcome ul.wp-submenu li.wp-first-item a').length) {
    $('#adminmenu li#toplevel_page_pch-welcome ul.wp-submenu li.wp-first-item a').html('Welcome');
  }

  $(window).on('load', (function() {
    if($('.pch-checkbox-boolean').length) {
      $('.pch-checkbox-boolean').on('change', (function() {
        var autoSync = "{";
  
        $('.pch-checkbox-boolean').each(function() {
          autoSync += '"' + $(this).attr('name').replace(/\-/g, '_') + '":' + ($(this).is(':checked') ? '1' : '0') + ",";
        })
  
        autoSync += "}";
  
        $('#pch-auto-sync').val(autoSync);
      }))
    }
  
    if($('#pch-remove-url').length) {
      $('#pch-remove-url').on('change', (function() {
        $('#pch-remove-url-value').val(($(this).is(':checked') ? '1' : '0'));
      }))
    }
  }))
})(jQuery);