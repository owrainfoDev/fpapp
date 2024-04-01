window.owra = (function (owra, $, undefined) {

    if ('undefined' === typeof window.owra) {
        var owra = window.owra = {};
    }

    document.addEventListener("DOMContentLoaded", function () {
        Util.init();
        UI.init();
    });
    var Util = owra.Util = {
            init: function () {
                this.Set();
            },
            cacheDom: function () {
                var cacheDomEls = {
                    $dom: $(document),
                    $html: $("html"),
                    $win: $(window),
                    body: document.getElementsByTagName("body")[0],
                    w: window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth,
                    h: window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight,
                    $wrap: $('#wrap'),
                    $header: $("#header"),
                    $content: $("#content"),
                    $slider: $(".visual-slider"),
                    $gnbWrap: $(".gnbWrap"),
                    $btn: $(".btnAll"),
                    $current: $(".btnCurrent"),
                    $gnb: $(".global-nav"),
                    $tab: $(".tabscript"),
                    $accord: $(".accord")
                }
                var getDom = function () {
                    return cacheDomEls;
                }
                return {
                    getDom: getDom
                }
            },
            Set: function () {
                var isMobile;
                var getView = {
                    mobFunc: function () {
                        if ($(".fusion-mobile-menu-icons").css("display") == "none") {
                            isMobile = "pc";
                        } else if (
                            $(".fusion-mobile-menu-icons").css("display") == "block"
                        ) {
                            isMobile = "mobile";
                        }
                        return isMobile;
                    }
                }
                var MobileChk = function () {
                    return getView;
                }
                return {
                    MobileChk: MobileChk
                }
            }
        },
        UI = owra.UI = {
            init: function () {
                this.MobileNav();
                jQuery(window).on({
                    "resize": function () {
                        var isMobile = Util.Set().MobileChk().mobFunc();
                        if (isMobile == "pc") {
                            $("html").css('overflow-y', 'scroll');
                            $("body").removeClass("modal-open");
                            $(".modal-back").remove();
                        }
                    },
                    "load": function () {

                    }
                })
            },
            MobileNav: function () {
                var $html = Util.cacheDom().getDom().$html,
                    body = Util.cacheDom().getDom().body,
                    $btn = Util.cacheDom().getDom().$btn,
                    $gnbLink = jQuery("html.gnbOpen .menu-gnb-container>ul>li>a"),
                    adminBar = $("#wpadminbar"),
                    chk = true;


                $btn.on("click", function (e) {
                    console.log(chk)
                    if (chk) {
                        $html.css('overflow-y', 'hidden');
                        body.classList.add("gnbOpen");
                        body.classList.add("modal-open");
                        // jQuery('<div class="modal-back fade in" />').appendTo(document.body);
                        chk = false;
                    } else {
                        chk = true;
                        $html.css('overflow-y', 'scroll');
                        body.classList.remove("gnbOpen");
                        body.classList.remove("modal-open");
                        jQuery(".modal-back").remove();
                    }
                });

                $(document).on('click', '.btn-close', function (e) {
                    chk = true;
                    $html.css('overflow-y', 'scroll');
                    body.classList.remove("gnbOpen");
                    body.classList.remove("modal-open");
                    jQuery(".modal-back").remove();
                });
                // 닫기버튼 누르지 않고 모달창 닫기
                $('.bg_wrap').click(function () {
                    console.log(1)
                    $html.css('overflow-y', 'scroll');
                    body.classList.remove("gnbOpen");
                    body.classList.remove("modal-open");
                    jQuery(".modal-back").remove();
                });

                (adminBar.length > 0) ? $(".custom-mobile-menu.nav-wrap").css("margin-top", "32px"): $(".custom-mobile-menu.nav-wrap").css("margin-top", "0")
            }
        }
})(window.owra || {}, jQuery);