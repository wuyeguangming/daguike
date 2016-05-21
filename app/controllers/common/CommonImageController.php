<?php
/**
* CommonImageController
*/
use Intervention\Image\ImageManagerStatic as Image;

class CommonImageController extends BaseController{
    function __construct() {
        Image::configure([
            'driver' => class_exists('Imagick') ? 'imagick' : 'gd'
        ]);
        $this->lifetime = 60*60*24*7;
        $this->quality = 75;
    }

    private function sendImage($img, $info, $lifetime) {
        $lastModified = gmdate('D, d M Y H:i:s', $info['modified']).' GMT';
        $eTag = $info['checksum'];
        $isModified = false;

        header('Cache-Control: private, max-age='. $lifetime);
        header('Expires: '.gmdate('D, d M Y H:i:s', time()+$lifetime).' GMT');
        header('Content-Length: ' . strlen($img));
        header('Last-Modified: ' . $lastModified);
        header('ETag: ' . $eTag);

        $ifModifiedSince = isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])? stripslashes($_SERVER['HTTP_IF_MODIFIED_SINCE']): false;
        $ifNoneMatch = isset($_SERVER['HTTP_IF_NONE_MATCH'])? stripslashes($_SERVER['HTTP_IF_NONE_MATCH']): false;

        if (!$ifModifiedSince && !$ifNoneMatch) {
            $isModified = true;
        } elseif ($ifNoneMatch && $ifNoneMatch !== $eTag) {
            $isModified = true;
        } else if ($ifModifiedSince && $ifModifiedSince != $lastModified) {
            $isModified = true;
        }

        if ($isModified) {
            return Response::make($img, 200, array(
                'Content-Type' => $info['Content-Type'],
                // 'Cache-Control' => 'max-age='.$this->lifetime.', public',
                // 'Etag' => md5($img)
            ));
        } else {
            return Response::make(null, 304);
        }
    }

    private function make_size($image,$name,$size){
        switch ($size) {
            case 'xs':
                $image->make($name)->fit(25, 25);
                break;
            case 'sm':
                $image->make($name)->fit(60, 60);
                break;
            case 'md':
                $image->make($name)->fit(120, 120);
                break;
            default:
                $image->make($name);
                break;
        }
        return $image;
    }

    public function get($size,$name){
        try {
            $info = [];
            $name = public_path('/img/'.$name);
            $name = file_exists($name)? $name: public_path('/assets/img/noimg.jpg');
            $img = Image::cache(function($image) use ( $name, $size, &$info) {
                if (in_array(suffix($name), array('png','gif'))) { //gd不支持 bmp
                    $mime = 'image/'.suffix($name);
                }else{
                    $mime = 'image/jpeg';
                }
                $image = $this->make_size($image,$name,$size);
                $info = [
                    'Content-Type' => $mime, 
                    'checksum' =>  $image->checksum(),
                    'modified' => array_key_exists('modified', $image->properties) ? $image->properties['modified'] : time(),
                ];
            }, $this->lifetime);
            return $this->sendImage($img, $info, $this->lifetime);
        } catch (Exception $e) {
            return Response::make(null, 404); //todo: default img
        }
    }
}
