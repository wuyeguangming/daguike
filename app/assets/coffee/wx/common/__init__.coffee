angular.module('common',[])
.config(['$httpProvider', '$sceDelegateProvider','$provide', ($httpProvider, $sceDelegateProvider,$provide) -> 
    # Serialize an array of form elements or a set of
    # key/values into a query string
    r20 = /%20/g
    rbracket = /\[\]$/
    rCRLF = /\r?\n/g
    rsubmitterTypes = /^(?:submit|button|image|reset|file)$/i
    rsubmittable = /^(?:input|select|textarea|keygen)/i
    buildParams = (prefix, obj, traditional, add) ->
        if angular.isArray(obj)
            # Serialize array item.
            for v, i in obj
                if traditional or rbracket.test(prefix)
                    # Treat each array item as a scalar.
                    add prefix, v
                else
                    # Item is non-scalar (array or object), encode its numeric index.
                    buildParams prefix + '[' + (if typeof v == 'object' then i else '') + ']', v, traditional, add
        else if !traditional and typeof(obj) == 'object'
            # Serialize object item.
            for name of obj
                buildParams prefix + '[' + name + ']', obj[name], traditional, add
        else
            # Serialize scalar item.
            add prefix, obj
    _param = (a, traditional)->
        s = []

        add = (key, value) ->
            # If value is a function, invoke it and return its value
            value = if angular.isFunction(value) then value() else if value == null then '' else value
            s[s.length] = encodeURIComponent(key) + '=' + encodeURIComponent(value)

        # If an array was passed in, assume that it is an array of form elements.
        if angular.isArray(a) # or a.jquery and !jQuery.isPlainObject(a)
            # Serialize the form elements
            add @name, @value for i, v in a
        else
            # If traditional, encode the "old" way (the way 1.3.2 or older
            # did it), otherwise encode params recursively.
            (buildParams prefix, a[prefix], traditional, add) for prefix of a
        # Return the resulting serialization
        s.join('&').replace r20, '+'
    $httpProvider.defaults.headers.common["Is-Ajax"] = true
    $httpProvider.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded; charset=UTF-8'# 默认为JSONP参数传递模式
    $httpProvider.defaults.transformRequest = (val) ->
        return if val? then (_param val) else val # jquery json转字符串,必须判断,否则jquery会报错
    $sceDelegateProvider.resourceUrlWhitelist ['**']# todo: 列出所有白名单

    $provide.decorator '$controller', ['$delegate','$http', ($delegate,$http)->
        return (constructor, locals, later, indent)->
            if (typeof constructor == "string" and constructor.split('common').length<=1) # 防止指令中的controller(以common开头)造成干扰
                $http.defaults.headers.common["controller"] = constructor
                locals.$scope.$root._controller = constructor 
            return $delegate(constructor, locals, later, indent)
    ]
])
.factory('http', ['$http','$q','$routeParams','$rootScope','$location','$timeout',($http,$q,$routeParams,$rootScope,$location,$timeout) -> 
    post: (path, val) ->
        val = val or {}
        deferred = $q.defer()
        # $http.post(app.server.req(path), val)
        val._token = $rootScope.data._token
        val._referer = location.href
        $rootScope.__loading__ = true
        $http.post(path, val)
            .success (val, status, headers, config) ->
                _hm = document.getElementById("_hm")
                _hm.src = data._hm if data._hm? and _hm
                $rootScope.__loading__ = false
                $rootScope.data._token = val._token if val._token?
                if (-1 != val.code) then deferred.resolve(val) else deferred.reject(val)
            .error (val, status, headers, config) ->
                _hm = document.getElementById("_hm")
                _hm.src = data._hm if data._hm? and _hm
                $rootScope.__loading__ = false
                $rootScope.data._token = val._token if val._token?
                deferred.reject('网络异常！') #TODO
        return deferred.promise
    get: (path) ->
        $rootScope.__loading__ = true
        deferred = $q.defer()
        $http.get(path)
            .success (val, status, headers, config) ->
                _hm = document.getElementById("_hm")
                _hm.src = data._hm if data._hm? and _hm
                $rootScope.__loading__ = false
                $rootScope.data._token = val._token if val._token?
                location.href = val.url if val.url # 立即静默跳转
                if (-1 != val.code) then deferred.resolve(val) else deferred.reject(val)
            .error (val, status, headers, config) ->
                _hm = document.getElementById("_hm")
                _hm.src = data._hm if data._hm? and _hm
                $rootScope.__loading__ = false
                $rootScope.data._token = val._token if val._token?
                deferred.reject('网络异常！') #TODO
        return deferred.promise
])
.factory('commonService', ['$rootScope','http','$location','$cacheFactory',($rootScope,http,$location,$cacheFactory) -> 
    init: (name,success,error,is_cache)->
        $rootScope._path = $location.path()
        $rootScope.data = data
        try
            cache = $cacheFactory('__controller__'+name)
        catch e
            cache = $cacheFactory.get('__controller__'+name)
        if !$rootScope.__init__? and $location.url() in ['','/'] # 当首次载入时，无需再次获取data
            $rootScope.__init__ = true
            $rootScope[name] = {}
            cache.put('data',data)
            success() if success
        else
            cb = (_data)->
                $rootScope[name] = {}
                $rootScope.__init__ = true
                window.data = _data
                $rootScope.data = _data
                cache.put('data',data)
                success() if success
            return cb(cache.get('data')) if cache.get('data') and is_cache
            http.get($location.url()).then (_data)->
                cb(_data)
            ,(e)->
                cache.put('data',data)
                error(e) if error
])
.factory('commonScroll', [ -> 
    isCenter: (cb)->
        #获取滚动条当前的位置 
        getScrollTop = ->
            scrollTop = 0
            if (document.documentElement && document.documentElement.scrollTop)
                scrollTop = document.documentElement.scrollTop
            else if (document.body) 
                scrollTop = document.body.scrollTop
            return scrollTop

        #获取当前可是范围的高度 
        getClientHeight = ->
            clientHeight = 0
            if (document.body.clientHeight && document.documentElement.clientHeight)
                clientHeight = Math.min(document.body.clientHeight, document.documentElement.clientHeight)
            else 
                clientHeight = Math.max(document.body.clientHeight, document.documentElement.clientHeight)
            return clientHeight
        #获取文档完整的高度 
        getScrollHeight = ->
            return Math.max(document.body.scrollHeight, document.documentElement.scrollHeight)

        window.onscroll = -> cb() if (getScrollTop() + getClientHeight() > getScrollHeight()/3)
            # alert("到达底部") if (this.getScrollTop() + this.getClientHeight() == this.getScrollHeight())
])
.run(['$rootScope','$location','$cacheFactory',($rootScope,$location,$cacheFactory) ->
    # 扩充Array String属性 !!不能扩充对象Object属性，容易冲突 !!
    angular.extend Array.prototype, 
        last: (index)-> # 来自underscore
            return this[this.length - 1] if !index?
            return slice.call(this, Math.max(this.length - index, 0))
        set: (array)->                                                             
            this.splice(i,1) for i in [this.length..0] by -1
            this[i] = e for e,i in array
            return this
        get: (k,v)->
            return i for i in this when angular.equals(i[k], v)
            return false
        copy: ()->
            this.concat()
        remove: (items)->
            items = [items] if !angular.isArray items
            for item in items
                this.splice(i,1) for i in [this.length..0] when angular.equals(this[i], item)
            return this
        uniq: ()->
            res = []
            for value in this
                has = false
                (has=true;break) for i in res when (angular.equals i, value)
                res.push value if !has
            return this.set(res)
        contains: (e)-> 
            ((return true) if (angular.equals i, e)) for i in this
            return false
        indexOf: (val) ->
            (return i if angular.equals v, val) for v,i in this
            return -1

      
    angular.extend String.prototype, 
        capitalize : ()->                                                 # 首字母大写
            this.charAt(0).toUpperCase() + this.substring(1).toLowerCase()
        endWith : (str)->                                                 # 判断是否以xxx结尾
            this.substring(this.length - str.length) == str
        startWith : (str)->                                               # 判断是否以xxx开头
            this.substr(0,str.length) == str
        suffix : ()->                                                     # 获取后缀
            this.split('.').last().toString()
        file_name : ()->                                                  # 获取文件名
            this.split('/').last().toString()
        int: ()->                                                         # 强制转换int
            if !parseInt(this) then return 0 else return parseInt(this)
        replace_all: (source,dest)->                                      # 全部替换
            temps = this.split(source)
            temp = temps[0]
            temp += dest + t for t in temps[1..-1]
            return temp
        size: ()->
            size = this.int()
            if size < 1024
                return size.toString()
            else if size < 1024*1024
                return (size/1024).toString()+'K'
            else if size < 1024*1024*1024
                return (size/1024/1024).toString()+'M'
            else if size < 1024*1024*1024*1024
                return (size/1024/1024/1024).toString()+'G'
            else if size < 1024*1024*1024*1024*1024
                return (size/1024/1024/1024/1024).toString()+'T'

    angular.extend Date.prototype, 
        format: (format) ->
            o = 
                'M+': @getMonth() + 1
                'd+': @getDate()
                'h+': @getHours()
                'm+': @getMinutes()
                's+': @getSeconds()
                'q+': Math.floor((@getMonth() + 3) / 3)
                'S': @getMilliseconds()
            if /(y+)/.test(format)
                format = format.replace(RegExp.$1, (@getFullYear() + '').substr(4 - RegExp.$1.length))
            for k of o
                if new RegExp('(' + k + ')').test(format)
                    format = format.replace(RegExp.$1, if RegExp.$1.length == 1 then o[k] else ('00' + o[k]).substr(('' + o[k]).length))
            format

    # $rootScope.CACHE = $cacheFactory('daguike')
    $rootScope.Uuid = -> #angular-uuid4 from github
        now = Date.now()
        return 'xxxxxxxx_xxxx_4xxx_yxxx_xxxxxxxxxxxx'.replace /[xy]/g, (c) ->
            r = (now + Math.random()*16)%16 | 0
            now = Math.floor(now/16)
            r = (r&0x7|0x8) if (c=='x').toString(16)
            return r.toString(16)
    # $rootScope.Param = (_data)->
    #     # 防止$.param深度序列化字符串
    #     if _.isString _data then _data else $.param(_data)
    $rootScope.Goto = (url)-> $location.path(url)
    # todo Back有时无效
    $rootScope.Back = ()-> if window.history.length>1 then window.history.back() else $location.path('/')

    $rootScope.U = window.U =
        img: (url,type)-> 
            type = (if type then (type+'/') else '')
            if location.host == 'm.daguike.com' 
                cdn = 'http://cdn.daguike.com'
            else 
                cdn = '' 
            return  cdn + '/img/'+(if url then type+url else 'sm/noimg.jpg')
        goods: (id)-> '/wx/goods/'+id
    _hm = document.getElementById("_hm")
    _hm.src = data._hm if data._hm? and _hm
])