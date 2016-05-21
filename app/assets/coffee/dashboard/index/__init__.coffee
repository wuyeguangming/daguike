angular.module('dashboard', ['common','tpl','ngRoute','ui.bootstrap', 'ngCookies','ngLocale']) # ngLocale: 本地化年月日，如Datepicker 
.config(['$routeProvider','$locationProvider',($routeProvider,$locationProvider) ->
    $routeProvider
        .when('/dashboard/index', {templateUrl: 'dashboard/index/index.html',controller: 'index'})
        .when('/dashboard/index/settings', {templateUrl: 'dashboard/index/settings.html',controller: 'settings'})
        .when('/dashboard/index/password', {templateUrl: 'dashboard/index/password.html',controller: 'password'})
        # .when('/dashboard/store/create', {templateUrl: 'dashboard/store/create.html',controller: 'storeCreate'})
        .otherwise(redirectTo: '/dashboard/index')
    $locationProvider.html5Mode(true).hashPrefix('!');
])
.run(['$rootScope',($rootScope) ->
    $rootScope.PASSWORD_MIN = 6
    $rootScope.PASSWORD_MAX = 20
    $rootScope.invalid_password = (password)->  (/^[0-9]+$/).test(password) || (/^[A-Za-z]+$/).test(password)
])
