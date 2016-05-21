angular.module('wx')
.filter('wx_cartHongbaoValid',['$rootScope',($rootScope)-> 
    return (hongbao)-> 
        now = new Date
        return hongbao.id? and (new Date(hongbao.time_start.split(' ')[0]) <= now <= new Date(hongbao.time_end.split(' ')[0])) and !hongbao.used and ($rootScope.wx_cart.cache.goods_amount >= hongbao.condition) # 注意，必须在正确计算$rootScope.wx_cart.cache.amount的前提下进行
]).run(['$rootScope','$filter','$location','http','$timeout',($rootScope,$filter,$location,http,$timeout)->
    CART_INIT = 
        items: []
        errors: []
        amount: 0
        goods_amount: 0
    $rootScope.wx_cart = {}
    $rootScope.wx_cart.MIN_HOUR = 10
    $rootScope.wx_cart.MAX_HOUR = 23
    $rootScope.wx_cart.GOODS_BOOK = '次日送'
    $rootScope.wx_cart.errors = []
    $rootScope.wx_cart.ready = false
    $rootScope.wx_cart.pay_type = 1 #0微信支付 1到付
    $rootScope.wx_cart.countdown = ''
    $rootScope.wx_cart.cache = CART_INIT
    $rootScope.wx_cart.item = {}
    $rootScope.wx_cart.hongbao = {}
    $rootScope.wx_cart.discount = 0
    $rootScope.wx_cart.add = (goto_cart)->
        # todo: 限购产品判断
        $rootScope.wx_cart.hongbao = {}
        $rootScope.wx_cart.errors = []
        return $rootScope.Back() if !$rootScope.wx_cart.item.total_num
        return if !$rootScope.wx_cart.item.sku_selected.num
        is_new = true
        for item,index in $rootScope.wx_cart.cache.items
            if (item.goods.id == $rootScope.wx_cart.item.goods.id) and ($rootScope.wx_cart.item.sku_selected.id == item.sku_selected.id)
                $rootScope.wx_cart.cache.items[index].num += 1 #只更新，不添加
                is_new = false
                break
        $rootScope.wx_cart.cache.items.push angular.copy($rootScope.wx_cart.item) if is_new
        $rootScope.wx_cart.update()
        $location.path('wx/cart') if goto_cart
    $rootScope.wx_cart.update = ->
        $rootScope.wx_cart.cache.goods_amount = 0
        $rootScope.wx_cart.cache.goods_amount += items.num*items.sku_selected.price for items in $rootScope.wx_cart.cache.items
        if $filter('wx_cartHongbaoValid')($rootScope.wx_cart.hongbao)
            amount = $rootScope.wx_cart.cache.goods_amount - $rootScope.wx_cart.hongbao.amount
            $rootScope.wx_cart.discount = (if(amount>0) then $rootScope.wx_cart.hongbao.amount else $rootScope.wx_cart.cache.goods_amount)
            $rootScope.wx_cart.cache.amount = $rootScope.wx_cart.cache.goods_amount - $rootScope.wx_cart.discount
        else
            $rootScope.wx_cart.hongbao = {}
            $rootScope.wx_cart.discount = 0
            $rootScope.wx_cart.cache.amount = $rootScope.wx_cart.cache.goods_amount
        localStorage.wx_cart_cache = JSON.stringify($rootScope.wx_cart.cache)
        $rootScope.wx_cart.delivery_time = $filter('commonDeliveryTimeTrans')() if $filter('commonCartHasBook')()
    $rootScope.wx_cart.sku_default = (goods)-> if goods? and goods.skus and (goods.skus.length==1 and goods.skus[0].value=='') then goods.skus[0] else ''
    $rootScope.wx_cart.reset = ->
        $rootScope.wx_cart.cache = CART_INIT
        $rootScope.wx_cart.update()
    $rootScope.wx_cart.init = (goods)->
        $rootScope.wx_cart.cache = if localStorage.wx_cart_cache? then JSON.parse(localStorage.wx_cart_cache) else CART_INIT
        $rootScope.wx_cart.discount = 0 #discount需清空
        $rootScope.$watchCollection 'wx_cart.cache.items',-> $rootScope.wx_cart.update()
        $rootScope.wx_cart.item = 
            total_num: 0
            goods: if goods then goods else {}
            price_show: if goods then goods.price else ''
            num: 1
            sku_selected: $rootScope.wx_cart.sku_default(goods)
        $rootScope.wx_cart.item.total_num+=1 for i in goods.skus when i.num if goods?
        $rootScope.wx_cart.update()
        $rootScope.wx_cart.delivery_time = $filter('commonDeliveryTimeTrans')()
        $rootScope.$watch 'wx_cart.item.sku_selected',->
            return if !$rootScope.wx_cart.item.goods.id?
            if $rootScope.wx_cart.item.goods.skus.length
                max = min = $rootScope.wx_cart.item.goods.skus[0].price
                for sku in $rootScope.wx_cart.item.goods.skus
                    min = sku.price if sku.price < min
                    max = sku.price if sku.price > max
                $rootScope.wx_cart.item.price_show = (if(min == max) then $filter('currency')(min,"￥") else ($filter('currency')(min,"￥") + '~' + $filter('currency')(max,"￥")))
            if $rootScope.wx_cart.item.sku_selected
                $rootScope.wx_cart.item.price_show = $filter('currency')($rootScope.wx_cart.item.sku_selected.price,"￥")
    $rootScope.wx_cart.sku_minus = (item,index)->
        if (item.num>1) then (item.num=item.num-1) else $rootScope.wx_cart.cache.items.remove(item)
        error = $rootScope.wx_cart.errors[index]
        (error = '') if error? and error.num? and error.num>= item.num
        $rootScope.wx_cart.update()
    $rootScope.wx_cart.sku_plus = (item)->
        (return alert '该商品限购'+item.goods.buy_limit+'件哦') if (item.goods.buy_limit>0 and item.num >= item.goods.buy_limit)
        item.num = item.num+1
        $rootScope.wx_cart.update()

    $rootScope.wx_cart.cart_reset = -> $rootScope.wx_cart.reset() if confirm('清空购物车？')
    $rootScope.wx_cart.cart_submit = (btn)->
        if $filter('commonCartHasBook')() or ($rootScope.wx_cart.delivery_time - (new Date).getTime() > 30*60*1000)
            return if !confirm('您的配送时间为'+$filter('commonDeliveryTime')($rootScope.wx_cart.delivery_time)+'确定吗？') 
        btn.class = 'btn-success'
        btn.disabled =-> true
        btn.text = '正在提交，请稍后...'
        post = 
            goods: []
            address_id: data.wx_addresses[0].address.id
            delivery_time: $rootScope.wx_cart.delivery_time
            pay_type: if $rootScope.wx_cart.cache.amount > 0 then $rootScope.wx_cart.pay_type else 1 # 为0时，省略在线支付环节
            amount: $rootScope.wx_cart.cache.amount
            goods_amount: $rootScope.wx_cart.cache.goods_amount
            hongbao: $rootScope.wx_cart.hongbao
            discount: $rootScope.wx_cart.discount
        for item in $rootScope.wx_cart.cache.items
            post.goods.push
                id: item.goods.id
                name: item.goods.name
                sku_price: item.sku_selected.price #用来校验 todo 判断是否在下单中有更新
                sku_id: item.sku_selected.id
                sku_value: item.sku_selected.value
                sku_serial: item.sku_selected.serial
                num: item.num
        http.post('/wx/cart',post).then (ret)->
            $rootScope.wx_cart.reset()
            btn.text = '提交订单'
            return  $location.path('/wx/order/detail/'+ret.data.order.id)
        ,(e)-> 
            btn.disabled =-> false
            btn.class = 'btn-danger'
            btn.text = e.info
            $rootScope.wx_cart.errors = e.data
            # $rootScope.wx_cart.pay_submit(btn) if confirm('订单创建失败，是否重新提交？')
    try
        $rootScope.wx_cart.init()
    catch e
        localStorage.clear()
        $rootScope.wx_cart.init()
    

])

