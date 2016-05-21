angular.module('common')
.factory('commonAlbum', ->
    f2b: (data)->
        ret = 
            changes:[]
            news:[]
        sort = (node)->
            for n,i in node
                if node[i].sort != i
                    node[i].sort = i
                    node[i].is_changed = true
                if node[i].is_new
                    ret.news.push node[i]
                else if node[i].is_changed
                    ret.changes.push node[i]
                node[i].items = sort(n.items) if n.items.length
            return node
        sort(data)
        return {} if !(ret.changes.length or ret.news.length) # 若无变化，返回空
        return ret
        
    b2f: (data)->
        getAlbumTree = (nodes,parent_id,deep)->
            parent_id = parent_id or '0'
            deep = deep or 1
            ret = []
            if nodes.length
                for album in nodes
                    if album['parent_id'].toString() == parent_id.toString()
                        album['title'] = album['name']
                        album['items'] = getAlbumTree(nodes,album['id'],deep+1)
                        album['items'].sort (a,b)->(a['sort'] - b['sort']) # 对子层排序
                        ret.push(album)
            return ret
        albums = getAlbumTree(data)
        albums.sort (a,b)->(a['sort'] - b['sort']) # 对顶层排序
        return albums
)