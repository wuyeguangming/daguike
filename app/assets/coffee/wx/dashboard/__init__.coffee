angular.module('wx', ["ngRoute","tpl","common"]) # ngLocale: 本地化年月日，如Datepicker 
.config(['$routeProvider','$locationProvider',($routeProvider,$locationProvider) ->
    $routeProvider
        # .when('/wx/dashboard/order', {templateUrl: 'wx/dashboard/order.html',controller: 'orderIndex'})
        .when('/wx/dashboard/order-page', {templateUrl: 'wx/dashboard/order.html',controller: 'orderIndex'})
        .when('/wx/dashboard/order-detail/:id', {templateUrl: 'wx/dashboard/order.html',controller: 'orderDetail'})
        # .otherwise(redirectTo: '/wx/dashboard/order')
        .otherwise(redirectTo: '/wx/dashboard/order-page')
    # $locationProvider.html5Mode(true).hashPrefix('')
])
.run(['$rootScope','$location','$timeout',($rootScope,$location,$timeout)->
    # todo: cache -> wx_cache
    $rootScope.cache = {}
    $rootScope.wx_navbar = {}
    $rootScope.wx_navbar.id = 1 #每个control还是要指定navbar.id，否则control之间跳转后会丢失
    $rootScope.data = data
    $rootScope.wx = wx
    $rootScope.wx_ready = false
    $rootScope.wx.ready -> $rootScope.wx_ready = true
    $rootScope.wx_config = (_wx)->
        wx.config
            debug: _wx.js_debug or false
            appId: _wx.js_sign.appid # 必填，公众号的唯一标识
            timestamp: _wx.js_sign.timestamp # 必填，生成签名的时间戳，切记时间戳是整数型，别加引号
            nonceStr: _wx.js_sign.noncestr # 必填，生成签名的随机串
            signature: _wx.js_sign.signature # 必填，签名，见附录1
            jsApiList: ['chooseWXPay']
        wx.error (res) ->
            console.log res
    return (location.href = '/') if !data.user
])

