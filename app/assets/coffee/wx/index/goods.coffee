angular.module('wx')
.controller('goods', ['$scope', '$rootScope' ,'commonService','$filter','http','$location','$timeout',($scope, $rootScope, commonService, $filter,http,$location,$timeout) ->
    $rootScope.wx_cart.countdown = ''
    $scope.time_valid = false
    $scope.dt = 0
    $rootScope.wx_navbar =
        id: 2
        ready: false
        btn: [
            text: '返回'
            click: -> $location.path('/')
            hide: -> !data.goods or !$rootScope.wx_cart.item.total_num
        ,
            text: '被抢光啦！下次早点来哦~'
            hide: -> !data.goods or $rootScope.wx_cart.item.total_num
        ,
            text: '加入购物车'
            class: 'btn-danger'
            click: -> 
                return alert('请选择商品规格') if !$rootScope.wx_cart.item.sku_selected
                $rootScope.wx_cart.add(true)
            hide: -> !data.goods or !$rootScope.wx_cart.item.total_num or !$scope.time_valid
            # disabled: -> !$rootScope.wx_cart.item.sku_selected
        ]
    commonService.init 'goods',->
        $rootScope.wx_navbar.ready = true
        $rootScope.wx_cart.init($rootScope.data.goods)
        countdown = ->
            if 'goods' == $rootScope._controller
                now = (new Date).getTime()/1000
                if now > data.goods.end_time # 已结束
                    $scope.time_valid = false
                    $rootScope.wx_navbar.btn[0].text = '已下架！点击返回'
                else if data.goods.start_time > now # 未开始
                    $rootScope.wx_cart.countdown = '距离开抢还有 '+$filter('commonCountDown')(data.goods.start_time*1000)
                    $scope.time_valid = false
                else # 未结束
                    $rootScope.wx_cart.countdown = if (0 < (data.goods.end_time - now) < 2*24*3600) then '还剩 '+$filter('commonCountDown')(data.goods.end_time*1000) else ''
                    $scope.time_valid = true
                $timeout (->countdown()),100
        countdown()

])