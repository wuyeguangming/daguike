angular.module('dashboard', ['common','tpl','ngRoute','ui.bootstrap', 'ngCookies','ngLocale','angularFileUpload','ui.tree','ng-sortable']) # ngLocale: 本地化年月日，如Datepicker 
.config(['$routeProvider','$locationProvider',($routeProvider,$locationProvider) ->
    $routeProvider
        .when('/dashboard/store/create', {templateUrl: 'dashboard/store/create.html',controller: 'create'})
        .when('/dashboard/store/publish', {templateUrl: 'dashboard/store/publish.html',controller: 'publish'})
        .when('/dashboard/store/publish/:id', {templateUrl: 'dashboard/store/publish.html',controller: 'publish'})
        .when('/dashboard/store/album', {templateUrl: 'dashboard/store/album.html',controller: 'album'})
        .when('/dashboard/store/manage', {templateUrl: 'dashboard/store/manage.html',controller: 'manage'})
        .when('/dashboard/store/manage/:page', {templateUrl: 'dashboard/store/manage.html',controller: 'manage'})
        .when('/dashboard/store/order', {templateUrl: 'dashboard/store/order.html',controller: 'order'})
        .when('/dashboard/store/order/:id', {templateUrl: 'dashboard/store/order.html',controller: 'order'})
        .when('/dashboard/store/order-page', {templateUrl: 'dashboard/store/order.html',controller: 'order'})
        .when('/dashboard/store/order-page/:page', {templateUrl: 'dashboard/store/order.html',controller: 'order'})
        .when('/dashboard/store/bill-page', {templateUrl: 'dashboard/store/bill.html',controller: 'bill'})
        .when('/dashboard/store/bill-page/:page', {templateUrl: 'dashboard/store/bill.html',controller: 'bill'})
        .when('/dashboard/store/refund-page', {templateUrl: 'dashboard/store/refund.html',controller: 'refund'})
        .when('/dashboard/store/refund-page/:page', {templateUrl: 'dashboard/store/refund.html',controller: 'refund'})
        .when('/dashboard/store/location', {templateUrl: 'dashboard/store/location.html',controller: 'location'})
        
        .otherwise(redirectTo: '/dashboard/store/create')
    $locationProvider.html5Mode(true).hashPrefix('!');
])
.run(['$rootScope',($rootScope) ->
    $rootScope.PASSWORD_MIN = 6
    $rootScope.PASSWORD_MAX = 20
    $rootScope.invalid_password = (password)->  (/^[0-9]+$/).test(password) || (/^[A-Za-z]+$/).test(password)
])
