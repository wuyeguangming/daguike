# 用于初始化定义
angular.module('common')
.factory('commonUploader',['$upload','cfpLoadingBar','$rootScope',($upload,cfpLoadingBar,$rootScope)->
    upload:(config, success, error, progress)->
        config.data._token = $rootScope.data._token
        #将data以键值对形式提交
        config.formDataAppender = (fd, key, val)-> 
            if _.isArray val
                fd.append(key, $rootScope.Param(v)) for v in val
            else
                fd.append(key, $rootScope.Param(val))
        $upload.upload(config)
        .success (data, status, headers, config) ->
            $rootScope.data._token = data._token if data._token?
            success(data, status, headers, config) if success and (-1 != data.code)
            error(data, status, headers, config) if error and (-1 == data.code)
            cfpLoadingBar.complete()
        .error (data, status, headers, config) ->
            $rootScope.data._token = data._token if data._token?
            error(data) if error
            cfpLoadingBar.complete()
        .progress (evt) ->
            progress(evt) if progress
            cfpLoadingBar.set(parseInt(100.0 * evt.loaded / evt.total)) if evt.total>0
        cfpLoadingBar.start()
])