<!-- begin::Global Config(global config for global JS sciprts) -->
<script>
    var KTAppOptions = {
        "colors": {
            "state": {
                "brand": "#2c77f4",
                "light": "#ffffff",
                "dark": "#282a3c",
                "primary": "#5867dd",
                "success": "#34bfa3",
                "info": "#36a3f7",
                "warning": "#ffb822",
                "danger": "#fd3995"
            },
            "base": {
                "label": ["#c5cbe3", "#a1a8c3", "#3d4465", "#3e4466"],
                "shape": ["#f0f3ff", "#d9dffa", "#afb4d4", "#646c9a"]
            }
        }
    };
</script>
<!-- end::Global Config -->

<!--begin::Global Theme Bundle(used by all pages) -->
<script src="{{ asset('backend/themes/metronic1/plugins/global/plugins.bundle.js?v=2') }}"
        type="text/javascript"></script>
<script src="{{ asset('backend/themes/metronic1/js/scripts.bundle.js') }}"
        type="text/javascript"></script>
<script src="{{ asset('libs/datetimepicker/bootstrap-datetimepicker.js') }}"></script>
<script src="{{ asset('libs/datetimepicker/moment-with-locales.js') }}"></script>

<!--end::Global Theme Bundle -->

<!--begin::Page Vendors(used by this page) -->

<!--end::Page Vendors -->

<script src="{{ asset('libs/toastr/toastr.min.js') }}"></script>
<script src="{{ asset('backend/themes/metronic1/js/common.js') }}"></script>
@if(Session::has('message'))
    <script>
        toastr.{{ Session::get('alert-class') }}("{!! Session::get('message') !!}");
    </script>
@endif
<script type="text/javascript">
    $(document).on('click', '.file_image_thumb', function () {
        // alert($(this).attr('src').split('timthumb.php?src=')[1]);
        if ($(this).attr('src').split('_thumbs')[1] == undefined) {
            $('#blank_modal .modal-body').html('<img src="' + $(this).attr('src') + '"/>');
        } else {
            var str = $(this).attr('src').replace('/_thumbs', '');
            var str2 = str.split('-')[str.split('-').length - 1];
            if (str2 != undefined && str2 != '') {
                str = str.replace('-' + str2, '');
                str = str + '.' + str2.split('.')[str2.split('.').length - 1]
            }
            $('#blank_modal .modal-body').html('<img src="' + str + '"/>');
        }
        $('#blank_modal').modal();
    });
    $(document).on('click', '.dz-image', function () {
        let str = $(this).data('background');
        /*var str2 = str.split('-')[str.split('-').length - 1];
        if (str2 != undefined && str2 != '') {
            str = str.replace('-' + str2, '');
            str = str + '.' + str2.split('.')[str2.split('.').length - 1]
        }*/
        $('#blank_modal .modal-body').html('<img src="' + str + '"/>');
        $('#blank_modal').modal();
    });

    // $('.dz-details').attr('alt', 'Bấm để phóng to ảnh');
    // $(document).on('hover', '.dz-details', function () {
    //     alert(1)
    // });

    $(document).on('click', '.box-header', function () {
        $(this).parents('.box').find('.tab-content').slideToggle();
    });
</script>
<script>
    $(document).ready(function () {
        $('.a-tooltip_info').hover(function () {
            var div = $(this).parents('td').find('.div-tooltip_info');
            if (div.html().trim() == '<img class="tooltip_info_loading" src="/images_core/icons/loading.gif">') {
                var id = div.parents('tr').find('td:first-child').text();
                $.ajax({
                    url: '/admin/tooltip-info',
                    type: 'GET',
                    data: {
                        id: id,
                        modal: div.data('modal'),
                        tooltip_info: div.data('tooltip_info'),
                    },
                    success: function (result) {
                        div.html(result);
                    },
                    error: function () {
                        console.log('tooltip-info Có lỗi xảy ra!');
                    }
                });
            }
        });
    });
</script>

{{--Ladyload--}}
<script>
    setTimeout(function(){
        !function (e) {
            document.createElement("style").innerHTML = "img:not([src]) {visibility: hidden;}";

            function t(e, t) {
                var n = new Image, r = e.getAttribute("data-src");
                n.onload = function () {
                    e.parent ? e.parent.replaceChild(n, e) : e.src = r, e.style.opacity = "1", t && t()
                }, n.src = r
            }

            for (var n = new Array, r = function (e, t) {
                if (document.querySelectorAll) t = document.querySelectorAll(e); else {
                    var n = document, r = n.styleSheets[0] || n.createStyleSheet();
                    r.addRule(e, "f:b");
                    for (var i = n.all, l = 0, c = [], o = i.length; l < o; l++) i[l].currentStyle.f && c.push(i[l]);
                    r.removeRule(0), t = c
                }
                return t
            }("img.lazy"), i = function () {
                for (var r = 0; r < n.length; r++) i = n[r], l = void 0, (l = i.getBoundingClientRect()).top >= 0 && l.left >= 0 && l.top <= (e.innerHeight || document.documentElement.clientHeight) && t(n[r], function () {
                    n.splice(r, r)
                });
                var i, l
            }, l = 0; l < r.length; l++) n.push(r[l]);
            i(), function (t, n) {
                e.addEventListener ? this.addEventListener(t, n, !1) : e.attachEvent ? this.attachEvent("on" + t, n) : this["on" + t] = n
            }("scroll", i)
        }(this);
    }, 20);

</script>

<script type="text/javascript">


    $(window).scroll(function(e) {
      if ($(document).scrollTop() > 80) {
        $('.button-save').addClass("button-save-fixed");
      } else {
        $('.button-save').removeClass("button-save-fixed");
      }
      
    });
</script>

{!! Eventy::filter('footer_script.script', '') !!}

@stack('scripts')
@yield('custom_footer')
{!! @$settings['admin_footer_code'] !!}
