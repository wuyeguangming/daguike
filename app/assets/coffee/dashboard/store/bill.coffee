angular.module('dashboard')
.filter 'orderPay',-> 
    return (order)-> (if order.pay_type then '-' else '￥')+order.amount+' 优惠:'+order.discount+(if order.pay_type then ' 到付' else '')
.filter 'orderAddress',-> 
    return (order)-> order.location.loc_community.name+order.location.loc_building.name+order.address.loc_detail
.filter 'orderTime',-> 
    return (order)-> 
        ret = order.delivery_time[5..15]
        now = new Date
        dt = new Date(order.delivery_time)
        if dt - now > 30*60*1000
            ret = '[预约]' + ret
        if dt.getDate() > now.getDate()
            ret = '[明天]' + ret
        return ret
.filter 'orderStatus',-> 
    return (order)-> ['已取消','等待支付','下单成功','发货中','已发货','交易成功','申请退款中','退款成功','退款失败','无人收货'][order.status]
.controller('bill', ['$scope', '$rootScope','http','commonService','commonAlbum','commonUploader','commonModal','$q','$routeParams','$timeout','$location',($scope, $rootScope, http, commonService,commonAlbum,commonUploader,commonModal,$q,$routeParams,$timeout,$location)->
    # $scope.status = $location.search().status or ''
    # $scope.update = (status)->
    #     page = if $routeParams.page? then $routeParams.page else 0
    #     page = 0 if status != $scope.status
    #     $scope.search(page,status)
    $scope.page = (i)->
        ii = data.page.current_page + i
        if ii < 0
            ii = 0
        else if ii > data.page.last_page
            ii = data.page.last_page
        $scope.search(ii)
    $scope.search = (p, status)->
        # $scope.status = status or $scope.status
        p = p or 0
        post = 
            p: p
            status: 'bill' #$scope.status
            sn: $scope.search_sn
            username: $scope.search_username
            phone: $scope.search_phone
            goodsname: $scope.search_goodsname
        http.post('/dashboard/store/order-search',post).then (res)->
            data.page = res.page
        ,(e)->
            console.log e
    $scope.note = (order)->
        post = 
            id: order.id
            seller_note: order.seller_note
        http.post('/dashboard/store/order',post).then (res)->
            console.log res
        ,(e)->commonModal.alert(e)
    commonService.init 'bill',->
])