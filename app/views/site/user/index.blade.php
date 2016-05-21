@extends('site.layouts.default')
@section('content')
    <body ng-app="user">
        <div ng-view>
            <sidebar></sidebar>
        </div>
    </body>
@stop

@section('asset')
{{iasset([
        'css/common/base/libs.css',
        'css/common/base/common.css',
        'css/common/base/bootstrap.css',
        'css/common/base/font-awesome.css',
        'css/common/directive/location.css',
        'css/user/index/index.css',

        'js/common/__init__/libs.js',
        'js/common/base/base.js',
        'js/common/service/service.js',
        'js/common/directive/location.js',
        'tpl/common/directive/location.tpl.js',

        'js/user/index/__init__.js',
        'js/user/index/confirm.js',
        'js/user/index/create.js',
        'js/user/index/forgot.js',
        'js/user/index/login.js',
        'js/user/index/reset.js',

        'tpl/user/index/confirm.tpl.js',
        'tpl/user/index/create.tpl.js',
        'tpl/user/index/forgot.tpl.js',
        'tpl/user/index/login.tpl.js',
        'tpl/user/index/reset.tpl.js'

    ],[
        'css/common/base/all.css',
        'css/common/directive/all.css',
        'css/user/index/all.css',
        'js/common/__init__/all.js',
        'js/common/base/all.js',
        'js/common/service/all.js',
        'js/common/directive/all.js',
        'js/user/index/all.js',
        'tpl/common/directive/all.js',
        'tpl/user/index/all.js'
    ])}}
@stop
