;
if (!window.jQuery && !window.BXmakerJQueryCheck) {
    window.BXmakerJQueryCheck = true;
    document.write('<' + 'script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></' + 'script>');
}


if (window.frameCacheVars !== undefined) {
    BX.addCustomEvent("onFrameDataReceived", function (json) {
        BXmakerGeoIPDeliveryManager.init();
    });
} else {
    BX.ready(function () {
        BXmakerGeoIPDeliveryManager.init();
    });
}


if (!!window.BXmakerGeoIPDeliveryConstructor === false) {

    var BXmakerGeoIPDeliveryConstructor = function (box) {
        var that = this;
        that.params = {};
        that.box = box;
        that.box.addClass('js-bxmaker__geoip__delivery--init');
        that.value = that.box.find('.js-bxmaker__geoip__delivery-box');

        that.params.key = that.box.attr('data-key');
        that.params.debug = (that.box.attr('data-debug') == 'Y');
        that.params.bSubdomainOn  = (that.box.attr('data-subdomain-on') == 'Y');
        that.params.baseDomain  = (that.box.attr('data-base-domain') || location.hostname );
        that.params.cookiePrefix  = (that.box.attr('data-cookie-prefix') || 'bxmaker.geoip_' );
        that.params.baseDomainCurrent  = null;
        that.params.template = (that.box.attr('data-city') || '');
        that.params.productId = (that.box.attr('data-product-id') || 0);
        that.params.showParent = (that.box.attr('data-show-parent') == 'Y' ? 'Y': 'N');
        that.params.imgShow = (that.box.attr('data-img-show') == 'Y' ? 'Y': 'N');
        that.params.imgWidth = (that.box.attr('data-img-width') || '30');
        that.params.imgHeight = (that.box.attr('data-img-height') || '30');

        that.params.location = (!!that.value.length && !!that.value.attr('data-location') ? that.value.attr('data-location') : '');
        that.params.city = (!!that.value.length && !!that.value.attr('data-city') ? that.value.attr('data-city') : '');



        if (!window.BXmakerDebugGeoIP && ((location.hash == '#BXmakerDebugGeoIP') || that.params.debug)) {
            window.BXmakerDebugGeoIP = true;
            that.log('debug is on');
        }

        that.initEvent();
    };


    BXmakerGeoIPDeliveryConstructor.prototype.log = function () {
        if (window.BXmakerDebugGeoIP) {
            var args = Array.prototype.slice.call(arguments);
            args.unshift('bxmaker:geoip.delivery: ');
            console.log.apply(console, args);
        }
    };
    BXmakerGeoIPDeliveryConstructor.prototype.logError = function () {
        if (window.BXmakerDebugGeoIP) {
            var args = Array.prototype.slice.call(arguments);
            args.unshift('bxmaker:geoip.delivery: ');
            console.error.apply(console, args);
        }
    };

    BXmakerGeoIPDeliveryConstructor.prototype.getBaseDomain = function () {
        var that = this;
        if (that.params.baseDomainCurrent == null) {
            var currentHost = location.hostname.toLowerCase();
            var arBaseDomain = that.params.baseDomain.toLowerCase().split(',');
            that.params.baseDomainCurrent = currentHost;

            for(var i in arBaseDomain)
            {
                if(currentHost.indexOf(arBaseDomain[i]) > -1)
                {
                    that.params.baseDomainCurrent = arBaseDomain[i];
                }
            }
        }
        return that.params.baseDomainCurrent;
    };


    BXmakerGeoIPDeliveryConstructor.prototype.intval = function (num) {
        if (typeof num == 'number' || typeof num == 'string') {
            num = num.toString();
            var dotLocation = num.indexOf('.');
            if (dotLocation > 0) {//Ампутация дробной части
                num = num.substr(0, dotLocation);
            }
            if (isNaN(Number(num))) {
                num = parseInt(num);
            }
            if (isNaN(num)) {
                return 0;
            }
            return Number(num);
        }
        else if (typeof num == 'object' && num.length != null && num.length > 0) {//Непустой массив/объект -> 1
            return 1;
        }
        else if (typeof num == 'boolean' && num === true) {//true -> 1
            return 1;
        }
        return 0;//Чуть что не так - сразу в ноль
    };


    BXmakerGeoIPDeliveryConstructor.prototype.cookie = function (name, value, params) {
        var that = this;
        var d = new Date();
        var name = that.params.cookiePrefix + name;
        var params = params || {};
        var parts = [];
        var currentValue, matches;


        if (value === undefined) {
            matches = document.cookie.match(new RegExp(
                "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
            ));
            currentValue = matches ? decodeURIComponent(matches[1].replace(/\+/g, ' ')) : undefined;
            that.log('cookie get: ' + name + ' = ' + currentValue);
            return currentValue;
        }
        else {
            d.setTime(d.getTime() + ((!!params.expires ? params.expires : 365 ) * 24 * 60 * 60 * 1000));
            parts.push(name + "=" + value);// todo  parts.push(name + "=" + encodeURIComponent(value));
            parts.push("expires=" + d.toUTCString());
            parts.push("path=" + (!!params.path ? params.path : '/' ));
            // !!params.domain && parts.push("domain=" + params.domain);
            parts.push("domain=" + that.getBaseDomain());
            document.cookie = parts.join('; ');
            that.log('cookie: ' + parts.join('; '));
        }
    };


    BXmakerGeoIPDeliveryConstructor.prototype.getJsonFromUrl = function (hashBased) {
        var query;
        if (hashBased) {
            var pos = location.href.indexOf("?");
            if (pos == -1) return [];
            query = location.href.substr(pos + 1);
        } else {
            query = location.search.substr(1);
        }
        var result = {};
        query.split("&").forEach(function (part) {
            if (!part) return;
            part = part.split("+").join(" "); // replace every + with space, regexp-free version
            var eq = part.indexOf("=");
            var key = eq > -1 ? part.substr(0, eq) : part;
            var val = eq > -1 ? decodeURIComponent(part.substr(eq + 1)) : "";
            var from = key.indexOf("[");
            if (from == -1) result[decodeURIComponent(key)] = val;
            else {
                var to = key.indexOf("]", from);
                var index = decodeURIComponent(key.substring(from + 1, to));
                key = decodeURIComponent(key.substring(0, from));
                if (!result[key]) result[key] = [];
                if (!index) result[key].push(val);
                else result[key][index] = val;
            }
        });
        return result;
    };


    BXmakerGeoIPDeliveryConstructor.prototype.initEvent = function () {
        var that = this;

        that.log('init event');

        that.box.on("click", '.js-bxmaker__geoip__delivery-city', function(){
            that.log('trigger: bxmaker.geoip.city.search.start');
            $(document).trigger('bxmaker.geoip.city.search.start');
        });

        // $(document).on('bxmaker.geoip.city.change', function (event, data) {
        //     that.log('event: bxmaker.geoip.city.change');
        //     that.reload();
        // });

        $(document).on('bxmaker.geoip.city.show', function(event, data){
            that.log('event: bxmaker.geoip.city.show');
            that.box.find('.js-bxmaker__geoip__delivery-city').text(data.city);

            if(that.params.location != data.location || that.params.city != data.city)
            {
                that.params.location = data.location;
                that.params.city = data.city;
                that.reload();
            }
            else
            {
                that.box.removeClass('preloader');
            }
        });

        if(!!window.BXmakerGeoIPCity)
        {
            that.box.find('.js-bxmaker__geoip__delivery-city').text(window.BXmakerGeoIPCity.getCity());


            if(that.params.location != window.BXmakerGeoIPCity.getLocation() || that.params.city != window.BXmakerGeoIPCity.getCity())
            {
                that.params.location = window.BXmakerGeoIPCity.getLocation();
                that.params.city = window.BXmakerGeoIPCity.getCity();
                that.reload();
            }
            else
            {
                that.box.removeClass('preloader');
            }
        }
    };

    BXmakerGeoIPDeliveryConstructor.prototype.getKey = function () {
        var that = this;
        return that.params.key;
    };

    BXmakerGeoIPDeliveryConstructor.prototype.setProductId = function (productId) {
        var that = this;
        that.log('set product id - ' + productId);
        that.params.productId = productId;
        that.reload();
    };

    BXmakerGeoIPDeliveryConstructor.prototype.reload = function () {
        var that = this;

        that.log('trigger:bxmaker.geoip.delivery.reload.before');
        $(document).trigger('bxmaker.geoip.delivery.reload.before');

        that.box.addClass('preloader');

        var data = {};

        for(var i in that.params)
        {
            data[i] = that.params[i];
        }

        data['sessid'] = BX.bitrix_sessid();
        data['module'] = 'bxmaker.geoip';
        data['method'] = 'getDelivery';

        $.ajax({
            type: 'POST',
            url: '/',
            dataType: 'json',
            data: data,
            error: function (r) {

                that.log(r, true);

                that.box.removeClass('preloader');

                var error = {
                    'error': {
                        code: 'ajax_error',
                        msg: 'Error  connection to server',
                        more: r
                    }
                };

                that.log('trigger:bxmaker.geoip.delivery.reload.after', error);
                $(document).trigger('bxmaker.geoip.delivery.reload.after', error);
            },
            success: function (r) {
                if (!!r.response) {
                    that.log(' getDelivery: success');
                    that.box.find('.js-bxmaker__geoip__delivery-box').replaceWith(r.response.html);

                    that.value = that.box.find('.js-bxmaker__geoip__delivery-box');
                    that.params.location = (!!that.value.length && !!that.value.attr('data-location') ? that.value.attr('data-location') : '');
                    that.params.city = (!!that.value.length && !!that.value.attr('data-city') ? that.value.attr('data-city') : '');

                }
                else if (!!r.error) {
                    that.logError(' getDelivery: error', r);
                }

                that.box.removeClass('preloader');

                that.log('trigger:bxmaker.geoip.delivery.reload.after', r);
                $(document).trigger('bxmaker.geoip.delivery.reload.after', r);
            }
        })

    };
}


if (!!window.BXmakerGeoIPDeliveryManager === false) {
    (function () {

        var BXmakerGeoIPDelivery = function () {
            var that = this;
            that.itemsKey = {};
        };

        BXmakerGeoIPDelivery.prototype.init = function () {
            var that = this;

            $('.js-bxmaker__geoip__delivery:not(.js-bxmaker__geoip__delivery--init)').each(function () {
                var item = new BXmakerGeoIPDeliveryConstructor($(this));

                that.itemsKey[item.getKey()] = item;
            });
        };

        BXmakerGeoIPDelivery.prototype.getItemByKey = function (code) {
            var that = this;
            return that.itemsKey[code] || false;
        };

        BXmakerGeoIPDelivery.prototype.setProductID = function (productId, code) {
            var that = this;
            if(!!code && !!that.itemsKey[code])
            {
                that.itemsKey[code].setProductId(productId);
            }
            else
            {
                for(var i in that.itemsKey)
                {
                    that.itemsKey[i].setProductId(productId);
                }
            }
        };

        window.BXmakerGeoIPDeliveryManager = new BXmakerGeoIPDelivery();
    })();

}
