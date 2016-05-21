angular.module('common').directive('commonLocation', ['http','commonCache', (http,commonCache)->
  restrict: 'EA'
  transclude: true
  templateUrl: 'common/directive/location.html'
  replace: true
  scope:
    loc: '=' #注意：todo: loc.loc_模式要改！！！！！
    mode: '@'
    size: '@'
  link: (scope, element, attrs) ->
    scope.loc = scope.loc || {}
    if !_.isEmpty scope.loc # int转string
      scope.loc.loc_city = scope.loc.loc_city.toString()
      scope.loc.loc_district = scope.loc.loc_district.toString()
      scope.loc.loc_community = scope.loc.loc_community.toString()
    
    scope.$watch 'loc',->
      # return if !(scope.province and scope.city and scope.district and scope.community)
      return if !scope.loc
      scope.mode = 'default' if !scope.mode?
      scope.provinces      = []
      scope.cities         = []
      scope.districts      = []
      scope.communities    = []
      
      provinces_all        = []
      cities_all           = []
      districts_all        = []
      communities_all      = []

      scope.sel = (level, loc)-> #todo: _.where..==[]??
        switch level
          when 1
            scope.loc.loc_province = loc.sid
            scope.province_name = loc.name
            scope.cities   = _.where cities_all, {'parent': loc.sid}
            scope.sel(2, scope.cities[0])
          when 2
            scope.loc.loc_city  = loc.sid
            scope.city_name  = loc.name
            scope.districts = _.where districts_all, {'parent': loc.sid}
            scope.sel(3, scope.districts[0])
          when 3
            scope.loc.loc_district  = loc.sid
            scope.district_name  = loc.name
            scope.communities = _.where communities_all, {'parent': loc.sid}
            scope.sel(4, scope.communities[0])
          when 4
            scope.loc.loc_community = loc.sid
            scope.community_name = loc.name

      init = (res)->
        for loc in res
          provinces_all.push(loc) if loc.level   == '1'
          cities_all.push(loc) if loc.level      == '2'
          districts_all.push(loc) if loc.level   == '3'
          communities_all.push(loc) if loc.level == '4'

        province  = (_.where provinces_all,   {'sid': (scope.loc.loc_province)})[0]||provinces_all[0]
        city      = (_.where cities_all,      {'sid': (scope.loc.loc_city||province.sid)})[0] ||
                    (_.where cities_all,      {'parent': province.sid})[0] 
        district  = (_.where districts_all,   {'sid': (scope.loc.loc_district||city.sid)})[0] ||
                    (_.where districts_all,      {'parent': city.sid})[0] 
        community = (_.where communities_all, {'sid': (scope.loc.loc_community||district.sid)})[0] ||
                    (_.where communities_all,      {'parent': district.sid})[0] 
        scope.provinces = provinces_all
        scope.sel(1,province)
        scope.sel(2,city)
        scope.sel(3,district)
        scope.sel(4,community)
      loc_all = commonCache.get('common/directive/location/all')
      if loc_all
        init(loc_all) 
      else 
        http.get('/common/location/all').then (res)-> 
          commonCache.put('common/directive/location/all',res.data)
          init(res.data)
        ,(e)->
          console.log e # todo
])

