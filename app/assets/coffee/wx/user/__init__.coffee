angular.module('wx', ["ngRoute","mobile-angular-ui","tpl","common"]) # ngLocale: 本地化年月日，如Datepicker 
.config(['$routeProvider','$locationProvider',($routeProvider,$locationProvider) ->
    $routeProvider
        .when('/wx/user/bind', {templateUrl: 'wx/user/bind.html',controller: 'bind'})
        .otherwise(redirectTo: '/wx/user/bind')
    $locationProvider.html5Mode(true).hashPrefix('')
])
.run(['$rootScope',($rootScope)->
    $rootScope.data = data
    $rootScope.wx = wx
    $rootScope.wx_ready = false
    $rootScope.wx.ready -> $rootScope.wx_ready = true

])

