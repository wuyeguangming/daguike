<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>{{$title}}</title>
        <base href="/" /> 
        <meta name="keywords" content="大贵客，daguike" />
        <meta name="description" content="大贵客daguike.com-为社区提供专业的电商服务。快速、精准、有趣，为您提供最便捷的购物体验! " />
        <script type="text/javascript">
            window.data = {{$data or '{}'}};
        </script>
        @yield('asset')
    </head>

    @yield('content')
</html>
