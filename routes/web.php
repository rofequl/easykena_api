<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['namespace' => 'User'], function () use ($router) {
    $router->post('register', 'AuthController@register');
    $router->post('login', 'AuthController@login');
    $router->post('send-otp', 'AuthController@sendOTP');
});


$router->group(['middleware' => 'auth:api'], function () use ($router) {
    $router->get('profile', 'User\AuthController@profile');
    $router->post('profile-update', 'User\AuthController@profileUpdate');
    $router->post('logout', 'User\AuthController@logout');
    $router->post('send-mail-otp', 'User\AuthController@sendMailOTP');
    $router->post('mail-update', 'User\AuthController@mailUpdate');

    //Address Book
    $router->get('address', 'AddressController@index');
    $router->get('address_user/{user_id}', 'AddressController@addressByuser');
    $router->post('address', 'AddressController@store');
    $router->put('address/{id}', 'AddressController@update');
    $router->put('address_shipping/{id}', 'AddressController@update_shipping');
    $router->delete('address/{id}', 'AddressController@destroy');

    //Setup & Configurations
    $router->post('general-settings', 'GeneralController@generalStore');
    $router->post('language_active', 'GeneralController@languageActive');
    $router->post('default_language', 'GeneralController@languageDefault');
    $router->post('maintenance_active', 'GeneralController@maintenanceActive');
    $router->post('maintenance_date', 'GeneralController@maintenanceDate');

    $router->post('home-slider-update', 'HomeController@sliderStore');

    //product
    $router->post('category', 'CategoryController@store');
    $router->put('category/{id}', 'CategoryController@update');
    $router->delete('category/{id}', 'CategoryController@destroy');

    $router->post('subcategory', 'SubcategoryController@store');
    $router->put('subcategory/{id}', 'SubcategoryController@update');
    $router->delete('subcategory/{id}', 'SubcategoryController@destroy');

    $router->post('subsubcategory', 'SubSubcategoryController@store');
    $router->put('subsubcategory/{id}', 'SubSubcategoryController@update');
    $router->delete('subsubcategory/{id}', 'SubSubcategoryController@destroy');


    $router->post('product', 'ProductController@store');
    $router->delete('product/{id}', 'ProductController@destroy');

    $router->post('brand', 'BrandController@store');
    $router->put('brand/{id}', 'BrandController@update');
    $router->delete('brand/{id}', 'BrandController@destroy');

    //Shipping
    $router->post('region', 'RegionController@store');
    $router->put('region/{id}', 'RegionController@update');
    $router->delete('region/{id}', 'RegionController@destroy');

    $router->post('city', 'CityController@store');
    $router->put('city/{id}', 'CityController@update');
    $router->delete('city/{id}', 'CityController@destroy');

    $router->post('area', 'AreaController@store');
    $router->put('area/{id}', 'AreaController@update');
    $router->delete('area/{id}', 'AreaController@destroy');


});

//Setup & Configurations
$router->get('general-settings', 'GeneralController@generalIndex');
$router->get('home-setup', 'HomeController@homeIndex');

//product
$router->get('category', 'CategoryController@index');
$router->get('subcategory', 'SubcategoryController@index');
$router->get('subsubcategory', 'SubSubcategoryController@index');
$router->get('brand', 'BrandController@index');
$router->get('product-listing', 'ProductController@productListing');
$router->get('product', 'ProductController@index');

//Shipping
$router->get('region', 'RegionController@index');
$router->get('city', 'CityController@index');
$router->get('area', 'AreaController@index');

//Frontend


$router->group(['middleware' => 'JWTRefresh', 'namespace' => 'User'], function () use ($router) {
    $router->post('token/refresh', 'AuthController@refresh');
});
