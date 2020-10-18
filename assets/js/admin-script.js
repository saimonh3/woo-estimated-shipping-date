;(function($) {
    var total_products = 0;

    $('#enable-for-all-products').on('click', function(e) {
        e.preventDefault();

        const form = $('#enable-for-all-products-div');
        const submit = form.find('input[type=submit]');
        const loader = form.find('.regen-sync-loader');
        const responseDiv = $('.regen-sync-response');

        submit.attr('disabled', 'disabled');
        loader.show();

        const s_data = {
            nonce  : $('#my_nonce').val(),
            limit : $('#limit').val(),
            offset : $('#offset').val(),
            action : 'enabled_for_all_products',
            total_products : total_products
        };

        $.post( wcesd.ajaxurl, s_data, function(resp) {
            if ( resp.success ) {
                if( resp.data.total_products != 0 ){
                    total_products = resp.data.total_products;
                }

                completed = (resp.data.done*100)/total_products;
                completed = Math.round(completed);

                $('#regen-pro').width(completed+'%');
                if(!$.isNumeric(completed)){
                    $('#regen-pro').html('Finished');
                    $('#regen-pro').width('100%');
                }else{
                    $('#regen-pro').html(completed+'%');
                }

                $('#progressbar').show();

                responseDiv.html( '<span>' + resp.data.message +'</span>' );

                if ( resp.data.done != 'All' ) {
                    form.find('input[name="offset"]').val( resp.data.offset );
                    submit.trigger('click');
                    return;
                } else {
                    submit.removeAttr('disabled');
                    loader.hide();
                }
            }
        });
    });

    const enable_for_all = $('#wpuf-wcesd_settings\\[wcesd_enable_all_products\\]');
    const enable_date_range = $('#wpuf-wcesd_settings\\[wc_esd_enable_date_range\\]');

    enable_for_all.on('change', function(e) {
        const self = $(this);
        const postbox = $('.postbox');

        if ( self.is(':checked') ) {
            postbox.fadeIn();
        } else {
            postbox.fadeOut();
        }
    });

    enable_date_range.on('change', function(e) {
        const self = $(this);
        const date_range_gap = $('.wc_esd_date_range_gap');

        if ( self.is(':checked') ) {
            date_range_gap.fadeIn();
        } else {
            date_range_gap.fadeOut();
        }
    });

    if ( ! enable_date_range.is(':checked') ) {
        $('.wc_esd_date_range_gap').hide();
    }

    if ( ! enable_for_all.is(':checked') ) {
        $('.postbox').hide();
    }
}(jQuery));