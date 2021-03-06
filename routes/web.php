<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\WorkingWithOrdersController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\CheckOrderController;
use App\Http\Controllers\ServicesController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/',  function () {
    if(Auth::check()) {
        return redirect('/orders');
    }
     else {
        return view('auth.login');
    }
})->name('enter');

/* client can see status of order */
Route::get('/client', [CheckOrderController::class, 'index']);
/* get order info by unique code */
Route::get('/client/order/{code}', [CheckOrderController::class, 'getOrderForChecking']);

Route::group(['middleware' => ['auth', 'is_blocked']], function() {
    /* profile page */
    Route::view('/profile', 'profile/index')->name('profile');
    /* get user by id */
    Route::get('/user/{id}', [ProfileController::class, 'getUser']);
    /* save user data to db */
    Route::post('/user/store', [ProfileController::class, 'saveData']);
    /* change pass */
    Route::post('/user/password/store', [ProfileController::class, 'changePassword']);
    /* logout from app */
    Route::get('/logout', [LoginController::class, 'logout']);
    /* main page */
    Route::get('/orders', [OrdersController::class, 'index'])->name('orders');
    /* get all orders */
    Route::get('/orders/all', [OrdersController::class, 'getAllOrders']);
    /* get all services */
    Route::get('/services', [ServicesController::class, 'index']);
    /* change status of one order */
    Route::post('/change_status', [OrdersController::class, 'changeStatus']);
    /* add order to database */
    Route::post('/add_order', [WorkingWithOrdersController::class, 'addOrder']);
    /* searching client login */
    Route::get('/search_client', [WorkingWithOrdersController::class, 'searchLogin']);
    /* get count of open orders */
    Route::get('/open_orders', [OrdersController::class, 'getOpenOrders']);
    /* edit order - get info */
    Route::get('/order/edit/{id}', [WorkingWithOrdersController::class, 'openOrder']);
    /* get order story */
    Route::get('/order/story/{id}', [WorkingWithOrdersController::class, 'getStory']);
    /* update order in database */
    Route::post('/update_order', [WorkingWithOrdersController::class, 'updateOrder']);
    /* delete selected orders */
    Route::get('/orders/massDelete/{orders}', [WorkingWithOrdersController::class, 'massDelete']);
    /* select all orders on selected service (or all services, if selected) */
    Route::get('/orders/selectAll', [WorkingWithOrdersController::class, 'selectAll']);
    /* export orders to excel */
    Route::get('/orders/export/{orders}', [OrdersController::class, 'export']);
});

Route::group(['middleware' => 'is_admin', 'prefix' => 'dashboard'], function() {
    /* dashboard main page */
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');
    /* collecting count of orders on dashboard */
    Route::get('/total', [AdminController::class, 'getTotalData']);
    /* MAYBE YES, MAYBE NO... */
    Route::get('/options', [AdminController::class, 'getAllSettings']);
    /* export to excel orders: month collection */
    Route::get('/export_month', [AdminController::class, 'exportMonth']);
    /* get all users */
    Route::get('/users/all', [AdminController::class, 'getUsers']);
    /* add new user */
    Route::post('/user/add', [AdminController::class, 'addUser']);
    /* block user */
    Route::get('/user/block/{id}', [AdminController::class, 'blockUser']);
    /* edit user */
    Route::get('/user/edit/{id}', [AdminController::class, 'editUser']);
    /* POST -> update user */
    Route::post('/user/update', [AdminController::class, 'updateUser']);
    /* switch service */
    Route::get('/service/switch/{id}/{order_id}', [AdminController::class, 'switchService']);
    /* get data to analytics pie graph */
    Route::get('/analytics', [AnalyticsController::class, 'getAnalytics']);
    /* temporary */
    Route::get('/st', function () {
       Artisan::call('storage:link');

       return Artisan::output();
    });;
});
/* if you dont know, what is it, I have bad news to you... */
Auth::routes();
