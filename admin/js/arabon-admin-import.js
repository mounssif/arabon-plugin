(function( $ ) {
    'use strict';
    $('div#arabon-start-screen input#import_all').on('click', function(e){
        e.preventDefault();
        $('div#arabon-start-screen textarea#messages').show();
        $.ajax({
            url: '/wp-admin/admin-ajax.php',
            type: 'post',
            cache: true,

            data: {
                "action": "arabon_import_all",
            },

            beforeSend: function() {
                console.log('import triggered');
            },
            success: function(response, textStatus, XMLHttpRequest) {
                $('div#arabon-start-screen textarea#messages').prepend(response + "&#xA;")

            },
            error: function(response) {
                console.log(response);
            },
        });
        return false;
    });


})( jQuery );