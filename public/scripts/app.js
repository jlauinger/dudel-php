(function(window, document, $, undefined) {

    "use strict";

    $(function() {
        $('#new-shortlink').click(function() {
            $('#shortlink').val(Math.random().toString(36).substring(5))
        });
    });

})( window, document, jQuery );
