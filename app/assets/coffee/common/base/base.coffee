angular.module('common', ['ui.bootstrap','angular-loading-bar', 'ngAnimate'])
.factory('commonCache', ['$cacheFactory', ($cacheFactory)->
    return $cacheFactory('cache')
])
.run(['$rootScope','$location','$cacheFactory',($rootScope,$location,$cacheFactory) ->
    # 扩充Array String属性 !!不能扩充对象Object属性，容易冲突 !!
    _.extend Array.prototype, 
        last: (index)-> 
            _.last(this, index)
        set: (array)->                                                             
            this.splice(i,1) for i in [this.length..0] by -1
            this[i] = e for e,i in array
            return this
        get: (k,v)->
            return i for i in this when _.isEqual(i[k], v)
            return false
        copy: ()->
            this.concat()
        remove: (items)->
            items = [items] if !_.isArray items
            for item in items
                this.splice(i,1) for i in [this.length..0] when _.isEqual(this[i], item)
            return this
        uniq: ()->
            res = []
            for value in this
                has = false
                (has=true;break) for i in res when (_.isEqual i, value)
                res.push value if !has
            return this.set(res)
        contains: (e)-> 
            ((return true) if (_.isEqual i, e)) for i in this
            return false
        indexOf: (val) ->
            (return i if _.isEqual v, val) for v,i in this
            return -1

      
    _.extend String.prototype, 
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
            
    # $rootScope.CACHE = $cacheFactory('daguike')
    $rootScope.Uuid = -> #angular-uuid4 from github
        now = Date.now()
        return 'xxxxxxxx_xxxx_4xxx_yxxx_xxxxxxxxxxxx'.replace /[xy]/g, (c) ->
            r = (now + Math.random()*16)%16 | 0
            now = Math.floor(now/16)
            r = (r&0x7|0x8) if (c=='x').toString(16)
            return r.toString(16)
    $rootScope.Param = (data)->
        # 防止$.param深度序列化字符串
        if _.isString data then data else $.param(data)
    $rootScope.Goto = (url)-> location.href = url

    $rootScope.Update = (url)-> 
        console.log url
        $location.path(url)

    $rootScope.U = window.U =
        img: (url,type)-> 
            type = (if type then (type+'/') else '')
            if location.host == 'm.daguike.com' 
                cdn = 'http://cdn.daguike.com'
            else 
                cdn = '' 
            return  cdn + '/img/'+(if url then type+url else 'sm/noimg.jpg')
    $rootScope.Logout = ->
        location.href = '/user/logout'
])
