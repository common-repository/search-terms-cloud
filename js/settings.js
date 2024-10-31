jQuery(function ($) {
    $('#terms_color').colpick({
        layout:'hex',
        submit:0,
        colorScheme:'dark',
        onChange:function(hsb,hex,rgb,fromSetColor) {
            if(!fromSetColor) $('#terms_color').val(hex).css('border-color','#'+hex);
        }
    })
        .keyup(function(){
            $(this).colpickSetColor(this.value);
        });
});
