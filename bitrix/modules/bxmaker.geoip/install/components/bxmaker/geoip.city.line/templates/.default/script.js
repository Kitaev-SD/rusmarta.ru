;
if (!window.jQuery && !window.BXmakerJQueryCheck) {
    window.BXmakerJQueryCheck = true;
    document.write('<' + 'script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></' + 'script>');
}


if (window.frameCacheVars !== undefined) {
    BX.addCustomEvent("onFrameDataReceived", function (json) {
        if (!window.BXmakerGeoIPCityLineCheck) {
            window.BXmakerGeoIPCityLineCheck = true;
            $('.js-bxmaker__geoip__city__line:not(.js-bxmaker__geoip__city__line--init)').each(function () {
                new BXmakerGeoIPCityLineConstructor($(this));
            });
        }
    });
} else {
    BX.ready(function () {
        if (!window.BXmakerGeoIPCityLineCheck) {
            window.BXmakerGeoIPCityLineCheck = true;
            $('.js-bxmaker__geoip__city__line:not(.js-bxmaker__geoip__city__line--init)').each(function () {
                new BXmakerGeoIPCityLineConstructor($(this));
            });
        }
    });
}


if (!!window.BXmakerGeoIPCityLineConstructor === false) {

    var BXmakerGeoIPCityLineConstructor = function (box) {
        var that = this;
        that.params = {};
        that.box = box;
        that.box.addClass('js-bxmaker__geoip__city__line--init');

        that.params.bQuestionShow = (that.box.attr('data-question-show') == 'Y');
        that.params.bInfoShow = (that.box.attr('data-info-show') == 'Y');
        that.params.key = that.box.attr('data-key');
        that.params.animateTimeout = that.intval(that.box.attr('data-fade-timeout') || 200);
        that.params.searchTimeout = that.intval(that.box.attr('data-tooltip-timeout') || 500);
        that.params.debug = (that.box.attr('data-debug') == 'Y');
        that.params.bSubdomainOn  = (that.box.attr('data-subdomain-on') == 'Y');
        that.params.baseDomain  = (that.box.attr('data-base-domain') || location.hostname );
        that.params.cookiePrefix  = (that.box.attr('data-cookie-prefix') || 'bxmaker.geoip_' );
        that.params.baseDomainCurrent  = null;


        if (!window.BXmakerDebugGeoIP && ((location.hash == '#BXmakerDebugGeoIP') || that.params.debug)) {
            window.BXmakerDebugGeoIP = true;
            that.log('debug is on');
        }

        that.initEvent();
    };


    BXmakerGeoIPCityLineConstructor.prototype.log = function () {
        if (window.BXmakerDebugGeoIP) {
            var args = Array.prototype.slice.call(arguments);
            args.unshift('bxmaker:geoip.city.line: ');
            console.log.apply( console, args);
        }
    };
    BXmakerGeoIPCityLineConstructor.prototype.logError = function () {
        if (window.BXmakerDebugGeoIP) {
            var args = Array.prototype.slice.call(arguments);
            args.unshift('bxmaker:geoip.city.line: ');
            console.error.apply(console, args);
        }
    };

    BXmakerGeoIPCityLineConstructor.prototype.getBaseDomain = function () {
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


    BXmakerGeoIPCityLineConstructor.prototype.intval = function (num) {
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


    BXmakerGeoIPCityLineConstructor.prototype.cookie = function (name, value, params) {
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


    BXmakerGeoIPCityLineConstructor.prototype.getJsonFromUrl = function (hashBased) {
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


    BXmakerGeoIPCityLineConstructor.prototype.initEvent = function () {
        var that = this;

        var bQuestionShow = false;
        var timeout = false;

        that.log('init event');

        // нужно ли показывать вопрос
        if (that.params.bQuestionShow) {
            that.log('question tooltip is on');
            if (that.cookie('question_hide') == 'Y') {
                that.log('question tooltip is hide');
                bQuestionShow = false;
            }
            else {
                that.log('question tooltip not hide');
                that.tooltipQuestionShow();
                bQuestionShow = true;
            }
        }


        that.box
            .on("mouseenter", '.js-bxmaker__geoip__city__line-context', function () {

                if (!!timeout) clearTimeout(timeout);

                that.log('mouseenter');

                if (bQuestionShow) {
                    that.tooltipQuestionShow();
                }
                else {
                    that.tooltipInfoShow();
                }
            })
            .on("mouseleave", '.js-bxmaker__geoip__city__line-context', function () {

                if (!!timeout) clearTimeout(timeout);
                that.log('mouseleave');
                if (bQuestionShow) {
                    timeout = setTimeout($.proxy(that.tooltipQuestionHide, that), that.params.searchTimeout);
                }
                else {
                    timeout = setTimeout($.proxy(that.tooltipInfoHide, that), that.params.searchTimeout);
                }
            })
            .on("click", '.js-bxmaker__geoip__city__line-question-btn-no', function () {
                that.log('question answer no');
                that.cookie('question_hide', 'Y');
                that.tooltipQuestionHide();
                that.log('trigger: bxmaker.geoip.city.search.start');
                $(document).trigger('bxmaker.geoip.city.search.start');
                bQuestionShow = false;
            })
            .on("click", '.js-bxmaker__geoip__city__line-question-btn-yes', function () {
                that.log('question answer yes');
                that.cookie('question_hide', 'Y');
                that.tooltipQuestionHide();
                bQuestionShow = false;
            })
            .on("click", '.js-bxmaker__geoip__city__line-info-btn', function () {
                that.log('info need city change');
                that.tooltipInfoHide();
                that.log('trigger: bxmaker.geoip.city.search.start');
                $(document).trigger('bxmaker.geoip.city.search.start');
            })
            .on("click", '.js-bxmaker__geoip__city__line-name', function () {
                that.log('click by city name');
                that.cookie('question_hide', 'Y');
                that.tooltipQuestionHide();
                that.tooltipInfoHide();
                that.log('trigger: bxmaker.geoip.city.search.start');
                $(document).trigger('bxmaker.geoip.city.search.start');
            });

        $(document).on('bxmaker.geoip.city.show', function(event, data){
            that.log('event: bxmaker.geoip.city.show');
            that.showCity(data.city);
        });

        if(!!window.BXmakerGeoIPCity)
        {
            that.showCity(window.BXmakerGeoIPCity.getCity());
        }


    };

    BXmakerGeoIPCityLineConstructor.prototype.tooltipQuestionShow = function () {
        var that = this;
        that.log('tooltip question show');
        that.box.find('.js-bxmaker__geoip__city__line-question').stop().fadeIn(that.params.animateTimeout);
    };

    BXmakerGeoIPCityLineConstructor.prototype.tooltipQuestionHide = function () {
        var that = this;
        that.log('tooltip question hide');
        that.box.find('.js-bxmaker__geoip__city__line-question').stop().fadeOut(that.params.animateTimeout);
    };

    BXmakerGeoIPCityLineConstructor.prototype.tooltipInfoShow = function () {
        var that = this;
        if (that.params.bInfoShow) {
            that.log('tooltip info show');
            that.box.find('.js-bxmaker__geoip__city__line-info').stop().fadeIn(that.params.animateTimeout);
        }
    };

    BXmakerGeoIPCityLineConstructor.prototype.tooltipInfoHide = function () {
        var that = this;
        that.log('tooltip info hide');
        that.box.find('.js-bxmaker__geoip__city__line-info').stop().fadeOut(that.params.animateTimeout);
    };

    BXmakerGeoIPCityLineConstructor.prototype.showCity = function (city) {
        var that = this;
        that.log('show city');
        that.box.find('.js-bxmaker__geoip__city__line-city').text(city);
    };


}