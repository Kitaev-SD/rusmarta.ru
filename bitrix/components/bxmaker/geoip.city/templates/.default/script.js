;
if (!window.jQuery && !window.BXmakerJQueryCheck) {
    window.BXmakerJQueryCheck = true;
    document.write('<' + 'script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></' + 'script>');
}


if (window.frameCacheVars !== undefined) {
    BX.addCustomEvent("onFrameDataReceived", function (json) {
        window.BXmakerGeoIPCity.init($('.js-bxmaker__geoip__city'));
    });
} else {
    BX.ready(function () {
        window.BXmakerGeoIPCity.init($('.js-bxmaker__geoip__city'));
    });
}


if (!!window.BXmakerGeoIPCity === false) {

    (function () {


        var BXmakerGeoIPCityConstructor = function () {
            var that = this;
            that.params = {};
            that.box = null;
            that.popup = null;
            that.bInit = false;
        };

        BXmakerGeoIPCityConstructor.prototype.init = function (box) {

            var that = this;
            if (!box.length) return false;
            if (box.filter('.js-bxmaker__geoip__city--init').length) return false;

            that.params = {};
            that.box = box.eq(0);
            that.box.addClass('js-bxmaker__geoip__city--init');
            that.popup = that.box.find('.js-bxmaker__geoip__popup').detach();

            that.params.debug = (that.box.attr('data-debug') == 'Y');
            that.params.bSubdomainOn = (that.box.attr('data-subdomain-on') == 'Y');
            that.params.baseDomain = (that.box.attr('data-base-domain') || location.hostname );
            that.params.subDomain = (that.box.attr('data-sub-domain') || location.hostname );
            that.params.cookiePrefix = (that.box.attr('data-cookie-prefix') || 'bxmaker.geoip_' );
            that.params.baseDomainCurrent = null; // current base domain defined by js

            that.params.reload = (that.box.attr('data-reload') == 'Y');
            that.params.key = that.box.attr('data-key');
            that.params.bSearchShow = (that.box.attr('data-search-show') == 'Y');
            that.params.bFavoriteShow = (that.box.attr('data-favorite-show') == 'Y');
            that.params.bUseYandex = (that.box.attr('data-use-yandex') == 'Y');
            that.params.bUseYandexSearch = (that.box.attr('data-use-yandex-search') == 'Y');
            that.params.arYandexSearchSkipWords = (!!that.box.attr('data-yandex-search-skip-words') ? that.box.attr('data-yandex-search-skip-words').split(',') : []);
            that.params.msgEmptyResult = (that.box.attr('data-msg-empty-result') || 'not found');
            that.params.animateTimeout = that.intval(that.box.attr('data-animate-timeout') || 300);
            that.params.searchTimeout = that.intval(that.box.attr('data-search-timeout') || 500);


            if (!window.BXmakerDebugGeoIP && ((location.hash == '#BXmakerDebugGeoIP') || that.params.debug)) {
                window.BXmakerDebugGeoIP = true;
                that.log('geoip debug is on');
            }

            that.initEvent();
            that.bInit = true;
            that.showCity();

            if (that.params.bUseYandex || that.params.bUseYandexSearch) {
                window.BXmakerGeoIPYandexGeo.init();
            }
            if (!that.params.bUseYandex) {
                that.checkRedirect();
            }


        };

        BXmakerGeoIPCityConstructor.prototype.isInit = function () {
            return this.bInit;
        };

        BXmakerGeoIPCityConstructor.prototype.log = function () {
            if (window.BXmakerDebugGeoIP) {
                var args = Array.prototype.slice.call(arguments);
                args.unshift('bxmaker:geoip.city: ');
                console.log.apply(console, args);
            }
        };
        BXmakerGeoIPCityConstructor.prototype.logError = function () {
            if (window.BXmakerDebugGeoIP) {
                var args = Array.prototype.slice.call(arguments);
                args.unshift('bxmaker:geoip.city: ');
                console.error.apply(console, args);
            }
        };

        BXmakerGeoIPCityConstructor.prototype.getBaseDomain = function () {
            var that = this;
            if (that.params.baseDomainCurrent == null) {
                var currentHost = location.hostname.toLowerCase();
                var arBaseDomain = that.params.baseDomain.toLowerCase().split(',');
                that.params.baseDomainCurrent = currentHost;

                for (var i in arBaseDomain) {
                    if (currentHost.indexOf(arBaseDomain[i]) > -1) {
                        that.params.baseDomainCurrent = arBaseDomain[i];
                    }
                }
            }
            return that.params.baseDomainCurrent;
        };


        BXmakerGeoIPCityConstructor.prototype.intval = function (num) {
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

        BXmakerGeoIPCityConstructor.prototype.cookie = function (name, value, params) {
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

        BXmakerGeoIPCityConstructor.prototype.getJsonFromUrl = function (hashBased) {
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

        BXmakerGeoIPCityConstructor.prototype.initEvent = function () {
            var that = this;
            var searchTimeout = false;

            // user locatin is defined
            $(document).on('bxmaker.geoip.city.yandex.defined', function (event, params) {
                that.log('save user location defined by yandex');
                that.selectLocation(false, params);
                that.cookie('yandex_location_defined', 'Y');
                that.cookie('yandex_location_defined_check', '1');
            });

            //find user location by yandex
            $(document).on('bxmaker.geoip.yandex.geo.loaded', function () {

                if (!that.params.bUseYandex) {
                    that.cookie('yandex_location_defined_check', '0');
                    return true;
                }

                //check definde user location by yandex early
                if (that.cookie('yandex_location_defined') == 'Y' && that.cookie('yandex_location_defined_check') == '1') {
                    that.checkRedirect();
                    return true;
                }

                that.log('Yandex geocoder find user location');

                window.BXmakerGeoIPYandexGeo.getConnection().geolocation.get({
                    provider: 'yandex',
                    timeout: 5000,
                    autoReverseGeocode: true
                }).then(function (res) {

                    that.log('Yandex geocoder success', res);

                    var boundedBy = res.geoObjects.get(0).properties.get('boundedBy');
                    var responseItem = res.geoObjects.get(0).properties.get('metaDataProperty').GeocoderMetaData;
                    var responseItemAddress = responseItem.Address.Components;
                    var item = {};
                    var bSkip = false;


                    item = {
                        'location': responseItem.id,
                        'city': '',
                        'city_id': responseItem.id,
                        'country': '',
                        'country_id': 0,
                        'region': '',
                        'region_id': 0,
                        'area': '',
                        'zip': '000000',
                        'range': 0,
                        'lng': 0,
                        'lat': 0,
                        'yandex': 1
                    };

                    for (var ai in responseItemAddress) {
                        switch (responseItemAddress[ai].kind) {
                            case 'locality':
                            {
                                item['city'] = responseItemAddress[ai].name;
                                break;
                            }
                            case 'area':
                            {
                                item['area'] = responseItemAddress[ai].name;
                                break;
                            }
                            case 'province':
                            {
                                item['region'] = responseItemAddress[ai].name;
                                break;
                            }
                            case 'country':
                            {
                                item['country'] = responseItemAddress[ai].name;
                                break;
                            }
                        }
                    }

                    bSkip = false;
                    for (var ci = 0; ci < that.params.arYandexSearchSkipWords.length; ci++) {
                        if (item['city'].indexOf(that.params.arYandexSearchSkipWords[ci].replace(/^\s+/, '').replace(/\s+$/)) != -1) {
                            that.log('Skip location ' + item['city'] + ': skip word = ' + that.params.arYandexSearchSkipWords[ci]);
                            bSkip = true;
                        }
                    }

                    item['lng'] = (boundedBy[1][0] - boundedBy[0][0]);
                    item['lat'] = (boundedBy[1][1] - boundedBy[0][1]);

                    if (responseItem.kind == 'locality' || (responseItem.kind == 'province' && item['city'] == item['region'])) {
                        if (!bSkip) {
                            that.log('location is defined');
                            that.log('trigger:bxmaker.geoip.city.yandex.defined', item);
                            $(document).trigger('bxmaker.geoip.city.yandex.defined', item);
                        }
                    }
                    else {
                        that.logError('location is not defined');
                    }


                }, function (e) {
                    that.logError('location is not defined');
                });
            });

            $(document).on('bxmaker.geoip.city.search.start', function () {
                that.popupShow();
            });

            $(document).on('click', '.js-bxmaker__geoip__city-name-global', function () {
                that.popupShow();
            });


            // click po knopke zakryt ocno dlya smeny goroda
            that.popup.on("click", '.js-bxmaker__geoip__popup-close', function () {
                that.log('click by close popup btn');
                that.popupHide();
            });

            //click po fonu
            that.popup.on("click", '.js-bxmaker__geoip__popup-background', function () {
                that.log('click by popup background');
                that.popupHide();
            });

            //clear input
            that.popup.on("click", '.js-bxmaker__geoip__popup-search-clean', function () {
                if ($(this).hasClass('preloader')) return false;
                that.log('Search input clean');
                that.popup.find('input[name="city"]').val('').focus();
                that.popup.find('.js-bxmaker__geoip__popup-search-options').hide();
            });

            that.popup.on("click", '.js-bxmaker__geoip__popup-option', function () {
                var item = $(this);

                that.popup.find('.js-bxmaker__geoip__popup-search-options').hide();

                if (that.params.bUseYandexSearch) {
                    that.popup.find('input[name="city"]').val(item.text()).keyup();
                }
                else {
                    that.selectLocation(item.attr('data-id'));
                    that.popupHide();
                }
            });

            that.popup.on("keyup", 'input[name="city"]', function () {

                var adres = $(this).val().trim();

                if (adres.length < 2) return;
                if (!!searchTimeout) clearTimeout(searchTimeout);

                searchTimeout = setTimeout(function () {

                    that.log('Search adress:' + adres);
                    that.popup.find('.js-bxmaker__geoip__popup-search-clean').addClass('preloader');
                    that.popup.find('.js-bxmaker__geoip__popup-search-search').hide();

                    that.search(adres, function (r) {

                        that.log('count search results:' + r.response.count);

                        that.popup.find('.js-bxmaker__geoip__popup-search-clean').removeClass('preloader');

                        var searchOptions = that.popup.find('.js-bxmaker__geoip__popup-search-options');
                        searchOptions.empty();


                        if (!!r.response) {
                            if (r.response.count <= 0) {
                                searchOptions.append($('<div class="bxmaker__geoip__popup-search-option bxmaker__geoip__popup-search-option--empty" >' + that.params.msgEmptyResult + '</div>'));
                            }
                            else {
                                for (var i = 0; i < r.response.count; i++) {
                                    var item = r.response.items[i];

                                    var itemName = ('<span>' + item.city + '</span>' + (!!item.district ? ', ' + item.district : '') + (!!item.region && item.region != item.city ? ', ' + item.region : '') + (!!item.country ? ', ' + item.country : ''));
                                    var itemHtml = '<div class="bxmaker__geoip__popup-search-option js-bxmaker__geoip__popup-search-option" ';

                                    itemHtml += (' data-location="' + item.location + '" ');
                                    itemHtml += (' data-city="' + item.city + '" ');
                                    itemHtml += (' data-city_id="' + item.city_id + '" ');
                                    itemHtml += (' data-country="' + item.country + '" ');
                                    itemHtml += (' data-country_id="' + item.country_id + '" ');
                                    itemHtml += (' data-region="' + item.region + '" ');
                                    itemHtml += (' data-region_id="' + item.region_id + '" ');
                                    itemHtml += (' data-area="' + item.area + '" ');
                                    itemHtml += (' data-zip="' + item.zip + '" ');
                                    itemHtml += (' data-range="' + item.range + '" ');
                                    itemHtml += (' data-lng="' + item.lng + '" ');
                                    itemHtml += (' data-lat="' + item.lat + '" ');
                                    itemHtml += (' data-yandex="' + item.yandex + '" ');
                                    itemHtml += ' >';

                                    itemHtml += itemName;
                                    itemHtml += '</div>';

                                    searchOptions.append($(itemHtml));
                                }
                            }
                        }
                        else if (!!r.error) {
                            searchOptions.append('<div class="bxmaker__geoip__popup-search-option bxmaker__geoip__popup-search-option--empty" >' + r.error.msg + '</div>');
                        }

                        searchOptions.show();


                    }, function (r) {
                        that.logError('Error search adress:', r);

                        that.popup.find('.js-bxmaker__geoip__popup-search-clean').removeClass('preloader');
                        that.popup.find('.js-bxmaker__geoip__popup-search-search').empty().append($('<div class="bxmaker__geoip__popup-search-option bxmaker__geoip__popup-search-option--empty" >' + that.params.msgEmptyResult + '</div>'));
                    });
                }, that.params.searchTimeout);
            });

            that.popup.on("click", '.js-bxmaker__geoip__popup-search-option', function () {
                var item = $(this);

                that.popup.find('input[name="city"]').val(item.find('span').text());
                that.popup.find('.js-bxmaker__geoip__popup-search-options').hide();

                if (that.params.bUseYandexSearch) {
                    // сохраняем на сервере
                    that.selectLocation(false, {
                        location: item.attr('data-location'),
                        city: item.attr('data-city'),
                        city_id: item.attr('data-city_id'),
                        region: item.attr('data-region'),
                        region_id: item.attr('data-region_id'),
                        country: item.attr('data-country'),
                        country_id: item.attr('data-country_id'),
                        area: item.attr('data-area'),
                        zip: item.attr('data-zip'),
                        range: item.attr('data-range'),
                        lng: item.attr('data-lng'),
                        lat: item.attr('data-lat'),
                        yandex: item.attr('data-yandex')
                    });
                }
                else {
                    that.selectLocation(item.attr('data-location'));
                }
                //that.popupHide();
            });

            that.popup.on("click", '.btn_buy.popdef', function () {
                that.popupHide();
            });
        };

        BXmakerGeoIPCityConstructor.prototype.popupHide = function () {
            var that = this;
            that.log('trigger:bxmaker.geoip.popup.hide.before');
            $(document).trigger('bxmaker.geoip.popup.hide.before');

            that.log('popup hide');
            that.popup.find('.js-bxmaker__geoip__popup-background').animate({'opacity': '0'}, that.params.animateTimeout);
            that.popup.find('.js-bxmaker__geoip__popup-content').animate({'opacity': '0'}, {
                duration: that.params.animateTimeout,
                complete: function () {
                    that.popup.hide();
                    that.popup.detach();
                    that.log('trigger:bxmaker.geoip.popup.hide.after', {
                        'popup': that.popup
                    });
                    $(document).trigger('bxmaker.geoip.popup.hide.after', {
                        'popup': that.popup
                    });
                }
            });
        };

        BXmakerGeoIPCityConstructor.prototype.popupShow = function () {
            var that = this;
            that.log('trigger:bxmaker.geoip.popup.show.before');
            $(document).trigger('bxmaker.geoip.popup.show.before');

            that.log('popup show');

            $('body').append(that.popup);
            that.popup.show();
            that.popup.find('.js-bxmaker__geoip__popup-background').animate({'opacity': '1'}, that.params.animateTimeout);
            that.popup.find('.js-bxmaker__geoip__popup-content').animate({'opacity': '1'}, {
                duration: that.params.animateTimeout,
                complete: function () {
                    that.log('trigger:bxmaker.geoip.popup.show.after', {
                        'popup': that.popup
                    });
                    $(document).trigger('bxmaker.geoip.popup.show.after', {
                        'popup': that.popup
                    });
                }
            });
        };

        BXmakerGeoIPCityConstructor.prototype.search = function (query, success, error) {
            var that = this;

            that.log('trigger:bxmaker.geoip.search.before', {
                'query': query,
                'success': success,
                'error': error
            });
            $(document).trigger('bxmaker.geoip.search.before', {
                'query': query,
                'success': success,
                'error': error
            });

            if (that.params.bUseYandexSearch) {
                that.log('search in yandex - ' + query);


                window.BXmakerGeoIPYandexGeo.getConnection().geocode(query, {
                        'kind': 'locality',
                        'results': 100,
                        'json': true
                    })
                    .then(function (result) {

                        that.log('Yandex geocoder success', result);

                        var collection = result.GeoObjectCollection;
                        var items = [];

                        var responseItems = result.GeoObjectCollection.featureMember;
                        var responseItem = false;
                        var responseItemAddress = [];
                        var item = {};
                        var bSkip = false;
                        var arLngLat = [0, 0];

                        for (var i in responseItems) {
                            responseItem = responseItems[i];
                            responseItemAddress = responseItem.GeoObject.metaDataProperty.GeocoderMetaData.Address.Components;

                            if (responseItem.GeoObject.metaDataProperty.GeocoderMetaData.kind != 'locality' && responseItem.GeoObject.metaDataProperty.GeocoderMetaData.kind != 'province') continue;

                            item = {
                                'location': responseItem.GeoObject.metaDataProperty.GeocoderMetaData.id,
                                'city': responseItem.GeoObject.name,
                                'city_id': responseItem.GeoObject.metaDataProperty.GeocoderMetaData.id,
                                'country': '',
                                'country_id': 0,
                                'region': '',
                                'region_id': 0,
                                'area': '',
                                'zip': '000000',
                                'range': 0,
                                'lng': 0,
                                'lat': 0,
                                'yandex': 1
                            };

                            bSkip = false;
                            for (var ci = 0; ci < that.params.arYandexSearchSkipWords.length; ci++) {
                                if (item['city'].indexOf(that.params.arYandexSearchSkipWords[ci].replace(/^\s+/, '').replace(/\s+$/)) != -1) {
                                    that.log('Skip location ' + item['city'] + ': skip word = ' + that.params.arYandexSearchSkipWords[ci]);
                                    bSkip = true;
                                }
                            }

                            if (bSkip) continue;

                            arLngLat = responseItem.GeoObject.Point.pos.split(' ');
                            item['lng'] = arLngLat[0];
                            item['lat'] = arLngLat[1];

                            for (var ai in responseItemAddress) {
                                switch (responseItemAddress[ai].kind) {
                                    case 'locality':
                                    {
                                        item['city'] = responseItemAddress[ai].name;
                                        break;
                                    }
                                    case 'area':
                                    {
                                        item['area'] = responseItemAddress[ai].name;
                                        break;
                                    }
                                    case 'province':
                                    {
                                        item['region'] = responseItemAddress[ai].name;
                                        break;
                                    }
                                    case 'country':
                                    {
                                        item['country'] = responseItemAddress[ai].name;
                                        break;
                                    }
                                }
                            }

                            items.push(item);
                        }

                        items.splice(10);

                        success({
                            response: {
                                items: items,
                                count: items.length
                            }
                        });

                        that.log('trigger:bxmaker.geoip.search.after', {
                            response: {
                                items: items,
                                count: items.length
                            }
                        });
                        $(document).trigger('bxmaker.geoip.search.after', {
                            response: {
                                items: items,
                                count: items.length
                            }
                        });

                    }, function (r) {
                        that.logError('Yandex geocoder error', r);

                        error(r);
                        that.log('trigger:bxmaker.geoip.search.after', {
                            'error': {
                                'code': 'error_ajax',
                                'msg': 'error response',
                                'more': r
                            }
                        });
                        $(document).trigger('bxmaker.geoip.search.after', {
                            'error': {
                                'code': 'error_ajax',
                                'msg': 'error response',
                                'more': r
                            }
                        });
                    });
            }
            else {
                that.log('search in bitrix - ' + query);
                $.ajax({
                    type: 'POST',
                    url: '/',
                    dataType: 'json',
                    data: {
                        sessid: BX.bitrix_sessid(),
                        module: 'bxmaker.geoip',
                        method: 'search',
                        query: query
                    },
                    error: function (r) {
                        if (typeof error == 'function') {
                            that.logError('Error connection', r);

                            error(r);
                        }
                        that.log('trigger:bxmaker.geoip.search.after', {
                            'error': {
                                'code': 'error_ajax',
                                'msg': 'error response',
                                'more': r
                            }
                        });
                        $(document).trigger('bxmaker.geoip.search.after', {
                            'error': {
                                'code': 'error_ajax',
                                'msg': 'error response',
                                'more': r
                            }
                        });
                    },
                    success: function (r) {
                        if (!!r.response) {
                            if (typeof success == 'function') {
                                that.log('Search success', r);

                                success(r);
                            }
                        }
                        else if (!!r.error) {
                            if (typeof error == 'function') {
                                that.logError('Error', r);

                                error(r);
                            }
                        }
                        that.log('trigger:bxmaker.geoip.search.after', r);
                        $(document).trigger('bxmaker.geoip.search.after', r);
                    }
                });
            }
            return true;
        };

        BXmakerGeoIPCityConstructor.prototype.selectLocation = function (id, params) {
            var that = this;
            var params = params || {};
            var currentLocation = that.getLocation();
            var currentCity = that.getCity();

            that.log('trigger:bxmaker.geoip.select.location.before', {
                id: id,
                params: params
            });
            $(document).trigger('bxmaker.geoip.select.location.before', {
                id: id,
                params: params
            });

            that.log('select location ', id, params);

            params['sessid'] = BX.bitrix_sessid();
            params['module'] = 'bxmaker.geoip';
            if (id) {
                params['method'] = 'selectLocation';
                params['id'] = id;
            }
            else {
                params['method'] = 'selectYandexLocation';
            }

            $.ajax({
                type: 'POST',
                url: '/',
                dataType: 'json',
                data: params,
                error: function (r) {
                    that.logError('error ajax select location', r);

                    that.log('trigger: bxmaker.geoip.select.location.after', {
                        'error': {
                            'code': 'error_ajax',
                            'msg': 'error response',
                            'more': r
                        }
                    });
                    $(document).trigger('bxmaker.geoip.select.location.after', {
                        'error': {
                            'code': 'error_ajax',
                            'msg': 'error response',
                            'more': r
                        }
                    });
                },
                success: function (r) {
                    if (!!r.response) {

                        that.log('success select location', r);

                        if (!!r.response.redirect) {
                            location.href = r.response.redirect;
                            return;
                        }

                        if (that.params.reload) {
                            that.log('need page reload');

                            //если есть форма оформления заказа
                            if (!!BX.Sale && !!BX.Sale.OrderAjaxComponent && !!BX.Sale.OrderAjaxComponent.sendRequest) {
                                that.log('order page - send request');
                                // BX.Sale.OrderAjaxComponent.sendRequest()
                            }
                            else if (!!BX.saleOrderAjax) {
                                that.log('order page old - unknown action')
                                //window.location.reload();
                            }
                            else {
                                that.log('reload');
                                window.location.reload();
                                return;
                            }
                        }

                        if (!!r.response && !!r.response.city && !!r.response.location && (r.response.city != currentCity || r.response.location != currentLocation)) {
                            var params = that.getFullData();
                            params['response'] = r.response;

                            that.log('trigger: bxmaker.geoip.city.change', params);
                            $(document).trigger('bxmaker.geoip.city.change', params);
                        }

                        that.showCity();

                        that.log('trigger: bxmaker.geoip.select.location.after', {
                            'response': r.response
                        });
                        $(document).trigger('bxmaker.geoip.select.location.after', {
                            'response': r.response
                        });


                    }
                    else if (!!r.error) {

                        that.logError('error select location ', r);

                        that.log('trigger: bxmaker.geoip.select.location.after', {
                            'error': r.error
                        });

                        $(document).trigger('bxmaker.geoip.select.location.after', {
                            'error': r.error
                        });
                    }
                }
            });

            return true;
        };

        BXmakerGeoIPCityConstructor.prototype.showCity = function () {
            var that = this;

            var params = that.getFullData();

            that.log('show city', params);

            $('.js-bxmaker__geoip__city-name-global').text(params['city']);

            that.log('trigger: bxmaker.geoip.city.show', params);
            $(document).trigger('bxmaker.geoip.city.show', params);
        };

        BXmakerGeoIPCityConstructor.prototype.getFullData = function () {
            var that = this;

            return {
                'location': that.getLocation(),
                'city': that.getCity(),
                'city_id': that.getLocation(),
                'country': that.getCountry(),
                'country_id': that.getCountryId(),
                'region': that.getRegion(),
                'region_id': that.getRegionId(),
                'area': that.getArea(),
                'zip': that.getZip(),
                'range': that.getRange(),
                'lng': that.getLng(),
                'lat': that.getLat(),
                'yandex': that.getYandex()
            };
        };


        BXmakerGeoIPCityConstructor.prototype.getLocation = function () {
            var that = this;
            return that.cookie('location') || 0;
        };

        BXmakerGeoIPCityConstructor.prototype.getCity = function () {
            var that = this;
            return that.cookie('city') || false;
        };
        BXmakerGeoIPCityConstructor.prototype.getCityId = function () {
            var that = this;
            return that.cookie('city_id') || 0;
        };
        BXmakerGeoIPCityConstructor.prototype.getCountry = function () {
            var that = this;
            return that.cookie('country') || false;
        };
        BXmakerGeoIPCityConstructor.prototype.getCountryId = function () {
            var that = this;
            return that.cookie('country_id') || 0;
        };
        BXmakerGeoIPCityConstructor.prototype.getRegion = function () {
            var that = this;
            return that.cookie('region') || false;
        };
        BXmakerGeoIPCityConstructor.prototype.getRegionId = function () {
            var that = this;
            return that.cookie('region_id') || 0;
        };

        BXmakerGeoIPCityConstructor.prototype.getArea = function () {
            var that = this;
            return that.cookie('area') || false;
        };

        BXmakerGeoIPCityConstructor.prototype.getZip = function () {
            var that = this;
            return that.cookie('zip') || '000000';
        };

        BXmakerGeoIPCityConstructor.prototype.getLng = function () {
            var that = this;
            return that.cookie('lng') || 0;
        };

        BXmakerGeoIPCityConstructor.prototype.getLat = function () {
            var that = this;
            return that.cookie('lat') || 0;
        };

        BXmakerGeoIPCityConstructor.prototype.getRange = function () {
            var that = this;
            return that.cookie('range') || false;
        };

        BXmakerGeoIPCityConstructor.prototype.getYandex = function () {
            var that = this;
            return that.cookie('yandex') || that.params.bUseYandexSearch;
        };

        BXmakerGeoIPCityConstructor.prototype.checkRedirect = function () {
            var that = this;

            if (that.params.bSubdomainOn) {
                //check definde user location by yandex early
                if (that.cookie('checked_redirect') != 'Y') {

                    var arKeys = [
                        'checkRedirect'
                    ];
                    var cache = that.storageGet(arKeys.join(','));
                    if (!!cache) {
                        if (!!cache.city && cache.location && cache.city == that.getCity() && cache.location == that.getLocation()) {
                            var r = cache.data;
                            that.log('success checked_redirect from cache', r);

                            if (!!r.response.need) {
                                that.cookie('checked_redirect', 'Y');
                                location.href = r.response.redirect;
                            }

                            return true;
                        }
                    }


                    $.ajax({
                        type: 'POST',
                        url: '/',
                        dataType: 'json',
                        data: {
                            sessid: BX.bitrix_sessid(),
                            module: 'bxmaker.geoip',
                            method: 'checkNeedRedirect'
                        },
                        error: function (r) {
                            that.logError('error ajax checked_redirect', r);
                        },
                        success: function (r) {
                            if (!!r.response) {

                                that.log('success checked_redirect', r);

                                window.BXmakerGeoIPCity.storageSet(arKeys.join(','), {
                                    "city": that.getCity(),
                                    "location": that.getLocation(),
                                    "data": r
                                });

                                if (!!r.response.need) {
                                    that.cookie('checked_redirect', 'Y');
                                    location.href = r.response.redirect;
                                }
                            }
                        }
                    });
                }
                else {

                    if (that.params.subDomain != location.hostname) {
                        var url = location.protocol;
                        url += '//';
                        url += that.params.subDomain;
                        url += location.pathname;
                        url += location.search;
                        url += location.hash;

                        location.href = url;
                    }
                }
            }


        };


        BXmakerGeoIPCityConstructor.prototype.storageTest = function () {
            try {
                return 'localStorage' in window && window['localStorage'] !== null;
            } catch (e) {
                return false;
            }
        };

        BXmakerGeoIPCityConstructor.prototype.storageSet = function (key, value) {
            var that = this;
            key = that.params.cookiePrefix + key;
            try {
                if (value === null) {
                    return window.localStorage.removeItem(key);
                }
                else {
                    return window.localStorage.setItem(key, JSON.stringify({
                        "data": value,
                        "time": (new Date()).getTime()
                    }));
                }
            } catch (e) {
                if (e == QUOTA_EXCEEDED_ERR) {
                    that.logError('LocalStorage limit exceeded');
                }
                else {
                    that.logError('localStorage:', e);
                }
                return false;
            }
        };

        BXmakerGeoIPCityConstructor.prototype.storageGet = function (key) {
            var that = this;
            var time = (new Date()).getTime();
            key = that.params.cookiePrefix + key;
            try {
                var obj = JSON.parse(window.localStorage.getItem(key));
                if (!!obj) {
                    if (!!obj['time'] && obj['time'] > (time - 60 * 60 * 24 * 3)) {
                        return obj['data'];
                    }
                }
                return false;
            } catch (e) {
                that.logError('localStorage:', e);
                return false;
            }

        };

        window.BXmakerGeoIPCity = new BXmakerGeoIPCityConstructor();

    })();
}


if (!!window.BXmakerGeoIPYandexGeo === false) {

    (function () {

        var GeoIPYandexGeo = function () {
            var that = this;

            that.bInit = false;
            that.ymap = false;
            that.cb = [];

        };

        GeoIPYandexGeo.prototype.log = function () {
            if (window.BXmakerDebugGeoIP) {
                var args = Array.prototype.slice.call(arguments);
                args.unshift('bxmaker:geoip.city.yandex.geo: ');
                console.log.apply(console, args);
            }
        };
        GeoIPYandexGeo.prototype.logError = function () {
            if (window.BXmakerDebugGeoIP) {
                var args = Array.prototype.slice.call(arguments);
                args.unshift('bxmaker:geoip.city.yandex.geo: ');
                console.error.apply(console, args);
            }
        };


        GeoIPYandexGeo.prototype.init = function (params) {
            var that = this;

            if (that.bInit) return true;

            that.bInit = true; //&ns=BXmakerGeoIPYandexMap
            $('head').append($('<script id="BXmakerGeoIPYandexGeo" type="text/javascript" src="https://api-maps.yandex.ru/2.1/?lang=ru_RU&load=geolocation,geocode&ns=&onload=BXmakerGeoIPYandexGeo.onload" />'));
        };

        GeoIPYandexGeo.prototype.onload = function (ym) {
            var that = this;
            that.ymap = ym;

            that.log('API is loaded');

            that.log('trigger: bxmaker.geoip.yandex.geo.loaded');
            $(document).trigger('bxmaker.geoip.yandex.geo.loaded');
        };

        GeoIPYandexGeo.prototype.getConnection = function () {
            var that = this;
            return that.ymap;
        };


        window.BXmakerGeoIPYandexGeo = new GeoIPYandexGeo();
    })();

}