<?php

class Location extends BaseModel {
    protected $visible = array('level','name','parent','sid');
    const LEVEL_ROOM = 6;
    const LEVEL_BUILDING = 5;
    const LEVEL_COMMUNITY = 4;

    static public function groupByLevel($locations){
        $res = array();
        if (empty($locations)) {
            return $res;
        }
        $res['level'] = $locations[0]->level;
        $res['data'] = '';
        foreach ($locations as $key => $loc) {
            $res['data'] = $res['data'].$loc->name.',';
        }
        $res['data'] = substr($res['data'],0,-1);
        return $res;
    }

    static public function iall($to_level){
        $ret = array();
        $locations = Location::where('level','<',$to_level)->get();
        foreach ($locations as $key => $loc) {
            $buf = array();
            $buf['sid'] = (string)$loc['sid'];
            $buf['name'] = $loc['name'];
            $buf['parent'] = (string)$loc['parent'];
            $buf['level'] = (string)$loc['level'];
            array_push($ret, $buf);
        }
        return $ret;
    }


    static public function getLocAllByNodeSid($sid){
        $node = Location::where('sid','=',$sid)->first();
        return self::getLocAllByNode($node);
    }

    static public function getLocAllByNode($loc){
        $res = array();
        if (self::LEVEL_ROOM == $loc['level']) {
            $res['loc_room'] = Location::where('sid','=',$loc['sid'])->first();
            $res['loc_building']  = Location::where('sid','=',$res['loc_room']['parent'])->first();
            $res['loc_community']  = Location::where('sid','=',$res['loc_building']['parent'])->first();
        }
        if (self::LEVEL_BUILDING == $loc['level']) {
            $res['loc_building']  = Location::where('sid','=',$loc['sid'])->first();
            $res['loc_community']  = Location::where('sid','=',$res['loc_building']['parent'])->first();
        }
        if (self::LEVEL_COMMUNITY == $loc['level']) {
            $res['loc_community'] = Location::where('sid','=',$loc['sid'])->first();
        }
        $res['loc_district']  = Location::where('sid','=',$res['loc_community']['parent'])->first();
        $res['loc_city']      = Location::where('sid','=',$res['loc_district']['parent'])->first();
        $res['loc_province']  = Location::where('sid','=',$res['loc_city']['parent'])->first();
        if (self::LEVEL_ROOM == $loc['level']) {
            return array(
                'loc_room' => $res['loc_room'],
                'loc_building' => $res['loc_building'],
                'loc_community' => $res['loc_community'],
                'loc_district'  => $res['loc_district'],
                'loc_city'      => $res['loc_city'],
                'loc_province'  => $res['loc_province']
            );
        }
        if (self::LEVEL_BUILDING == $loc['level']) {
            return array(
                'loc_building' => $res['loc_building'],
                'loc_community' => $res['loc_community'],
                'loc_district'  => $res['loc_district'],
                'loc_city'      => $res['loc_city'],
                'loc_province'  => $res['loc_province']
            );
        }
        if (self::LEVEL_COMMUNITY == $loc['level']) {
            return array(
                'loc_community' => $res['loc_community'],
                'loc_district'  => $res['loc_district'],
                'loc_city'      => $res['loc_city'],
                'loc_province'  => $res['loc_province']
            );
        }
        return array();
    }

    //  todo: 用类似于匹配表达式，如杭州地区不包含下沙：2:杭州^下沙;
    static public function sale_map2str($sale_map){
        $str = '';
        foreach ($sale_map as $key => $value) {
            $loc = Location::where('name','=',$value)->first();
            if (!empty($loc)) {
                $str .= $loc->level.':'.$loc->sid.';';
            }
        }
        return $str;
    }
}
