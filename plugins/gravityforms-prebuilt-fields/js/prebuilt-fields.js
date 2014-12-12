jQuery(function($) {

    var gf_prebuilt_fields = $('table#gf_prebuilt_fields');

    gf_prebuilt_fields.on( "change", "select#gf_prebuilt_fields_type", function(){
        var optionSelected = $(this).find("option:selected");
        var valueSelected  = optionSelected.val();
        if ( valueSelected == 'text' ){
            gf_prebuilt_fields.find('#gf_prebuilt_fields_array_group').addClass('hidden');
            gf_prebuilt_fields.find('#gf_prebuilt_fields_text_group').removeClass('hidden');
        }else if (valueSelected == 'checkbox' || valueSelected == 'dropdown' ){
            gf_prebuilt_fields.find('#gf_prebuilt_fields_array_group').removeClass('hidden');
            gf_prebuilt_fields.find('#gf_prebuilt_fields_text_group').addClass('hidden');
        }
    });

    gf_prebuilt_fields.on( "click", "a.add-item", function() {
        var newInput = $( this ).parent().clone().find( 'input' ).val('').parent();
        $(this).parent().after( newInput );
    });
 
    gf_prebuilt_fields.on( "click", "a.rm-item", function() {
        var parent_count = $(this).parent().siblings('div').length;
        if ( parent_count >=1 ){
           $(this).parent().remove();
        }
    });
   
});