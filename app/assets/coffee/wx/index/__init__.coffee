angular.module('wx', ["ngRoute","tpl","common"]) # ngLocale: 本地化年月日，如Datepicker 
.config(['$routeProvider','$locationProvider',($routeProvider,$locationProvider) ->
    $routeProvider
        .when('/', {templateUrl: 'wx/index/index.html',controller: 'index'})
        .when('/wx/goods/:id', {templateUrl: 'wx/index/goods.html',controller: 'goods'})
        .when('/wx/cart', {templateUrl: 'wx/index/cart.html',controller: 'cart'})
        .when('/wx/cart/delivery_time', {templateUrl: 'wx/index/cart.html',controller: 'cartDeliveryTime'})
        .when('/wx/me', {templateUrl: 'wx/index/me.html',controller: 'me'})
        .when('/wx/setting/community', {templateUrl: 'wx/index/setting.html',controller: 'settingCommunity'})
        .when('/wx/setting/room', {templateUrl: 'wx/index/setting.html',controller: 'settingRoom'})
        .when('/wx/setting/nolocation', {templateUrl: 'wx/index/setting.html',controller: 'settingNolocation'})
        .when('/wx/setting/location', {templateUrl: 'wx/index/setting.html',controller: 'settingLocation'})
        .when('/wx/setting/location/:from', {templateUrl: 'wx/index/setting.html',controller: 'settingLocation'})
        .when('/wx/setting/hongbao', {templateUrl: 'wx/index/setting.html',controller: 'settingHongbao'})
        .when('/wx/address', {templateUrl: 'wx/index/address.html',controller: 'addressIndex'})
        .when('/wx/address/:id', {templateUrl: 'wx/index/address.html',controller: 'addressIndex'})
        .when('/wx/order', {templateUrl: 'wx/index/order.html',controller: 'orderIndex'})
        .when('/wx/hongbao', {templateUrl: 'wx/index/hongbao.html',controller: 'hongbao'})
        .when('/wx/order/detail/:id', {templateUrl: 'wx/index/order.html',controller: 'orderDetail'})
        .when('/wx/me/dashboard', {templateUrl: 'wx/index/dashboard.html',controller: 'dashboard'})
        .when('/wx/me/about', {templateUrl: 'wx/index/about.html',controller: 'about'})
        .when('/wx/games/hongbao', {templateUrl: 'wx/index/games.html',controller: 'gamesHongbao'})
        
        # .when('/wx/store/order:id', {templateUrl: 'wx/index/store.html',controller: 'storeOrder'})
        .otherwise(redirectTo: '/')
    $locationProvider.html5Mode(true).hashPrefix('')
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
    return $location.path('/wx/setting/nolocation') if !data.user and '/wx/setting/nolocation' != $location.path()
])

