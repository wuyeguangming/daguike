angular.module('common')
# 用于突破ng-href的束缚，添加(herf='xxx' goto)后，可任意跳转到xxx
.directive('hrefBase', ['$location','$rootScope','$window',($location,$rootScope,$window) ->
    return (scope, elm, attrs) ->
        elm.bind 'click', ->
            if attrs.href && attrs.hrefBase && (0 != attrs.href.indexOf(attrs.hrefBase))
                $rootScope.$on "$routeChangeStart", (event, next, current) ->
                    # // next.$$route <-not set when routed through 'otherwise' since none $route were matched
                    if (next && !next.$$route)
                        event.preventDefault() # Stops the ngRoute to proceed with all the history state logic
                        # // We have to do it async so that the route callback 
                        # // can be cleanly completed first, so $timeout works too
                        $rootScope.$evalAsync ->
                            # // next.redirectTo would equal be 'http://yourExternalSite.com/404.html'
                            $window.location.href = attrs.href
])