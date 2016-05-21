angular.module('wx')
.filter('cartLimit',->
    (item)-> if item.goods.buy_limit>0 then ('限购'+item.goods.buy_limit+'件') else ''
)
.filter('cartError',->
    (item)->
        return if !item?
        return if item.goods? then '该商品已下架或被删除'
        else if (item.sku?) then '该商品已更新，请删除后重新下单'
        else if item.num? then ('库存不足:'+item.num)
)
.filter('cartHongbao',->
    (hongbao)-> 
        if hongbao.id?
            return ('￥'+hongbao.amount+'(满'+hongbao.condition+'元使用)') 
        else 
            return '您有'+data.hongbao_num+'个红包优惠，可点击使用'
)
.controller('cart', ['$scope', '$rootScope' ,'commonService','http','$location','$filter',($scope, $rootScope, commonService, http, $location,$filter) ->
    $rootScope.wx_navbar = 
        id : 2
        ready: true
        btn : [
            text: '继续购物'
            click:-> $location.path('/')
        ,
            text: '提交订单'
            class : 'btn-success'
            disabled: -> !$scope.ready or !$rootScope.wx_cart.cache.items.length
            click : ->
                return alert('请选择收货时间段') if !$rootScope.wx_cart.delivery_time 
                return alert('请添加收货人信息') if !data.wx_addresses.length
                $rootScope.wx_cart.cart_submit($rootScope.wx_navbar.btn[1])
        ]
    $scope.ready = false
    $scope.has_book = $filter('commonCartHasBook')()
    commonService.init 'cart',->
        # $rootScope.wx_cart.delivery_time = $filter('commonDeliveryTimeTrans')() #now.getTime()
        $scope.ready = true
        $scope.address_eidt = ->
            $rootScope.cache.address = data.wx_addresses[0].address
            $rootScope.cache.address['selects'] = []
            $rootScope.cache.address['selects'][v.level] = v for k, v of data.wx_addresses[0].location
            $rootScope.Goto('/wx/address/'+$rootScope.cache.address.id)
        $scope.delivery_time_eidt = -> $rootScope.Goto('/wx/cart/delivery_time')
        # $rootScope.wx_cart.delivery_time = $filter('commonDeliveryTimeTrans')()
])
.controller('cartDeliveryTime', ['$scope', '$rootScope' ,'commonService','http','$location','$routeParams','$filter',($scope, $rootScope, commonService, http,$location,$routeParams,$filter) ->
    $rootScope.wx_navbar =
        id: 2
        ready: true
    $scope.option =
        fn : 'time'
        title: '请选择收货时间段'
        select: ''
        selects: []
        list : []
        filter: (time)-> $filter('commonDeliveryTime')(time)
        click: ()->
            $rootScope.wx_cart.delivery_time = $scope.option.select
            $rootScope.Back()
    TIME = [$rootScope.wx_cart.MIN_HOUR..$rootScope.wx_cart.MAX_HOUR-1]
    if !$filter('commonCartHasBook')()
        for t in TIME
            tt = $filter('commonGetTime')((new Date),t,0,0).getTime()
            $scope.option.list.push tt if tt > (new Date).getTime()
    time = new Date
    time.setDate(time.getDate()+1)
    $scope.option.list.push($filter('commonGetTime')(time,t,0,0).getTime()) for t in TIME

])