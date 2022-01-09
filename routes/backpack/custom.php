<?php

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\Base.
// Routes you generate using Backpack\Generators will be placed here.

Route::get('api/article', 'App\Http\Controllers\Api\ArticleController@index');
Route::get('api/article-search', 'App\Http\Controllers\Api\ArticleController@search');

Route::group([
    'prefix'     => config('backpack.base.route_prefix', 'admin'),
    'middleware' => ['web', config('backpack.base.middleware_key', 'admin')],
    'namespace'  => 'App\Http\Controllers\Admin',
], function () { // custom admin routes
    // -----
    // CRUDs
    // -----
    Route::crud('monster', 'MonsterCrudController');
    Route::crud('fluent-monster', 'FluentMonsterCrudController');
    Route::crud('icon', 'IconCrudController');
    Route::crud('product', 'ProductCrudController');
    Route::crud('dummy', 'DummyCrudController');
    Route::crud('areas', 'AreasCrudController');
    Route::crud('cars', 'CarsCrudController');
    Route::crud('carsorders', 'CarsOrdersCrudController');
    Route::crud('cartypes', 'CarTypesCrudController');
    Route::crud('customertypes', 'CustomerTypesCrudController');
    Route::crud('carmakes', 'CarMakesCrudController');
    Route::crud('carmodel', 'CarModelCrudController');
    Route::crud('requeststatus', 'RequestStatusCrudController');
    Route::crud('customers', 'CustomersCrudController');
    Route::crud('orders', 'OrdersCrudController');
    Route::crud('driversorders', 'OrdersDriverCrudController');
    Route::crud('orderscollected', 'OrdersCollectedCrudController');
    Route::crud('comissions', 'ComissionsCrudController');

    Route::crud('user', 'UserCrudController');
    Route::crud('invoices', 'InvoicesCrudController');
    Route::crud('xeroinvoices', 'XeroInvoicesCrudController');
    Route::crud('missingxero', 'MissingXeroCrudController');
    Route::get('invoice/generate', 'InvoicesCrudController@generateInvoice');
    Route::get('order/xeror/generate/{id}', 'MissingXeroCrudController@generateInvoice')->name('createXeroIncoie');


    //Filter Fetch
    Route::post('fetch/areas', 'AreasCrudController@fetch');
    Route::post('fetch/carmakes', 'CarMakesCrudController@fetch');
    Route::get('fetch/carmodel', 'CarModelCrudController@fetch');
    Route::post('fetch/car', 'CarsCrudController@fetch');
    Route::post('fetch/customer', 'CustomersCrudController@fetch');
    Route::post('fetch/driver', 'CustomersCrudController@driver');

    //Filter Fetch

    Route::get('filter/areas', 'AreasCrudController@fetch');
    Route::get('filter/carmakes', 'CarMakesCrudController@fetch');
    Route::get('filter/carmodel', 'CarModelCrudController@fetch');
    Route::get('filterorder/car', 'CarsCrudController@filter');
    Route::get('filter/customer', 'CustomersCrudController@filter');
    Route::get('filter/driver', 'CustomersCrudController@driver');

    // ------------------
    // AJAX Chart Widgets
    // ------------------
    Route::get('charts/users', 'Charts\LatestUsersChartController@response');
    Route::get('charts/new-entries', 'Charts\NewEntriesChartController@response');
    Route::get('charts/comissions', 'Charts\ComissionChartController@response');
    Route::get('charts/cash', 'Charts\CashDriverChartController@response');
    Route::get('/switch_lang/{locale}', function ($locale = '') {
        session(['locale' => $locale]);
 
        return redirect()->back();
    })->name('switch_lang');
    // ---------------------------
    // Backpack DEMO Custom Routes
    // Prevent people from doing nasty stuff in the online demo
    // ---------------------------
    if (app('env') == 'production') {
        // disable delete and bulk delete for all CRUDs
        $cruds = ['article', 'category', 'tag', 'monster', 'icon', 'product', 'page', 'menu-item', 'user', 'role', 'permission'];
        foreach ($cruds as $name) {
            Route::delete($name.'/{id}', function () {
                return false;
            });
            Route::post($name.'/bulk-delete', function () {
                return false;
            });
        }
    }
}); // this should be the absolute last line of this file
