angular.module('common')
.config(['$httpProvider', '$sceDelegateProvider','$provide', ($httpProvider, $sceDelegateProvider,$provide) -> 
    $httpProvider.defaults.headers.common["Is-Ajax"] = true
    $httpProvider.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded; charset=UTF-8'# 默认为JSONP参数传递模式
    $httpProvider.defaults.transformRequest = (data) ->
        return if data? then ($.param data) else data # jquery json转字符串,必须判断,否则jquery会报错
    $sceDelegateProvider.resourceUrlWhitelist ['**']# todo: 列出所有白名单
    $provide.decorator '$controller', ['$delegate','$http', ($delegate,$http)->
        return (constructor, locals, later, indent)->
            if (typeof constructor == "string" and constructor.split('common').length<=1) # 防止指令中的controller(以common开头)造成干扰
                $http.defaults.headers.common["controller"] = constructor
                locals.$scope.$root._controller = constructor 
            return $delegate(constructor, locals, later, indent)
    ]
])
.config(['cfpLoadingBarProvider', (cfpLoadingBarProvider)->
    cfpLoadingBarProvider.includeBar = true
    cfpLoadingBarProvider.latencyThreshold = 500
])
# .config(['$compileProvider',($compileProvider)->
#   $compileProvider.aHrefSanitizationWhitelist(/^\s*(app|file|http|https):/)
#   $compileProvider.imgSrcSanitizationWhitelist(/^\s*(app|file|http|https):/)
# ])
.factory('http', ['$http','$q','$routeParams','$rootScope','cfpLoadingBar',($http,$q,$routeParams,$rootScope,cfpLoadingBar) -> 
    # put: (url,data)->
    #     cfpLoadingBar.start()
    #     deferred = $q.defer()
    #     $http.put(url,data).then (data)->
    post: (path, val) ->
        cfpLoadingBar.start()
        deferred = $q.defer()
        # $http.post(app.server.req(path), data)
        val._token = $rootScope.data._token
        $http.post(path, val)
            .success (val, status, headers, config) ->
                $rootScope.data._token = val._token if val._token
                cfpLoadingBar.complete()
                if (-1 != val.code) then deferred.resolve(val) else deferred.reject(val.info)
            .error (val, status, headers, config) ->
                $rootScope.data._token = val._token if val._token
                cfpLoadingBar.complete()
                deferred.reject('网络异常！') #TODO
        return deferred.promise
    get: (path, val) ->
        cfpLoadingBar.start()
        deferred = $q.defer()
        $http.get(path, val)
            .success (val, status, headers, config) ->
                $rootScope.data._token = val._token if val._token
                cfpLoadingBar.complete()
                location.href = val.url if val.url # 立即静默跳转
                if (-1 != val.code) then deferred.resolve(val) else deferred.reject(val.info)
            .error (val, status, headers, config) ->
                $rootScope.data._token = val._token if val._token
                cfpLoadingBar.complete()
                deferred.reject('网络异常！') #TODO
        return deferred.promise
])
.factory('commonService', ['$rootScope','http','$location',($rootScope,http,$location) -> 
    init: (name,success,error)->
        $rootScope._path = $location.path()
        if !$rootScope.__init__?
            # 首次载入
            window.data = data
            $rootScope.data = data
            $rootScope.__init__ = true
            $rootScope.__controller__ = name
            $rootScope[name] = {}
            success() if success
        else
            # # 页面跳转
            # if !$rootScope[name]?
            #     # 首次跳转
            #     $rootScope[name] = {}
            #     http.get($location.url()).then (data)->
            #         $rootScope.data = data
            #         success() if success
            #     ,(e)->
            #         error(e) if error
            # else
            #     # 多次跳转，已有缓存
            #     success() if success
            $rootScope[name] = {}
            http.get($location.url()).then (data)->
                window.data = data
                $rootScope.data = data
                success() if success
            ,(e)->
                error(e) if error
])