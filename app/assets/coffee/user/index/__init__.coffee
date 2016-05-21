angular.module('user', ['common','tpl','ngRoute','ui.bootstrap', 'ngCookies'])
#---------------------------------------------------------------------------------------------------------  init
.config(['$routeProvider','$locationProvider',($routeProvider,$locationProvider) ->
    $routeProvider
        .when('/user/create', {templateUrl: 'user/index/create.html',controller: 'create'})
        .when('/user/login', {templateUrl: 'user/index/login.html',controller: 'login'})
        .when('/user/forgot', {templateUrl: 'user/index/forgot.html',controller: 'forgot'})
        .when('/user/reset/:x', {templateUrl: 'user/index/reset.html',controller: 'reset'})
        .when('/user/confirm/:x', {templateUrl: 'user/index/confirm.html',controller: 'confirm'})
        .otherwise(redirectTo: '/user/login')
    $locationProvider.html5Mode(true).hashPrefix('!');
])
.run(['$rootScope',($rootScope) ->
    $rootScope.USER_MIN = 4
    $rootScope.USER_MAX = 16
    $rootScope.PASSWORD_MIN = 6
    $rootScope.PASSWORD_MAX = 20

    # todo 写成factory
    $rootScope.invalid_password = (password)-> (/^[0-9]+$/).test(password) || (/^[A-Za-z]+$/).test(password)
])