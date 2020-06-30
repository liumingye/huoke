/*function initSparkline() {
    $(".sparkline").each(function () {
        var e = $(this);
        e.sparkline("html", e.data())
    }),
        $(".sparkbar").sparkline("html", {
            type: "bar"
        })
}*/
// 时间格式化
function DateToTime(unixTime = (new Date()).valueOf(), type = "Y-M-D H:i:s") {
    var date = new Date(unixTime);
    var datetime = "";
    datetime += date.getFullYear() + type.substring(1, 2);
    datetime += (date.getMonth() + 1 < 10 ? '0' + (date.getMonth() + 1) : date.getMonth() + 1) + type.substring(3, 4);
    datetime += (date.getDate() < 10 ? '0' + (date.getDate()) : date.getDate());
    if (type.substring(5, 6)) {
        if (type.substring(5, 6).charCodeAt() > 255) {
            datetime += type.substring(5, 6);
            if (type.substring(7, 8)) {
                datetime += " " + (date.getHours() < 10 ? '0' + (date.getHours()) : date.getHours());
                if (type.substring(9, 10)) {
                    datetime += type.substring(8, 9) + (date.getMinutes() < 10 ? '0' + (date.getMinutes()) : date.getMinutes());
                    if (type.substring(11, 12)) {
                        datetime += type.substring(10, 11) + (date.getSeconds() < 10 ? '0' + (date.getSeconds()) : date.getSeconds());
                    };
                };
            };
        } else {
            datetime += " " + (date.getHours() < 10 ? '0' + (date.getHours()) : date.getHours());
            if (type.substring(8, 9)) {
                datetime += type.substring(7, 8) + (date.getMinutes() < 10 ? '0' + (date.getMinutes()) : date.getMinutes());
                if (type.substring(10, 11)) {
                    datetime += type.substring(9, 10) + (date.getSeconds() < 10 ? '0' + (date.getSeconds()) : date.getSeconds());
                };
            };
        };
    };
    return datetime;
}
// 打开链接
function openUrl(url) {
    if ($("#openUrl").length === 0) {
        $("body").append('<a target="_blank" id="openUrl"></a>');
    }
    var a = $("#openUrl");
    a.attr("href", url);
    a[0].click();
};
// ajax请求
$(document).on('click', '.j-ajax', function () {
    var that = $(this),
        href = that.attr('href'),
        refresh = that.hasClass('refresh') ? true : false,
        confirm = that.hasClass('confirm') ? true : false,
        closebtn = that.hasClass('closebtn') ? true : false,
        html = that.html();
    var ajax = function () {
        that.text('...').css("pointer-events", "none");
        $.ajax({
            url: href,
            type: "POST",
            data: $('#form-post').serialize(),
            success: function (data) {
                if (data.code == 1) {
                    parent.swal({
                        type: "success",
                        title: data.msg,
                        cancelButtonText: "关闭选项卡",
                        showConfirmButton: true,
                        showCancelButton: closebtn ? true : false,
                    }, function (e) {
                        !e && (self != top ? parent.tab.close_this() : window.close());
                    });
                    if (refresh) {
                        if (typeof (data.url) != 'undefined' && data.url != null && data.url != '') {
                            location.href = data.url;
                        } else {
                            location.reload();
                        }
                    }
                } else {
                    parent.swal({
                        type: "error",
                        title: data.msg,
                        confirmButtonText: "确认"
                    });
                }
            },
            error: function () {
                parent.swal({
                    type: "error",
                    title: "请求失败",
                    confirmButtonText: "确认"
                });
            },
            complete: function () {
                that.html(html).css("pointer-events", "");
            }
        });
    }
    if (confirm) {
        parent.swal({
            title: that.data("title") ? that.data("title") : "您确定要执行此操作吗?",
            text: that.data("text"),
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#dc3545",
            confirmButtonText: "确认",
            cancelButtonText: "取消",
        }, function () {
            ajax();
        });
    } else {
        ajax();
    }
    return false;
});
// 列表按钮组
$('.j-page-btns').click(function () {
    var that = $(this),
        confirm = that.hasClass('confirm') ? true : false,
        href = that.attr('href'),
        html = that.html();
    if ($('input[name="ids[]"]:checked').length <= 0) {
        parent.swal({
            type: "error",
            title: "请选择要操作的数据",
            confirmButtonText: "确认"
        });
        return false;
    }
    var ajax = function () {
        that.html('提交中...').css("pointer-events", "none");
        var query = $('#myDatatable').parents('form').serialize();
        $.ajax({
            url: href,
            type: "POST",
            data: query,
            success: function (data) {
                if (data.code == 1) {
                    parent.swal({
                        type: "success",
                        title: data.msg,
                    }, function () {
                        that.html(html);
                        location.reload();
                    });
                } else {
                    parent.swal({
                        type: "error",
                        title: data.msg,
                        confirmButtonText: "确认"
                    });
                }
            },
            error: function () {
                parent.swal({
                    type: "error",
                    title: "请求失败",
                    confirmButtonText: "确认"
                });
            },
            complete: function () {
                that.html(html).css("pointer-events", "");
            }
        });
    };
    if (confirm) {
        parent.swal({
            title: that.data("title") ? that.data("title") : "您确定要执行此操作吗?",
            text: that.data("text"),
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#dc3545",
            confirmButtonText: "确认",
            cancelButtonText: "取消",
        }, function () {
            ajax();
        });
    } else {
        ajax();
    }
    return false;
});

/*function skinChanger() {
    $(".choose-skin li").on("click",
        function () {
            var e = $("body"),
                t = $(this),
                a = $(".choose-skin li.active").data("theme");
            $(".choose-skin li").removeClass("active");
            e.removeClass("theme-" + a);
            t.addClass("active");
            $(".choose-skin li.active").data("theme");
            e.addClass("theme-" + t.data("theme"));
            $("#left-sidebar .navbar-brand .logo").attr("src", "assets/images/icon.svg")
        }
    )
}*/
$(function () {
    //skinChanger()
    $(".page-loader-wrapper").fadeOut(200)
    //initSparkline()
    /* var e = function () {
         for (var e = new Array(20), t = 0; t < e.length; t++) e[t] = [5 + a(), 10 + a(), 15 + a(), 20 + a(), 30 + a(), 35 + a(), 40 + a(), 45 + a(), 50 + a()];
         return e
     }()
     var t = {
         type: "bar",
         barWidth: 3,
         height: 15,
         barColor: "#0E9BE2"
     };
     function a() {
         return Math.floor(80 * Math.random())
     }
     $("#mini-bar-chart1").sparkline(e[0], t)
     t.barColor = "#7CAC25"
     $("#mini-bar-chart2").sparkline(e[1], t)
     t.barColor = "#FF4402"
     $("#mini-bar-chart3").sparkline(e[2], t)
     t.barColor = "#ff9800"*/
})
$(document).ready(function () {
    $(".btn-toggle-fullwidth").on("click",
        function () {
            $("body").hasClass("layout-fullwidth") ? $("body").removeClass("layout-fullwidth") : $("body").addClass("layout-fullwidth"),
                $(this).find(".fa").toggleClass("fa-arrow-left fa-arrow-right")
        }
    )
    $(".btn-toggle-offcanvas").on("click",
        function () {
            $("body").toggleClass("offcanvas-active")
        }
    )
    $("#navTabContent").on("click",
        function () {
            $("body").removeClass("offcanvas-active")
        }
    )
    $(".right_toggle, .overlay").on("click",
        function () {
            $("#rightbar").toggleClass("open"),
                $(".overlay").toggleClass("open")
        }
    )
    $(".megamenu_toggle").on("click",
        function () {
            $("#megamenu").toggleClass("open")
        }
    )
    $('.navbar-form.search-form input[type="text"]').on("focus",
        function () {
            $(this).animate({
                width: "+=50px"
            }, 300)
        }
    ).on("focusout",
        function () {
            $(this).animate({
                width: "-=50px"
            },
                300)
        }
    )
    $(".rightbar .right_chat li a, .rightbar .back_btn").on("click",
        function () {
            $("#rightbar").toggleClass("detail")
        }
    )
    0 < $('[data-toggle="tooltip"]').length && $('[data-toggle="tooltip"]').tooltip()
    0 < $('[data-toggle="popover"]').length && $('[data-toggle="popover"]').popover()
    $(window).on("load",
        function () {
            $("#main-content").height() < $("#left-sidebar").height() && $("#main-content").css("min-height", $("#left-sidebar").innerHeight() - $("footer").innerHeight())
        }
    )
    $(window).on("load resize",
        function () {
            $(window).innerWidth() < 420 ? $(".navbar-brand logo.svg").attr("src", "../assets/images/icon.svg") : $(".navbar-brand icon-light.svg").attr("src", "../assets/images/logo.svg")
        }
    )
    $(".full-screen").on("click",
        function () {
            $(this).parents(".card").toggleClass("fullscreen")
        }
    )
    $(".themesetting .theme_btn").on("click",
        function () {
            $(".themesetting").toggleClass("open")
        }
    )
    $(".header-dropdown .dropdown-toggle").on("click",
        function () {
            $(".header-dropdown li .dropdown-menu").toggleClass("vivify fadeIn")
        }
    )
    $(".check-all").on("click",
        function () {
            this.checked ? $(this).parents(".check-all-parent").find(".checkbox-tick").each(function () {
                this.checked = !0
            }) : $(this).parents(".check-all-parent").find(".checkbox-tick").each(function () {
                this.checked = !1
            })
        }
    )
    $(".checkbox-tick").on("click",
        function () {
            $(this).parents(".check-all-parent").find(".checkbox-tick:checked").length == $(this).parents(".check-all-parent").find(".checkbox-tick").length ? $(this).parents(".check-all-parent").find(".check-all").prop("checked", !0) : $(this).parents(".check-all-parent").find(".check-all").prop("checked", !1)
        }
    )
    $("a.mail-star").on("click",
        function () {
            $(this).toggleClass("active");
        }
    )
})
$.fn.clickToggle = function (t, a) {
    return this.each(function () {
        var e = !1;
        $(this).bind("click",
            function () {
                return e ? (e = !1, a.apply(this, arguments)) : (e = !0, t.apply(this, arguments))
            })
    })
}
