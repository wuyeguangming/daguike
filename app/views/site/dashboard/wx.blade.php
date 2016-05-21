@extends('site.layouts.default')
@section('content')
    <body ng-app="wx">
        <div ng-view></div>
    </body>
@stop

@section('asset')
{{iasset([
    ],[
    ])}}
@stop
