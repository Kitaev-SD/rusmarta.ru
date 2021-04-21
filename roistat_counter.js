function getCookie(name) {
  var matches = document.cookie.match(new RegExp(
    "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
  ));
  return matches ? decodeURIComponent(matches[1]) : undefined;
}

function setCookie(name, value, props) {
    props = props || {}
    var exp = props.expires
    if (typeof exp == "number" && exp) {
        var d = new Date()
        d.setTime(d.getTime() + exp*1000)
        exp = props.expires = d
    }
    if(exp && exp.toUTCString) { props.expires = exp.toUTCString() }
    value = encodeURIComponent(value)
    var updatedCookie = name + "=" + value
    for(var propName in props){
        updatedCookie += "; " + propName
        var propValue = props[propName]
       if(propValue !== true){ updatedCookie += "=" + propValue }
    }
    document.cookie = updatedCookie
}

/*$('header .telephone p:contains("+7 (495) 268-14-57")').each(function(){
    $(this).html($(this).html().replace(/\+7 \(495\) 268\-14\-57/gi,'<span class="roistat-phone">+7 (495) 268-14-57</span>'));
});

var message = '';
var LiveTex = {    
    onLiveTexReady: function() {
        // код, выполняемый после инициализации LiveTex Client API
        console.log('Livetex инициализирован!');        
        $('textarea[name="message"]').bind('input propertychange', function() {
            message = this.value;
        });
        $('button.lt-bttn.lt-main-color').click(function() {
            var name    = $('input[name="name"].lt-i-label__input').val();            
            console.log(message);           
            var emailPhone = '';
            if ($('input[name="emailPhone"].lt-i-label__input').length) {
                emailPhone = $('input[name="emailPhone"]').val();
            }            
            if (emailPhone.indexOf('@') != -1) {
                if(typeof name != 'undefined' && name != "" && typeof emailPhone != 'undefined' && emailPhone != "" && message != "" && typeof message != 'undefined') {
                    $.ajax({
                        url: 'https://rusmarta.ru/roistat.php?',
                        type: 'GET',
                        data : {
                            key : 'ODkzODM6NjcxMjQ6MThlOThkOGE0YTFlMDY3ZWRjNDFhMDgzOTlkN2IzZGU=',
                            name : name,                            
                            comment : message,
                            livetex : true,
                            roistat : getCookie('roistat_visit'),
                            email : emailPhone,
                            fields : {
                                form : 'LiveTex - Chat'
                            }
                        }
                    });
                    roistat.event.send('livetex_chat');
                    // roistatGoal.reach({leadName: 'Лид с виджета Livetex', name: name, text: message, email: emailPhone, fields: {
                        // form_type: 'livetex_chat'
                    // }});
                }
            }
            else {
                if(typeof name != 'undefined' && name != "" && typeof emailPhone != 'undefined' && emailPhone != "" && message != "" && typeof message != 'undefined') {
                
                    $.ajax({
                        url: 'https://rusmarta.ru/roistat.php?',
                        type: 'GET',
                        data : {
                            key : 'ODkzODM6NjcxMjQ6MThlOThkOGE0YTFlMDY3ZWRjNDFhMDgzOTlkN2IzZGU=',
                            name : name,    
                            livetex : true,
                            comment : message,
                            roistat : getCookie('roistat_visit'),
                            phone : emailPhone,
                            fields : {
                                form : 'LiveTex - Chat'
                            }
                        }
                    });
                    roistat.event.send('livetex_chat');
                    // roistatGoal.reach({leadName: 'Лид с виджета Livetex', name: name, text: message, phone: emailPhone, fields: {
                        // form_type: 'livetex_chat'
                    // }});
                }
            }
            
        });
        
        
        function onXWindowShown() {
          console.log('X-Widget показано');
        }

        var descriptor = LiveTex.addEventListener( LiveTex.Event.X_WINDOW_SHOWN, onXWindowShown);

        function onOfflineMessageSent(event) {
            roistat.event.send('livetex_form');
        }
        var descriptor = LiveTex.addEventListener(LiveTex.Event.OFFLINE_MESSAGE_SENT, onOfflineMessageSent);
 
    }
};

/*
//Events
window.roistatVisitCallback = function (visitId) {
    <?php
    $events = array(
        'href' => array(
            array('reg' => '/personal\/cart/gi', 'event' => 'personal_cart'),
            array('reg' => '/personal\/order\/make\/$/gi', 'event' => 'personal_order_make'),
            array('reg' => '/personal\/order\/make\/\?ORDER_ID/gi', 'event' => 'order'),
        ),
        'click' => array(
            array('sel' => '[name="add2basket"]', 'event' => 'basket'),
            array('sel' => '[data-action="under_order"]', 'event' => 'onrequest_click'),
            array('sel' => '.checkout', 'event' => 'checkout'),
            array('sel' => '.lt-xbutton', 'event' => 'livetex_call_open'),
            array('sel' => '.cheaper_anch', 'event' => 'cheaper_click'),
            array('sel' => '.subscribe_anch', 'event' => 'announce_click'),
            array('sel' => '.callback_anch', 'event' => 'callback_click'),
            array('sel' => '.boc_anch', 'event' => 'fast_click_d'),
            array('sel' => '.boc_anch_cart', 'event' => 'fast_click_b'),
            array('sel' => '#boc_cart_btn', 'event' => 'fasct_form_b')
        ),
    );
    foreach($events['href'] as $href) {
    ?>
        if(location.href.match(<?php echo $href['reg'];?>))
            setTimeout(function(){
                roistat.event.send('<?php echo $href['event'];?>');
            },1000);

    <?
    }
    foreach($events['click'] as $click) {
    ?>
        $(document).on('click','<?php echo $click['sel'];?>',function(ev){
            roistat.event.send('<?php echo $click['event'];?>');
        });
    <?
    }
    ?>
}
//onrequest_form


$(document).on('mousedown','.popup-window-content button.btn_buy',function(){
    if($(this).attr('id').match(/under_order/gi)) {
        roistat.event.send('onrequest_form');
    }
    else if($(this).attr('id').match(/cheaper/gi)) {
        roistat.event.send('cheaper_form');
    }
    else if($(this).attr('id').match(/callback/gi)) {
        roistat.event.send('callback_form');
    }
    else if($(this).attr('id').match(/boc_bx/gi)) {
        roistat.event.send('fast_form_d');
    }
    else if($(this).parent().parent().attr('id').match(/catalogSubscribe_subscribe/gi)) {
        roistat.event.send('announce_form');
    }

});


$(document).on('mousedown','.ppp',function(){
    roistat.event.send('post');
});

$(document).on('click', '.lt-xbutton-bttn.lt-xbutton_call', function() {
  var phone = $('.lt-xbutton-input').val();
  if (typeof phone != 'undefined' && phone != "") {  
        $.ajax({
            url: 'https://rusmarta.ru/roistat.php?',
            type: 'GET',
            data : {
                key : 'ODkzODM6NjcxMjQ6MThlOThkOGE0YTFlMDY3ZWRjNDFhMDgzOTlkN2IzZGU=',
                name : name,                            
                comment : message,
                roistat : getCookie('roistat_visit'),
                livetex : true,
                email : phone,
                fields : {
                    form : 'LiveTex - Backcall'
                }
            }
        });  
        roistat.event.send('livetex_call');
  }                
});

*/
(function(w, d, s, h, id) {
    w.roistatProjectId = id; w.roistatHost = h;
    var p = d.location.protocol == "https:" ? "https://" : "http://";
    var u = /^.*roistat_visit=[^;]+(.*)?$/.test(d.cookie) ? "/dist/module.js" : "/api/site/1.0/"+id+"/init";
    var js = d.createElement(s); js.charset="UTF-8"; js.async = 1; js.src = p+h+u; var js2 = d.getElementsByTagName(s)[0]; js2.parentNode.insertBefore(js, js2);
})(window, document, 'script', 'cloud.roistat.com', '9f4ad8a4374d7bf79c7198033ec9efd2');

setCookie('rs_test_init_0', 'roistat_init_step_0');
onRoistatAllModulesLoaded = function() {
    setCookie('rs_test_init_1', 'roistat_init_step_1');

}