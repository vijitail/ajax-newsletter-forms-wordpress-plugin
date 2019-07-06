
jQuery(document).ready(function($){
    $('.code-editor').each(function() {
        wp.codeEditor.initialize($(this), cm_settings);
    });
});
