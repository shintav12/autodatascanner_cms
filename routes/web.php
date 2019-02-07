<?php


Route::any('/', ["as" => "admin_index", "uses" => "LoginController@login"]);
Route::any('/logout', ["as" => "admin_logout", "uses" => 'LoginController@logout']);

Route::group(['prefix' => 'auth_users'], function (){
    Route::get('/','AuthUserController@index')->middleware('verify_permissions')->name('auth_users');
    Route::get('/get_types','AuthUserController@load')->name('get_user');
    Route::post('change_status','AuthUserController@change_status')->name('change_status_user');
    Route::post('/save','AuthUserController@save')->name('user_save');
    Route::get('/detail/{id?}','AuthUserController@detail')->middleware('verify_permissions')->name('detail_user');
});

Route::group(['prefix' => 'auth_role'], function (){
    Route::get('/','AuthRoleController@index')->middleware('verify_permissions')->name('auth_role');
    Route::get('/get_types','AuthRoleController@load')->name('get_role');
    Route::post('change_status','AuthRoleController@change_status')->name('change_status_role');
    Route::post('/save','AuthRoleController@save')->name('role_save');
    Route::post('/perms_save','AuthRoleController@permissionsSave')->name('perms_save');
    Route::get('/detail/{id?}','AuthRoleController@detail')->middleware('verify_permissions')->name('role_user');
    Route::get('/perms/{id}','AuthRoleController@perms')->middleware('verify_permissions')->name('perms');
});

Route::group(['prefix' => 'brands'], function (){
    Route::get('/','BrandController@index')->middleware('verify_permissions')->name('brands');
    Route::get('/get_types','BrandController@load')->name('get_brands');
    Route::post('change_status','BrandController@change_status')->name('change_status_brand');
    Route::post('/save','BrandController@save')->name('brand_save');
    Route::get('/detail/{id?}','BrandController@detail')->middleware('verify_permissions')->name('brand_detail');
});

Route::group(['prefix' => 'cars'], function (){
    Route::get('/','CarController@index')->middleware('verify_permissions')->name('cars');
    Route::get('/get_types','CarController@load')->name('get_cars');
    Route::post('change_status','CarController@change_status')->name('change_status_car');
    Route::post('/save','CarController@save')->name('car_save');
    Route::get('/detail/{id?}','CarController@detail')->middleware('verify_permissions')->name('car_detail');
});