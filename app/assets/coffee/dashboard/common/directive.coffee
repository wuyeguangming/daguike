angular.module('dashboard')
.directive 'sidebar', ($rootScope) ->
    restrict: 'EA'
    replace: false
    templateUrl: 'dashboard/common/sidebar.html'
    scope:
        user: '='
    link:(scope, iElement, iAttrs) ->
        scope.hrefBase = iAttrs.hrefBase