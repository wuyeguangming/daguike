angular.module('common')
.filter('commonCartHasBook',['$filter','$rootScope',($filter,$rootScope)->
    return ()->   
        is_book = false
        is_book = true for item in $rootScope.wx_cart.cache.items when item.goods.name.split($rootScope.wx_cart.GOODS_BOOK).length>1
        return is_book
])
.filter('commonDeliveryTimeTrans',['$filter','$rootScope',($filter,$rootScope)->
    return (dat)->   
        d = dat or new Date
        if d.getHours() >= $rootScope.wx_cart.MAX_HOUR or $filter('commonCartHasBook')()
            d.setDate(d.getDate()+1)
            d.setHours($rootScope.wx_cart.MIN_HOUR)
            d.setMinutes(0)
            d.setSeconds(0)
        else if d.getHours() < $rootScope.wx_cart.MIN_HOUR
            d.setHours($rootScope.wx_cart.MIN_HOUR)
            d.setMinutes(0)
            d.setSeconds(0)
        return d.getTime()

])
.filter('commonDeliveryTime',['$filter','$rootScope',($filter,$rootScope)->
    return (time)->   
        time_plus = (time)->
            t = new Date(time)
            if t.getMinutes(0)<30
                t.setMinutes(30)
            else 
                t.setHours(t.getHours()+1)
                t.setMinutes(0) 
            return t
        set_date = (d,h,m) -> 
            now.setDate(d)
            now.setHours(h)
            now.setMinutes(m)
            return now
        # return '立即配送' if !time? or !time
        if !time? or !time
            now = new Date
            if now.getHours() < $rootScope.wx_cart.MIN_HOUR 
                # return set_date(now.getDate(),$rootScope.wx_cart.MIN_HOUR,0).format("MM-dd hh:mm")
                time = set_date(now.getDate(),$rootScope.wx_cart.MIN_HOUR,0)
            else if now.getHours() >= $rootScope.wx_cart.MAX_HOUR
                # return set_date(now.getDate()+1,$rootScope.wx_cart.MIN_HOUR,0).format("MM-dd hh:mm")
                time = set_date(now.getDate()+1,$rootScope.wx_cart.MIN_HOUR,0)
            else 
                time = now # set_date(now.getDate(),$rootScope.wx_cart.MIN_HOUR,0)
                end  = $filter('date')((new Date).setTime(now.getTime()+1800*1000),'HH:mm')
        start = $filter('date')(new Date(time),'HH:mm')
        end = $filter('date')(time_plus(time),'HH:mm') if !end?
        now = new Date
        d = new Date(time)
        is_today = if d.getDate() == now.getDate() then '' else '明天'
        return is_today+start+'~'+end
])
.filter('commonAddress',->
    (item)->
        if item? and item.address?
            # item.address.name+' '+item.address.phone+' '+item.location.loc_community.name+item.location.loc_building.name+item.location.loc_room.name#+' '+(new Date(item.address.time)).format('DD hh-mm')
            item.address.name+' '+item.address.phone+' '+item.location.loc_community.name+item.location.loc_building.name+item.address.loc_detail
      
)
.filter('commonCountDown',->
    (t)->
        checkTime = (i) ->
            i = '0' + i if i < 10
            return i
        ts =  Math.abs(new Date(t) - new Date)
        dd = parseInt(ts / 1000 / 60 / 60 / 24, 10)
        hh = parseInt(ts / 1000 / 60 / 60 % 24, 10)
        mm = parseInt(ts / 1000 / 60 % 60, 10)
        ss = parseFloat(ts / 1000 % 60).toFixed(1)
        dd = checkTime(dd)
        hh = checkTime(hh)
        mm = checkTime(mm)
        ss = checkTime(ss)
        return dd + '天' + hh + '时' + mm + '分' + ss + '秒'
)
.filter('commonGetTime',->
    (date,h,m,s)->
        date.setHours(h)
        date.setMinutes(m)
        date.setSeconds(s)
        return date
)