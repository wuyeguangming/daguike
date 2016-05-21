angular.module('dashboard')
.filter('commonAddress',->
    (item)->
        if item? and item.address?
            # item.address.name+' '+item.address.phone+' '+item.location.loc_community.name+item.location.loc_building.name+item.location.loc_room.name#+' '+(new Date(item.address.time)).format('DD hh-mm')
            item.address.name+' '+item.address.phone+' '+item.location.loc_community.name+item.location.loc_building.name+item.address.loc_detail
      
)