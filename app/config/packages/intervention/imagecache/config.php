<?php

return array(

    /*
    |--------------------------------------------------------------------------
    | Name of route
    |--------------------------------------------------------------------------
    |
    | Enter the routes name to enable dynamic imagecache manipulation.
    | This handle will define the first part of the URI:
    | 
    | {route}/{template}/{filename}
    | 
    | Examples: "images", "img/cache"
    |
    */
   
    'route' => 'img',

    /*
    |--------------------------------------------------------------------------
    | Storage paths
    |--------------------------------------------------------------------------
    |
    | The following paths will be searched for the image filename, submited 
    | by URI. 
    | 
    | Define as many directories as you like.
    |
    */
    
    'paths' => array(
        public_path('assets/img'),
        public_path('img')
    ),

    /*
    |--------------------------------------------------------------------------
    | Manipulation templates
    |--------------------------------------------------------------------------
    |
    | Here you may specify your own manipulation callbacks.
    | The keys of this array will define which templates 
    | are available in the URI:
    |
    | {route}/{template}/{filename}
    |
    */
   
    'templates' => array(
        'lg' => function($image) {return $image->fit(600, 600);},
        'md' => function($image) {return $image->fit(120, 120);},
        'sm' => function($image) {return $image->fit(60, 60);},
        'xs' => function($image) {return $image->fit(25, 25);},
        '100' => function($image) {return $image->fit(100, 100);},
        '140' => function($image) {return $image->fit(140, 140);},
        '180' => function($image) {return $image->fit(180, 180);},
        '220' => function($image) {return $image->fit(220, 220);},
    ),

    /*
    |--------------------------------------------------------------------------
    | Image Cache Lifetime
    |--------------------------------------------------------------------------
    |
    | Lifetime in minutes of the images handled by the imagecache route.
    |
    */
   
    'lifetime' => 43200,

);
