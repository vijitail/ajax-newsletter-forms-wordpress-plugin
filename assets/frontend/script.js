jQuery(document).ready(function($){

    $('.anf').submit(function(e) {
        
        var serializedData = $(this).serialize();
        e.preventDefault();
        console.log(serializedData);

        var data = {
            action: 'anf_submit',
            nonce: anf.ajax_nonce,
            data: serializedData
        };

        $.ajax({
            method: "POST",
            url: anf.url,
            data: data,
            success: function(data) {
                console.log(data);
            }
        });

    });

});