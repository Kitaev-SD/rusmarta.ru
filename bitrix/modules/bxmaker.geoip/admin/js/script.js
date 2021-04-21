$(document).ready(function () {

    var obj = new BXmakerGeoIPAdminPage();
    obj.init();
});

var BXmakerGeoIPAdminPage = function () {

};

BXmakerGeoIPAdminPage.prototype.init = function () {

    this.initMessageEditPage(); // редактирвоание сообщений
    this.initFavoritesEditPage(); // редактирвоание группы избранных местоположений
    this.initImportPage(); // импорт местоположений
    this.initOptionsPage(); // настрйоки
    this.initDomainEditPage(); // поддомены

};

BXmakerGeoIPAdminPage.prototype.initMessageEditPage = function () {
    var box = $('.bxmaker_geoip_message_edit_form');
    if (!box.length) return false;


    // города ---------------------------
    var data_box = box.find('.data_box');
    var tpl = data_box.find('#bxmaker_geoip_data_row_tpl').text();

    box.on("click", '.data_box .btn_add', function () {
        data_box.find('.row_box').append(tpl);
    });

    box.on("click", '.data_box .btn_delete', function () {
        $(this).closest('.row_item').remove();
    });

    // группы местоположений ----------------
    var data_box_group = box.find('.data_box_group');
    var tpl2 = data_box_group.find('#bxmaker_geoip_group_data_row_tpl').text();

    box.on("click", '.data_box_group .btn_add_group', function () {
        data_box_group.find('.row_box').append(tpl2);
    });

    box.on("click", '.data_box_group .btn_delete_group', function () {
        $(this).closest('.row_item').remove();
    });

};

BXmakerGeoIPAdminPage.prototype.initFavoritesEditPage = function () {
    var box = $('.bxmaker_geoip_favorites_edit_form');
    if (!box.length) return false;


    // города ---------------------------
    var data_box = box.find('.data_box');
    var tpl = data_box.find('#bxmaker_geoip_data_row_tpl').text();

    box.on("click", '.data_box .btn_add', function () {
        data_box.find('.row_box').append(tpl);
        data_box.find('.row_box input[name^="LOCATION_NAME"]').focus();
        recalcSort();
    });

    box.on("click", '.data_box .btn_delete', function () {
        $(this).closest('.row_item').remove();
        recalcSort();
    });

    function recalcSort() {
        var i = 1;
        box.find('input[name^="LOCATION_ID"]').each(function () {
            $(this).val(i++);
        });
    }


    var tm = false, ajax = false;
    box.on("keydown", 'input[name^="LOCATION_NAME"]', function (e) {

        var option_box = $(this).closest('.city_box').find('.option_box');
        var option_items = option_box.find('.option_item');

        if (e.keyCode == 13) {
            e.preventDefault();
            e.stopPropagation();

            if (option_items.filter('.view').length) {
                option_items.filter('.view').click();
            }
            else {
                option_box.find('.option_item').eq(0).click();
            }
        }
        else if (e.keyCode == 38) { //up
            e.preventDefault();
            e.stopPropagation();

            if (option_items.length && !option_items.filter('.empty_result').length) {
                if (option_items.filter('.view').length) {
                    var index = option_items.index(option_items.filter('.view'));
                    option_items.removeClass('view');
                    if (index - 1 >= 0) {
                        option_items.eq(index - 1).addClass('view');
                    }
                    else {
                        option_items.eq(option_items.length - 1).addClass('view');
                    }
                }
                else {
                    option_items.eq(option_items.length - 1).addClass('view');
                }
            }

        }
        else if (e.keyCode == 40) { //down
            e.preventDefault();
            e.stopPropagation();

            console.log(option_items.length);

            if (option_items.length && !option_items.filter('.empty_result').length) {
                if (option_items.filter('.view').length) {
                    var index = option_items.index(option_items.filter('.view'));
                    option_items.removeClass('view');
                    if (index + 1 < (option_items.length)) {
                        option_items.eq(index + 1).addClass('view');
                    }
                    else {
                        option_items.eq(0).addClass('view');
                    }
                }
                else {
                    option_items.eq(0).addClass('view');
                }
            }
        }
    });

    box.on("keyup", 'input[name^="LOCATION_NAME"]', function (e) {
        e.preventDefault();
        e.stopPropagation();

        if (e.keyCode == 13 || e.keyCode == 38 || e.keyCode == 40) {
            return false;
        }

        var inp, city_box, loc_id, option_box;

        if (tm) {
            clearTimeout(tm);
        }
        if (ajax) {
            ajax.abort();
        }

        inp = $(this);
        city_box = inp.closest('.city_box'),
            loc_id = city_box.find('input[name^="LOCATION_ID"]'),
            option_box = city_box.find('.option_box');

        tm = setTimeout(function () {

            option_box.addClass('preloader active').empty();

            ajax = $.ajax({
                type: 'POST',
                dataType: 'json',
                data: {
                    sessid: BX.bitrix_sessid(),
                    method: 'searchLocation',
                    city: inp.val().trim()
                },
                error: function () {
                    option_box.removeClass('preloader active');
                },
                success: function (r) {
                    if (!!r.response) {
                        if (r.response.count) {
                            var html = '';
                            for (var i = 0; i < r.response.count; i++) {
                                html += '<div class="option_item" data-city="' + r.response.items[i]['city'] + '" data-location="' + r.response.items[i]['location'] + '" >'
                                    + r.response.items[i].city + '<span>'
                                    + (!!r.response.items[i].region ? ', ' + r.response.items[i].region : '')
                                    + (!!r.response.items[i].country ? ', ' + r.response.items[i].country : '')
                                    + '</span></div>';
                                option_box.html(html).removeClass('preloader').addClass('active');
                            }
                        }
                        else if (!!r.response.msg) {
                            option_box.append('<div class="option_item empty_result" >' + r.response.msg + '</div>').removeClass('preloader').addClass('active');
                        }
                        else {
                            option_box.removeClass('preloader active');
                        }
                    }
                    else if (!!r.error) {
                        option_box.removeClass('preloader active').empty();
                    }
                }
            })
        }, 500);
    });

    box.on("click", '.option_item:not(.empty_result)', function () {
        var btn = $(this);
        var item_box = btn.closest('.row_item');
        var city_box = btn.closest('.city_box');
        var option_box = city_box.find('.option_box');

        item_box.addClass('finish_item');
        city_box.find('input[name^="LOCATION_NAME"]').remove();
        city_box.find('input[name^="LOCATION_ID"]').attr('name', 'LOCATION_ID[l' + btn.attr('data-location') + ']');
        item_box.find('input[name^="MARK"]').attr('name', 'MARK[l' + btn.attr('data-location') + ']');
        city_box.prepend('<p>' + btn.attr('data-city') + '</p>');
        option_box.removeClass('preloader active').empty();

        box.find('.btn_add').click();
        $(".sortable_box").sortable("refresh");
    });


    // обработка события deactivate
    $(".sortable_box").sortable({
        cursor: 'move',
        items: '.finish_item',
        stop: function (e, ui) {
            recalcSort();
        },
        activate: function (event, ui) {
            ui.item.addClass('drag');
        },
        deactivate: function (event, ui) {
            ui.item.removeClass('drag');
        }
    });

};

// импорт местоположений
BXmakerGeoIPAdminPage.prototype.initImportPage = function () {
    var that = this;
    var box = $('.bxmaker_geoip_location_import_box');

    if (!box.length) return;


    var bActionStop = false;

    var btnStart = box.find('.btn-start');
    var btnStop = box.find('.btn-stop');
    var countryCode = box.find('select[name="country"]').val();

    showMsg = function (text, err) {
        var err = err || false;
        var msg_box = box.find('.msg_box');

        msg_box.removeClass('error success').empty();

        if (text == null) return;

        msg_box.html(text);
        if (err) msg_box.addClass('error');
        else msg_box.addClass('success');
    };

    that.start = function (params, start) {
        var params = params || {};
        var start = start || false;

        var formData = {
            sessid: BX.bitrix_sessid(),
            method: 'import_start',
            stop: (bActionStop ? 1 : 0)
        };

        if (!start) {
            formData['continue'] = params['continue'];
            formData['step'] = params['step'];
            formData['count'] = params['count'];
            formData['pos'] = params['pos'];
            formData['fseek'] = params['fseek'];
        }
        else {
            countryCode = box.find('select[name="country"]').val();
        }

        formData['country'] = countryCode;

        $.ajax({
            type: "POST",
            data: formData,
            dataType: 'json',
            complete: function () {

            },
            error: function (r) {

            },
            success: function (r) {
                if (!r) {
                    that.start(r.response);
                }
                else {
                    if (!!r.response) {

                        showMsg(r.response.msg);

                        if (r.response.continue) {
                            that.start(r.response);
                        }
                        else {
                            bActionStop = false;
                            btnStart.removeClass('preloader');
                            btnStop.removeClass('preloader');
                        }
                    }
                    else if (!!r.error) {
                        showMsg(r.error.msg, true);
                        bActionStop = false;
                        btnStart.removeClass('preloader');
                        btnStop.removeClass('preloader');
                    }
                }
            }
        });
        return true;
    };

    box.on("click", '.btn-start', function (e) {
        e.preventDefault();
        e.stopPropagation();

        var btn = $(this);

        if (btnStart.hasClass('preloader')) return false;
        btnStart.addClass('preloader');


        showMsg(null);

        that.start({}, true);

    });

    box.on("click", '.btn-stop', function (e) {
        e.preventDefault();
        e.stopPropagation();

        if (btnStop.hasClass('preloader') || !btnStart.hasClass('preloader')) return false;
        btnStop.addClass('preloader');

        bActionStop = true;

    });

};

BXmakerGeoIPAdminPage.prototype.initOptionsPage = function () {
    var box = $('.bxmaker__geoip__admin__options');
    if (!box.length) return false;

    var tm = false, ajax = false;
    box.on("keyup", 'input[name$="DEFAULT_CITY"]', function (e) {
        e.preventDefault();
        e.stopPropagation();

        var inp, city_box, loc_id, option_box;

        if (tm) {
            clearTimeout(tm);
        }
        if (ajax) {
            ajax.abort();
        }

        inp = $(this);
        city_box = inp.closest('.js-bxmaker__geoip__admin__options-city'),
            loc_id = city_box.find('input[name$="DEFAULT_CITY_ID"]'),
            option_box = city_box.find('.js-bxmaker__geoip__admin__options-city-options');

        tm = setTimeout(function () {

            option_box.addClass('preloader active').empty();

            ajax = $.ajax({
                type: 'POST',
                dataType: 'json',
                data: {
                    sessid: BX.bitrix_sessid(),
                    method: 'searchLocation',
                    city: inp.val().trim()
                },
                error: function () {
                    option_box.removeClass('preloader active');
                },
                success: function (r) {
                    if (!!r.response) {
                        if (r.response.count) {
                            var html = '';
                            for (var i = 0; i < r.response.count; i++) {
                                html += '<div class="bxmaker__geoip__admin__options-city-option js-bxmaker__geoip__admin__options-city-option" data-city="' + r.response.items[i]['city'] + '" data-location="' + r.response.items[i]['location'] + '" >'
                                    + r.response.items[i].city + '<span>'
                                    + (!!r.response.items[i].region ? ', ' + r.response.items[i].region : '')
                                    + (!!r.response.items[i].country ? ', ' + r.response.items[i].country : '')
                                    + '</span></div>';
                                option_box.html(html).removeClass('preloader').addClass('active');
                            }
                        }
                        else if (!!r.response.msg) {
                            option_box.append('<div class="bxmaker__geoip__admin__options-city-option js-bxmaker__geoip__admin__options-city-option empty_result" >' + r.response.msg + '</div>').removeClass('preloader').addClass('active');
                        }
                        else {
                            option_box.removeClass('preloader active').empty();
                        }
                    }
                    else if (!!r.error) {
                        option_box.removeClass('preloader active').empty();
                    }
                }
            })
        }, 500);
    });

    box.on("click", '.js-bxmaker__geoip__admin__options-city-option', function () {
        var btn = $(this);
        var city_box = btn.closest('.js-bxmaker__geoip__admin__options-city');
        var option_box = city_box.find('.js-bxmaker__geoip__admin__options-city-options');

        city_box.find('input[name$="DEFAULT_CITY"]').val(btn.attr('data-city'));
        city_box.find('input[name$="DEFAULT_CITY_ID"]').val(btn.attr('data-location'));
        option_box.removeClass('preloader active').empty();
    });
};

BXmakerGeoIPAdminPage.prototype.initDomainEditPage = function () {
    var editBox = $('.bxmaker__geoip__domain__edit');
    if (!editBox.length) return false;


    editBox.find('.data_box').each(function () {
        var box = $(this);
        var sid = box.attr("data-sid");

        var tpl = box.find('#bxmaker_geoip_data_row_tpl').text();

        box.on("click", '.btn_add', function () {
            box.find('.row_box').append(tpl);
            box.find('.row_box input[name^="LOCATION_NAME"]').focus();
        });

        box.on("click", '.btn_delete', function () {
            $(this).closest('.row_item').remove();
        });

        var tm = false, ajax = false;
        box.on("keydown", 'input[name^="LOCATION_NAME"]', function (e) {

            var option_box = $(this).closest('.city_box').find('.option_box');
            var option_items = option_box.find('.option_item');

            if (e.keyCode == 13) {
                e.preventDefault();
                e.stopPropagation();

                if (option_items.filter('.view').length) {
                    option_items.filter('.view').click();
                }
                else {
                    option_box.find('.option_item').eq(0).click();
                }
            }
            else if (e.keyCode == 38) { //up
                e.preventDefault();
                e.stopPropagation();

                if (option_items.length && !option_items.filter('.empty_result').length) {
                    if (option_items.filter('.view').length) {
                        var index = option_items.index(option_items.filter('.view'));
                        option_items.removeClass('view');
                        if (index - 1 >= 0) {
                            option_items.eq(index - 1).addClass('view');
                        }
                        else {
                            option_items.eq(option_items.length - 1).addClass('view');
                        }
                    }
                    else {
                        option_items.eq(option_items.length - 1).addClass('view');
                    }
                }

            }
            else if (e.keyCode == 40) { //down
                e.preventDefault();
                e.stopPropagation();

                if (option_items.length && !option_items.filter('.empty_result').length) {
                    if (option_items.filter('.view').length) {
                        var index = option_items.index(option_items.filter('.view'));
                        option_items.removeClass('view');
                        if (index + 1 < (option_items.length)) {
                            option_items.eq(index + 1).addClass('view');
                        }
                        else {
                            option_items.eq(0).addClass('view');
                        }
                    }
                    else {
                        option_items.eq(0).addClass('view');
                    }
                }
            }
        });

        box.on("keyup", 'input[name^="LOCATION_NAME"]', function (e) {
            e.preventDefault();
            e.stopPropagation();

            if (e.keyCode == 13 || e.keyCode == 38 || e.keyCode == 40) {
                return false;
            }

            var inp, city_box, loc_id, option_box;

            if (tm) {
                clearTimeout(tm);
            }
            if (ajax) {
                ajax.abort();
            }

            box.find('.city_box').removeClass('active');
            box.find('.option_box.active').removeClass('preloader active');

            inp = $(this);
            city_box = inp.closest('.city_box'),
                loc_id = city_box.find('input[name^="LOCATION_ID"]'),
                option_box = city_box.find('.option_box');

            tm = setTimeout(function () {

                option_box.addClass('preloader active').empty();
                city_box.addClass('active');

                ajax = $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        sessid: BX.bitrix_sessid(),
                        method: 'searchLocation',
                        city: inp.val().trim()
                    },
                    error: function () {
                        option_box.removeClass('preloader active');
                    },
                    success: function (r) {
                        if (!!r.response) {
                            if (r.response.count) {
                                var html = '';
                                for (var i = 0; i < r.response.count; i++) {
                                    html += '<div class="option_item" data-city="' + r.response.items[i]['city'] + '" data-location="' + r.response.items[i]['location'] + '" >'
                                        + r.response.items[i].city + '<span>'
                                        + (!!r.response.items[i].region ? ', ' + r.response.items[i].region : '')
                                        + (!!r.response.items[i].country ? ', ' + r.response.items[i].country : '')
                                        + '</span></div>';
                                    option_box.html(html).removeClass('preloader').addClass('active');
                                }
                            }
                            else if (!!r.response.msg) {
                                option_box.append('<div class="option_item empty_result" >' + r.response.msg + '</div>').removeClass('preloader').addClass('active');
                            }
                            else {
                                option_box.removeClass('preloader active');
                            }

                        }
                        else if (!!r.error) {
                            option_box.removeClass('preloader active').empty();
                        }
                    }
                })
            }, 500);
        });

        box.on("click", '.option_item:not(.empty_result)', function () {
            var btn = $(this);
            var item_box = btn.closest('.row_item');
            var city_box = btn.closest('.city_box');
            var option_box = city_box.find('.option_box');

            item_box.addClass('finish_item');
            city_box.find('input[name^="LOCATION_NAME"]').remove();
            item_box.find('input[name^="SUBDOMAIN"]').attr('name', 'SUBDOMAIN['+sid+'][l' + btn.attr('data-location') + ']');
            city_box.prepend('<p>' + btn.attr('data-city') + '</p>');
            option_box.removeClass('preloader active').empty();

            box.find('.btn_add').click();

            city_box.removeClass('active');
        });

    });

    editBox.find('.data_group_box').each(function () {
        var box = $(this);
        var sid = box.attr("data-sid");

        var tpl = box.find('#bxmaker_geoip_data_group_row_tpl').text();

        box.on("click", '.btn_add', function () {
            box.find('.row_box').append(tpl);
            box.find('.row_box input[name^="GROUP"]').focus();
        });

        box.on("click", '.btn_delete', function () {
            $(this).closest('.row_item').remove();
        });



        box.on("change", 'select[name^="GROUP"]', function (e) {
            e.preventDefault();
            e.stopPropagation();

            var select = $(this);
            var parent = select.closest('.row_item');
            parent.find('input[name^="SUBDOMAIN_GROUP"]').attr('name', 'SUBDOMAIN_GROUP['+sid+'][g' + select.val() + ']');

        });



    });
};


