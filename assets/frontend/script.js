jQuery(document).ready(function($){

    $('.anf').submit(function(e) {
        
        var serializedData = $(this).serialize();
        e.preventDefault();

        var data = {
            action: 'anf_submit',
            nonce: anf.ajax_nonce,
            data: serializedData
        };

        var objExists = typeof window[$(this).attr('data-id')] !== undefined;

        var form_id = $(this).attr("data-id");

        if(objExists && window[form_id].hasOwnProperty('ajax_url')) {
            var ajax_url = window[form_id].ajax_url;
        }

        $.ajax({
            method: "POST",
            url: ajax_url || anf.url,
            data: data,
            success: function(data) {
                if(data.status == 'success') {
                console.log(data);
                    if(objExists && window[form_id].hasOwnProperty('onsuccess')) {
                        window[form_id].onsuccess($);
                    } else {
                        $('.success.message-box').addClass('display');
                        setTimeout(function(){
                            $('.success.message-box').removeClass('display');
                        }, 1500)
                    }
                } else if(data.status == 'error') {
                    if(objExists && window[form_id].hasOwnProperty('onsuccess')) {
                        window[form_id].onerror($);
                    }    
                    else {
                        $('.error.message-box').addClass('display');
                        setTimeout(function(){
                            $('.error.message-box').removeClass('display');
                        }, 1500)
                    }
                } 
            }
        });

    });

});