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
            // window.history.pushState = undefined;
        </script>
        {{iasset([
                'css/wx/common/base.css',
                'css/wx/common/mobile-angular-ui-base.css',

                'js/wx/__init__/libs.js',
                'js/wx/common/__init__.js',
                'js/wx/user/__init__.js',
                'js/wx/user/bind.js',
                'tpl/wx/user/bind.tpl.js',
            ],[
        ])}}
    </head>

    <body ng-app="wx">
        <div class="app">
            <div class="app-content" id="wrapper">
              <ng-view id="scroller"></ng-view>
            </div>
          </div>

        </div>

    @if (isWeixin() && Config::get('app.debug'))
        <script src="http://192.168.0.103:8080/target/target-script-min.js#anonymous"></script>
    @endif
    </body>
</html>
