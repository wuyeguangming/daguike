@extends('site.layouts.default')
@section('content')
    <body ng-app="dashboard" id="view">
        <div ng-view></div>
    </body>
@stop

@section('asset')
{{iasset([
        'css/common/base/libs.css',
        'css/common/base/common.css',
        'css/common/base/bootstrap.css',
        'css/common/base/font-awesome.css',
        'css/common/directive/location.css',
        'css/dashboard/common/index.css',

        'js/common/__init__/libs.js',
        'js/common/base/base.js',
        'js/common/service/service.js',
        'js/common/directive/utils.js',
        'js/common/directive/location.js',

        'js/dashboard/index/__init__.js',
        'js/dashboard/common/directive.js',
        'js/dashboard/index/index.js',
        'js/dashboard/index/settings.js',
        'js/dashboard/index/password.js',

        'tpl/common/directive/location.tpl.js',
        'tpl/dashboard/index/index.tpl.js',
        'tpl/dashboard/index/settings.tpl.js',
        'tpl/dashboard/index/password.tpl.js',
        'tpl/dashboard/common/sidebar.tpl.js',
    ],[
        'css/common/base/all.css',
        'css/common/directive/all.css',
        'css/dashboard/common/all.css',

        'js/common/__init__/all.js',
        'js/common/base/all.js',
        'js/common/service/all.js',
        'js/common/directive/all.js',

        'js/dashboard/index/all.js',
        'js/dashboard/common/all.js',


        'tpl/common/directive/all.js',
        'tpl/dashboard/index/all.js',
        'tpl/dashboard/common/all.js',
    ])}}
@stop
