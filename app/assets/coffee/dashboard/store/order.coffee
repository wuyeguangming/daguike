angular.module('dashboard')
# .constant('STATUS', ['已取消','等待支付','下单成功','正在配货','已发货','正在配货','已发货','交易成功','疑问订单'])
.filter 'orderPay',-> 
    return (order)-> (if order.pay_type then '-' else '￥')+order.amount+' 优惠:'+order.discount+(if order.pay_type then ' 到付' else '')
.filter 'orderAddress',-> 
    # return (order)-> order.location.loc_community.name+order.location.loc_building.name+order.location.loc_room.name
    return (order)-> order.location.loc_community.name+order.location.loc_building.name+order.address.loc_detail
.filter 'orderTime',-> 
    return (order)-> 
        ret = order.delivery_time[5..15]
        now = new Date
        dt = new Date(order.delivery_time)
        if dt - now > 30*60*1000
            ret = '[预约]' + ret
        if (dt > now) and (dt.getDate() > now.getDate())
            ret = '[明天]' + ret
        return ret #"2015-03-25 00:11:51" order.delivery_time[8..9]+'/'+
.filter 'orderStatus',-> 
    # return (order)-> ['已支付','货到付款'][order.pay_type]
    return (order)-> ['已取消','等待支付','下单成功','发货中','已发货','交易成功','申请退款中','退款成功','退款失败','无人收货'][order.status]
.controller('order', ['$scope', '$rootScope','http','commonService','commonAlbum','commonUploader','commonModal','$q','$routeParams','$timeout','$location',($scope, $rootScope, http, commonService,commonAlbum,commonUploader,commonModal,$q,$routeParams,$timeout,$location)->
    $scope.status = $location.search().status or ''
    $scope.print = (order)->
        $('.qrcode img').remove()
        for node in $('.qrcode')
            $(node).qrcode 
                text: $(node).attr('data-id') #todo
                render: 'image'
                size: 80
        # $('#'+_order.sn).remove() for _order in data.page.data when 2!=_order.status
        
        deferred = $q.defer()
        $( "#"+(if order? then order.sn else "print")).print(
            # globalStyles: false,
            # mediaPrint: false,
            # stylesheet: null,
            # noPrintSelector: ".no-print",
            iframe: true,
            # append: null,
            # prepend: null,
            # manuallyCopyFormValues: true,
            deferred: deferred
        )
        return
    $scope.post = (order,status)->
        order.status = status #已经发货
        http.post('/dashboard/store/order-page',
            orders: if order? then [order] else $rootScope.data.page.data
        ).then (res)->
            console.log res
            # $scope.update('')
            # $scope.search()
        ,(e)->
            console.log e
    $scope.update = (status)->
        page = if $routeParams.page? then $routeParams.page else 0
        page = 0 if status != $scope.status
        # $scope.status = status
        # http.get('/dashboard/store/order-page/'+page+'?status='+$scope.status).then (d)->
        #     data.page = d.page
        $scope.search(page,status)
    $scope.cancel = (order)->
        commonModal.alert '确定要取消该订单吗？' ,->
            order.status = 0
            http.post('/dashboard/store/order-page',
                orders: if order? then [order] else $rootScope.data.page.data
            ).then (res)->
                console.log res
                # $scope.update('')
                # $scope.search()
            ,(e)->
                console.log e
    $scope.complete = (order)->
        commonModal.alert '确定该订单已完成了吗？' ,->
            order.status = 5
            http.post('/dashboard/store/order-page',
                orders: if order? then [order] else $rootScope.data.page.data
            ).then (res)->
                console.log res
                # $scope.update('')
                # $scope.search()
            ,(e)->
                console.log e
    $scope.page = (i)->
        ii = data.page.current_page + i
        if ii < 0
            ii = 0
        else if ii > data.page.last_page
            ii = data.page.last_page
        # $rootScope.Update('/dashboard/store/order-page/'+ii).search('status', $scope.status)
        $scope.search(ii)
    $scope.search = (p, status)->
        $scope.status = status or $scope.status
        p = p or 0
        post = 
            p: p
            status: $scope.status
            sn: $scope.search_sn
            username: $scope.search_username
            phone: $scope.search_phone
            goodsname: $scope.search_goodsname
        http.post('/dashboard/store/order-search',post).then (res)->
            data.page = res.page
        ,(e)->
            console.log e
    commonService.init 'order',->
        # $timeout ->
        #     $scope.update('')
        # ,1000
])