<?php

return array(

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => false,

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | your application so that it is used when running Artisan tasks.
    |
    */

    'url' => 'http://m.daguike.com',

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. We have gone
    | ahead and set this to a sensible default for you out of the box.
    |
    */

    'timezone' => 'Asia/Shanghai',

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by the translation service provider. You are free to set this value
    | to any of the locales which will be supported by the application.
    |
    */

    'locale' => 'zh_CN',

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the Illuminate encrypter service and should be set
    | to a random, long string, otherwise these encrypted values will not
    | be safe. Make sure to change it before deploying any application!
    |
    */

    'key' => 'YourSecretKey!!!',

    'cipher' => MCRYPT_RIJNDAEL_128,    

    /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    |
    | The service providers listed here will be automatically loaded on the
    | request to your application. Feel free to add your own services to
    | this array to grant expanded functionality to your applications.
    |
    */

    'providers' => array(
        /* Laravel Base Providers */
        'Illuminate\Foundation\Providers\ArtisanServiceProvider',
        'Illuminate\Auth\AuthServiceProvider',
        'Illuminate\Cache\CacheServiceProvider',
        'Illuminate\Session\CommandsServiceProvider',
        'Illuminate\Foundation\Providers\ConsoleSupportServiceProvider',
        'Illuminate\Routing\ControllerServiceProvider',
        'Illuminate\Cookie\CookieServiceProvider',
        'Illuminate\Database\DatabaseServiceProvider',
        'Illuminate\Encryption\EncryptionServiceProvider',
        'Illuminate\Filesystem\FilesystemServiceProvider',
        'Illuminate\Hashing\HashServiceProvider',
        'Illuminate\Html\HtmlServiceProvider',
        'Illuminate\Log\LogServiceProvider',
        'Illuminate\Mail\MailServiceProvider',
        'Illuminate\Database\MigrationServiceProvider',
        'Illuminate\Pagination\PaginationServiceProvider',
        'Illuminate\Queue\QueueServiceProvider',
        'Illuminate\Redis\RedisServiceProvider',
        'Illuminate\Remote\RemoteServiceProvider',
        'Illuminate\Auth\Reminders\ReminderServiceProvider',
        'Illuminate\Database\SeedServiceProvider',
        'Illuminate\Session\SessionServiceProvider',
        'Illuminate\Translation\TranslationServiceProvider',
        'Illuminate\Validation\ValidationServiceProvider',
        'Illuminate\View\ViewServiceProvider',
        'Illuminate\Workbench\WorkbenchServiceProvider',
        
        /* Additional Providers */
        'Zizaco\Confide\ConfideServiceProvider', // Confide Provider
        'Zizaco\Entrust\EntrustServiceProvider', // Entrust Provider for roles
        'Bllim\Datatables\DatatablesServiceProvider', // Datatables
        'Intervention\Image\ImageServiceProvider',
        'Gloudemans\Shoppingcart\ShoppingcartServiceProvider',
        // 'Barryvdh\Debugbar\ServiceProvider',
        // 'Modbase\AssetManager\AssetManagerServiceProvider',
        /* Uncomment for use in development */
//        'Way\Generators\GeneratorsServiceProvider', // Generators
//        'Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider', // IDE Helpers

        // 'Frozennode\Administrator\AdministratorServiceProvider'
    ),

    /*
    |--------------------------------------------------------------------------
    | Service Provider Manifest
    |--------------------------------------------------------------------------
    |
    | The service provider manifest is used by Laravel to lazy load service
    | providers which are not needed for each request, as well to keep a
    | list of all of the services. Here, you may set its storage spot.
    |
    */

    'manifest' => storage_path() . '/meta',

    /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    |
    | This array of class aliases will be registered when this application
    | is started. However, feel free to register as many as you wish as
    | the aliases are "lazy" loaded so they don't hinder performance.
    |
    */

    'aliases' => array(
        /* Laravel Base Aliases */
        'App'             => 'Illuminate\Support\Facades\App',
        'Artisan'         => 'Illuminate\Support\Facades\Artisan',
        'Auth'            => 'Illuminate\Support\Facades\Auth',
        'Blade'           => 'Illuminate\Support\Facades\Blade',
        'Cache'           => 'Illuminate\Support\Facades\Cache',
        'ClassLoader'     => 'Illuminate\Support\ClassLoader',
        'Config'          => 'Illuminate\Support\Facades\Config',
        'Controller'      => 'Illuminate\Routing\Controller',
        'Cookie'          => 'Illuminate\Support\Facades\Cookie',
        'Crypt'           => 'Illuminate\Support\Facades\Crypt',
        'DB'              => 'Illuminate\Support\Facades\DB',
        'Eloquent'        => 'Illuminate\Database\Eloquent\Model',
        'Event'           => 'Illuminate\Support\Facades\Event',
        'File'            => 'Illuminate\Support\Facades\File',
        'Form'            => 'Illuminate\Support\Facades\Form',
        'Hash'            => 'Illuminate\Support\Facades\Hash',
        'HTML'            => 'Illuminate\Support\Facades\HTML',
        'Input'           => 'Illuminate\Support\Facades\Input',
        'Lang'            => 'Illuminate\Support\Facades\Lang',
        'Log'             => 'Illuminate\Support\Facades\Log',
        'Mail'            => 'Illuminate\Support\Facades\Mail',
        'Paginator'       => 'Illuminate\Support\Facades\Paginator',
        'Password'        => 'Illuminate\Support\Facades\Password',
        'Queue'           => 'Illuminate\Support\Facades\Queue',
        'Redirect'        => 'Illuminate\Support\Facades\Redirect',
        'Redis'           => 'Illuminate\Support\Facades\Redis',
        'Request'         => 'Illuminate\Support\Facades\Request',
        'Response'        => 'Illuminate\Support\Facades\Response',
        'Route'           => 'Illuminate\Support\Facades\Route',
        'Schema'          => 'Illuminate\Support\Facades\Schema',
        'Seeder'          => 'Illuminate\Database\Seeder',
        'Session'         => 'Illuminate\Support\Facades\Session',
        'SoftDeletingTrait' => 'Illuminate\Database\Eloquent\SoftDeletingTrait',
        'SSH'             => 'Illuminate\Support\Facades\SSH',
        'Str'             => 'Illuminate\Support\Str',
        'URL'             => 'Illuminate\Support\Facades\URL',
        'Validator'       => 'Illuminate\Support\Facades\Validator',
        'View'            => 'Illuminate\Support\Facades\View',

        /* Additional Aliases */
        'Confide'         => 'Zizaco\Confide\ConfideFacade', // Confide Alias
        'Entrust'         => 'Zizaco\Entrust\EntrustFacade', // Entrust Alias
        // 'String'          => 'Andrew13\Helpers\String', // String
        'Carbon'          => 'Carbon\Carbon', // Carbon
        'Datatables'      => 'Bllim\Datatables\Datatables', // DataTables
        'Image'           => 'Intervention\Image\Facades\Image',
        'Cart'            => 'Gloudemans\Shoppingcart\Facades\Cart',
    ),

    'available_language' => array('zh-CN'),
    'wx' => array(
        'js_debug' => false,
        'mchid' => '1227679802',
        'key' => 'daguike880525daguike880525daguik',
        'sslcert_path' => '/b/domains/m.daguike.com/app/common/Wechat/WxPayPubHelper/cacert/apiclient_cert.pem',//todo
        'sslkey_path' => '/b/domains/m.daguike.com/app/common/Wechat/WxPayPubHelper/cacert/apiclient_key.pem',//todo
        'notify_url' => 'http://'.(empty($_SERVER['HTTP_HOST'])?'localhost':$_SERVER['HTTP_HOST']).'/wx/pay/notify',
        'curl_timeout' => 30,
        'token'=>'dgk', //填写你设定的key
        'encodingaeskey'=>'9XKTNcY8xhETdvwrBD8lnUv4tffzBlH6h7mRaB7pWCM', //填写加密用的EncodingAESKey，如接口为明文模式可忽略
        'appid' => 'wxd71f8a99418210c0',
        'appsecret' => '449fce7cdaa60a8cadfc1eb4288924c5',
        'menus' => array(
            "button" => array(
                array('type'=>'view','name'=>'买水果','url'=>'http://m.daguike.com/'),
                array('type'=>'view','name'=>'红包','url'=>'http://m.daguike.com/#/wx/setting/hongbao'),
                array('type'=>'view','name'=>'我','url'=>'http://m.daguike.com/#/wx/me'),
            )
        ),
        'kf' => '00@idaguike',//'525@idaguike',
        'reply' => array(
            'subscribe' => '欢迎来到大贵客鲜果速递！我们为您精心准备了以下优惠：“一分钱送水果”和“新人专享20元红包”，还等什么，赶快点击下方菜单试试吧！无运费无起步费，30分钟内送达，货到付款，无理由退换，客服电话：13656633843',
        ),
    ),
    'cdn' => array(
        'domain' => 'cdn.daguike.com',
    ),
    'sms' => array(
        'enable' => true,
        'tpl' => array(
            'store' => array(
                'order_notify' => 739323, //配送时间#time#，金额#amount#，地址#location#，商品规格#sku#，单号#sn#
                'order_cancel' => 761079,
            )
        )
    ),
    'baidu' => array(
        '_hm' => '7f620f1743c53057010398e93273d89e'
    ),
    'email' => array(
        'admin' => 'daguike@qq.com'
    )
);
