jQuery( function( $ )  {

    $( '#cat' ).on( 'change', function ()  {

        var cat_selected = $(this).val();
        var data = {
            action : 'filter_post',
            cat : cat_selected
        };

        $.ajax({
            url:VARS.ajax_url,
            method:'POST',
            data:data,
                success:function(response) {
                    $('.js-events').html(response)
                }
        })

    });

});