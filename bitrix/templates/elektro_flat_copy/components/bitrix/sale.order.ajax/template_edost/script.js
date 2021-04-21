$(window).load(function() {

    jQuery.validator.addMethod("checkMask", function(value, element) {
        return /\+\d{1} \(\d{3}\) \d{3}-\d{4}/g.test(value);
    });

    var inputPhone =  $('.ORDER_PROP_3');

    inputPhone.inputmask('mask', {'mask': '+7 (999) 999-9999' });

    $('#ORDER_FORM').validate(
        {
            rules: {
                ORDER_PROP_3: {
                    required: true,
                    checkMask: true
                },
                ORDER_PROP_1: {
                    required: true
                }
            },
            messages: {
                ORDER_PROP_3: {
                    required: "Телефон обязателен для заполения",
                    checkMask: 'Неверный формат телефона'
                },

                ORDER_PROP_1: {
                    required: "Ф.И.О. обязателеньно для заполения"
                }
            }
        }
    );

    var inputProfile = $('#ID_PROFILE_ID');

    inputProfile.on('click', function(){
        inputPhone.rules('remove', 'checkMask');

    });

});


BX.addCustomEvent('onAjaxSuccess', function(){
    var inputPhone =  $('.ORDER_PROP_3');
    inputPhone.inputmask('mask', {'mask': '+7 (999) 999-9999' }); 

    //$('#ORDER_FORM').validate(
    //    {
    //        rules: {
    //            ORDER_PROP_3: {
    //                required: true,
    //                minlength: 22
    //            },
    //            ORDER_PROP_1: {
    //                required: true
    //            }
    //        },
    //        messages: {
    //            ORDER_PROP_3: {
    //                required: "Телефон обязателен для заполения",
    //                minlength: 'Неверный формат телефона',
    //                maxlength: 'Неверный формат телефона'
    //            },
    //
    //            ORDER_PROP_1: {
    //                required: "Ф.И.О. обязателеньно для заполения"
    //            }
    //        }
    //    }
    //);
    //
    //var originRegExpString = $('input[name="FORMS_VALIDATE_PHONE_MASK"]').val();
    //var regExpString = originRegExpString.substr(1, originRegExpString.indexOf('$')-1);
    //var regExpPatter = new RegExp(regExpString, 'g');
    //
    //if( inputPhone.val().match( regExpPatter) == null ){
    //    inputPhone.rules('add', {
    //        minlength:21
    //    })
    //}else{
    //    inputPhone.rules('remove', 'minlength');
    //    console.log('remove');
    //}
});


