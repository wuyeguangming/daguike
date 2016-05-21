<?php

class Album extends Eloquent {
    protected $guarded = array('');
    
    static public function iCreate($album){
        return Album::create(array(
            'name'      => $album['name'],
            'parent_id' => $album['parent_id'],
            'sort'      => $album['sort'],
            'store_id'  => $album['store_id'],
            'state'     => isset($album['state'])?$album['state']:0,
            'is_show'   => isset($album['is_show'])?$album['is_show']:1,
        ));
    }

    public function iUpdate($album){
        return $this->update(array(
            'name'      => $album['name'],
            'parent_id' => $album['parent_id'],
            'sort'      => $album['sort'],
            'store_id'  => $album['store_id'],
            'state'     => isset($album['state'])?$album['state']:0,
            'is_show'   => isset($album['is_show'])?$album['is_show']:1,
        ));
    }
}
