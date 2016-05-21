<?php

function randString($length){
    $str = null;
    $str_map = "0123456789abcdefghijklmnopqrstuvwxyz";
    for($i=0;$i<$length;$i++){
        $str.=$str_map[rand(0,strlen($str_map)-1)];
    }
    return $str;
}
function isWeixin(){
    return (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false && strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false);
}

function isWeixinDebug(){
    return !isWeixin() && ('localhost' == $_SERVER['HTTP_HOST']);
}
function object2array($value){
    return json_decode( json_encode( $value),true);
}

function str_replace_once($needle, $replace, $haystack) {
    $pos = strpos($haystack, $needle);
    if ($pos === false) {
        return $haystack;
    }
    return substr_replace($haystack, $replace, $pos, strlen($needle));
}

function I($value=null, $default=''){
    if (empty($value)) {
        return Input::all();
    }
    if (!empty($default)) {
        return Input::get($value,$default);
    }
    return Input::get($value);
}

function suffix($name){
    return pathinfo($name, PATHINFO_EXTENSION);
}

function get_suffix($name){
    return strtolower(trim(substr(strrchr($name, '.'), 1)));
}

function get_hash_file($path){
    try {
        $base = public_path().'/'.dirname($path);
        $name = substr($path,strrpos($path,'/') + 1);
        $name = substr($name,0,strrpos($name,'.'));
        $files = scandir($base);
        foreach ($files as $key => $file) {
            if (0===strpos($file, $name)) {
                return dirname($path).'/'.$file;
            }
        }
        return ''; # todo
    } catch (Exception $e) {
        return ''; # todo
    }
}

// 返回单独asset
function asset_url($name){
    $is_dev      = Config::get('app.debug');
    $switch_path = ['dist','dev'];
    $switch_path = $switch_path[$is_dev].'/';
    $path = $switch_path.$name;
    if (!$is_dev) {
        $path = get_hash_file($path);
    }
    return asset($path);
}

// 重写asset
function iasset($locale, $production){
    $is_dev      = Config::get('app.debug');
    $switch_path = ['dist','dev'];
    $switch_path = $switch_path[$is_dev].'/';
    $assets      = $is_dev? $locale : $production;
    $str         = '';
    foreach ($assets as $asset) {
        $name = $asset;
        $path = $switch_path.$name;
        $path = get_hash_file($path);
        $path = asset($path);
        if (!$is_dev && 'localhost'!=$_SERVER['HTTP_HOST']) {
            $path = str_replace($_SERVER['HTTP_HOST'], Config::get('app.cdn.domain'),$path);
        }
        $disable_cache = '';
        if (count($asset)>1) {
            $disable_cache = '?'.time();//$disable_cache = '?'.hash_file('md5',$path);
        }
        switch (get_suffix($name)) {
            case 'css':
                $str = $str.'<link rel="stylesheet" href="'.$path.'">';
                break;
            case 'js':
                $str = $str.'<script src="'.$path.'"></script>';
                break;
        }
    }
    return $str;
}

function aa($value='') {
    print_r($value);
    die();
}

function ss($value='') {
    print_r($value);
}
