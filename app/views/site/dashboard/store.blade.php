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
        'css/dashboard/store/libs.css',
        'css/dashboard/store/index.css',

        'js/common/__init__/libs.js',
        'js/common/base/base.js',
        'js/common/base/modal.js',
        'tpl/common/base/modal.tpl.js',
        'js/common/service/service.js',
        'js/common/service/uploader.js',
        'js/common/directive/utils.js',
        'js/common/directive/location.js',
        'tpl/common/directive/location.tpl.js',


        'js/common/module/album.js',
        'js/dashboard/__init__/libs.js',
        'js/dashboard/store/__init__.js',
        'js/dashboard/common/directive.js',
        'tpl/dashboard/common/sidebar.tpl.js',
        'js/dashboard/store/create.js',
        'tpl/dashboard/store/create.tpl.js',
        'js/dashboard/store/publish.js',
        'tpl/dashboard/store/publish.tpl.js',
        'js/dashboard/store/album.js',
        'tpl/dashboard/store/album.tpl.js',
        'js/dashboard/store/manage.js',
        'tpl/dashboard/store/manage.tpl.js',
        'js/dashboard/store/order.js',
        'tpl/dashboard/store/order.tpl.js',
        'js/dashboard/store/bill.js',
        'tpl/dashboard/store/bill.tpl.js',
        'js/dashboard/store/refund.js',
        'tpl/dashboard/store/refund.tpl.js',
        'js/dashboard/store/location.js',
        'tpl/dashboard/store/location.tpl.js',
    ],[
        'css/common/base/all.css',
        'css/common/directive/all.css',
        'css/dashboard/common/all.css',
        'css/dashboard/store/all.css',

        'js/common/__init__/all.js',
        'js/common/base/all.js',
        'tpl/common/base/all.js',
        'js/common/service/all.js',
        'js/common/directive/all.js',
        'tpl/common/directive/all.js',

        'js/common/module/all.js',
        'js/dashboard/__init__/all.js',
        'js/dashboard/store/all.js',
        'js/dashboard/common/all.js',

        'tpl/dashboard/common/all.js',
        'tpl/dashboard/store/all.js',
    ])}}
@stop
