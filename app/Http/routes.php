<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
Route::group(['middleware' => 'languange'], function () {
    Route::get('/', 'HomeController@index');

    Route::get('home', 'HomeController@index');

    // Authentication routes...
    Route::get('auth/login', 'Auth\AuthController@getLogin');
    Route::post('auth/login', 'Auth\AuthController@postLogin');
    Route::get('auth/logout', 'Auth\AuthController@getLogout');

    // Password reset link request routes...
    Route::get('password/email', 'Auth\PasswordController@getEmail');
    Route::post('password/email', 'Auth\PasswordController@postEmail');

    // Password reset routes...
    Route::get('password/reset/{token}', 'Auth\PasswordController@getReset');
    Route::post('password/reset', 'Auth\PasswordController@postReset');

    Route::resource('customers', 'CustomerController');
    Route::resource('items', 'ItemController');
    Route::resource('item-kits', 'ItemKitController');
    Route::resource('inventory', 'InventoryController');
    Route::resource('suppliers', 'SupplierController');
    Route::resource('receivings', 'ReceivingController');
//    Route::resource('receiving-item', 'ReceivingItemController');

    Route::resource('sales', 'SaleController');
    Route::put('sales/{id}/void', 'SaleController@voidSale');

    Route::resource('reports/receivings', 'ReceivingReportController@index');

    Route::resource('reports/sales', 'SaleReportController@index');
//    Route::resource('reports/sales-filter', 'SaleReportController@filter');

    Route::get('reports/sales-filter', [
        'uses' => 'SaleReportController@filter',
        'as' => 'report.sales.filter'
    ]);
    Route::resource('employees', 'EmployeeController');


    Route::resource('api/customers', 'CustomerController@listAll');

    Route::resource('api/item', 'ItemController@listAll');
    Route::resource('api/receivingtemp', 'ReceivingTempApiController');

    Route::resource('api/saletemp', 'SaleTempApiController');

    Route::resource('api/itemkittemp', 'ItemKitController');
    Route::get('api/item-kit-temp', 'ItemKitController@itemKitApi');
    Route::get('api/item-kits', 'ItemKitController@itemKits');
    Route::post('store-item-kits', 'ItemKitController@storeItemKits');

    Route::resource('tutapos-settings', 'TutaposSettingController');
});

