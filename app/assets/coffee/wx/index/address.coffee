angular.module('wx')
.controller('addressIndex', ['$scope', '$rootScope' ,'commonService','http','$location','$routeParams',($scope, $rootScope, commonService, http,$location,$routeParams) ->
    if !$rootScope.cache.address?
        # $routeParams.fn == 'create'
        $rootScope.cache.address =  # todo
            id: ''
            name: ''
            phone: ''
            loc_detail: ''
            note: ''
            selects: []
    $rootScope.$watch 'cache.address.phone',()->
        return if !$rootScope.cache.address.phone
        if (11!=$rootScope.cache.address.phone.length) or ($rootScope.cache.address.phone != parseInt($rootScope.cache.address.phone).toString()) then $scope.phone_error = true else $scope.phone_error = false 

    $rootScope.wx_navbar =
        id: 2
        ready: true
        btn: [ 
            text: '返回'
            class: 'btn-default'
        ,
            text: '确定'
            class: 'btn-success'
            click: -> 
                return alert('请选择您所在的地址') if !$rootScope.cache.address.selects.length
                return alert('请填入您的姓名') if !$rootScope.cache.address.name
                return alert('请填入您的详细地址') if !$rootScope.cache.address.loc_detail
                return alert('电话号码不正确') if $scope.phone_error != false
                $rootScope.wx_navbar.btn[1].text = '提交中，请稍后'
                # $scope.option.post()
                http.post('/wx/address',{'address':$rootScope.cache.address}).then (res)->
                    $rootScope.Back() #$location.path '/wx/address'
                ,(e)-> 
                    console.log e #($scope.confirm = true if e.info == 'confirm')
                    # alert(e)
                    # $rootScope.Back()
            # disabled: -> !$rootScope.cache.address.selects.length or !$rootScope.cache.address.name or !$rootScope.cache.address.loc_detail or $scope.phone_error != false #or !$rootScope.wx_cart.delivery_time
        ]
])