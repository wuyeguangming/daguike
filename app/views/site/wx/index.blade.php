<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no"/>
        <title>{{$title}}</title>
        <base href="/" />
        <meta name="keywords" content="大贵客，daguike" />
        <meta name="description" content="大贵客daguike.com-为社区提供专业的电商服务。快速、精准、有趣，为您提供最便捷的购物体验! " />
        <script type="text/javascript">
            window.data = {{$data or '{}'}};
            window.history.pushState = undefined;
            href = location.href.split('state=wx_auth_redirect');
            if (href.length>=2) {
                location.href = location.href.split('?')[0];//去除?code=..(bug)
            };
        </script>
        {{iasset([
                'css/wx/common/base.css',
                'css/wx/common/mobile-angular-ui-base.css',
                'css/wx/index/index.css',

                'js/wx/__init__/libs.js',
                'js/wx/common/__init__.js',
                'js/wx/common/directive.js',
                'js/wx/common/filter.js',
                'tpl/wx/common/option.tpl.js',
                'tpl/wx/common/navbar.tpl.js',
                'tpl/wx/common/media.tpl.js',
                'tpl/wx/common/modal.tpl.js',
                'js/wx/index/__init__.js',
                'js/wx/index/wx_cart.js',
                'js/wx/index/index.js',
                'tpl/wx/index/index.tpl.js',
                'js/wx/index/cart.js',
                'tpl/wx/index/cart.tpl.js',
                'js/wx/index/me.js',
                'tpl/wx/index/me.tpl.js',
                'js/wx/index/order.js',
                'tpl/wx/index/order.tpl.js',
                'js/wx/index/goods.js',
                'tpl/wx/index/goods.tpl.js',
                'js/wx/index/setting.js',
                'tpl/wx/index/setting.tpl.js',
                'js/wx/index/address.js',
                'tpl/wx/index/address.tpl.js',
                'js/wx/index/hongbao.js',
                'tpl/wx/index/hongbao.tpl.js',
                'js/wx/index/dashboard.js',
                'tpl/wx/index/dashboard.tpl.js',
                'js/wx/index/about.js',
                'tpl/wx/index/about.tpl.js',
                'js/wx/index/games.js',
                'tpl/wx/index/games.tpl.js',
                
            ],[
                'css/wx/common/all.css',
                'css/wx/index/all.css',
                'js/wx/__init__/all.js',
                'js/wx/common/all.js',
                'js/wx/index/all.js',
                'tpl/wx/common/all.js',
                'tpl/wx/index/all.js',
        ])}}
    </head>

    <body ng-app="wx" ng-cloak>
        <!-- <div ng-include="'wx/index/navbar.html'"></div> -->
        <ng-view id="view"></ng-view>
        <div class="sk-spinner sk-spinner-three-bounce" ng-show="$root.__loading__">
            <div class="sk-bounce1"></div>
            <div class="sk-bounce2"></div>
            <div class="sk-bounce3"></div>
        </div>
        <img id="_hm" src="" width="0" height="0">
    </body>
<!-- 
    @if (isWeixin() && Config::get('app.debug'))
        <script src="http://192.168.0.103:8080/target/target-script-min.js#anonymous"></script>
    @endif -->
    {{-- <img src="{{$_hmtPixel}}" width="0" height="0" /> --}}
</html>
