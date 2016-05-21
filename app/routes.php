<?php

/** ------------------------------------------
 *  路由模式绑定
 *  ------------------------------------------
 */
Route::model('user', 'User');
Route::model('store', 'Store');
// Route::model('comment', 'Comment');
// Route::model('post', 'Post');
Route::model('role', 'Role');
Route::model('dashboard', 'User'); // 可以?

/** ------------------------------------------
 *  路由模式匹配
 *  ------------------------------------------
 */
Route::pattern('comment', '[0-9]+');
Route::pattern('post', '[0-9]+');
Route::pattern('role', '[0-9]+');
Route::pattern('token', '[0-9a-z]+');
Route::pattern('user', '[0-9]+');
Route::pattern('store', '[0-9]+');
// Route::pattern('size', '[0-9a-z]+');
// Route::pattern('img', '[0-9a-z]+');

Route::get('wx', function()
{
    return View::make('wx/test');
});

/** ------------------------------------------
 *  前端路由
 *  ------------------------------------------
 */
// common
Route::group(array('prefix' => 'common'), function(){
    Route::controller('location', 'CommonLocationController');
    Route::controller('category', 'CommonCategoryController');
});

// user
Route::group(array('prefix' => 'user'), function(){
    Route::get('reset/{token}', 'UserIndexController@getReset');
    Route::post('reset/{token}', 'UserIndexController@postReset');
    Route::post('{user}/edit', 'UserIndexController@postEdit');
    Route::controller('/', 'UserIndexController'); // 默认路径
});

// dashboard
Route::group(array('prefix' => 'dashboard'), function(){
    // Route::post('{user}/password', 'DashboardIndexController@postPassword');

    Route::controller('store', 'DashboardStoreController');
    Route::controller('/', 'DashboardIndexController');
});

// store
Route::group(array('prefix' => 'store'), function(){
    Route::controller('{store}', 'StoreIndexController');
    Route::controller('/', 'StoreIndexController');
});
// Route::controller('/', 'UserIndexController'); // 默认路径




// 图片路由
Route::get('img/{size}/{name}', 'CommonImageController@get');



// weixin
Route::group(array('prefix' => 'wx'), function(){
    Route::controller('pay', 'WxPayController'); 
    Route::controller('api', 'WxApiController'); 
    Route::controller('user', 'WxUserController'); 
    Route::controller('order', 'WxOrderController'); 
    Route::controller('dashboard', 'WxDashboardController'); 
    Route::controller('games', 'WxGamesController'); 
    Route::controller('/', 'WxIndexController'); 
});




/** ------------------------------------------
 *  后台路由
 *  ------------------------------------------
 */
// Route::group(array('prefix' => 'admin', 'before' => 'auth'), function(){
//     # Comment Management
//     Route::get('comments/{comment}/edit', 'AdminCommentsController@getEdit');
//     Route::post('comments/{comment}/edit', 'AdminCommentsController@postEdit');
//     Route::get('comments/{comment}/delete', 'AdminCommentsController@getDelete');
//     Route::post('comments/{comment}/delete', 'AdminCommentsController@postDelete');
//     Route::controller('comments', 'AdminCommentsController');

//     # Blog Management
//     Route::get('blogs/{post}/show', 'AdminBlogsController@getShow');
//     Route::get('blogs/{post}/edit', 'AdminBlogsController@getEdit');
//     Route::post('blogs/{post}/edit', 'AdminBlogsController@postEdit');
//     Route::get('blogs/{post}/delete', 'AdminBlogsController@getDelete');
//     Route::post('blogs/{post}/delete', 'AdminBlogsController@postDelete');
//     Route::controller('blogs', 'AdminBlogsController');

//     # User Management
//     Route::get('users/{user}/show', 'AdminUsersController@getShow');
//     Route::get('users/{user}/edit', 'AdminUsersController@getEdit');
//     Route::post('users/{user}/edit', 'AdminUsersController@postEdit');
//     Route::get('users/{user}/delete', 'AdminUsersController@getDelete');
//     Route::post('users/{user}/delete', 'AdminUsersController@postDelete');
//     Route::controller('users', 'AdminUsersController');

//     # User Role Management
//     Route::get('roles/{role}/show', 'AdminRolesController@getShow');
//     Route::get('roles/{role}/edit', 'AdminRolesController@getEdit');
//     Route::post('roles/{role}/edit', 'AdminRolesController@postEdit');
//     Route::get('roles/{role}/delete', 'AdminRolesController@getDelete');
//     Route::post('roles/{role}/delete', 'AdminRolesController@postDelete');
//     Route::controller('roles', 'AdminRolesController');

//     # Admin Dashboard
//     Route::controller('/', 'AdminDashboardController');
// });

# Filter for detect language
// Route::when('contact','detectLang');


/** ------------------------------------------
 *  静态路由
 *  ------------------------------------------
 */
# Contact Us Static Page
// Route::get('contact', function(){return View::make('site/contact');});

/** ------------------------------------------
 *  注意以下顺序，必须为最后
 *  ------------------------------------------
 */
# Posts - Second to last set, match slug
// Route::get('{postSlug}', 'BlogController@getView');
// Route::post('{postSlug}', 'BlogController@postView');

# Index Page - Last route, no matches
// Route::get('/', array('before' => 'detectLang','uses' => 'BlogController@getIndex'));
Route::controller('/', 'WxIndexController');
