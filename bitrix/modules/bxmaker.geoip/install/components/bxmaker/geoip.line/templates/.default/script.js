;
(function () {

    if (window.frameCacheVars !== undefined) {
        BX.addCustomEvent("onFrameDataReceived", function (json) {
            var obj = new BXmakerGeoIPLine();
            if(!!window.BXmakerGeoIP === false)
            {
                setTimeout(function(){
                    (!!window.BXmakerGeoIP && obj.init());
                }, 20);
            }
            else
            {
                obj.init();
            }
        });
    } else {
        BX.ready(function () {
            var obj = new BXmakerGeoIPLine();
            if(!!window.BXmakerGeoIP === false)
            {
                setTimeout(function(){
                    (!!window.BXmakerGeoIP && obj.init());
                }, 20);
            }
            else
            {
                obj.init();
            }
        });
    }

})();

if (!!window.BXmakerGeoIPLine === false) {
    var BXmakerGeoIPLine = function () {
        this.box = null;
    };

    BXmakerGeoIPLine.prototype.init = function (htmlClass) {
        var htmlClass = htmlClass || 'c-bxmaker_geoip_line_default_box';

        if (!!window.$bxm == false && !!console.error) {
            console.error('bxmaker.geoip.line - need jQuery');
            return true;
        }


        this.box = $bxm('.' + htmlClass + ':not(.init_ok)').eq(0)
        if (!this.box.length) {
            return false;
        }
        this.box.addClass('init_ok');

        this.initEvent();
    };

    BXmakerGeoIPLine.prototype.initEvent = function(){

        var bQuestionShow = false;
        var that = this;
        var timeout = false;


        if(BX.message('BXMAKER_GEOIP_PARAMS_CITY_QUESTION_ON') == 'Y')
        {
            if(window.BXmakerGeoIP.cookie('line_city_check') == 'Y')
            {
                bQuestionShow = false;
            }
            else
            {
                that.checkCityBoxShow();
                bQuestionShow = true;
            }
        }              


        this.box
            .on("mouseenter", '.city_box', function(){

                if(!!timeout) clearTimeout(timeout);

                if(bQuestionShow)
                {
                    that.checkCityBoxShow();
                }
                else
                {
                    that.infoBoxShow();
                }
            })
            .on("mouseleave", '.city_box', function(){

                if(!!timeout) clearTimeout(timeout);

                if(bQuestionShow)
                {
                    timeout = setTimeout($bxm.proxy(that.checkCityBoxHide, that), 500);
                }
                else
                {
                    timeout = setTimeout($bxm.proxy(that.infoBoxHide, that), 500);
                }
            })
            .on("click", '.city_question_box .btn_no', function(){
                window.BXmakerGeoIP.cityChangeBoxShow();
                that.checkCityBoxHide();
                window.BXmakerGeoIP.cookie('line_city_check', 'Y');
                bQuestionShow = false;
            })
            .on("click", '.city_question_box .btn_yes', function(){
                that.checkCityBoxHide();
                window.BXmakerGeoIP.cookie('line_city_check', 'Y');
                bQuestionShow = false;
            })
            .on("click", '.btn-city-change', function(){
                window.BXmakerGeoIP.cityChangeBoxShow();
                that.infoBoxHide();
            });

    };

    BXmakerGeoIPLine.prototype.checkCityBoxShow = function(){
        this.box.find('.city_question_box').fadeIn(200);
    };
    BXmakerGeoIPLine.prototype.checkCityBoxHide = function(){
        this.box.find('.city_question_box').fadeOut(200);
    };

    BXmakerGeoIPLine.prototype.infoBoxShow = function(){
        if(BX.message('BXMAKER_GEOIP_PARAMS_DELIVERY_INFO_ON') == 'Y') {
            this.box.find('.city_info_box').fadeIn(200);
        }
    };
    BXmakerGeoIPLine.prototype.infoBoxHide = function(){
        this.box.find('.city_info_box').fadeOut(200);
    };
}