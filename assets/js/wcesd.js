;(function() {
    var total_products = 0;
    $('#enable-for-all-products').on('click', function(e) {
        e.preventDefault();

        var form = $('#enable-for-all-products-div');
            submit = form.find('input[type=submit]'),
            loader = form.find('.regen-sync-loader');
            responseDiv = $('.regen-sync-response');

        submit.attr('disabled', 'disabled');
        loader.show();


        var s_data = {
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

    let enable_for_all = $('#wpuf-wcesd_enable_all_products');
    enable_for_all.on('change', function(e) {
    	var self = $(this);
		let postbox = $('.postbox');

    	if ( self.is(':checked') ) {
    		postbox.fadeIn();
    	} else {
    		postbox.fadeOut();
    	}
    });

    if ( ! enable_for_all.is(':checked') ) {
        $('.postbox').hide();
    }

}());
