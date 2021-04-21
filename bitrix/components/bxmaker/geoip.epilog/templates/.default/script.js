;
(function () {

    if (!!window.BXmakerGeoIP) return true;

    if (window.frameCacheVars !== undefined) {
        BX.addCustomEvent("onFrameDataReceived", function (json) {
            window.BXmakerGeoIP = new CBXmakerGeoIP();
            window.BXmakerGeoIP.init(true);
        });
    } else {
        BX.ready(function () {
            window.BXmakerGeoIP = new CBXmakerGeoIP();
            window.BXmakerGeoIP.init(false);
        });
    }


    var CBXmakerGeoIP = function () {
        this.bInit = false;
        this.bComposite = false;
        this.bLog = true;
        this.bFind = false;
        this.BXmakerGeoIPYandexMap = null; //yandex api
    };

    CBXmakerGeoIP.prototype.init = function (bComposite) {
        this.bComposite = bComposite || false;

        if (this.bInit) {
            return false;
        }
        this.bInit = true;
        this.bLog = (BX.message('BXMAKER_GEOIP_PARAMS_DEBUG') == 'Y');

        this.log('Composite ' + (bComposite ? ' true ' : ' false '));

        //
        //if (!!window.jQuery == false) {
        //    this.log(' need jQuery', true);
        //    return true;
        //}


        this.epilogBox = $bxm('.c-bxmaker_ipgeo_epilog_default-box').eq(0);

        this.showCity(); // вставка названия города в размеченные области
        this.initEvent(); // собятия
        this.initYandexMapAPI();
    };


    CBXmakerGeoIP.prototype.cookie = function (name, value, params) {

        name = 'bxmaker.geoip_' + name;

        if (value === undefined) {
            var matches = document.cookie.match(new RegExp(
                "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
            ));
            var value = matches ? decodeURIComponent(matches[1].replace(/\+/g, ' ')) : undefined;
            this.log('cookie get: ' + name + ' = ' + value);
            return value;
        }
        else {
            var params = params || {};
            var parts = [];
            var d = new Date();
            d.setTime(d.getTime() + ((!!params.expires ? params.expires : 365 ) * 24 * 60 * 60 * 1000));
            parts.push(name + "=" + value);// todo  parts.push(name + "=" + encodeURIComponent(value));
            parts.push("expires=" + d.toUTCString());
            parts.push("path=" + (!!params.path ? params.path : '/' ));
            !!params.domain && parts.push("domain=" + params.domain);
            document.cookie = parts.join('; ');
            this.log('cookie: ' + parts.join('; '));
        }
    };

    CBXmakerGeoIP.prototype.log = function (data, error) {
        if (!this.bLog) return;
        if (!!error) {
            console.error('bxmaker.geoip:', data);
            return false;
        }
        console.log('bxmaker.geoip:', data);
    };

    CBXmakerGeoIP.prototype.showCity = function () {
        var that = this;

        this.log('bxmaker.geoip.onBeforeShowCity');
        $bxm(document).trigger('bxmaker.geoip.onBeforeShowCity', {
            'city': that.getCity(),
            'cityId': that.getLocation()
        });

        $bxm('.bxmaker_geoip_epilog_city_name').text(that.getCity());

        this.log('bxmaker.geoip.onAfterShowCity');
        $bxm(document).trigger('bxmaker.geoip.onAfterShowCity', {
            'city': that.getCity(),
            'cityId': that.getLocation()
        });
    };

    CBXmakerGeoIP.prototype.getCity = function () {
        var city = this.cookie('city');

        if (city === undefined) {
            return BX.message('BXMAKER.GEOIP.EPILOG.DEFAULT_CITY');
        }
        return city;
    };

    CBXmakerGeoIP.prototype.getLocation = function () {
        var city = this.cookie('location');
        if (city === undefined) {
            return 1;
        }
        return city;
    };
    CBXmakerGeoIP.prototype.getZip = function () {
        var zip = this.cookie('zip');
        if (zip === undefined) {
            return '000000';
        }
        return zip;
    };

    CBXmakerGeoIP.prototype.saveCity = function (params) {
        var that = this;

        $bxm(document).trigger('bxmaker.geoip.onBeforeSaveCity', params);

        that.log('saveCity');
        that.log(params);

        params['sessid'] = BX.bitrix_sessid();
        params['module'] = 'bxmaker.geoip';
        params['method'] = 'saveCity';

        $bxm.ajax({
            url: '/',
            type: 'POST',
            dataType: 'json',
            data: params,
            error: function (r) {
                that.log('error save city', true);
                that.log(r, true);

                $bxm(document).trigger('bxmaker.geoip.onAfterSaveCity', {
                    'error': {
                        'msg': 'error save city'
                    }
                });
            },
            success: function (r) {
                if (!!r.response) {

                    //that.cookie('city', city);
                    //that.cookie('location', location);

                    if (BX.message('BXMAKER_GEOIP_PARAMS_CITY_CHANGE_PAGE_RELOAD') == 'Y') {
                        window.location.reload();
                    }
                    else {

                        that.cookie('location', r.response.location);
                        that.cookie('zip', r.response.zip);
                        that.cookie('city', r.response.city);
                        that.cookie('country', r.response.country);
                        that.cookie('region', r.response.region);
                        that.cookie('district', r.response.district);
                        that.cookie('yandex', r.response.yandex);

                        $bxm(document).trigger('bxmaker.geoip.onAfterSaveCity', r);

                        //если есть форма оформления заказа
                        if (!!BX && !!BX.Sale && !!BX.Sale.OrderAjaxComponent && !!BX.Sale.OrderAjaxComponent.sendRequest()) {
                            //BX.Sale.OrderAjaxComponent.sendRequest()
                            window.location.reload();
                        }

                        if (!!BX && !!BX.saleOrderAjax) {
                            window.location.reload();
                        }


                        that.showCity();
                    }
                }
                else if (!!r.error) {
                    that.log('error save city ', true);
                    that.log(r.error, true);

                    $bxm(document).trigger('bxmaker.geoip.onAfterSaveCity', r);
                }
            }
        });

        return true;
    };

    CBXmakerGeoIP.prototype.initEvent = function () {
        var that = this;
        that.log('initEvent');

        var search_timer = false;

        $bxm(document).on("click", '.bxmaker_geoip_epilog_city_name', function () {
            that.cityChangeBoxShow();
        });


        this.epilogBox
            // click po knopke zakryt ocno dlya smeny goroda
            .on("click", '.btn_close', function () {
                that.log('event click .btn_close');
                that.cityChangeBoxHide();
            })
            //click po fonu
            .on("click", '.bg_box', function () {
                that.log('event click .bg_box');
                that.cityChangeBoxHide();
            })
            //clear input
            .on("click", '.btn_clear_input', function () {
                if ($bxm(this).hasClass('preloader')) return false;
                that.epilogBox.find('input[name="city"]').val('');
                that.epilogBox.find('.search_options_box').hide();
            })
            // vvod gorod
            .on("keyup", 'input[name="city"]', function () {

                var adres = $bxm(this).val().replace(/^\s+/, '').replace(/\s+$/, '');

                if (adres.length < 3) return;
                if (!!search_timer) clearTimeout(search_timer);

                that.log('Поиск:' + adres);

                search_timer = setTimeout(function () {
                    that.log('Search adress:' + adres);
                    that.epilogBox.find('.btn_clear_input').addClass('preloader');
                    that.epilogBox.find('.search_options_box').hide();

                    that.search(adres, function (r) {
                        that.log('Найдено результатов:' + r.response.count);

                        that.epilogBox.find('.btn_clear_input').removeClass('preloader');

                        var search_result = that.epilogBox.find('.search_options_box');
                        search_result.empty();


                        if (!!r.response) {
                            if (r.response.count <= 0) {
                                search_result.append($bxm('<div class="empty" >' + BX.message('BXMAKER_GEOIP_PARAMS_EMPTY_RESULT') + '</div>'));
                            }
                            else {
                                for (var i = 0; i < r.response.count; i++) {
                                    var item = r.response.items[i];

                                    search_result.append($bxm('<div class="item" ' +
                                        'data-location="' + item.location + '" ' +
                                        'data-city="' + item.city + '" ' +
                                        'data-region="' + item.region + '" ' +
                                        'data-district="' + item.district + '" ' +
                                        'data-country="' + item.country + '" ' +
                                        'data-yandex="' + item.yandex + '" ' +
                                        '><span>'
                                        + item.city + '</span>'
                                        + (!!item.district ? ', ' + item.district : '')
                                        + (!!item.region && item.region != item.city ? ', ' + item.region : '')
                                        + (!!item.country ? ', ' + item.country : '')
                                        + '</div>'));
                                }
                            }
                        }
                        else if (!!r.error) {
                            search_result.append('<div class="empty" >' + r.error.msg + '</div>')
                        }

                        search_result.show();


                    }, function (r) {
                        that.log('Error search adress:', true);
                        that.log(r, true);

                        that.epilogBox.find('.btn_clear_input').removeClass('preloader');
                        that.epilogBox.find('.search_options_box').empty().append($bxm('<div class="empty" >' + BX.message('BXMAKER_GEOIP_PARAMS_EMPTY_RESULT') + '</div>'));
                    });
                }, 300);
            })
            .on("click", '.set_options div.item', function () {
                var item = $bxm(this);

                //prepare
                if (that.bFind) {

                    that.epilogBox.find('input').val(item.find('span').text()).keyup();
                    return;
                }

                that.epilogBox.find('.search_options_box').hide();

                // сохраняем на сервере
                that.saveCity({
                    location: item.attr('data-location'),
                    city: item.attr('data-city'),
                    region: item.attr('data-region'),
                    district: item.attr('data-district'),
                    country: item.attr('data-country'),
                    yandex: item.attr('data-yandex')
                });

                that.cityChangeBoxHide();

            })
            .on("click", '.search_options_box div.item', function () {
                var item = $bxm(this);

                that.epilogBox.find('.search_options_box').hide();

                //prepare
                if (that.bFind) {
                    console.log('array(' +
                        '"' + item.attr('data-country') + '",' +
                        '"' + item.attr('data-city') + '",' +
                        '"' + item.attr('data-region') + '",' +
                        '"' + item.attr('data-district') + '",' +
                        '"1"' +
                        '),');

                    return;
                }


                // сохраняем на сервере
                that.saveCity({
                    location: item.attr('data-location'),
                    city: item.attr('data-city'),
                    region: item.attr('data-region'),
                    district: item.attr('data-district'),
                    country: item.attr('data-country'),
                    yandex: item.attr('data-yandex')
                });

                that.cityChangeBoxHide();


            });

    };

    CBXmakerGeoIP.prototype.cityChangeBoxHide = function () {
        var that = this;
        this.epilogBox.find('.bg_box').animate({'opacity': '0'}, 300);
        this.epilogBox.find('.main_box').animate({'opacity': '0'}, 300, function () {
            that.epilogBox.find('.bxmaker_ipgeo_epilog_city_change_box').hide();
        });
    };

    CBXmakerGeoIP.prototype.cityChangeBoxShow = function () {

        this.epilogBox.find('.bxmaker_ipgeo_epilog_city_change_box').show();
        this.epilogBox.find('.bg_box').animate({'opacity': '1'}, 300);
        this.epilogBox.find('.main_box').animate({'opacity': '1'}, 300);
    };

    CBXmakerGeoIP.prototype.initYandexMapAPI = function () {
        if (BX.message('BXMAKER_GEOIP_PARAMS_YANDEX_GEOLOCATION_ON') != 'Y') return;

        if (this.cookie('check_yandex') === undefined || BX.message('BXMAKER_GEOIP_PARAMS_MODULE_SALE') != 'Y') {
            this.log('Append to head yandex maps script');
            $bxm('head').append($bxm('<script type="text/javascript"' +
                ' src="https://api-maps.yandex.ru/2.1/?lang=ru_RU&load=geolocation,geocode&ns=BXmakerGeoIPYandexMap&onload=BXmakerGeoIP.onloadYandexMap" >'));
        }
    };

    CBXmakerGeoIP.prototype.onloadYandexMap = function (BXmakerGeoIPYandexMap) {
        var that = this;
        that.log('API yandex maps is loaded');

        that.BXmakerGeoIPYandexMap = BXmakerGeoIPYandexMap;

        if (that.cookie('check_yandex') === undefined) {
            BXmakerGeoIPYandexMap.geolocation.get({
                provider: 'yandex',
                timeout: 10000
            }).then(function (res) {

                var obj = res.geoObjects.get(0);
                var city = obj.getLocalities();
                var area = obj.getAdministrativeAreas();
                var country = obj.getCountry();
                var regex = '';
                var bSkip = false;
                var citySkip = BX.message('BXMAKER_GEOIP_PARAMS_SKIP_CITY_WORDS').split(',');
                var cityCountryOk = BX.message('BXMAKER_GEOIP_PARAMS_COUNTRY_LIST').split(',');

                that.log('YandexMap geolocation result is success - ' + city.join(', ') + ', ' + area.join(', '));

                bSkip = true;
                // разрешение
                for (var ci = 0; ci < cityCountryOk.length; ci++) {
                    regex = new RegExp('^' + cityCountryOk[ci].replace(/^\s+/, '').replace(/\s+$/) + '$', 'gi');
                    if (country.match(regex)) {
                        bSkip = false;
                    }
                }

                if (bSkip) {
                    that.cookie('check_yandex', 'Y');
                    return;
                }

                // derevnya, celo
                for (var ci = 0; ci < citySkip.length; ci++) {
                    if (city[0].indexOf(citySkip[ci].replace(/^\s+/, '').replace(/\s+$/)) != -1) {
                        console.log('Исключен ' + city[0]);
                        bSkip = true;
                    }
                }

                if (bSkip) {
                    that.cookie('check_yandex', 'Y');
                    return;
                }


                that.cookie('check_yandex', 'Y');

                that.saveCity({
                    location: 0,
                    city: city[0],
                    region: (!!area[0] ? area[0] : ''),
                    district: (!!area[1] ? area[1] : ''),
                    country: country,
                    yandex: 1
                });

            }, function (e) {
                that.log('YandexMap geolocation result is empty', true);
            });
        }
        else {
            that.log('YandexMap geolocation check yet earlier');
        }


    };

    //search city
    CBXmakerGeoIP.prototype.search = function (query, success, error) {
        var that = this;

        $bxm(document).trigger('bxmaker.geoip.onBeforeSearch', {
            'query': query,
            'success': success,
            'error': error
        });

        if (BX.message('BXMAKER_GEOIP_PARAMS_YANDEX_GEOLOCATION_ON') == 'Y'
            && BX.message('BXMAKER_GEOIP_PARAMS_MODULE_SALE') != 'Y') {
            that.log('search in yandex - ' + query);


            that.BXmakerGeoIPYandexMap.geocode(query, {
                'kind': 'locality',
                'results': 100
            }).then(function (result) {

                that.log('Yandex geocoder success');
                that.log(result);

                var res = result.geoObjects;
                var r = {
                    items: [],
                    count: 0
                };

                var area = [];
                var city = [];
                var country = '';
                var regex = '';
                var bSkip = false;
                var citySkip = BX.message('BXMAKER_GEOIP_PARAMS_SKIP_CITY_WORDS').split(',');
                var cityCountryOk = BX.message('BXMAKER_GEOIP_PARAMS_COUNTRY_LIST').split(',');

                for (var i = 0; i < res.getLength(); i++) {
                    switch (res.get(i).properties.get('metaDataProperty').GeocoderMetaData.kind) {
                        case 'locality':
                        {
                            if (res.get(i).getLocalities().length != 1) break;

                            city = res.get(i).getLocalities();
                            area = res.get(i).getAdministrativeAreas();
                            country = res.get(i).getCountry();

                            bSkip = true;
                            // разрешение
                            for (var ci = 0; ci < cityCountryOk.length; ci++) {
                                regex = new RegExp('^' + cityCountryOk[ci].replace(/^\s+/, '').replace(/\s+$/) + '$', 'gi');
                                if (country.match(regex)) {
                                    bSkip = false;
                                }
                            }

                            if (bSkip) break;

                            // derevnya, celo
                            for (var ci = 0; ci < citySkip.length; ci++) {
                                if (city[0].indexOf(citySkip[ci].replace(/^\s+/, '').replace(/\s+$/)) != -1) {
                                    console.log('Исключен ' + city[0]);
                                    bSkip = true;
                                }
                            }

                            if (bSkip) break;

                            r.items.push({
                                'location': 0,
                                'country': res.get(i).getCountry(),
                                'city': city[0],
                                'region': (!!area[0] && area[0] ? area[0] : ''),
                                'district': (!!area[1] ? area[1] : ''),
                                'yandex': 1
                            });

                            break;
                        }
                    }
                }

                r.count = r.items.length;

                success({response: r});
                $bxm(document).trigger('bxmaker.geoip.onAfterSearch', r);

            }, function (r) {
                that.log('Yandex geocoder error', true);
                that.log(r, true);

                error(r);
                $bxm(document).trigger('bxmaker.geoip.onAfterSearch', r);
            });
        }
        else {
            that.log('search in bitrix - ' + query);
            $bxm.ajax({
                type: 'POST',
                dataType: 'json',
                data: {
                    sessid: BX.bitrix_sessid(),
                    module: 'bxmaker.geoip',
                    method: 'search',
                    query: query
                },
                error: function (r) {
                    if (typeof error == 'function') {
                        that.log('Error connection', true);
                        that.log(r, true);

                        error(r);
                        $bxm(document).trigger('bxmaker.geoip.onAfterSearch', r);
                    }
                },
                success: function (r) {
                    if (!!r.response) {
                        if (typeof success == 'function') {
                            that.log('Search success');
                            that.log(r);

                            success(r);
                            $bxm(document).trigger('bxmaker.geoip.onAfterSearch', r);
                        }
                    }
                    else if (!!r.error) {
                        if (typeof error == 'function') {
                            that.log('Error', true);
                            that.log(r, true);

                            error(r);
                            $bxm(document).trigger('bxmaker.geoip.onAfterSearch', r);
                        }
                    }
                }
            });
        }
        return true;
    };

})();
